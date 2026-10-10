{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} Blog</title>
        <link>{{ url('/blog') }}</link>
        <description>Tips, trik, dan panduan invoicing untuk freelancer dan UKM</description>
        <language>id</language>
        <atom:link href="{{ url('/blog/feed.xml') }}" rel="self" type="application/rss+xml"/>
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ url('/blog/' . $post->slug) }}</link>
            <guid>{{ url('/blog/' . $post->slug) }}</guid>
            <description>{{ $post->excerpt }}</description>
            <pubDate>{{ $post->published_at?->toRfc2822String() }}</pubDate>
            @if($post->category)<category>{{ $post->category->name }}</category>@endif
        </item>
        @endforeach
    </channel>
</rss>
