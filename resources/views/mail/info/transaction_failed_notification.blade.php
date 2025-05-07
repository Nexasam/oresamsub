{{-- <p>Hello team,</p>
<p>A transaction has failed and needs attention.</p>

<p><strong>ID:</strong> {{ $transaction->id }}</p>
<p><strong>User:</strong> {{ $transaction->user->email ?? 'N/A' }}</p>
<p><strong>Reason:</strong> {{ $transaction->failure_reason ?? 'Not provided' }}</p>
<p> <a href="#">Check the transaction here...</a> </p>
<p>Thank you.</p> --}}

<x-mail::message>
# Failed Transaction Notification

A transaction has failed and needs attention.

User: {{ $data['email'] }} <br>
Transaction ID: {{ $data['id'] }} <br>
Phone Number: {{ $data['phone_number'] }} <br>
Reason: {{ $data['admin_message'] }} <br>
Product: {{ $data['transaction_category'] }} <br>
Product Plan: {{ $data['product_plan_name'] }} <br>
Date: {{ date('F d, Y',strtotime($data['created_at']) ) }} <br>
<br>
<br>
<a href="{{ $data['url'] }}">Please login to follow up</a>

<x-mail::button :url="$data['url']">
  Click to login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

