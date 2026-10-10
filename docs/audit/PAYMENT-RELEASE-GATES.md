# PAYMENT RELEASE GATES (per provider × environment)

Code verification exists for midtrans / xendit / tripay / duitku / doku
(`WebhookSignatureVerifier` + 4 automated tests with fake secrets).
Enforcement: secret configured → invalid signature 403, no state change;
no secret → hardened processing (transaction + lock + replay + stored-amount)
with loud `webhook_unverified` log.

| Provider | Unit (fake secret) | Sandbox live | Production |
|---|---|---|---|
| midtrans | PASS | BLOCKED (needs server key + sandbox) | BLOCKED |
| xendit | PASS | BLOCKED | BLOCKED |
| tripay | code only | BLOCKED | BLOCKED |
| duitku | code only | BLOCKED | BLOCKED |
| doku | code only | BLOCKED | BLOCKED |
| oy / ipaymu / finpay / generic | unsupported → unconfigured path | BLOCKED | BLOCKED |

Release rule: a provider is production-ready only after its sandbox row is
verified. An unsigned/invalidly-signed callback must never create payment or
settle an invoice in live mode — enforced in code when secrets exist.
