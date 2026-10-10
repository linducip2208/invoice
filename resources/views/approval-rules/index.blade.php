<x-tabler.layout :title="__('Approval Rules')" :pretitle="__('Approvals')" :subtitle="__('Atur invoice/quote/expense yang harus di-approve sebelum kirim')">
    <x-slot name="actions">
        <a href="{{ route('approvals.index') }}" class="btn">
            <x-ti name="inbox" /> {{ __('Inbox Approval') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">{{ __('Tambah Rule Baru') }}</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('approval-rules.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Nama Rule *')" name="name" :required="true">
                            <input type="text" id="name" name="name" required placeholder="Mis. Approval Invoice >50jt" class="form-control">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Berlaku untuk *')" name="applies_to" :required="true">
                            <select id="applies_to" name="applies_to" required class="form-select">
                                <option value="invoice">Invoice</option>
                                <option value="quote">Quote / Estimate</option>
                                <option value="expense">Expense</option>
                                <option value="credit_note">Credit Note</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Kondisi *')" name="condition_type" :required="true">
                            <select id="condition_type" name="condition_type" required class="form-select">
                                <option value="amount_gte">{{ __('Jumlah ≥ batas tertentu') }}</option>
                                <option value="always">{{ __('Selalu butuh approval') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Batas Jumlah (Rp)')" name="condition_value">
                            <input type="number" id="condition_value" name="condition_value" step="0.01" min="0" placeholder="50000000" class="form-control">
                        </x-tabler.field>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('Approver(s) *') }}</label>
                    <div class="row g-2">
                        @foreach($users as $u)
                            <div class="col-md-6">
                                <label class="form-check form-check-inline border rounded p-2 w-100">
                                    <input type="checkbox" name="approver_user_ids[]" value="{{ $u->id }}" class="form-check-input">
                                    <span class="form-check-label">{{ $u->name }}</span>
                                    <span class="text-muted small ms-auto">{{ $u->pivot?->role ?? '' }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="require_all" value="1" class="form-check-input">
                        <span class="form-check-label">{{ __('Butuh semua approver setuju (kalau tidak dicentang, cukup 1 yang approve)') }}</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary"><x-ti name="plus" /> {{ __('Buat Rule') }}</button>
            </form>
        </div>
    </div>

    @if($rules->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="shield" :title="__('Belum ada approval rule')"
                    :body="__('Tambah rule untuk control siapa boleh approve invoice/quote/expense.')" />
            </div>
        </div>
    @else
        @foreach($rules as $r)
            <div class="card mb-2">
                <div class="card-body d-flex gap-3 align-items-start">
                    <span class="avatar"><x-ti name="shield" /></span>
                    <div class="flex-fill">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fw-semibold">{{ $r->name }}</span>
                            @if($r->is_active)
                                <span class="badge bg-green-lt">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-secondary-lt">{{ __('Disabled') }}</span>
                            @endif
                        </div>
                        <div class="text-muted small mt-1">
                            {{ ucfirst($r->applies_to) }}
                            @if($r->condition_type === 'amount_gte')
                                ≥ <strong>{{ number_format($r->condition_value, 0) }}</strong>
                            @endif
                            · {{ count($r->approver_user_ids ?? []) }} {{ __('approver') }}
                            · {{ $r->require_all ? 'butuh semua' : 'cukup 1' }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('approval-rules.destroy', $r) }}" onsubmit="return confirm('Hapus rule?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Hapus') }}</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</x-tabler.layout>
