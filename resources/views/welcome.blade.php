<x-tabler.public :title="__('Invoice Profesional. Pembayaran Lebih Cepat.')" :meta-description="__('Buat invoice, kelola pelanggan, pantau pembayaran, dan otomatisasi penagihan dalam satu platform.')">
    @push('head')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @php
            $welcomeJsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => 'Invocely',
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'description' => 'Buat invoice, kelola pelanggan, pantau pembayaran, dan otomatisasi penagihan dalam satu platform.',
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'IDR'],
            ];
            $faqJsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [
                    ['@type' => 'Question', 'name' => 'Apakah Invocely gratis?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Ya. Paket Free gratis selamanya untuk mencoba: 1 invoice tanpa kedaluwarsa, maks 5 klien, dengan watermark Invocely.']],
                    ['@type' => 'Question', 'name' => 'Bagaimana cara membuat invoice?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Daftar, buat workspace, tambah klien di menu Clients, lalu Invoices → New Invoice: pilih klien, isi item, pajak, dan tanggal jatuh tempo.']],
                    ['@type' => 'Question', 'name' => 'Apakah pelanggan bisa bayar online?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Bisa. Hubungkan gateway (Midtrans, Xendit, Tripay, Duitku, DOKU, OY!, iPaymu, Finpay) dengan kredensial provider Anda, atau catat transfer manual/parsial.']],
                    ['@type' => 'Question', 'name' => 'Apakah bisa recurring?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Bisa di paket Pro/Business. Jadwal berjalan otomatis per jam, bisa pause dan resume.']],
                    ['@type' => 'Question', 'name' => 'Apakah template invoice bisa dikustom?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Ya. Tersedia Classic, Modern, Minimal, Bold, Elegant, dan Bilingual. Paket Business membuka drag-drop builder dan custom domain.']],
                    ['@type' => 'Question', 'name' => 'Apakah data saya aman?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Setiap workspace terisolasi di level database, ada audit log, role owner/admin/member, 2FA, dan API token yang bisa dicabut.']],
                    ['@type' => 'Question', 'name' => 'Bisakah ekspor laporan keuangan?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Bisa. Tersedia laporan Aging, Cashflow, P&L, Pajak, plus ekspor CSV anti formula-injection.']],
                ],
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($welcomeJsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
        <script type="application/ld+json">{!! json_encode($faqJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <meta property="og:title" content="Invoice Profesional. Pembayaran Lebih Cepat. — Invocely">
        <meta property="og:description" content="Buat invoice, kelola pelanggan, pantau pembayaran, dan otomatisasi penagihan dalam satu platform.">
        <meta property="og:type" content="website">
    @endpush

    {{-- 1. HERO --}}
    <section class="landing-hero-v2 mb-4" aria-labelledby="hero-heading">
        <div class="row g-4 align-items-center position-relative" style="z-index:1;">
            <div class="col-lg-6">
                <p class="mb-2"><span class="badge bg-white text-primary text-wrap text-start fw-semibold">Invoicing &amp; penagihan · ID + EN</span></p>
                <h1 id="hero-heading" class="display-4 fw-bold mb-3">Invoice Profesional. Pembayaran Lebih Cepat.</h1>
                <p class="fs-3 mb-2" style="color:#fff;">Buat invoice, kelola pelanggan, pantau pembayaran, dan otomatisasi penagihan dalam satu platform.</p>
                <p class="mb-1" style="color:rgba(255,255,255,.78);">Workspace terisolasi, link publik aman, saldo otomatis, recurring, dan laporan yang bisa dibawa ke akuntan.</p>
                <div class="btn-list mt-3" data-cta="hero">
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-light btn-lg" data-track-cta="hero-mulai-gratis"><x-ti name="plus" /> Mulai Gratis</a>
                    <a href="#showcase" class="btn btn-outline-light btn-lg" data-track-cta="hero-lihat-demo">Lihat Demo</a>
                </div>
                <p class="small mt-3 mb-0" style="color:rgba(255,255,255,.75);">Paket Free termasuk · Tanpa kartu kredit · Batalkan kapan saja</p>
                <div class="d-flex flex-wrap gap-4 mt-3 pt-3 border-top border-white" style="--tblr-border-opacity:.2;">
                    <div><div class="h2 mb-0 text-white">{{ number_format($stats['workspaces']) }}</div><div class="small" style="color:rgba(255,255,255,.75);">workspace terdaftar (live)</div></div>
                    <div><div class="h2 mb-0 text-white">{{ number_format($stats['users']) }}</div><div class="small" style="color:rgba(255,255,255,.75);">pengguna aktual (live)</div></div>
                    <div><div class="h2 mb-0 text-white">{{ number_format($stats['invoices']) }}</div><div class="small" style="color:rgba(255,255,255,.75);">invoice dibuat (live)</div></div>
                    <div><div class="h2 mb-0 text-white">{{ count($gateways) }}</div><div class="small" style="color:rgba(255,255,255,.75);">gateway ID didukung</div></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card landing-shot mb-0" role="img" aria-label="Pratinjau invoice aplikasi Invocely">
                    <div class="card-header">
                        <div>
                            <div class="subheader">Pratinjau aplikasi · data ilustratif</div>
                            <h2 class="card-title mb-0 fs-3"><x-ti name="file-invoice" /> INV-2026-0042 · Contoso Studio</h2>
                        </div>
                        <div class="card-actions">
                            <span class="badge bg-warning-lt">Partial</span>
                            <span class="badge bg-azure-lt">Link publik aman</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table landing-shot-table">
                            <thead><tr><th>Item</th><th class="text-end col-qty">Qty</th><th class="text-end">Jumlah</th></tr></thead>
                            <tbody>
                                <tr><td>Paket identitas brand</td><td class="text-end text-muted col-qty">1</td><td class="text-end">Rp 1.500.000</td></tr>
                                <tr><td>Pembuatan landing page</td><td class="text-end text-muted col-qty">1</td><td class="text-end">Rp 950.000</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="landing-float-card p-3 mb-2">
                                    <div class="subheader mb-1">Sisa tagihan</div>
                                    <div class="h2 mb-0">Rp 950.000</div>
                                    <div class="progress mt-2" style="height:.5rem;" role="img" aria-label="61% terkumpul"><div class="progress-bar bg-success" style="width:61%"></div></div>
                                    <div class="small text-muted mt-1">Terkumpul 61% · 2 pengingat terjadwal</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="landing-float-card p-3 mb-2">
                                    <div class="subheader mb-1">Status pembayaran</div>
                                    <div class="fw-semibold">Partial · jatuh tempo 09 Nov 2026</div>
                                    <div class="small text-muted">Total Rp 2.450.000 · otomatis terupdate</div>
                                </div>
                                <span class="btn btn-primary btn-sm w-100"><x-ti name="link" /> Tautan invoice publik</span>
                            </div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Angka contoh untuk ilustrasi. Counter kepercayaan di kiri memakai data live dari database.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. TRUST --}}
    <section class="card mb-4" aria-label="Data platform terverifikasi">
        <div class="card-body">
            <div class="row g-3 text-center align-items-center">
                <div class="col-6 col-lg-3"><div class="h1 mb-0 text-primary">{{ number_format($stats['workspaces']) }}</div><div class="text-muted small">Workspace terdaftar</div><div class="text-muted small">query live: workspaces</div></div>
                <div class="col-6 col-lg-3"><div class="h1 mb-0 text-primary">{{ number_format($stats['users']) }}</div><div class="text-muted small">Pengguna aktual</div><div class="text-muted small">query live: users</div></div>
                <div class="col-6 col-lg-3"><div class="h1 mb-0 text-primary">{{ number_format($stats['invoices']) }}</div><div class="text-muted small">Invoice dibuat</div><div class="text-muted small">query live: invoices</div></div>
                <div class="col-6 col-lg-3"><div class="h1 mb-0 text-primary">{{ count($gateways) }}</div><div class="text-muted small">Metode gateway</div><div class="text-muted small">{{ implode(' · ', array_slice($gateways, 0, 4)) }}…</div></div>
            </div>
            <p class="text-muted small text-center mt-3 mb-0">Tanpa angka palsu, tanpa logo pelanggan fiktif — semua counter dihitung dari database saat halaman dibuka. Daftar gateway lengkap: {{ implode(', ', $gateways) }} (masing-masing butuh kredensial provider).</p>
        </div>
    </section>

    {{-- 3. FEATURES --}}
    <section class="mb-5" aria-labelledby="features-heading">
        <div class="page-pretitle">Fitur</div>
        <h2 id="features-heading" class="h1 mb-2">Semua yang dibutuhkan seputar invoice</h2>
        <p class="text-muted mb-3">Satu workspace dari draf pertama sampai uang masuk. Setiap kartu tertaut ke dokumentasi yang sesuai.</p>
        <div class="row row-cards">
            @foreach([
                ['file-invoice', 'Invoice Management', 'Line item, pajak, diskon, multi-mata uang, lifecycle draft→paid.', 'docs#invoices', 'Lihat panduan invoice'],
                ['users', 'Customer Management', 'Hingga ribuan klien, impor CSV tervalidasi, opt-out pengingat.', 'docs#clients-products', 'Kelola klien'],
                ['credit-card', 'Payment Tracking', 'Parsial/penuh, manual + gateway, saldo otomatis nol saat lunas.', 'docs#payments', 'Lacak pembayaran'],
                ['repeat', 'Recurring Invoices', 'Jadwal otomatis per jam, pause/resume, idempoten per run.', 'docs#recurring-reminders', 'Otomatiskan tagihan'],
                ['file-text', 'Quotations', 'Penomoran sendiri, revisi, approval OTP/portal, konversi 1-klik.', 'docs#quotations', 'Buat quotation'],
                ['receipt', 'Expense Management', 'Catat biaya, kategorisasi, OCR struk → review → konfirmasi.', 'docs#finance-reports', 'Kelola biaya'],
                ['chart-bar', 'Financial Reports', 'Aging, cashflow, P&L, pajak — dari data Anda sendiri.', 'docs#finance-reports', 'Buka laporan'],
                ['scan', 'OCR Receipt', 'Upload struk, ekstraksi otomatis, tanpa antrean transaksi offline.', 'docs#finance-reports', 'Coba OCR'],
                ['brand-whatsapp', 'WhatsApp Integration', 'Pratinjau kirim invoice via WhatsApp per workspace.', 'docs#integrations', 'Integrasikan WA'],
                ['mail', 'Email Automation', 'Invoice PDF + pengingat terjadwal, reschedule aman.', 'docs#recurring-reminders', 'Otomatiskan email'],
                ['link', 'Public Invoice Links', 'Token publik tak tertebak, rotasi link, tampilan + PDF rapi.', 'docs#invoices', 'Bagikan link'],
                ['plug', 'Payment Gateway Integration', 'Midtrans, Xendit, Tripay, Duitku, DOKU, OY!, iPaymu, Finpay.', 'docs#integrations', 'Sambungkan gateway'],
            ] as [$icon, $title, $desc, $anchor, $cta])
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100 landing-feature-card">
                        <div class="card-body">
                            <span class="avatar avatar-md mb-2" style="background:var(--invocely-accent-soft);color:var(--tblr-primary);"><x-ti :name="$icon" /></span>
                            <h3 class="card-title mb-1">{{ $title }}</h3>
                            <p class="text-muted small mb-2">{{ $desc }}</p>
                            <a href="{{ url($anchor) }}" class="link-primary fw-semibold small">{{ $cta }} →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 4. PRODUCT SHOWCASE --}}
    <section id="showcase" class="mb-5" aria-labelledby="showcase-heading">
        <div class="page-pretitle">Product showcase</div>
        <h2 id="showcase-heading" class="h1 mb-2">Lihat struktur layar yang sebenarnya</h2>
        <p class="text-muted mb-3">Ringkasan berbasis route asli aplikasi (bukan screenshot palsu). Setiap panel tertaut ke halaman/dokumentasi yang relevan. <span class="badge bg-azure-lt text-wrap">Pratinjau ilustratif · field sesuai aplikasi</span></p>
        <div class="row row-cards">
            @foreach([
                ['Dashboard', 'Total ditagih, terkumpul, outstanding, overdue + quick actions.', 'dashboard', 'route: dashboard'],
                ['Buat invoice', 'Pilih klien → item → pajak/diskon → issue/due date → simpan draft.', 'invoices.create', 'route: invoices.create'],
                ['Detail invoice', 'Status draft/sent/partial/paid, pembayaran, lampiran, komentar, PDF.', 'invoices.index', 'route: invoices.index'],
                ['Pelanggan', 'Cari, filter status, paginasi, blokir hapus bila ada invoice.', 'clients.index', 'route: clients.index'],
                ['Laporan keuangan', 'Aging, cashflow, P&L, pajak. Cancelled dikecualikan dari revenue.', 'reports.index', 'route: reports.index'],
                ['Pelacakan pembayaran', 'Manual parsial + gateway, webhook idempoten, mismatch logged.', 'payments.index', 'route: payments.index'],
            ] as [$title, $desc, $route, $badge])
                <div class="col-md-6 col-lg-4">
                    <div class="showcase-shot h-100">
                        <div class="shot-bar" aria-hidden="true"><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="badge bg-secondary-lt ms-2">{{ $badge }}</span></div>
                        <div class="p-3">
                            <h3 class="h4 mb-1">{{ $title }}</h3>
                            <p class="text-muted small mb-2">{{ $desc }}</p>
                            @if(Route::has($route))
                                <a href="{{ route($route) }}" class="btn btn-sm btn-outline-primary">Buka {{ $title }}</a>
                            @endif
                            <a href="{{ url('docs') }}" class="btn btn-sm ms-1">Docs</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 5. INVOICE TEMPLATES --}}
    <section class="mb-5" aria-labelledby="templates-heading">
        <div class="page-pretitle">Template</div>
        <h2 id="templates-heading" class="h1 mb-2">Semua template invoice yang tersedia</h2>
        <p class="text-muted mb-3">Template di bawah sesuai file <code>resources/views/invoices/templates/*.blade.php</code> dan seeder global. Pratinjau visual berskala besar.</p>
        <div class="row row-cards">
            @foreach($templates as $tpl)
                <div class="col-sm-6 col-lg-4">
                    <div class="template-preview tpl-{{ $tpl->slug }} h-100">
                        <div class="tpl-head">
                            <div class="fw-bold">INV-2026-0042</div>
                            <div class="small" style="opacity:.85;">{{ $tpl->name }} · Contoso Studio → Acme Corp</div>
                        </div>
                        <div class="p-3">
                            <div class="d-flex justify-content-between small text-muted mb-2 flex-wrap gap-1"><span>Item (2)</span><span>Total Rp 2.450.000</span></div>
                            <div class="progress mb-2" style="height:.4rem;"><div class="progress-bar" style="width:72%;background:var(--tblr-primary);"></div></div>
                            <h3 class="h4 mb-1">{{ $tpl->name }}</h3>
                            <p class="text-muted small mb-2">{{ $tpl->description }}</p>
                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge bg-secondary-lt">slug: {{ $tpl->slug }}</span>
                                <a href="{{ route('seo.templates') }}" class="btn btn-sm btn-outline-primary">Lihat template</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 6. HOW IT WORKS --}}
    <section class="py-4 mb-5 border-top border-bottom" aria-labelledby="how-heading">
        <h2 id="how-heading" class="h2 mb-1">Dari daftar sampai dibayar dalam 3 langkah</h2>
        <p class="text-muted mb-4">Tanpa plugin, tanpa spreadsheet pendamping.</p>
        <ol class="row g-3 list-unstyled mb-0">
            <li class="col-md-4 d-flex gap-3"><span class="display-6 fw-bold text-primary">01</span><div><div class="fw-semibold">Buat akun Anda</div><div class="text-muted small">Daftar gratis, buat workspace pertama (nama + mata uang default).</div><a href="{{ Route::has('register') ? route('register') : route('login') }}" class="small fw-semibold">Daftar →</a></div></li>
            <li class="col-md-4 d-flex gap-3"><span class="display-6 fw-bold text-primary">02</span><div><div class="fw-semibold">Buat invoice pertama</div><div class="text-muted small">Tambah klien, isi line item, pajak &amp; diskon — total dihitung server-side.</div><a href="{{ url('docs#invoices') }}" class="small fw-semibold">Panduan invoice →</a></div></li>
            <li class="col-md-4 d-flex gap-3"><span class="display-6 fw-bold text-primary">03</span><div><div class="fw-semibold">Kirim dan pantau pembayaran</div><div class="text-muted small">Email PDF / link publik aman, pengingat otomatis, saldo terupdate.</div><a href="{{ url('docs#payments') }}" class="small fw-semibold">Panduan pembayaran →</a></div></li>
        </ol>
    </section>

    {{-- 7. BENEFITS (pengganti testimoni — belum ada testimoni terverifikasi) --}}
    <section class="mb-5" aria-labelledby="benefits-heading">
        <div class="page-pretitle">Kenapa Invocely</div>
        <h2 id="benefits-heading" class="h1 mb-2">Manfaat yang terasa di minggu pertama</h2>
        <p class="text-muted mb-3">Belum ada testimoni pelanggan terverifikasi — jadi kami tampilkan manfaat produk yang bisa Anda uji langsung, bukan kutipan palsu.</p>
        <div class="row row-cards">
            @foreach([
                ['bolt', 'Ditagih lebih cepat', 'Link publik + PDF + pengingat otomatis. Tidak perlu kejar-kejaran manual.'],
                ['shield-check', 'Rapih buat akuntan', 'Audit trail, aging, P&L, dan ekspor CSV yang aman dari formula-injection.'],
                ['building', 'Satu workspace per bisnis', 'Isolasi level database. Role owner/admin/member, 403/404 lintas workspace.'],
                ['currency-dollar', 'Bayar sesuai gaya Indonesia', 'Transfer manual/parsial + 8 gateway lokal. Webhook transaksional anti replay.'],
            ] as [$icon, $t, $d])
                <div class="col-sm-6 col-lg-3"><div class="card h-100"><div class="card-body"><span class="avatar avatar-md mb-2" style="background:var(--invocely-accent-soft);color:var(--tblr-primary);"><x-ti :name="$icon" /></span><h3 class="card-title">{{ $t }}</h3><p class="text-muted small mb-0">{{ $d }}</p></div></div></div>
            @endforeach
        </div>
    </section>

    {{-- 8. PRICING --}}
    <section class="mb-5" aria-labelledby="pricing-heading">
        <div class="row g-2 align-items-center mb-3">
            <div class="col-lg-7">
                <div class="page-pretitle">Pricing</div>
                <h2 id="pricing-heading" class="h1 mb-1">Mulai gratis, upgrade saat sudah menghasilkan</h2>
                <p class="text-muted mb-0">Harga nyata dari database (tabel plans). Perbandingan Free / Pro / Business.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <img src="{{ asset('images/pricing-premium.png') }}" alt="{{ __('Ilustrasi paket harga Invocely') }}" class="img-fluid rounded-3 shadow-sm" loading="lazy" width="832" height="480">
            </div>
            <div class="col-12 text-lg-end"><a href="{{ route('pricing') }}" class="btn btn-outline-primary mt-2">Bandingkan semua fitur</a></div>
        </div>
        <div class="row row-cards">
            @foreach($plans as $plan)
                <div class="col-md-4">
                    <div class="card h-100 {{ $plan->slug === 'pro' ? 'card-active' : '' }}">
                        <div class="card-body text-center d-flex flex-column">
                            @if($plan->slug === 'pro')<span class="badge bg-primary-lt mb-2 align-self-center">Paling populer</span>@endif
                            <h3 class="card-title justify-content-center">{{ $plan->name }}</h3>
                            <p class="text-muted small">{{ $plan->tagline }}</p>
                            <p class="display-6 mb-1">
                                @if((float) $plan->price_idr_monthly > 0)
                                    Rp{{ number_format((float) $plan->price_idr_monthly, 0, ',', '.') }}<span class="text-muted h4">/bln</span>
                                @else
                                    Gratis
                                @endif
                            </p>
                            <p class="text-muted small">
                                @if((float) $plan->price_idr_yearly > 0)
                                    Rp{{ number_format((float) $plan->price_idr_yearly, 0, ',', '.') }}/thn · hemat {{ $plan->yearlySavingsPercent('IDR') }}%
                                @else
                                    Gratis selamanya · 1 invoice
                                @endif
                            </p>
                            <ul class="list-unstyled text-start small mb-3">
                                @if($plan->slug === 'free')
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> <strong>1 invoice selamanya</strong>, maks 5 klien</li>
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> 5 template · 1 user</li>
                                    <li class="mb-1 text-muted"><x-ti name="info-circle" /> Watermark “Powered by Invocely”</li>
                                @elseif($plan->slug === 'pro')
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> <strong>Invoice unlimited</strong> + recurring</li>
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> 3 user, klien unlimited</li>
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> Branding sendiri, audit log, 2FA, CSV</li>
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> Pengingat otomatis + time/expense tracking</li>
                                @else
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> <strong>Semua di Pro</strong> + drag-drop builder</li>
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> API REST, custom domain, webhook</li>
                                    <li class="mb-1"><x-ti name="check" class="text-success" /> User tim unlimited, prioritas support</li>
                                @endif
                            </ul>
                            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn {{ $plan->slug === 'pro' ? 'btn-primary' : 'btn-outline-primary' }} w-100 mt-auto" data-track-cta="pricing-{{ $plan->slug }}">Pilih {{ $plan->name }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 9. FAQ --}}
    <section class="row mb-5" aria-labelledby="faq-heading">
        <div class="col-lg-4">
            <h2 id="faq-heading" class="h2 mb-2">Pertanyaan yang sering masuk</h2>
            <p class="text-muted">Jawaban jujur — tanpa kejutan biaya tersembunyi.</p>
            <a href="{{ route('docs') }}" class="btn btn-outline-primary btn-sm">Buka dokumentasi</a>
        </div>
        <div class="col-lg-8">
            <div class="accordion" id="landing-faq">
                @foreach([
                    ['Apakah Invocely gratis?', 'Ya. Paket Free gratis selamanya: 1 invoice tanpa kedaluwarsa, maks 5 klien, dengan watermark. Upgrade ke Pro/Business saat butuh unlimited + automation.'],
                    ['Bagaimana cara membuat invoice?', 'Daftar → buat workspace → tambah klien (Clients) → Invoices → New Invoice: pilih klien, tambah line item, atur pajak/diskon dan jatuh tempo, lalu kirim.'],
                    ['Apakah pelanggan bisa bayar online?', 'Bisa. Sambungkan gateway (Midtrans, Xendit, Tripay, Duitku, DOKU, OY!, iPaymu, Finpay) dengan kredensial provider Anda, atau catat transfer manual/parsial.'],
                    ['Apakah bisa recurring?', 'Bisa di Pro/Business. Jadwal dibuat otomatis per jam (idempoten), bisa pause/resume, cocok untuk retainer & langganan.'],
                    ['Apakah template invoice bisa dikustom?', 'Ya. 6 template bawaan (Classic, Modern, Minimal, Bold, Elegant, Bilingual). Business membuka drag-drop builder, branding penuh, dan custom domain.'],
                    ['Apakah data saya aman?', 'Ya. Isolasi per workspace di level database, role owner/admin/member, audit log read-only, 2FA, API token revocable, dan link publik bertoken tak tertebak.'],
                    ['Bisakah ekspor laporan keuangan?', 'Bisa. Laporan Aging, Cashflow, P&L, dan Pajak dari data Anda, plus ekspor CSV yang lolos dari formula-injection. Cancelled selalu dikecualikan dari revenue.'],
                ] as $i => [$q, $a])
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-{{ $i }}" aria-expanded="false" aria-controls="faq-{{ $i }}">{{ $q }}</button>
                        </h3>
                        <div id="faq-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#landing-faq">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 10. FINAL CTA --}}
    <section class="landing-cta-band mb-4 text-center" aria-labelledby="cta-heading">
        <h2 id="cta-heading" class="mb-2">Kelola Invoice Bisnis Lebih Mudah Mulai Hari Ini.</h2>
        <p style="color:rgba(255,255,255,.8);">Buat invoice pertama dalam hitungan menit. Gratis untuk memulai.</p>
        <div class="btn-list justify-content-center" data-cta="final">
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-light btn-lg" data-track-cta="final-mulai-gratis"><x-ti name="plus" /> Mulai Gratis</a>
            <a href="{{ route('pricing') }}" class="btn btn-outline-light btn-lg">Lihat Harga</a>
        </div>
    </section>

    {{-- 11. FOOTER --}}
    <footer class="py-4 border-top" aria-label="Footer">
        <div class="row g-3">
            <div class="col-sm-6 col-lg-3">
                <h3 class="subheader">Produk</h3>
                <ul class="list-unstyled mb-0">
                    <li><a href="#showcase">Product showcase</a></li>
                    <li><a href="{{ route('pricing') }}">Pricing</a></li>
                    <li><a href="{{ route('seo.templates') }}">Invoice templates</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-3">
                <h3 class="subheader">Fitur</h3>
                <ul class="list-unstyled mb-0">
                    <li><a href="{{ url('docs#invoices') }}">Invoice &amp; link publik</a></li>
                    <li><a href="{{ url('docs#recurring-reminders') }}">Recurring &amp; pengingat</a></li>
                    <li><a href="{{ url('docs#finance-reports') }}">Laporan &amp; OCR</a></li>
                    <li><a href="{{ url('docs#integrations') }}">WhatsApp &amp; gateway</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-3">
                <h3 class="subheader">Perusahaan &amp; Bantuan</h3>
                <ul class="list-unstyled mb-0">
                    <li><a href="{{ route('docs') }}">Documentation</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('support.index') }}">Support</a></li>
                    <li><a href="{{ route('pricing') }}">Pricing</a></li>
                </ul>
            </div>
            <div class="col-sm-6 col-lg-3">
                <h3 class="subheader">Legal</h3>
                <ul class="list-unstyled mb-0">
                    <li><a href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                </ul>
                <p class="text-muted small mt-2 mb-0">Invoicing untuk freelancer, agensi, dan UKM.</p>
                <p class="text-muted small mt-1 mb-0">&copy; {{ now()->year }} {{ branding('app_name') }}</p>
            </div>
        </div>
    </footer>
</x-tabler.public>
