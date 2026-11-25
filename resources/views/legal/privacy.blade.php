<x-tabler.public :title="__('Privacy')" :meta-description="__('Kebijakan privasi Invocely: isolasi workspace, retensi data, dan keamanan.')">
    <h1 class="page-title mb-2">{{ __('Privacy Policy') }}</h1>
    <p class="text-muted">{{ __('Ringkasan jujur sesuai implementasi aplikasi saat ini.') }}</p>
    <div class="card"><div class="card-body">
        <ul>
            <li>{{ __('Setiap record milik tepat satu workspace; URL lintas workspace mengembalikan 403/404.') }}</li>
            <li>{{ __('Kami tidak menjual data. Email/SMTP, gateway, dan AI memakai kredensial provider Anda sendiri.') }}</li>
            <li>{{ __('Audit log read-only mencatat perubahan kunci; API token per workspace dan dapat dicabut.') }}</li>
            <li>{{ __('Ekspor CSV Anda tersedia kapan saja; akun demo hanya untuk local/dev.') }}</li>
        </ul>
        <p class="text-muted small mb-0">{{ __('Hubungi support melalui Support Center untuk permintaan penghapusan data.') }}</p>
    </div></div>
</x-tabler.public>
