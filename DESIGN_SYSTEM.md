# DESIGN_SYSTEM.md — Global Consultancy Education

Single source of truth: `public/css/gc-design.css` (static, no build). Full rationale in `DESIGN.md`; reference screens generated in Stitch project "Global Consultancy Education".

## Brand (real business data)
- Primary Navy `#0B2C5C` — sidebar, footer, hero band, headings ink
- Accent Orange `#FF8C00` — primary buttons, active states, stamps, hero rule (hover `#cc6f00`)
- Paper `#FAF8F3` page bg, White cards, warm border `#E7E1D2`
- Muted semantics: ok `#166534`/bg `#EAF5EE`, warn `#92400E`, bad `#991B1B`, info navy, mute `#6B7690`
- BANNED: gradients, glows, purple, rainbow stat cards

## Type
- Display: Newsreader 500–700 (hero, H1/H2, stat numerals), `text-wrap: balance`
- UI: Inter 400–700; body `text-wrap: pretty`
- All figures/dates/tables: `tabular-nums` (`.tnum` or automatic on `.table`, `.badge`, `.stat-num`)
- Eyebrows: 12px uppercase, 0.14em tracking, brass (`.gc-eyebrow`, `.blue` variant)

## Components
| Class | Use |
|---|---|
| `.gc-hero` / `.gc-hero-card` / `.gc-hero-rule` | Navy band, cream ticket card, brass rule |
| `.gc-proof .n/.l` | Proof stats (serif numeral + micro label) |
| `.gc-dest-card` | Destination/course/testimonial cards, -3px hover lift |
| `.gc-step-num` | Numbered discs (navy, cream text) |
| `.gc-stat` (+`.brass`) | Calm stat: serif numeral, small-caps label, 3px top border |
| `.gc-stamp .ok/.info/.warn/.bad/.mute` (+`.sealed`) | Uppercase micro-badge, 1.5px border; sealed = double ring, -2° tilt |
| `.gc-rail .stop(.done/.now) .dot` | Boarding-pass stepper with dashed dividers |
| `.gc-track .pct` | Portal progress card with big serif % |
| `.gc-topstrip` | Orange? No — ink utility bar with hotline + social proof |
| `.gc-cta-band` | Navy CTA panel, brass top border |
| `.gc-faq` | Accordion with brass-soft open state |
| `.gc-tl` | Vertical timeline, brass dots |
| `.btn-brass` / `.btn-outline-brass` | Orange actions (sparingly — one accent per view) |

## Shell
- CRM sidebar ink (`#0B2C5C`), paper text, brass left-bar active, section eyebrows
- Topbar white with warm border, tabular counts, bell + user menu
- Footer ink, 4 columns: brand/USP, London, Khulna+areas, social

## Motion & a11y
- transform/opacity only, ≤200ms, ease-out; `prefers-reduced-motion` kills all
- z-scale: nav 100, dropdown 200, modal 300, toast 400
- icon-only buttons need `aria-label`; errors next to fields; skip-link on public
- Mobile-first portal + bottom nav (candidate only, ≤767px)
