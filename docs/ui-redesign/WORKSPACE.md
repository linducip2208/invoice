# Workspace Dashboard — Premium Business

- KPI: collected, outstanding, paid count, overdue + collection rate, total customers, total invoiced, upcoming due.
- Chart: 8-week collections SVG (tanpa dep eksternal), status donut, aging 5 bucket.
- Widgets: recent invoices, quotes pending, top customers (paid sum), upcoming due, recent payments, quick actions.
- Semua query workspace-scoped via `BelongsToWorkspace`. Isolasi diuji (`IsolationTest` + manual 403 lintas workspace).
