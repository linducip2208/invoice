@props(['icon' => 'inbox', 'title' => '', 'body' => '', 'actionUrl' => null, 'actionLabel' => null])
<div class="empty">
    <div class="empty-icon"><x-ti :name="$icon" class="icon icon-lg" /></div>
    @if($title)<p class="empty-title">{{ $title }}</p>@endif
    @if($body)<p class="empty-subtitle text-muted">{{ $body }}</p>@endif
    @if($actionUrl && $actionLabel)
        <div class="empty-action">
            <a href="{{ $actionUrl }}" class="btn btn-primary">{{ $actionLabel }}</a>
        </div>
    @endif
    {{ $slot }}
</div>
