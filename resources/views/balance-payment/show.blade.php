<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment - {{ $booking->reference }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Balance Payment</h1>
                <p class="text-gray-600">Booking Reference: <span class="font-semibold">{{ $booking->reference }}</span></p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Booking Details</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tour:</span>
                        <span class="font-semibold">{{ $booking->tour->title }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Customer:</span>
                        <span class="font-semibold">{{ $booking->customer_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Start Date:</span>
                        <span class="font-semibold">{{ $booking->start_date->format('M j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Price:</span>
                        <span class="font-semibold">${{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Already Paid:</span>
                        <span class="font-semibold">${{ number_format($booking->amount_paid, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-300 pt-3 mt-3"></div>
                    <div class="flex justify-between text-lg">
                        <span class="text-gray-900 font-bold">Balance Due:</span>
                        <span class="text-blue-600 font-bold">${{ number_format($booking->amount_remaining, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> This secure payment link is valid for one-time use only and will expire soon.
                        Please complete your payment to confirm your booking.
                    </p>
                </div>
            </div>

            <button
                onclick="processPayment()"
                id="payButton"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                <span id="buttonText">Proceed to Payment</span>
                <span id="buttonLoader" class="hidden ml-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            <p class="text-center text-sm text-gray-500 mt-4">
                Payments are securely processed by OCTO Payment Gateway
            </p>
        </div>
    </div>

    <script>
        async function processPayment() {
            const button = document.getElementById('payButton');
            const buttonText = document.getElementById('buttonText');
            const buttonLoader = document.getElementById('buttonLoader');

            button.disabled = true;
            buttonText.classList.add('hidden');
            buttonLoader.classList.remove('hidden');

            try {
                const response = await fetch('{{ route('balance-payment.process', $token) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success && data.payment_url) {
                    window.location.href = data.payment_url;
                } else {
                    alert('Payment initialization failed: ' + (data.message || 'Unknown error'));
                    button.disabled = false;
                    buttonText.classList.remove('hidden');
                    buttonLoader.classList.add('hidden');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                button.disabled = false;
                buttonText.classList.remove('hidden');
                buttonLoader.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
