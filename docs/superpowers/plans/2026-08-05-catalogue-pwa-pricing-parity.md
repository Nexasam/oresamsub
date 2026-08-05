# Catalogue and PWA Pricing Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make API V2 catalogue prices use exactly the authenticated PWA pricing rules for data, airtime, cable, and electricity.

**Architecture:** Extract the PWA's product pricing decisions into a focused `CustomerProductPricingService`. Both the PWA plan-fetch controller and API V2 catalogue delegate to it; data pricing continues to delegate to `DataPlansService` so migrated, legacy, and customer-specific rules stay unchanged.

**Tech Stack:** Laravel, Eloquent, PHP 8, Pest feature tests.

## Global Constraints

- Keep catalogue `id` mapped to `ProductPlan::api_id`.
- Keep existing catalogue visibility, public visibility, active status, ordering, and response fields.
- Keep `price` for compatibility and add `pricing_type`.
- Do not alter vending, provider selection, wallet debits, migrations, or API V1.

---

### Task 1: Lock the catalogue/PWA pricing contract with failing tests

**Files:**
- Modify: `tests/Feature/Api/V2/BusinessApiTest.php`

**Interfaces:**
- Exercises: `GET /api/v2/catalogue` with a Bearer API token.
- Proves: catalogue output matches literal PWA pricing outcomes for the same user and plan.

- [ ] **Step 1: Add regression fixtures and failing catalogue assertions**

Add tests for a customer-specific data override, legacy data cost-plus-profit, cable custom pricing, and airtime/electricity percentage metadata. Expected values must be literal fixture outcomes, not calculated by the production resolver inside the assertions.

- [ ] **Step 2: Run the focused tests and verify RED**

Run: `php artisan test tests/Feature/Api/V2/BusinessApiTest.php`

Expected: failures showing catalogue still returns the raw level/default field or lacks `pricing_type`.

### Task 2: Add the shared customer pricing resolver

**Files:**
- Create: `app/Services/Pricing/CustomerProductPricingService.php`
- Test: `tests/Feature/Api/V2/BusinessApiTest.php`

**Interfaces:**
- Consumes: `resolve(User $user, ProductPlan $plan): array`
- Produces: `['price' => float, 'pricing_type' => 'fixed'|'percentage_discount', 'plan_level' => int]`

- [ ] **Step 1: Implement minimal shared resolution**

For data, call `DataPlansService::get_customer_price_per_plan()` with the plan's product and network identifiers. For cable, resolve the customer's level field then apply `ProductPlanCustomPricing`. For airtime/electricity, return the same level percentage used by the PWA and identify it as `percentage_discount`. Default a missing user plan to level 1.

- [ ] **Step 2: Run focused tests**

Run: `php artisan test tests/Feature/Api/V2/BusinessApiTest.php`

Expected: pricing regression assertions pass after catalogue integration in Task 3.

### Task 3: Delegate catalogue and PWA plan presentation to the resolver

**Files:**
- Modify: `app/Http/Controllers/Api/V2/BusinessApiController.php`
- Modify: `app/Http/Controllers/DataController.php`
- Modify: `app/Http/Services/DataPlansService.php`
- Test: `tests/Feature/Api/V2/BusinessApiTest.php`

**Interfaces:**
- Catalogue injects `CustomerProductPricingService` into `catalogue()`.
- PWA data list and generic product-plan fetch call the same resolver without changing their response keys.

- [ ] **Step 1: Integrate catalogue**

Replace direct `user_level_{level}_selling_price` access with `CustomerProductPricingService::resolve()`. Keep `id => api_id`, map the returned price to `price`, and append `pricing_type`.

- [ ] **Step 2: Integrate PWA presentation**

Replace duplicated plan-level/custom-price branches in `DataController::fetch_product_plans()` with the shared resolver. Preserve dynamic amount calculation for airtime/electricity: when an amount is supplied, apply the resolver's percentage using the existing `ceil()` behavior; when absent, expose the percentage value as before.

Keep `DataPlansService::fetch_user_data_plans()` output unchanged while making its per-plan price call pass through the shared resolver without recursive data delegation.

- [ ] **Step 3: Run focused tests and verify GREEN**

Run: `php artisan test tests/Feature/Api/V2/BusinessApiTest.php`

Expected: all API V2 tests pass.

### Task 4: Verify compatibility and quality

**Files:**
- Verify all modified PHP files and relevant feature tests.

- [ ] **Step 1: Check syntax and formatting**

Run: `php -l app/Services/Pricing/CustomerProductPricingService.php`

Run: `php -l app/Http/Controllers/Api/V2/BusinessApiController.php`

Run: `php -l app/Http/Controllers/DataController.php`

- [ ] **Step 2: Run relevant suites**

Run: `php artisan test tests/Feature/Api/V2/BusinessApiTest.php tests/Feature/PwaApiAccessTest.php`

Expected: zero failures.

- [ ] **Step 3: Inspect the final diff**

Run: `git diff --check && git diff --stat && git status --short`

Expected: no whitespace errors and only pricing-parity implementation/test files changed beyond the committed specification.
