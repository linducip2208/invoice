<x-tabler.layout :title="__('Detail Pajak Indonesia')" :pretitle="__('Invoice') . ' ' . $invoice->invoice_no . ' — PPh 23 & e-Faktur'">
    <x-slot name="actions">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Kembali') }}
        </a>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('invoices.tax-details.update', $invoice) }}" class="card" x-data="{
                  ppnRate: {{ (float) $detail->ppn_rate }},
                  pph23Rate: {{ (float) $detail->pph23_rate }},
                  subtotal: {{ (float) $invoice->subtotal }},
                  get ppnAmount() { return this.subtotal * this.ppnRate / 100; },
                  get pph23Amount() { return this.subtotal * this.pph23Rate / 100; },
                  get totalReceivable() { return this.subtotal + this.ppnAmount - this.pph23Amount; },
                  fmt(n) { return new Intl.NumberFormat('en-US').format(Math.round(n)); }
              }">
                @csrf @method('PUT')

                <div class="card-body">
                    {{-- PPN Section --}}
                    <div class="mb-4">
                        <h3 class="card-title mb-3">{{ __('PPN (Pajak Pertambahan Nilai)') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Tarif PPN (%)')" name="ppn_rate" :help="__('Default 11% (sejak Apr 2022). Untuk non-PKP isi 0.')">
                                    <input type="number" step="0.01" min="0" max="100" id="ppn_rate" name="ppn_rate"
                                        x-model.number="ppnRate" value="{{ $detail->ppn_rate }}"
                                        class="form-control @error('ppn_rate') is-invalid @enderror">
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <p class="subheader">PPN Amount</p>
                                <p class="h2 text-info mt-1" x-text="fmt(ppnAmount)"></p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- PPh 23 Section --}}
                    <div class="my-4">
                        <h3 class="card-title mb-3">{{ __('PPh 23 (Pemotongan oleh Klien)') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Tarif PPh 23 (%)')" name="pph23_rate">
                                    <select id="pph23_rate" name="pph23_rate" x-model.number="pph23Rate" class="form-select">
                                        <option value="0"  {{ (float)$detail->pph23_rate === 0.0 ? 'selected' : '' }}>0% — Tidak dipotong</option>
                                        <option value="2"  {{ (float)$detail->pph23_rate === 2.0 ? 'selected' : '' }}>2% — Jasa, klien ber-NPWP</option>
                                        <option value="4"  {{ (float)$detail->pph23_rate === 4.0 ? 'selected' : '' }}>4% — Jasa, klien tidak ber-NPWP</option>
                                        <option value="15" {{ (float)$detail->pph23_rate === 15.0 ? 'selected' : '' }}>15% — Royalti / hadiah</option>
                                    </select>
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Object Code (opsional)')" name="pph23_object_code" :help="__('Kode objek pajak (contoh: 24-104-01 jasa konsultan)')">
                                    <input type="text" id="pph23_object_code" name="pph23_object_code" value="{{ $detail->pph23_object_code }}"
                                        class="form-control font-monospace @error('pph23_object_code') is-invalid @enderror" placeholder="24-104-01">
                                </x-tabler.field>
                            </div>
                        </div>
                        <div class="mt-3 p-3 rounded bg-yellow-lt border">
                            <p class="small text-yellow mb-0">
                                <strong>{{ __('Catatan:') }}</strong> Jumlah PPh 23 dipotong oleh klien dan disetor langsung ke kas negara.
                                Klien wajib mengirim bukti potong ke kamu. PPh 23 = <span x-text="fmt(pph23Amount)"></span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    {{-- e-Faktur Section --}}
                    <div class="my-4">
                        <h3 class="card-title mb-3">{{ __('e-Faktur / Coretax') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Status e-Faktur')" name="efaktur_status">
                                    <select id="efaktur_status" name="efaktur_status" class="form-select">
                                        <option value="not_applicable" {{ $detail->efaktur_status === 'not_applicable' ? 'selected' : '' }}>— Tidak Berlaku (non-PKP)</option>
                                        <option value="draft"          {{ $detail->efaktur_status === 'draft' ? 'selected' : '' }}>Draft (belum upload)</option>
                                        <option value="approved"       {{ $detail->efaktur_status === 'approved' ? 'selected' : '' }}>Approved (sudah dapat NSFP)</option>
                                        <option value="rejected"       {{ $detail->efaktur_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </x-tabler.field>
                            </div>
                            <div class="col-md-6">
                                <x-tabler.field :label="__('Nomor Faktur (NSFP)')" name="efaktur_no">
                                    <input type="text" id="efaktur_no" name="efaktur_no" value="{{ $detail->efaktur_no }}"
                                        class="form-control font-monospace @error('efaktur_no') is-invalid @enderror" placeholder="010.000-26.00000001">
                                </x-tabler.field>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Live Calculation Summary --}}
                    <div class="rounded bg-light p-3 my-4">
                        <p class="subheader mb-3">{{ __('Live Kalkulasi') }}</p>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1"><span>Subtotal (DPP)</span><span class="fw-bold" x-text="fmt(subtotal)"></span></div>
                            <div class="d-flex justify-content-between mb-1"><span>PPN <span x-text="'(' + ppnRate + '%)'"></span></span><span class="text-info fw-bold" x-text="'+' + fmt(ppnAmount)"></span></div>
                            <div class="d-flex justify-content-between border-top pt-2 mb-1"><span class="fw-semibold">Total Tagih ke Klien</span><span class="fw-bold" x-text="fmt(subtotal + ppnAmount)"></span></div>
                            <div class="d-flex justify-content-between mb-1"><span>PPh 23 <span x-text="'(' + pph23Rate + '%)'"></span></span><span class="text-warning fw-bold" x-text="'-' + fmt(pph23Amount)"></span></div>
                            <div class="d-flex justify-content-between border-top pt-2 h4 mb-0"><span>Net Diterima</span><span x-text="fmt(totalReceivable)"></span></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn">{{ __('Batal') }}</a>
                        <button type="submit" class="btn btn-primary"><x-ti name="check" /> {{ __('Simpan') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>
