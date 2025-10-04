<p>Hi {{ $name }},</p>
<p>You requested to reset your password. Click the link below to reset it:</p>
<a href="https://o-hair-web.vercel.app/account/reset-password?token={{ $token }}&email={{ urlencode($email) }}">Reset Password</a>
<p>If you did not request this, please ignore this email.</p>