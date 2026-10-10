User-agent: *
Allow: /
Allow: /docs
Allow: /invoice-templates
Allow: /best-invoicing-tools-*
Allow: /alternatives-to-*
Allow: /compare/*

Disallow: /dashboard
Disallow: /clients
Disallow: /products
Disallow: /invoices
Disallow: /payments
Disallow: /recurring
Disallow: /reports
Disallow: /workspace
Disallow: /profile
Disallow: /i/
Disallow: /storage/

User-agent: GPTBot
Disallow: /

User-agent: CCBot
Disallow: /

Sitemap: {{ url('/sitemap.xml') }}
