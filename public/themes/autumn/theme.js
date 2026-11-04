/* Kidzio theme: Autumn forest — warm chestnut woods at dusk (dark).
   Mascot: Hazel the hedgehog. Progress: an acorn rolls from a sapling to the acorn basket. */
window.KIDZIO_THEME = {
  id: 'autumn',
  name: 'Autumn forest',
  themeColor: '#33211C',
  preview: { bg: '#33211C', accents: ['#F28A3B', '#F2C14E', '#9DCB8E', '#EF8F8F'] },   /* used by themes.html */

  words: {
    tagline: "სად ვიკვლიოთ დღეს?",
    pick: "აირჩიე ბილიკი",
    start: "დაიწყე დღევანდელი თავგადასავალი",
    daily: "დღევანდელი თავგადასავალი",
    subject: (name) => `${name} — ბილიკი`,
    leave: "დატოვე თავგადასავალი და დაბრუნდი მთავარ გვერდზე",
    progress: "ბილიკის პროგრესი",
    next: "შემდეგი თავგადასავალი",
    pickHint: "შეეხე ფოთოლს პასუხის ასარჩევად.",
    clue: "სთხოვე ჰაზელს მინიშნება",
    resultTitles: ['', "თავგადასავალი დასრულდა!", "შესანიშნავი კვლევა!", "სუპერმკვლევარი!"],
    threeStars: "შენ ყველაფერი ზუსტად გააკეთე. ჰაზელი ფოთლების ბედნიერ ცეკვას ცეკვავს!",
  },

  sprite: `<defs>
    <path id="t-spark" d="M0,-11 C7,-7 9,0 5,6 C3,9 1,10 0,11 C-1,10 -3,9 -5,6 C-9,0 -7,-7 0,-11Z"/>
    <symbol id="t-start" viewBox="0 0 64 64">
      <path d="M32 58V36" stroke="#1E2346" stroke-width="5" stroke-linecap="round"/>
      <path d="M32 58V36" stroke="#8C5A3C" stroke-width="2.5" stroke-linecap="round"/>
      <circle cx="32" cy="26" r="18" fill="#F2C14E" stroke="#1E2346" stroke-width="3"/>
      <circle cx="24" cy="22" r="4" fill="#F28A3B"/><circle cx="38" cy="30" r="3.5" fill="#F28A3B"/>
    </symbol>
    <symbol id="t-end" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="2.5" stroke-linejoin="round">
        <ellipse cx="22" cy="28" rx="7" ry="8" fill="#D99A52"/><path d="M14 24q8-8 16 0z" fill="#7A4A2E"/>
        <ellipse cx="42" cy="28" rx="7" ry="8" fill="#D99A52"/><path d="M34 24q8-8 16 0z" fill="#7A4A2E"/>
        <ellipse cx="32" cy="22" rx="7" ry="8" fill="#D99A52"/><path d="M24 18q8-8 16 0z" fill="#7A4A2E"/>
        <path d="M8 32h48l-6 24H14Z" fill="#B5763C" stroke-width="3"/>
        <path d="M12 42h40M14 49h36" fill="none" stroke="#8C5A3C"/>
      </g>
    </symbol>
    <symbol id="t-traveller" viewBox="0 0 64 64">
      <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round">
        <path d="M32 6v8" stroke-linecap="round"/>
        <ellipse cx="32" cy="38" rx="17" ry="20" fill="#D99A52"/>
        <path d="M12 30c0-12 9-17 20-17s20 5 20 17Z" fill="#7A4A2E"/>
        <path d="M20 22l6 7M30 17l5 10M41 19l3 9" fill="none" stroke="#5A3622" stroke-width="2"/>
        <path d="M24 46q4 5 8 6" fill="none" stroke="#FFF1DA" stroke-opacity=".7" stroke-width="2.5" stroke-linecap="round"/>
      </g>
    </symbol>
  </defs>`,

  scenery: `<svg viewBox="0 0 400 860" preserveAspectRatio="xMidYMid slice">
      <circle cx="320" cy="120" r="58" fill="#4A2F27"/>
      <circle cx="300" cy="104" r="9" fill="#523429"/>
      <circle cx="338" cy="138" r="6" fill="#523429"/>
      <g fill="#3F2822">
        <ellipse cx="20" cy="770" rx="90" ry="70"/><ellipse cx="140" cy="800" rx="80" ry="60"/>
        <ellipse cx="270" cy="785" rx="95" ry="75"/><ellipse cx="390" cy="760" rx="85" ry="80"/>
      </g>
      <g fill="#291914">
        <rect x="14" y="790" width="14" height="80"/><rect x="132" y="820" width="12" height="60"/>
        <rect x="262" y="810" width="14" height="70"/><rect x="380" y="800" width="14" height="70"/>
        <rect x="0" y="840" width="400" height="30"/>
      </g>
      <g fill="#FFF0DA" opacity=".5">
        <circle cx="60" cy="60" r="1.6"/><circle cx="190" cy="40" r="1.3"/><circle cx="240" cy="210" r="1.4"/>
        <circle cx="30" cy="330" r="1.2"/><circle cx="380" cy="300" r="1.5"/>
      </g>
    </svg>
    <span class="leaf-fall ambient" style="left:1%;  --c:#B8642F; animation-duration:17s; animation-delay:-2s"><svg viewBox="-12 -12 24 24"><use href="#t-spark"/></svg></span>
    <span class="leaf-fall ambient" style="left:4%;  --c:#B8913A; animation-duration:23s; animation-delay:-13s"><svg viewBox="-12 -12 24 24"><use href="#t-spark"/></svg></span>
    <span class="leaf-fall ambient" style="left:91%; --c:#A65A52; animation-duration:19s; animation-delay:-6s"><svg viewBox="-12 -12 24 24"><use href="#t-spark"/></svg></span>
    <span class="leaf-fall ambient" style="left:94%; --c:#B8642F; animation-duration:25s; animation-delay:-16s"><svg viewBox="-12 -12 24 24"><use href="#t-spark"/></svg></span>`,

  /* Hazel the hedgehog. Hooks: .m-arm-l, .m-arm-r, .m-head */
  mascot: `<svg viewBox="0 0 120 150" aria-hidden="true" focusable="false">
    <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round" stroke-linecap="round">
      <path d="M11.7 98.3 L21 87.2 L11 76.7 L24.5 70.7 L20.3 57.1 L35.3 57.4 L37.9 43.4 L51.1 49.9 L60 38.5 L68.9 49.9 L82.1 43.4 L84.7 57.4 L99.7 57.1 L95.5 70.7 L109 76.7 L99 87.2 L108.3 98.3 L104 118 Q60 138 16 118Z" fill="#7A4A2E"/>
      <ellipse cx="46" cy="136" rx="10" ry="6" fill="#5A3622"/>
      <ellipse cx="74" cy="136" rx="10" ry="6" fill="#5A3622"/>
      <g class="m-arm m-arm-l"><rect x="26" y="98" width="11" height="24" rx="5.5" fill="#F3D9B0"/><circle cx="31.5" cy="123" r="6" fill="#E3BD8C"/></g>
      <g class="m-arm m-arm-r"><rect x="83" y="98" width="11" height="24" rx="5.5" fill="#F3D9B0"/><circle cx="88.5" cy="123" r="6" fill="#E3BD8C"/></g>
      <ellipse cx="60" cy="114" rx="24" ry="20" fill="#F3D9B0"/>
      <g class="m-head">
        <circle cx="38" cy="62" r="7" fill="#E3BD8C"/>
        <circle cx="82" cy="62" r="7" fill="#E3BD8C"/>
        <ellipse cx="60" cy="78" rx="27" ry="23" fill="#F3D9B0"/>
        <ellipse cx="60" cy="88" rx="11" ry="8" fill="#F8E7C9"/>
        <circle cx="60" cy="83" r="4.5" fill="#1E2346"/>
        <path d="M54 92q6 5 12 0" fill="none" stroke-width="2.6"/>
        <g stroke="none">
          <circle cx="49" cy="74" r="4" fill="#1E2346"/><circle cx="71" cy="74" r="4" fill="#1E2346"/>
          <circle cx="50.5" cy="72.5" r="1.4" fill="#FFFFFF"/><circle cx="72.5" cy="72.5" r="1.4" fill="#FFFFFF"/>
          <circle cx="42" cy="84" r="3.6" fill="#EF8F8F"/><circle cx="78" cy="84" r="3.6" fill="#EF8F8F"/>
        </g>
        <path d="M62 44 C72 34 84 38 84 38 C80 50 68 52 62 44Z" fill="#F2C14E" stroke-width="2.5"/>
        <path d="M63 44 L78 40" fill="none" stroke="#B8891C" stroke-width="1.8"/>
      </g>
    </g>
  </svg>`,

  /* Letter badge: a leaf. viewBox 58×46. */
  badge: (a) => `<svg viewBox="0 0 58 46">
    <g class="ring">
      <path d="M5 38 C 8 14, 26 4, 54 7 C 52 31, 32 44, 5 38 Z" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6" stroke-linejoin="round"/>
      <path d="M5 38 l-4 5" stroke="#1E2346" stroke-width="2.6" stroke-linecap="round"/>
      <path d="M9 35 Q 30 24 49 11" fill="none" style="stroke: var(--${a}-deep)" stroke-width="2" opacity=".45"/>
    </g>
  </svg>`,

  /* Subject tile art: a big leaf holding the subject icon. viewBox 76×62. */
  tile: (a, icon) => `<svg viewBox="0 0 76 62">
    <g class="ring">
      <path d="M7 54 C 10 20, 34 4, 72 7 C 70 42, 44 60, 7 54 Z" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6" stroke-linejoin="round"/>
      <path d="M7 54 l-5 6" stroke="#1E2346" stroke-width="2.6" stroke-linecap="round"/>
    </g>
    <use href="#${icon}" x="22" y="13" width="34" height="34"/>
  </svg>`,

  /* Correct answer: a gust of leaves swirls across the forest. */
  celebration: [
    ['a1', 18, 0], ['a2', 30, 60], ['a3', 42, 120], ['a4', 54, 40], ['a1', 66, 160],
    ['a2', 24, 220], ['a3', 48, 260], ['a4', 36, 320], ['a1', 60, 300], ['a2', 72, 380],
  ].map(([c, top, delay]) =>
    `<span class="gust-leaf" style="top:${top}%; --c: var(--${c}); animation-delay:${delay}ms"><svg viewBox="-12 -12 24 24"><use href="#t-spark"/><path d="M0,-9 L0,9" stroke="#1E2346" stroke-width="1.4" opacity=".4"/></svg></span>`
  ).join(''),
};
