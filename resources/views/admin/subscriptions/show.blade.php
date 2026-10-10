<x-admin-layout title="Subscription #{{ $subscription->id }}">
    <div class="card-unicorn">
        <h3 class="font-display font-bold text-xl">{{ $subscription->workspace?->name }} — {{ $subscription->plan?->name }}</h3>
        <p class="text-sm text-purple-500 mt-1">Status: <strong>{{ $subscription->status }}</strong> · Period: <strong>{{ $subscription->billing_period }}</strong> · Currency: <strong>{{ $subscription->currency }}</strong></p>
    </div>

    <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="mt-6 card-unicorn space-y-4">
        @csrf @method('PUT')
        <h4 class="font-display font-bold text-lg">Override Subscription</h4>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-bold text-purple-700">Plan</label>
                <select name="plan_id" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                    @foreach(\App\Models\Plan::orderBy('sort_order')->get() as $p)
                        <option value="{{ $p->id }}" {{ $subscription->plan_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-bold text-purple-700">Status</label>
                <select name="status" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                    @foreach(['trial','active','past_due','cancelled','expired'] as $s)
                        <option value="{{ $s }}" {{ $subscription->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-bold text-purple-700">Period</label>
                <select name="billing_period" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                    @foreach(['free','monthly','yearly'] as $p)
                        <option value="{{ $p }}" {{ $subscription->billing_period === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-bold text-purple-700">Currency</label>
                <select name="currency" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
                    <option value="IDR" {{ $subscription->currency === 'IDR' ? 'selected' : '' }}>IDR</option>
                    <option value="USD" {{ $subscription->currency === 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="text-sm font-bold text-purple-700">Period ends at</label>
                <input type="date" name="current_period_ends_at" value="{{ $subscription->current_period_ends_at?->format('Y-m-d') }}" class="input-unicorn rounded-xl px-4 py-2 mt-1 w-full">
            </div>
        </div>

        <div class="flex justify-end">
            <button class="btn-unicorn">Save Override</button>
        </div>
    </form>

    @if($subscription->invoices->isNotEmpty())
    <div class="mt-6 card-unicorn">
        <h4 class="font-display font-bold text-lg mb-3">Billing History</h4>
        <table class="table-unicorn">
            <thead><tr><th>Date</th><th>Reference</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($subscription->invoices as $inv)
                    <tr>
                        <td>{{ $inv->paid_at?->format('d M Y') ?? $inv->created_at->format('d M Y') }}</td>
                        <td>{{ $inv->reference_no }}</td>
                        <td>{{ $inv->currency }} {{ number_format($inv->amount, 0) }}</td>
                        <td>{{ ucfirst($inv->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</x-admin-layout>
