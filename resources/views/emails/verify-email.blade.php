<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Halalytics Account</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #004D40 0%, #26A69A 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #1a202c;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p {
            margin-bottom: 20px;
            color: #4a5568;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #004D40 0%, #26A69A 100%);
            color: white;
            padding: 16px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #f7fafc;
            padding: 20px 30px;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .security-note {
            background: #fef5e7;
            border-left: 4px solid #f39c12;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌟 Halalytics</h1>
            <p>Your Halal Product Companion</p>
        </div>
        
        <div class="content">
            <h2>Welcome, {{ $user->full_name ?? $user->username }}!</h2>
            
            <p>Thank you for joining Halalytics! We're excited to help you discover and verify halal products with ease.</p>
            
            <p>To get started, please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    ✅ Verify Email Address
                </a>
            </div>
            
            <div class="security-note">
                <strong>🔒 Security Notice:</strong> This verification link will expire in 24 hours. If you didn't create an account with us, please ignore this email.
            </div>
            
            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #004D40; font-size: 12px;">{{ $verificationUrl }}</p>
        </div>
        
        <div class="footer">
            <p>This email was sent to {{ $user->email }} because you registered for a Halalytics account.</p>
            <p>© 2026 Halalytics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
