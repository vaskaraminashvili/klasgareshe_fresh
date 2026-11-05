<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="{ t: window.KIDZIO_THEME }">
        <div class="sky" aria-hidden="true" wire:ignore x-html="t.scenery"></div>

        <form class="screen" wire:submit="save" novalidate aria-label="კლასის არჩევა">
            <div class="screen-body pt-7 grid content-start gap-6">
                <div>
                    <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.25rem] leading-none text-onbg">რომელ კლასში ხარ?</h1>
                    <p class="mt-2 font-semibold text-onbg-soft">მხოლოდ შენი კლასის კითხვებს მიიღებ.</p>
                </div>

                <div class="grid gap-2">
                    <div class="chip-row" role="group" aria-label="კლასი">
                        @foreach (range(1, 12) as $g)
                            <button type="button" class="chip" aria-pressed="{{ $grade === $g ? 'true' : 'false' }}"
                                    wire:click="$set('grade', {{ $g }})">{{ $g }}</button>
                        @endforeach
                    </div>
                    @error('grade') <p class="field-error" role="alert">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-3" aria-live="polite">
                    @if ($grade)
                        <h2 class="font-head font-extrabold text-2xl text-onbg">{{ $grade }} კლასის საგნები</h2>
                        <ul class="chip-row">
                            @foreach ($subjects as $subject)
                                <li class="chip inline-flex items-center">{{ $subject }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="font-semibold text-onbg-soft">აირჩიე კლასი და ნახავ, რა საგნები გელოდება.</p>
                    @endif
                </div>
            </div>

            <div class="screen-foot">
                <button type="submit" class="btn btn-flame w-full" wire:loading.attr="disabled">პროფილის განახლება</button>
            </div>
        </form>
    </main>
</div>
