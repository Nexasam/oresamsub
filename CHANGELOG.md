# Changelog

---

## What i've been working on

This covers everything that's changed across two main work sessions. A mix of bug fixes, new features, and a big push to modernise the admin UI.

---

## Bug Fixes

**The `WalletLog` transaction relationship was pointing to the wrong model.** It was set to `User::class` instead of `Transaction::class`, which meant any eager load on wallet logs would silently return wrong data. Fixed.

**The `WalletCrediting` model was named `Wallets` inside the file.** The class name didn't match the file name or the table. Renamed it to `WalletCrediting`, added the explicit table name, and wired up the `user()` relationship while i was in there.

**The pending funding page was crashing** with "Attempt to read property field_value on string". This happened because when the `max_automatic_crediting_allowed` setting doesn't exist in the database, the controller falls back to the string `"SET MAX AMOUNT"` — and then the view tried to call `->field_value` on that string. Fixed the view to check `is_string($setting)` first.

**The pending creditings DataTable was throwing an AJAX error** because the route `fetch_crystal_pay_pending_transactions` was commented out in `routes/web.php`. Uncommented it.

**The product plan categories detail page was crashing** with "Illegal operator and value combination". The `RESELLER_PLAN_COUNT` env variable wasn't set, so `env()` returned `null`, and `where('plan_level', '<=', null)` blew up. Both `view_details()` and `view_details_by_automation()` now fall back to `UserPlan::all()` when the env var is missing.

**Double-refund bug in `transaction_refund()`.** The check for "already refunded" (`status == 2`) was running *after* some DB operations had already started. Moved it to the top so it bails out before touching anything.

**Null pointer errors on `reprocess_automation`** in the transactions DataTable. Two places were using `->` instead of `?->`, which would crash if no reprocess automation was set on a plan.

**Inertia was intercepting the `admin_total_balances` JSON endpoint** and throwing "All Inertia requests must receive a valid Inertia response". This happened because `HandleInertiaRequests` is applied globally to all web routes, and the Inertia JS client adds its header to fetch calls on Inertia pages. Fixed in three places: added `Accept: application/json` to the fetch call, added `X-Inertia: false` to the response header, and updated the middleware to short-circuit for any request that `expectsJson()`.

**Impersonating an unverified user would redirect to the PIN setup screen** because the email verification middleware blocked access. The impersonation flow now temporarily marks the user as verified for the session, and restores the original state when the admin exits impersonation. The PIN middleware also now skips its check entirely when an impersonation session is active.

**The landing page was showing `messages.Home`, `messages.About`, etc.** everywhere instead of actual text. All those translation keys were just missing from `lang/en/messages.php`. Same issue on the register page — labels like `messages.Fullname`, `messages.Password` were showing raw. Added all the missing keys.

---

## New Endpoints

i added a bunch of paginated JSON endpoints to replace the old DataTables server-side responses. The old approach was returning pre-formatted HTML inside JSON which made it impossible to build proper UI on top of. The new endpoints return clean data and let the frontend handle presentation.

- **`/admin/users/fetch_users_paginated`** — paginated user list with search across name, username, email, phone, plus date range filtering
- **`/admin/transactions/admin_fetch_transactions_paginated`** — paginated transactions with status, category, date range, and full-text search
- **`/admin/product_plans/fetch_product_plans_paginated`** — paginated product plans with search across plan name, network, category, automation
- **`/admin/product_plans/details/{id}/json`** — single plan details for the modal
- **`/transactions/details/{id}/json`** — single transaction details; admin-only fields are hidden from regular users
- **`/transactions/fetch_pending_creditings_paginated`** — paginated pending funding approvals with search and date filters
- **`/admin/product_plan_categories/admin_fetch_product_plan_categories_paginated`** — paginated categories with search
- **`/admin/product_plan_categories/details/{id}/json`** — single category details including plan count and commission settings
- **`/dashboard/transactions`** — user's own transaction history with filters

---

## UI Overhaul

I went through most of the admin pages and rebuilt them from scratch. The old pages were using DataTables with server-rendered HTML columns, which was messy and hard to work with. Everything is now Alpine.js with clean paginated JSON fetches.

**Admin Dashboard** — completely redesigned. The old plain white stat boxes are now gradient cards with icons. Added "All Time" and "This Month" filter options. There's a refresh button that re-fetches the stats without a page reload.

**Product Plans** — replaced the DataTable with a proper paginated table. You can search, filter by page size, and toggle visibility/public visibility directly in the row with a pill switch. The "Details" button opens a modal instead of navigating away.

**Transactions** — same treatment. Paginated table with status filter, category filter, date range, and a details modal that shows everything including admin-only messages.

**Users** — paginated table with search, date range, and per-page selector. Each row has a Manage button and an Access button for impersonation.

**Product Plan Categories** — rebuilt with the same pattern. Hot Sales and Visibility are both toggleable inline. The Details modal shows category info and links through to the full details page.

**Pending Funding Approvals** — the old page had a broken DataTable (route was commented out). Rebuilt it with the new paginated approach. Pending items show a Review button, approved ones just show a dash.

**User Settings** — full redesign. Tabbed layout with a sidebar on desktop and horizontal scrolling tabs on mobile. Flash messages are now proper styled banners instead of the old Bootstrap alert classes.

**Register page** — split layout with a branded left panel that shows the signup image (or falls back to the default auth image). The form is cleaner, password fields have eye-toggle buttons, and errors show inline under each field.

**Landing page** — full redesign. Dark top nav, sticky navbar with smooth scroll, hero slides with a proper gradient overlay and fade-in animation, about section as a two-column layout, stats section with glassmorphism cards, service cards with hover effects, reviews as dark cards with avatar initials (no more broken image references), contact section with icon cards, and a footer with social links.

**Livewire transactions table** — the "Details" link used to navigate to a separate page. It now opens a modal that fetches the transaction data inline.

---

## Database

The `wallet_creditings` migration was originally creating a table called `wallets` with just an auto-increment ID. Rebuilt it properly with UUID primary key, user foreign key, and all the columns it actually needs (transaction reference, status, bank details, amounts).

Added `api_id` to the network seeds (MTN=1, GLO=2, AIRTEL=3, 9MOBILE=4).

Added a `PendingApprovalsSeeder` that creates 20 test records for the pending funding approvals table — 14 pending and 6 already approved. The `DummyDataSeeder` also now seeds wallet creditings and pending approvals.

---

## Other

The `Template2Controller` was querying `AdminColorSetting` separately in every single method. Pulled that into a private `colorVars()` helper and now all the page methods just call that. Also fixed the auth layout for template2 — it was missing `$site_secondary_color` and `$site_logo`, and had no fallbacks if the DB records didn't exist.

