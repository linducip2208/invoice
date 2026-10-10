# QUOTE APPROVAL — THREAT MODEL (2026-10-10)

Scope: public quotation review/approval (`/q/{token}` + OTP/confirm),
portal approval, workspace quote actions, conversion.

## Trust levels (what each factor actually proves)

| # | Factor | Proves | Does NOT prove |
|---|---|---|---|
| 1 | Possession of `/q/{token}` | Bearer had the link | Identity, authority, intent |
| 2 | Email OTP to on-file address | Access to that mailbox at that time | Corporate signing authority |
| 3 | Portal login (verified email) | Access to issued credentials | Approval authority (needs flag) |
| 4 | `can_approve_quotes` designation | Workspace explicitly delegated approval | Legal capacity to bind a company |
| 5 | Typed name / drawn signature image | What the visitor typed/drew | Identity (self-asserted) |
| 6 | Consent checkbox + revision hash | The exact version reviewed | Understanding or authority |

Authority for corporate quotes is therefore EVIDENCE GRADED, never absolute:
records store `authority_basis` (`designated_approver` | `verified_contact` |
`workspace_manual`) and the UI/docs disclose the level.

## Threats → mitigations

| Threat | Mitigation (code) |
|---|---|
| Leaked/forwarded link | 64-char random token; rotation invalidates; no acceptance from view |
| Sequential-ID guessing | Token lookup only (`/q/1` → 404); integer IDs never accepted |
| Wrong-person / ex-employee | OTP only to on-file address; portal approver flag; revocation immediate |
| Unverified email | Resolve: active+verified portal user, else client email on file; unknown → generic response, no challenge |
| Unauthorized representative | Self-declared name/role recorded as CLAIM; high-risk quotes force portal approver |
| OTP brute force | 6-digit + max 5 attempts + 15-min expiry + resend cooldown + route throttle |
| OTP replay | Single-use consume inside transaction; idempotency key per approver |
| Concurrent acceptance | Row lock + unique evidence key + duplicate-safe catch |
| Double submit | Double-submit guard + idempotent repeat = same event |
| Changed quote mid-flow | Revision frozen at verify; mismatch → `revision_changed`; reopen bumps revision |
| Expired/withdrawn acceptance | Server-side eligibility at challenge AND decision time + hourly scheduler |
| Cross-workspace | Scoped bindings (404) + explicit 403s; public token has no workspace trust |
| CSRF | POST-only mutations; Laravel 13 Sec-Fetch-Site + token (curl: 419 without origin) |
| Session fixation | `session()->regenerate()` on portal login |
| Email enumeration | Identical OTP responses; generic login errors; 404 without existence leak |
| Referrer/analytics/log leaks | `no-referrer` + `no-store` on public pages; OTP/token never in URL; secrets never logged (tested) |
| Crawler/preview mutation | GET is read-only (tested incl. crawler-style call) |
| Abuse/DoS | Throttles: show 60/min, OTP 5/min, verify 10/min, confirm 20/min |
| Duplicate invoices | Conversion identity (quote, revision) + lock + duplicate-safe return |

## Residual (accepted, documented)

- Without provider SMTP the OTP cannot be delivered (needs prod mail).
- OTP proves channel access, not legal authority — disclosed in UI + docs.
- True lock-concurrency behavior is MySQL-specific; sqlite tests prove logic,
  unique-key race catch, and serialization intent.
