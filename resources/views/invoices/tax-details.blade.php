<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="heading-unicorn flex items-center gap-2">{{ __('Detail Pajak Indonesia') }} <span class="text-2xl">🏛️</span></h2>
                <p class="text-sm text-purple-500 mt-1">✦ Invoice <strong>{{ $invoice->invoice_no }}</strong> — PPh 23 & e-Faktur</p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary text-sm">← {{ __('Kembali') }}</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if(session('success'))
            <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200 p-4 text-emerald-800">✓ {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('invoices.tax-details.update', $invoice) }}" class="card-unicorn !p-8 space-y-6"
              x-data="{
                  ppnRate: {{ (float) $detail->ppn_rate }},
                  pph23Rate: {{ (float) $detail->pph23_rate }},
                  subtotal: {{ (float) $invoice->subtotal }},
                  get ppnAmount() { return this.subtotal * this.ppnRate / 100; },
                  get pph23Amount() { return this.subtotal * this.pph23Rate / 100; },
                  get totalReceivable() { return this.subtotal + this.ppnAmount - this.pph23Amount; },
                  fmt(n) { return new Intl.NumberFormat('en-US').format(Math.round(n)); }
              }">
            @csrf @method('PUT')

            {{-- PPN Section --}}
            <div>
                <h3 class="font-display font-bold text-lg text-purple-900 mb-3 flex items-center gap-2">
                    <span class="text-sky-500">✦</span> {{ __('PPN (Pajak Pertambahan Nilai)') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Tarif PPN (%)')" />
                        <x-text-input name="ppn_rate" type="number" step="0.01" min="0" max="100"
                                      x-model.number="ppnRate" :value="$detail->ppn_rate" class="mt-1 w-full" />
                        <p class="text-xs text-purple-400 mt-1">Default 11% (sejak Apr 2022). Untuk non-PKP isi 0.</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wider text-purple-400">PPN Amount</p>
                        <p class="font-display font-bold text-2xl text-sky-700 mt-1" x-text="fmt(ppnAmount)"></p>
                    </div>
                </div>
            </div>

            <hr class="border-purple-100">

            {{-- PPh 23 Section --}}
            <div>
                <h3 class="font-display font-bold text-lg text-purple-900 mb-3 flex items-center gap-2">
                    <span class="text-amber-500">✦</span> {{ __('PPh 23 (Pemotongan oleh Klien)') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Tarif PPh 23 (%)')" />
                        <select name="pph23_rate" x-model.number="pph23Rate" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="0"  {{ (float)$detail->pph23_rate === 0.0 ? 'selected' : '' }}>0% — Tidak dipotong</option>
                            <option value="2"  {{ (float)$detail->pph23_rate === 2.0 ? 'selected' : '' }}>2% — Jasa, klien ber-NPWP</option>
                            <option value="4"  {{ (float)$detail->pph23_rate === 4.0 ? 'selected' : '' }}>4% — Jasa, klien tidak ber-NPWP</option>
                            <option value="15" {{ (float)$detail->pph23_rate === 15.0 ? 'selected' : '' }}>15% — Royalti / hadiah</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Object Code (opsional)')" />
                        <x-text-input name="pph23_object_code" :value="$detail->pph23_object_code" class="mt-1 w-full font-mono" placeholder="24-104-01" />
                        <p class="text-xs text-purple-400 mt-1">Kode objek pajak (contoh: <code>24-104-01</code> jasa konsultan)</p>
                    </div>
                </div>
                <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200">
                    <p class="text-xs text-amber-800">
                        💡 <strong>Catatan:</strong> Jumlah PPh 23 dipotong oleh klien dan disetor langsung ke kas negara.
                        Klien wajib mengirim bukti potong ke kamu. PPh 23 = <span x-text="fmt(pph23Amount)"></span>
                    </p>
                </div>
            </div>

            <hr class="border-purple-100">

            {{-- e-Faktur Section --}}
            <div>
                <h3 class="font-display font-bold text-lg text-purple-900 mb-3 flex items-center gap-2">
                    <span class="text-pink-500">✦</span> {{ __('e-Faktur / Coretax') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Status e-Faktur')" />
                        <select name="efaktur_status" class="input-unicorn rounded-xl px-4 py-2 mt-1 block w-full">
                            <option value="not_applicable" {{ $detail->efaktur_status === 'not_applicable' ? 'selected' : '' }}>— Tidak Berlaku (non-PKP)</option>
                            <option value="draft"          {{ $detail->efaktur_status === 'draft' ? 'selected' : '' }}>📝 Draft (belum upload)</option>
                            <option value="approved"       {{ $detail->efaktur_status === 'approved' ? 'selected' : '' }}>✓ Approved (sudah dapat NSFP)</option>
                            <option value="rejected"       {{ $detail->efaktur_status === 'rejected' ? 'selected' : '' }}>✗ Rejected</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Nomor Faktur (NSFP)')" />
                        <x-text-input name="efaktur_no" :value="$detail->efaktur_no" class="mt-1 w-full font-mono" placeholder="010.000-26.00000001" />
                    </div>
                </div>
            </div>

            <hr class="border-purple-100">

            {{-- Live Calculation Summary --}}
            <div class="rounded-2xl p-5" style="background: linear-gradient(135deg, rgba(255,228,241,0.5), rgba(237,233,254,0.5));">
                <p class="text-xs uppercase tracking-wider text-purple-400 mb-3">{{ __('Live Kalkulasi') }}</p>
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between"><span class="text-purple-700">Subtotal (DPP)</span><span class="font-bold text-purple-900" x-text="fmt(subtotal)"></span></div>
                    <div class="flex justify-between"><span class="text-purple-700">PPN <span x-text="'(' + ppnRate + '%)'"></span></span><span class="text-sky-700 font-bold" x-text="'+' + fmt(ppnAmount)"></span></div>
                    <div class="flex justify-between border-t border-purple-200 pt-1.5"><span class="text-purple-900 font-semibold">Total Tagih ke Klien</span><span class="font-display font-bold text-purple-900" x-text="fmt(subtotal + ppnAmount)"></span></div>
                    <div class="flex justify-between"><span class="text-purple-700">PPh 23 <span x-text="'(' + pph23Rate + '%)'"></span></span><span class="text-amber-700 font-bold" x-text="'-' + fmt(pph23Amount)"></span></div>
                    <div class="flex justify-between border-t-2 border-purple-300 pt-2 text-base"><span class="font-display font-bold text-purple-900">Net Diterima</span><span class="font-display font-bold text-rainbow" x-text="fmt(totalReceivable)"></span></div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}" class="btn-unicorn-secondary">{{ __('Batal') }}</a>
                <button type="submit" class="btn-unicorn px-8">✦ {{ __('Simpan') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
