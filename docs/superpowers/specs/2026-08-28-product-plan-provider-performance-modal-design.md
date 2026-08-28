# Product Plan Provider Performance and Management Modal Design

## Goal

Enhance `admin/product_plans2` so administrators can see every provider mapping and plan identifier, identify the best-performing provider over the previous 30 days, load up to 500 plans by default, and manage a selected plan without leaving the list.

## Scope

The change covers the existing Laravel admin product-plan list and management views. It does not change purchase routing, provider selection, transaction processing, or historical transaction data.

## Product-plan list data

Each product-plan row will expose:

- The plan's public `api_id`.
- The default provider from `product_plans.automation_id`, with its `product_plans.automation_product_plan_id` and a `Default` source badge.
- Every configured provider from `automation_product_plans`, with its `provider_plan_id`, priority, cost, and active state.
- The best-performing identifiable provider over the previous 30 days.

Default and configured provider mappings remain separate records when their provider plan IDs differ. If the same automation and provider plan ID occur in both sources, the UI may present a single entry carrying both source labels to avoid misleading duplication.

The controller will eager-load automation details for configured providers. Provider mappings will be prepared in bulk, without per-row database queries.

## Performance calculation

Provider performance uses transactions satisfying all of these conditions:

- `transactions.product_plan_id` matches the displayed product plan.
- `transactions.automation_id` is not null.
- `transactions.created_at` is within the previous 30 days.

For each product-plan/provider pair:

- `total_count` is the number of identifiable transactions.
- `successful_count` is the number whose status represents success (`1`).
- `success_rate` is `successful_count / total_count * 100`.

The best provider is the provider with the highest success rate. Equal rates are resolved by the larger total transaction count, then by provider name for deterministic display. The UI shows the percentage and `successful_count / total_count`. Plans without identifiable transactions show `No tracked transactions`.

The aggregation will be one grouped query covering the product plans on the current page. It will not issue one analytics query per plan.

## Pagination and filtering

The list controller will accept only `50`, `100`, `200`, or `500` as page sizes and default to `500`. The dropdown will visibly select 500 when `per_page` is absent. Existing filters and query-string pagination remain available. The Reset link will return to `admin.product_plans.index2`.

## Management modal

The current management form will be extracted into a reusable Blade partial. The standalone `product-plans/{id}/manage` page will continue rendering that partial inside the existing layout as a fallback.

On `admin/product_plans2`, Manage remains a real link to the standalone route for progressive enhancement. JavaScript intercepts an ordinary click, opens one shared large modal, and fetches modal-only content from the management route. Only the selected plan is loaded; the page will not render one management form for each of the 500 rows.

The modal will show a loading state, an error state with a link to the standalone page, a close control, and scrollable content. Existing forms retain their current server-side routes and CSRF protection. Normal form submissions redirect back according to the existing controller behavior, so validation and flash messages remain server-managed.

## Error handling

- A failed modal request displays a concise error inside the modal and preserves the standalone Manage link.
- A missing plan continues to return 404.
- Analytics absence is an empty state, not an error.
- Transactions with null or unknown automation IDs are excluded from provider ranking because their processor cannot be attributed reliably.

## Testing

Feature tests will verify:

- The list defaults to 500 records per page and restricts unsupported page-size input.
- Public API IDs are rendered.
- Default and configured providers and their respective plan IDs are rendered.
- The 30-day provider ranking selects the highest success rate, includes counts, excludes older transactions, and handles no identifiable transactions.
- The management route can render modal-only content while retaining the standalone page.
- The list contains one lazy-loaded modal shell and Manage links with the required modal-loading metadata.

Existing product-plan update and provider-management tests, where present, remain valid. New behavior will be developed test-first.
