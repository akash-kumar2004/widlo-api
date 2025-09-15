<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Verification Code</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Google Sans', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
        }
        
        .header {
            background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 400;
            margin: 0;
            text-align: center;
            width: 100%;
        }
        
        .content {
            padding: 40px;
            color: #202124;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 24px;
            color: #1f1f1f;
        }
        
        .message {
            font-size: 16px;
            color: #5f6368;
            margin-bottom: 32px;
            line-height: 1.5;
        }
        
        .otp-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e8f0fe 100%);
            border: 2px solid #4285f4;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            margin: 32px 0;
        }
        
        .otp-label {
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: 600;
            color: #1a73e8;
            letter-spacing: 8px;
            margin: 16px 0;
            font-family: 'Courier New', monospace;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .expiry-notice {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 16px 20px;
            margin: 24px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .expiry-notice .icon {
            display: inline-block;
            margin-right: 8px;
            font-size: 16px;
        }
        
        .expiry-text {
            font-size: 14px;
            color: #e65100;
            font-weight: 500;
        }
        
        .security-section {
            background-color: #f1f3f4;
            border-radius: 12px;
            padding: 24px;
            margin: 32px 0;
        }
        
        .security-title {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: 600;
            color: #d93025;
            margin-bottom: 16px;
        }
        
        .security-title .icon {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .security-text {
            font-size: 14px;
            color: #5f6368;
            line-height: 1.5;
        }
        
        .security-text strong {
            color: #d93025;
            font-weight: 500;
        }
        
        .ignore-notice {
            background-color: #e8f5e8;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
            color: #137333;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 32px 20px;
            text-align: center;
            border-top: 1px solid #dadce0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .footer-text {
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 8px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            text-align: center;
        }
        
        .team-signature {
            font-size: 16px;
            font-weight: 500;
            color: #1a73e8;
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: center;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #dadce0, transparent);
            margin: 24px 0;
        }
        
        /* Responsive design */
        @media (max-width: 600px) {
            body {
                padding: 5px;
            }
            
            .email-container {
                margin: 0;
                border-radius: 8px;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .header {
                padding: 30px 15px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .header h1 {
                font-size: 22px;
                margin: 0;
                text-align: center;
                line-height: 1.2;
            }
            
            .footer {
                padding: 20px 15px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .footer-text {
                font-size: 12px;
                line-height: 1.4;
                margin-bottom: 8px;
                text-align: center;
            }
            
            .team-signature {
                font-size: 13px;
                line-height: 1.3;
                text-align: center;
            }
            
            .greeting {
                font-size: 16px;
                margin-bottom: 20px;
            }
            
            .message {
                font-size: 14px;
                margin-bottom: 24px;
                line-height: 1.5;
            }
            
            .otp-container {
                padding: 20px 10px;
                margin: 24px 0;
            }
            
            .otp-code {
                font-size: 22px;
                letter-spacing: 3px;
                word-break: break-all;
                line-height: 1.2;
            }
            
            .expiry-notice {
                padding: 12px 15px;
                margin: 20px 0;
            }
            
            .expiry-text {
                font-size: 12px;
                line-height: 1.4;
            }
            
            .security-section {
                padding: 16px 15px;
                margin: 20px 0;
            }
            
            .security-title {
                font-size: 14px;
                margin-bottom: 12px;
            }
            
            .security-text {
                font-size: 12px;
                line-height: 1.4;
            }
            
            .ignore-notice {
                padding: 12px 15px;
                margin: 20px 0;
                font-size: 12px;
                line-height: 1.4;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 2px;
            }
            
            .content, .header, .footer {
                padding-left: 12px;
                padding-right: 12px;
            }
            
            .header {
                padding: 20px 12px;
            }
            
            .footer {
                padding: 16px 12px;
            }
            
            .header h1 {
                font-size: 18px;
            }
            
            .header .shield-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
                margin-bottom: 10px;
            }
            
            .otp-code {
                font-size: 20px;
                letter-spacing: 2px;
            }
            
            .greeting {
                font-size: 15px;
            }
            
            .footer-text {
                font-size: 11px;
            }
            
            .team-signature {
                font-size: 12px;
            }
        }
        
        @media (max-width: 360px) {
            .content, .header, .footer {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .footer-text {
                font-size: 10px;
                line-height: 1.3;
                text-align: center;
            }
            
            .team-signature {
                font-size: 11px;
                line-height: 1.2;
                text-align: center;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1f1f1f;
            }
            
            .content {
                color: #e8eaed;
            }
            
            .greeting {
                color: #ffffff;
            }
            
            .message {
                color: #bdc1c6;
            }
            
            .security-section {
                background-color: #2d2d30;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Login Verification</h1>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <div class="greeting">
                Hello  <strong>{{ $userName }}</strong>,
            </div>
            
            <div class="message">
                We received a request to sign in to your <strong>{{ config('app.name') }}</strong> account.
                <br><br>
                To complete your login, please use the verification code below:
            </div>
            
            <!-- OTP Container -->
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <!-- Expiry Notice -->
            <div class="expiry-notice">
                <span class="icon">⏱️</span>
                <span class="expiry-text">
                    <strong>Important:</strong> This code will expire in <strong>{{ $expiryMinutes }} minutes</strong>.
                </span>
            </div>
            
            <div class="divider"></div>
            
            <!-- Security Section -->
            <div class="security-section">
                <div class="security-title">
                    <span class="icon">🔒</span>
                    Security Notice
                </div>
                <div class="security-text">
                    <strong>Never share this code with anyone.</strong> Our team will never ask you for this verification code.
                    <br><br>
                    This code is unique to your login attempt and should only be entered on the official App.
                </div>
            </div>
            
            <!-- Ignore Notice -->
            <div class="ignore-notice">
                💡 <strong>Didn't request this?</strong> If you didn't try to sign in, you can safely ignore this email. Your account remains secure.
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">
                Thanks for keeping your account secure 
            </div>
            <div class="team-signature">
                <strong>{{ config('app.name') }} Team</strong>
            </div>
        </div>
    </div>
</body>
</html>