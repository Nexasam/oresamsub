# Catalogue and PWA Pricing Parity Design

## Objective

Ensure `GET /api/v2/catalogue` calculates customer pricing through the same pricing flow used by the authenticated PWA for data, airtime, cable, and electricity products.

For the same user and product plan, the catalogue must not independently infer a price from a product-plan column when the PWA would apply legacy pricing, customer-specific pricing, or percentage-discount rules.

## Scope

- Introduce one shared customer pricing resolver for catalogue and PWA plan presentation.
- Cover the four public catalogue services: data, airtime, cable, and electricity.
- Preserve existing catalogue authentication, plan identifiers, visibility filters, ordering, and service naming.
- Preserve both legacy and newer data pricing models.
- Preserve `ProductPlanCustomPricing` behavior wherever the PWA applies it.
- Do not change vending, provider selection, wallet debits, commissions, or transaction processing in this change.

## Pricing Contract

### Data

The shared resolver will use the existing PWA data calculation in `DataPlansService::get_customer_price_per_plan()`:

- Resolve the authenticated customer's plan level.
- For migrated/automated plans, use the matching `user_level_{level}_selling_price`, with the PWA's existing fallback.
- For legacy plans, calculate cost price plus the configured level profit.
- Apply a matching `ProductPlanCustomPricing` record as the final customer override.

The catalogue returns the resolved naira amount with `pricing_type: fixed`.

### Cable

Use the PWA's level-specific fixed selling price and apply a matching customer-specific price as the final override.

The catalogue returns the resolved naira amount with `pricing_type: fixed`.

### Airtime and Electricity

The PWA treats the level-specific selling-price field as a percentage discount and calculates the payable amount only after the customer supplies a purchase amount. The catalogue has no purchase amount, so it cannot truthfully return a final payable naira amount.

For compatibility, `price` will contain the same level-specific percentage value exposed by the PWA before an amount is supplied. The response will also include `pricing_type: percentage_discount` so API consumers do not mistake it for a fixed naira price.

Customer-specific pricing will follow the same precedence currently used by the PWA for the relevant stage of pricing. This change will not silently invent new override semantics.

## Architecture

Create a focused pricing service that accepts a user and product plan and returns a normalized result containing:

- the resolved numeric pricing value;
- `fixed` or `percentage_discount` pricing type;
- the customer plan level used.

The PWA plan-fetching flow and API V2 catalogue will both delegate to this resolver. The resolver may internally delegate data pricing to `DataPlansService` so legacy and migrated data rules remain authoritative.

The API controller remains responsible only for catalogue filtering and response formatting. Pricing precedence will not be duplicated in the controller.

## Response Compatibility

- Keep the existing `price` field.
- Add `pricing_type` to distinguish fixed naira prices from percentage discounts.
- Do not remove or rename existing catalogue fields.
- Round the returned numeric value consistently with the existing catalogue response.

## Error Handling

- Missing user-plan relationships fall back to level 1, matching existing defensive behavior.
- Missing legacy data profit settings retain the PWA's current fallback behavior.
- A missing customer-specific override simply falls through to the normal plan-level calculation.
- Pricing resolution must not make an otherwise valid catalogue request fail because an optional override is absent.

## Tests

Regression tests will prove that:

1. A migrated data plan returns the same customer-level price through PWA pricing and catalogue pricing.
2. A legacy data plan returns its cost-plus-profit PWA price in the catalogue.
3. A data customer-specific override takes precedence in the catalogue.
4. A cable customer-specific override takes precedence in the catalogue.
5. Airtime and electricity expose the PWA percentage value and identify it as `percentage_discount`.
6. Catalogue visibility and public-availability filters remain enforced.

The focused API test suite will be run first, followed by the relevant broader test suite.

## Non-Goals

- Redesigning provider automation pricing.
- Changing how API purchases debit wallets.
- Changing product-plan migrations or database schema.
- Adding an amount parameter to the catalogue endpoint.
- Modifying API V1 behavior.
