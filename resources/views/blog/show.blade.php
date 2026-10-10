<x-tabler.public :title="$seoMeta['title'] ?? null" :meta-description="$seoMeta['description'] ?? ''" :og-type="$seoMeta['og_type'] ?? 'website'" :published-time="$seoMeta['published_time'] ?? null">
    @push('head')
        @php
            $articleJsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $post->meta_description,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at->toIso8601String(),
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($articleJsonLd, JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
        @if($post->category)
            <li class="breadcrumb-item"><a href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a></li>
        @endif
        <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
    </ol>

    <article class="card">
        <div class="card-body">
            <header class="mb-3">
                @if($post->category)
                    <span class="badge bg-blue-lt mb-2">{{ $post->category->name }}</span>
                @endif
                <h1 class="page-title">{{ $post->title }}</h1>
                @if($post->excerpt)
                    <p class="text-muted mt-2">{{ $post->excerpt }}</p>
                @endif
                <div class="d-flex align-items-center gap-2 text-muted mt-2">
                    <span class="d-inline-flex align-items-center gap-1"><x-ti name="calendar" /> {{ $post->published_at?->format('d M Y') }}</span>
                    @if($post->author)
                        <span>&middot;</span>
                        <span>{{ $post->author->name }}</span>
                    @endif
                </div>
            </header>

            @if($post->featured_image)
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="img-responsive rounded mb-3">
            @endif

            <div class="markdown">
                {!! $post->content !!}
            </div>
        </div>
    </article>

    @if($relatedPosts->isNotEmpty())
        <div class="page-header mt-4">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">{{ __('Related articles') }}</h2>
                </div>
            </div>
        </div>
        <div class="row row-cards">
            @foreach($relatedPosts as $rel)
                <div class="col-sm-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="card-title"><a href="{{ route('blog.show', $rel->slug) }}">{{ $rel->title }}</a></h4>
                            <p class="text-muted mb-0"><x-ti name="calendar" /> {{ $rel->published_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card text-center mt-4">
        <div class="card-body">
            <h3 class="card-title justify-content-center">{{ __('Try Invocely free') }}</h3>
            <p class="text-muted">{{ __('Simple invoicing for freelancers and small businesses.') }}</p>
            <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="btn btn-primary"><x-ti name="plus" /> {{ __('Start Free') }}</a>
        </div>
    </div>
</x-tabler.public>
