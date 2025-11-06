<div class="space-y-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-blue-900">Token Information</h3>
                <div class="mt-2 text-sm text-blue-700 space-y-1">
                    <p><strong>Booking:</strong> {{ $token->booking->reference }}</p>
                    <p><strong>Customer:</strong> {{ $token->booking->customer_name }}</p>
                    <p><strong>Status:</strong>
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $token->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $token->isValid() ? 'Valid' : 'Invalid' }}
                        </span>
                    </p>
                    <p><strong>Expires:</strong> {{ $token->expires_at->format('d M Y H:i') }}
                        @if($token->isExpired())
                            <span class="text-red-600 font-medium">(Expired)</span>
                        @elseif($token->expires_at->isBefore(now()->addDay()))
                            <span class="text-orange-600 font-medium">(Expires soon)</span>
                        @endif
                    </p>
                    @if($token->isUsed())
                        <p><strong>Used:</strong> {{ $token->used_at?->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Payment URL
        </label>
        <div class="flex items-center space-x-2">
            <input
                type="text"
                value="{{ $url }}"
                readonly
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm font-mono"
                id="payment-url-input"
            >
            <button
                type="button"
                onclick="copyToClipboard()"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm font-medium"
            >
                Copy
            </button>
        </div>
        <p class="mt-2 text-xs text-gray-500">
            Share this URL with the customer to allow them to complete their payment.
        </p>
    </div>

    @if($token->isValid())
    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
        <p class="text-sm text-green-800">
            ✓ This token is active and can be used for payment.
        </p>
    </div>
    @elseif($token->isExpired())
    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
        <p class="text-sm text-red-800">
            ✗ This token has expired. Please regenerate a new token.
        </p>
    </div>
    @elseif($token->isUsed())
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
        <p class="text-sm text-gray-800">
            This token has already been used for payment.
        </p>
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
        <p class="text-sm text-gray-800">
            This token is not valid.
        </p>
    </div>
    @endif
</div>

<script>
function copyToClipboard() {
    const input = document.getElementById('payment-url-input');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices

    try {
        navigator.clipboard.writeText(input.value).then(function() {
            // Success feedback
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-green-600');

            setTimeout(function() {
                button.textContent = originalText;
                button.classList.remove('bg-green-600');
                button.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        });
    } catch (err) {
        // Fallback for older browsers
        document.execCommand('copy');
        alert('URL copied to clipboard!');
    }
}
</script>
