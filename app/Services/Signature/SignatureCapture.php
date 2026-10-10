<?php

namespace App\Services\Signature;

use App\Models\Signature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureCapture
{
    /**
     * Capture a signature for a polymorphic subject (quote/invoice/contract).
     *
     * @param  string  $signatureDataUrl  Base64-encoded data URL from canvas (or null if typed-only)
     */
    public function capture(
        Model $subject,
        string $signerName,
        string $signerEmail,
        ?string $signatureDataUrl = null,
        ?string $signatureTyped = null,
        ?string $signerRole = 'client',
    ): Signature {
        $imagePath = null;
        if ($signatureDataUrl && str_starts_with($signatureDataUrl, 'data:image/')) {
            [$header, $data] = explode(',', $signatureDataUrl, 2);
            $ext = 'png';
            if (str_contains($header, 'svg')) $ext = 'svg';
            elseif (str_contains($header, 'jpeg')) $ext = 'jpg';
            $bin = base64_decode($data);
            $filename = 'signatures/' . class_basename($subject) . '/' . $subject->getKey() . '/' . Str::random(16) . '.' . $ext;
            Storage::disk('public')->put($filename, $bin);
            $imagePath = $filename;
        }

        $hash = $this->hashSubject($subject);

        return Signature::create([
            'workspace_id' => $subject->workspace_id ?? workspace()?->id,
            'subject_type' => get_class($subject),
            'subject_id'   => $subject->getKey(),
            'signer_name'  => $signerName,
            'signer_email' => $signerEmail,
            'signer_role'  => $signerRole,
            'signature_image_path' => $imagePath ?? '',
            'signature_typed' => $signatureTyped,
            'ip'           => request()->ip(),
            'ua'           => substr(request()->userAgent() ?? '', 0, 500),
            'signed_at'    => now(),
            'verification_hash' => $hash,
        ]);
    }

    /**
     * Generate canonical state hash for tampering detection.
     */
    public function hashSubject(Model $subject): string
    {
        $subject->refresh();
        $canonical = json_encode([
            'type'  => get_class($subject),
            'id'    => $subject->getKey(),
            'data'  => $subject->only(array_keys($subject->getAttributes())),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return hash('sha256', $canonical);
    }

    public function isValid(Signature $sig): bool
    {
        $subject = $sig->subject;
        if (! $subject) return false;

        $currentHash = $this->hashSubject($subject);
        return hash_equals($sig->verification_hash, $currentHash);
    }
}
