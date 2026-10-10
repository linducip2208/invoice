# FINANCIAL INVARIANTS (tested in FinancialInvariantsTest, 4/4 green)

1. `invoice.total` derives from persisted lines + header tax/discount policy
   (verified end-to-end through `invoices.store`: subtotal/tax/discount/total
   recomputed from items, not trusted from input).
2. `balance_due = total − amount_paid` after payments (no credits).
3. Credit notes are NOT payments: apply reduces `balance_due` only;
   `amount_paid` untouched; cancel restores the exact balance. Reports must
   use `amount_paid` for cash-received, never `total − balance_due`.
4. Late fees: policy-gated (`one_time` single, `monthly` ≥25d gap), computed
   from current balance, applied under row lock + recheck (no TOCTOU
   compounding); reverse restores totals exactly.
5. Quote conversion preserves per-line tax rates and recomputes totals
   server-side; converted invoices are born `draft` with zero payment.
6. Money stays `decimal` in schema; PHP-side rounding is explicit
   (`round(..., 2)` at line and total level). Full minor-units migration is
   deferred (needs data migration + rollback plan).

## Late-fee policy (workspace default columns)

Basis: percent of current `balance_due` or fixed amount; frequency one_time /
monthly / none; no automatic cap — set `late_fee_value` deliberately and
prefer one_time for fixed penalties. Reversal deletes the fee row and
restores totals (controller actions are audit-logged).
