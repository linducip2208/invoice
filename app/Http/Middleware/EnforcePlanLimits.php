<?php

namespace App\Http\Middleware;

use App\Models\Invoice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePlanLimits
{
    public function handle(Request $request, Closure $next, string $action): Response
    {
        // Superadmin can access every gated feature for inspection / support.
        if ($request->user()?->isSuperadmin()) {
            return $next($request);
        }

        $workspace = workspace();

        if (! $workspace) {
            return $next($request);
        }

        $sub = $workspace->activeSubscription();

        if (! $sub->isActive() && $sub->status !== 'active') {
            return redirect()->route('billing.show')->with('error', __('Subscription kamu tidak aktif. Aktifkan dulu untuk lanjut.'));
        }

        return match ($action) {
            'invoice.create'    => $this->checkInvoiceCreate($request, $next, $workspace, $sub),
            'invoice.edit'      => $this->checkInvoiceEdit($request, $next, $workspace, $sub),
            'invoice.delete'    => $this->checkInvoiceDelete($request, $next, $workspace, $sub),
            'recurring.create'  => $this->checkRecurring($request, $next, $sub),
            'client.create'     => $this->checkClientCreate($request, $next, $workspace, $sub),
            'csv.import'        => $this->checkFeature($request, $next, $sub, 'csv_import', __('Import CSV hanya untuk Pro+.')),
            'bulk.action'       => $this->checkFeature($request, $next, $sub, 'bulk_actions', __('Bulk actions hanya untuk Pro+.')),
            'api.access'        => $this->checkFeature($request, $next, $sub, 'api_access', __('API access hanya untuk Business.')),
            'drag_drop'         => $this->checkFeature($request, $next, $sub, 'drag_drop_builder', __('Drag-drop builder hanya untuk Business.')),
            default             => $next($request),
        };
    }

    protected function checkInvoiceCreate(Request $request, Closure $next, $workspace, $sub): Response
    {
        $lifetimeLimit = $sub->feature('max_invoices_lifetime');

        if ($lifetimeLimit !== null) {
            $count = Invoice::where('workspace_id', $workspace->id)->withoutGlobalScopes()->count();

            if ($count >= (int) $lifetimeLimit) {
                return redirect()->route('billing.show')->with('error',
                    __('Plan :plan dibatasi :n invoice selamanya. Upgrade untuk invoice unlimited.', [
                        'plan' => $sub->plan->name,
                        'n'    => $lifetimeLimit,
                    ])
                );
            }
        }

        return $next($request);
    }

    protected function checkInvoiceEdit(Request $request, Closure $next, $workspace, $sub): Response
    {
        if ($sub->feature('invoice_lock', false)) {
            return redirect()->route('billing.show')->with('error',
                __('Plan :plan tidak bisa edit invoice. Upgrade untuk edit, delete, dan tambah invoice baru.', [
                    'plan' => $sub->plan->name,
                ])
            );
        }

        return $next($request);
    }

    protected function checkInvoiceDelete(Request $request, Closure $next, $workspace, $sub): Response
    {
        if ($sub->feature('invoice_lock', false)) {
            return redirect()->route('billing.show')->with('error',
                __('Invoice di plan Free tidak bisa dihapus. Upgrade ke Pro untuk delete.')
            );
        }

        return $next($request);
    }

    protected function checkRecurring(Request $request, Closure $next, $sub): Response
    {
        if (! $sub->feature('recurring_enabled', false)) {
            return redirect()->route('billing.show')->with('error',
                __('Recurring invoice hanya tersedia untuk plan Pro+.')
            );
        }

        return $next($request);
    }

    protected function checkClientCreate(Request $request, Closure $next, $workspace, $sub): Response
    {
        $maxClients = $sub->feature('max_clients');

        if ($maxClients !== null) {
            $count = $workspace->clients()->count();
            if ($count >= (int) $maxClients) {
                return redirect()->route('billing.show')->with('error',
                    __('Plan :plan dibatasi :n klien. Upgrade untuk unlimited.', [
                        'plan' => $sub->plan->name,
                        'n'    => $maxClients,
                    ])
                );
            }
        }

        return $next($request);
    }

    protected function checkFeature(Request $request, Closure $next, $sub, string $feature, string $message): Response
    {
        if (! $sub->feature($feature, false)) {
            return redirect()->route('billing.show')->with('error', $message);
        }

        return $next($request);
    }
}
