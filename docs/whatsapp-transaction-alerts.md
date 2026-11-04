# WhatsApp failed and refunded transaction alerts

> Status: implementation complete and paused on 2026-11-03. Return to this handoff when the Meta Utility templates are ready for approval/testing.

## Implemented scope

- Failed and refunded transactions automatically queue one WhatsApp alert.
- An admin can manually queue the same protected alert from a failed/refunded transaction detail page.
- An admin can open **Admin → WhatsApp Messages**, select one eligible customer, compose a message, and send it to that single customer.
- Customer consent commands and Meta delivery/read/failure webhooks work on the live `/api/webhook/whatsapp` endpoint.
- Delivery attempts are recorded in the database and protected against duplicate transaction alerts.
- The implementation uses the existing cron-driven default queue; it does not require a permanent queue worker.

## Important implementation files

- `app/Jobs/SendWhatsappTransactionAlert.php`
- `app/Observers/TransactionWhatsappNotificationObserver.php`
- `app/Services/Whatsapp/TransactionWhatsappMessage.php`
- `app/Services/Whatsapp/WhatsappNotificationWebhookHandler.php`
- `app/Http/Controllers/Admin/WhatsappMessageController.php`
- `resources/views/admin/whatsapp_messages/index.blade.php`
- `resources/views/transaction_details.blade.php`
- `database/migrations/2026_11_03_000000_create_whatsapp_transaction_notification_tables.php`
- `config/whatsapp_notifications.php`

## Customer consent

A customer must have a linked `whatsapp_number` and explicitly enable alerts by sending:

```text
NOTIFICATIONS ON
```

They can disable alerts at any time with:

```text
STOP
```

or:

```text
NOTIFICATIONS OFF
```

## Meta utility templates

Create and obtain approval for these two **Utility** templates in WhatsApp Manager. The names and variable order must match exactly.

### `oresamsub_transaction_failed`

Language: English (`en`)

```text
Hello {{1}}, your OresamSub transaction was not successful.

Service: {{2}}
Amount: NGN {{3}}
Recipient: {{4}}
Reference: {{5}}
Reason: {{6}}

If your wallet was debited, check your transaction history for a refund update. Reply SUPPORT if you need help.
```

### `oresamsub_transaction_refunded`

Language: English (`en`)

```text
Hello {{1}}, your OresamSub transaction has been refunded.

Service: {{2}}
Amount: NGN {{3}}
Recipient: {{4}}
Reference: {{5}}
Reason: {{6}}

The amount has been returned to your OresamSub wallet. Reply SUPPORT if you need help.
```

Use realistic, non-sensitive samples for all six variables when submitting each template to Meta.

Suggested sample values:

```text
{{1}} Samuel
{{2}} 1GB MTN WEEKLY
{{3}} 410.00
{{4}} 0816***9044
{{5}} ORS-20261103-001
{{6}} The provider was temporarily unavailable.
```

Create them in **Meta Business Manager → WhatsApp Manager → Message templates → Create template**. Select the OresamSub WhatsApp Business Account, choose **Utility**, use English, paste the matching content above, provide the sample values, and submit for review.

## Delivery rules

- Inside 24 hours of the customer's latest WhatsApp message, the alert is sent as a normal text message.
- Outside that service window, the matching approved Utility template is used.
- Only customer-facing `user_screen_message` or `refund_reason` content is used. Raw provider/admin responses are excluded.
- Phone numbers in message content are masked.
- One delivery record is allowed per transaction and terminal status.
- Transport failures are marked indeterminate and are not automatically retried, preventing duplicate alerts.
- Meta webhook events update the delivery record through `sent`, `delivered`, `read`, or `failed`.

## Admin controls

- Open **Admin → WhatsApp Messages** or `/admin/whatsapp-messages` to select one customer and send one support message.
- The customer selector only includes linked customers who messaged OresamSub inside Meta's current 24-hour service window. A custom free-form message is blocked outside that window.
- A failed or refunded transaction detail page includes a protected **Send failure/refund alert** button.
- Transaction alerts require the customer's explicit opt-in and use the same idempotency key as automatic alerts, so the admin button cannot send a second copy.
- The delivery table records the initiating admin, masked recipient, status, Meta message ID, and any sanitized failure reason.

## Deployment

```bash
php artisan migrate --force
php artisan optimize:clear
```

The existing `schedule:run` cron drains the default queue. No additional long-running queue worker is required for the current hosting setup.

## Resume checklist

1. Deploy the code and run the migration/clear commands above.
2. Submit both exact Utility templates to Meta and wait until each status is **Approved**.
3. Confirm the production webhook remains subscribed to message and message-status events.
4. Link a test customer's WhatsApp number and send `NOTIFICATIONS ON` to the bot.
5. Test one failed transaction inside the 24-hour window; confirm a normal text is delivered once.
6. Test one failed/refunded transaction outside the window; confirm the approved template is delivered once.
7. Confirm the admin delivery history changes from `sent` to `delivered` or `read` when Meta sends status webhooks.

## Verification already completed

The WhatsApp, admin-send, webhook, duplicate-protection, and mobile-push regression suite passed with 41 tests and 120 assertions. Blade templates also compiled successfully.
