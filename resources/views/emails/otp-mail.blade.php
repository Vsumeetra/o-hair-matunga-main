@component('mail::message')
# Waahanam

Hello **{{ $name }}**,  
Thank you for registering with **Waahanam**!  

Please verify your email using the OTP below:

@component('mail::panel')
<span style="font-size: 20px; font-weight: bold; color: #007bff;">
    {{ $otp }}
</span>
@endcomponent

This OTP is valid for **15 minutes**. Please do not share it with anyone.

To complete your registration:  
- Enter the OTP on the verification page.  
- Click **Verify** to confirm your email address.  

If you did not initiate this request, please ignore this email or contact our support team immediately.  

For assistance, contact us at [support@waahanam.com](mailto:support@waahanam.com).

@component('mail::footer')
This is an automated email. Please do not reply directly.  
&copy; {{ date('Y') }} Waahanam. All rights reserved.
@endcomponent
@endcomponent
