<x-mail::message>
@php
    $registration = $data ?? [];
    $firstName = trim((string) (data_get($registration, 'first_name') ?? data_get($registration, 'firstname') ?? ''));
    $lastName = trim((string) (data_get($registration, 'last_name') ?? data_get($registration, 'lastname') ?? ''));
    $fullName = trim($firstName.' '.$lastName);
    $email = (string) (data_get($registration, 'email') ?? 'Not provided');
    $phoneNumber = (string) (data_get($registration, 'phone_number') ?? data_get($registration, 'phone') ?? 'Not provided');
    $createdAt = data_get($registration, 'created_at');
    $registeredAt = 'Not available';

    if ($createdAt) {
        try {
            $registeredAt = \Illuminate\Support\Carbon::parse($createdAt)->format('F d, Y');
        } catch (\Throwable) {
            $registeredAt = (string) $createdAt;
        }
    }

    if ($fullName === '') {
        $fullName = (string) (data_get($registration, 'name') ?? data_get($registration, 'username') ?? $email ?? 'New customer');
    }

    $loginUrl = (string) (data_get($registration, 'url') ?? url('/login'));
@endphp

# New User Registration

This is to notify you that a new user just registered on your platform.

Name: {{ $fullName }}  <br>
Email: {{ $email }} <br>
Phone Number: {{ $phoneNumber }} <br>
Date Registered: {{ $registeredAt }} <br>
<br>
Please login to follow up <br>
{{ $loginUrl }}

<x-mail::button :url="$loginUrl">
  Click to login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
