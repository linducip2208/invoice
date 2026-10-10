<x-tabler.public :title="$seoMeta['title'] ?? null" :meta-description="$seoMeta['description'] ?? ''" :og-type="$seoMeta['og_type'] ?? 'website'" :published-time="$seoMeta['published_time'] ?? null">
    <div class="page-header">
        <div class="row g-2 align-items-center">
            <div class="col text-center">
                <div class="page-pretitle justify-content-center"><span class="badge bg-blue-lt"><x-ti name="file-text" /> {{ __('Blog') }}</span></div>
                <h1 class="page-title">{{ $currentCategory->name ?? 'Blog' }} — {{ config('app.name') }}</h1>
                <div class="text-muted mt-1">{{ __('Tips, guides, and stories about invoicing, freelancing, and small business management.') }}</div>
            </div>
        </div>
    </div>

    @if($categories->isNotEmpty())
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-4">
            <a href="{{ route('blog.index') }}" class="btn btn-sm {{ !isset($currentCategory) ? 'btn-primary' : '' }}">{{ __('All') }}</a>
            @foreach($categories as $cat)
                <a href="{{ route('blog.category', $cat->slug) }}" class="btn btn-sm {{ isset($currentCategory) && $currentCategory->id === $cat->id ? 'btn-primary' : '' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    @endif

    @if($posts->isEmpty())
        <div class="card">
            <div class="card-body">
                <x-tabler.empty icon="file-text" :title="__('No articles yet')" :body="__('Check back soon for new posts.')" />
            </div>
        </div>
    @else
        <div class="row row-cards">
            @foreach($posts as $post)
                <div class="col-sm-6 col-lg-4">
                    <div class="card h-100">
                        @if($post->featured_image)
                            <div class="card-img-top img-responsive" style="background-image: url('{{ $post->featured_image }}'); min-height: 11rem;"></div>
                        @endif
                        <div class="card-body">
                            @if($post->category)
                                <span class="badge bg-blue-lt mb-2">{{ $post->category->name }}</span>
                            @endif
                            <h3 class="card-title"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                            <p class="text-muted">{{ $post->excerpt }}</p>
                            <p class="text-muted mb-0"><x-ti name="calendar" /> {{ $post->published_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($posts->hasPages())
            <div class="d-flex justify-content-center mt-4">{{ $posts->links() }}</div>
        @endif
    @endif
</x-tabler.public>
