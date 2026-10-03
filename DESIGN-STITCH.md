# DESIGN.md — Global Consultancy Education

## 1. Atmosphere
A British university prospectus crossed with an air-traffic control desk. Calm, ink-on-paper, editorial. Density 5 (balanced daily app), Variance 6 (offset asymmetric heroes, disciplined symmetric CRM), Motion 3 (restrained, ease-out micro-feedback only).

## 2. Color calibration (single palette, no fluctuation)
- Oxford Ink `#16233F` — primary surfaces (sidebar, footer, hero band), display text on light.
- Parchment `#FAF8F3` — page background everywhere. Cards pure white `#FFFFFF` with 1px warm border `#E7E1D2`.
- Oxford Blue `#1E40AF` — THE single accent. Primary buttons, active nav, links, progress. Saturation under 80%.
- Brass `#B45309` — metallic seal color, reserved ONLY for approval stamps (visa approved, enrolled) and one hairline rule under the hero. Never for buttons or large fills.
- Muted semantics: success `#166534` on `#EAF5EE`, warning `#92400E` on `#FDF3E7`, danger `#991B1B` on `#FBECEC`, info = Oxford Blue on `#EAF0FD`, neutral `#6B7690` on `#EEF0F4`.
- BANNED: gradients (all), purple/blue neon glows, pure black `#000000`, multicolor rainbow stat cards.

## 3. Typography
- Display (public pages, hero, section titles): Newsreader, weight 500–700, tight leading 1.05–1.15, balanced text wrap. Never oversized screaming type.
- UI (everything incl. all CRM dashboards): Space Grotesk, weight 400–700. Inter is banned.
- Figures (fees, counts, dates, table numerals): tabular numerals everywhere; Space Mono for hero proof numbers and finance totals.
- Eyebrow labels: 12px, uppercase, letter-spacing 0.14em, Brass color — used above every section and sidebar group.
- Body max 65 characters per line.

## 4. Hero (public home)
Asymmetric (never centered): left column holds eyebrow ("Free counselling · UK, USA, Canada, Australia, Europe"), Newsreader outcome headline naming the audience ("Get into a British university, guided start to finish"), one-line subheadline with specificity, dual CTA (solid Oxford Blue "Book free counselling" + outline "Apply online"), and a proof strip with three mono-font stats (offers secured, visa grant rate, partner universities). Right column: single application-journey boarding-pass card (Profile → Offer → CAS → Visa → Enrolled) rendered as a cream ticket with dashed dividers and a brass stamp reading "Enrolled". No scroll hints, no filler text, no overlapping elements.

## 5. CRM shell
Ink sidebar (`#16233F`, paper text `#E9E4D8`), section eyebrows, active item = paper pill with 3px brass left bar. Paper-white topbar with tabular counts and a single blue bell. Dashboard: one calm stat strip (ink serif numerals, small-caps labels, 3px blue top border; brass top border only for the money card), pipeline funnel, bordered chart cards — zero rainbow cards. Tables: sticky warm header row, row hover `#F6F2E9`, tabular numerals, status stamps (uppercase micro-badges with 1.5px colored border; terminal approvals get double-ring "sealed" stamp tilted -2deg).

## 6. Motion
Entrance ease-out only, max 200ms for feedback, transform/opacity only, destination cards lift -3px on hover, respect prefers-reduced-motion. No layout-property animation, no large blurs.

## 7. Signature elements (must appear, unique to this product)
1. Journey rail: boarding-pass timeline with dashed dividers for Profile → Application → Offer → Deposit → CAS → Visa → Enrolment.
2. Stamp badges: double-ring bordered approval stamps for VISA_APPROVED and ENROLLED.
3. Brass hairline rule under every hero band.
