<x-tabler.layout :title="__('Blog Categories')" :pretitle="__('Superadmin')">
    <x-slot name="actions">
        <a href="{{ route('admin.blog.index') }}" class="btn">
            <x-ti name="arrow-left" /> {{ __('Articles') }}
        </a>
    </x-slot>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">{{ __('New Category') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blog.categories.store') }}">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <x-tabler.field :label="__('Name')" name="name" required>
                            <input type="text" id="name" name="name" required placeholder="{{ __('Category name…') }}"
                                class="form-control @error('name') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-5">
                        <x-tabler.field :label="__('Description')" name="description">
                            <input type="text" id="description" name="description" placeholder="{{ __('Short description…') }}"
                                class="form-control @error('description') is-invalid @enderror">
                        </x-tabler.field>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <x-ti name="plus" /> {{ __('Add') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td class="fw-semibold">{{ $cat->name }}</td>
                            <td><code>{{ $cat->slug }}</code></td>
                            <td class="text-muted small">{{ Str::limit($cat->description, 60) }}</td>
                            <td class="text-end text-nowrap">
                                <button type="button" onclick="document.getElementById('editCat{{ $cat->id }}').classList.toggle('d-none')" class="btn btn-sm btn-outline-warning">{{ __('Edit') }}</button>
                                <x-tabler.delete-button :action="route('admin.blog.categories.destroy', $cat)"
                                    :confirm="__('Delete this category?')" :title="__('Delete')" />
                            </td>
                        </tr>
                        <tr id="editCat{{ $cat->id }}" class="d-none table-active">
                            <td colspan="4">
                                <form method="POST" action="{{ route('admin.blog.categories.update', $cat) }}">
                                    @csrf @method('PUT')
                                    <div class="row g-2 align-items-end p-2">
                                        <div class="col-md-4">
                                            <x-tabler.field :label="__('Name')" name="name" required>
                                                <input type="text" id="edit-name-{{ $cat->id }}" name="name" value="{{ $cat->name }}" required class="form-control">
                                            </x-tabler.field>
                                        </div>
                                        <div class="col-md-5">
                                            <x-tabler.field :label="__('Description')" name="description">
                                                <input type="text" id="edit-description-{{ $cat->id }}" name="description" value="{{ $cat->description }}" class="form-control">
                                            </x-tabler.field>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="btn-list justify-content-end">
                                                <button type="button" onclick="this.closest('tr').classList.add('d-none')" class="btn btn-sm">{{ __('Cancel') }}</button>
                                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-tabler.empty icon="file-text" :title="__('No categories yet')"
                                    :body="__('Create your first category above.')" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-tabler.layout>
