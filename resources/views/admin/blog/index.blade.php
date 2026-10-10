<x-admin-layout title="Blog">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="font-display font-bold text-xl text-purple-900">📝 Artikel Blog</h2>
                <p class="text-sm text-purple-500">Kelola konten blog untuk SEO & traffic organik.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.blog.categories') }}" class="btn-unicorn-secondary text-xs">📂 Kategori</a>
                <a href="{{ route('admin.blog.create') }}" class="btn-unicorn text-xs">+ Artikel Baru</a>
            </div>
        </div>

        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <div class="overflow-x-auto">
                <table class="table-unicorn">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td>
                                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="font-bold text-purple-700 hover:text-pink-500">
                                        {{ Str::limit($post->title, 50) }}
                                    </a>
                                </td>
                                <td>{{ $post->category?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge-{{ $post->is_published ? 'green' : 'gray' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-purple-500 text-xs">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
                                <td class="text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.blog.edit', $post) }}" class="text-amber-600 hover:text-amber-700 font-semibold text-sm">Edit</a>
                                    <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Hapus artikel ini?')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-purple-400">
                                    <span class="text-4xl block mb-3">📝</span>
                                    Belum ada artikel. <a href="{{ route('admin.blog.create') }}" class="text-pink-500 font-semibold">Buat pertama →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $posts->links() }}</div>
    </div>
</x-admin-layout>
