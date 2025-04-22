<x-mail::message>
# New User Registration

This is to notify you that a new user just registered on your platform.

Name: {{ $data['first_name'].' '. $data['last_name']}}  <br>
Email: {{ $data['email'] }} <br>
Phone Number: {{ $data['phone_number'] }} <br>
Date Registered: {{ date('F d, Y',strtotime($data['created_at']) ) }} <br>
<br>
Please login to follow up <br>
{{ $data['url'] }}

<x-mail::button :url="''">
  Click to login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
