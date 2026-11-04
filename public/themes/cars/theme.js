/* Kidzio theme: Race cars — a sunny race day (bright).
   Mascot: Vroomy the little car. Progress: a car drives from the start light to the chequered flag. */
window.KIDZIO_THEME = {
  id: 'cars',
  name: 'Race cars',
  themeColor: '#CFEAFB',
  preview: { bg: '#CFEAFB', accents: ['#FF6B4A', '#FFC93C', '#43C08A', '#4FA3F7'] },   /* used by themes.html */

  words: {
    tagline: "მზად ხარ დღევანდელი რბოლისთვის?",
    pick: "აირჩიე ტრასა",
    start: "დაიწყე დღევანდელი რბოლა",
    daily: "დღევანდელი რბოლა",
    subject: (name) => `${name} — ტრასა`,
    leave: "დატოვე რბოლა და დაბრუნდი მთავარ გვერდზე",
    progress: "რბოლის პროგრესი",
    next: "შემდეგი რბოლა",
    pickHint: "შეეხე ნიშანს პასუხის ასარჩევად.",
    clue: "სთხოვე ვრუმის მინიშნება",
    resultTitles: ['', "რბოლა დასრულდა!", "შესანიშნავი მართვა!", "ჩემპიონი მძღოლი!"],
    threeStars: "შენ ყველაფერი ზუსტად გააკეთე. ვრუმი საპატიო წრეს აკეთებს!",
  },

  sprite: `<defs>
    <path id="t-spark" d="M0,-11 L3.2,-3.6 11,-3.4 4.9,1.6 7,9.2 0,4.9 -7,9.2 -4.9,1.6 -11,-3.4 -3.2,-3.6Z"/>
    <symbol id="t-start" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round">
        <path d="M32 50v10" stroke-linecap="round"/>
        <rect x="20" y="4" width="24" height="48" rx="9" fill="#3A4354"/>
        <circle cx="32" cy="16" r="6" fill="#5B6476"/>
        <circle cx="32" cy="28" r="6" fill="#5B6476"/>
        <circle cx="32" cy="40" r="6" fill="#43C08A"/>
      </g>
    </symbol>
    <symbol id="t-end" viewBox="0 0 64 64">
      <path d="M14 6v54" stroke="#1E2346" stroke-width="4" stroke-linecap="round"/>
      <path d="M16 8h38v26H16Z" fill="#FFF8EC" stroke="#1E2346" stroke-width="3" stroke-linejoin="round"/>
      <g fill="#1E2346">
        <rect x="16" y="8" width="9.5" height="6.5"/><rect x="35" y="8" width="9.5" height="6.5"/>
        <rect x="25.5" y="14.5" width="9.5" height="6.5"/><rect x="44.5" y="14.5" width="9.5" height="6.5"/>
        <rect x="16" y="21" width="9.5" height="6.5"/><rect x="35" y="21" width="9.5" height="6.5"/>
        <rect x="25.5" y="27.5" width="9.5" height="6.5"/><rect x="44.5" y="27.5" width="9.5" height="6.5"/>
      </g>
    </symbol>
    <symbol id="t-traveller" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round">
        <path d="M6 42v-8c0-4 3-6 7-7l9-2 7-9c2-2 4-3 7-3h8c4 0 7 2 9 6l4 8c3 1 4 3 4 6v9Z" fill="#FF6B4A"/>
        <path d="M30 25l5-7c1-1 2-2 4-2h4c2 0 3 1 4 2l3 7Z" fill="#CFEAFB"/>
        <circle cx="18" cy="44" r="7" fill="#2A2F3A"/><circle cx="18" cy="44" r="2.5" fill="#D9DEE6" stroke="none"/>
        <circle cx="48" cy="44" r="7" fill="#2A2F3A"/><circle cx="48" cy="44" r="2.5" fill="#D9DEE6" stroke="none"/>
        <circle cx="58" cy="34" r="2.4" fill="#FFE38A" stroke-width="2"/>
      </g>
    </symbol>
  </defs>`,

  scenery: `<svg viewBox="0 0 400 860" preserveAspectRatio="xMidYMid slice">
      <g>
        <circle cx="345" cy="78" r="34" fill="#FFE07A"/>
        <g stroke="#FFE07A" stroke-width="6" stroke-linecap="round">
          <path d="M345 26v-10M345 140v-10M293 78h-10M407 78h-10M308 41l-7-7M382 115l7 7M308 115l-7 7M382 41l7-7"/>
        </g>
      </g>
      <path d="M-20 720 Q 80 660 190 700 T 420 680 V 900 H -20Z" fill="#B9E4A8"/>
      <path d="M-20 770 Q 120 720 240 760 T 420 750 V 900 H -20Z" fill="#A3DA92"/>
      <path d="M-20 872 Q 160 826 420 860" fill="none" stroke="#E3E8EE" stroke-width="34" stroke-linecap="round"/>
      <path d="M-20 872 Q 160 826 420 860" fill="none" stroke="#FFFFFF" stroke-width="4" stroke-dasharray="18 16"/>
      <g fill="#8FD18A" stroke="#6FB36B" stroke-width="2">
        <circle cx="60" cy="708" r="14"/><circle cx="330" cy="700" r="12"/>
      </g>
    </svg>
    <span class="cloud ambient" style="top:9%;  animation-duration:60s; animation-delay:-10s"></span>
    <span class="cloud ambient" style="top:22%; animation-duration:80s; animation-delay:-45s; transform: scale(.7)"></span>
    <span class="cloud ambient" style="top:40%; animation-duration:70s; animation-delay:-25s; transform: scale(.85)"></span>`,

  /* Vroomy the car. Hooks: .m-arm-l, .m-arm-r, .m-head (the cabin). */
  mascot: `<svg viewBox="0 0 120 150" aria-hidden="true" focusable="false">
    <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round" stroke-linecap="round">
      <ellipse cx="32" cy="128" rx="13" ry="11" fill="#2A2F3A"/><circle cx="32" cy="128" r="4" fill="#D9DEE6"/>
      <ellipse cx="88" cy="128" rx="13" ry="11" fill="#2A2F3A"/><circle cx="88" cy="128" r="4" fill="#D9DEE6"/>
      <g class="m-arm m-arm-l"><rect x="5" y="88" width="10" height="26" rx="5" fill="#FFF8EC"/><circle cx="10" cy="115" r="7" fill="#FFF8EC"/></g>
      <g class="m-arm m-arm-r"><rect x="105" y="88" width="10" height="26" rx="5" fill="#FFF8EC"/><circle cx="110" cy="115" r="7" fill="#FFF8EC"/></g>
      <g class="m-head">
        <path d="M78 52l6-16" fill="none"/>
        <path d="M84 36l12 3-3 8-11-3z" fill="#FFC93C" stroke-width="2.5"/>
        <path d="M28 88 C30 62 42 50 60 50 C78 50 90 62 92 88 Z" fill="#FF6B4A"/>
        <path d="M37 86 C39 68 47 59 60 59 C73 59 81 68 83 86 Z" fill="#CFEAFB"/>
        <g stroke="none">
          <ellipse cx="51" cy="74" rx="4.2" ry="5.6" fill="#1E2346"/><ellipse cx="69" cy="74" rx="4.2" ry="5.6" fill="#1E2346"/>
          <circle cx="52.5" cy="72" r="1.5" fill="#FFFFFF"/><circle cx="70.5" cy="72" r="1.5" fill="#FFFFFF"/>
        </g>
      </g>
      <rect x="13" y="84" width="94" height="40" rx="18" fill="#FF6B4A"/>
      <rect x="20" y="113" width="80" height="10" rx="5" fill="#D9DEE6"/>
      <circle cx="31" cy="99" r="7.5" fill="#FFE38A"/>
      <circle cx="89" cy="99" r="7.5" fill="#FFE38A"/>
      <path d="M48 99q12 8 24 0" fill="none" stroke-width="2.8"/>
      <circle cx="42" cy="108" r="3.2" fill="#FF9EB5" stroke="none"/>
      <circle cx="78" cy="108" r="3.2" fill="#FF9EB5" stroke="none"/>
    </g>
  </svg>`,

  /* Letter badge: a round road sign. viewBox 58×46. */
  badge: (a) => `<svg viewBox="0 0 58 46">
    <circle cx="29" cy="23" r="21" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6"/>
    <circle cx="29" cy="23" r="14.5" fill="#FFF8EC" stroke="#1E2346" stroke-width="2"/>
  </svg>`,

  /* Subject tile art: a big road sign holding the subject icon. viewBox 76×62. */
  tile: (a, icon) => `<svg viewBox="0 0 76 62">
    <path d="M38 58v4" stroke="#1E2346" stroke-width="4"/>
    <circle cx="38" cy="30" r="28" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6"/>
    <circle cx="38" cy="30" r="20" fill="#FFF8EC" stroke="#1E2346" stroke-width="2"/>
    <use href="#${icon}" x="23" y="15" width="30" height="30"/>
  </svg>`,

  /* Correct answer: the car zooms across, leaving speed lines and dust puffs. */
  celebration: `
    <div class="zoom-car"><svg viewBox="0 0 64 64"><use href="#t-traveller"/></svg></div>
    <span class="zoom-line" style="top:calc(56% + 8px); animation-delay:120ms"></span>
    <span class="zoom-line" style="top:calc(56% + 30px); animation-delay:200ms"></span>
    <span class="zoom-line" style="top:calc(56% + 52px); animation-delay:160ms"></span>
    ${[14, 30, 46, 62, 78].map((x, i) =>
      `<span class="dust" style="left:${x}%; animation-delay:${150 + i * 110}ms"></span>`).join('')}
    ${[[22, 44], [50, 38], [78, 46]].map(([x, y], i) =>
      `<span class="pop-star" style="left:${x}%; top:${y}%; animation-delay:${300 + i * 160}ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>`).join('')}`,
};
