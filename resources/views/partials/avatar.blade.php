{{-- Round avatar. `$key` is a config('avatars') key (null = none yet); `$label` shows initials instead of the placeholder star. --}}
@php($index = $key ? (int) array_search($key, config('avatars'), true) : 0)
<span class="avatar" data-accent="a{{ ($index % 4) + 1 }}" aria-hidden="true">
    @if ($label ?? null)
        <span>{{ $label }}</span>
    @else
        <svg><use href="#i-star"></use></svg>
    @endif
</span>
