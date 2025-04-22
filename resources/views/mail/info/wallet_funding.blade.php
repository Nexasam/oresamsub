<x-mail::message>
# Introduction

This is to notify you that there was an attempt to fund user's account
{{-- <br> --}}
If this is not from you, kindly login to your account and change your PIN.

<x-mail::button :url="''">
  Click here
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
