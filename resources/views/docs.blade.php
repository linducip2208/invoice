<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dokumentasi Invocely — Pelajari cara membuat invoice, kelola klien, lacak pembayaran, dan lainnya.">
    <meta property="og:title" content="Dokumentasi Invocely — Panduan Pengguna & Tutorial">
    <meta property="og:description" content="Panduan langkah demi langkah untuk semua fitur Invocely.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/docs') }}">
    <link rel="canonical" href="{{ url('/docs') }}">

    <title>Dokumentasi ✦ Invocely — Panduan Invoicing Ajaib</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=quicksand:400,500,600,700|comfortaa:400,600,700|figtree:400,500,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .docs-sidebar-link {
            transition: all 0.2s;
        }
        .docs-sidebar-link.active {
            background: linear-gradient(90deg, rgba(255, 122, 198, 0.15), rgba(167, 139, 250, 0.12));
            border-left: 3px solid #A78BFA;
            color: #6D28D9;
        }
        .prose h2 {
            font-family: 'Comfortaa', display;
            font-weight: 700;
            font-size: 1.75rem;
            color: #4C1D95;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(196, 181, 253, 0.3);
        }
        .prose h3 {
            font-family: 'Comfortaa', display;
            font-weight: 600;
            font-size: 1.25rem;
            color: #6D28D9;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
        }
        .prose p, .prose li { color: #4A3550; line-height: 1.8; }
        .prose ul { list-style: none; padding-left: 0; }
        .prose ul li::before { content: '✦'; margin-right: 8px; color: #A78BFA; }
        .prose ol { padding-left: 1.5rem; }
        .prose ol li { margin-bottom: 0.5rem; }
        .prose code {
            background: rgba(237, 233, 254, 0.5);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
            color: #7C3AED;
            border: 1px solid rgba(196, 181, 253, 0.4);
        }
        .prose pre {
            background: linear-gradient(135deg, rgba(237,233,254,0.6), rgba(224,242,254,0.4));
            padding: 1rem 1.25rem;
            border-radius: 12px;
            overflow-x: auto;
            border: 1px solid rgba(196, 181, 253, 0.3);
            font-size: 0.85rem;
        }
        html { scroll-behavior: smooth; scroll-padding-top: 5rem; }
    </style>
</head>
<body class="font-sans antialiased text-purple-900 min-h-screen">

    <!-- Nav atas -->
    <nav class="fixed top-0 left-0 right-0 z-50" style="background: rgba(255, 248, 254, 0.9); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(196,181,253,0.35);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <span class="logo-glow flex items-center justify-center" style="padding: 4px;">
                    <x-application-logo size="sm" />
                </span>
                <span class="font-display font-bold text-lg text-rainbow">Invocely</span>
            </a>
            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('seo.templates') }}" class="hidden sm:inline text-sm font-semibold text-purple-600 hover:text-pink-500">Template</a>
                <a href="{{ route('docs') }}" class="text-sm font-bold text-pink-500 underline underline-offset-4">Dokumentasi</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-unicorn">✦ Buka Dasbor →</a>
                @else
                    <a href="{{ route('login') }}" class="btn-unicorn-secondary">🔑 Masuk</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-unicorn">✦ Daftar Gratis</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <div class="pt-16 flex">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 fixed inset-y-16 left-0 overflow-y-auto py-8 px-4"
               style="background: rgba(255,255,255,0.65); backdrop-filter: blur(12px); border-right: 1px solid rgba(196,181,253,0.25);">
            <p class="text-[10px] font-bold text-purple-400 uppercase tracking-[0.2em] mb-4 px-3">✦ Dokumentasi</p>
            <nav class="space-y-0.5">
                @php $links = [
                    'memulai'       => '🚀 Memulai',
                    'alur-bisnis'   => '🗺️ Alur Bisnis',
                    'tutorial'      => '🎓 Tutorial Praktis',
                    'akun'          => '🔐 Pendaftaran & Masuk',
                    'dasbor'        => '📊 Dasbor',
                    'klien'         => '👥 Klien',
                    'produk'        => '📦 Produk',
                    'invoice'       => '📄 Invoice',
                    'template'      => '📜 Template Invoice',
                    'email-invoice' => '📨 Kirim Invoice via Email',
                    'bagikan'       => '🔗 Bagikan Invoice',
                    'pdf'           => '📥 Unduh PDF',
                    'pembayaran'    => '💰 Pembayaran',
                    'berulang'      => '🔄 Invoice Berulang',
                    'laporan'       => '📈 Laporan',
                    'workspace'     => '⚙️ Workspace',
                    'seo'           => '🌐 Halaman SEO',
                    'beli'          => '🛒 Beli Source Code',
                ];
                @endphp
                @foreach($links as $id => $label)
                    <a href="#{{ $id }}" class="docs-sidebar-link block px-3 py-2 text-sm font-medium text-purple-700 rounded-r-xl hover:bg-pink-50/60 border-l-3 border-transparent">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- Konten utama -->
        <main class="flex-1 lg:ml-64 min-w-0">
            <div class="max-w-4xl mx-auto px-4 sm:px-8 py-10 prose">

                <!-- Hero -->
                <div class="text-center mb-10">
                    <span class="badge-unicorn inline-flex animate-unicorn-bounce mb-4">📖 Dokumentasi</span>
                    <h1 class="font-display text-4xl sm:text-5xl font-bold text-purple-900 mt-4">
                        Panduan Pengguna Invocely
                    </h1>
                    <p class="text-lg text-purple-500 max-w-2xl mx-auto mt-4">
                        Semua yang perlu kamu ketahui tentang invoicing ajaib — dari login pertama sampai invoice berulang otomatis.
                    </p>
                </div>

                <!-- ─── AKSES MASUK / QUICK ACCESS ──────────── -->
                <div class="card-unicorn-glow !p-8 mb-12 sparkles-bg" style="background: linear-gradient(135deg, rgba(255,228,241,0.65), rgba(237,233,254,0.65), rgba(224,242,254,0.55));">
                    <div class="text-center mb-6">
                        <span class="text-5xl inline-block animate-unicorn-float">🦄</span>
                        @auth
                            <h2 class="!mt-3 !mb-2 !border-0 !pb-0">Selamat datang kembali, {{ Auth::user()->name }}!</h2>
                            <p class="text-purple-600">Lanjutkan keajaibanmu di dasbor.</p>
                        @else
                            <h2 class="!mt-3 !mb-2 !border-0 !pb-0">Siap mulai keajaibanmu?</h2>
                            <p class="text-purple-600">Masuk ke akun atau daftar gratis untuk akses penuh ke semua fitur Invocely.</p>
                        @endauth
                    </div>

                    @auth
                        <div class="max-w-md mx-auto">
                            <a href="{{ route('dashboard') }}" class="card-unicorn group flex items-center gap-4 hover:shadow-unicorn-glow no-underline">
                                <span class="text-4xl w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 group-hover:animate-unicorn-bounce" style="background: linear-gradient(135deg, #FFE4F1, #EDE9FE, #BAE6FD);">📊</span>
                                <div class="flex-1">
                                    <p class="font-display font-bold text-purple-900 text-lg !m-0">Buka Dasbor</p>
                                    <p class="text-sm text-purple-500 !m-0">Lanjut ke ruang kerja ajaibmu →</p>
                                </div>
                            </a>
                        </div>
                    @else
                        <div class="grid sm:grid-cols-2 gap-4">
                            <a href="{{ route('login') }}" class="card-unicorn group flex items-center gap-4 hover:shadow-unicorn-glow no-underline">
                                <span class="text-4xl w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 group-hover:animate-unicorn-bounce" style="background: linear-gradient(135deg, #EDE9FE, #BAE6FD, #DCFCE7);">🔑</span>
                                <div class="flex-1">
                                    <p class="font-display font-bold text-purple-900 text-lg !m-0">Sudah punya akun?</p>
                                    <p class="text-sm text-purple-500 !m-0">Masuk ke akunmu →</p>
                                </div>
                            </a>

                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="card-unicorn group flex items-center gap-4 hover:shadow-unicorn-glow no-underline" style="background: linear-gradient(135deg, rgba(255,228,241,0.85), rgba(237,233,254,0.85)); border-color: rgba(255,122,198,0.4);">
                                    <span class="text-4xl w-16 h-16 rounded-2xl flex items-center justify-center shrink-0 group-hover:animate-unicorn-bounce" style="background: linear-gradient(135deg, #FFB1D8, #C4B5FD, #BAE6FD);">✨</span>
                                    <div class="flex-1">
                                        <p class="font-display font-bold text-purple-900 text-lg !m-0">Belum punya akun?</p>
                                        <p class="text-sm text-purple-500 !m-0">Daftar gratis sekarang →</p>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endauth
                </div>

                <!-- ─── DEMO CREDENTIALS — auto-injected ──────────── -->
                <div class="glass-card p-6 mb-12" id="demo-credentials">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-3xl">🔐</span>
                        <div>
                            <h2 class="!m-0 !border-0 !pb-0 font-display text-xl font-bold text-purple-900">Demo Login Credentials</h2>
                            <p class="!m-0 text-sm text-purple-500">Akses cepat untuk testing — gunakan akun di bawah ini.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 not-prose">
                        <!-- Superadmin -->
                        <div class="rounded-2xl p-5 border-2 border-pink-300" style="background: linear-gradient(135deg, rgba(255,228,241,0.7), rgba(237,233,254,0.5));">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="pill pill-pink">👑 Superadmin</span>
                                <span class="text-xs text-purple-500">→ /admin</span>
                            </div>
                            <h3 class="font-display font-bold text-lg text-purple-900 !m-0 !border-0">Owner Aplikasi</h3>
                            <p class="text-xs text-purple-500 !mt-1">Akses semua plan, user, workspace, billing, branding.</p>
                            <div class="mt-3 space-y-1.5 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-purple-400 w-16">Email:</span>
                                    <code class="font-mono text-purple-900 bg-white/70 px-2 py-1 rounded flex-1 select-all">superadmin@invocely.test</code>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-purple-400 w-16">Password:</span>
                                    <code class="font-mono text-purple-900 bg-white/70 px-2 py-1 rounded flex-1 select-all">superadmin123</code>
                                </div>
                            </div>
                        </div>

                        <!-- Demo Workspace User -->
                        <div class="rounded-2xl p-5 border-2 border-purple-300" style="background: linear-gradient(135deg, rgba(237,233,254,0.7), rgba(186,230,253,0.5));">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="pill pill-purple">🦄 Workspace Admin</span>
                                <span class="text-xs text-purple-500">→ /dashboard</span>
                            </div>
                            <h3 class="font-display font-bold text-lg text-purple-900 !m-0 !border-0">Demo Company</h3>
                            <p class="text-xs text-purple-500 !mt-1">Sudah ada 10 klien, 15 produk, 20 invoice, 2 recurring.</p>
                            <div class="mt-3 space-y-1.5 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-purple-400 w-16">Email:</span>
                                    <code class="font-mono text-purple-900 bg-white/70 px-2 py-1 rounded flex-1 select-all">demo@invocely.test</code>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-purple-400 w-16">Password:</span>
                                    <code class="font-mono text-purple-900 bg-white/70 px-2 py-1 rounded flex-1 select-all">password</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                        <span>⚠️</span>
                        <span><strong>Hanya untuk demo / development.</strong> Ganti password sebelum production. Reset semua data: <code class="bg-white px-1.5 py-0.5 rounded">php artisan migrate:fresh --seed</code></span>
                    </div>

                    <div class="mt-3 text-xs text-purple-500">
                        💡 Tip: klik field di atas untuk select-all, copy paste ke form login.
                    </div>
                </div>

                @guest
                    <div class="text-center mb-8">
                        <p class="text-sm text-purple-500 flex items-center justify-center gap-3 flex-wrap">
                            <span>🔐 Aman & terenkripsi</span>
                            <span class="text-purple-300">·</span>
                            <span>💳 Tidak perlu kartu kredit</span>
                            <span class="text-purple-300">·</span>
                            <span>⚡ Setup &lt; 1 menit</span>
                        </p>
                    </div>
                @endguest

                <!-- ─── MEMULAI ─────────────────────────── -->
                <section id="memulai" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🚀 Memulai</h2>
                    <p>Invocely adalah platform invoicing ajaib yang dibuat untuk freelancer, agensi, dan bisnis kecil. Dengan Invocely kamu bisa membuat invoice cantik, melacak pembayaran, mengirim email, dan mengotomatiskan tagihan berulang — semua dari satu dasbor.</p>
                    <p>Panduan ini akan memandumu langkah demi langkah. Klik bagian mana pun di sidebar kiri untuk langsung melompat.</p>
                    <p class="font-semibold">Tech stack:</p>
                    <ul>
                        <li>Laravel 13 + PHP 8.3</li>
                        <li>Tailwind CSS 3 dengan tema Unicorn custom</li>
                        <li>Alpine.js untuk UI reaktif</li>
                        <li>MySQL / PostgreSQL database</li>
                        <li>Barryvdh DomPDF untuk generate PDF</li>
                    </ul>
                </section>

                <!-- ─── ALUR BISNIS ───────────────────── -->
                <section id="alur-bisnis" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🗺️ Alur Bisnis End-to-End</h2>
                    <p>Dari nol sampai dapat bayaran — inilah alur normal yang kamu jalanin tiap kali punya klien baru. Ikutin urutan ini supaya ga ada langkah yang ke-skip.</p>

                    <div class="not-prose mt-6 space-y-3">
                        @php $flow = [
                            ['1', '⚙️', 'Setup Workspace', 'Isi nama bisnis, alamat, NPWP, mata uang default, awalan invoice (mis. INV-), upload logo, set warna brand. Sekali setup, dipakai di semua invoice.', '#workspace'],
                            ['2', '👥', 'Tambah Klien', 'Catat data klien — nama, email, alamat, NPWP. Email penting karena dipakai untuk kirim invoice otomatis.', '#klien'],
                            ['3', '📦', 'Tambah Produk (opsional)', 'Bikin daftar item yang sering ditagih — jasa per jam, retainer, biaya lisensi. Saat bikin invoice tinggal pilih dari dropdown, harga & deskripsi auto-fill.', '#produk'],
                            ['4', '📄', 'Buat Invoice', 'Pilih klien → tambah item (manual atau dari produk) → set pajak/diskon → pilih template & warna. Simpan sebagai Draft dulu kalau belum yakin.', '#invoice'],
                            ['5', '📨', 'Kirim ke Klien', 'Dua opsi: kirim via email (otomatis attach PDF, status berubah jadi Terkirim) atau bagikan link publik /i/{token} via WhatsApp/Slack.', '#email-invoice'],
                            ['6', '💰', 'Catat Pembayaran', 'Setelah klien bayar, buka invoice → Catat Pembayaran → input jumlah, metode, tanggal, referensi transfer. Status auto-update jadi Sebagian/Lunas.', '#pembayaran'],
                            ['7', '🔄', 'Otomatisasi (Klien Tetap)', 'Untuk klien retainer/langganan, bikin Invoice Berulang dengan frekuensi bulanan/tahunan. Sistem auto-generate invoice + auto-kirim email tiap periode.', '#berulang'],
                            ['8', '📈', 'Review Laporan', 'Cek dasbor mingguan/bulanan — pendapatan, klien teratas, invoice jatuh tempo. Tindak lanjut yang belum bayar.', '#laporan'],
                        ]; @endphp

                        @foreach($flow as [$num, $icon, $title, $desc, $href])
                            <a href="{{ $href }}" class="flex items-start gap-4 p-4 rounded-2xl border-2 border-purple-100 hover:border-pink-300 hover:shadow-unicorn-pink transition no-underline group">
                                <div class="shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-display font-black text-white shadow-unicorn-pink"
                                     style="background: linear-gradient(135deg, #FF7AC6, #A78BFA);">
                                    {{ $num }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-bold text-purple-900 !m-0 flex items-center gap-2">
                                        <span class="text-xl">{{ $icon }}</span> {{ $title }}
                                    </p>
                                    <p class="text-sm text-purple-600 !mt-1 !mb-0">{{ $desc }}</p>
                                </div>
                                <span class="shrink-0 text-pink-400 group-hover:text-pink-600 self-center text-xl">→</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 p-4 rounded-xl bg-gradient-to-r from-amber-50 to-pink-50 border border-amber-200 not-prose">
                        <p class="font-bold text-amber-900 !m-0 flex items-center gap-2">💡 Tips Workflow</p>
                        <ul class="!mt-2 !mb-0 text-sm text-amber-900 space-y-1">
                            <li>Selalu <strong>simpan sebagai Draft dulu</strong> sebelum kirim — gampang revisi.</li>
                            <li>Set <strong>tanggal jatuh tempo realistis</strong> (default 30 hari) — terlalu cepat bikin klien stress, terlalu lama bikin cash flow tersendat.</li>
                            <li>Manfaatin <strong>Invoice Berulang</strong> untuk klien tetap — hemat 15 menit per bulan, ga lupa nagih.</li>
                            <li>Cek <strong>laporan jatuh tempo</strong> tiap Senin pagi — tindak lanjut yang lewat 7+ hari.</li>
                        </ul>
                    </div>
                </section>

                <!-- ─── TUTORIAL PRAKTIS ─────────────── -->
                <section id="tutorial" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🎓 Tutorial Praktis Step-by-Step</h2>
                    <p>Skenario nyata dari user Invocely. Pilih yang paling mirip kebutuhanmu — copy alurnya.</p>

                    <!-- Tutorial 1 -->
                    <div class="mt-8">
                        <h3 class="flex items-center gap-2"><span class="badge-unicorn">Tutorial 1</span> Setup Pertama Kali (≈10 menit)</h3>
                        <p class="text-sm text-purple-500 !mt-1"><strong>Skenario:</strong> Baru daftar Invocely, mau langsung bisa kirim invoice ke klien pertama.</p>
                        <ol>
                            <li>Login → otomatis ke <code>/dashboard</code>.</li>
                            <li>Klik avatar pojok kanan atas → <strong>Workspace Settings</strong>.</li>
                            <li>Isi <strong>Nama Bisnis</strong>, <strong>Alamat</strong>, <strong>Telepon</strong>, <strong>Email</strong>, <strong>NPWP</strong> (kalau PKP).</li>
                            <li>Set <strong>Mata Uang</strong> = <code>IDR</code>, <strong>Awalan Invoice</strong> = <code>INV-</code>, <strong>Nomor Berikutnya</strong> = <code>1</code>.</li>
                            <li>Upload <strong>Logo</strong> (PNG/JPG, max 2 MB, transparan disarankan).</li>
                            <li>Pilih <strong>Warna Brand</strong> — warna ini muncul di header semua invoice & PDF.</li>
                            <li>Klik <strong>Simpan</strong>.</li>
                            <li>Pergi ke <code>/clients</code> → <strong>+ Klien Baru</strong> → isi data klien pertama → simpan.</li>
                            <li>Pergi ke <code>/invoices/create</code> → pilih klien → tambah 1-2 item → simpan & kirim.</li>
                        </ol>
                        <p class="text-sm text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">✅ <strong>Hasil:</strong> Invoice pertama terkirim, status <code>Terkirim</code>, klien dapat email + PDF.</p>
                    </div>

                    <!-- Tutorial 2 -->
                    <div class="mt-10">
                        <h3 class="flex items-center gap-2"><span class="badge-unicorn">Tutorial 2</span> Tagih Freelance per Jam</h3>
                        <p class="text-sm text-purple-500 !mt-1"><strong>Skenario:</strong> Kerja freelance design, charge Rp 250.000/jam, total 18 jam bulan ini.</p>
                        <ol>
                            <li>Sekali setup: <code>/products</code> → <strong>+ Produk Baru</strong> → Nama: <em>Design Per Jam</em>, Harga Satuan: <code>250000</code>, Deskripsi: <em>Jasa desain UI/UX</em> → simpan.</li>
                            <li>Buat invoice: <code>/invoices/create</code> → pilih klien.</li>
                            <li>Di tabel Items, dropdown <strong>Pilih Produk</strong> → pilih <em>Design Per Jam</em>. Deskripsi & harga auto-fill.</li>
                            <li>Ubah <strong>Qty</strong> menjadi <code>18</code> → total auto-hitung jadi Rp 4.500.000.</li>
                            <li>Tambah breakdown di <strong>Deskripsi</strong>: <em>"Logo redesign (8 jam) + landing page (6 jam) + revisi (4 jam)"</em>.</li>
                            <li>Set <strong>Pajak</strong> = <code>11%</code> (PPN, kalau PKP) atau <code>0</code>.</li>
                            <li>Tambah <strong>Catatan</strong>: <em>"Pembayaran via transfer BCA 1234567890 a.n. [Nama]"</em>.</li>
                            <li>Pilih template <strong>Modern</strong> atau <strong>Bold</strong> → simpan & kirim.</li>
                        </ol>
                        <p class="text-sm text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">✅ <strong>Hasil:</strong> Invoice Rp 4.995.000 (PPN 11% included), klien jelas berapa jam yang ditagih.</p>
                    </div>

                    <!-- Tutorial 3 -->
                    <div class="mt-10">
                        <h3 class="flex items-center gap-2"><span class="badge-unicorn">Tutorial 3</span> Retainer Bulanan Otomatis</h3>
                        <p class="text-sm text-purple-500 !mt-1"><strong>Skenario:</strong> Klien langganan SEO management Rp 5.000.000/bulan. Mau auto-tagih tiap tanggal 1.</p>
                        <ol>
                            <li>Pergi ke <code>/recurring</code> → <strong>+ Berulang Baru</strong>.</li>
                            <li>Pilih klien → tambah item: <em>"SEO Management Bulanan"</em> qty 1, harga 5.000.000.</li>
                            <li>Set <strong>Frekuensi</strong> = <code>Bulanan</code>.</li>
                            <li>Set <strong>Tanggal Berikutnya</strong> = tanggal 1 bulan depan.</li>
                            <li>Aktifkan toggle <strong>Kirim Otomatis</strong> — invoice langsung email ke klien tanpa perlu konfirmasi manual.</li>
                            <li>Set <strong>Jatuh Tempo</strong> = +14 hari dari tanggal terbit (atau sesuai term kontrak).</li>
                            <li>Tambah <strong>Catatan</strong>: <em>"Pembayaran via transfer max H+14"</em>.</li>
                            <li>Klik <strong>Simpan</strong>. Status: <code>Aktif</code>.</li>
                        </ol>
                        <p class="text-sm text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">✅ <strong>Hasil:</strong> Tiap tanggal 1, sistem auto-buat invoice + auto-email. Kamu cuma perlu catat pembayaran saat klien transfer.</p>
                        <p class="text-xs text-amber-700 bg-amber-50 p-3 rounded-xl border border-amber-200 mt-2">⚠️ <strong>Penting:</strong> Server harus jalanin cron <code>php artisan schedule:run</code> tiap menit. Kalau hosting shared, set di cPanel; kalau VPS, edit <code>crontab -e</code>.</p>
                    </div>

                    <!-- Tutorial 4 -->
                    <div class="mt-10">
                        <h3 class="flex items-center gap-2"><span class="badge-unicorn">Tutorial 4</span> Lacak Pembayaran Parsial</h3>
                        <p class="text-sm text-purple-500 !mt-1"><strong>Skenario:</strong> Klien commit Rp 10jt, bayar DP 50% sekarang, sisanya saat handover.</p>
                        <ol>
                            <li>Buat invoice normal Rp 10.000.000 → kirim ke klien.</li>
                            <li>Saat klien transfer DP Rp 5.000.000:
                                <ul>
                                    <li>Buka invoice → klik <strong>Catat Pembayaran</strong>.</li>
                                    <li>Jumlah: <code>5000000</code>, Metode: <code>Transfer Bank</code>, Tanggal: hari ini, Referensi: nomor transfer / 4 digit terakhir rekening.</li>
                                    <li>Simpan.</li>
                                </ul>
                            </li>
                            <li>Status invoice auto-berubah jadi <span class="badge-gold">Sebagian</span>. Total bayar: 5jt, Sisa: 5jt.</li>
                            <li>Saat klien transfer pelunasan:
                                <ul>
                                    <li>Buka invoice yang sama → <strong>Catat Pembayaran</strong> lagi.</li>
                                    <li>Jumlah: <code>5000000</code>, isi metode/tgl/ref.</li>
                                    <li>Simpan.</li>
                                </ul>
                            </li>
                            <li>Status auto-berubah jadi <span class="badge-mint">Lunas</span>. Timeline pembayaran tampil di halaman invoice.</li>
                        </ol>
                        <p class="text-sm text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">✅ <strong>Hasil:</strong> Riwayat 2 transaksi tercatat lengkap dengan referensi. Reporting akurat untuk pembukuan.</p>
                    </div>

                    <!-- Tutorial 5 -->
                    <div class="mt-10">
                        <h3 class="flex items-center gap-2"><span class="badge-unicorn">Tutorial 5</span> Konfigurasi Payment Gateway</h3>
                        <p class="text-sm text-purple-500 !mt-1"><strong>Skenario:</strong> Mau klien bisa bayar lewat link checkout (Midtrans/Xendit/Stripe/dll) langsung dari halaman publik invoice.</p>
                        <ol>
                            <li>Login sebagai admin workspace → <code>/payment-gateways</code>.</li>
                            <li>Klik <strong>+ Tambah Gateway</strong>.</li>
                            <li>Isi <strong>Nama</strong> bebas (mis. <em>"Midtrans Production"</em>).</li>
                            <li>Pilih <strong>Format API</strong> sesuai provider (REST redirect / embed / QR).</li>
                            <li>Masukkan <strong>Base URL</strong>, <strong>API Key</strong>, <strong>Secret</strong> dari dashboard provider kamu.</li>
                            <li>(Opsional) Tambah <strong>Extra Headers</strong> kalau provider butuh custom auth header.</li>
                            <li>Centang <strong>Aktif untuk Recurring</strong> kalau gateway support charge otomatis.</li>
                            <li>Klik <strong>Simpan</strong>. Test koneksi via tombol <strong>Test Connection</strong>.</li>
                            <li>Setelah aktif, halaman publik <code>/i/{token}</code> otomatis tampilin tombol <strong>Bayar Sekarang</strong>.</li>
                        </ol>
                        <p class="text-sm text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">✅ <strong>Hasil:</strong> Klien bayar 1-klik via link, status invoice auto-update via webhook saat pembayaran sukses.</p>
                        <p class="text-xs text-purple-600 bg-purple-50 p-3 rounded-xl border border-purple-200 mt-2">🔐 <strong>Keamanan:</strong> API key disimpan ter-encrypt di database. Tidak pernah ditampilkan ulang setelah disimpan. Untuk rotate key, hapus & tambah ulang.</p>
                    </div>

                    <!-- FAQ Singkat -->
                    <div class="mt-12 not-prose">
                        <h3 class="font-display font-bold text-xl text-purple-900 mb-4 flex items-center gap-2">❓ FAQ Singkat</h3>
                        <div class="space-y-3">
                            @php $faqs = [
                                ['Bisa edit invoice yang udah Lunas?', 'Tidak — supaya audit trail tetap utuh. Kalau salah, batalkan invoice (status: Dibatalkan), lalu duplikat & buat ulang.'],
                                ['Bisa hapus klien yang punya invoice?', 'Tidak — klien dengan minimal 1 invoice dilindungi supaya history tidak rusak. Solusi: ubah email/nama klien jadi versi lama (mis. "[ARSIP] Nama") biar tidak muncul di pencarian aktif.'],
                                ['Berapa lama PDF di-generate?', 'Biasanya <2 detik. Kalau lebih dari 10 detik, cek log — kemungkinan logo file rusak atau font Google ke-block.'],
                                ['Email invoice masuk spam — kenapa?', 'Default pakai SMTP server. Untuk deliverability bagus, set DKIM/SPF di domain kamu, atau pakai SendGrid/Postmark/Resend.'],
                                ['Bisa multi-currency dalam 1 workspace?', 'Ya — set currency per invoice (bukan workspace). Tiap invoice independen.'],
                                ['Backup data?', 'Database MySQL → mysqldump rutin. File upload (logo, attachment) → backup folder <code>storage/app/public</code>.'],
                            ]; @endphp

                            @foreach($faqs as [$q, $a])
                                <details class="rounded-xl border-2 border-purple-100 hover:border-pink-200 overflow-hidden bg-white/60">
                                    <summary class="cursor-pointer px-4 py-3 font-semibold text-purple-900 flex items-center justify-between gap-2">
                                        <span>{{ $q }}</span>
                                        <span class="text-pink-400 text-xl">+</span>
                                    </summary>
                                    <div class="px-4 pb-4 text-sm text-purple-700 leading-relaxed">{!! $a !!}</div>
                                </details>
                            @endforeach
                        </div>
                    </div>
                </section>

                <!-- ─── AKUN ───────────────────────────── -->
                <section id="akun" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🔐 Pendaftaran & Masuk</h2>
                    <h3>Daftar Akun Baru</h3>
                    <ol>
                        <li>Kunjungi <code>/register</code> atau klik <strong>Daftar</strong> di halaman utama.</li>
                        <li>Isi <strong>nama</strong>, <strong>email</strong>, dan <strong>kata sandi</strong> (min 8 karakter).</li>
                        <li>Klik <strong>✦ Buat Akun</strong>.</li>
                        <li>Cek email kamu untuk link verifikasi. Klik link tersebut untuk verifikasi akun.</li>
                        <li>Saat pertama login, kamu akan diminta membuat <strong>Workspace</strong> (lihat bagian di bawah).</li>
                    </ol>

                    <h3>Masuk</h3>
                    <ol>
                        <li>Kunjungi <code>/login</code> atau klik <strong>Masuk</strong> di halaman utama.</li>
                        <li>Masukkan email dan kata sandi kamu.</li>
                        <li>Opsional, centang <strong>Ingat saya</strong> agar tetap login.</li>
                    </ol>

                    <h3>Lupa Kata Sandi</h3>
                    <ol>
                        <li>Klik <strong>Lupa kata sandi?</strong> di halaman login.</li>
                        <li>Masukkan email kamu — link reset akan dikirim.</li>
                        <li>Klik link, atur kata sandi baru, lalu masuk.</li>
                    </ol>

                    <h3>Profil</h3>
                    <p>Setelah login, klik <strong>avatar</strong> kamu (pojok kanan atas) → <strong>Profil</strong> untuk memperbarui nama dan email. Kamu juga bisa menghapus akun dari halaman ini.</p>
                </section>

                <!-- ─── DASBOR ──────────────────────────── -->
                <section id="dasbor" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📊 Dasbor</h2>
                    <p>Dasbor adalah layar utama setelah login. Menampilkan ringkasan real-time:</p>
                    <ul>
                        <li><strong>Total Pendapatan</strong> — jumlah semua invoice yang sudah dibayar (bulan ini).</li>
                        <li><strong>Belum Dibayar</strong> — total invoice yang belum lunas & tertunda.</li>
                        <li><strong>Jatuh Tempo</strong> — invoice yang lewat tanggal jatuh tempo dan belum dibayar.</li>
                        <li><strong>Klien Aktif</strong> — jumlah klien yang sudah kamu buat.</li>
                        <li><strong>Invoice Terbaru</strong> — daftar akses cepat 5 invoice terakhir.</li>
                    </ul>
                    <p>Klik baris invoice mana pun untuk langsung ke halaman detailnya.</p>
                </section>

                <!-- ─── KLIEN ───────────────────────────── -->
                <section id="klien" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">👥 Klien</h2>
                    <p>Klien adalah orang atau perusahaan yang kamu tagih. Kelola di <code>/clients</code>.</p>
                    <h3>Tambah Klien</h3>
                    <ol>
                        <li>Klik tombol <strong>✦ Klien Baru</strong>.</li>
                        <li>Isi: <strong>Nama</strong> (wajib), <strong>Email</strong>, <strong>Telepon</strong>, <strong>Alamat</strong>, <strong>NPWP</strong>, <strong>Catatan</strong>.</li>
                        <li>Klik <strong>Simpan</strong>.</li>
                    </ol>
                    <h3>Edit / Hapus</h3>
                    <p>Klik baris klien untuk membuka detail, di mana kamu bisa mengedit data atau menghapus klien. Klien yang memiliki invoice tidak bisa dihapus — arsipkan saja.</p>
                    <h3>Fitur Daftar Klien</h3>
                    <ul>
                        <li><strong>Cari</strong> — filter berdasarkan nama atau email.</li>
                        <li><strong>Pagination</strong> — 10 klien per halaman.</li>
                        <li><strong>Total Invoice</strong> — setiap baris menampilkan jumlah invoice milik klien.</li>
                    </ul>
                </section>

                <!-- ─── PRODUK ──────────────────────────── -->
                <section id="produk" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📦 Produk</h2>
                    <p>Produk adalah item yang bisa kamu tambahkan cepat ke invoice (tarif per jam, biaya tetap, retainer, dll). Kelola di <code>/products</code>.</p>
                    <h3>Tambah Produk</h3>
                    <ol>
                        <li>Klik <strong>✦ Produk Baru</strong>.</li>
                        <li>Masukkan: <strong>Nama</strong>, <strong>Deskripsi</strong>, <strong>Harga Satuan</strong>, <strong>SKU</strong> (opsional).</li>
                        <li>Klik <strong>Simpan</strong>.</li>
                    </ol>
                    <p>Saat membuat invoice, gunakan dropdown <strong>Tambah Produk</strong> untuk menyisipkan produk yang sudah ada secara instan.</p>
                </section>

                <!-- ─── INVOICE ─────────────────────────── -->
                <section id="invoice" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📄 Invoice</h2>
                    <p>Invoice adalah inti dari Invocely. Akses di <code>/invoices</code>.</p>

                    <h3>Buat Invoice</h3>
                    <ol>
                        <li>Klik <strong>✦ Invoice Baru</strong> (tombol pojok kanan atas atau dari sidebar).</li>
                        <li><strong>Pilih Klien</strong> — dropdown atau buat klien baru langsung.</li>
                        <li><strong>Tentukan Tanggal</strong> — tanggal terbit, tanggal jatuh tempo (default +14 hari).</li>
                        <li><strong>Pilih Template</strong> — Classic, Modern, Minimal, Bold, atau Elegant.</li>
                        <li><strong>Tambah Item</strong> — ketik deskripsi, jumlah, harga; atau gunakan <strong>Tambah Produk</strong> untuk menyisipkan produk yang sudah ada.</li>
                        <li>Review <strong>Subtotal</strong>, <strong>Pajak</strong>, <strong>Diskon</strong>, <strong>Total</strong>.</li>
                        <li>Tambahkan <strong>Catatan</strong> atau <strong>Syarat & Ketentuan</strong> (opsional).</li>
                        <li>Klik <strong>Simpan</strong> untuk membuat sebagai Draft, atau <strong>Simpan & Kirim</strong> untuk langsung email.</li>
                    </ol>

                    <h3>Status Invoice</h3>
                    <ul>
                        <li><span class="badge-gray">Draft</span> — masih dikerjakan, belum dikirim.</li>
                        <li><span class="badge-blue">Terkirim</span> — sudah dikirim email atau ditandai terkirim.</li>
                        <li><span class="badge-gold">Sebagian</span> — sudah ada pembayaran tapi belum lunas.</li>
                        <li><span class="badge-mint">Lunas</span> — sudah dibayar penuh.</li>
                        <li><span class="badge-red">Dibatalkan</span> — invoice dibatalkan.</li>
                    </ul>

                    <h3>Tindakan pada Invoice</h3>
                    <table class="table-unicorn">
                        <thead><tr><th>Tindakan</th><th>Deskripsi</th></tr></thead>
                        <tbody>
                            <tr><td class="font-semibold">Edit</td><td>Ubah data invoice. Tidak tersedia untuk status Lunas/Dibatalkan.</td></tr>
                            <tr><td class="font-semibold">Duplikat</td><td>Salin invoice dengan nomor baru (Draft).</td></tr>
                            <tr><td class="font-semibold">Tandai Terkirim</td><td>Ubah status dari Draft menjadi Terkirim.</td></tr>
                            <tr><td class="font-semibold">Catat Pembayaran</td><td>Tambahkan pembayaran sebagian atau penuh.</td></tr>
                            <tr><td class="font-semibold">Kirim Email</td><td>Kirim invoice via email dengan lampiran PDF.</td></tr>
                            <tr><td class="font-semibold">Unduh PDF</td><td>Generate PDF siap cetak.</td></tr>
                            <tr><td class="font-semibold">Batalkan</td><td>Void invoice (tidak bisa dibatalkan).</td></tr>
                        </tbody>
                    </table>
                </section>

                <!-- ─── TEMPLATE ────────────────────────── -->
                <section id="template" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📜 Template Invoice</h2>
                    <p>Invocely hadir dengan 5 template cantik. Pilih saat membuat invoice:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                        @foreach([
                            ['Classic', 'Tata letak bersih dan timeless, header aksen ungu.'],
                            ['Modern', 'Blok warna bold, sudut membulat, nuansa kontemporer.'],
                            ['Minimal', 'Fokus pada whitespace, monokrom dengan border halus.'],
                            ['Bold', 'Gradien pink-ke-emas yang cerah, menonjol di inbox.'],
                            ['Elegant', 'Berbasis tipografi, gaya serif, tampilan premium.'],
                        ] as [$nama, $desc])
                            <div class="card-unicorn !p-4">
                                <p class="font-display font-bold text-purple-900">{{ $nama }}</p>
                                <p class="text-sm text-purple-500 mt-1">{{ $desc }}</p>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-4">Warna brand workspace kamu (aksen, logo) otomatis diterapkan ke semua template. Preview template saat mengedit invoice — perubahan bersifat langsung.</p>
                </section>

                <!-- ─── KIRIM EMAIL ─────────────────────── -->
                <section id="email-invoice" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📨 Kirim Invoice via Email</h2>
                    <p>Kirim invoice langsung dari aplikasi — tanpa copy-paste.</p>
                    <ol>
                        <li>Buka invoice dan klik <strong>Kirim Email</strong>.</li>
                        <li>Kolom <strong>Kepada</strong> otomatis terisi email klien.</li>
                        <li>Kustomisasi <strong>Subjek</strong> dan <strong>Pesan</strong>.</li>
                        <li>Klik <strong>Kirim</strong> — klien menerima email HTML cantik dengan lampiran PDF invoice.</li>
                    </ol>
                    <p>Status invoice otomatis berubah menjadi <span class="badge-blue">Terkirim</span>.</p>
                </section>

                <!-- ─── BAGIKAN ─────────────────────────── -->
                <section id="bagikan" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🔗 Bagikan Invoice</h2>
                    <p>Setiap invoice mendapat URL publik unik: <code>/i/{token}</code> (contoh: <code>/i/abc123def</code>).</p>
                    <ul>
                        <li>Klien bisa melihat invoice tanpa harus login.</li>
                        <li>Halaman menampilkan invoice lengkap dengan styling brand kamu.</li>
                        <li>Klien bisa klik <strong>Unduh PDF</strong> dari halaman publik.</li>
                        <li>Bagikan link via email, WhatsApp, Slack, atau embed di portal klien kamu.</li>
                    </ul>
                    <p>Temukan link yang bisa dibagikan di halaman detail invoice pada bagian <strong>Link Publik</strong>.</p>
                </section>

                <!-- ─── PDF ────────────────────────────── -->
                <section id="pdf" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📥 Unduh PDF</h2>
                    <p>Generate PDF profesional dan siap cetak dari invoice mana pun.</p>
                    <ol>
                        <li>Buka halaman detail invoice.</li>
                        <li>Klik <strong>Unduh PDF</strong>.</li>
                        <li>PDF langsung terunduh — dengan template pilihanmu, warna brand, dan semua data invoice.</li>
                    </ol>
                    <p>PDF di-generate server-side menggunakan <code>barryvdh/laravel-dompdf</code> dengan margin halaman yang tepat untuk pencetakan.</p>
                </section>

                <!-- ─── PEMBAYARAN ──────────────────────── -->
                <section id="pembayaran" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">💰 Pembayaran</h2>
                    <p>Lacak semua pembayaran di <code>/payments</code>. Setiap pembayaran terhubung ke invoice.</p>
                    <h3>Catat Pembayaran</h3>
                    <ol>
                        <li>Buka invoice, klik <strong>Catat Pembayaran</strong>.</li>
                        <li>Masukkan <strong>Jumlah</strong>, <strong>Metode Pembayaran</strong> (Tunai, Transfer Bank, Kartu Kredit, E-Wallet, dll.), <strong>Tanggal</strong>, dan <strong>Referensi</strong> (opsional).</li>
                        <li>Klik <strong>Simpan</strong>.</li>
                    </ol>
                    <ul>
                        <li>Jika jumlah sama atau melebihi total invoice, status menjadi <span class="badge-mint">Lunas</span>.</li>
                        <li>Jika jumlah kurang dari total, status menjadi <span class="badge-gold">Sebagian</span> — kamu bisa mencatat pembayaran tambahan nanti.</li>
                    </ul>
                    <h3>Riwayat Pembayaran</h3>
                    <p>Halaman detail invoice menampilkan semua pembayaran dalam bentuk timeline. Kamu bisa menghapus pembayaran yang salah dari daftar ini.</p>
                </section>

                <!-- ─── BERULANG ────────────────────────── -->
                <section id="berulang" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🔄 Invoice Berulang</h2>
                    <p>Otomatiskan penagihan dengan invoice berulang di <code>/recurring</code>.</p>
                    <h3>Buat Invoice Berulang</h3>
                    <ol>
                        <li>Klik <strong>✦ Berulang Baru</strong>.</li>
                        <li>Isi kolom yang sama seperti invoice biasa (klien, item, pajak, dll.).</li>
                        <li>Tentukan <strong>Frekuensi</strong> — Harian, Mingguan, Bulanan, Triwulan, atau Tahunan.</li>
                        <li>Tentukan <strong>Tanggal Berikutnya</strong> — kapan invoice pertama harus dibuat otomatis.</li>
                        <li>Aktifkan <strong>Kirim Otomatis</strong> untuk langsung mengirim email saat invoice dibuat (opsional).</li>
                        <li>Klik <strong>Simpan</strong>.</li>
                    </ol>
                    <h3>Kelola Invoice Berulang</h3>
                    <ul>
                        <li><strong>Aktifkan/Nonaktifkan</strong> — jeda atau lanjutkan jadwal berulang.</li>
                        <li><strong>Edit</strong> — ubah template, item, atau frekuensi. Invoice yang sudah dibuat tidak terpengaruh.</li>
                        <li><strong>Hapus</strong> — hapus jadwal. Invoice yang sudah dibuat tetap utuh.</li>
                    </ul>
                    <p>Invoice berulang diproses melalui Laravel task scheduler. Pastikan cron job server kamu menjalankan <code>php artisan schedule:run</code> setiap menit.</p>
                </section>

                <!-- ─── LAPORAN ─────────────────────────── -->
                <section id="laporan" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">📈 Laporan</h2>
                    <p>Akses <code>/reports</code> untuk insight bisnis:</p>
                    <ul>
                        <li><strong>Pendapatan dari Waktu ke Waktu</strong> — grafik pendapatan bulanan.</li>
                        <li><strong>Klien Teratas</strong> — peringkat berdasarkan total nilai invoice.</li>
                        <li><strong>Rincian Belum Dibayar</strong> — jumlah belum lunas vs. jatuh tempo.</li>
                        <li><strong>Metode Pembayaran</strong> — diagram pie tunai, transfer, kartu kredit.</li>
                        <li><strong>Distribusi Status Invoice</strong> — rasio draft / terkirim / lunas / dibatalkan.</li>
                        <li><strong>Filter Rentang Tanggal</strong> — lihat laporan per bulan, kuartal, tahun, atau rentang kustom.</li>
                    </ul>
                </section>

                <!-- ─── WORKSPACE ───────────────────────── -->
                <section id="workspace" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">⚙️ Workspace</h2>
                    <p>Workspace adalah profil bisnismu. Konfigurasi di <code>/workspace/settings</code>:</p>
                    <ul>
                        <li><strong>Nama</strong> — nama bisnis/tim kamu (muncul di invoice).</li>
                        <li><strong>Mata Uang</strong> — misal IDR, USD, EUR, GBP.</li>
                        <li><strong>Warna Brand</strong> — warna aksen yang diterapkan ke semua template invoice.</li>
                        <li><strong>Logo</strong> — upload logo bisnis (ditampilkan di invoice dan halaman publik).</li>
                        <li><strong>Alamat & Kontak</strong> — alamat bisnis, telepon, website (ditampilkan di invoice).</li>
                        <li><strong>Pengaturan Pajak</strong> — tarif pajak default, format NPWP.</li>
                        <li><strong>Awalan Invoice</strong> — misal <code>INV-</code> → invoice tampil sebagai <code>INV-001</code>.</li>
                        <li><strong>Nomor Invoice Berikutnya</strong> — titik awal auto-increment.</li>
                    </ul>
                </section>

                <!-- ─── SEO ────────────────────────────── -->
                <section id="seo" class="card-unicorn !p-8 mb-8">
                    <h2 class="!mt-0">🌐 Halaman SEO Otomatis</h2>
                    <p>Invocely menyertakan halaman SEO yang dibuat otomatis untuk membantu bisnismu ditemukan di Google. Semua halaman memiliki skema JSON-LD, meta tag, dan konten 300+ kata yang unik.</p>
                    <table class="table-unicorn">
                        <thead><tr><th>Rute</th><th>Tujuan</th></tr></thead>
                        <tbody>
                            <tr><td><code>/best-invoicing-tools-{tahun}</code></td><td>Daftar tools invoicing terbaik tahun ini</td></tr>
                            <tr><td><code>/alternatives-to-{slug}</code></td><td>Halaman alternatif produk (misal: freshbooks, quickbooks)</td></tr>
                            <tr><td><code>/compare/{a}-vs-{b}</code></td><td>Halaman perbandingan head-to-head</td></tr>
                            <tr><td><code>/invoice-templates</code></td><td>Galeri template dengan preview</td></tr>
                            <tr><td><code>/sitemap.xml</code></td><td>XML sitemap dinamis untuk search engine</td></tr>
                            <tr><td><code>/robots.txt</code></td><td>Aturan crawl untuk bot</td></tr>
                        </tbody>
                    </table>
                    <p class="mt-4">Halaman SEO bersifat publik dan bisa diindeks. Submit <code>/sitemap.xml</code> ke Google Search Console untuk indexing lebih cepat.</p>
                </section>

                <!-- ─── BELI SOURCE CODE ────────────────── -->
                <section id="beli" class="card-unicorn-glow !p-8 mb-8">
                    <h2 class="!mt-0">🛒 Beli Source Code</h2>
                    <p class="text-lg font-semibold text-purple-800">Mau aplikasi invoicing ini untuk bisnis atau klien kamu sendiri?</p>
                    <p>Full source code — Laravel 13 + Tailwind + Alpine.js — tersedia untuk dibeli.</p>

                    <div class="mt-6 p-6 rounded-2xl" style="background: linear-gradient(135deg, rgba(255,122,198,0.12), rgba(167,139,250,0.12)); border: 2px solid rgba(167,139,250,0.3);">
                        <p class="font-display font-bold text-xl text-purple-900 mb-2">💎 Yang Kamu Dapatkan</p>
                        <ul>
                            <li>Full source code Laravel 13 (semua controller, model, view)</li>
                            <li>5 template invoice (Classic, Modern, Minimal, Bold, Elegant)</li>
                            <li>Modul Klien, Produk, Invoice, Pembayaran, Invoice Berulang</li>
                            <li>Kirim invoice via email dengan lampiran PDF</li>
                            <li>Bagikan invoice via link publik dengan token unik</li>
                            <li>Dasbor laporan dengan grafik</li>
                            <li>Halaman programmatic SEO otomatis</li>
                            <li>Tailwind CSS 3 tema Unicorn + Alpine.js</li>
                            <li>Database migrations & seeders</li>
                            <li>Lisensi: <strong>1 domain = 1 lisensi</strong> · unlimited client</li>
                        </ul>
                    </div>

                    <p class="mt-6 text-lg font-semibold">💰 Harga: Nego — hubungi langsung.</p>

                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <a href="https://wa.me/62081296052010" target="_blank" rel="noopener" class="btn-unicorn px-8 py-4 text-base !gap-3" style="font-size:1.05rem;">
                            💬 WhatsApp 081296052010
                        </a>
                        <p class="text-sm text-purple-500">Atau kirim pesan untuk demo, permintaan kustomisasi, atau inquiry harga.</p>
                    </div>
                </section>

                <div class="text-center py-12">
                    <div class="rainbow-bar mb-6 rounded-full"></div>
                    <p class="text-sm text-purple-400">✦ Itu semua keajaibannya. Sekarang buat invoice ajaibmu! ✦</p>
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn-unicorn mt-6 px-8 py-4 text-base">
                        ✦ Mulai Gratis
                    </a>
                </div>

            </div>
        </main>
    </div>

    <!-- Highlight sidebar aktif via IntersectionObserver -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const links = document.querySelectorAll('.docs-sidebar-link');
            const sections = [...links].map(l => document.querySelector(l.getAttribute('href')));
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        links.forEach(l => l.classList.remove('active'));
                        const active = document.querySelector(`.docs-sidebar-link[href="#${entry.target.id}"]`);
                        if (active) active.classList.add('active');
                    }
                });
            }, { rootMargin: '-80px 0px -60% 0px' });
            sections.forEach(s => s && observer.observe(s));
        });
    </script>
</body>
</html>
