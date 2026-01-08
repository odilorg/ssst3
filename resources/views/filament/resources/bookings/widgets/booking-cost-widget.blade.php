@php
    $breakdown = $this->getCostBreakdown();
    $items = $breakdown['items'];
    $byType = $breakdown['byType'];
    $total = $breakdown['total'];
    $currency = $breakdown['currency'];
    
    $currencySymbol = match($currency) {
        'USD' => '$',
        'EUR' => '€',
        'RUB' => '₽',
        default => '$',
    };
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-calculator"
        icon-color="primary"
        collapsible
    >
        <x-slot name="heading">
            Калькулятор стоимости
        </x-slot>
        
        <x-slot name="description">
            Автоматический расчет на основе назначений
        </x-slot>

        <div class="space-y-4" wire:poll.5s>
            {{-- Summary by Type --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                @foreach($byType as $type => $data)
                    @if($data['count'] > 0)
                        <div class="bg-{{ $data['color'] }}-50 dark:bg-{{ $data['color'] }}-900/20 rounded-lg p-3 border border-{{ $data['color'] }}-200 dark:border-{{ $data['color'] }}-700">
                            <div class="flex items-center gap-2 mb-1">
                                <x-dynamic-component :component="$data['icon']" class="w-4 h-4 text-{{ $data['color'] }}-600 dark:text-{{ $data['color'] }}-400" />
                                <span class="text-xs font-medium text-{{ $data['color'] }}-700 dark:text-{{ $data['color'] }}-300">
                                    {{ $data['label'] }}
                                </span>
                            </div>
                            <div class="text-lg font-bold text-{{ $data['color'] }}-900 dark:text-{{ $data['color'] }}-100">
                                {{ $currencySymbol }}{{ number_format($data['cost'], 2) }}
                            </div>
                            <div class="text-xs text-{{ $data['color'] }}-600 dark:text-{{ $data['color'] }}-400">
                                {{ $data['count'] }} назначений
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Detailed Breakdown --}}
            @if(count($items) > 0)
                <div x-data="{ expanded: false }" class="border dark:border-gray-700 rounded-lg overflow-hidden">
                    <button 
                        @click="expanded = !expanded" 
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Детальная разбивка по дням
                        </span>
                        <x-heroicon-o-chevron-down class="w-5 h-5 text-gray-500 transition-transform" x-bind:class="expanded ? 'rotate-180' : ''" />
                    </button>
                    
                    <div x-show="expanded" x-collapse class="divide-y dark:divide-gray-700">
                        @foreach($items as $item)
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item['date'] }}</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['title'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $currencySymbol }}{{ number_format($item['total'], 2) }}
                                    </span>
                                </div>
                                
                                @if(count($item['costs']) > 0)
                                    <div class="space-y-1 ml-4">
                                        @foreach($item['costs'] as $cost)
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2">
                                                    @php
                                                        $typeColor = $byType[$cost['type']]['color'] ?? 'gray';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $typeColor }}-100 text-{{ $typeColor }}-800 dark:bg-{{ $typeColor }}-900 dark:text-{{ $typeColor }}-200">
                                                        {{ $cost['name'] }}
                                                    </span>
                                                    @if($cost['quantity'] > 1)
                                                        <span class="text-gray-500">×{{ $cost['quantity'] }}</span>
                                                    @endif
                                                    @if($cost['isOverride'])
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200" title="Переопределено вручную">
                                                            <x-heroicon-s-pencil class="w-3 h-3" />
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-gray-600 dark:text-gray-400">
                                                    {{ $currencySymbol }}{{ number_format($cost['cost'], 2) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 dark:text-gray-500 ml-4">
                                        Нет назначений
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Total --}}
            <div class="flex items-center justify-between p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border-2 border-primary-200 dark:border-primary-700">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-banknotes class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                    <span class="text-lg font-semibold text-primary-900 dark:text-primary-100">
                        Итого:
                    </span>
                </div>
                <span class="text-2xl font-bold text-primary-900 dark:text-primary-100">
                    {{ $currencySymbol }}{{ number_format($total, 2) }}
                </span>
            </div>

            {{-- Update Total Button --}}
            <div class="flex justify-end">
                <x-filament::button
                    wire:click="updateBookingTotal"
                    color="success"
                    size="sm"
                    icon="heroicon-o-arrow-path"
                >
                    Обновить итого в бронировании
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
