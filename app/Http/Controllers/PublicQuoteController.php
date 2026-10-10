<?php

namespace App\Http\Controllers;

use App\Models\ClientPortalUser;
use App\Models\Quote;
use App\Services\QuoteApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Secure public quotation review + approval.
 *
 * Threat posture: the link token proves POSSESSION only. Acceptance always
 * requires a verified channel (authenticated portal approver or OTP bound
 * to one quote + revision + recipient + purpose) plus explicit consent.
 * Viewing never mutates state. All state changes are POST + CSRF.
 */
class PublicQuoteController extends Controller
{
    public function __construct(
        protected QuoteApprovalService $approvals,
    ) {}

    protected function findByToken(string $token): Quote
    {
        // Token is the sole lookup key: 32 random chars, unique, unguessable.
        // 404 either way so existence is not leaked beyond "invalid link".
        return Quote::withoutGlobalScope('workspace')
            ->with('client', 'workspace')
            ->where('public_token', $token)
            ->firstOrFail();
    }

    protected function noStore($response)
    {
        // Sensitive per-recipient page: never stored in shared caches.
        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Referrer-Policy', 'no-referrer');
    }

    /** GET: read-only review. Never mutates state (safe for crawlers/previews). */
    public function show(string $token)
    {
        $quote = $this->findByToken($token);
        $portalUser = Auth::guard('client_portal')->user();
        $portalCanApprove = $this->portalCanApprove($portalUser, $quote);

        return $this->noStore(response()->view('public.quotes.show', compact(
            'quote', 'portalUser', 'portalCanApprove'
        )));
    }

    /** POST: request an OTP for accept|reject. Generic response (no enumeration). */
    public function requestOtp(string $token, Request $request)
    {
        $quote = $this->findByToken($token);
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'purpose' => ['required', 'in:accept,reject'],
        ]);

        $email = strtolower(trim($validated['email']));
        $recipient = $this->resolveRecipient($quote, $email);

        if ($recipient) {
            $result = $this->approvals->requestChallenge($quote, $validated['purpose'], $email, $recipient['portal_user']);
            if (! isset($result['error'])) {
                [$challenge, $rawToken, $otp] = $result;
                $this->approvals->sendOtpMail($quote, $email, $otp);
                // Verification session binds browser -> challenge WITHOUT
                // putting any credential in the URL (no referrer/history leak).
                session(['quote_otp_' . $quote->id => $challenge->id]);
            } else {
                Log::info('Quote OTP not issued', ['quote_id' => $quote->id, 'reason' => $result['error']]);
            }
        }
        // Identical next step whether or not the address is on file:
        // go to the code-entry form (it reveals nothing by itself).
        return redirect()->route('public.quote.verify-form', ['token' => $token, 'purpose' => $validated['purpose']])
            ->with('status', __('If this address is on file for this quotation, an approval code was sent. It expires in 15 minutes.'));
    }

    /** GET: OTP entry form. Reveals nothing about quote validity beyond show. */
    public function verifyForm(string $token)
    {
        $quote = $this->findByToken($token);
        return $this->noStore(response()->view('public.quotes.verify', compact('quote')));
    }

    /** POST: check the OTP, then park the verified challenge in session. */
    public function verify(string $token, Request $request)
    {
        $quote = $this->findByToken($token);
        $validated = $request->validate([
            'otp' => ['required', 'string', 'max:10'],
            'purpose' => ['required', 'in:accept,reject'],
        ]);

        $challengeId = session('quote_otp_' . $quote->id);
        $challenge = $challengeId
            ? \App\Models\QuoteApprovalChallenge::find($challengeId)
            : null;

        if (! $challenge || $challenge->quote_id !== $quote->id) {
            return back()->withErrors(['otp' => __('Request a fresh code first.')])->withInput();
        }

        // The token never travels in URLs/forms: re-derive is impossible, so
        // verification uses the session-bound challenge + OTP only.
        $result = $this->verifySessionChallenge($challenge, $validated['otp'], $validated['purpose']);

        if (isset($result['error'])) {
            return back()->withErrors(['otp' => $this->otpErrorMessage($result['error'])])->withInput();
        }

        session(['quote_verified_' . $quote->id => $challenge->id]);
        session(['quote_verified_rev_' . $quote->id => $challenge->revision]);
        return redirect()->route('public.quote.confirm', $token);
    }

    /** GET: confirmation summary (revision, total, consent). No mutation. */
    public function confirmForm(string $token)
    {
        $quote = $this->findByToken($token);
        $portalUser = Auth::guard('client_portal')->user();

        $context = $this->approvalContext($quote, $portalUser);
        if ($context['mode'] === 'none') {
            return redirect()->route('public.quote.verify-form', $token)
                ->with('error', __('Verification is required before approval.'));
        }

        return $this->noStore(response()->view('public.quotes.confirm', [
            'quote' => $quote,
            'context' => $context,
            'consent' => QuoteApprovalService::consentText($quote),
            'hash' => QuoteApprovalService::documentHash($quote),
        ]));
    }

    /** POST: final approval. Revalidates EVERYTHING inside the service transaction. */
    public function confirm(string $token, Request $request)
    {
        $quote = $this->findByToken($token);
        $validated = $request->validate([
            'purpose' => ['required', 'in:accept,reject'],
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'consent' => ['accepted'],
            'reason' => ['nullable', 'string', 'max:500'],
            'signature_typed' => ['nullable', 'string', 'max:120'],
        ]);

        $portalUser = Auth::guard('client_portal')->user();
        $context = $this->approvalContext($quote, $portalUser);
        if ($context['mode'] === 'none') {
            return redirect()->route('public.quote.verify-form', $token)
                ->with('error', __('Verification is required before approval.'));
        }
        // OTP challenges bind their purpose; portal approvers choose on the page.
        if ($context['purpose'] !== null && $context['purpose'] !== $validated['purpose']) {
            return back()->with('error', __('Approval purpose mismatch. Start over.'));
        }

        $identity = [
            'method' => $context['method'],
            'portal_user' => $context['portal_user'],
            'challenge' => $context['challenge'],
            // Revision the approver actually reviewed (frozen at verify time
            // for OTP; current for portal, rechecked live below).
            'revision' => $context['challenge']
                ? (int) session('quote_verified_rev_' . $quote->id, $quote->revision)
                : (int) $quote->revision,
            'name' => $validated['name'],
            'role' => $validated['role'] ?? null,
            'email' => $context['email'],
            'authority' => $context['authority'],
            'consent' => true,
        ];

        $result = $validated['purpose'] === 'accept'
            ? $this->approvals->accept($quote, $identity)
            : $this->approvals->reject($quote, $identity, $validated['reason'] ?? null);

        if (isset($result['error'])) {
            return back()->with('error', $this->decisionErrorMessage($result['error']))->withInput();
        }

        // Optional typed signature as supporting evidence (NOT authority).
        if (! empty($validated['signature_typed']) && ($result['event'] ?? null) && empty($result['duplicate'])) {
            try {
                app(\App\Services\Signature\SignatureCapture::class)->capture(
                    subject: $quote->fresh(),
                    signerName: $validated['name'],
                    signerEmail: $context['email'],
                    signatureTyped: $validated['signature_typed'],
                    signerRole: 'client',
                );
            } catch (\Throwable $e) {
                Log::warning('Quote supporting signature failed', ['quote_id' => $quote->id]);
            }
        }

        // Single-use verification: clear session binding after decision.
        session()->forget(['quote_verified_' . $quote->id, 'quote_verified_rev_' . $quote->id, 'quote_otp_' . $quote->id]);

        $message = $validated['purpose'] === 'accept'
            ? __('Quotation approved. Evidence recorded.')
            : __('Quotation declined. Evidence recorded.');
        return redirect()->route('public.quote.show', $token)->with('success', $message);
    }

    /**
     * Resolve who may act: portal designated approver, or OTP-verified channel.
     * Returns ['mode' => 'portal'|'otp'|'none', ...]. Never trusts link alone.
     */
    protected function approvalContext(Quote $quote, $portalUser): array
    {
        // Mode A: authenticated portal approver, rechecked on every POST.
        if ($this->portalCanApprove($portalUser, $quote)) {
            return [
                'mode' => 'portal',
                'method' => 'portal',
                'portal_user' => $portalUser,
                'challenge' => null,
                'email' => $portalUser->email,
                'authority' => 'designated_approver',
                // Portal approvers choose accept vs decline on the confirm page.
                'purpose' => null,
            ];
        }

        // Mode B: OTP-verified session binding.
        $challengeId = session('quote_verified_' . $quote->id);
        $challenge = $challengeId ? \App\Models\QuoteApprovalChallenge::find($challengeId) : null;
        if ($challenge && $challenge->quote_id === $quote->id
            && (int) $challenge->revision === (int) $quote->revision
            && $challenge->consumed_at === null && $challenge->expires_at->isFuture()) {
            // Recheck recipient binding + contact still valid.
            $recipient = $this->resolveRecipient($quote, $challenge->contact_email);
            if ($recipient) {
                return [
                    'mode' => 'otp',
                    'method' => 'otp',
                    'portal_user' => $recipient['portal_user'],
                    'challenge' => $challenge,
                    'email' => $challenge->contact_email,
                    'authority' => $recipient['authority'],
                    'purpose' => $challenge->purpose,
                ];
            }
        }

        return ['mode' => 'none'];
    }

    protected function portalCanApprove($portalUser, Quote $quote): bool
    {
        return $portalUser
            && $portalUser->is_active
            && $portalUser->can_approve_quotes
            && $portalUser->email_verified_at !== null
            && (int) $portalUser->client_id === (int) $quote->client_id
            && (int) $portalUser->workspace_id === (int) $quote->workspace_id;
    }

    /**
     * Recipient resolution: portal user (verified email, active) for this
     * client, else the client email on file (workspace-designated contact).
     * Unknown addresses resolve to null (generic response upstream).
     */
    protected function resolveRecipient(Quote $quote, string $email): ?array
    {
        $portalUser = ClientPortalUser::where('workspace_id', $quote->workspace_id)
            ->where('client_id', $quote->client_id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('is_active', true)
            ->whereNotNull('email_verified_at')
            ->first();

        if ($portalUser) {
            return ['portal_user' => $portalUser, 'authority' => 'designated_approver'];
        }
        if ($quote->client && strtolower(trim($quote->client->email ?? '')) === $email && $email !== '') {
            return ['portal_user' => null, 'authority' => 'verified_contact'];
        }
        return null;
    }

    protected function verifySessionChallenge($challenge, string $otp, string $purpose): array
    {
        if ($challenge->purpose !== $purpose) {
            return ['error' => 'challenge_invalid'];
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
        if (! \Illuminate\Support\Facades\Hash::check($otp, $challenge->otp_hash)) {
            $challenge->increment('attempts');
            return ['error' => 'invalid_otp'];
        }
        return ['ok' => true];
    }

    protected function otpErrorMessage(string $code): string
    {
        return match ($code) {
            'invalid_otp' => __('Incorrect code. Check and try again.'),
            'challenge_expired' => __('This code has expired. Request a new one.'),
            'challenge_used' => __('This code was already used. Request a new one.'),
            'too_many_attempts' => __('Too many attempts. Request a new code.'),
            default => __('Verification failed. Request a new code.'),
        };
    }

    protected function decisionErrorMessage(string $code): string
    {
        return match ($code) {
            'already_accepted' => __('This quotation was already approved.'),
            'already_rejected' => __('This quotation was already declined.'),
            'withdrawn' => __('This quotation was withdrawn by the issuer.'),
            'expired' => __('This quotation has expired.'),
            'revision_changed' => __('The quotation changed since your review. Please review the new revision.'),
            'portal_required' => __('This quotation requires approval by an authorized portal approver.'),
            'challenge_invalid' => __('Your verification is no longer valid. Start over.'),
            default => __('Approval could not be recorded in the current state.'),
        };
    }
}
