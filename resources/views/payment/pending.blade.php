@extends('layouts.app')

@section('title', 'Ожидание оплаты')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-yellow-50 to-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto text-center">
            <!-- Loading Icon -->
            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-yellow-600 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">Обработка платежа...</h1>
            
            <p class="text-gray-600 mb-8">
                Пожалуйста, подождите. Мы проверяем статус вашего платежа.
            </p>

            <!-- Payment Details -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 text-left">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Номер транзакции:</span>
                        <span class="font-medium text-gray-900">{{ $payment->octo_shop_transaction_id }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Сумма:</span>
                        <span class="font-medium text-gray-900">{{ $payment->formatted_amount }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Статус:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $payment->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Auto-refresh notice -->
            <p class="text-sm text-gray-500 mb-8">
                Страница обновится автоматически через <span id="countdown">10</span> секунд...
            </p>

            <!-- Manual refresh button -->
            <button onclick="location.reload()" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Обновить статус
            </button>
        </div>
    </div>
</div>

<script>
    // Countdown and auto-refresh
    let seconds = 10;
    const countdownEl = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        seconds--;
        countdownEl.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(timer);
            location.reload();
        }
    }, 1000);
</script>
@endsection
