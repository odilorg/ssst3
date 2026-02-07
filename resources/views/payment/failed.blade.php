@extends('layouts.app')

@section('title', 'Ошибка оплаты')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-red-50 to-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto text-center">
            <!-- Error Icon -->
            <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">Оплата не удалась</h1>
            
            <p class="text-gray-600 mb-8">
                К сожалению, произошла ошибка при обработке платежа. Пожалуйста, попробуйте ещё раз.
            </p>

            @if($payment->error_message)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8 text-left">
                <p class="text-sm text-red-700">
                    <strong>Причина:</strong> {{ $payment->error_message }}
                </p>
            </div>
            @endif

            <!-- Payment Details -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Детали</h2>
                
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $payment->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($payment->booking && $payment->booking->tour)
                <a href="{{ route('tours.show', $payment->booking->tour->slug) }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition">
                    Попробовать снова
                </a>
                @endif
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                    На главную
                </a>
            </div>

            <!-- Help -->
            <p class="mt-8 text-sm text-gray-500">
                Нужна помощь? Свяжитесь с нами: <a href="tel:+998915550808" class="text-primary-600 hover:underline">+998 91 555 0808</a>
            </p>
        </div>
    </div>
</div>
@endsection
