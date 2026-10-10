<x-admin-layout title="Kategori Blog">
    <div class="max-w-3xl mx-auto">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h2 class="font-display font-bold text-xl text-purple-900">📂 Kategori Blog</h2>
            <a href="{{ route('admin.blog.index') }}" class="text-sm text-purple-500 hover:text-pink-500">← Artikel</a>
        </div>

        {{-- Add category --}}
        <div class="card-unicorn mb-6">
            <h4 class="font-bold text-purple-900 mb-3">+ Kategori Baru</h4>
            <form method="POST" action="{{ route('admin.blog.categories.store') }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <x-input-label :value="__('Nama')" />
                    <x-text-input name="name" class="block mt-1 w-full" required placeholder="Nama kategori..." />
                </div>
                <div class="flex-1 min-w-[200px]">
                    <x-input-label :value="__('Deskripsi')" />
                    <x-text-input name="description" class="block mt-1 w-full" placeholder="Deskripsi singkat..." />
                </div>
                <button type="submit" class="btn-unicorn text-xs">+ Tambah</button>
            </form>
        </div>

        {{-- Categories list --}}
        <div class="card-unicorn p-0 overflow-hidden">
            <div class="rainbow-bar"></div>
            <table class="table-unicorn">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td class="font-bold text-purple-900">{{ $cat->name }}</td>
                            <td class="text-purple-400 font-mono text-xs">{{ $cat->slug }}</td>
                            <td class="text-purple-500 text-sm">{{ Str::limit($cat->description, 60) }}</td>
                            <td class="text-right space-x-2">
                                <button onclick="document.getElementById('editCat{{ $cat->id }}').classList.toggle('hidden')" class="text-amber-600 hover:text-amber-700 font-semibold text-sm">Edit</button>
                                <form action="{{ route('admin.blog.categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <tr id="editCat{{ $cat->id }}" class="hidden bg-purple-50/50">
                            <td colspan="4">
                                <form method="POST" action="{{ route('admin.blog.categories.update', $cat) }}" class="flex flex-wrap items-end gap-3 p-3">
                                    @csrf @method('PUT')
                                    <div class="flex-1 min-w-[150px]">
                                        <x-input-label :value="__('Nama')" />
                                        <x-text-input name="name" class="block mt-1 w-full" :value="$cat->name" required />
                                    </div>
                                    <div class="flex-1 min-w-[200px]">
                                        <x-input-label :value="__('Deskripsi')" />
                                        <x-text-input name="description" class="block mt-1 w-full" :value="$cat->description" />
                                    </div>
                                    <button type="submit" class="btn-unicorn text-xs">Simpan</button>
                                    <button type="button" onclick="this.closest('tr').classList.add('hidden')" class="btn-unicorn-secondary text-xs">Batal</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-purple-400">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
