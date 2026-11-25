<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $plans = Plan::where('is_public', true)->orderBy('sort_order')->take(3)->get();

        // Real platform counters — never fabricated. On a fresh install these are 0.
        $stats = [
            'workspaces' => (int) Workspace::count(),
            'users' => (int) User::count(),
            'invoices' => (int) Invoice::count(),
        ];

        // Gateways actually implemented in the codebase (docs + Payment flows).
        $gateways = ['Midtrans', 'Xendit', 'Tripay', 'Duitku', 'DOKU', 'OY!', 'iPaymu', 'Finpay'];

        // Invoice templates that actually exist as Blade templates.
        $templates = InvoiceTemplate::global()->orderBy('name')->get();
        if ($templates->isEmpty()) {
            $templates = collect([
                ['name' => 'Classic', 'slug' => 'classic', 'description' => 'Traditional branded layout with striped table header.'],
                ['name' => 'Modern', 'slug' => 'modern', 'description' => 'Card-based minimal layout, borderless table.'],
                ['name' => 'Minimal', 'slug' => 'minimal', 'description' => 'Single-column text-only header, simple lines.'],
                ['name' => 'Bold', 'slug' => 'bold', 'description' => 'Full-width dark banner with accent rows.'],
                ['name' => 'Elegant', 'slug' => 'elegant', 'description' => 'Framed serif layout with centered brand.'],
                ['name' => 'Bilingual', 'slug' => 'bilingual', 'description' => 'Dual-language ID/EN layout for cross-border billing.'],
            ])->map(fn ($t) => (object) $t);
        } else {
            $descriptions = [
                'classic' => 'Traditional branded layout with striped table header.',
                'modern' => 'Card-based minimal layout, borderless table.',
                'minimal' => 'Single-column text-only header, simple lines.',
                'bold' => 'Full-width dark banner with accent rows.',
                'elegant' => 'Framed serif layout with centered brand.',
                'bilingual' => 'Dual-language ID/EN layout for cross-border billing.',
            ];
            $templates = $templates->map(function ($t) use ($descriptions) {
                $t->description = $descriptions[$t->slug] ?? ($t->config['layout'] ?? 'Production invoice template.');
                return $t;
            });
            if (! $templates->contains(fn ($t) => $t->slug === 'bilingual')) {
                $templates->push((object) ['name' => 'Bilingual', 'slug' => 'bilingual', 'description' => 'Dual-language ID/EN layout for cross-border billing.']);
            }
        }

        return view('welcome', compact('plans', 'stats', 'gateways', 'templates'));
    }
}
