<x-layouts.seo :meta="$seoMeta">
    <div class="max-w-3xl mx-auto py-16 px-4 sm:px-6">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-purple-400 mb-8">
            <a href="{{ route('blog.index') }}" class="hover:text-pink-500 transition">Blog</a>
            @if($post->category)
                <span class="mx-1.5">/</span>
                <a href="{{ route('blog.category', $post->category->slug) }}" class="hover:text-pink-500 transition">{{ $post->category->name }}</a>
            @endif
            <span class="mx-1.5">/</span>
            <span class="text-purple-600">{{ $post->title }}</span>
        </nav>

        <article>
            {{-- Header --}}
            <header class="mb-8">
                @if($post->category)
                    <span class="badge-unicorn text-xs mb-3 inline-block">{{ $post->category->name }}</span>
                @endif
                <h1 class="font-display text-3xl sm:text-4xl font-bold text-purple-900 leading-tight">{{ $post->title }}</h1>
                @if($post->excerpt)
                    <p class="mt-3 text-lg text-purple-500 leading-relaxed">{{ $post->excerpt }}</p>
                @endif
                <div class="mt-4 flex items-center gap-3 text-sm text-purple-400">
                    <span>{{ $post->published_at?->format('d M Y') }}</span>
                    @if($post->author)
                        <span>·</span>
                        <span>{{ $post->author->name }}</span>
                    @endif
                </div>
            </header>

            {{-- Featured image --}}
            @if($post->featured_image)
                <div class="w-full h-64 sm:h-80 rounded-2xl bg-cover bg-center mb-8" style="background-image: url('{{ $post->featured_image }}');"></div>
            @endif

            {{-- Content --}}
            <div class="prose prose-purple max-w-none text-purple-800 leading-relaxed">
                {!! $post->content !!}
            </div>
        </article>

        {{-- Related posts --}}
        @if($relatedPosts->isNotEmpty())
            <div class="mt-16 pt-8 border-t border-purple-100">
                <h3 class="font-display font-bold text-xl text-purple-900 mb-6">Artikel Terkait</h3>
                <div class="grid sm:grid-cols-3 gap-4">
                    @foreach($relatedPosts as $rel)
                        <a href="{{ route('blog.show', $rel->slug) }}" class="card-unicorn group block no-underline">
                            <h4 class="font-semibold text-purple-900 group-hover:text-pink-500 transition leading-snug">{{ $rel->title }}</h4>
                            <p class="mt-1 text-xs text-purple-400">{{ $rel->published_at?->format('d M Y') }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- CTA --}}
        <div class="mt-12 p-8 rounded-2xl text-center" style="background: linear-gradient(135deg, rgba(255,228,241,0.6), rgba(237,233,254,0.6));">
            <p class="font-display font-bold text-purple-900 text-lg">Mau Aplikasi Ini?</p>
            <p class="text-sm text-purple-600 mt-1">Source code aplikasi invoicing lengkap siap pakai.</p>
            <a href="https://wa.me/62081296052010" target="_blank" rel="noopener"
               class="btn-unicorn mt-4 px-8 py-3 inline-flex items-center gap-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat WhatsApp
            </a>
        </div>
    </div>

    {{-- JSON-LD Article --}}
    <script type="application/ld+json">
    @verbatim
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ $post->title }}",
        "description": "{{ $post->meta_description }}",
        "datePublished": "{{ $post->published_at?->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}"
        @if($post->author),
        "author": { "@type": "Person", "name": "{{ $post->author->name }}" }
        @endif
    }
    @endverbatim
    </script>
</x-layouts.seo>
