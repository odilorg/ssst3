<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Payment System - Customer View</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .booking-card {
            transition: all 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-indigo-600 mb-3">
                    🎫 Payment System Frontend Testing
                </h1>
                <p class="text-gray-600 text-lg">
                    Test the complete customer payment experience
                </p>
                <div class="mt-4 inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-full">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-semibold">All Systems Ready</span>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📖 How to Test</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-3xl mb-2">1️⃣</div>
                    <h3 class="font-bold text-gray-800 mb-2">Choose a Booking</h3>
                    <p class="text-sm text-gray-600">Select any booking with remaining balance below</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="text-3xl mb-2">2️⃣</div>
                    <h3 class="font-bold text-gray-800 mb-2">Generate Token</h3>
                    <p class="text-sm text-gray-600">Click "Generate Payment Link" to create a secure token</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-3xl mb-2">3️⃣</div>
                    <h3 class="font-bold text-gray-800 mb-2">Test Payment</h3>
                    <p class="text-sm text-gray-600">Open the payment page and test the complete flow</p>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">💼 Test Bookings</h2>

            @if($bookings->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500">No bookings with remaining balance found</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bookings as $booking)
                        <div class="booking-card bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md p-6 border-2 border-gray-100">
                            <!-- Booking Header -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ $booking->reference }}
                                </span>
                                @if($booking->payment_status === 'deposit_paid')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                        Partial
                                    </span>
                                @endif
                            </div>

                            <!-- Customer Info -->
                            <div class="mb-4">
                                <h3 class="font-bold text-lg text-gray-800 mb-1">
                                    {{ $booking->customer_name }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    📧 {{ Str::limit($booking->customer_email, 25) }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    📞 {{ $booking->customer_phone }}
                                </p>
                            </div>

                            <!-- Tour Info -->
                            <div class="mb-4 bg-gray-100 rounded-lg p-3">
                                <p class="text-sm text-gray-700 font-semibold mb-1">
                                    🎯 {{ $booking->tour_name ?? 'Tour Package' }}
                                </p>
                                @if($booking->tour_date)
                                    <p class="text-xs text-gray-600">
                                        📅 {{ \Carbon\Carbon::parse($booking->tour_date)->format('M d, Y') }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-600">
                                    👥 {{ $booking->number_of_people }} people
                                </p>
                            </div>

                            <!-- Payment Info -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Price:</span>
                                    <span class="font-semibold text-gray-800">
                                        ${{ number_format($booking->total_price, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Paid:</span>
                                    <span class="font-semibold text-green-600">
                                        ${{ number_format($booking->amount_paid, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm pt-2 border-t border-gray-200">
                                    <span class="text-gray-700 font-semibold">Remaining:</span>
                                    <span class="font-bold text-red-600 text-lg">
                                        ${{ number_format($booking->amount_remaining, 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Active Tokens -->
                            @php
                                $activeTokens = $booking->paymentTokens()
                                    ->where('expires_at', '>', now())
                                    ->whereNull('used_at')
                                    ->count();
                            @endphp
                            @if($activeTokens > 0)
                                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <p class="text-xs text-blue-700 font-semibold">
                                        🎫 {{ $activeTokens }} active payment {{ $activeTokens > 1 ? 'tokens' : 'token' }}
                                    </p>
                                </div>
                            @endif

                            <!-- Action Button -->
                            <button
                                onclick="generateToken({{ $booking->id }}, '{{ $booking->reference }}')"
                                class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg"
                            >
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Generate Payment Link
                                </span>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Results Modal -->
        <div id="resultModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-8">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Payment Link Generated!</h3>
                        <p class="text-gray-600">Your secure payment token has been created</p>
                    </div>

                    <div id="tokenDetails" class="space-y-4">
                        <!-- Will be populated by JavaScript -->
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            onclick="copyPaymentUrl()"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                        >
                            📋 Copy Link
                        </button>
                        <button
                            onclick="openPaymentPage()"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors"
                        >
                            🚀 Open Payment Page
                        </button>
                    </div>

                    <button
                        onclick="closeModal()"
                        class="mt-4 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-indigo-600 mx-auto mb-4"></div>
                <p class="text-gray-700 font-semibold">Generating secure payment token...</p>
            </div>
        </div>
    </div>

    <script>
        let currentPaymentUrl = '';

        async function generateToken(bookingId, reference) {
            // Show loading
            document.getElementById('loadingOverlay').classList.remove('hidden');

            try {
                const response = await fetch('/api/test/generate-payment-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        booking_id: bookingId,
                        expiry_days: 7
                    })
                });

                const data = await response.json();

                if (data.success) {
                    currentPaymentUrl = data.payment_url;

                    // Display results
                    document.getElementById('tokenDetails').innerHTML = `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Booking Reference</p>
                            <p class="font-bold text-gray-800">${reference}</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Token Preview</p>
                            <p class="font-mono text-sm text-gray-800 break-all">${data.token.substring(0, 40)}...</p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Expires At</p>
                            <p class="font-semibold text-gray-800">${data.expires_at}</p>
                        </div>

                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 border-2 border-indigo-200">
                            <p class="text-sm text-gray-600 mb-2">Payment URL</p>
                            <p class="font-mono text-xs text-gray-800 break-all bg-white p-2 rounded">${data.payment_url}</p>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-green-800 mb-1">Testing Tips:</p>
                                    <ul class="text-xs text-green-700 space-y-1">
                                        <li>• Token is valid for 7 days</li>
                                        <li>• One-time use only</li>
                                        <li>• Automatically invalidated after payment</li>
                                        <li>• Test the complete customer experience</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;

                    // Show modal
                    document.getElementById('resultModal').classList.remove('hidden');
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate token'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to generate payment token. Check console for details.');
            } finally {
                // Hide loading
                document.getElementById('loadingOverlay').classList.add('hidden');
            }
        }

        function copyPaymentUrl() {
            navigator.clipboard.writeText(currentPaymentUrl).then(() => {
                // Show success feedback
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '✅ Copied!';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            });
        }

        function openPaymentPage() {
            window.open(currentPaymentUrl, '_blank');
        }

        function closeModal() {
            document.getElementById('resultModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('resultModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
