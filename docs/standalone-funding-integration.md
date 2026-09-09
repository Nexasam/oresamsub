# Standalone Master Wallet

OresamSub now keeps the authoritative master wallet for every standalone. SecureWave Kolomoni payments are matched by virtual account, recorded once, and credited centrally using the settled amount.

An administrator creates the standalone at `/admin/standalones` and securely transfers the one-time bootstrap API token. It expires after 20 minutes and can only be exchanged at `POST /api/v1/standalone/credentials/api-token/rotate`. The returned `ors_live_...` operational token is used for virtual-account and wallet endpoints.

```text
GET  /api/v1/standalone/virtual-account
POST /api/v1/standalone/virtual-account
GET  /api/v1/standalone/wallet
POST /api/v1/standalone/wallet/deduct
GET  /api/v1/standalone/wallet/transactions
GET  /api/v1/standalone/wallet/transactions/{reference}
```

A deduction requires only a positive decimal `amount`, unique `reference`, and plain-text `purpose`. Replaying identical details is idempotent; conflicting reuse returns HTTP 409, and insufficient funds returns HTTP 422 without a debit.

OresamSub sends no funding callback and generates no webhook secret. The standalone performs its own downstream service integration only after OresamSub confirms the wallet deduction. See `docs/standalone-multi-tenant-integration-handover.txt` for the complete developer contract.
