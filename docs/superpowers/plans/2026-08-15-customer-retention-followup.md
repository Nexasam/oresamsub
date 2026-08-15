# Customer Retention Follow-up Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the basic daily follow-up list with a paginated retention queue that identifies stale and suddenly inactive customers and records an auditable call history.

**Architecture:** A dedicated follow-up-call model stores immutable contact attempts. The controller builds aggregate SQL subqueries over successful transactions and applies validated GET filters before pagination. Blade renders the existing admin design system with a filter panel, prioritized customer table, and per-customer call-history/logging panel.

**Tech Stack:** Laravel, Eloquent/query builder, Blade, Alpine.js, Tailwind-compatible project utilities, Pest/PHPUnit, SQLite test database.

## Global Constraints

- Only transactions with `status = 1` count as successful purchases.
- Stale includes customers who have never purchased.
- Suddenly inactive means at least X successes in the Y-day window immediately preceding Z inactive days, with no success during the last Z days.
- The authenticated admin is always recorded as the caller.
- Call outcomes are answered, no answer, busy, unreachable, or wrong number.
- Follow-up statuses are follow up again, resolved/reactivated, or not interested.

---

### Task 1: Retention query and filters

**Files:**
- Create: `tests/Feature/DailyCustomerFollowupTest.php`
- Modify: `app/Http/Controllers/DailyCustomerFollowupController.php`
- Modify: `routes/web.php`

**Interfaces:**
- Consumes: `users`, `transactions`, authenticated admin middleware.
- Produces: GET `admin.daily_customer_followup.index` with validated filters and paginated `customers` containing aggregate purchase fields and a retention segment.

- [ ] Write feature tests for successful-only activity, stale/never-purchased inclusion, POS filtering, last-purchase period filtering, and X/Y/Z suddenly-inactive classification.
- [ ] Run `php artisan test tests/Feature/DailyCustomerFollowupTest.php` and verify failures occur because the new query behavior is absent.
- [ ] Implement request validation, aggregate subqueries, filtering, prioritization, and pagination.
- [ ] Re-run the focused tests and make them pass.

### Task 2: Auditable call logs

**Files:**
- Create: `database/migrations/2026_08_15_000000_create_customer_followup_calls_table.php`
- Create: `app/Models/CustomerFollowupCall.php`
- Modify: `app/Models/User.php`
- Modify: `app/Http/Controllers/DailyCustomerFollowupController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/DailyCustomerFollowupTest.php`

**Interfaces:**
- Produces: `CustomerFollowupCall` relationships and POST `admin.daily_customer_followup.calls.store`.
- Stores: `customer_id`, `called_by`, `outcome`, `feedback`, `followup_status`, `next_followup_at`, timestamps.

- [ ] Add failing tests for admin attribution, valid call persistence, validation failures, and ordinary-user authorization.
- [ ] Run the focused tests and verify expected failures.
- [ ] Add the migration/model/relationships/controller action/route with strict validation.
- [ ] Re-run the focused tests and make them pass.

### Task 3: Retention dashboard UI

**Files:**
- Modify: `resources/views/admin/daily_customer_followup/index.blade.php`
- Test: `tests/Feature/DailyCustomerFollowupTest.php`

**Interfaces:**
- Consumes: paginated `customers`, normalized `filters`, call-history relationships.
- Produces: accessible filters, priority table, call/WhatsApp links, call-entry form, and history timeline.

- [ ] Add failing response assertions for filter controls, retention labels, customer aggregates, and call-log fields.
- [ ] Run the focused tests and verify the view assertions fail.
- [ ] Replace the legacy list with the responsive dashboard using existing project classes and Alpine disclosure state.
- [ ] Re-run focused tests, then run the full Laravel test suite.

### Task 4: Verification and cleanup

**Files:**
- Review all files above.

- [ ] Run PHP syntax checks on changed PHP files.
- [ ] Run `php artisan route:list --name=admin.daily_customer_followup`.
- [ ] Run `php artisan test tests/Feature/DailyCustomerFollowupTest.php` and the full test suite.
- [ ] Inspect the final diff for unrelated changes, unsafe queries, missing indexes, and accidental debug code.
