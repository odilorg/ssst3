<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Jahongir Travel</title>
    <link rel="stylesheet" href="{{ asset('css/tour-details.css') }}">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }
        .error-content {
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: #DC2626;
            line-height: 1;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--color-text, #1E1E1E);
        }
        .error-message {
            font-size: 1.125rem;
            color: var(--color-text-muted, #555);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .error-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--color-primary, #0D4C92);
            color: white;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark, #0A3A6F);
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: white;
            color: var(--color-primary, #0D4C92);
            border: 2px solid var(--color-primary, #0D4C92);
        }
        .btn-secondary:hover {
            background: var(--color-bg, #FAF8F4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-code">500</div>
            <h1 class="error-title">Server Error</h1>
            <p class="error-message">
                Oops! Something went wrong on our end. Our team has been notified and we're working to fix the issue. Please try again in a few moments.
            </p>
            <div class="error-buttons">
                <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
                <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            </div>
        </div>
    </div>
</body>
</html>
