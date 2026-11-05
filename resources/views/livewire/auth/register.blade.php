<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="{ t: window.KIDZIO_THEME }">
        <div class="sky" aria-hidden="true" wire:ignore x-html="t.scenery"></div>

        <form class="screen" wire:submit="register" novalidate aria-label="რეგისტრაცია">
            <div class="screen-body pt-7 grid content-start gap-5">
                <div class="home-hero">
                    <div>
                        <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.5rem] leading-none text-onbg">შემოგვიერთდი!</h1>
                        <p class="mt-2 font-semibold text-onbg-soft">შექმენი შენი ანგარიში და შეინახე შედეგები.</p>
                    </div>
                    <div class="mascot is-wave" wire:ignore x-html="t.mascot"></div>
                </div>

                <label class="field">
                    <span class="field-label">სახელი</span>
                    <input type="text" wire:model="name" autocomplete="given-name" maxlength="60" required>
                    @error('name') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </label>

                <label class="field">
                    <span class="field-label">მეტსახელი</span>
                    <input type="text" wire:model="username" autocomplete="username" autocapitalize="none" maxlength="30" required>
                    @error('username') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </label>

                <label class="field">
                    <span class="field-label">ელფოსტა</span>
                    <input type="email" wire:model="email" autocomplete="email" inputmode="email" required>
                    @error('email') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </label>

                <div class="field" x-data="{ show: false }">
                    <label class="field-label" for="register-password">პაროლი</label>
                    <div class="field-row">
                        <input id="register-password" :type="show ? 'text' : 'password'" wire:model="password" autocomplete="new-password" minlength="8" required>
                        <button type="button" class="btn btn-paper field-toggle" :aria-pressed="show" @click="show = !show"
                                x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
                    </div>
                    @error('password') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </div>

                <div class="grid gap-2">
                    <p class="field-label" id="gender-label">ვინ ხარ?</p>
                    <div class="chip-row" role="group" aria-labelledby="gender-label">
                        @foreach (\App\Enums\Gender::cases() as $option)
                            <button type="button" class="chip" aria-pressed="{{ $gender === $option->value ? 'true' : 'false' }}"
                                    wire:click="$set('gender', '{{ $option->value }}')">{{ $option->label() }}</button>
                        @endforeach
                    </div>
                    @error('gender') <p class="field-error" role="alert">{{ $message }}</p> @enderror
                </div>

                @include('partials.grade-picker')
                @include('partials.avatar-picker')
            </div>

            <div class="screen-foot">
                <a href="{{ route('login') }}" class="btn btn-paper w-full">უკვე მაქვს ანგარიში</a>
                <button type="submit" class="btn btn-flame w-full" wire:loading.attr="disabled">რეგისტრაცია</button>
            </div>
        </form>
    </main>
</div>
