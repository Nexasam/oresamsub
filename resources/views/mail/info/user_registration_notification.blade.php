<x-mail::message>
# Introduction

This is to notify you that there is a new who just registered on your platform.

Name: {{ $data['first_name'].' '. $data['last_name']}}  <br>
Email: {{ $data['email'] }} <br>
Phone Number: {{ $data['phone_number'] }} <br>
<br>
<br>
Please login to follow up

<x-mail::button :url="{{ env('APP_URL') }}">
  Click to login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
