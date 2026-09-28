@component('mail::message')
# New Airtime-to-Cash Request

A customer submitted a new manual airtime-to-cash request.

@component('mail::table')
| Field | Value |
| --- | --- |
| Reference | {{ $requestRecord->reference }} |
| Customer | {{ $requestRecord->user->username ?? $requestRecord->user->email ?? 'Customer' }} |
| Network | {{ $requestRecord->network_name }} |
| Airtime Amount | ₦{{ number_format((float) $requestRecord->airtime_amount, 2) }} |
| Cash Payout | ₦{{ number_format((float) $requestRecord->cash_amount, 2) }} |
| Rate | ₦{{ number_format((float) $requestRecord->rate_per_100, 2) }} per ₦100 |
| Sender Phone | {{ $requestRecord->sender_phone }} |
| Bank | {{ $requestRecord->payout_bank_name }} |
| Account Name | {{ $requestRecord->payout_account_name }} |
| Account Number | {{ $requestRecord->maskedAccountNumber() }} |
| Disclaimer Accepted | {{ $requestRecord->fraud_disclaimer_accepted_at ? $requestRecord->fraud_disclaimer_accepted_at->format('M j, Y g:i A') : 'Not recorded' }} |
| Status | {{ strtoupper($requestRecord->status) }} |
@endcomponent

Please log in to the admin dashboard to verify the airtime and process the payout manually.
@endcomponent
