@extends('layouts.app')

@section('title', 'Оплата успешна')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-green-50 to-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto text-center">
            <!-- Success Icon -->
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-4">Оплата успешно завершена!</h1>
            
            <p class="text-gray-600 mb-8">
                Спасибо за ваш заказ. Мы отправили подтверждение на вашу электронную почту.
            </p>

            <!-- Payment Details -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 text-left">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Детали платежа</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Номер транзакции:</span>
                        <span class="font-medium text-gray-900">{{ $payment->octo_shop_transaction_id }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Сумма:</span>
                        <span class="font-bold text-green-600">{{ $payment->formatted_amount }}</span>
                    </div>
                    
                    @if($payment->booking && $payment->booking->tour)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Тур:</span>
                        <span class="font-medium text-gray-900">{{ $payment->booking->tour->title }}</span>
                    </div>
                    @endif
                    
                    @if($payment->masked_pan)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Карта:</span>
                        <span class="font-medium text-gray-900">{{ $payment->masked_pan }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-500">Дата:</span>
                        <span class="font-medium text-gray-900">{{ $payment->updated_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition">
                    На главную
                </a>
                @if($payment->booking && $payment->booking->tour)
                <a href="{{ route('tours.show', $payment->booking->tour->slug) }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                    Вернуться к туру
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
