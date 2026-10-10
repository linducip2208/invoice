<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ url('/invoice-templates') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/docs') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/best-invoicing-tools-' . date('Y')) }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.8</priority>
    </url>
    @foreach ([2024, 2023] as $pastYear)
    <url>
        <loc>{{ url('/best-invoicing-tools-' . $pastYear) }}</loc>
        <changefreq>yearly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @foreach (['invocely', 'freshbooks', 'quickbooks', 'xero', 'zoho-invoice', 'wave', 'invoice-ninja'] as $slug)
    <url>
        <loc>{{ url('/alternatives-to-' . $slug) }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
    @foreach ([['invocely', 'freshbooks'], ['invocely', 'quickbooks'], ['invocely', 'xero'], ['invocely', 'zoho-invoice'], ['freshbooks', 'quickbooks'], ['quickbooks', 'xero']] as $pair)
    <url>
        <loc>{{ url('/compare/' . $pair[0] . '-vs-' . $pair[1]) }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
    <url>
        <loc>{{ url('/blog') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    @php $blogPosts = \App\Models\BlogPost::published()->get(); @endphp
    @foreach ($blogPosts as $post)
    <url>
        <loc>{{ url('/blog/' . $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    @endforeach
</urlset>
