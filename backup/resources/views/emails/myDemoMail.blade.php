@component('mail::message')
# {{ $mailData['title'] }}

Hello,

You have requested a One-Time Password (OTP). Use the code below to complete your action:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold;">
    {{ $mailData['otp'] }}
</div>
@endcomponent

This code will expire shortly. If you did not request this, please ignore this email.

Thanks,<br>
PRIME EDGE

<small>&copy; {{ date('Y') }} PRIME EDGE. All rights reserved.</small>
@endcomponent