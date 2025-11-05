<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Error</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            text-align: center;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
        }
        .icon {
            font-size: 80px;
        }
        .header h1 {
            font-size: 32px;
            margin: 20px 0 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            color: #555;
            margin: 25px 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0D4C92 0%, #59C1BD 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13, 76, 146, 0.3);
        }
        .footer {
            text-align: center;
            padding: 25px;
            background: #f8f9fa;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">⚠️</div>
            <h1>Oops! Something Went Wrong</h1>
        </div>

        <div class="content">
            <p class="message">
                {{ $message ?? 'We encountered an error while processing your request. Please try again or contact our support team for assistance.' }}
            </p>

            <a href="{{ config('app.url') }}" class="btn btn-primary">
                🏠 Return to Homepage
            </a>
            <a href="mailto:{{ config('mail.from.address') }}" class="btn btn-primary">
                ✉️ Contact Support
            </a>
        </div>

        <div class="footer">
            <p>Jahongir Travel | {{ config('mail.from.address') }} | +998 91 123 45 67</p>
        </div>
    </div>
</body>
</html>
