<x-tabler.admin-layout :title="__('Blog')" :pretitle="__('Superadmin')" :subtitle="__('Manage blog content for SEO and organic traffic.')">
    <x-slot name="actions">
        <a href="{{ route('admin.blog.categories') }}" class="btn">
            <x-ti name="file-text" /> {{ __('Categories') }}
        </a>
        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">
            <x-ti name="plus" /> {{ __('New Article') }}
        </a>
    </x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td>
                                <a href="{{ route('blog.show', $post->slug) }}" target="_blank" rel="noopener" class="fw-semibold">
                                    {{ Str::limit($post->title, 50) }}
                                </a>
                            </td>
                            <td class="text-muted">{{ $post->category?->name ?? '—' }}</td>
                            <td>
                                @if($post->is_published)
                                    <span class="badge bg-green-lt">{{ __('Published') }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">{{ __('Draft') }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</a>
                                <x-tabler.delete-button :action="route('admin.blog.destroy', $post)"
                                    :confirm="__('Delete this article?')" :title="__('Delete')" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-tabler.empty icon="file-text" :title="__('No articles yet')"
                                    :body="__('Create your first article to get started.')"
                                    :actionUrl="route('admin.blog.create')" :actionLabel="__('New Article')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
            <div class="card-footer d-flex justify-content-center">{{ $posts->links() }}</div>
        @endif
    </div>
</x-tabler.admin-layout>
