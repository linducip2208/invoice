# ROLE ACCESS MATRIX (generated 2026-10-10)

| Pintu | Jumlah route | Penegakan |
|---|---|---|
| Publik tanpa auth | 44 | rate-limit selektif + token tak-tertebak + no-store |
| Auth workspace | 266 | session + workspace scope + 403/404 |
| Portal guard terpisah | 11 | guard client_portal + EnsurePortalActive + can_approve |
| Superadmin | 41 | middleware superadmin + section tersembunyi |
| Plan-gated | 33 | EnforcePlanLimits server-side |
| Token publik {token} | 11 | bearer possession-only, verifikasi terpisah |

Detail per route: ROUTE-MAP.md. Bukti uji: IsolationTest, NavIntegrityTest, QuoteApprovalTest.
