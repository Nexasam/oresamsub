# Automation Securewave Funding Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build configurable, response-driven automation balance monitoring with manual and automatic Securewave customer funding.

**Architecture:** A balance resolver owns transaction-response extraction, a Securewave HTTP client owns provider transport, and the funding service owns thresholds, locking, and state changes. An admin controller and Blade page expose configuration and explicit operations without placing provider logic in the UI.

**Tech Stack:** Laravel, Eloquent, Laravel HTTP client, Blade/Tailwind, Pest/PHPUnit, SQLite test database.

**Spec:** `docs/superpowers/specs/2026-09-15-automation-securewave-funding-design.md`

## Global Constraints

- Successful transactions are identified by `transactions.status = 1`.
- Raw provider responses are read from `transactions.admin_screen_message`.
- Invalid or absent balances never replace the last confirmed balance.
- All funding mutations require a confirmed successful Securewave response.
- Existing Securewave credentials remain in the `securewaveng` `FundingOption` record.

---

### Task 1: Persist funding configuration and balance provenance

**Files:**
- Create: `database/migrations/2026_09_15_000000_expand_automation_wallet_fundings_table.php`
- Modify: `app/Models/AutomationWalletFunding.php`
- Modify: `app/Models/Automation.php`
- Test: `tests/Feature/AutomationWalletFundingTest.php`

**Interfaces:**
- Produces: one nullable `Automation::walletFunding()` relation and casts for monetary, boolean, and timestamp fields.

- [ ] Write a failing test that creates an automation funding configuration with a response path, default balance, and provenance fields and reads it through `Automation::walletFunding()`.
- [ ] Run `php artisan test tests/Feature/AutomationWalletFundingTest.php` and confirm the missing columns/relation failure.
- [ ] Add the migration, casts, and relations; make `linked_customer_email` nullable and `automation_id` unique.
- [ ] Re-run the focused test and confirm it passes.

### Task 2: Resolve balances from successful transaction history

**Files:**
- Create: `app/Services/Automation/AutomationBalanceResolver.php`
- Modify: `app/Models/Transaction.php`
- Test: `tests/Feature/AutomationWalletFundingTest.php`

**Interfaces:**
- Produces: `resolve(AutomationWalletFunding $funding): ?float` and `sync(AutomationWalletFunding $funding): bool`.
- Consumes: `balance_response_path`, `automation_id`, and successful transaction `admin_screen_message` JSON.

- [ ] Add failing tests proving the resolver uses the newest successful numeric matching response and skips failures, invalid JSON, missing paths, and non-numeric values.
- [ ] Run the focused tests and confirm they fail because the resolver is absent.
- [ ] Implement newest-first chunked lookup with `data_get`, numeric validation, and provenance updates.
- [ ] Re-run the focused tests and confirm they pass.

### Task 3: Centralize Securewave HTTP operations

**Files:**
- Create: `app/Services/Securewave/SecurewaveClient.php`
- Modify: `config/services.php`
- Test: `tests/Feature/AutomationWalletFundingTest.php`

**Interfaces:**
- Produces: `merchantBalance(): array`, `createCustomer(string $name, string $email): array`, and `fundCustomer(string $email, float $amount): array` normalized to `ok`, `message`, `data`, and balance fields.

- [ ] Add failing HTTP-fake tests for authentication headers, request bodies, successful normalization, malformed responses, and non-success responses.
- [ ] Run focused tests and confirm the missing client failure.
- [ ] Implement the client using Laravel's HTTP client with connection and response timeouts and configurable URLs.
- [ ] Re-run the focused tests and confirm they pass.

### Task 4: Implement safe manual and automatic funding

**Files:**
- Modify: `app/Services/Automation/WalletAutoFundingService.php`
- Modify: `app/Console/Commands/RunWalletAutoFunding.php`
- Test: `tests/Feature/AutomationWalletFundingTest.php`

**Interfaces:**
- Produces: `fund(AutomationWalletFunding $funding, float $amount, string $source): array`, `process(AutomationWalletFunding $funding): array`, and an operational `run(): void`.
- Consumes: `AutomationBalanceResolver` and `SecurewaveClient`.

- [ ] Add failing tests for disabled auto-funding, above-threshold balances, insufficient master funds, provider failure, returned customer balance, fallback addition, and duplicate locking behavior.
- [ ] Run focused tests and confirm behavior fails against the current disabled/raw-cURL service.
- [ ] Replace raw cURL and remove `exit`; implement refresh-before-threshold, row locking, shared manual/automatic funding, error persistence, and normalized results.
- [ ] Re-run the focused tests and confirm they pass.

### Task 5: Add admin configuration and operations

**Files:**
- Create: `app/Http/Controllers/AutomationWalletFundingController.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/automations/funding.blade.php`
- Modify: `resources/views/admin/automations/index.blade.php`
- Test: `tests/Feature/AutomationWalletFundingTest.php`

**Interfaces:**
- Produces named admin routes for index, configuration, customer creation, response refresh, manual correction, auto toggle, and manual funding.
- Consumes: the funding model, resolver, client, and funding service.

- [ ] Add failing feature tests for admin access, rendering all automations, validated configuration, provisioning, refreshing, manual correction, toggling, and manual funding.
- [ ] Run focused tests and confirm route/controller failures.
- [ ] Implement validated controller actions and admin routes using route-model binding.
- [ ] Build the funding table and forms, plus an entry link from the automation index.
- [ ] Re-run focused tests and confirm they pass.

### Task 6: Integrate immediate response-driven updates and verify

**Files:**
- Create: `app/Observers/TransactionObserver.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/AutomationWalletFundingTest.php`

**Interfaces:**
- Produces: an observer that invokes the shared resolver after a successful automation transaction is created or becomes successful.

- [ ] Add a failing test proving a newly saved successful transaction immediately updates its configured automation balance while a failed transaction does not.
- [ ] Run the focused test and confirm the balance remains unchanged.
- [ ] Register and implement the observer with change guards that avoid recursion.
- [ ] Run the focused test, then `php artisan test`, and confirm zero failures.
- [ ] Run `php artisan route:list --name=admin.automation-funding` and inspect all expected protected routes.
- [ ] Run `php artisan schedule:list` and confirm the five-minute wallet funding command remains scheduled.
