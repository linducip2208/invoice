# QUOTE APPROVAL — EVIDENCE (what is stored, where, who sees it)

## quote_approval_events (append-only)

Per decision: workspace, quote, **revision**, client, portal user (nullable),
name/role/email snapshots, `verification_method` (portal|otp|manual),
`authority_basis` (designated_approver|verified_contact|workspace_manual),
consent version + full snapshot, **document_sha256**, pdf_sha256 (when a PDF
snapshot exists; currently null — canonical JSON is the artifact),
outcome, decline reason, decided_at UTC, ip/ua (privacy: workspace-visible
only), correlation id. Unique key (quote, revision, outcome, approver_email).

Retention: events are never updated/deleted by the app (no routes);
corrections are new events/revisions. Access: owning workspace roles only
(quotes.show evidence card); public pages expose outcome + timestamp +
revision + document hash ONLY (no emails, no audit internals) — tested (a7).

## quote_approval_challenges (transient)

Bound to (quote, revision, recipient, purpose). Stores ONLY token_hash
(SHA-256) + otp_hash (bcrypt); raw values exist solely in the issuance
return + the email body. Consumed on decision/withdraw/rotation/reopen;
expired rows remain as audit residue without secrets.

## Signatures

Typed/drawn marks are supporting records attached post-evidence
(`signatures` polymorphic), explicitly NOT authority. External qualified
e-signature remains a documented integration point (manual-review status),
not a claim.

## Notifications

Mails (`quote-otp`, `quote-decided`) send AFTER commit with failure logging;
idempotent repeats never resend. Workspace recipient only; no approver PII
to unrelated parties.
