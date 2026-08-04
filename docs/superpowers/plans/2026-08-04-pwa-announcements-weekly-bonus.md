# PWA Announcements and Weekly Volume Bonus Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a 24-hour-snoozable PWA announcement modal and recurring weekly transaction-volume bonus campaigns.

**Architecture:** Keep announcement suppression in browser local storage. Extend `bonuses.conditions` for weekly rules, add an idempotent weekly reward ledger, and process completed weeks through a reusable service invoked by a scheduled Artisan command.

**Tech Stack:** Laravel, Eloquent, Pest, React/Inertia, Tailwind, Vite.

## Global Constraints

- Weekly boundaries use `Africa/Lagos` and Monday-to-Sunday weeks.
- Only successful transactions (`status = 1`) qualify.
- Percentage rewards require a cap.
- Existing targeted-customer rules take precedence.

---

### Task 1: Announcement modal

**Files:** Modify `resources/js/Components/Announcements.jsx`; test with the production Vite build.

- [ ] Replace the inline slider with an auto-opening modal and compact reopen trigger.
- [ ] Persist `oresamsub-announcements-snoozed-until` for exactly 24 hours when selected.
- [ ] Preserve multiple-announcement navigation and safe manual reopening.
- [ ] Run `npm run build`.

### Task 2: Weekly campaign persistence and validation

**Files:** Create a migration and `WeeklyBonusReward`; modify `Bonus`, `BonusCampaignRequest`, `BonusController`, and the admin bonus form.

- [ ] Add weekly group/rule validation and optional indefinite campaign end date.
- [ ] Add the unique campaign/customer/week reward ledger.
- [ ] Expose threshold, category scope, reward type/value and percentage cap in admin.

### Task 3: Weekly processor

**Files:** Create `WeeklyTransactionBonusService` and `ProcessWeeklyTransactionBonuses`; modify `routes/console.php`.

- [ ] Write failing qualification, filter, cap and duplicate-prevention tests.
- [ ] Aggregate successful transaction amounts for the requested completed week.
- [ ] Credit the bonus wallet transactionally and write financial audit logs.
- [ ] Schedule Monday processing in Africa/Lagos.

### Task 4: Verification

- [ ] Run focused weekly bonus tests and the existing bonus suite.
- [ ] Run PHP syntax, route, migration, Blade and Vite build checks.
