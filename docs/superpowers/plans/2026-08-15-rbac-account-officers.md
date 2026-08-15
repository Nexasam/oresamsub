# RBAC and Account Officers Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add compatible multi-role authorization and use it to deliver secure account-officer ownership, weighted customer allocation, scoped follow-ups, email notifications, and performance reporting.

**Architecture:** Keep `users.role_id` as the legacy primary role while adding role and permission pivots. Resolve effective permissions through a focused service/middleware with a protected Super Admin bypass. Store current and historical customer ownership in assignment records, execute weighted allocation through a transactional service, and scope the retention query by effective permission and active ownership.

**Tech Stack:** Laravel, Eloquent, Blade/Alpine, queued mail/notifications, Pest/PHPUnit, SQLite tests.

## Global Constraints

- Preserve `users.role_id` and existing customer `User` roles.
- Effective permissions are the union of the primary role and supplementary roles.
- `adebsholey4real@gmail.com` is the protected Super Admin.
- Only unassigned customers are included in ordinary allocation runs.
- Active officer weights must total exactly 100%.
- Officers can access only their assigned customers unless they also hold `followups.view_all`.
- Existing customer follow-up behavior and tests must remain green.

---

### Task 1: Compatible multi-role authorization foundation

**Files:**
- Create: `database/migrations/2026_08_15_010000_create_rbac_pivot_tables.php`
- Create: `app/Http/Middleware/RequirePermission.php`
- Modify: `app/Models/User.php`, `app/Models/Role.php`, `app/Models/Permission.php`, `bootstrap/app.php`
- Test: `tests/Feature/RolePermissionTest.php`

**Interfaces:**
- Produces: `User::roles()`, `User::effectiveRoles()`, `User::hasPermission(string): bool`, route middleware alias `permission`.

- [ ] Write failing tests proving primary-role compatibility, supplementary-role union, denial without permission, and protected Super Admin bypass.
- [ ] Run `php artisan test tests/Feature/RolePermissionTest.php` and confirm failures are caused by missing multi-role behavior.
- [ ] Add normalized `role_user` and `role_permission` pivots while retaining legacy permission records.
- [ ] Implement relationships, effective-permission resolution, and middleware.
- [ ] Run the focused tests until green.

### Task 2: Permission catalogue and protected role management

**Files:**
- Modify: `config/permissions.php`, `app/Http/Controllers/RoleController.php`, `resources/views/admin/roles/permissions.blade.php`, `routes/web.php`
- Test: `tests/Feature/RolePermissionTest.php`

**Interfaces:**
- Produces: grouped capability catalogue and Super-Admin-only role permission update action.

- [ ] Add failing tests for permission replacement, non-Super-Admin rejection, and protected-email access.
- [ ] Run focused tests and observe expected authorization failures.
- [ ] Replace legacy CRUD-column writes with synchronized normalized permissions while retaining read compatibility.
- [ ] Protect role management routes and render grouped capabilities.
- [ ] Re-run focused tests until green.

### Task 3: Officer profiles and assignment history

**Files:**
- Create: `database/migrations/2026_08_15_020000_create_account_officer_tables.php`
- Create: `app/Models/AccountOfficerProfile.php`, `app/Models/CustomerOfficerAssignment.php`, `app/Models/OfficerAssignmentBatch.php`
- Modify: `app/Models/User.php`, `app/Models/CustomerFollowupCall.php`, follow-up-call migration with a new follow-up migration
- Test: `tests/Feature/AccountOfficerTest.php`

**Interfaces:**
- Produces: active officer profiles, one active assignment per customer, assignment history, and call-time officer attribution.

- [ ] Write failing relationship/history and deactivation tests.
- [ ] Run focused tests and verify missing-schema failures.
- [ ] Add indexed schema, models, casts, and relationships.
- [ ] Re-run focused tests until green.

### Task 4: Weighted unassigned-customer allocation

**Files:**
- Create: `app/Services/AccountOfficers/WeightedCustomerAllocator.php`
- Create: `app/Http/Controllers/AccountOfficerController.php`
- Create: `resources/views/admin/account_officers/index.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/AccountOfficerTest.php`

**Interfaces:**
- Produces: preview and execution endpoints; `WeightedCustomerAllocator::preview()` and `::allocate()`.

- [ ] Add failing tests for 25/30/45 weighted results, 100% validation, unassigned-only behavior, idempotency, and protected-email authorization.
- [ ] Run tests and verify expected failures.
- [ ] Implement deterministic weighted distribution inside a locked transaction and store batch/history records.
- [ ] Build the protected management screen with officer activation, weights, preview, execute, and deactivated-officer redistribution.
- [ ] Re-run focused tests until green.

### Task 5: Scoped officer follow-up dashboard and reporting

**Files:**
- Modify: `app/Http/Controllers/DailyCustomerFollowupController.php`, `resources/views/admin/daily_customer_followup/index.blade.php`, `routes/web.php`
- Create: `app/Notifications/CustomersAssignedNotification.php`
- Test: `tests/Feature/AccountOfficerTest.php`, `tests/Feature/DailyCustomerFollowupTest.php`

**Interfaces:**
- Produces: officers see only assigned portfolios; global viewers filter by officer; allocation notifications; period performance summary.

- [ ] Add failing tests for portfolio isolation, cross-customer call denial, global-view permission, notification dispatch, and period performance metrics.
- [ ] Run tests and verify expected failures.
- [ ] Apply ownership scopes and permission middleware, store assigned-officer context on calls, queue allocation summaries, and render officer filters/performance.
- [ ] Re-run both focused test files until green.

### Task 6: Verification

**Files:** Review all files above.

- [ ] Run syntax checks and `php artisan route:list` for permission/officer/follow-up routes.
- [ ] Run focused RBAC, officer, and retention tests.
- [ ] Run the complete Laravel test suite.
- [ ] Run `git diff --check` and inspect the diff for unsafe access paths, missing indexes, accidental legacy breakage, and debug code.
