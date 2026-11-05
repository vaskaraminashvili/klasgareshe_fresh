<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="{ t: window.KIDZIO_THEME }">
        <div class="sky" aria-hidden="true" wire:ignore x-html="t.scenery"></div>

        <section class="screen" aria-label="რა არის კლასგარეშე">
            <div class="screen-body pt-7 grid content-start gap-6">
                <div class="home-hero">
                    <div>
                        <p class="font-head font-bold text-xl text-onbg-soft">კეთილი იყოს შენი მობრძანება</p>
                        <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.5rem] leading-none text-onbg">კლასგარეშე</h1>
                        <p class="mt-2 font-semibold text-onbg-soft">გაკვეთილები, რომლებიც თამაშს ჰგავს.</p>
                    </div>
                    <div class="mascot is-wave" wire:ignore x-html="t.mascot" aria-hidden="true"></div>
                </div>

                <p class="font-semibold text-lg text-onbg">
                    აქ სასკოლო საგნებს თამაშით იმეორებ. აირჩიე შენი კლასი, უპასუხე მოკლე კითხვებს და ნახე, რამდენი იცი.
                </p>

                <ol class="about-steps" aria-label="როგორ მუშაობს">
                    <li class="about-step">
                        <span class="avatar" data-accent="a1" aria-hidden="true">1</span>
                        <div>
                            <h2 class="font-head font-extrabold text-xl text-onbg">აირჩიე საგანი და კლასი</h2>
                            <p class="font-semibold text-onbg-soft">მიიღებ კითხვებს მხოლოდ იმ თემებზე, რასაც ახლა სწავლობ.</p>
                        </div>
                    </li>
                    <li class="about-step">
                        <span class="avatar" data-accent="a2" aria-hidden="true">2</span>
                        <div>
                            <h2 class="font-head font-extrabold text-xl text-onbg">დააყენე დონე</h2>
                            <p class="font-semibold text-onbg-soft">1-დან 10-მდე. დაიწყე მარტივით და გაზარდე, როცა მოგბეზრდება.</p>
                        </div>
                    </li>
                    <li class="about-step">
                        <span class="avatar" data-accent="a3" aria-hidden="true">3</span>
                        <div>
                            <h2 class="font-head font-extrabold text-xl text-onbg">ითამაშე 8 კითხვა</h2>
                            <p class="font-semibold text-onbg-soft">თითო რაუნდი მოკლეა. ყოველი პასუხის შემდეგ გაიგებ, სწორი იყო თუ არა.</p>
                        </div>
                    </li>
                </ol>

                <div class="grid gap-3">
                    <h2 class="font-head font-extrabold text-2xl text-onbg">8 სახის თამაში</h2>
                    <ul class="chip-row">
                        @foreach ($gameLabels as $label)
                            <li class="chip inline-flex items-center">{{ $label }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="screen-foot">
                <a href="{{ route('register') }}" class="btn btn-flame w-full">რეგისტრაცია</a>
                <a href="{{ route('login') }}" class="btn btn-paper w-full">შესვლა</a>
            </div>
        </section>
    </main>
</div>
