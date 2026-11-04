<div class="min-h-[100dvh] flex items-center justify-center min-[480px]:py-6">
    <main class="device" x-data="quiz" wire:ignore>

        <div class="sky" aria-hidden="true" x-html="t.scenery"></div>

        {{-- ===================== QUIZ ===================== --}}
        <section class="screen" data-screen="quiz" aria-label="თამაში"
                 x-show="screen === 'quiz'"
                 x-transition:enter="screen-anim" x-transition:enter-start="screen-from" x-transition:enter-end="screen-to"
                 x-transition:leave="screen-anim-out" x-transition:leave-start="screen-to" x-transition:leave-end="screen-out">

            <div class="quiz-top">
                <button type="button" class="btn btn-paper !min-h-[56px] !w-[56px] !p-0" :aria-label="t.words.leave" @click="leaveQuiz()">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
                <div class="flight" role="progressbar" :aria-label="t.words.progress"
                     aria-valuemin="0" :aria-valuemax="total" :aria-valuenow="index + (checked ? 1 : 0)"
                     :aria-valuetext="`${index + (checked ? 1 : 0)} / ${total} კითხვა შესრულებულია`">
                    <div class="flight-track"></div>
                    <div class="flight-trail" :style="`width: calc((100% - 44px) * ${progress})`"></div>
                    <svg class="flight-end start" aria-hidden="true"><use href="#t-start"></use></svg>
                    <svg class="flight-end end" aria-hidden="true"><use href="#t-end"></use></svg>
                    <div class="flight-traveller" aria-hidden="true" :style="`left: calc(22px + (100% - 44px) * ${progress}); --progress: ${progress}`">
                        <svg><use href="#t-traveller"></use></svg>
                    </div>
                </div>
            </div>
            <p class="q-meta"><span x-text="q.game"></span>, კითხვა <span x-text="index + 1"></span> / <span x-text="total"></span></p>

            <div class="screen-body pt-3">
                <template x-for="k in [qKey]" :key="k">
                    <div class="q-in grid gap-5 pb-3">
                        <h1 id="q-prompt" data-focus tabindex="-1" class="q-prompt"
                            x-text="q.type === 'truefalse' ? 'მართალია თუ არასწორია?' : q.prompt"></h1>

                        {{-- Picture --}}
                        <template x-if="q.type === 'picture'">
                            <div class="picture-frame" :class="{ 'is-shadow': q.shadow && !checked }" role="img" :aria-label="q.alt">
                                <template x-if="q.picture"><svg aria-hidden="true"><use :href="'#' + q.picture"></use></svg></template>
                                <template x-if="!q.picture && q.image"><img :src="`/storage/${q.image}`" alt="" class="h-full w-full object-contain"></template>
                            </div>
                        </template>

                        {{-- True / false statement --}}
                        <template x-if="q.type === 'truefalse'">
                            <p class="statement" x-text="q.prompt"></p>
                        </template>

                        {{-- Riddle clues --}}
                        <template x-if="q.type === 'riddle'">
                            <div class="grid gap-3">
                                <ol class="clue-card" tabindex="-1" aria-label="მინიშნებები">
                                    <template x-for="(c, i) in (q.clues || [])" :key="i">
                                        <li class="q-in"><span class="clue-num" x-text="`მინიშნება ${i + 1}:`"></span> <span x-text="c"></span></li>
                                    </template>
                                </ol>
                                <button type="button" class="btn btn-paper w-full"
                                        x-show="(q.clues || []).length < q.clue_total && !checked"
                                        @click="showClue()" x-text="t.words.clue"></button>
                            </div>
                        </template>

                        {{-- Choice cards: multiple choice, picture, true/false, riddle --}}
                        <template x-if="isChoice">
                            <div class="answer-grid" role="group" aria-labelledby="q-prompt"
                                 :class="{ 'is-tf': q.type === 'truefalse', 'is-picture': q.type === 'picture' || q.type === 'riddle' }">
                                <template x-for="(opt, i) in options" :key="i">
                                    <button type="button" class="answer-card"
                                            :data-accent="opt.accent"
                                            :class="cardClass(i)"
                                            :aria-pressed="(picked === i).toString()"
                                            :aria-disabled="checked.toString()"
                                            :aria-label="cardLabel(i)"
                                            @click="pick(i)">
                                        <span class="picked-tick" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7"/></svg>
                                        </span>
                                        <span class="card-inner" aria-hidden="true">
                                            <span class="card-face card-front">
                                                <template x-if="opt.icon">
                                                    <svg class="opt-icon"><use :href="'#' + opt.icon"></use></svg>
                                                </template>
                                                <template x-if="!opt.icon">
                                                    <span class="planet-badge">
                                                        <span class="badge-art" x-html="t.badge(opt.accent)"></span>
                                                        <span class="letter" x-text="letter(i)"></span>
                                                    </span>
                                                </template>
                                                <span class="answer-text" x-text="opt.label"></span>
                                            </span>
                                            <span class="card-face card-back" :class="i === answerIndex ? 'is-right' : 'is-miss'">
                                                <span class="verdict">
                                                    <template x-if="i === answerIndex">
                                                        <svg width="22" height="22" viewBox="0 0 64 64"><use href="#i-star"></use></svg>
                                                    </template>
                                                    <template x-if="i !== answerIndex">
                                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E2346" stroke-width="2.6" stroke-linecap="round"><circle cx="12" cy="12" r="9" fill="#FFF8EC"/><path d="M8.5 15c1.2.8 2.3.8 3.5 0s2.3-.8 3.5 0"/><circle cx="9" cy="10" r=".6" fill="#1E2346"/><circle cx="15" cy="10" r=".6" fill="#1E2346"/></svg>
                                                    </template>
                                                    <span x-text="i === answerIndex ? 'სწორია!' : 'თითქმის'"></span>
                                                </span>
                                                <span class="answer-text" x-text="opt.label"></span>
                                            </span>
                                        </span>
                                        <template x-if="checked && i === answerIndex && correct">
                                            <span class="burst" aria-hidden="true">
                                                <template x-for="n in 5" :key="n">
                                                    <span><svg viewBox="-11 -11 22 22" width="18" height="18"><use href="#t-spark" fill="currentColor"></use></svg></span>
                                                </template>
                                            </span>
                                        </template>
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- Fill the gap --}}
                        <template x-if="q.type === 'gap'">
                            <div class="grid gap-6">
                                <p class="sentence">
                                    <span x-text="q.before"></span>
                                    <button type="button" class="gap" :class="{ 'is-filled': gapWord !== null }"
                                            :aria-label="gapLabel" :aria-disabled="checked.toString()" @click="clearGap()">
                                        <template x-if="gapWord !== null">
                                            <span class="gap-chip" :class="{ 'is-right': checked && correct, 'is-miss': checked && !correct }" x-text="q.words[gapWord]"></span>
                                        </template>
                                    </button>
                                    <span x-text="q.after"></span>
                                </p>
                                <div class="word-tray" role="group" aria-label="სიტყვები">
                                    <template x-for="(w, i) in q.words" :key="i">
                                        <button type="button" class="word-tile" :class="{ 'is-used': gapWord === i }"
                                                :disabled="gapWord === i || checked"
                                                :aria-label="gapWord === i ? `${w}, ჩასმულია` : `ჩასვი: ${w}`"
                                                @click="placeWord(i, $event)" x-text="w"></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Match pairs --}}
                        <template x-if="q.type === 'match'">
                            <div class="match-board">
                                <template x-for="l in lines" :key="l.key">
                                    <svg class="match-lines" aria-hidden="true">
                                        <path :d="l.d" pathLength="1"></path>
                                        <circle :cx="l.x1" :cy="l.y1" r="6"></circle>
                                        <circle :cx="l.x2" :cy="l.y2" r="6"></circle>
                                    </svg>
                                </template>
                                <div class="match-col" role="group" aria-label="მარცხენა მხარე">
                                    <template x-for="(text, i) in q.left" :key="'L' + i">
                                        <button type="button" class="match-item" :data-m="'L' + i"
                                                :class="{ 'is-sel': isSel('L', i), 'is-matched': isMatched('L', i), 'is-wrong': isWrong('L', i) }"
                                                :aria-pressed="isSel('L', i).toString()" :aria-label="matchLabel('L', i)"
                                                @click="tapMatch('L', i)" x-text="text"></button>
                                    </template>
                                </div>
                                <div class="match-col" role="group" aria-label="მარჯვენა მხარე">
                                    <template x-for="(text, pos) in q.right" :key="'R' + pos">
                                        <button type="button" class="match-item" :data-m="'R' + pos"
                                                :class="{ 'is-sel': isSel('R', pos), 'is-matched': isMatched('R', pos), 'is-wrong': isWrong('R', pos) }"
                                                :aria-pressed="isSel('R', pos).toString()" :aria-label="matchLabel('R', pos)"
                                                @click="tapMatch('R', pos)" x-text="text"></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Label the picture --}}
                        <template x-if="q.type === 'label'">
                            <div class="grid gap-5">
                                <div class="picture-frame is-label" role="img" :aria-label="q.alt">
                                    <div class="label-pic">
                                        <template x-if="q.picture"><svg aria-hidden="true"><use :href="'#' + q.picture"></use></svg></template>
                                        <template x-if="!q.picture && q.image"><img :src="`/storage/${q.image}`" alt="" class="h-full w-full object-contain"></template>
                                        <template x-for="(s, i) in (q.dots || [])" :key="i">
                                            <span class="label-dot" aria-hidden="true"
                                                  x-show="i <= labelStep"
                                                  :class="{ 'is-now': i === labelStep, 'is-done': i < labelStep }"
                                                  :style="`left: ${s.x}%; top: ${s.y}%`"></span>
                                        </template>
                                    </div>
                                </div>
                                <div class="label-grid" role="group" aria-label="ნაწილების სახელები">
                                    <template x-for="(name, pos) in (q.names || [])" :key="pos">
                                        <button type="button" class="match-item"
                                                :class="{ 'is-matched': labelDone(pos), 'is-wrong': labelWrong === pos }"
                                                :aria-disabled="(checked || labelDone(pos)).toString()" :aria-label="labelAria(pos)"
                                                @click="tapLabel(pos)" x-text="name"></button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Word search --}}
                        <template x-if="q.type === 'wordsearch'">
                            <div class="grid gap-5">
                                <div class="ws-grid" role="group" aria-label="ასოების ბადე" :style="`--ws-n: ${wsSize}`">
                                    <template x-for="(ch, cell) in wsLetters" :key="cell">
                                        <button type="button" class="ws-cell"
                                                :class="{ 'is-sel': wsStart === cell, 'is-found': wsFound(cell), 'is-wrong': wsWrong.includes(cell) }"
                                                :aria-pressed="(wsStart === cell).toString()" :aria-disabled="checked.toString()"
                                                :aria-label="wsAria(cell)" @click="tapCell(cell)" x-text="ch"></button>
                                    </template>
                                </div>
                                <ul class="word-tray" aria-label="საპოვნელი სიტყვები">
                                    <template x-for="w in q.words" :key="w">
                                        <li class="gap-chip" :class="{ 'is-right': wsFoundWords.includes(w) }">
                                            <span x-show="wsFoundWords.includes(w)" aria-hidden="true">✓</span>
                                            <span x-text="w"></span>
                                            <span class="sr-only" x-text="wsFoundWords.includes(w) ? ', ნაპოვნია' : ', ჯერ არ არის ნაპოვნი'"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div class="screen-foot">
                <div class="feedback">
                    <div class="mascot" :class="'is-' + mood" x-html="t.mascot"></div>
                    <p role="status" aria-live="polite" x-text="feedback"></p>
                </div>
                <button type="button" class="btn btn-flame w-full" :disabled="!canCheck" @click="check()" x-text="buttonLabel">შემოწმება</button>
            </div>
        </section>

        {{-- ===================== RESULTS ===================== --}}
        <section class="screen" data-screen="results" aria-label="შედეგები"
                 x-show="screen === 'results'"
                 x-transition:enter="screen-anim" x-transition:enter-start="screen-from" x-transition:enter-end="screen-to"
                 x-transition:leave="screen-anim-out" x-transition:leave-start="screen-to" x-transition:leave-end="screen-out">
            <div class="quiz-top">
                <a href="{{ route('home') }}" class="btn btn-paper !min-h-[56px] !w-[56px] !p-0" aria-label="მთავარ გვერდზე">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 11.5 12 5l8 6.5M6.5 10v9h11v-9"/></svg>
                </a>
            </div>
            <div class="screen-body">
                <template x-if="screen === 'results'">
                    <div class="grid content-start gap-5 text-center pt-2">
                        <h1 data-focus tabindex="-1" class="font-head font-extrabold text-[2.25rem] leading-tight text-onbg" x-text="resultTitle"></h1>
                        <div class="result-stars" role="img" :aria-label="`${stars} ვარსკვლავი სამიდან`">
                            <template x-for="n in 3" :key="n">
                                <span class="result-star" :class="n <= stars ? 'is-on' : 'is-off'">
                                    <svg viewBox="0 0 64 64"><use :href="n <= stars ? '#i-star' : '#i-star-empty'"></use></svg>
                                </span>
                            </template>
                        </div>
                        <div class="result-card mt-16">
                            <div class="mascot" :class="'is-' + resultMood" x-html="t.mascot"></div>
                            <p class="result-score"><span x-text="s.correct"></span> / <span x-text="s.total"></span> სწორი</p>
                            <p class="font-semibold text-ink-soft" x-text="`ქულა: ${s.score} · დრო: ${durationText} · სიზუსტე: ${s.accuracy}%`"></p>
                            <p class="font-semibold text-ink-soft" x-text="resultMessage"></p>

                            <template x-if="!$wire.playerName && nameState !== 'saved'">
                                <form class="name-form" @submit.prevent="saveName()">
                                    <label for="player-name" class="font-bold text-ink">როგორ გქვია? (მაქსიმუმ 9 ასო)</label>
                                    <input id="player-name" class="name-input" type="text" maxlength="9" autocomplete="off"
                                           x-model="name" aria-describedby="name-note">
                                    <p id="name-note" class="font-semibold text-ink-soft" role="status" x-text="nameState && nameState !== 'saved' ? nameState : 'სახელით შევინახავთ შენს პროგრესს.'"></p>
                                    <button type="submit" class="btn btn-paper w-full" :disabled="busy || !name.trim()">შენახვა</button>
                                </form>
                            </template>
                            <template x-if="$wire.playerName">
                                <p class="font-bold text-ink" role="status" x-text="`პროგრესი შენახულია: ${$wire.playerName}`"></p>
                            </template>
                        </div>
                        <p class="font-bold text-onbg-soft">
                            ზედიზედ <span class="text-hi" x-text="`${s.streak} დღე`"></span> თამაშობ.
                        </p>
                    </div>
                </template>
            </div>
            <div class="screen-foot">
                <button type="button" class="btn btn-paper w-full" @click="$wire.playAgain()">ისევ სცადე</button>
                <button type="button" class="btn btn-flame w-full" @click="$wire.playNext()" x-text="t.words.next"></button>
                <p class="text-center font-semibold text-onbg-soft -mt-1">შემდეგი: <span x-text="`${s.game} · დონე ${s.level}`"></span></p>
            </div>
        </section>

        {{-- ===================== Celebration (drawn by the theme) ===================== --}}
        <template x-if="blasting">
            <div class="blast" aria-hidden="true" x-html="t.celebration"></div>
        </template>
    </main>
</div>
