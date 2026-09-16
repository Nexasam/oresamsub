<x-mail::message>
# Automation balance is low

**{{ $funding->automation->automation_name }}** currently has a tracked balance of **₦{{ number_format((float) $funding->last_balance, 2) }}**.

Its configured low-stock threshold is **₦{{ number_format((float) $funding->threshold, 2) }}**. Please review and fund this automation to prevent service interruption.

Automatic funding is currently **{{ $funding->automatic_funding ? 'enabled' : 'disabled' }}**.

<x-mail::button :url="route('admin.automation-funding.index')">
Review automation funding
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
