<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tips Invoicing', 'slug' => 'tips-invoicing', 'description' => 'Tips dan trik seputar pembuatan dan pengelolaan invoice.'],
            ['name' => 'Freelancing', 'slug' => 'freelancing', 'description' => 'Panduan untuk freelancer: dapatkan klien, atur keuangan, dan scale bisnis.'],
            ['name' => 'UKM & Bisnis', 'slug' => 'ukm-bisnis', 'description' => 'Strategi bisnis untuk UKM dan startup di Indonesia.'],
            ['name' => 'Update Produk', 'slug' => 'update-produk', 'description' => 'Fitur baru, update, dan roadmap Invocely.'],
        ];

        foreach ($categories as $cat) {
            BlogCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        $catIds = BlogCategory::pluck('id', 'slug');

        $posts = [
            [
                'title' => '5 Template Invoice yang Bikin Klien Terkesan',
                'slug' => '5-template-invoice-bikin-klien-terkesan',
                'category_slug' => 'tips-invoicing',
                'excerpt' => 'Template invoice yang profesional bisa meningkatkan kredibilitas bisnis kamu. Ini 5 template terbaik beserta tips memilihnya.',
                'content' => '<h2>Kenapa Template Invoice Penting?</h2>
<p>Invoice adalah wajah bisnis kamu. Template yang profesional memberikan kesan pertama yang baik dan meningkatkan kepercayaan klien.</p>
<h2>5 Template Terbaik</h2>
<h3>1. Classic</h3>
<p>Desain minimalis dengan tata letak bersih. Cocok untuk hampir semua jenis bisnis. Fokus pada informasi penting tanpa distraksi visual.</p>
<h3>2. Modern</h3>
<p>Warna-warna cerah dan tipografi berani. Cocok untuk agency kreatif, desainer, dan startup tech.</p>
<h3>3. Minimal</h3>
<p>Hitam putih dengan whitespace luas. Profesional dan elegan. Cocok untuk konsultan dan lawyer.</p>
<h3>4. Bold</h3>
<p>Warna kontras tinggi dan layout dramatis. Cocok untuk brand yang ingin tampil beda.</p>
<h3>5. Elegant</h3>
<p>Gradient halus dan tipografi serif. Mewah dan sophisticated. Cocok untuk luxury brand dan wedding planner.</p>
<h2>Tips Memilih Template</h2>
<ul><li>Sesuaikan dengan brand identity kamu</li><li>Pastikan mudah dibaca di mobile</li><li>Sertakan logo dan informasi kontak</li><li>Gunakan warna yang konsisten</li></ul>',
            ],
            [
                'title' => 'Cara Mengatur Invoice Berulang untuk Passive Income',
                'slug' => 'invoice-berulang-passive-income',
                'category_slug' => 'tips-invoicing',
                'excerpt' => 'Recurring invoice menghemat waktu dan memastikan cashflow stabil. Pelajari cara setup dan best practice-nya.',
                'content' => '<h2>Apa itu Recurring Invoice?</h2>
<p>Recurring invoice adalah invoice yang dikirim otomatis secara berkala — harian, mingguan, bulanan, atau tahunan. Cocok untuk retainer client, subscription service, dan maintenance contract.</p>
<h2>Kenapa Perlu Recurring Invoice?</h2>
<ul><li>Menghemat waktu administrasi</li><li>Mencegah lupa menagih</li><li>Cashflow lebih predictable</li><li>Klien lebih nyaman</li></ul>
<h2>Cara Setup di Invocely</h2>
<p>Cukup buka menu Recurring, klik "Create", pilih klien, frekuensi, dan template. Sistem akan generate invoice otomatis sesuai jadwal.</p>
<h2>Best Practice</h2>
<ul><li>Kirim reminder H-3 sebelum due date</li><li>Sertakan terms & conditions jelas</li><li>Review recurring list setiap bulan</li><li>Pastikan rate tetap kompetitif</li></ul>',
            ],
            [
                'title' => 'Freelancer Wajib Punya Sistem Invoicing — Ini Alasannya',
                'slug' => 'freelancer-wajib-punya-sistem-invoicing',
                'category_slug' => 'freelancing',
                'excerpt' => 'Masih pakai Excel atau Word untuk invoice? Ini 7 alasan kenapa freelancer harus upgrade ke sistem invoicing digital.',
                'content' => '<h2>Masalah dengan Invoice Manual</h2>
<p>Banyak freelancer masih membuat invoice di Excel atau Word, lalu convert ke PDF, lalu attach di email. Ini rentan error dan makan waktu.</p>
<h2>7 Alasan Upgrade</h2>
<ol>
<li><strong>Auto-numbering</strong> — Tidak perlu tracking nomor invoice manual</li>
<li><strong>Client database</strong> — Simpan semua data klien di satu tempat</li>
<li><strong>Payment tracking</strong> — Tahu siapa yang sudah dan belum bayar</li>
<li><strong>Laporan otomatis</strong> — Revenue, outstanding, dan pajak dalam satu klik</li>
<li><strong>Template profesional</strong> — Invoice terlihat branded dan credible</li>
<li><strong>Kirim via email</strong> — Langsung dari aplikasi dengan PDF attachment</li>
<li><strong>Public share link</strong> — Klien bisa lihat dan bayar tanpa login</li>
</ol>
<h2>Mulai dengan Invocely</h2>
<p>Gratis untuk 5 klien pertama. Daftar sekarang dan upgrade cara kamu menagih.</p>',
            ],
            [
                'title' => 'Update Invocely: Blog, IndexNow, dan Responsive Design',
                'slug' => 'update-invocely-blog-indexnow-responsive',
                'category_slug' => 'update-produk',
                'excerpt' => 'Rangkuman update terbaru Invocely: Blog untuk SEO, IndexNow auto-submit, responsive design full, dan dua-column branded login.',
                'content' => '<h2>Update Juni 2026</h2>
<p>Kami terus mengembangkan Invocely agar semakin powerful. Berikut update terbaru:</p>
<h3>📝 Blog System</h3>
<p>Blog built-in dengan kategori, RSS feed, JSON-LD schema, dan auto-submit ke IndexNow setiap kali publish artikel baru. Admin CRUD lengkap untuk kelola konten.</p>
<h3>🚀 IndexNow Integration</h3>
<p>Auto-submit URL ke Bing, Yandex, Seznam, dan Naver setiap kali ada konten baru. Scheduler berjalan otomatis setiap hari jam 02:45.</p>
<h3>📱 Full Responsive Design</h3>
<p>Seluruh halaman — admin panel, dashboard, form, dan blog — sekarang fully responsive. Mobile hamburger menu, touch-friendly buttons (min 44px), dan tabel dengan horizontal scroll.</p>
<h3>🔐 Two-Column Branded Login</h3>
<p>Halaman login, register, forgot password, reset password, verify email, dan confirm password sekarang pakai layout two-column premium dengan hero brand panel.</p>
<h2>What\'s Next?</h2>
<ul><li>Stripe & PayPal live integration</li><li>e-Faktur auto-submit ke DJP</li><li>Client portal self-service</li><li>Mobile PWA notifications</li></ul>',
            ],
            [
                'title' => 'Cara Membuat Laporan Keuangan Sederhana untuk UKM',
                'slug' => 'laporan-keuangan-sederhana-ukm',
                'category_slug' => 'ukm-bisnis',
                'excerpt' => 'Laporan keuangan tidak harus rumit. Ini panduan membuat P&L, aging report, dan cashflow forecast untuk UKM.',
                'content' => '<h2>Kenapa UKM Perlu Laporan Keuangan?</h2>
<p>Laporan keuangan membantu kamu memahami kesehatan bisnis, mengambil keputusan tepat, dan memenuhi syarat pajak.</p>
<h2>3 Laporan Wajib</h2>
<h3>1. Profit & Loss (P&L)</h3>
<p>Ringkasan pendapatan dan pengeluaran dalam periode tertentu. Menunjukkan apakah bisnis untung atau rugi.</p>
<h3>2. Aging Report</h3>
<p>Menampilkan invoice yang belum dibayar berdasarkan umur: 0-30 hari, 31-60 hari, 61-90 hari, dan 90+ hari. Membantu prioritas penagihan.</p>
<h3>3. Cashflow Forecast</h3>
<p>Proyeksi kas masuk dan keluar untuk beberapa bulan ke depan. Mencegah kejutan cashflow negatif.</p>
<h2>Pakai Invocely untuk Laporan</h2>
<p>Semua laporan di atas tersedia di dashboard Invocely. Cukup pilih periode, dan grafik + tabel akan terisi otomatis dari data invoice dan payment.</p>',
            ],
            [
                'title' => 'Invoice Profesional dalam 2 Menit — Tutorial Singkat',
                'slug' => 'invoice-profesional-2-menit',
                'category_slug' => 'tips-invoicing',
                'excerpt' => 'Cara membuat dan mengirim invoice profesional dalam 2 menit menggunakan Invocely. Step-by-step tutorial.',
                'content' => '<h2>Step 1: Tambah Klien (30 detik)</h2>
<p>Buka menu Clients → Add Client. Isi nama, email, dan alamat. Data klien akan tersimpan untuk invoice berikutnya.</p>
<h2>Step 2: Buat Invoice (60 detik)</h2>
<p>Buka menu Invoices → Create. Pilih klien, tambahkan item (deskripsi, quantity, harga), pilih template, klik Create.</p>
<h2>Step 3: Kirim (30 detik)</h2>
<p>Klik tombol Send di halaman invoice. Email akan terkirim dengan PDF invoice sebagai attachment. Klien juga dapat public link untuk melihat invoice tanpa login.</p>
<h2>Bonus: Lacak Status</h2>
<p>Di dashboard, kamu bisa lihat status semua invoice: draft, sent, paid, partial, overdue. Filter dan search untuk pencarian cepat.</p>',
            ],
        ];

        foreach ($posts as $data) {
            if (BlogPost::where('slug', $data['slug'])->exists()) {
                continue;
            }

            BlogPost::create([
                'category_id' => $catIds[$data['category_slug']] ?? null,
                'author_id' => 1,
                'title' => $data['title'],
                'slug' => $data['slug'],
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'is_published' => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'meta_title' => $data['title'] . ' — Invocely Blog',
                'meta_description' => $data['excerpt'],
            ]);
        }
    }
}
