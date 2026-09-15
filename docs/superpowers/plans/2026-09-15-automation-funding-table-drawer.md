# Automation Funding Table and Drawer Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the automation-funding card grid with a compact table and one reusable lazy-loaded right-side management drawer.

**Architecture:** The index response renders summary data only. A new read-only controller endpoint renders the selected automation's existing forms into a Blade partial, and small page JavaScript loads that partial into a single accessible drawer shell.

**Tech Stack:** Laravel, Blade, Tailwind utility classes, vanilla JavaScript, Pest.

---

### Task 1: Specify table and lazy drawer behavior

**Files:**
- Modify: `tests/Feature/AutomationWalletFundingTest.php`

- [ ] **Step 1: Write failing rendering tests**

Assert that the index includes `data-manage-funding`, one `automation-funding-drawer`, and table headings, but does not inline the configuration field for every automation. Assert that the new manage endpoint returns the selected automation name, `balance_response_path`, customer action, correction action, toggle action, and funding action.

- [ ] **Step 2: Verify the tests fail**

Run: `php artisan test tests/Feature/AutomationWalletFundingTest.php --filter='funding page|management drawer'`

Expected: failure because the current page is a card grid and the manage route does not exist.

### Task 2: Build the drawer endpoint and partial

**Files:**
- Modify: `app/Http/Controllers/AutomationWalletFundingController.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/automations/partials/funding-manage.blade.php`

- [ ] **Step 1: Add the read-only manage endpoint**

Add `manage(Automation $automation): View`, eager-load `walletFunding`, and return the funding management partial. Register `GET admin/automation-funding/automations/{automation}/manage` under the existing admin middleware and route-name group.

- [ ] **Step 2: Move all management controls to the partial**

Render the configuration form for every automation. When a funding record exists, also render status/error details, Create Customer, Refresh, Toggle, Correct Balance, and Fund forms using the existing named POST routes.

### Task 3: Replace cards with the compact table and drawer shell

**Files:**
- Modify: `resources/views/admin/automations/funding.blade.php`

- [ ] **Step 1: Render the summary table**

Use the `admin/product_plans2` table classes and columns: Automation, Current Balance, Threshold, Default Funding, Securewave Customer, Auto Funding, Stock, Last Updated, and Manage.

- [ ] **Step 2: Add one reusable responsive drawer**

Add a fixed backdrop and right-aligned panel with sticky header, independently scrolling content, loading/error states, close button, and full-width mobile behavior.

- [ ] **Step 3: Add drawer interaction**

Intercept Manage buttons, fetch their `data-manage-url` with same-origin credentials and `Accept: text/html`, inject the response, lock body scrolling, close on backdrop/close/Escape, and restore focus to the opener.

- [ ] **Step 4: Verify**

Run the focused feature file, then `php artisan test --compact`, `php artisan route:list --name=admin.automation-funding`, and `git diff --check`.
