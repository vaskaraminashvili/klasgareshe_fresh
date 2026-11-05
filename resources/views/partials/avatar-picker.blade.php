{{-- Needs the component's public `$avatar`; click again on the chosen one to clear it. --}}
<div class="grid gap-2">
    <p class="field-label" id="avatar-label">ფიგურა</p>
    <div class="avatar-grid" role="group" aria-labelledby="avatar-label">
        @foreach ($avatars as $key)
            <button type="button" class="avatar-pick" aria-pressed="{{ $avatar === $key ? 'true' : 'false' }}"
                    aria-label="ფიგურა {{ $loop->iteration }}"
                    wire:click="$set('avatar', {{ $avatar === $key ? 'null' : "'".$key."'" }})">
                @include('partials.avatar', ['key' => $key, 'label' => null])
            </button>
        @endforeach
    </div>
</div>
