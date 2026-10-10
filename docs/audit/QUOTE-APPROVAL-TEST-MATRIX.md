# QUOTE APPROVAL — TEST MATRIX (§21, 52 items)

Runner: `php vendor/bin/phpunit --filter QuoteApprovalTest` → **50/50 green**.
F47 proven via Playwright + curl (CSRF needs a real browser/server round-trip;
phpunit bypasses CSRF by framework design). c25 (consent version) is asserted
inside c24.

| # | Item | Test | Result |
|---|---|---|---|
| A1 | View never accepts | a1 | PASS |
| A2 | No GET mutation endpoint | a2 | PASS |
| A3 | Crawler changes nothing | a3 | PASS |
| A4 | Sequential IDs rejected | a4 | PASS |
| A5 | Invalid/rotated tokens rejected | a5 | PASS |
| A6 | Cross-workspace rejected | a6 | PASS |
| A7 | No evidence leak on public page | a7 | PASS |
| B8 | Unverified email cannot approve | b8 | PASS |
| B9 | OTP only to on-file contact | b9 | PASS |
| B10 | Wrong OTP rejected | b10 | PASS |
| B11 | Expired OTP rejected | b11 | PASS |
| B12 | Reused OTP: idempotent, no duplicate | b12 | PASS |
| B13 | Attempts throttled (5) | b13 | PASS |
| B14 | Resend cooldown | b14 | PASS |
| B15 | Revoked contact blocked (HTTP + service) | b15 | PASS |
| B16 | Other-client portal user blocked | b16 | PASS |
| B17 | Viewer without permission blocked | b17 | PASS |
| B18 | High-risk enforces portal | b18 | PASS |
| B19 | Missing config blocks safely | b19 | PASS |
| C20 | Expired quote refused | c20 | PASS |
| C21 | Withdrawn quote refused | c21 | PASS |
| C22 | Rejected → reopen policy + history | c22 | PASS |
| C23 | Revision change invalidates challenge | c23 | PASS |
| C24 | Evidence pins revision + hash (+c25 consent) | c24 | PASS |
| C26 | Repeat = same event | c26 | PASS |
| C27 | Second challenge moot | c27 | PASS |
| C28 | Failed txn leaves no trace | c28 | PASS |
| C29 | Totals immutable via params | c29 | PASS |
| C30 | No mass assignment (draft unconvertible) | c30 | PASS |
| C31 | Accepted locked (withdraw refused) | c31 | PASS |
| C32 | Evidence workspace-scoped | c32 | PASS |
| D33 | Accept → draft invoice | d33 | PASS |
| D34 | No conversion before acceptance | d34 | PASS |
| D35 | Concurrent conversion → one invoice | d35 | PASS |
| D36 | Retry after failure safe | d36 | PASS |
| D37 | Invoice references revision | d37 | PASS |
| D38 | Line-tax totals consistent | d38 | PASS |
| D39 | Conversion never pays | d39 | PASS |
| D40 | Outsider cannot convert | d40 | PASS |
| E41 | Anonymous decline impossible | e41 | PASS |
| E42 | Decline pins revision + reason | e42 | PASS |
| E43 | Withdrawal kills challenges | e43 | PASS |
| E44 | Expiry enforced server-side | e44 | PASS |
| E45 | New revision needs new approval | e45 | PASS |
| E46 | Reopen preserves history | e46 | PASS |
| F47 | CSRF enforced (browser/curl, phpunit bypasses by design) | E2E flow3 + curl 419 | PASS |
| F48 | OTP/accept endpoints rate limited | f48 + E2E | PASS |
| F49 | No secrets in logs/storage | f49 | PASS |
| F50 | No-referrer + no-store headers | f50 | PASS |
| F51 | No enumeration | f51 | PASS |
| F52 | Failures leave valid state | f52 | PASS |

Concurrency note: sqlite serializes the tested paths (locks degrade to
transactions); the unique evidence key + duplicate-safe catch is the
MySQL-true mechanism. A live MySQL race test is NOT_RUN (no server here).
