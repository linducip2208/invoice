<x-admin-layout title="Buat Artikel">
    <div class="max-w-4xl mx-auto">
        <div class="card-unicorn">
            <div class="rainbow-bar -mx-6 -mt-6 mb-6 rounded-t-2xl"></div>
            <h3 class="font-display font-bold text-lg text-purple-900 mb-4">📝 Artikel Baru</h3>

            <form method="POST" action="{{ route('admin.blog.store') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-input-label :value="__('Judul *')" />
                        <x-text-input name="title" class="block mt-1 w-full" required :value="old('title')" placeholder="Judul artikel..." />
                        @error('title') <p class="text-sm text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label :value="__('Kategori')" />
                        <select name="category_id" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2">
                            <option value="">— Tanpa Kategori —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Status')" />
                        <select name="is_published" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2">
                            <option value="1">Published</option>
                            <option value="0" @selected(old('is_published', '1') === '0')>Draft</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('Featured Image URL')" />
                        <x-text-input name="featured_image" class="block mt-1 w-full" :value="old('featured_image')" placeholder="https://..." />
                    </div>
                    <div>
                        <x-input-label :value="__('Tanggal Publish')" />
                        <x-text-input name="published_at" type="datetime-local" class="block mt-1 w-full" :value="old('published_at', now()->format('Y-m-d\TH:i'))" />
                    </div>
                    <div>
                        <x-input-label :value="__('Meta Title')" />
                        <x-text-input name="meta_title" class="block mt-1 w-full" :value="old('meta_title')" placeholder="SEO title..." />
                    </div>
                    <div>
                        <x-input-label :value="__('Meta Description')" />
                        <x-text-input name="meta_description" class="block mt-1 w-full" :value="old('meta_description')" placeholder="SEO description..." />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label :value="__('Excerpt')" />
                        <textarea name="excerpt" rows="2" class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2" placeholder="Ringkasan singkat...">{{ old('excerpt') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label :value="__('Konten *')" />
                        <textarea name="content" rows="20" required class="block mt-1 w-full input-unicorn rounded-xl px-4 py-2 font-mono text-sm" placeholder="Tulis konten HTML...">{{ old('content') }}</textarea>
                        @error('content') <p class="text-sm text-rose-500 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-purple-400 mt-1">Gunakan HTML untuk formatting. Contoh: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;strong&gt;, dll.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-purple-100">
                    <a href="{{ route('admin.blog.index') }}" class="btn-unicorn-secondary">Batal</a>
                    <button type="submit" class="btn-unicorn">✦ Simpan Artikel</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
