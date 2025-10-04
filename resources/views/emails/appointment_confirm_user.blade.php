<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salon Appointment Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f8f8f8; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <tr>
            <td align="center">
                <h2 style="color: #d97706;">Your Salon Appointment is Confirmed</h2>
                <p>Dear <strong>{{$name}}</strong>,</p>
                <p>Thank you for choosing <strong>O Hair</strong>! Your appointment has been successfully booked.</p>
                
                <h3 style="background: #d97706; color: #ffffff; padding: 10px; border-radius: 3px; text-align: center;">Appointment Details</h3>
                <p><strong>Date:</strong> {{$date}}</p>
                <p><strong>Time:</strong> {{$slot}}</p>
            
                <h3>Need to Reschedule?</h3>
                <p>If you need to make changes to your appointment, please call us at <strong>+91 9876543210</strong>.</p>

                <p>We look forward to pampering you!</p>
                <br>
                <p><strong>O Hair</strong></p>
                <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                <p style="font-size: 12px; color: #888;">This is an auto-generated email. Please do not reply to this message.</p>
            </td>
        </tr>
    </table>
</body>
</html>
