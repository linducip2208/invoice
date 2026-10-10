<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProgrammaticSeoController extends Controller
{
    public function bestTools(string $year): View
    {
        $tools = $this->getBestToolsData();
        $metaTitle = "Best Invoicing Tools in {$year} - Top 10 Picks";
        $metaDescription = "Discover the top 10 best invoicing tools in {$year}. Compare features, pricing, and reviews to find the perfect invoicing software for your business. Updated for {$year}.";

        return view('seo.best-tools', compact('year', 'tools', 'metaTitle', 'metaDescription'));
    }

    public function alternatives(string $slug): View
    {
        $toolName = $this->slugToName($slug);
        $alternatives = $this->getAlternativesData($slug);
        $metaTitle = "Best {$toolName} Alternatives - Top 10 Competitors";
        $metaDescription = "Looking for {$toolName} alternatives? We've reviewed the top 10 competitors offering better features, pricing, and user experience. Find your perfect {$toolName} alternative.";

        return view('seo.alternatives', compact('slug', 'toolName', 'alternatives', 'metaTitle', 'metaDescription'));
    }

    public function compare(string $a, string $b): View
    {
        $toolA = $this->slugToName($a);
        $toolB = $this->slugToName($b);
        $metaTitle = "{$toolA} vs {$toolB}: Which Invoicing Tool is Better?";
        $metaDescription = "Detailed comparison of {$toolA} vs {$toolB}. Compare pricing, features, ease of use, integrations, and customer support to decide which invoicing software fits your business.";

        $comparison = $this->getComparisonData($a, $b);

        return view('seo.compare', compact('a', 'b', 'toolA', 'toolB', 'comparison', 'metaTitle', 'metaDescription'));
    }

    public function templates(): View
    {
        $metaTitle = 'Free Invoice Templates Gallery - Professional Designs';
        $metaDescription = 'Browse our gallery of professional invoice templates. Choose from classic, modern, minimalist, bold, and elegant designs. Download and customize for your business.';

        $templateCategories = $this->getTemplateData();

        return view('seo.templates', compact('templateCategories', 'metaTitle', 'metaDescription'));
    }

    protected function slugToName(string $slug): string
    {
        $map = [
            'invocely'    => 'Invocely',
            'freshbooks'  => 'FreshBooks',
            'quickbooks'  => 'QuickBooks',
            'xero'        => 'Xero',
            'zoho-invoice' => 'Zoho Invoice',
            'wave'        => 'Wave',
            'invoice-ninja' => 'Invoice Ninja',
            'harvest'     => 'Harvest',
            'and-co'      => 'AND.CO',
            'square-invoices' => 'Square Invoices',
            'paymo'       => 'Paymo',
            'hiveage'     => 'Hiveage',
        ];

        return $map[$slug] ?? ucwords(str_replace('-', ' ', $slug));
    }

    protected function getBestToolsData(): array
    {
        return [
            ['name' => 'Invocely', 'slug' => 'invocely', 'rating' => 4.9, 'price' => 'Free / Premium', 'description' => 'Modern SaaS invoicing with recurring billing, public invoice sharing, multiple templates, and real-time analytics. Built for freelancers and small businesses who need enterprise-grade features without the complexity.'],
            ['name' => 'FreshBooks', 'slug' => 'freshbooks', 'rating' => 4.7, 'price' => '$19/mo', 'description' => 'Award-winning cloud accounting software with powerful invoicing capabilities. Double-entry bookkeeping, bank reconciliation, and project profitability tracking.'],
            ['name' => 'QuickBooks', 'slug' => 'quickbooks', 'rating' => 4.5, 'price' => '$30/mo', 'description' => 'Industry-leading accounting suite with comprehensive invoicing. Features include automated billing, expense tracking, payroll integration, and extensive reporting.'],
            ['name' => 'Xero', 'slug' => 'xero', 'rating' => 4.6, 'price' => '$15/mo', 'description' => 'Cloud accounting platform with unlimited invoices. Multi-currency support, inventory tracking, and seamless bank connections make it ideal for growing businesses.'],
            ['name' => 'Zoho Invoice', 'slug' => 'zoho-invoice', 'rating' => 4.4, 'price' => 'Free', 'description' => 'Completely free invoicing solution for small businesses. Customizable templates, expense tracking, multi-language support, and integration with Zoho ecosystem.'],
            ['name' => 'Wave', 'slug' => 'wave', 'rating' => 4.3, 'price' => 'Free', 'description' => 'Free accounting and invoicing platform for entrepreneurs. Unlimited invoices, receipt scanning, and basic reporting at no cost.'],
            ['name' => 'Invoice Ninja', 'slug' => 'invoice-ninja', 'rating' => 4.5, 'price' => 'Free / $12/mo', 'description' => 'Open-source invoicing with client portal, recurring invoices, project tracking, and expense management. Self-hosted or cloud options available.'],
            ['name' => 'Harvest', 'slug' => 'harvest', 'rating' => 4.4, 'price' => '$12/mo', 'description' => 'Time tracking and invoicing combined. Perfect for agencies and consultants who bill by the hour with automatic timesheet-to-invoice conversion.'],
            ['name' => 'Square Invoices', 'slug' => 'square-invoices', 'rating' => 4.3, 'price' => 'Free / 2.9% + 30c', 'description' => 'Payment-first invoicing with integrated POS. Accept credit cards, ACH, and Apple Pay directly through invoices. Ideal for retail and service businesses.'],
            ['name' => 'Paymo', 'slug' => 'paymo', 'rating' => 4.2, 'price' => '$9.95/mo', 'description' => 'Full project management with invoicing built-in. Kanban boards, Gantt charts, time tracking, and automated billing make it a complete agency solution.'],
        ];
    }

    protected function getAlternativesData(string $slug): array
    {
        $toolName = $this->slugToName($slug);
        $all = $this->getBestToolsData();

        $alternatives = array_filter($all, fn ($t) => $t['slug'] !== $slug);
        $result = [];

        foreach ($alternatives as $alt) {
            $result[] = [
                'name'        => $alt['name'],
                'slug'        => $alt['slug'],
                'rating'      => $alt['rating'],
                'price'       => $alt['price'],
                'description' => $alt['description'],
                'advantage'   => $this->getAdvantage($alt['slug']),
            ];
        }

        return $result;
    }

    protected function getAdvantage(string $slug): string
    {
        $advantages = [
            'invocely'    => 'Modern UI with programmatic SEO built-in and recurring invoice automation.',
            'freshbooks'  => 'Double-entry accounting with professional invoice customization.',
            'quickbooks'  => 'Comprehensive accounting suite with payroll and inventory integration.',
            'xero'        => 'Unlimited users and invoices with strong multi-currency support.',
            'zoho-invoice' => 'Completely free with multi-language and multi-currency capabilities.',
            'wave'        => '100% free accounting with unlimited invoicing and receipt scanning.',
            'invoice-ninja' => 'Open-source with self-hosting option and full API access.',
            'harvest'     => 'Seamless time tracking to invoice conversion for hourly billing.',
            'square-invoices' => 'Built-in payment processing with POS integration.',
            'paymo'       => 'Unified project management and invoicing platform.',
            'hiveage'     => 'Global payment gateway support with multi-currency invoicing.',
            'and-co'      => 'Contracts and invoicing for freelancers in one app.',
        ];

        return $advantages[$slug] ?? 'Competitive alternative with strong feature set and value pricing.';
    }

    protected function getComparisonData(string $a, string $b): array
    {
        return [
            'pricing' => [
                'label' => 'Pricing',
                'a'     => $this->getFeatureValue($a, 'pricing'),
                'b'     => $this->getFeatureValue($b, 'pricing'),
                'winner' => $this->getPricingWinner($a, $b),
            ],
            'ease_of_use' => [
                'label' => 'Ease of Use',
                'a'     => $this->getFeatureValue($a, 'ease_of_use'),
                'b'     => $this->getFeatureValue($b, 'ease_of_use'),
                'winner' => $a,
            ],
            'templates' => [
                'label' => 'Templates',
                'a'     => $this->getFeatureValue($a, 'templates'),
                'b'     => $this->getFeatureValue($b, 'templates'),
                'winner' => $b,
            ],
            'automation' => [
                'label' => 'Automation',
                'a'     => $this->getFeatureValue($a, 'automation'),
                'b'     => $this->getFeatureValue($b, 'automation'),
                'winner' => $a,
            ],
            'integrations' => [
                'label' => 'Integrations',
                'a'     => $this->getFeatureValue($a, 'integrations'),
                'b'     => $this->getFeatureValue($b, 'integrations'),
                'winner' => $b,
            ],
            'support' => [
                'label' => 'Customer Support',
                'a'     => $this->getFeatureValue($a, 'support'),
                'b'     => $this->getFeatureValue($b, 'support'),
                'winner' => $a,
            ],
        ];
    }

    protected function getFeatureValue(string $slug, string $feature): string
    {
        $data = [
            'invocely' => [
                'pricing'      => 'Free tier available, premium from $10/mo',
                'ease_of_use'  => 'Clean, intuitive interface with guided setup',
                'templates'    => '5 beautiful templates (classic, modern, minimal, bold, elegant)',
                'automation'   => 'Built-in recurring invoices with flexible scheduling',
                'integrations' => 'API-first architecture for custom integrations',
                'support'      => 'Email and chat support, comprehensive documentation',
            ],
            'freshbooks' => [
                'pricing'      => 'Starts at $19/mo, premium at $60/mo',
                'ease_of_use'  => 'Excellent UX with mobile apps for iOS and Android',
                'templates'    => 'Professional templates with full customization',
                'automation'   => 'Automated late payment reminders and recurring billing',
                'integrations' => '200+ integrations including Shopify, Gusto, G Suite',
                'support'      => 'Award-winning phone support during business hours',
            ],
            'quickbooks' => [
                'pricing'      => 'Starts at $30/mo, advanced at $200/mo',
                'ease_of_use'  => 'Feature-rich but steeper learning curve',
                'templates'    => 'Standard templates with limited design customization',
                'automation'   => 'Advanced automation rules and scheduled transactions',
                'integrations' => '650+ app integrations across all business categories',
                'support'      => 'Phone, chat, and community forum support',
            ],
            'xero' => [
                'pricing'      => 'Starts at $15/mo, established at $78/mo',
                'ease_of_use'  => 'Modern dashboard with smart reconciliation',
                'templates'    => 'Clean templates with branding customization',
                'automation'   => 'Bank rules and repeating invoices with smart categorization',
                'integrations' => '800+ integrations including Stripe, HubSpot, PayPal',
                'support'      => '24/7 online support, no phone support on basic plan',
            ],
        ];

        $default = [
            'pricing'      => 'Competitive pricing with free trial available',
            'ease_of_use'  => 'User-friendly interface designed for business owners',
            'templates'    => 'Multiple professional templates to choose from',
            'automation'   => 'Recurring billing and payment reminders included',
            'integrations' => 'Popular third-party integrations supported',
            'support'      => 'Email and knowledge base support available',
        ];

        return $data[$slug][$feature] ?? $default[$feature];
    }

    protected function getPricingWinner(string $a, string $b): string
    {
        $pricing = [
            'invocely'     => 1,
            'zoho-invoice' => 1,
            'wave'         => 1,
            'square-invoices' => 2,
            'invoice-ninja' => 2,
            'paymo'        => 3,
            'harvest'      => 3,
            'xero'         => 3,
            'freshbooks'   => 4,
            'quickbooks'   => 5,
        ];

        $scoreA = $pricing[$a] ?? 3;
        $scoreB = $pricing[$b] ?? 3;

        if ($scoreA < $scoreB) {
            return $a;
        }
        if ($scoreB < $scoreA) {
            return $b;
        }

        return 'tie';
    }

    protected function getTemplateData(): array
    {
        return [
            [
                'category' => 'Professional',
                'description' => 'Clean, corporate-ready templates for B2B invoicing.',
                'templates' => [
                    ['name' => 'Classic', 'slug' => 'classic', 'description' => 'Traditional invoice layout with clear sections for bill-to, items, and totals. Best for formal business relationships.'],
                    ['name' => 'Modern', 'slug' => 'modern', 'description' => 'Contemporary design with card-based layout and modern typography. Great for tech companies and agencies.'],
                ],
            ],
            [
                'category' => 'Creative',
                'description' => 'Eye-catching designs that make your brand stand out.',
                'templates' => [
                    ['name' => 'Bold', 'slug' => 'bold', 'description' => 'Striking design with strong color accents and bold typography. Perfect for creative agencies and design studios.'],
                    ['name' => 'Elegant', 'slug' => 'elegant', 'description' => 'Sophisticated layout with refined typography and subtle styling. Ideal for premium brands and luxury services.'],
                ],
            ],
            [
                'category' => 'Simple',
                'description' => 'No-frills templates focused on clarity and speed.',
                'templates' => [
                    ['name' => 'Minimal', 'slug' => 'minimal', 'description' => 'Stripped-down, content-first design with maximum readability. Best for freelancers who value simplicity.'],
                ],
            ],
        ];
    }
}
