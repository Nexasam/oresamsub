# OresamSub Business API V2 Design

## Goal

Expose a stable API for third-party data and airtime reseller websites without exposing provider internals or changing the existing API.

## Contract

The API is mounted at `/api/v2`, authenticates `Authorization: Bearer <token>` against `users.api_token`, and always returns `success`, `message`, `data`, `meta`, and `errors`. It exposes catalogue, wallet, one-fit-all purchase, and transaction reconciliation endpoints. Each plan is priced from the authenticated user's pricing level and only active, visible, public plans are returned.

Purchase references are supplied by the client. Repeating the same reference and same purchase returns the existing transaction; reusing it for different purchase details returns HTTP 409. Phase one activates data and airtime. The contract can add cable and electricity without introducing another purchase endpoint.

## Documentation

A branded public developer portal at `/developers` provides quick start, authentication, endpoint reference, request and response examples, error semantics, and code samples. `/api/v2/openapi.json` is the machine-readable OpenAPI 3.1 source.

## Security

Tokens are never accepted in query strings or logged. Requests are rate limited by authenticated user or IP. Responses exclude automation identifiers, cost prices, provider responses, admin messages, and transaction PINs.
