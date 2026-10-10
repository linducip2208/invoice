<x-admin-layout title="Plans">
    <div class="flex justify-between items-center mb-4">
        <p class="text-sm text-purple-500">{{ $plans->count() }} plans</p>
        <a href="{{ route('admin.plans.create') }}" class="btn-unicorn">+ New Plan</a>
    </div>

    <div class="card-unicorn">
        <table class="table-unicorn">
            <thead>
                <tr><th>Slug</th><th>Name</th><th>IDR/mo</th><th>USD/mo</th><th>Public</th><th>Subs</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($plans as $p)
                    <tr>
                        <td class="font-mono text-xs">{{ $p->slug }}</td>
                        <td class="font-bold">{{ $p->name }}</td>
                        <td>Rp{{ number_format($p->price_idr_monthly, 0, ',', '.') }}</td>
                        <td>${{ number_format($p->price_usd_monthly, 2) }}</td>
                        <td>{{ $p->is_public ? '✓' : '✗' }}</td>
                        <td>{{ $p->subscriptions()->count() }}</td>
                        <td>
                            <a href="{{ route('admin.plans.edit', $p) }}" class="text-purple-600 hover:text-pink-500 font-semibold text-sm">Edit</a>
                            @if($p->slug !== 'free')
                            <form method="POST" action="{{ route('admin.plans.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus plan?');">
                                @csrf @method('DELETE')
                                <button class="text-rose-500 hover:text-rose-700 font-semibold text-sm ml-2">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
