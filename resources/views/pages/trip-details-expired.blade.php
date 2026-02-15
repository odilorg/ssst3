<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Details - {{ $booking->reference }} | Jahongir Travel</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            text-align: center;
        }
        .header {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            padding: 2.5rem 2rem;
        }
        .header-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        h1 { font-family: 'Poppins', sans-serif; font-size: 1.4rem; margin-bottom: 0.25rem; }
        .subtitle { font-size: 0.9rem; opacity: 0.9; }
        .content { padding: 2rem; }
        .message { color: #4b5563; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem; }
        .btn {
            display: inline-block; padding: 0.7rem 1.5rem;
            background: #1d4ed8; color: white; border-radius: 10px;
            text-decoration: none; font-weight: 600; font-size: 0.9rem;
        }
        .btn:hover { opacity: 0.9; }
        .footer {
            background: #f9fafb; padding: 1.25rem 2rem;
            color: #6b7280; font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <h1>This Form Has Closed</h1>
            <p class="subtitle">{{ $booking->reference }}</p>
        </div>
        <div class="content">
            <p class="message">
                The trip details form for <strong>{{ $booking->tour->title }}</strong> is no longer accepting updates as the tour date ({{ $booking->start_date->format('F j, Y') }}) has passed.
            </p>
            <p class="message">If you need to make changes, please contact us directly.</p>
            <a href="{{ url('/en/tours') }}" class="btn">Browse Tours</a>
        </div>
        <div class="footer">
            <p><strong>Jahongir Travel</strong></p>
            <p>Questions? Contact us at support@jahongir-hotels.uz</p>
        </div>
    </div>
</body>
</html>
