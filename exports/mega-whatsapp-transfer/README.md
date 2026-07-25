# MegaWhatsapp transfer package

This directory is a portable snapshot of the MegaWhatsapp bot implementation.
Copy the `app`, `database`, and (optionally) `tests` directories into the root
of the target Laravel application while preserving their paths. Do not blindly
overwrite shared target files.

## Included runtime files

- `app/Http/Controllers/Api/v1/VendorUsersApi/MegaWhatsappWebhookController.php`
  parses Meta webhook text, button, list, and shared-contact messages.
- `app/Services/Whatsapp/MegaWhatsappService.php` sends text, button, and list
  messages through Meta Graph API v23.
- `app/Services/Whatsapp/MegaWhatsappConversationService.php` contains the bot
  state machine, menus, purchases, wallet, and repeat-transaction flows.
- `app/Services/Whatsapp/MegaWhatsappUserResolverService.php` maps Nigerian
  WhatsApp numbers to users.
- `app/Services/Whatsapp/WhatsappIntentResolver.php` supplies the wallet/account
  response used by the bot.
- `app/Enums/WhatsappState.php` defines persisted conversation states.
- Three migrations for the API configuration, user WhatsApp number, and bot
  conversations.
- Focused service and resolver tests.

The route to merge into the target application is in `integration/routes.php`.
Model class files are intentionally excluded from this package.

## Required host-application contracts

The target is described as a similar codebase, so shared commerce files are not
duplicated here. Confirm that it already has compatible versions of:

- `App\Models\User`, using UUID `id`, `phone_number`, `whatsapp_number`,
  `main_wallet`, and `pin`.
- `App\Models\MegaWhatsappConversation`, with fillable `phone`, `user_id`,
  `current_state`, and `payload`; cast `payload` to an array and define its
  `user()` relationship.
- `App\Models\WhatsappConfig`, exposing `token` and `phone_number_id`.
- `App\Models\Network`, `Product`, `ProductPlan`, `ProductPlanCategory`, and
  `Transaction`, including the relationships used by the source application.
- `App\Models\UserVirtualAccount`, used by `WhatsappIntentResolver::resolveAccount`.
- `App\Http\Controllers\DataController::buy_again_data_action(Request)`.
- `App\Http\Controllers\AirtimeController::buy_airtime_action_1(Request)`.
- Laravel's HTTP client, cache, logging, Eloquent, and Pest test support.

`WhatsappIntentResolver` has other imports for non-Mega flows. If the target
does not already use that shared resolver, either copy its additional model
contracts or extract only `resolveAccount()` into a smaller target service.

## Database and configuration

Run migrations in timestamp order:

1. `2026_06_24_220923_create_whatsapp_configs_table.php`
2. `2026_07_01_073414_add_whnumber_to_users.php`
3. `2026_07_13_062712_create_mega_whatsapp_conversations_table.php`

The conversation migration assumes `users.id` is a UUID. Adjust that foreign
key before migrating if the target uses integer IDs.

Create exactly one `whatsapp_configs` row with:

- `token`: the Meta permanent/system-user access token.
- `phone_number_id`: the Meta WhatsApp phone-number ID.

The service currently uses `WhatsappConfig::firstOrFail()`. Secrets are not
included in this package.

Configure the Meta webhook callback to POST to `/whatsapp/webhook`. The source
controller processes webhook events but does not implement Meta's GET
verification challenge or validate webhook signatures; retain the target
application's existing verification/security layer if it has one.

## Installation checklist

1. Back up or branch the target codebase.
2. Copy this package's `app` and `database/migrations` contents into matching
   target paths.
3. Merge `integration/routes.php` into the target route file.
4. Reconcile the host contracts listed above.
5. Run `php artisan migrate`.
6. Insert the `whatsapp_configs` record securely.
7. Run the included tests and a webhook smoke test.
8. Register the public HTTPS callback in Meta and subscribe to messages.

## Important behavior to review

- Sending `mega` opens the bot; sending `start` deletes its conversation and
  clears its cache session.
- The purchase flow invokes controller methods directly through Laravel's
  container. Those methods and their JSON response shapes must remain compatible.
- User resolution is Nigeria-specific: a leading `234` becomes a leading `0`.
- Graph API version `v23.0` is hard-coded in `MegaWhatsappService`.
- The source route is in `web.php`; ensure CSRF handling permits Meta's POST in
  the target Laravel version or place it in the API/webhook route stack.
