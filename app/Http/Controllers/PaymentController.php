<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\PaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with('invoice.client', 'paymentGateway')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sq) use ($search) {
                    $sq->where('reference_no', 'like', "%{$search}%")
                       ->orWhere('notes', 'like', "%{$search}%")
                       ->orWhereHas('invoice', fn($iq) => $iq->where('invoice_no', 'like', "%{$search}%"))
                       ->orWhereHas('invoice.client', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('method'), function ($q) use ($request) {
                $method = $request->method;
                if (str_starts_with($method, 'gateway_')) {
                    $gatewayId = (int) str_replace('gateway_', '', $method);
                    $q->where('payment_gateway_id', $gatewayId);
                } elseif ($method === 'gateway') {
                    $q->whereNotNull('payment_gateway_id');
                } else {
                    $q->where('payment_method', $method);
                }
            })
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('payment_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('payment_date', '<=', $request->date_to))
            ->latest('payment_date')
            ->paginate(20)
            ->withQueryString();

        $statsRow = Payment::query()
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('payment_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('payment_date', '<=', $request->date_to))
            ->selectRaw('COALESCE(SUM(amount),0) as total_paid, COUNT(*) as cnt')
            ->first();

        $totalPaid = (float) ($statsRow->total_paid ?? 0);
        $count = (int) ($statsRow->cnt ?? 0);

        $activeGateways = PaymentGateway::where('is_active', true)->orderBy('sort_order')->get();
        $methods = $this->availableMethods($activeGateways);

        return view('payments.index', compact('payments', 'totalPaid', 'count', 'methods'));
    }

    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount'              => 'required|numeric|min:0.01',
            'payment_date'        => 'required|date',
            'payment_method'      => 'required|string|max:50',
            'channel'             => 'nullable|string|max:50',
            'payment_gateway_id'  => 'nullable|exists:payment_gateways,id',
            'reference_no'        => 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:1000',
        ]);

        // If gateway is selected, create transaction via gateway
        $gatewayResult = null;
        if (!empty($validated['payment_gateway_id'])) {
            $gateway = PaymentGateway::findOrFail($validated['payment_gateway_id']);
            try {
                $service = app(PaymentGatewayService::class);
                $gatewayResult = $service->createTransaction($gateway, [
                    'order_id'       => 'INV-' . $invoice->id . '-' . time(),
                    'amount'         => $validated['amount'],
                    'invoice_no'     => $invoice->invoice_no,
                    'channel'        => $validated['channel'] ?? $validated['payment_method'],
                    'customer_name'  => $invoice->client->name ?? 'Customer',
                    'customer_email' => $invoice->client->email ?? '',
                    'customer_phone' => $invoice->client->phone ?? '',
                    'callback_url'   => route('webhooks.payment', ['gateway' => $gateway->gateway]),
                    'success_url'    => route('public.invoice.show', $invoice->public_token),
                    'cancel_url'     => route('public.invoice.show', $invoice->public_token),
                ]);
            } catch (\Exception $e) {
                Log::error('Payment gateway error', ['error' => $e->getMessage(), 'gateway' => $gateway->gateway]);
                return Redirect::back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
            }
        }

        $payment = Payment::create([
            'invoice_id'            => $invoice->id,
            'workspace_id'          => $invoice->workspace_id,
            'payment_gateway_id'    => $validated['payment_gateway_id'] ?? null,
            'gateway_transaction_id'=> $gatewayResult['transaction_id'] ?? null,
            'amount'                => $validated['amount'],
            'payment_date'          => $validated['payment_date'],
            'payment_method'        => $validated['channel'] ?? $validated['payment_method'],
            'channel'               => $validated['channel'] ?? $validated['payment_method'],
            'gateway_status'        => $gatewayResult['status'] ?? 'manual',
            'gateway_raw'           => $gatewayResult['raw'] ?? null,
            'reference_no'          => $validated['reference_no'] ?? null,
            'notes'                 => $validated['notes'] ?? null,
        ]);

        // For gateway payments, don't auto-confirm — wait for webhook
        if ($gatewayResult && $gatewayResult['payment_url']) {
            $this->recalculateInvoice($invoice);
            return redirect()->away($gatewayResult['payment_url']);
        }

        $this->recalculateInvoice($invoice);

        return Redirect::route('invoices.show', $invoice)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $invoice = $payment->invoice;
        $payment->delete();
        $this->recalculateInvoice($invoice);

        return Redirect::back()->with('success', 'Pembayaran dihapus.');
    }

    // ─── PUBLIC: Create gateway payment from public invoice page ──────────
    public function gatewayPay(Request $request, Invoice $invoice)
    {
        $gateway = PaymentGateway::where('id', $request->gateway_id)
            ->where('is_active', true)
            ->firstOrFail();

        $channel = $request->channel;
        $amount = $invoice->balance_due;

        if ($request->filled('amount') && $request->amount > 0 && $request->amount <= $invoice->balance_due) {
            $amount = $request->amount;
        }

        try {
            $service = app(PaymentGatewayService::class);
            $result = $service->createTransaction($gateway, [
                'order_id'       => 'INV-' . $invoice->id . '-' . time(),
                'amount'         => $amount,
                'invoice_no'     => $invoice->invoice_no,
                'channel'        => $channel,
                'customer_name'  => $invoice->client->name ?? 'Customer',
                'customer_email' => $invoice->client->email ?? '',
                'customer_phone' => $invoice->client->phone ?? '',
                'callback_url'   => route('webhooks.payment', ['gateway' => $gateway->gateway]),
                'success_url'    => route('public.invoice.show', $invoice->public_token),
                'cancel_url'     => route('public.invoice.show', $invoice->public_token),
            ]);

            Payment::create([
                'invoice_id'            => $invoice->id,
                'workspace_id'          => $invoice->workspace_id,
                'payment_gateway_id'    => $gateway->id,
                'gateway_transaction_id'=> $result['transaction_id'] ?? null,
                'amount'                => $amount,
                'payment_date'          => now(),
                'payment_method'        => $channel,
                'channel'               => $channel,
                'gateway_status'        => 'pending',
                'gateway_raw'           => $result['raw'] ?? null,
                'notes'                 => 'Pembayaran via ' . $gateway->name,
            ]);

            if ($result['payment_url']) {
                return redirect()->away($result['payment_url']);
            }

            return Redirect::route('public.invoice.show', $invoice->public_token)
                ->with('payment_va', $result['va_number'] ?? null);

        } catch (\Exception $e) {
            Log::error('Payment error', ['error' => $e->getMessage()]);
            return Redirect::route('public.invoice.show', $invoice->public_token)
                ->with('error', 'Gagal memproses pembayaran.');
        }
    }

    // ─── WEBHOOK: Receive callback from payment gateway ───────────────────
    public function webhook(Request $request, string $gateway)
    {
        Log::info('Payment webhook received', ['gateway' => $gateway, 'body' => $request->all()]);

        $transactionId = $this->extractTransactionId($gateway, $request);

        if (!$transactionId) {
            return response()->json(['status' => 'transaction_id not found'], 400);
        }

        $payment = Payment::where('gateway_transaction_id', $transactionId)->first();

        if (!$payment) {
            return response()->json(['status' => 'payment not found'], 404);
        }

        $status = $this->extractWebhookStatus($gateway, $request);
        $payment->update([
            'gateway_status' => $status,
            'gateway_raw'    => $request->all(),
        ]);

        if (in_array($status, ['settlement', 'capture', 'success', 'PAID', 'SUCCESS'])) {
            $payment->update(['amount' => $this->extractWebhookAmount($gateway, $request, $payment)]);
            $this->recalculateInvoice($payment->invoice);
        }

        return response()->json(['status' => 'ok']);
    }

    // ─── HELPERS ──────────────────────────────────────────────────────────

    protected function recalculateInvoice(Invoice $invoice): void
    {
        $amountPaid = $invoice->payments()->whereIn('gateway_status', ['settlement', 'capture', 'success', 'PAID', 'SUCCESS', 'manual', null])
            ->sum('amount');
        $balanceDue = $invoice->total - $amountPaid;

        $status = $invoice->status;
        if ($balanceDue <= 0) {
            $status = 'paid';
        } elseif ($amountPaid > 0) {
            $status = 'partial';
        }

        $invoice->update([
            'amount_paid' => $amountPaid,
            'balance_due' => max($balanceDue, 0),
            'status'      => $status,
        ]);
    }

    protected function availableMethods($activeGateways): array
    {
        $methods = [
            'cash'          => '💵 Tunai',
            'bank_transfer' => '🏦 Transfer Bank Manual',
            'credit_card'   => '💳 Kartu Kredit (Manual)',
            'other'         => '✦ Lainnya',
        ];

        if ($activeGateways->isNotEmpty()) {
            $methods['gateway'] = '⚡ Semua Gateway';
            foreach ($activeGateways as $gw) {
                $methods['gateway_' . $gw->id] = '⚡ ' . $gw->name;
            }
        }

        return $methods;
    }

    protected function extractTransactionId(string $gateway, Request $request): ?string
    {
        return match ($gateway) {
            'midtrans' => $request->input('order_id') ?? $request->input('transaction_id'),
            'xendit'   => $request->input('external_id'),
            'tripay'   => $request->input('reference') ?? $request->input('merchant_ref'),
            'duitku'   => $request->input('merchantOrderId'),
            'doku'     => $request->input('invoice_number'),
            'oy'       => $request->input('partner_trx_id'),
            'ipaymu'   => $request->input('trx_id'),
            'finpay'   => $request->input('invoice_no'),
            default    => $request->input('transaction_id') ?? $request->input('order_id') ?? $request->input('reference'),
        };
    }

    protected function extractWebhookStatus(string $gateway, Request $request): string
    {
        return match ($gateway) {
            'midtrans' => $request->input('transaction_status', 'pending'),
            'xendit'   => $request->input('status', 'PENDING'),
            'tripay'   => $request->input('status', 'UNPAID'),
            'duitku'   => $request->input('resultCode') === '00' ? 'success' : 'failed',
            'doku'     => strtolower($request->input('RESULTMSG', 'pending')),
            'oy'       => $request->input('status', 'PENDING'),
            'ipaymu'   => $request->input('status', 'pending'),
            'finpay'   => $request->input('response_code') === '00' ? 'success' : 'failed',
            default    => $request->input('status', $request->input('transaction_status', 'pending')),
        };
    }

    protected function extractWebhookAmount(string $gateway, Request $request, Payment $payment): float
    {
        return match ($gateway) {
            'midtrans' => (float) $request->input('gross_amount', $payment->amount),
            'xendit'   => (float) $request->input('amount', $payment->amount),
            'tripay'   => (float) $request->input('amount', $payment->amount),
            'duitku'   => (float) $request->input('amount', $payment->amount),
            default    => (float) ($request->input('amount') ?? $request->input('gross_amount') ?? $payment->amount),
        };
    }
}
