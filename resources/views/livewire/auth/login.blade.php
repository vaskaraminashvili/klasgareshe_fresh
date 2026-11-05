<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="{ t: window.KIDZIO_THEME }">
        <div class="sky" aria-hidden="true" wire:ignore x-html="t.scenery"></div>

        <form class="screen" wire:submit="authenticate" novalidate aria-label="შესვლა">
            <div class="screen-body pt-7 grid content-start gap-5">
                <div class="home-hero">
                    <div>
                        <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.5rem] leading-none text-onbg">კეთილი დაბრუნება!</h1>
                        <p class="mt-2 font-semibold text-onbg-soft">შედი და გააგრძელე თამაში.</p>
                    </div>
                    <div class="mascot is-wave" wire:ignore x-html="t.mascot"></div>
                </div>

                <label class="field">
                    <span class="field-label">მეტსახელი ან ელფოსტა</span>
                    <input type="text" wire:model="login" autocomplete="username" autocapitalize="none" required>
                    @error('login') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </label>

                <div class="field" x-data="{ show: false }">
                    <label class="field-label" for="login-password">პაროლი</label>
                    <div class="field-row">
                        <input id="login-password" :type="show ? 'text' : 'password'" wire:model="password" autocomplete="current-password" required>
                        <button type="button" class="btn btn-paper field-toggle" :aria-pressed="show" @click="show = !show"
                                x-text="show ? 'დამალვა' : 'ჩვენება'"></button>
                    </div>
                    @error('password') <span class="field-error" role="alert">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="screen-foot">
                <a href="{{ route('register') }}" class="btn btn-paper w-full">ახალი ანგარიში</a>
                <button type="submit" class="btn btn-flame w-full" wire:loading.attr="disabled">შესვლა</button>
            </div>
        </form>
    </main>
</div>
