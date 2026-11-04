/* Kidzio theme: Under the sea — a sunny coral reef (bright).
   Mascot: Finn the fish. Progress: a submarine dives from the boat to the treasure chest. */
window.KIDZIO_THEME = {
  id: 'ocean',
  name: 'Under the sea',
  themeColor: '#A9E6EE',
  preview: { bg: '#A9E6EE', accents: ['#FF7F66', '#FFD24D', '#3FC195', '#FF9EC4'] },   /* used by themes.html */

  words: {
    tagline: "სად ჩავყვინთოთ დღეს?",
    pick: "აირჩიე რიფი",
    start: "დაიწყე დღევანდელი ჩაყვინთვა",
    daily: "დღევანდელი ჩაყვინთვა",
    subject: (name) => `${name} — რიფი`,
    leave: "დატოვე ჩაყვინთვა და დაბრუნდი მთავარ გვერდზე",
    progress: "ჩაყვინთვის პროგრესი",
    next: "შემდეგი ჩაყვინთვა",
    pickHint: "შეეხე ნიჟარას პასუხის ასარჩევად.",
    clue: "სთხოვე ფინს მინიშნება",
    resultTitles: ['', "ჩაყვინთვა დასრულდა!", "შესანიშნავი ცურვა!", "სუპერმყვინთავი!"],
    threeStars: "შენ ყველაფერი ზუსტად გააკეთე. ფინი ბედნიერ სალტოებს აკეთებს!",
  },

  sprite: `<defs>
    <path id="t-spark" d="M0,-11 C1.6,-4 2.4,-3.6 10.4,-3.4 C4.3,0.8 3.9,1.8 6.4,9 C0.9,4.6 -0.9,4.6 -6.4,9 C-3.9,1.8 -4.3,0.8 -10.4,-3.4 C-2.4,-3.6 -1.6,-4 0,-11Z"/>
    <symbol id="t-start" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round">
        <path d="M32 8v28" stroke-linecap="round"/>
        <path d="M34 10l16 20H34Z" fill="#FFF8EC"/>
        <path d="M6 38h52l-8 14H14Z" fill="#FF7F66"/>
        <path d="M2 56q7.5-5 15 0t15 0 15 0 15 0" fill="none" stroke="#4C9FB0"/>
      </g>
    </symbol>
    <symbol id="t-end" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round">
        <path d="M8 30c0-10 10-16 24-16s24 6 24 16Z" fill="#B5763C"/>
        <rect x="8" y="30" width="48" height="24" rx="3" fill="#C98A48"/>
        <path d="M8 30h48" fill="none"/>
        <rect x="27" y="26" width="10" height="12" rx="2" fill="#FFD24D"/>
        <circle cx="20" cy="22" r="3.5" fill="#FFD24D" stroke-width="2"/><circle cx="44" cy="20" r="3.5" fill="#FFD24D" stroke-width="2"/>
      </g>
    </symbol>
    <symbol id="t-traveller" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round">
        <path d="M34 22v-8h8" fill="none" stroke-linecap="round"/>
        <rect x="26" y="20" width="14" height="9" rx="3" fill="#FFD24D"/>
        <ellipse cx="32" cy="38" rx="24" ry="13" fill="#FFD24D"/>
        <path d="M8 38l-6-7v14Z" fill="#FF7F66"/>
        <circle cx="30" cy="38" r="5" fill="#A9E6EE"/><circle cx="44" cy="38" r="5" fill="#A9E6EE"/>
      </g>
    </symbol>
  </defs>`,

  scenery: `<svg viewBox="0 0 400 860" preserveAspectRatio="xMidYMid slice">
      <path d="M-10 0H410V46Q360 64 300 48T200 50T100 48T-10 50Z" fill="#C9F2F6"/>
      <g fill="#FFFFFF" opacity=".28">
        <path d="M70 0h40L60 520H20Z"/><path d="M210 0h30L210 460H170Z"/><path d="M330 0h46L320 560H280Z"/>
      </g>
      <g fill="#7CC9D5">
        <path d="M300 300q16-12 32 0-16 12-32 0Zm32 0l10-8v16Z"/>
        <path d="M60 420q12-9 24 0-12 9-24 0Zm24 0l8-6v12Z"/>
      </g>
      <path d="M-10 790Q60 760 140 782T290 776T410 770V900H-10Z" fill="#F6E3B4"/>
      <g class="sway ambient" style="transform-origin: 40px 800px"><path d="M40 800q-12-30 0-60t0-60" fill="none" stroke="#2EA57D" stroke-width="9" stroke-linecap="round"/></g>
      <g class="sway ambient d2" style="transform-origin: 60px 800px"><path d="M60 800q10-26 0-52t0-44" fill="none" stroke="#3FC195" stroke-width="8" stroke-linecap="round"/></g>
      <g class="sway ambient d1" style="transform-origin: 360px 790px"><path d="M360 790q-12-30 0-60t0-50" fill="none" stroke="#2EA57D" stroke-width="9" stroke-linecap="round"/></g>
      <path d="M330 834l4 9 10 1-8 6 3 10-9-6-9 6 3-10-8-6 10-1Z" fill="#FF9E7A" stroke="#1E2346" stroke-width="2" stroke-linejoin="round"/>
    </svg>
    ${[[10, 13, -3], [24, 17, -9], [47, 15, -1], [66, 19, -12], [84, 14, -6], [93, 21, -15]].map(([x, d, delay]) =>
      `<span class="bubble ambient" style="left:${x}%; animation-duration:${d}s; animation-delay:${delay}s"></span>`).join('')}`,

  /* Finn the fish. Hooks: .m-arm-l / .m-arm-r (fins), .m-head (face). */
  mascot: `<svg viewBox="0 0 120 150" aria-hidden="true" focusable="false">
    <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round" stroke-linecap="round">
      <circle cx="94" cy="26" r="4" fill="#FFFFFF" fill-opacity=".6" stroke-width="2"/>
      <circle cx="102" cy="12" r="2.6" fill="#FFFFFF" fill-opacity=".6" stroke-width="2"/>
      <path d="M44 50 Q58 22 80 46Z" fill="#FF8A4C"/>
      <path d="M48 116 L36 140 Q60 130 84 140 L72 116 Z" fill="#FF8A4C"/>
      <g class="m-arm m-arm-l"><path d="M26 86 q-20 4 -18 26 q12 -2 20 -16 z" fill="#FFB37A"/></g>
      <g class="m-arm m-arm-r"><path d="M94 86 q20 4 18 26 q-12 -2 -20 -16 z" fill="#FFB37A"/></g>
      <circle cx="60" cy="82" r="38" fill="#FF8A4C" stroke="none"/>
      <path d="M37 52 Q30 82 37 112" fill="none" stroke="#FFF8EC" stroke-width="9" stroke-linecap="butt"/>
      <path d="M83 52 Q90 82 83 112" fill="none" stroke="#FFF8EC" stroke-width="9" stroke-linecap="butt"/>
      <circle cx="60" cy="82" r="38" fill="none"/>
      <g class="m-head">
        <circle cx="48" cy="74" r="10" fill="#FFFFFF"/>
        <circle cx="72" cy="74" r="10" fill="#FFFFFF"/>
        <g stroke="none">
          <circle cx="50" cy="76" r="5" fill="#1E2346"/><circle cx="74" cy="76" r="5" fill="#1E2346"/>
          <circle cx="51.6" cy="73.8" r="1.7" fill="#FFFFFF"/><circle cx="75.6" cy="73.8" r="1.7" fill="#FFFFFF"/>
          <circle cx="42" cy="92" r="3.6" fill="#FF9EC4"/><circle cx="78" cy="92" r="3.6" fill="#FF9EC4"/>
        </g>
        <path d="M52 95q8 7 16 0" fill="none" stroke-width="2.8"/>
      </g>
    </g>
  </svg>`,

  /* Letter badge: a scallop shell. viewBox 58×46. */
  badge: (a) => `<svg viewBox="0 0 58 46">
    <path d="M22 39h14l-2 6h-10z" style="fill: var(--${a}-deep)" stroke="#1E2346" stroke-width="2.2" stroke-linejoin="round"/>
    <path d="M29 2C43 2 54 12 54 25c0 6-4 11-9 13l-9 2H22l-9-2C8 36 4 31 4 25 4 12 15 2 29 2Z" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6" stroke-linejoin="round"/>
    <g style="stroke: var(--${a}-deep)" stroke-width="2" opacity=".45" fill="none">
      <path d="M29 40 L12 12M29 40 L46 12"/>
    </g>
  </svg>`,

  /* Subject tile art: a big shell holding the subject icon. viewBox 76×62. */
  tile: (a, icon) => `<svg viewBox="0 0 76 62">
    <path d="M30 53h16l-2 7H32z" style="fill: var(--${a}-deep)" stroke="#1E2346" stroke-width="2.4" stroke-linejoin="round"/>
    <path d="M38 2C57 2 72 15 72 33c0 8-5 14-12 17l-12 3H28l-12-3C9 47 4 41 4 33 4 15 19 2 38 2Z" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6" stroke-linejoin="round"/>
    <use href="#${icon}" x="21" y="11" width="34" height="34"/>
  </svg>`,

  /* Correct answer: bubbles burst upward while a school of fish dashes past. */
  celebration: `
    ${[[12, 0], [22, 140], [34, 60], [46, 220], [58, 100], [70, 260], [82, 40], [90, 180]].map(([x, delay]) =>
      `<span class="pop-bubble" style="left:${x}%; animation-delay:${delay}ms"></span>`).join('')}
    ${[[52, 0, 'a1'], [60, 120, 'a2'], [68, 60, 'a4']].map(([top, delay, c]) =>
      `<span class="dash-fish" style="top:${top}%; animation-delay:${delay}ms"><svg viewBox="0 0 64 40"><path d="M6 20q22-18 44 0-22 18-44 0Z" style="fill: var(--${c})" stroke="#1E2346" stroke-width="3" stroke-linejoin="round"/><path d="M50 20l12-10v20Z" style="fill: var(--${c})" stroke="#1E2346" stroke-width="3" stroke-linejoin="round"/><circle cx="18" cy="17" r="3" fill="#1E2346"/></svg></span>`).join('')}`,
};
