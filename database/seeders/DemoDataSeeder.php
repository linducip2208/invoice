<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RecurringInvoice;
use App\Models\User;
use App\Models\Workspace;
use App\Services\InvoiceService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@invocely.test'],
            [
                'name'              => 'Demo User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $workspace = Workspace::firstOrCreate(
            ['slug' => 'demo-company'],
            [
                'name'            => 'Demo Company',
                'email'           => 'hello@demo-company.test',
                'phone'           => '+1-555-0199',
                'address'         => '123 Business Ave, Suite 100',
                'city'            => 'San Francisco',
                'state'           => 'CA',
                'country'         => 'United States',
                'postal_code'     => '94105',
                'tax_id'          => 'TAX-12345-DEMO',
                'invoice_prefix'  => 'INV-',
                'next_invoice_no' => 1,
                'default_template' => 'classic',
                'currency'        => 'USD',
                'primary_color'   => '#2563EB',
                'accent_color'    => '#1E40AF',
            ]
        );

        $user->workspaces()->syncWithoutDetaching([$workspace->id => ['role' => 'admin']]);

        $clients = [];
        $clientNames = [
            ['name' => 'Acme Corporation', 'company' => 'Acme Corp', 'email' => 'billing@acme.test'],
            ['name' => 'Jane Smith', 'company' => 'Smith Consulting', 'email' => 'jane@smithconsulting.test'],
            ['name' => 'Bob Johnson', 'company' => 'Johnson LLC', 'email' => 'bob@johnson.test'],
            ['name' => 'Global Tech Solutions', 'company' => 'Global Tech Solutions', 'email' => 'accounts@globaltech.test'],
            ['name' => 'Creative Studios', 'company' => 'Creative Studios Inc', 'email' => 'hello@creativestudios.test'],
            ['name' => 'DataFlow Analytics', 'company' => 'DataFlow Analytics', 'email' => 'finance@dataflow.test'],
            ['name' => 'GreenLeaf Organics', 'company' => 'GreenLeaf Organics', 'email' => 'orders@greenleaf.test'],
            ['name' => 'CloudNine Hosting', 'company' => 'CloudNine Hosting LLC', 'email' => 'billing@cloudnine.test'],
            ['name' => 'PixelPerfect Design', 'company' => 'PixelPerfect Design', 'email' => 'info@pixelperfect.test'],
            ['name' => 'BuildRight Construction', 'company' => 'BuildRight Construction Co', 'email' => 'accounts@buildright.test'],
        ];

        foreach ($clientNames as $i => $c) {
            $clients[] = Client::create([
                'workspace_id' => $workspace->id,
                'name'         => $c['name'],
                'company_name'  => $c['company'],
                'email'        => $c['email'],
                'phone'        => '+1-555-' . str_pad((string) (1000 + $i), 4, '0', STR_PAD_LEFT),
                'address'      => ($i + 1) . '00 Market St',
                'city'         => 'San Francisco',
                'state'        => 'CA',
                'country'      => 'United States',
                'postal_code'  => '941' . str_pad((string) (10 + $i), 2, '0', STR_PAD_LEFT),
                'is_active'    => true,
            ]);
        }

        $products = [];
        $productData = [
            ['name' => 'Web Development - Hourly', 'price' => 85.00, 'category' => 'Development'],
            ['name' => 'UI/UX Design - Hourly', 'price' => 75.00, 'category' => 'Design'],
            ['name' => 'SEO Consultation', 'price' => 150.00, 'category' => 'Marketing'],
            ['name' => 'Content Writing - per 1000 words', 'price' => 50.00, 'category' => 'Content'],
            ['name' => 'Server Maintenance - Monthly', 'price' => 200.00, 'category' => 'Hosting'],
            ['name' => 'Logo Design', 'price' => 350.00, 'category' => 'Design'],
            ['name' => 'Email Marketing Setup', 'price' => 500.00, 'category' => 'Marketing'],
            ['name' => 'Mobile App Development - Hourly', 'price' => 95.00, 'category' => 'Development'],
            ['name' => 'Photo Editing - per image', 'price' => 15.00, 'category' => 'Design'],
            ['name' => 'Social Media Management - Monthly', 'price' => 600.00, 'category' => 'Marketing'],
            ['name' => 'Database Optimization', 'price' => 250.00, 'category' => 'Development'],
            ['name' => 'Video Editing - Hourly', 'price' => 65.00, 'category' => 'Content'],
            ['name' => 'WordPress Plugin Development', 'price' => 1200.00, 'category' => 'Development'],
            ['name' => 'Technical Support - Hourly', 'price' => 55.00, 'category' => 'Support'],
            ['name' => 'Cloud Migration Service', 'price' => 1500.00, 'category' => 'Hosting'],
        ];

        foreach ($productData as $p) {
            $products[] = Product::create([
                'workspace_id' => $workspace->id,
                'name'         => $p['name'],
                'description'  => 'Professional ' . strtolower($p['category']) . ' service.',
                'unit_price'   => $p['price'],
                'tax_rate'     => 0,
                'category'     => $p['category'],
                'is_active'    => true,
            ]);
        }

        $statuses = ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'];
        $invoices = [];

        for ($i = 0; $i < 20; $i++) {
            $client = $clients[array_rand($clients)];
            $numItems = rand(1, 4);
            $items = [];

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products[array_rand($products)];
                $qty = rand(1, 5);
                $price = (float) $product->unit_price;
                $itemTaxRate = (float) $product->tax_rate;
                $lineSubtotal = round($qty * $price, 2);
                $lineTax = round($lineSubtotal * ($itemTaxRate / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $items[] = [
                    'product_id'    => $product->id,
                    'description'   => $product->name,
                    'quantity'      => $qty,
                    'unit_price'    => $price,
                    'tax_rate'      => $itemTaxRate,
                    'tax_amount'    => $lineTax,
                    'discount_type'  => 'none',
                    'discount_value' => 0,
                    'subtotal'      => $lineSubtotal,
                    'total'         => $lineTotal,
                    'sort_order'    => $j + 1,
                ];
            }

            $taxRate = 10;
            $discountType = 'none';
            $discountValue = 0;

            if (rand(0, 3) === 0) {
                $discountType = rand(0, 1) ? 'percent' : 'fixed';
                $discountValue = $discountType === 'percent' ? rand(5, 15) : rand(10, 50);
            }

            $totals = InvoiceService::calculateTotals($items, $taxRate, $discountType, $discountValue);

            $statusPool = match (true) {
                $i < 3   => ['draft'],
                $i < 8   => ['sent'],
                $i < 13  => ['paid'],
                $i < 16  => ['partial', 'sent', 'overdue'],
                $i < 20  => ['overdue', 'cancelled', 'draft'],
                default   => ['sent'],
            };

            $status = $statusPool[array_rand($statusPool)];
            $issueDate = now()->subDays(rand(1, 60));
            $dueDate = (clone $issueDate)->addDays(30);

            $amountPaid = 0;
            $balanceDue = $totals['total'];
            if ($status === 'paid') {
                $amountPaid = $totals['total'];
                $balanceDue = 0;
            } elseif ($status === 'partial') {
                $amountPaid = round($totals['total'] * (rand(30, 70) / 100), 2);
                $balanceDue = round($totals['total'] - $amountPaid, 2);
            }

            $invoice = Invoice::create([
                'workspace_id'          => $workspace->id,
                'client_id'             => $client->id,
                'invoice_no'            => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'status'                => $status,
                'template'              => 'classic',
                'issue_date'            => $issueDate->toDateString(),
                'due_date'              => $dueDate->toDateString(),
                'subtotal'              => $totals['subtotal'],
                'tax_rate'              => $taxRate,
                'tax_amount'            => $totals['tax_amount'],
                'discount_type'         => $discountType,
                'discount_value'        => $discountValue,
                'discount_amount'       => $totals['discount_amount'],
                'total'                 => $totals['total'],
                'amount_paid'           => $amountPaid,
                'balance_due'           => $balanceDue,
                'currency'              => 'USD',
                'notes'                 => 'Thank you for your business!',
                'terms'                 => 'Payment is due within 30 days. Late payments may incur a 2% monthly fee.',
            ]);

            foreach ($items as $itemData) {
                InvoiceItem::create(array_merge($itemData, ['invoice_id' => $invoice->id]));
            }

            $invoices[] = $invoice;
        }

        $payments = [];
        foreach ($invoices as $invoice) {
            if (in_array($invoice->status, ['paid', 'partial']) && $invoice->amount_paid > 0) {
                $payments[] = Payment::create([
                    'invoice_id'     => $invoice->id,
                    'workspace_id'   => $workspace->id,
                    'amount'         => $invoice->amount_paid,
                    'payment_date'   => now()->subDays(rand(1, 30))->toDateString(),
                    'payment_method' => ['bank_transfer', 'credit_card', 'cash'][rand(0, 2)],
                    'reference_no'   => 'PAY-' . str_pad((string) (count($payments) + 1), 4, '0', STR_PAD_LEFT),
                    'notes'          => 'Payment received',
                ]);
            }
        }

        RecurringInvoice::create([
            'workspace_id'      => $workspace->id,
            'client_id'         => $clients[0]->id,
            'template'          => 'classic',
            'frequency'         => 'monthly',
            'interval_value'    => 1,
            'next_run_date'     => now()->addDays(7)->toDateString(),
            'max_occurrences'   => 12,
            'current_occurrence' => 0,
            'status'            => 'active',
            'invoice_data'      => [
                'items' => [
                    ['description' => 'Monthly Website Maintenance', 'quantity' => 1, 'unit_price' => 200, 'tax_rate' => 0],
                    ['description' => 'Monthly SEO Report', 'quantity' => 1, 'unit_price' => 150, 'tax_rate' => 0],
                ],
                'tax_rate'       => 10,
                'discount_type'  => 'none',
                'discount_value' => 0,
                'currency'       => 'USD',
                'notes'          => 'Monthly retainer services.',
                'terms'          => 'Net 15.',
                'subtotal'       => 350,
                'tax_amount'     => 35,
                'discount_amount' => 0,
                'total'          => 385,
            ],
        ]);

        RecurringInvoice::create([
            'workspace_id'      => $workspace->id,
            'client_id'         => $clients[3]->id,
            'template'          => 'modern',
            'frequency'         => 'monthly',
            'interval_value'    => 1,
            'next_run_date'     => now()->addDays(30)->toDateString(),
            'max_occurrences'   => null,
            'current_occurrence' => 0,
            'status'            => 'paused',
            'invoice_data'      => [
                'items' => [
                    ['description' => 'Cloud Infrastructure Support', 'quantity' => 1, 'unit_price' => 500, 'tax_rate' => 0],
                ],
                'tax_rate'       => 0,
                'discount_type'  => 'none',
                'discount_value' => 0,
                'currency'       => 'USD',
                'notes'          => 'Ongoing cloud infrastructure support.',
                'terms'          => 'Net 30.',
                'subtotal'       => 500,
                'tax_amount'     => 0,
                'discount_amount' => 0,
                'total'          => 500,
            ],
        ]);

        $workspace->update(['next_invoice_no' => 21]);
    }
}
