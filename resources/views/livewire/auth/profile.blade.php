<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="{ t: window.KIDZIO_THEME }">
        <div class="sky" aria-hidden="true" wire:ignore x-html="t.scenery"></div>

        <form class="screen" wire:submit="save" novalidate aria-label="პროფილი">
            <div class="screen-body pt-7 grid content-start gap-5">
                <div class="profile-hero">
                    @include('partials.avatar', ['key' => $avatar, 'label' => $avatar ? null : $user->initials()])
                    <div class="min-w-0">
                        <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.25rem] leading-none text-onbg truncate">{{ $user->name }}</h1>
                        <p class="mt-1 font-bold text-onbg-soft truncate">{{ '@'.$user->username }}</p>
                    </div>
                </div>

                <label class="field">
                    <span class="field-label">სახელი</span>
                    <input type="text" wire:model="name" autocomplete="given-name" maxlength="60" required>
                    @error('name') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </label>

                @include('partials.grade-picker')
                @include('partials.avatar-picker')

                @if ($saved)
                    <p class="feedback" role="status">შენახულია!</p>
                @endif
            </div>

            <div class="screen-foot">
                <a href="{{ route('home') }}" class="btn btn-paper w-full">თამაშზე დაბრუნება</a>                <button type="submit" class="btn btn-flame w-full" wire:loading.attr="disabled">შენახვა</button>
                <button type="button" class="font-head font-bold text-onbg-soft underline min-h-[44px]" wire:click="logout">გასვლა</button>
            </div>
        </form>
    </main>
</div>
