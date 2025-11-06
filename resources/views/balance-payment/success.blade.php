<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
            <p class="text-gray-600 mb-6">
                Your payment has been processed successfully. Your booking is now confirmed.
            </p>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="text-left space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Booking Reference:</span>
                        <span class="font-semibold">{{ $booking->reference }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="font-mono text-xs">{{ $transaction_id }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="text-green-600 font-semibold">Paid in Full</span>
                    </div>
                </div>
            </div>
            <p class="text-sm text-gray-500 mb-6">
                A confirmation email has been sent to {{ $booking->customer_email }}
            </p>
            <a href="/" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Return to Homepage
            </a>
        </div>
    </div>
</body>
</html>
