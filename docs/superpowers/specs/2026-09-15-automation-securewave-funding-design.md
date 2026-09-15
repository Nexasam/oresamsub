# Automation Securewave Funding Design

## Goal

Give administrators one place to configure each automation as a Securewave customer, monitor the provider balance reported by successful transactions, and fund that customer manually or automatically from the master Securewave merchant wallet.

## Balance authority

The automation provider response is the authority for stock balance. Each funding configuration stores a dot-notation `balance_response_path`, such as `data.balance_after`. The resolver searches the automation's successful transactions (`status = 1`) newest-first, decodes `admin_screen_message`, and uses the newest numeric value found at that path.

A valid response replaces `last_balance` and records the response transaction and timestamp. A missing path, invalid JSON, or non-numeric value never overwrites the last confirmed balance. Before any automatic-funding decision, the resolver refreshes from transaction history. New successful transaction persistence can invoke the same resolver without provider-specific parsing.

An administrator may set an opening/default balance and manually correct the current balance. The next valid successful provider response supersedes either value.

## Data model

`automation_wallet_fundings` remains the per-automation configuration and gains:

- `default_balance` for initialization;
- `balance_response_path` for provider-specific response extraction;
- Securewave customer reference and creation timestamp;
- balance source, last balance synchronization timestamp, and source transaction ID;
- last error and last funding timestamp.

There is at most one funding configuration per automation. Existing `threshold`, `amount_to_fund`, `automatic_funding`, `linked_customer_email`, `active`, and `last_balance` fields retain their meanings. The customer email becomes nullable until provisioning.

## Securewave integration

A focused HTTP client reads the existing `FundingOption` credentials for `securewaveng`. It provides master-balance lookup, customer creation, and customer funding. Requests have bounded connection and response timeouts and return normalized results. Securewave endpoint URLs live in application configuration and are not environment variables.

Customer creation is an explicit admin action. It sends the automation name and admin-entered unique email and persists the returned customer reference when present. Duplicate provisioning is rejected locally.

Both manual and automatic funding use one service operation. It validates the amount, requires a provisioned customer, locks the funding row, verifies the master wallet can cover the amount, and calls Securewave. Only a confirmed provider success changes local state. The provider's returned customer balance is preferred; otherwise the confirmed amount is added to the last known balance. Failures preserve the balance and store a safe error message.

## Automatic funding

The five-minute command refreshes configured balances from successful transaction history and processes only active records with `automatic_funding = true` and `last_balance <= threshold`. The existing unconditional `exit` is removed. Per-record locks and the scheduler's overlap protection prevent duplicate concurrent attempts.

## Admin interface

An admin-only funding page lists every automation, including unconfigured ones. Each row displays customer status, current/default balance, threshold, default funding amount, response path, balance source, last update, low-stock state, auto-funding state, and the last error.

Administrators can:

- create or update the funding configuration;
- provision the Securewave customer explicitly;
- refresh balance from the latest matching successful transaction;
- manually correct the current balance;
- enable or disable automatic funding;
- manually fund an editable amount prefilled from `amount_to_fund`.

All state-changing routes are POST/PATCH, protected by existing admin middleware and CSRF validation, and return clear flash messages.

## Error handling and observability

Provider transport failures, malformed responses, missing credentials, insufficient master balance, and invalid response paths are reported without changing confirmed balances. Logs omit API credentials and include automation IDs and operation context. The page keeps the latest safe error visible until a successful relevant operation clears it.

## Testing

Automated tests cover path extraction; newest matching successful transaction selection; rejection of unsuccessful, malformed, missing, and non-numeric responses; configuration and manual correction; customer provisioning; manual funding; insufficient merchant funds; failed provider responses; returned-balance preference; auto-funding toggles and thresholds; and removal of the disabled scheduled flow.
