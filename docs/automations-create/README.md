# Automation Provider Creation Flow

This document extracts the reusable flow behind the OresamSub admin page currently exposed at:

```text
GET /admin/automations/create
```

The feature allows an administrator to describe how the application should communicate with a third-party service provider without hard-coding every provider's request and response format.

## 1. What an automation represents

An `Automation` is a vendor/provider adapter configuration. It stores:

- Provider identity and activation state.
- Provider API credentials.
- Service-specific endpoint URLs.
- The HTTP method to use.
- A mapping from internal transaction values to the provider's request keys.
- Provider-specific network identifiers.
- Static request headers.
- Conditions used to recognise a successful provider response.
- Dot-notation paths used to extract success and failure messages.
- Optional funding/bank and support information.

An automation can then be attached to product plans, product-plan categories, transactions, customer-specific provider selections, and provider-plan mappings.

## 2. Access and routes

Both routes must be restricted to authenticated, email-verified administrators.

```php
use App\Http\Controllers\Admin\AutomationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin/automations')
    ->name('admin.automations.')
    ->group(function () {
        Route::get('/create', [AutomationController::class, 'create'])
            ->name('create');

        Route::post('/', [AutomationController::class, 'store'])
            ->name('store');
    });
```

Current OresamSub route names are `admin.automation.create` and `admin.automation.storev2`. The cleaner names above are recommended for the receiving project.

## 3. Page-loading flow

The create controller supplies the view with the internal fields that an administrator can map to a provider's request parameters:

```php
$mappableFields = [
    'phone_number',
    'network',
    'plan',
    'amount',
    'email',
    'user',
    'ported_number',
    'reference',
    'action',
];

return view('admin.automations.create', compact('mappableFields'));
```

The existing page also loads all automations, although they are not needed by the form. The receiving implementation should omit that query unless the UI actually displays existing providers.

## 4. Form sections

### Provider information

- `name`: Human-readable provider name.
- `slug`: Unique machine-readable provider identifier.
- `activation_status`: Whether the provider can be used.

### Credentials and funding information

- `api_public_key`
- `api_secret_key`
- `api_password`
- `bank_name`
- `bank_accounts`
- `whatsapp_support_link`

Credentials must use password inputs in the browser and encrypted model casts at rest. They must never be returned by normal JSON resources or written to logs.

### Service endpoints

- `endpoint_url`: Optional base endpoint.
- `data_url`
- `airtime_url`
- `cable_url`
- `electricity_url`

The clean version should eventually use a child `automation_endpoints` table instead of adding a new column for every future product.

### Request mapping

`request_params` is an ordered JSON array. Each item maps the name expected by the provider to an internal runtime value.

```json
[
  { "key": "mobile_number", "value": "phone_number" },
  { "key": "network_id", "value": "network" },
  { "key": "plan_id", "value": "plan" },
  { "key": "request_id", "value": "reference" }
]
```

At transaction time, the adapter resolves the values on the right and creates the provider payload:

```json
{
  "mobile_number": "08030000000",
  "network_id": "1",
  "plan_id": "M500MB",
  "request_id": "ORDER-10001"
}
```

The current implementation treats an unrecognised mapping value as a literal/static value. A cleaner implementation should make this explicit by storing a mapping `type` such as `runtime`, `credential`, or `literal`.

### Network mapping

`network_plans` maps the application's network name to the provider's network identifier:

```json
{
  "MTN": "1",
  "GLO": "2",
  "AIRTEL": "3",
  "9MOBILE": "4"
}
```

Despite its current name, this maps networks, not individual product plans. `network_mapping` would be a clearer name in a new project.

### Request headers

`request_headers` is stored as key/value JSON:

```json
[
  { "key": "Authorization", "value": "Bearer provider-token" },
  { "key": "Content-Type", "value": "application/json" }
]
```

Secrets embedded in headers should be encrypted or represented using credential placeholders instead of being exposed directly in the form after saving.

### Success detection

`success_condition` is an array of response paths and expected values. Every condition must pass.

```json
[
  { "key": "status", "value": "success" },
  { "key": "data.completed", "value": "true" }
]
```

The adapter walks dot-separated response paths, normalises string forms of `true` and `false`, and compares the actual and expected values.

### Response message extraction

`success_response` and `failed_response` are dot-notation paths, not literal messages.

```text
success_response = data.message
failed_response  = error.message
```

If the configured path is absent, the current fallbacks are `Transaction was successful` and `Transaction failed`.

## 5. Suggested consolidated schema

The existing application builds the table across several historical migrations. A new project should start with one clean migration:

```php
Schema::create('automations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('group')->default('v2')->index();
    $table->boolean('is_active')->default(true)->index();

    $table->text('api_public_key')->nullable();
    $table->text('api_secret_key')->nullable();
    $table->text('api_password')->nullable();

    $table->string('http_method', 10)->default('POST');
    $table->text('base_url')->nullable();
    $table->text('data_url')->nullable();
    $table->text('airtime_url')->nullable();
    $table->text('cable_url')->nullable();
    $table->text('electricity_url')->nullable();

    $table->json('network_mapping')->nullable();
    $table->json('request_parameters')->nullable();
    $table->json('request_headers')->nullable();
    $table->json('success_conditions')->nullable();
    $table->string('success_message_path')->nullable();
    $table->string('failure_message_path')->nullable();
    $table->unsignedSmallInteger('expected_success_code')->nullable();
    $table->unsignedSmallInteger('expected_failure_code')->nullable();

    $table->string('bank_name')->nullable();
    $table->text('bank_accounts')->nullable();
    $table->text('support_url')->nullable();
    $table->timestamps();
});
```

For a multi-tenant project, add a tenant/business foreign key and make the slug unique within that tenant:

```php
$table->foreignUuid('business_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
$table->unique(['business_id', 'slug']);
```

## 6. Model requirements

```php
class Automation extends Model
{
    use HasUuids;

    protected $guarded = ['id'];

    protected $hidden = [
        'api_public_key',
        'api_secret_key',
        'api_password',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'network_mapping' => 'array',
            'request_parameters' => 'array',
            'request_headers' => 'array',
            'success_conditions' => 'array',
            'api_public_key' => 'encrypted',
            'api_secret_key' => 'encrypted',
            'api_password' => 'encrypted',
        ];
    }
}
```

Expected relationships include:

- `Automation hasMany ProductPlan`
- `Automation hasMany AutomationProductPlan`
- `Automation hasMany Transaction`
- `Automation hasMany UserAutomation` when customers can override the default provider.
- `Business hasMany Automation` in a multi-tenant deployment.

## 7. Validation flow

Use a dedicated form request rather than validating inside the controller.

Minimum rules:

```php
return [
    'name' => ['required', 'string', 'max:255'],
    'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('automations')->where('business_id', $businessId)],
    'http_method' => ['required', Rule::in(['GET', 'POST'])],
    'network_mapping' => ['required', 'array', 'min:1'],
    'request_parameters' => ['required', 'array', 'min:1'],
    'request_parameters.*.key' => ['required', 'string'],
    'request_parameters.*.value' => ['required', 'string'],
    'request_headers' => ['nullable', 'array'],
    'request_headers.*.key' => ['required_with:request_headers.*.value', 'string'],
    'request_headers.*.value' => ['required_with:request_headers.*.key', 'string'],
    'success_conditions' => ['required', 'array', 'min:1'],
    'success_conditions.*.key' => ['required', 'string'],
    'success_conditions.*.value' => ['present'],
    'success_message_path' => ['required', 'string'],
    'failure_message_path' => ['required', 'string'],
    'base_url' => ['nullable', 'url:http,https'],
    'data_url' => ['nullable', 'url:http,https'],
    'airtime_url' => ['nullable', 'url:http,https'],
    'cable_url' => ['nullable', 'url:http,https'],
    'electricity_url' => ['nullable', 'url:http,https'],
];
```

The receiving project should also validate that at least one service endpoint is supplied.

## 8. Store flow

1. Authenticate the administrator.
2. Resolve and authorise the current business/tenant.
3. Validate the complete nested payload.
4. Normalise the slug, URLs, mappings, headers, and response paths.
5. Encrypt credentials through model casts.
6. Create the automation inside a database transaction.
7. Write an audit entry without credential values.
8. Return a redirect for a traditional Blade form, or a consistent JSON response for an AJAX form.

Example success response:

```json
{
  "success": true,
  "message": "Automation saved successfully.",
  "data": {
    "id": "019c...",
    "name": "Example Provider",
    "slug": "example-provider",
    "is_active": true
  }
}
```

Never return API credentials in this response.

## 9. Runtime transaction flow

```text
Customer initiates purchase
        |
        v
Resolve product plan and selected automation
        |
        v
Choose endpoint for the transaction service
        |
        v
Map internal values to provider request keys
        |
        v
Resolve credential/static header placeholders
        |
        v
Send GET or POST request with finite timeouts
        |
        v
Decode and preserve provider response safely
        |
        v
Evaluate HTTP code and all configured success conditions
        |
        +------ success ------> extract success message
        |
        +------ failure ------> extract failure message
        |
        v
Update the auditable transaction through the normal transaction state machine
```

The adapter should return a consistent internal result regardless of the provider:

```php
[
    'successful' => true,
    'message' => 'Transaction completed',
    'provider_reference' => 'VENDOR-12345',
    'http_status' => 200,
    'provider_response' => $redactedResponse,
]
```

## 10. Alpine.js form behaviour

The existing page uses Alpine.js arrays to add and remove request mappings, headers, and success conditions. The reusable pattern is:

```html
<form x-data="automationForm()" method="POST">
    <template x-for="(parameter, index) in requestParameters" :key="parameter.id">
        <!-- key and value controls -->
    </template>
</form>
```

Each dynamic row should have a stable client-side ID rather than using only the array index. Preserve submitted values and validation errors after a failed request. Alpine should be loaded once through the application's Vite entry point, not from a CDN inside this page.

## 11. Important current-code differences not to copy

The current OresamSub flow works as a configuration proof of concept, but the following behaviour should be corrected in the receiving project:

- The form permits `GET`, while `DataAutomation` and `AirtimeAutomation` always send `POST`.
- `AirtimeAutomation` currently reads `data_url`; it should use `airtime_url`.
- Runtime adapters generate a new reference instead of consistently using the supplied transaction reference.
- cURL has an unlimited timeout (`CURLOPT_TIMEOUT => 0`), which can hold workers indefinitely.
- Network lookup falls back to provider network ID `1`; missing mappings should fail explicitly.
- Request headers may contain plain-text secrets.
- The current creation response exposes the created model; hidden fields help, but an explicit API resource is safer.
- Endpoint validation currently accepts arbitrary strings rather than valid HTTP/HTTPS URLs.
- Required API credentials prevent providers that use only one token or no password. Credential requirements should be configurable.
- Bank information is required even though it is unrelated to request execution.
- `domain_url` is populated from `endpoint_url`, creating duplicate concepts.
- Success/failure extraction supports only three nested path levels; use Laravel `data_get()` for arbitrary depth.
- The controller contains both legacy and v2 store methods. The receiving project should have one canonical implementation.

## 12. Recommended extraction order

1. Create the consolidated migration and tenant-aware constraints.
2. Create the `Automation` model with encrypted casts and relationships.
3. Create the form request and administrator policy.
4. Add the protected create/store routes.
5. Build the Blade + Alpine form with repeatable mapping components.
6. Implement a generic HTTP provider client using Laravel's HTTP client.
7. Add service-specific endpoint selection.
8. Add response-condition evaluation and dot-path extraction.
9. Connect product plans and transactions to an automation.
10. Add audit logging, redacted request/response logs, timeouts, retries, and tests.

## 13. Minimum tests for the receiving project

- Guests cannot access either route.
- Non-admin users receive `403`.
- One tenant cannot view or modify another tenant's automation.
- A valid configuration is persisted with JSON casts intact.
- Duplicate slugs are rejected within the same tenant but allowed across tenants.
- Credentials are encrypted in the database and absent from responses/logs.
- Invalid or non-HTTPS production endpoints are rejected.
- Runtime mappings produce the expected provider payload.
- GET and POST configurations use their configured methods.
- Data and airtime select their correct endpoints.
- Missing network mappings fail safely.
- Nested success conditions and message paths are evaluated correctly.
- Provider timeout, invalid JSON, HTTP error, success, and business-level failure are handled consistently.

