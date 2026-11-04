<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="home(@js($config))" wire:ignore>

        <div class="sky" aria-hidden="true" x-html="t.scenery"></div>

        <section class="screen" data-screen="home" aria-label="მთავარი">
            <div class="screen-body pt-7 grid content-start gap-5">
                <div class="home-hero">
                    <div>
                        <p class="font-head font-bold text-xl text-onbg-soft" x-text="greeting + ','"></p>
                        <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.75rem] leading-none text-onbg" x-text="kidName + '!'"></h1>
                        <p class="mt-2 font-semibold text-onbg-soft" x-text="t.words.tagline"></p>
                    </div>
                    <div class="mascot is-wave" x-html="t.mascot"></div>
                </div>

                {{-- Streak strip hidden for now; restore this block to bring it back.
                <div class="streak" role="group" :aria-label="`${streak} დღე ზედიზედ. ${weekLabel}`">
                    <div aria-hidden="true">
                        <p class="font-head font-extrabold text-[1.75rem] leading-none text-hi" x-text="`${streak} დღე`"></p>
                        <p class="font-bold text-onbg-soft">ზედიზედ</p>
                    </div>
                    <div class="streak-days" aria-hidden="true">
                        <template x-for="(d, i) in week" :key="i">
                            <span class="streak-day" :class="{ 'is-today': d.today }">
                                <svg><use :href="d.lit ? '#i-star' : '#i-star-empty'"></use></svg>
                                <span x-text="d.label"></span>
                            </span>
                        </template>
                    </div>
                </div>
                --}}

                <div class="level-slider">
                    <div class="flex items-baseline justify-between gap-3">
                        <h2 class="font-head font-extrabold text-2xl text-onbg">დონე <span class="text-hi" x-text="level"></span></h2>
                        <p class="font-semibold text-onbg-soft text-right" x-text="levelHint"></p>
                    </div>
                    <input type="range" min="1" max="10" step="1" x-model.number="level" @change="setLevel(level)"
                           aria-label="სირთულის დონე" :aria-valuetext="`დონე ${level}: ${levelHint}`">
                    <div class="level-ticks" aria-hidden="true">
                        <template x-for="n in 10" :key="n"><span :class="{ 'is-on': n === level }" x-text="n"></span></template>
                    </div>
                </div>

                <h2 class="font-head font-extrabold text-2xl text-onbg -mb-1" x-text="t.words.pick"></h2>
                <div class="subject-grid">
                    <template x-for="g in games" :key="g.type">
                        <button type="button" class="subject-tile" :data-accent="g.accent" :disabled="starting"
                                :aria-label="`${g.name}, დონე ${level}`" @click="start(g.type)">
                            <span class="subject-planet" aria-hidden="true" x-html="t.tile(g.accent, g.icon)"></span>
                            <span class="subject-name" aria-hidden="true" x-text="g.name"></span>
                            <span class="subject-meta" aria-hidden="true" x-text="`დონე ${level}`"></span>
                        </button>
                    </template>
                </div>
            </div>

            <div class="screen-foot">
                <p class="feedback" role="alert" x-show="error" x-text="error"></p>
                <button type="button" class="btn btn-flame w-full" :disabled="starting" @click="start(null)" x-text="t.words.start"></button>
                <p class="text-center font-semibold text-onbg-soft -mt-1">8 კითხვა, ყველა თამაში</p>
            </div>
        </section>
    </main>
</div>
