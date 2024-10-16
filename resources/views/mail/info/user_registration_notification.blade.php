<x-mail::message>
# Introduction

This is to notify you that there is a new who just registered on your platform.

Name: {{ $data['first_name'].' '. $data['last_name']}}
Email: {{ $data['email'] }}
Phone Number: {{ $data['phone_number'] }}

{{-- <br> --}}
If this is not from you, kindly login to your account and change your PIN.

<x-mail::button :url="''">
  Click here
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
