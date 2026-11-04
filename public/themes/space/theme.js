/* Kidzio theme: Space — friendly cartoon space adventure (dark).
   A theme provides words, drawings and a celebration. See themes/README.md. */
window.KIDZIO_THEME = {
  id: 'space',
  name: 'Space',
  themeColor: '#141B3C',
  preview: { bg: '#141B3C', accents: ['#FF7A3D', '#FFCF4A', '#5ED6B0', '#FF9EB5'] },   /* used by themes.html */

  words: {
    tagline: "სად გავფრინდეთ დღეს?",
    pick: "აირჩიე პლანეტა",
    start: "დაიწყე დღევანდელი მისია",
    daily: "დღევანდელი მისია",
    subject: (name) => `${name} — პლანეტა`,
    leave: "დატოვე მისია და დაბრუნდი მთავარ გვერდზე",
    progress: "მისიის პროგრესი",
    next: "შემდეგი მისია",
    pickHint: "შეეხე პლანეტას პასუხის ასარჩევად.",
    clue: "სთხოვე ზიპს მინიშნება",
    resultTitles: ['', "მისია შესრულებულია!", "შესანიშნავი ფრენა!", "სუპერვარსკვლავი პილოტი!"],
    threeStars: "შენ ყველაფერი ზუსტად გააკეთე. ზიპი კოსმოსურ ცეკვას ცეკვავს!",
  },

  /* Progress bar: Earth → Moon, flown by the rocket. Sparkle: a 4-point star. */
  sprite: `<defs>
    <path id="t-spark" d="M0,-10 C1.2,-2.4 2.6,-1.1 10,0 C2.4,1.4 1.3,2.2 0,10.5 C-1.6,2.6 -2.2,1.2 -9.5,0 C-2.4,-1.3 -1.4,-2.1 0,-10Z"/>
    <symbol id="t-start" viewBox="0 0 64 64"><use href="#i-earth" width="64" height="64"/></symbol>
    <symbol id="t-end" viewBox="0 0 64 64"><use href="#i-moon" width="64" height="64"/></symbol>
    <symbol id="t-traveller" viewBox="0 0 64 64"><use href="#i-rocket" width="64" height="64"/></symbol>
  </defs>`,

  scenery: `<svg viewBox="0 0 400 860" preserveAspectRatio="xMidYMid slice">
    <g>
      <circle cx="360" cy="96" r="30" fill="#253068"/>
      <circle cx="350" cy="86" r="6" fill="#2E3A78"/>
      <circle cx="370" cy="108" r="4" fill="#2E3A78"/>
      <ellipse cx="360" cy="98" rx="50" ry="11" fill="none" stroke="#3B488A" stroke-width="4" stroke-linecap="round" transform="rotate(-14 360 98)" stroke-dasharray="120 18"/>
    </g>
    <g>
      <circle cx="-10" cy="830" r="120" fill="#1C2550"/>
      <circle cx="40" cy="770" r="14" fill="#212B5C"/>
      <circle cx="70" cy="830" r="9" fill="#212B5C"/>
    </g>
    <g transform="translate(215 26) scale(.8)"><use href="#t-spark" class="twinkle ambient" fill="#FFF4D6"/></g>
    <g transform="translate(250 40) scale(.7)"><use href="#t-spark" class="twinkle ambient d2" fill="#FFE59A"/></g>
    <g transform="translate(300 220) scale(.7)"><use href="#t-spark" class="twinkle ambient d1" fill="#FFF4D6"/></g>
    <g transform="translate(24 300) scale(.7)"><use href="#t-spark" class="twinkle ambient d3" fill="#FFF4D6"/></g>
    <g transform="translate(380 420) scale(.7)"><use href="#t-spark" class="twinkle ambient d2" fill="#FFE59A"/></g>
    <g transform="translate(140 600) scale(.7)"><use href="#t-spark" class="twinkle ambient d1" fill="#FFF4D6"/></g>
    <g transform="translate(340 700) scale(.7)"><use href="#t-spark" class="twinkle ambient d3" fill="#FFF4D6"/></g>
    <g fill="#FFF4D6" opacity=".7">
      <circle cx="120" cy="30" r="1.8"/><circle cx="190" cy="120" r="1.4"/>
      <circle cx="80" cy="180" r="1.6"/><circle cx="220" cy="260" r="1.2"/>
      <circle cx="370" cy="300" r="1.6"/><circle cx="60" cy="470" r="1.3"/>
      <circle cx="300" cy="560" r="1.8"/><circle cx="210" cy="780" r="1.4"/>
      <circle cx="380" cy="820" r="1.6"/><circle cx="16" cy="640" r="1.2"/>
    </g>
  </svg>`,

  /* Zip the astronaut. Every mascot uses the same hooks:
     .m-arm-l, .m-arm-r and .m-head (pivot points are set in theme.css). */
  mascot: `<svg viewBox="0 0 120 150" aria-hidden="true" focusable="false">
    <g stroke="#1E2346" stroke-width="3" stroke-linejoin="round" stroke-linecap="round">
      <rect x="30" y="80" width="60" height="38" rx="12" fill="#D8CBB2"/>
      <rect x="43" y="112" width="13" height="22" rx="6.5" fill="#FFF8EC"/>
      <rect x="64" y="112" width="13" height="22" rx="6.5" fill="#FFF8EC"/>
      <path d="M39 132h20v5a5 5 0 0 1-5 5H44a5 5 0 0 1-5-5z" fill="#FF7A3D"/>
      <path d="M61 132h20v5a5 5 0 0 1-5 5H66a5 5 0 0 1-5-5z" fill="#FF7A3D"/>
      <g class="m-arm m-arm-l"><rect x="28" y="84" width="12" height="27" rx="6" fill="#FFF8EC"/><circle cx="34" cy="113" r="7" fill="#FF7A3D"/></g>
      <g class="m-arm m-arm-r"><rect x="80" y="84" width="12" height="27" rx="6" fill="#FFF8EC"/><circle cx="86" cy="113" r="7" fill="#FF7A3D"/></g>
      <rect x="36" y="77" width="48" height="42" rx="16" fill="#FFF8EC"/>
      <rect x="49" y="89" width="22" height="14" rx="5" fill="#2B3670"/>
      <circle cx="55" cy="96" r="2.4" fill="#FFCF4A" stroke="none"/>
      <circle cx="65" cy="96" r="2.4" fill="#5ED6B0" stroke="none"/>
      <g class="m-head">
        <path d="M60 21V9"/>
        <circle cx="60" cy="7" r="6" fill="#FF7A3D"/>
        <circle cx="60" cy="50" r="31" fill="#FFF8EC"/>
        <ellipse cx="60" cy="52" rx="22" ry="18" fill="#2B3670"/>
        <g stroke="none">
          <ellipse cx="51" cy="50" rx="4" ry="5.5" fill="#FFF4D6"/>
          <ellipse cx="69" cy="50" rx="4" ry="5.5" fill="#FFF4D6"/>
          <circle cx="45" cy="59" r="3.2" fill="#FF9EB5"/>
          <circle cx="75" cy="59" r="3.2" fill="#FF9EB5"/>
        </g>
        <path d="M53 59q7 6 14 0" fill="none" stroke="#FFF4D6" stroke-width="2.6"/>
        <path d="M43 41q4-7 11-8" fill="none" stroke="#FFFFFF" stroke-opacity=".55" stroke-width="2.6"/>
      </g>
    </g>
  </svg>`,

  /* Letter badge on answer cards: a little ringed planet. viewBox 58×46. */
  badge: (a) => `<svg viewBox="0 0 58 46">
    <ellipse class="ring" cx="29" cy="23" rx="28" ry="8" fill="none" stroke="#1E2346" stroke-width="2.6" stroke-linecap="round" stroke-dasharray="40 8 60 10"/>
    <circle cx="29" cy="23" r="20" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6"/>
    <circle cx="19" cy="16" r="3" style="fill: var(--${a}-deep)" opacity=".35"/>
    <circle cx="40" cy="31" r="2.2" style="fill: var(--${a}-deep)" opacity=".35"/>
  </svg>`,

  /* Subject tile art: a bigger planet holding the subject icon. viewBox 76×62. */
  tile: (a, icon) => `<svg viewBox="0 0 76 62">
    <ellipse class="ring" cx="38" cy="31" rx="37" ry="9" fill="none" stroke="#1E2346" stroke-width="2.6" stroke-linecap="round" stroke-dasharray="50 9 80 8"/>
    <circle cx="38" cy="31" r="27" style="fill: var(--${a})" stroke="#1E2346" stroke-width="2.6"/>
    <use href="#${icon}" x="21" y="14" width="34" height="34"/>
  </svg>`,

  /* Correct answer: the rocket blasts across with a trail of stars. */
  celebration: `
    <div class="blast-rocket"><svg viewBox="0 0 64 64"><use href="#i-rocket"/></svg></div>
    <span class="blast-star" style="left:19%;top:70%;animation-delay:250ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>
    <span class="blast-star" style="left:34%;top:62%;animation-delay:380ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>
    <span class="blast-star" style="left:44%;top:50%;animation-delay:450ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>
    <span class="blast-star" style="left:60%;top:41%;animation-delay:580ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>
    <span class="blast-star" style="left:68%;top:31%;animation-delay:640ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>
    <span class="blast-star" style="left:86%;top:20%;animation-delay:780ms"><svg viewBox="0 0 64 64"><use href="#i-star"/></svg></span>`,
};
