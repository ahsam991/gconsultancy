# DESIGN.md — Global Consultancy Education

Evidence-based design language for the public site, CRM, and candidate portal.
Skills applied: `interface-design` (product craft) + `landing-page` (public conversion) + `baseline-ui` (polish constraints, adapted to the Bootstrap 5.3 CDN stack — no build step, Hostinger-safe).

## 1. Intent brief

**Who (public):** a 19–24-year-old prospective student (often on mobile, often with a parent nearby), comparing study-abroad options between classes. 5 minutes ago: Googling "study in UK requirements". 5 after: WhatsApping parents a university shortlist.
**Who (CRM):** a counsellor doing 30+ cases a day on desktop — triaging candidates, chasing documents, advancing applications. Needs density without noise.
**Who (portal):** the same student, anxious about visa — needs to see exactly where they stand and what's next.

**Verbs:** enquire → apply (public); triage → verify → advance (CRM); track → upload → wait with confidence (portal).

**Feel:** a British university prospectus, not a SaaS dashboard. Calm ink-on-paper, editorial serif headlines, one confident blue. The CRM should feel like air-traffic control for admissions: dense, tabular, calm — never clownish.

## 2. Domain exploration

- **Domain words:** prospectus, offer letter, CAS, vignette, intake term, UCAS, adviser desk, boarding pass, stamp.
- **Color world:** oxford navy ink, parchment paper, brass (visa-vignette gold), passport burgundy (sparingly), chalk white.
- **Signature:** the *journey rail* — Profile → Application → Offer → Deposit → CAS → Visa → Enrolment rendered as a boarding-pass timeline; terminal approvals rendered as *stamps* (double-ring bordered badges).
- **Defaults to avoid:** rainbow stat cards; gradient hero CTAs; generic Bootstrap jumbotron; emoji-style icon soup; grey-on-grey tables; ALL-CAPS walls of text outside stamps.

## 3. Tokens (`public/css/gc-design.css`, CSS vars on `:root`)

| Token | Value | Use |
|---|---|---|
| `--gc-ink` | `#16233f` | Sidebar, footer, display text |
| `--gc-ink-soft` | `#3c4a68` | Secondary text |
| `--gc-paper` | `#faf8f3` | Page background (public + portal) |
| `--gc-card` | `#ffffff` | Cards |
| `--gc-line` | `#e7e1d5` | Warm borders |
| `--gc-accent` | `#1e40af` | ONE accent per view: primary actions, active states |
| `--gc-brass` | `#b45309` | Stamps, key approvals, premium highlights only |
| `--gc-ok` | `#166534` | Success (muted) |
| `--gc-warn` | `#92400e` | Warning (muted) |
| `--gc-bad` | `#991b1b` | Danger (muted) |
| `--gc-display` | `"Newsreader", Georgia, serif` | Headlines, hero, prospectus feel |
| `--gc-ui` | `"Inter", system-ui, sans-serif` | Everything else |

Type: display serif for H1/H2 + eyebrows (small-caps section labels); Inter for UI. `text-wrap: balance` on headings; `font-variant-numeric: tabular-nums` on ALL figures, fees, dates, tables.

## 4. Rules (baseline-ui, adapted)

- No gradients, no glow, no purple. Ever.
- One accent color per view (blue; brass reserved for stamps/approvals).
- Bootstrap default shadows only; depth via 1px warm borders first.
- Empty states get exactly one clear next action.
- Errors render next to the action, never only at page top.
- Icon-only buttons get `aria-label`. Skip-link on public pages (exists).
- Motion: only transform/opacity, ≤200ms, ease-out entrances; `prefers-reduced-motion` respected. No layout-property animation.
- Fixed z-scale: nav 100, sidebar 100, dropdown 200, modal 300, toast 400.
- Mobile-first portal; tables scroll in `.table-responsive`, never break layout.

## 5. Public landing structure (landing-page skill)

Home = one intent: **book free counselling** (primary) / apply online (secondary).
Order: eyebrow + outcome headline + sub + dual CTA + proof strip → destinations → how it works (3 steps) → featured universities/courses → testimonials → FAQ (6) → risk reversal (free counselling, no hidden fees) → final CTA. Every other public page reuses hero-band + proof + final-CTA partials.

## 6. CRM shell

- Ink sidebar (paper text), section eyebrows, active item = brass left bar + paper pill. Topbar paper-white, tabular counts.
- Dashboard: ONE stat strip (ink numbers, tabular), pipeline funnel, charts in bordered cards — no rainbow cards.
- Tables: sticky header, row hover, tabular nums, status stamps.
- Detail pages: journey rail on top, tabs below.

## 7. What NOT to touch

- Route names, controller logic, validation, RBAC, policies.
- DataTables/Chart.js/Alpine CDN wiring in layouts.
- `noindex` on CRM layout; public SEO tags.
