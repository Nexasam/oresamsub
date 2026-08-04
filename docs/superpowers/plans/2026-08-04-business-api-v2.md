# Business API V2 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Deliver a secure, versioned reseller API and branded developer documentation.

**Architecture:** A dedicated route file and V2 controller isolate the public contract from legacy endpoints. A token middleware attaches the matching `User`; the controller delegates purchases to the existing `ProductsService` and maps all output to a safe public envelope.

**Tech Stack:** Laravel, Pest, Blade, OpenAPI 3.1

## Global Constraints

- Preserve every existing API route.
- Authenticate from `users.api_token` using a Bearer header.
- Phase one purchase services are data and airtime.
- Never expose internal automation or provider fields.

---

### Task 1: Public API contract

**Files:**
- Create: `routes/api_v2.php`
- Create: `app/Http/Middleware/AuthenticateBusinessApi.php`
- Create: `app/Http/Controllers/Api/V2/BusinessApiController.php`
- Create: `tests/Feature/Api/V2/BusinessApiTest.php`
- Modify: `bootstrap/app.php`

- [ ] Write failing tests for authentication, catalogue pricing, wallet, purchase idempotency and transaction ownership.
- [ ] Run the focused test file and confirm the missing routes fail.
- [ ] Register `/api/v2`, middleware and rate limiting.
- [ ] Implement the smallest controller contract that passes the tests.
- [ ] Run the focused test file and confirm it passes.

### Task 2: Developer portal

**Files:**
- Create: `resources/views/developers/index.blade.php`
- Create: `resources/api/oresamsub-v2.openapi.json`
- Modify: `routes/web.php`
- Test: `tests/Feature/Api/V2/BusinessApiTest.php`

- [ ] Write failing tests for the portal and OpenAPI document.
- [ ] Run them and confirm the missing endpoints fail.
- [ ] Implement the branded documentation page and serve the OpenAPI document.
- [ ] Run API tests, route checks, Blade compilation and diff validation.
