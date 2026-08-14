<x-mail::message>
# Staff login verification

Hello {{ $name }},

Use the following one-time verification code to complete your Ujuzi Shop Mall staff sign-in:

# {{ $code }}

This code expires in **5 minutes** and can only be used once.

If you did not attempt to sign in, you can safely ignore this email and should notify the platform administrator.

Thanks,<br>
{{ config('app.name', 'Ujuzi Shop Mall') }}
</x-mail::message>
