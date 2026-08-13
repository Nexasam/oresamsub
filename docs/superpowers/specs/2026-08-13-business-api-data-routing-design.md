# Business API Data Routing Design

The Business API data purchase flow must select its provider using the same migration boundary as `DataController::processDataViaAutomations()`.

- A plan with at least one active `automation_product_plans` row uses the new routing flow and requires an active `UserProductPlanAutomation` whose `UserAutomation` relation exists.
- A plan without an active `automation_product_plans` row uses the legacy fields on that exact `product_plans` row: `automation_id`, `automation_product_plan_id`, and the related `automation` credentials.
- The legacy fallback must not search for a different plan or provider.
- A provider-configuration error is returned only when the provider data required by the selected flow is missing.
- Customer pricing remains resolved through `CustomerProductPricingService` and is independent of provider routing.

Regression tests cover successful selection of the legacy provider details and continued rejection of incomplete new-flow user mappings.
