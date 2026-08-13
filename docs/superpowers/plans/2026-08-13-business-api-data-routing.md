# Business API Data Routing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow `buy_data_service_one_api()` to use the exact product plan's legacy automation when the plan has not migrated to active `AutomationProductPlan` routing.

**Architecture:** Detect the flow from the presence of an active `AutomationProductPlan`. New-flow plans retain the per-user mapping requirement; legacy plans construct vending details from `ProductPlan::automation` and `automation_product_plan_id`.

**Tech Stack:** PHP 8, Laravel, Eloquent, Pest

## Global Constraints

- Preserve catalogue-price charging.
- Do not auto-switch to another product plan.
- Preserve new-flow provider validation.

---

### Task 1: Route legacy and new-flow plans correctly

**Files:**
- Modify: `app/Http/Services/Api/v1/VendorUsersApi/Products/ProductsService.php`
- Test: `tests/Feature/Api/V2/BusinessApiTest.php`

**Interfaces:**
- Consumes: `ProductPlan`, `AutomationProductPlan`, and optional `UserProductPlanAutomation` records.
- Produces: the provider model passed as `automation_details` to `AutomationLogic::initiateDataPurchase(array $data)`.

- [ ] Add a failing regression test proving a legacy plan without a user-specific mapping is not rejected as unconfigured.
- [ ] Run the focused test and confirm the current 422 provider-configuration response.
- [ ] Detect active `AutomationProductPlan` rows for the selected plan.
- [ ] Require `UserProductPlanAutomation` only for a new-flow plan.
- [ ] For a legacy plan, pass the plan's related `Automation` with the plan's `automation_product_plan_id` to vending.
- [ ] Run the focused regression tests.
- [ ] Run the complete Business API feature suite and `git diff --check`.
