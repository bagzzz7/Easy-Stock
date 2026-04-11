{{-- resources/views/emails/verify-email.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Verify Email - Pharmacy Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background: #11998e;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to Pharmacy Inventory!</h2>
            <p>Email Verification Required</p>
        </div>
        
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            
            <p>Thank you for registering with the Pharmacy Inventory System!</p>
            
            <p>Please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
            </div>
            
            <p>If the button doesn't work, copy and paste this link into your browser:</p>
            <p style="word-break: break-all; background: #eee; padding: 10px; border-radius: 3px;">
                {{ $verificationUrl }}
            </p>
            
            <p>Once verified, you'll have full access to all system features.</p>
            
            <p>Thank you,<br>
            Pharmacy Inventory System Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Pharmacy Inventory System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>