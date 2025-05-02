<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <div style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <div style="text-align: center; padding: 10px 0;">
            <img src="https://gitlab.up.pt/lbaw/lbaw2425/lbaw24145/-/raw/dev/img/glinthub.jpg?ref_type=heads" alt="Glinthub Logo" style="max-width: 150px;">
        </div>
        <div style="padding: 20px;">
            <h3 style="color: #333333;">Hi {{ $mailData['name'] }},</h3>
            <p style="color: #666666; line-height: 1.5;">Click the link below to reset your password:</p>
            <a href="{{ $mailData['reset_link'] }}" style="display: inline-block; padding: 10px 20px; margin: 20px 0; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px;">Reset Password</a>
            <p style="color: #666666; line-height: 1.5;">If you did not request a password reset, please ignore this email.</p>
        </div>
        <div style="text-align: center; padding: 10px 0; color: #999999; font-size: 12px;">
            <p>-------</p>
            <p>Glinthub Team</p>
        </div>
    </div>
</body>
</html>