<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Appointment Booking Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f8f8f8; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <tr>
            <td align="center">
                <h2 style="color: #d97706;">New Appointment Booking Received</h2>
                <p>Dear Admin,</p>
                <p>A new appointment has been booked. Here are the details:</p>
                
                <h3 style="background: #d97706; color: #ffffff; padding: 10px; border-radius: 3px; text-align: center;">Appointment Details</h3>
                <p><strong>Customer Name:</strong> {{$name}}</p>
                <p><strong>Date:</strong> {{$date}}</p>
                <p><strong>Time:</strong> {{$slot}}</p>
                <p><strong>Customer Contact:</strong> {{$number}}</p>

              
                <br>
                <p><strong>O Hair</strong></p>
                <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                <p style="font-size: 12px; color: #888;">This is an auto-generated email. Please do not reply to this message.</p>
            </td>
        </tr>
    </table>
</body>
</html>
