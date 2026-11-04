/* Kidzio front end. Alpine drives the screens and animations; every answer is checked on the
   server through Livewire ($wire), so the browser never holds a correct answer.
   Words and look come from the active theme (public/themes/<name>/theme.js -> window.KIDZIO_THEME). */

const T = window.KIDZIO_THEME;
const ACCENTS = ['a1', 'a2', 'a3', 'a4'];
const LETTERS = ['ა', 'ბ', 'გ', 'დ'];

const MSG = {
  start: {
    picture: 'კარგად დააკვირდი და აირჩიე პასუხი.',
    truefalse: 'მართალია თუ არასწორია? აირჩიე ერთ-ერთი.',
    gap: 'შეეხე სიტყვას, რომ გამოტოვებულ ადგილას ჩაჯდეს.',
    match: 'შეეხე ბარათს მარცხნივ, შემდეგ მის წყვილს მარჯვნივ.',
    label: 'შეხედე წერტილს და შეეხე ამ ნაწილის სახელს.',
    wordsearch: 'შეეხე სიტყვის პირველ ასოს, შემდეგ ბოლო ასოს.',
    riddle: 'წაიკითხე მინიშნება. გჭირდება დახმარება? მოითხოვე შემდეგი.',
  },
};

const prefersReducedMotion = () =>
  window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function mountTheme() {
  document.getElementById('theme-sprite').innerHTML = T.sprite;
  document.querySelector('meta[name="theme-color"]')?.setAttribute('content', T.themeColor);
}

function levelHint(level) {
  if (level <= 1) return 'ძალიან მარტივი, პატარებისთვის';
  if (level <= 3) return 'ძირითადი ცოდნა';
  if (level <= 5) return 'ცოტა დაფიქრება მოგიწევს';
  if (level <= 7) return 'უფრო რთული კითხვები';
  if (level <= 9) return 'მოწინავე დონე';
  return 'ყველაზე რთული გამოწვევა';
}

function register() {
  mountTheme();

  /* ===================== Home ===================== */
  Alpine.data('home', (cfg) => ({
    t: T,
    kidName: cfg.name,
    streak: cfg.streak,
    lit: cfg.lit, // seven booleans, oldest first, today last
    games: cfg.games,
    level: 1,
    starting: false,
    error: '',

    init() {
      try { this.level = Math.min(10, Math.max(1, Number(localStorage.getItem('kidzio-level')) || 1)); } catch (e) {}
    },

    get greeting() {
      const h = new Date().getHours();
      if (h < 12) return 'დილა მშვიდობისა';
      if (h < 18) return 'გამარჯობა';
      return 'საღამო მშვიდობისა';
    },

    get week() {
      const letters = ['კ', 'ო', 'ს', 'ო', 'ხ', 'პ', 'შ'];
      const names = ['კვირა', 'ორშაბათი', 'სამშაბათი', 'ოთხშაბათი', 'ხუთშაბათი', 'პარასკევი', 'შაბათი'];
      return this.lit.map((lit, i) => {
        const d = new Date();
        d.setDate(d.getDate() - (6 - i));
        return { label: letters[d.getDay()], name: i === 6 ? 'დღეს' : names[d.getDay()], lit, today: i === 6 };
      });
    },

    get weekLabel() {
      const done = this.week.filter((d) => d.lit).map((d) => d.name);
      return done.length ? `ამ კვირაში შესრულებულია: ${done.join(', ')}` : 'ამ კვირაში ჯერ არაფერი შესრულებულა';
    },

    get levelHint() { return levelHint(this.level); },

    setLevel(n) {
      this.level = n;
      try { localStorage.setItem('kidzio-level', n); } catch (e) {}
    },

    async start(type) {
      if (this.starting) return;
      this.starting = true;
      this.error = '';
      const r = await this.$wire.start(type, this.level);
      if (r && r.error) {
        this.error = r.error;
        this.starting = false;
      }
    },
  }));

  /* ===================== Quiz ===================== */
  Alpine.data('quiz', () => ({
    t: T,
    screen: 'quiz',
    qKey: 0,

    // local, per-question UI state (answers are never held here)
    picked: null,
    gapWord: null,
    sel: null,
    wrong: [],
    lines: [],
    wsStart: null,
    wsWrong: [],
    labelWrong: null,
    reveal: null,
    checked: false,
    correct: false,
    busy: false,
    feedback: '',
    mood: 'idle',
    blasting: false,
    name: '',
    nameState: '',

    init() {
      window.addEventListener('resize', () => this.drawLines());
      this.name = this.$wire.playerName || '';
      if (this.$wire.finished) this.screen = 'results';
      else this.resetQuestion();
    },

    /* ---------- server state ---------- */
    get q() { return this.$wire.question || {}; },
    get total() { return this.$wire.total; },
    get index() { return this.q.position ?? 0; },
    get isLast() { return this.index === this.total - 1; },
    get isChoice() { return ['multiple', 'picture', 'truefalse', 'riddle'].includes(this.q.type); },
    get progress() { return (this.index + (this.checked ? 1 : 0)) / (this.total || 1); },

    /* ---------- navigation ---------- */
    go(screen) {
      this.screen = screen;
      this.$nextTick(() => {
        setTimeout(() => {
          this.$root.querySelector(`[data-screen="${screen}"] [data-focus]`)?.focus();
          this.drawLines();
        }, 280);
      });
    },

    resetQuestion() {
      this.picked = null;
      this.gapWord = null;
      this.sel = null;
      this.wrong = [];
      this.lines = [];
      this.wsStart = null;
      this.wsWrong = [];
      this.labelWrong = null;
      this.reveal = null;
      this.checked = false;
      this.correct = false;
      this.mood = 'idle';
      this.feedback = this.q.type === 'multiple' ? T.words.pickHint : (MSG.start[this.q.type] || '');
      this.qKey++;
      this.$nextTick(() => {
        if (this.screen === 'quiz') this.$root.querySelector('#q-prompt')?.focus();
        this.drawLines(); // a refreshed match question already has pairs
      });
    },

    get canCheck() {
      if (this.checked) return true;
      if (this.busy) return false;
      if (this.isChoice) return this.picked !== null;
      if (this.q.type === 'gap') return this.gapWord !== null;
      return !!this.q.complete;
    },

    get buttonLabel() {
      if (!this.checked) return 'შემოწმება';
      return this.isLast ? 'ვარსკვლავების ნახვა' : 'შემდეგი კითხვა';
    },

    async check() {
      if (!this.canCheck) return;
      if (this.checked) return this.advance();

      this.busy = true;
      const input = this.isChoice ? this.picked : this.q.type === 'gap' ? this.gapWord : null;
      const r = await this.$wire.check(input);
      this.busy = false;
      if (!r || r.error) return;

      this.reveal = r.reveal || {};
      this.correct = r.correct;
      this.checked = true;
      this.feedback = r.feedback || '';
      this.mood = r.correct ? 'happy' : 'shrug';
      if (r.correct) this.celebrate();
    },

    async advance() {
      if (this.busy) return;
      this.busy = true;
      await this.$wire.next();
      this.busy = false;
      if (this.$wire.finished) {
        this.name = this.$wire.playerName || '';
        this.go('results');
        return;
      }
      this.resetQuestion();
    },

    leaveQuiz() { window.location.href = '/'; },

    celebrate() {
      if (prefersReducedMotion()) return;
      this.blasting = false;
      this.$nextTick(() => {
        this.blasting = true;
        setTimeout(() => { this.blasting = false; }, 1500);
      });
    },

    /* ---------- choice: multiple, picture, true/false, riddle ---------- */
    get options() {
      const q = this.q;
      return (q.options || []).map((o, i) => ({
        ...o,
        accent: q.type === 'truefalse' ? (i === 0 ? 'good' : 'miss') : ACCENTS[i % ACCENTS.length],
      }));
    },

    get answerIndex() { return this.reveal?.answer ?? -1; },

    letter(i) { return LETTERS[i]; },

    pick(i) {
      if (this.checked) return;
      this.picked = this.picked === i ? null : i;
      if (this.picked !== null) {
        this.feedback = `აირჩიე: ${this.options[i].label}. როცა მზად იქნები, დააჭირე „შემოწმება“.`;
      }
    },

    cardClass(i) {
      const isAnswer = i === this.answerIndex;
      const isPicked = i === this.picked;
      if (!this.checked) return { 'is-selected': isPicked };
      return {
        'is-revealed': true,
        'is-flipped': isAnswer || isPicked,
        'is-dimmed': !isAnswer && !isPicked,
        'is-wobbling': isPicked && !isAnswer,
      };
    },

    cardLabel(i) {
      const opt = this.options[i];
      const base = this.q.type === 'truefalse' ? opt.label : `${this.letter(i)}: ${opt.label}`;
      if (!this.checked) return base;
      if (i === this.answerIndex) return `${base}. სწორი პასუხი.`;
      if (i === this.picked) return `${base}. შენი არჩევანი, მაგრამ არა სწორი.`;
      return base;
    },

    /* ---------- fill the gap ---------- */
    placeWord(i, ev) {
      if (this.checked) return;
      const from = ev.currentTarget.getBoundingClientRect();
      this.gapWord = i;
      this.feedback = `„${this.q.words[i]}“ ჩაჯდა. შეეხე, რომ ამოიღო, ან დააჭირე „შემოწმება“.`;
      this.$nextTick(() => {
        const chip = this.$root.querySelector('.gap-chip');
        if (!chip || prefersReducedMotion()) return;
        const to = chip.getBoundingClientRect();
        chip.animate(
          [
            { transform: `translate(${from.left - to.left}px, ${from.top - to.top}px) scale(.9)` },
            { transform: 'translate(0, 0) scale(1)' },
          ],
          { duration: 320, easing: 'cubic-bezier(.34, 1.56, .64, 1)' }
        );
      });
    },

    clearGap() {
      if (this.checked || this.gapWord === null) return;
      this.gapWord = null;
      this.feedback = MSG.start.gap;
    },

    get gapLabel() {
      if (this.gapWord === null) return 'გამოტოვებული ადგილი, ცარიელია';
      const w = this.q.words[this.gapWord];
      if (!this.checked) return `გამოტოვებულ ადგილას წერია ${w}. შეეხე, რომ ამოიღო.`;
      return this.correct ? `${w}. სწორია.` : `${w}. არა, სწორი პასუხია ${this.reveal?.answer}.`;
    },

    /* ---------- match pairs ---------- */
    get done() { return this.q.progress?.done || {}; },

    isMatched(side, i) {
      return side === 'L' ? i in this.done : Object.values(this.done).includes(i);
    },
    isSel(side, i) { return this.sel?.side === side && this.sel?.i === i; },
    isWrong(side, i) { return this.wrong.includes(side + i); },
    matchText(side, i) { return side === 'L' ? this.q.left?.[i] : this.q.right?.[i]; },

    matchLabel(side, i) {
      const text = this.matchText(side, i);
      if (this.isMatched(side, i)) return `${text}, შეწყვილებულია`;
      if (this.isSel(side, i)) return `${text}, არჩეულია. ახლა შეეხე მის წყვილს.`;
      return text;
    },

    async tapMatch(side, i) {
      if (this.checked || this.busy || this.isMatched(side, i)) return;
      const sel = this.sel;

      if (!sel || sel.side === side) {
        this.sel = sel && sel.i === i ? null : { side, i };
        if (this.sel) this.feedback = 'ახლა შეეხე მის წყვილს მეორე მხარეს.';
        return;
      }

      const L = side === 'L' ? i : sel.i;
      const R = side === 'R' ? i : sel.i;
      this.sel = null;
      this.busy = true;
      const r = await this.$wire.step({ l: L, r: R });
      this.busy = false;

      if (r.hit) {
        this.feedback = this.q.complete
          ? 'ყველა წყვილი შეუხამე! დააჭირე „შემოწმება“.'
          : `${this.q.left[L]} და ${this.q.right[R]} წყვილია!`;
        this.$nextTick(() => this.drawLines());
      } else {
        this.wrong = ['L' + L, 'R' + R];
        this.feedback = 'ეს ორი წყვილი არ არის. სცადე ისევ!';
        setTimeout(() => { this.wrong = []; }, 650);
      }
    },

    drawLines() {
      if (this.screen !== 'quiz' || this.q.type !== 'match') return;
      const board = this.$root.querySelector('.match-board');
      if (!board) return;
      const b = board.getBoundingClientRect();
      this.lines = Object.entries(this.done).map(([L, R]) => {
        const a = board.querySelector(`[data-m="L${L}"]`)?.getBoundingClientRect();
        const c = board.querySelector(`[data-m="R${R}"]`)?.getBoundingClientRect();
        if (!a || !c) return null;
        const x1 = a.right - b.left - 4, y1 = a.top + a.height / 2 - b.top;
        const x2 = c.left - b.left + 4, y2 = c.top + c.height / 2 - b.top;
        const bend = (x2 - x1) * 0.6;
        return { key: L, x1, y1, x2, y2, d: `M${x1} ${y1} C${x1 + bend} ${y1}, ${x2 - bend} ${y2}, ${x2} ${y2}` };
      }).filter(Boolean);
    },

    /* ---------- label the picture ---------- */
    get labelStep() { return this.q.progress?.step ?? 0; },
    labelDone(pos) { return (this.q.progress?.done || []).includes(pos); },

    labelAria(pos) {
      const name = this.q.names?.[pos] ?? '';
      return this.labelDone(pos) ? `${name}, დასახელებულია` : name;
    },

    async tapLabel(pos) {
      if (this.checked || this.busy || this.labelDone(pos)) return;
      this.busy = true;
      const r = await this.$wire.step({ p: pos });
      this.busy = false;
      if (r.hit) {
        this.feedback = this.q.complete
          ? 'ყველა ნაწილი დაასახელე! დააჭირე „შემოწმება“.'
          : `სწორია, ეს არის ${this.q.names[pos]}! წერტილი გადავიდა. რა არის ეს ნაწილი?`;
      } else {
        this.labelWrong = pos;
        this.feedback = 'ეს არა. შეხედე, სად არის წერტილი და სცადე ისევ.';
        setTimeout(() => { this.labelWrong = null; }, 650);
      }
    },

    /* ---------- word search ---------- */
    // The guards keep a leaving question block from throwing while the next one renders.
    get wsSize() { return this.q.grid?.length ?? 0; },
    get wsLetters() { return (this.q.grid ?? []).join('').split(''); },
    get wsFoundWords() { return this.q.progress?.found || []; },
    wsFound(cell) { return (this.q.progress?.cells || []).includes(cell); },

    wsAria(cell) {
      const n = this.wsSize;
      const base = `${this.wsLetters[cell]}, მწკრივი ${Math.floor(cell / n) + 1}, სვეტი ${cell % n + 1}`;
      if (this.wsStart === cell) return `${base}, პირველი ასო არჩეულია`;
      return this.wsFound(cell) ? `${base}, ნაპოვნი სიტყვის ნაწილია` : base;
    },

    async tapCell(cell) {
      if (this.checked || this.busy) return;
      const start = this.wsStart;
      if (start === null || start === cell) {
        this.wsStart = start === cell ? null : cell;
        if (this.wsStart !== null) this.feedback = `${this.wsLetters[cell]} არჩეულია. ახლა შეეხე სიტყვის ბოლო ასოს.`;
        return;
      }
      this.wsStart = null;
      this.busy = true;
      const r = await this.$wire.step({ a: start, b: cell });
      this.busy = false;
      if (r.hit) {
        this.feedback = this.q.complete
          ? 'ყველა სიტყვა იპოვე! დააჭირე „შემოწმება“.'
          : `იპოვე: ${r.extra.word}!`;
      } else {
        this.wsWrong = [start, cell];
        this.feedback = r.extra.straight
          ? 'ეს არ არის ერთ-ერთი სიტყვა. სცადე ისევ!'
          : 'სიტყვები სწორ ხაზზე წერია. სცადე ისევ!';
        setTimeout(() => { this.wsWrong = []; }, 650);
      }
    },

    /* ---------- riddle ---------- */
    async showClue() {
      if (this.checked || this.busy || (this.q.clues?.length ?? 0) >= this.q.clue_total) return;
      this.busy = true;
      await this.$wire.revealClue();
      this.busy = false;
      const n = this.q.clues.length;
      this.feedback = `მინიშნება ${n}: ${this.q.clues[n - 1]}`;
      // The clue button disappears after the last clue, so keep focus somewhere useful.
      if (n === this.q.clue_total) {
        this.$nextTick(() => this.$root.querySelector('.clue-card')?.focus());
      }
    },

    /* ================= Results ================= */
    get s() { return this.$wire.summary || {}; },
    get stars() { return this.s.stars || 1; },
    get resultTitle() { return T.words.resultTitles[this.stars]; },

    get resultMessage() {
      if (this.stars === 3) return T.words.threeStars;
      if (this.stars === 2) return 'სამ ვარსკვლავს ცოტა აკლდა. კიდევ სცადე?';
      return 'ყოველი ცდა ტვინს აძლიერებს. გინდა ისევ სცადო?';
    },

    get resultMood() { return this.stars >= 2 ? 'happy' : 'shrug'; },

    get durationText() {
      const sec = this.s.duration || 0;
      return `${Math.floor(sec / 60)}:${String(sec % 60).padStart(2, '0')}`;
    },

    async saveName() {
      const value = this.name.trim();
      if (!value || this.busy) return;
      this.busy = true;
      const r = await this.$wire.saveName(value);
      this.busy = false;
      this.nameState = r.ok ? 'saved' : r.error;
    },
  }));
}

if (window.Alpine) register();
else document.addEventListener('alpine:init', register);
