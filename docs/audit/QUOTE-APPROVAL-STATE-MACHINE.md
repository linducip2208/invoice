# QUOTE STATE MACHINE (implemented in QuoteApprovalService)

```
DRAFT --send()--> SENT --requestChallenge()/OTP+confirm--> ACCEPTED --convert()--> CONVERTED (terminal, invoice linked)
  |                 |  \-> reject() -> REJECTED --reopen()--> SENT (revision+1)
  |                 |--> expiry_date past (checked at challenge + decision + hourly scheduler) => EXPIRED --reopen()--> SENT (revision+1)
  |                 \-> withdraw() -> WITHDRAWN --reopen()--> SENT (revision+1)
  \-> withdraw() -> WITHDRAWN
```

- VIEWED is an engagement observation, not a stored status.
- PENDING_VERIFICATION = live OTP challenge row (expires/consumed), not acceptance.
- Terminal states (accepted/converted/rejected/expired/withdrawn) never transition
  except controlled reopen (rejected/withdrawn/expired → sent + revision+1).
- Accepted/converted quotes reject withdrawal (`already_accepted`).
- Every transition runs in a transaction with row locks; illegal transitions
  return safe reason codes (`already_accepted`, `withdrawn`, `expired`,
  `revision_changed`, `portal_required`, `challenge_invalid`, ...).
- Reopen preserves all prior events; old challenges stay consumed; new revision
  requires fresh approval.
- `withdrawn` status added by migration 2026_10_10_000001 (MySQL enum extend).
