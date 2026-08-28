# Product Plan Provider Performance Modal Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Show complete provider mappings, API plan IDs, 30-day provider performance, and lazy-loaded plan management on the product-plan admin list, with 500 plans displayed by default.

**Architecture:** Extend the existing product-plan list query with eager-loaded provider automations and one grouped transaction-performance query. Extract the existing manage form into a reusable partial, retain the standalone page, and load the partial into one shared modal on demand.

**Tech Stack:** Laravel 13, Eloquent/query builder, Blade, Alpine-compatible browser JavaScript, Tailwind/Preline classes, Pest 4.

**Spec:** `docs/superpowers/specs/2026-08-28-product-plan-provider-performance-modal-design.md`

## Global Constraints

- Purchase routing and transaction processing must not change.
- Performance covers only identifiable transactions from the previous 30 days.
- Ranking is success rate descending, total count descending, provider name ascending.
- Page-size choices are 50, 100, 200, and 500; the default is 500.
- The standalone Manage URL remains functional.
- Only one management modal shell is rendered on the list page.

---

### Task 1: Provider and performance list data

**Files:**
- Modify: `app/Http/Controllers/ProductPlanController.php`
- Modify: `resources/views/admin/product_plans/index2.blade.php`
- Test: `tests/Feature/AdminProductPlanManagementTest.php`

**Interfaces:**
- Consumes: `ProductPlan::automation`, `ProductPlan::automationProductPlans`, `Transaction.automation_id`, `Transaction.status`.
- Produces: each paginated plan has `providerMappings` and `bestProviderPerformance` view attributes.

- [ ] **Step 1: Write failing list-data tests**

Create authenticated admin feature tests that seed one default provider, multiple `AutomationProductPlan` providers, recent and old transactions, then assert the list renders the public API ID, every provider plan ID, and the correct highest-rate provider with its success/total count.

- [ ] **Step 2: Run the focused tests and verify RED**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php`

Expected: failures because the list does not render API IDs, provider mappings, or performance.

- [ ] **Step 3: Implement bulk aggregation and view attributes**

Update `index2()` to eager-load `automationProductPlans.automation`, collect current-page plan IDs, issue one grouped transactions query using `created_at >= now()->subDays(30)` and non-null `automation_id`, calculate rates, rank deterministically, and attach display-ready provider/performance values without per-plan queries.

- [ ] **Step 4: Render the new list columns**

Add compact API ID, Providers, and Best provider · 30 days columns. Distinguish default/configured sources and show plan ID, priority, active state, success rate, and counts.

- [ ] **Step 5: Run focused tests and verify GREEN**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php`

Expected: provider and performance tests pass.

### Task 2: Safe 500-record default

**Files:**
- Modify: `app/Http/Controllers/ProductPlanController.php`
- Modify: `resources/views/admin/product_plans/index2.blade.php`
- Test: `tests/Feature/AdminProductPlanManagementTest.php`

**Interfaces:**
- Consumes: `per_page` query parameter.
- Produces: allow-listed integer page size with default 500.

- [ ] **Step 1: Write failing pagination tests**

Assert an absent `per_page` selects 500 and an unsupported value falls back to 500.

- [ ] **Step 2: Run tests and verify RED**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php --filter=page`

Expected: current controller default of 10 and current dropdown default fail.

- [ ] **Step 3: Implement the allow-list and correct Reset route**

Normalize `per_page` against `[50, 100, 200, 500]`, default to 500, update selected-state expressions, and point Reset to `admin.product_plans.index2`.

- [ ] **Step 4: Run tests and verify GREEN**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php --filter=page`

Expected: pagination tests pass.

### Task 3: Reusable management partial and lazy modal

**Files:**
- Create: `resources/views/admin/product_plans/partials/manage-form.blade.php`
- Modify: `resources/views/admin/product_plans/manage.blade.php`
- Modify: `resources/views/admin/product_plans/index2.blade.php`
- Modify: `app/Http/Controllers/ProductPlanController.php`
- Test: `tests/Feature/AdminProductPlanManagementTest.php`

**Interfaces:**
- Consumes: GET `/admin/product-plans/{id}/manage?modal=1`.
- Produces: modal-only Blade response for `modal=1`; unchanged full standalone page otherwise.

- [ ] **Step 1: Write failing modal response and list-shell tests**

Assert modal mode returns management controls without the application layout, standalone mode retains the page, and the list contains one modal shell plus Manage links carrying the modal URL.

- [ ] **Step 2: Run tests and verify RED**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php --filter=manage`

Expected: modal response and shell assertions fail.

- [ ] **Step 3: Extract and reuse the management form**

Move the management content and its price-calculation script into `partials/manage-form.blade.php`. Make `manage.blade.php` a layout wrapper around the partial. Return the partial directly when `modal=1`.

- [ ] **Step 4: Add the single lazy-loaded modal**

Convert Manage to a progressive-enhancement link with modal metadata. Add one shared modal shell, loading/error states, `fetch()` logic with same-origin credentials, close controls, Escape handling, and a fallback link to the standalone page.

- [ ] **Step 5: Run focused tests and verify GREEN**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php`

Expected: all new admin product-plan tests pass.

### Task 4: Regression and presentation verification

**Files:**
- Verify all files changed above.

**Interfaces:**
- Produces: formatted, regression-tested implementation.

- [ ] **Step 1: Format changed PHP files**

Run: `vendor/bin/pint app/Http/Controllers/ProductPlanController.php tests/Feature/AdminProductPlanManagementTest.php`

- [ ] **Step 2: Run focused and related suites**

Run: `php artisan test tests/Feature/AdminProductPlanManagementTest.php tests/Feature/Api/V2/BusinessApiTest.php`

Expected: zero failures.

- [ ] **Step 3: Compile Blade views**

Run: `php artisan view:cache`

Expected: views cache successfully without Blade parse errors.

- [ ] **Step 4: Review the final diff**

Confirm there are no unrelated edits, no per-plan performance queries, one modal shell, and no changes to purchase routing.
