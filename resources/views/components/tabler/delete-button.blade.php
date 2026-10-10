@props(['action' => '#', 'label' => null, 'confirm' => null, 'title' => null])
<form method="POST" action="{{ $action }}" class="d-inline"
    onsubmit="return confirm({{ Js::from($confirm ?? __('Are you sure?')) }});">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-outline-danger btn-sm" @if($title) title="{{ $title }}" @endif>
        <x-ti name="trash" />
        {{ $label }}
    </button>
</form>
