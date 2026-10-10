<x-tabler.layout :title="__('Edit Article')" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.blog.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Back to Blog') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $post->title }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blog.update', $post) }}">
                @csrf @method('PUT')
                <div class="row g-2">
                    <div class="col-12">
                        <x-tabler.field :label="__('Title')" name="title" required>
                            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                                placeholder="{{ __('Article title…') }}" class="form-control @error('title') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Category')" name="category_id">
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">{{ __('— No Category —') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Status')" name="is_published">
                            <select id="is_published" name="is_published" class="form-select @error('is_published') is-invalid @enderror">
                                <option value="1" @selected(old('is_published', $post->is_published) == '1')>{{ __('Published') }}</option>
                                <option value="0" @selected(old('is_published', $post->is_published) == '0')>{{ __('Draft') }}</option>
                            </select>
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Featured Image URL')" name="featured_image">
                            <input type="text" id="featured_image" name="featured_image" value="{{ old('featured_image', $post->featured_image) }}"
                                placeholder="https://…" class="form-control @error('featured_image') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Publish Date')" name="published_at">
                            <input type="datetime-local" id="published_at" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                                class="form-control @error('published_at') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Meta Title')" name="meta_title">
                            <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}"
                                placeholder="{{ __('SEO title…') }}" class="form-control @error('meta_title') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-6">
                        <x-tabler.field :label="__('Meta Description')" name="meta_description">
                            <input type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', $post->meta_description) }}"
                                placeholder="{{ __('SEO description…') }}" class="form-control @error('meta_description') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <x-tabler.field :label="__('Excerpt')" name="excerpt">
                            <textarea id="excerpt" name="excerpt" rows="2" placeholder="{{ __('Short summary…') }}"
                                class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                        </x-tabler.field>
                    </div>
                    <div class="col-12">
                        <x-tabler.field :label="__('Content')" name="content" required>
                            <textarea id="content" name="content" rows="20" required placeholder="{{ __('Write HTML content…') }}"
                                class="form-control font-monospace @error('content') is-invalid @enderror">{{ old('content', $post->content) }}</textarea>
                        </x-tabler.field>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <a href="{{ route('admin.blog.index') }}" class="btn">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        <x-ti name="check" /> {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-tabler.layout>
