<x-mail::message>
# User Registration

This is to notify you that a new user just registered on your website
{{-- <br> --}}

<x-mail::button :url="''">
  Click here
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
