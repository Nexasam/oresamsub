<x-mail::message>
# Pending Transaction Notification

You are required to urgently attend to {{ $data['transactions_count'] }} transactions that need to be manually processed.

<br>
<br>
<a href="{{ $data['url'] }}">Please login to follow up</a>

<x-mail::button :url="$data['url']">
  Click to login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

