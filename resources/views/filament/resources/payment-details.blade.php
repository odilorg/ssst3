<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Сумма</h4>
            <p class="text-lg font-bold text-gray-900 dark:text-white">
                {{ $payment->getFormattedAmount() }}
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Статус</h4>
            <p class="text-sm text-gray-900 dark:text-white">
                @if($payment->isCompleted())
                    <span class="text-green-600 font-medium">✓ Завершен</span>
                @elseif($payment->isFailed())
                    <span class="text-red-600 font-medium">✗ Неудачно</span>
                @else
                    <span class="text-yellow-600 font-medium">⏳ В ожидании</span>
                @endif
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Тип платежа</h4>
            <p class="text-sm text-gray-900 dark:text-white capitalize">{{ $payment->payment_type }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Метод оплаты</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $payment->getPaymentMethodName() }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">ID транзакции</h4>
            <p class="text-sm text-gray-900 dark:text-white font-mono">{{ $payment->transaction_id ?? 'N/A' }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Создано</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $payment->created_at->format('d M Y H:i') }}</p>
        </div>
        @if($payment->processed_at)
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Обработано</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $payment->processed_at->format('d M Y H:i') }}</p>
        </div>
        @endif
    </div>

    @if($gatewayResponse && is_array($gatewayResponse) && count($gatewayResponse) > 0)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Ответ платежного шлюза
            </h4>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 max-h-64 overflow-auto">
                <pre class="text-xs text-gray-900 dark:text-white whitespace-pre-wrap">{{ json_encode($gatewayResponse, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Информация о бронировании
        </h4>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Номер бронирования:</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $payment->booking->reference }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Клиент:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ $payment->booking->customer_name }}</span>
            </div>
        </div>
    </div>
</div>
