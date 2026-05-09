<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Halalytics Password</title>
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
        .reset-button {
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
        .reset-button:hover {
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
        .warning-note {
            background: #fee;
            border-left: 4px solid #e53e3e;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Halalytics</h1>
            <p>Password Reset Request</p>
        </div>
        
        <div class="content">
            <h2>Password Reset Request</h2>
            
            <p>Hi {{ $user->full_name ?? $user->username }},</p>
            
            <p>We received a request to reset your Halalytics account password. If you made this request, click the button below to reset your password:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">
                    🔄 Reset Password
                </a>
            </div>
            
            <div class="security-note">
                <strong>⏰ Time Sensitive:</strong> This password reset link will expire in 1 hour for security reasons.
            </div>
            
            <div class="warning-note">
                <strong>⚠️ Security Alert:</strong> If you didn't request this password reset, please ignore this email. Your account is still secure.
            </div>
            
            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #004D40; font-size: 12px;">{{ $resetUrl }}</p>
            
            <p><strong>For your security:</strong></p>
            <ul style="color: #4a5568;">
                <li>Never share this link with anyone</li>
                <li>Always use a strong, unique password</li>
                <li>Enable two-factor authentication if available</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>This email was sent to {{ $user->email }} because a password reset was requested for your Halalytics account.</p>
            <p>© 2026 Halalytics. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
