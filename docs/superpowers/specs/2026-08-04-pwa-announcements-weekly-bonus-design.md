# PWA Announcements and Weekly Volume Bonus Design

## Outcome

The PWA opens active announcements in a polished modal. A customer may dismiss the modal or suppress automatic opening for 24 hours on the current browser; a persistent dashboard trigger reopens it at any time.

Weekly transaction-volume rewards extend the existing bonus campaign system. A campaign may target everyone or selected customers, qualify all successful purchase categories or selected categories, and credit either a flat amount or a capped percentage of weekly successful transaction volume into the existing bonus wallet.

## Weekly accounting rules

- Weeks use Africa/Lagos time and run Monday 00:00 through Sunday 23:59:59.
- Only `transactions.status = 1` contributes to volume.
- Category scope is either all transaction categories or an explicit list such as `data`.
- Percentage rewards require a monetary cap; flat rewards use the configured value directly.
- A unique campaign/customer/week record makes processing idempotent.
- Campaign end date is optional for indefinitely running weekly campaigns.
- Rewards use existing bonus logs and bonus-wallet conversion accounting.
- A scheduled command processes the completed previous week every Monday after midnight and can be rerun safely.

## Boundaries

The announcement snooze is device-local and does not require a database write. Weekly qualification state is financial data and is stored server-side with unique constraints and audit metadata.
