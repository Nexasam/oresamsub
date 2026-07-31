<x-mail::message>
# Wallet balance alert

Hello {{ $setting->user->first_name }},

Your OresamSub wallet balance is currently **₦{{ number_format($walletBalance, 2) }}**, which is below your configured funding threshold of **₦{{ number_format((float) $setting->funding_threshold, 2) }}**.

Please fund your wallet to avoid interruptions to your customer transactions.

@if ($setting->funding_account_number)
Your authorised funding account on record ends in **{{ substr($setting->funding_account_number, -4) }}**. No automatic transfer has been initiated unless OresamSub has separately confirmed that the transfer integration is active.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
