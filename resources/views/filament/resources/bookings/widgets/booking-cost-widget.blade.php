@php
    $breakdown = $this->getCostBreakdown();
    $items = $breakdown["items"];
    $byType = $breakdown["byType"];
    $total = $breakdown["total"];
    $currency = $breakdown["currency"];
    
    $currencySymbol = match($currency) {
        "USD" => "$",
        "EUR" => "€",
        "RUB" => "₽",
        default => "$",
    };
    
    // Map colors to actual Tailwind classes (must be complete class names)
    $colorMap = [
        "success" => ["bg" => "bg-green-50 dark:bg-green-900/20", "border" => "border-green-200 dark:border-green-700", "text" => "text-green-700 dark:text-green-300", "textBold" => "text-green-900 dark:text-green-100", "badge" => "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"],
        "warning" => ["bg" => "bg-amber-50 dark:bg-amber-900/20", "border" => "border-amber-200 dark:border-amber-700", "text" => "text-amber-700 dark:text-amber-300", "textBold" => "text-amber-900 dark:text-amber-100", "badge" => "bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200"],
        "info" => ["bg" => "bg-sky-50 dark:bg-sky-900/20", "border" => "border-sky-200 dark:border-sky-700", "text" => "text-sky-700 dark:text-sky-300", "textBold" => "text-sky-900 dark:text-sky-100", "badge" => "bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200"],
        "danger" => ["bg" => "bg-red-50 dark:bg-red-900/20", "border" => "border-red-200 dark:border-red-700", "text" => "text-red-700 dark:text-red-300", "textBold" => "text-red-900 dark:text-red-100", "badge" => "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200"],
        "gray" => ["bg" => "bg-gray-50 dark:bg-gray-900/20", "border" => "border-gray-200 dark:border-gray-700", "text" => "text-gray-700 dark:text-gray-300", "textBold" => "text-gray-900 dark:text-gray-100", "badge" => "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200"],
    ];
    
    // Icons as simple text labels
    $typeLabels = [
        "guide" => "👤",
        "restaurant" => "🍽️",
        "hotel" => "🏨",
        "transport" => "🚗",
        "monument" => "🏛️",
    ];
@endphp

<x-filament-widgets::widget>
    <x-filament::section collapsible>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <span>📊</span>
                <span>Калькулятор стоимости</span>
            </div>
        </x-slot>
        
        <x-slot name="description">
            Автоматический расчет на основе назначений
        </x-slot>

        <div class="space-y-4" wire:poll.5s>
            {{-- Summary by Type --}}
            @php
                $hasAnyAssignments = collect($byType)->sum("count") > 0;
            @endphp
            
            @if($hasAnyAssignments)
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    @foreach($byType as $type => $data)
                        @if($data["count"] > 0)
                            @php
                                $colors = $colorMap[$data["color"]] ?? $colorMap["gray"];
                                $icon = $typeLabels[$type] ?? "📦";
                            @endphp
                            <div class="rounded-lg p-3 border {{ $colors["bg"] }} {{ $colors["border"] }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-base">{{ $icon }}</span>
                                    <span class="text-xs font-medium {{ $colors["text"] }}">
                                        {{ $data["label"] }}
                                    </span>
                                </div>
                                <div class="text-lg font-bold {{ $colors["textBold"] }}">
                                    {{ $currencySymbol }}{{ number_format($data["cost"], 2) }}
                                </div>
                                <div class="text-xs {{ $colors["text"] }}">
                                    {{ $data["count"] }} назначений
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                    <span class="text-4xl mb-2 block">📭</span>
                    <p>Нет назначений для расчета</p>
                </div>
            @endif

            {{-- Detailed Breakdown --}}
            @if(count($items) > 0 && $hasAnyAssignments)
                <div x-data="{ expanded: false }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <button 
                        type="button"
                        @click="expanded = !expanded" 
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left"
                    >
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            📋 Детальная разбивка по дням
                        </span>
                        <span x-text="expanded ? '▲' : '▼'" class="text-gray-500"></span>
                    </button>
                    
                    <div x-show="expanded" x-collapse class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($items as $item)
                            <div class="p-4 bg-white dark:bg-gray-900">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                            {{ $item["date"] }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item["title"] }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $currencySymbol }}{{ number_format($item["total"], 2) }}
                                    </span>
                                </div>
                                
                                @if(count($item["costs"]) > 0)
                                    <div class="space-y-1 ml-4">
                                        @foreach($item["costs"] as $cost)
                                            @php
                                                $typeColor = $byType[$cost["type"]]["color"] ?? "gray";
                                                $badgeClass = $colorMap[$typeColor]["badge"] ?? $colorMap["gray"]["badge"];
                                                $icon = $typeLabels[$cost["type"]] ?? "📦";
                                            @endphp
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                        <span>{{ $icon }}</span>
                                                        {{ $cost["name"] }}
                                                    </span>
                                                    @if($cost["quantity"] > 1)
                                                        <span class="text-gray-500 dark:text-gray-400">×{{ $cost["quantity"] }}</span>
                                                    @endif
                                                    @if($cost["isOverride"])
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-200" title="Переопределено вручную">
                                                            ✏️
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-gray-600 dark:text-gray-400 font-medium">
                                                    {{ $currencySymbol }}{{ number_format($cost["cost"], 2) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 dark:text-gray-500 ml-4 italic">
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
                    <span class="text-2xl">💰</span>
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
                >
                    🔄 Обновить итого в бронировании
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
