<?php

namespace App\Services;

use App\Mail\QuoteApprovalDecided;
use App\Mail\QuoteApprovalOtp;
use App\Models\ClientPortalUser;
use App\Models\Quote;
use App\Models\QuoteApprovalChallenge;
use App\Models\QuoteApprovalEvent;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Server-side quote approval: state machine, OTP challenges, canonical
 * document hashing, atomic acceptance/rejection evidence.
 *
 * Security posture:
 * - A public link proves POSSESSION, never identity or authority.
 * - Acceptance requires a verified channel (portal approver or OTP bound to
 *   one quote + one revision + one recipient + one purpose).
 * - Raw OTPs / tokens are never stored and never logged.
 */
class QuoteApprovalService
{
    public const CONSENT_VERSION = '2026-10-10-v1';

    public const OTP_TTL_MINUTES = 15;
    public const OTP_MAX_ATTEMPTS = 5;
    public const OTP_RESEND_COOLDOWN_SECONDS = 60;

    public static function consentText(Quote $quote): string
    {
        return 'Saya menyatakan telah membaca dan menyetujui quotation '
            . $quote->quote_no . ' revisi ' . $quote->revision
            . ' dengan total ' . $quote->currency . ' ' . number_format((float) $quote->total, 2)
            . '. Saya menyatakan memiliki kewenangan untuk menyetujui atas nama '
            . ($quote->client?->company_name ?: $quote->client?->name ?? 'klien')
            . '. Persetujuan ini dicatat beserta identitas kontak terverifikasi dan hash dokumen.';
    }

    /**
     * Canonical approval document: sorted keys, normalized decimals, only
     * commercial fields (volatile columns excluded so status workflow never
     * changes the hash).
     */
    public static function canonicalDocument(Quote $quote): array
    {
        $items = collect($quote->items ?? [])->map(fn ($it) => [
            'description' => (string) ($it['description'] ?? ''),
            'quantity' => sprintf('%.4f', (float) ($it['quantity'] ?? 0)),
            'unit_price' => sprintf('%.2f', (float) ($it['unit_price'] ?? 0)),
            'tax_rate' => sprintf('%.2f', (float) ($it['tax_rate'] ?? 0)),
        ])->sortBy(fn ($it) => $it['description'] . $it['quantity'] . $it['unit_price'])->values()->all();

        $doc = [
            'quote_no' => (string) $quote->quote_no,
            'revision' => (int) $quote->revision,
            'currency' => (string) $quote->currency,
            'client_id' => (int) $quote->client_id,
            'workspace_id' => (int) $quote->workspace_id,
            'issue_date' => (string) ($quote->issue_date?->format('Y-m-d') ?? ''),
            'expiry_date' => (string) ($quote->expiry_date?->format('Y-m-d') ?? ''),
            'items' => $items,
            'subtotal' => sprintf('%.2f', (float) $quote->subtotal),
            'tax_rate' => sprintf('%.2f', (float) $quote->tax_rate),
            'tax_amount' => sprintf('%.2f', (float) $quote->tax_amount),
            'discount_type' => (string) $quote->discount_type,
            'discount_value' => sprintf('%.2f', (float) $quote->discount_value),
            'discount_amount' => sprintf('%.2f', (float) $quote->discount_amount),
            'total' => sprintf('%.2f', (float) $quote->total),
            'notes' => (string) ($quote->notes ?? ''),
            'terms' => (string) ($quote->terms ?? ''),
            'consent_version' => self::CONSENT_VERSION,
        ];
        ksort($doc);
        return $doc;
    }

    public static function documentHash(Quote $quote): string
    {
        return hash('sha256', json_encode(
            self::canonicalDocument($quote),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        ));
    }

    /** High-risk quotes require portal designated-approver acceptance (no OTP fallback). */
    public static function requiresPortalApproval(Quote $quote): bool
    {
        $threshold = $quote->workspace?->quote_high_risk_threshold;
        return $threshold !== null && (float) $quote->total >= (float) $threshold;
    }

    /** Eligibility shared by accept/reject/OTP flows. Returns null when eligible, else a safe reason code. */
    public static function eligibility(Quote $quote): ?string
    {
        if (in_array($quote->status, ['accepted', 'converted'], true)) {
            return 'already_accepted';
        }
        if ($quote->status === 'rejected') {
            return 'already_rejected';
        }
        if ($quote->status === 'withdrawn') {
            return 'withdrawn';
        }
        if ($quote->status === 'expired' || $quote->isExpired()) {
            return 'expired';
        }
        if (! in_array($quote->status, ['sent', 'draft'], true)) {
            return 'invalid_state';
        }
        return null;
    }

    /**
     * Issue an OTP challenge bound to one quote + revision + recipient + purpose.
     * Returns [challenge, rawToken, otp] — the caller delivers the OTP by mail.
     * Throttles: resend cooldown + max 3 live challenges per quote+purpose.
     *
     * @return array{0: QuoteApprovalChallenge, 1: string, 2: string}|array{error: string}
     */
    public function requestChallenge(Quote $quote, string $purpose, string $contactEmail, ?ClientPortalUser $portalUser = null): array
    {
        if (! in_array($purpose, ['accept', 'reject'], true)) {
            return ['error' => 'invalid_purpose'];
        }
        if ($reason = self::eligibility($quote)) {
            return ['error' => $reason];
        }

        return DB::transaction(function () use ($quote, $purpose, $contactEmail, $portalUser) {
            $live = QuoteApprovalChallenge::where('quote_id', $quote->id)
                ->where('purpose', $purpose)
                ->whereNull('consumed_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->get();

            if ($live->count() >= 3) {
                return ['error' => 'too_many_challenges'];
            }
            $recent = $live->firstWhere('contact_email', $contactEmail);
            if ($recent && $recent->last_sent_at && $recent->last_sent_at->diffInSeconds(now()) < self::OTP_RESEND_COOLDOWN_SECONDS) {
                return ['error' => 'resend_cooldown'];
            }
            // Supersede older live challenges for the same recipient+purpose.
            foreach ($live as $old) {
                if ($old->contact_email === $contactEmail) {
                    $old->update(['consumed_at' => now()]);
                }
            }

            $rawToken = Str::random(40);
            $otp = (string) random_int(100000, 999999);

            $challenge = QuoteApprovalChallenge::create([
                'workspace_id' => $quote->workspace_id,
                'quote_id' => $quote->id,
                'revision' => $quote->revision,
                'contact_email' => $contactEmail,
                'portal_user_id' => $portalUser?->id,
                'purpose' => $purpose,
                'token_hash' => hash('sha256', $rawToken),
                'otp_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                'max_attempts' => self::OTP_MAX_ATTEMPTS,
                'last_sent_at' => now(),
            ]);

            return [$challenge, $rawToken, $otp];
        });
    }

    /** Deliver the OTP by mail AFTER the challenge row has committed. Never logs the OTP. */
    public function sendOtpMail(Quote $quote, string $contactEmail, string $otp): void
    {
        try {
            Mail::to($contactEmail)->send(new QuoteApprovalOtp($quote, $otp));
        } catch (\Throwable $e) {
            Log::warning('Quote OTP mail failed', ['quote_id' => $quote->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Verify an OTP attempt. Consumes the challenge on success, counts the
     * attempt on failure, locks out at max attempts.
     *
     * @return array{challenge?: QuoteApprovalChallenge, error?: string}
     */
    public function verifyOtp(string $rawToken, string $otp, string $purpose): array
    {
        return DB::transaction(function () use ($rawToken, $otp, $purpose) {
            $challenge = QuoteApprovalChallenge::where('token_hash', hash('sha256', $rawToken))
                ->where('purpose', $purpose)
                ->lockForUpdate()
                ->first();

            if (! $challenge) {
                return ['error' => 'invalid_challenge'];
            }
            if ($challenge->consumed_at) {
                return ['error' => 'challenge_used'];
            }
            if ($challenge->expires_at->isPast()) {
                return ['error' => 'challenge_expired'];
            }
            if ($challenge->attempts >= $challenge->max_attempts) {
                return ['error' => 'too_many_attempts'];
            }
            if (! Hash::check($otp, $challenge->otp_hash)) {
                $challenge->increment('attempts');
                return ['error' => 'invalid_otp'];
            }
            return ['challenge' => $challenge];
        });
    }

    /**
     * Atomic acceptance. Idempotent per (quote, revision, approver).
     *
     * $identity: ['method' => 'portal'|'otp'|'manual', 'portal_user' => ?ClientPortalUser,
     *   'challenge' => ?QuoteApprovalChallenge, 'name' => string, 'role' => ?string,
     *   'email' => string, 'authority' => 'designated_approver'|'verified_contact'|'workspace_manual',
     *   'consent' => true]
     *
     * @return array{event?: QuoteApprovalEvent, error?: string, duplicate?: bool}
     */
    public function accept(Quote $quote, array $identity, ?string $correlationId = null): array
    {
        return DB::transaction(function () use ($quote, $identity, $correlationId) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();

            if ($reason = self::eligibility($locked)) {
                return ['error' => $reason];
            }
            // Revision must still match what the approver reviewed.
            if (isset($identity['revision']) && (int) $identity['revision'] !== (int) $locked->revision) {
                return ['error' => 'revision_changed'];
            }
            // Challenge binding checks.
            if (! empty($identity['challenge'])) {
                /** @var QuoteApprovalChallenge $ch */
                $ch = $identity['challenge'];
                if ($ch->quote_id !== $locked->id || (int) $ch->revision !== (int) $locked->revision
                    || $ch->purpose !== 'accept' || $ch->consumed_at !== null) {
                    return ['error' => 'challenge_invalid'];
                }
            }
            // High-risk policy: portal designated approver only.
            if (self::requiresPortalApproval($locked) && ($identity['method'] ?? null) !== 'portal') {
                return ['error' => 'portal_required'];
            }

            // Idempotency: same approver + revision + outcome already recorded.
            $existing = QuoteApprovalEvent::where('quote_id', $locked->id)
                ->where('revision', $locked->revision)
                ->where('outcome', 'accepted')
                ->where('approver_email', $identity['email'])
                ->first();
            if ($existing) {
                return ['event' => $existing, 'duplicate' => true];
            }

            // Authoritative server-side totals live on the quote row itself
            // (validated at creation); the hash above binds this exact revision.
            $hash = self::documentHash($locked);
            $now = now();

            $event = QuoteApprovalEvent::create([
                'workspace_id' => $locked->workspace_id,
                'quote_id' => $locked->id,
                'revision' => $locked->revision,
                'client_id' => $locked->client_id,
                'approver_portal_user_id' => $identity['portal_user']->id ?? null,
                'approver_name' => $identity['name'],
                'approver_role' => $identity['role'] ?? null,
                'approver_email' => $identity['email'],
                'verification_method' => $identity['method'],
                'authority_basis' => $identity['authority'],
                'consent_text_version' => self::CONSENT_VERSION,
                'consent_text_snapshot' => self::consentText($locked),
                'document_sha256' => $hash,
                'outcome' => 'accepted',
                'decided_at' => $now,
                'ip' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                'correlation_id' => $correlationId ?? (string) Str::uuid(),
            ]);

            $locked->update(['status' => 'accepted', 'accepted_at' => $now]);

            if (! empty($identity['challenge'])) {
                $identity['challenge']->update(['consumed_at' => $now]);
            }
            // Any other live challenges for this quote are now moot.
            QuoteApprovalChallenge::where('quote_id', $locked->id)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => $now]);

            $created = true;
            DB::afterCommit(function () use ($locked, $event, $created) {
                $this->notifyDecided($locked, $event);
                $this->maybeAutoConvert($locked);
            });

            return ['event' => $event];
        });
    }

    /** Atomic rejection with the same identity/revision guarantees. */
    public function reject(Quote $quote, array $identity, ?string $reason = null, ?string $correlationId = null): array
    {
        return DB::transaction(function () use ($quote, $identity, $reason, $correlationId) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();

            if ($check = self::eligibility($locked)) {
                return ['error' => $check];
            }
            if (isset($identity['revision']) && (int) $identity['revision'] !== (int) $locked->revision) {
                return ['error' => 'revision_changed'];
            }
            if (! empty($identity['challenge'])) {
                $ch = $identity['challenge'];
                if ($ch->quote_id !== $locked->id || (int) $ch->revision !== (int) $locked->revision
                    || $ch->purpose !== 'reject' || $ch->consumed_at !== null) {
                    return ['error' => 'challenge_invalid'];
                }
            }

            $existing = QuoteApprovalEvent::where('quote_id', $locked->id)
                ->where('revision', $locked->revision)
                ->where('outcome', 'rejected')
                ->where('approver_email', $identity['email'])
                ->first();
            if ($existing) {
                return ['event' => $existing, 'duplicate' => true];
            }

            $now = now();
            $event = QuoteApprovalEvent::create([
                'workspace_id' => $locked->workspace_id,
                'quote_id' => $locked->id,
                'revision' => $locked->revision,
                'client_id' => $locked->client_id,
                'approver_portal_user_id' => $identity['portal_user']->id ?? null,
                'approver_name' => $identity['name'],
                'approver_role' => $identity['role'] ?? null,
                'approver_email' => $identity['email'],
                'verification_method' => $identity['method'],
                'authority_basis' => $identity['authority'],
                'consent_text_version' => self::CONSENT_VERSION,
                'consent_text_snapshot' => self::consentText($locked),
                'document_sha256' => self::documentHash($locked),
                'outcome' => 'rejected',
                'decline_reason' => $reason ? substr($reason, 0, 500) : null,
                'decided_at' => $now,
                'ip' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                'correlation_id' => $correlationId ?? (string) Str::uuid(),
            ]);

            $locked->update(['status' => 'rejected']);

            if (! empty($identity['challenge'])) {
                $identity['challenge']->update(['consumed_at' => $now]);
            }
            QuoteApprovalChallenge::where('quote_id', $locked->id)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => $now]);

            DB::afterCommit(function () use ($locked, $event) {
                $this->notifyDecided($locked, $event);
            });

            return ['event' => $event];
        });
    }

    /** Workspace-side withdrawal: authorized caller only (enforced by controller). */
    public function withdraw(Quote $quote, string $actor, ?string $reason = null): array
    {
        return DB::transaction(function () use ($quote, $actor, $reason) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();
            if (in_array($locked->status, ['accepted', 'converted'], true)) {
                return ['error' => 'already_accepted'];
            }
            $now = now();
            $locked->update(['status' => 'withdrawn']);
            QuoteApprovalChallenge::where('quote_id', $locked->id)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => $now]);
            Log::info('Quote withdrawn', ['quote_id' => $locked->id, 'actor' => $actor, 'reason' => $reason]);
            return ['ok' => true];
        });
    }

    /**
     * Controlled reopen: rejected/withdrawn/expired -> sent as a NEW revision.
     * History is preserved; old challenges stay consumed.
     */
    public function reopen(Quote $quote, string $actor, ?string $reason = null): array
    {
        return DB::transaction(function () use ($quote, $actor, $reason) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();
            if (! in_array($locked->status, ['rejected', 'withdrawn', 'expired'], true)) {
                return ['error' => 'cannot_reopen_state'];
            }
            $locked->update([
                'status' => 'sent',
                'revision' => $locked->revision + 1,
                'accepted_at' => null,
                'signed_at' => null,
            ]);
            Log::info('Quote reopened', ['quote_id' => $locked->id, 'actor' => $actor, 'reason' => $reason]);
            return ['ok' => true, 'revision' => $locked->revision];
        });
    }

    /** Rotate the public link (leak response): new token + kill live challenges. */
    public function rotatePublicToken(Quote $quote): string
    {
        return DB::transaction(function () use ($quote) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();
            $token = Str::random(32);
            $locked->update(['public_token' => $token]);
            QuoteApprovalChallenge::where('quote_id', $locked->id)
                ->whereNull('consumed_at')
                ->update(['consumed_at' => now()]);
            return $token;
        });
    }

    /** Workspace-side send: draft -> sent. */
    public function send(Quote $quote): array
    {
        return DB::transaction(function () use ($quote) {
            $locked = Quote::where('id', $quote->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'draft') {
                return ['error' => 'invalid_state'];
            }
            $locked->update(['status' => 'sent']);
            return ['ok' => true];
        });
    }

    /** Mark past-expiry sent quotes expired (scheduler). Returns count. */
    public function markExpiredDue(): int
    {
        return Quote::where('status', 'sent')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now()->toDateString())
            ->update(['status' => 'expired']);
    }

    protected function notifyDecided(Quote $quote, QuoteApprovalEvent $event): void
    {
        try {
            $workspace = $quote->workspace;
            $to = $workspace?->email;
            if ($to) {
                Mail::to($to)->send(new QuoteApprovalDecided($quote, $event));
            }
        } catch (\Throwable $e) {
            Log::warning('Quote decision mail failed', ['quote_id' => $quote->id, 'error' => $e->getMessage()]);
        }
    }

    protected function maybeAutoConvert(Quote $quote): void
    {
        try {
            if (($quote->workspace?->quote_auto_convert ?? 'manual') !== 'draft_on_accept') {
                return;
            }
            if ($quote->converted_invoice_id) {
                return;
            }
            // Conversion itself is handled by QuoteConversionService for
            // idempotency; the policy hook only triggers it.
            app(QuoteConversionService::class)->convert($quote->fresh(), 'system:auto-convert');
        } catch (\Throwable $e) {
            Log::warning('Quote auto-convert failed', ['quote_id' => $quote->id, 'error' => $e->getMessage()]);
        }
    }
}
