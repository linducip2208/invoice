<x-layouts.seo :meta="$seoMeta">
    <div class="max-w-5xl mx-auto py-16 px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="badge-unicorn">📝 {{ __('Blog') }}</span>
            <h1 class="mt-4 font-display text-3xl sm:text-4xl font-bold text-purple-900">
                {{ $currentCategory->name ?? 'Blog' }} — {{ config('app.name') }}
            </h1>
            <p class="mt-3 text-purple-500 max-w-xl mx-auto">
                Tips, trik, dan panduan seputar invoicing, freelancing, dan manajemen bisnis UKM.
            </p>
        </div>

        {{-- Categories --}}
        @if($categories->isNotEmpty())
            <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
                <a href="{{ route('blog.index') }}"
                   class="px-4 py-1.5 rounded-full text-sm font-semibold transition {{ !isset($currentCategory) ? 'bg-purple-100 text-purple-800' : 'bg-purple-50 text-purple-600 hover:bg-purple-100' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}"
                       class="px-4 py-1.5 rounded-full text-sm font-semibold transition {{ isset($currentCategory) && $currentCategory->id === $cat->id ? 'bg-purple-100 text-purple-800' : 'bg-purple-50 text-purple-600 hover:bg-purple-100' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Posts grid --}}
        @if($posts->isEmpty())
            <div class="text-center py-16 sparkles-bg">
                <span class="text-6xl block animate-unicorn-float">📝</span>
                <p class="mt-4 text-purple-500 font-semibold">Belum ada artikel.</p>
            </div>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="card-unicorn group block no-underline">
                        @if($post->featured_image)
                            <div class="w-full h-44 rounded-xl bg-cover bg-center mb-4" style="background-image: url('{{ $post->featured_image }}');"></div>
                        @else
                            <div class="w-full h-44 rounded-xl bg-gradient-to-br from-pink-100 via-purple-100 to-sky-100 mb-4 flex items-center justify-center text-4xl">📝</div>
                        @endif
                        @if($post->category)
                            <span class="badge-unicorn text-[10px] mb-2 inline-block">{{ $post->category->name }}</span>
                        @endif
                        <h3 class="font-display font-bold text-lg text-purple-900 group-hover:text-pink-500 transition leading-snug">{{ $post->title }}</h3>
                        <p class="mt-2 text-sm text-purple-500 line-clamp-2">{{ $post->excerpt }}</p>
                        <p class="mt-3 text-xs text-purple-400">{{ $post->published_at?->format('d M Y') }}</p>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </div>
</x-layouts.seo>
