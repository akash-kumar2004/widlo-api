<x-mail::message>
# Login Verification Code

Hello **{{ $userName }}**,

We received a request to sign in to your {{ config('app.name') }} account.  
To complete your login, please use the verification code below:

<x-mail::panel>
<div style="text-align: center; font-size: 32px; font-weight: bold; font-family: monospace; letter-spacing: 6px; color: #1a202c; background: #f7fafc; padding: 20px; border-radius: 8px; border: 2px dashed #cbd5e0;">
    {{ $otp }}
</div>
</x-mail::panel>

<p>
    <strong>Important:</strong> This code will expire in 
    <span style="
        color: red; 
        font-weight: bold; 
        text-shadow: 0 0 5px red, 0 0 10px red;
        animation: blink 1s infinite;
        -webkit-animation: blink 1s infinite;
    ">
        {{ $expiryMinutes }} minutes
    </span>.
</p>

## Security Notice
--------------------

<p style="color: red; font-weight: bold; font-size: 16px;">
     Never share this code with anyone 
</p>

<p>
    If you didn't request this, please ignore this email.
</p>
Thanks for keeping your account secure,<br>
{{ config('app.name') }} Team

<style>
@keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.2; }
    100% { opacity: 1; }
}
@-webkit-keyframes blink {
    0% { opacity: 1; }
    50% { opacity: 0.2; }
    100% { opacity: 1; }
}
</style>
</x-mail::message>
