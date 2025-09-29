<style>
/* Custom styles for booking estimate modal */
.booking-estimate-modal {
    padding: 1.5rem;
}

.booking-estimate-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    border-radius: 0.75rem;
    background: white;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.dark .booking-estimate-section {
    background: rgb(17, 24, 39);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.booking-estimate-section h3 {
    margin-bottom: 1.5rem !important;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.dark .booking-estimate-section h3 {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.booking-estimate-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.booking-estimate-info-item {
    padding: 1rem;
    background: rgba(0, 0, 0, 0.02);
    border-radius: 0.5rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.dark .booking-estimate-info-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.booking-estimate-info-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: rgb(107, 114, 128);
    margin-bottom: 0.5rem;
}

.dark .booking-estimate-info-label {
    color: rgb(156, 163, 175);
}

.booking-estimate-info-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: rgb(17, 24, 39);
}

.dark .booking-estimate-info-value {
    color: rgb(243, 244, 246);
}

.booking-estimate-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.booking-estimate-table th {
    padding: 1rem 0.75rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    color: rgb(17, 24, 39);
    background: rgba(0, 0, 0, 0.02);
    border-bottom: 2px solid rgba(0, 0, 0, 0.1);
}

.dark .booking-estimate-table th {
    color: rgb(243, 244, 246);
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

.booking-estimate-table td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    vertical-align: middle;
}

.dark .booking-estimate-table td {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.booking-estimate-table tr:hover {
    background: rgba(0, 0, 0, 0.02);
}

.dark .booking-estimate-table tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.booking-estimate-total {
    text-align: center;
    padding: 2rem;
    background: rgba(34, 197, 94, 0.05);
    border-radius: 0.75rem;
    border: 2px solid rgba(34, 197, 94, 0.1);
}

.dark .booking-estimate-total {
    background: rgba(34, 197, 94, 0.1);
    border: 2px solid rgba(34, 197, 94, 0.2);
}

.booking-estimate-total-amount {
    font-size: 3rem;
    font-weight: 700;
    color: rgb(34, 197, 94);
    margin-bottom: 0.5rem;
}

.dark .booking-estimate-total-amount {
    color: rgb(74, 222, 128);
}

.booking-estimate-total-label {
    font-size: 1rem;
    font-weight: 500;
    color: rgb(55, 65, 81);
    margin-bottom: 0.5rem;
}

.dark .booking-estimate-total-label {
    color: rgb(209, 213, 219);
}

.booking-estimate-total-disclaimer {
    font-size: 0.875rem;
    color: rgb(107, 114, 128);
}

.dark .booking-estimate-total-disclaimer {
    color: rgb(156, 163, 175);
}

.booking-estimate-empty {
    text-align: center;
    padding: 3rem 2rem;
    color: rgb(107, 114, 128);
}

.dark .booking-estimate-empty {
    color: rgb(156, 163, 175);
}

.booking-estimate-empty h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.booking-estimate-empty p {
    font-size: 0.875rem;
}

/* Print styles */
@media print {
    /* Ensure all content is visible when printing */
    * {
        overflow: visible !important;
        height: auto !important;
        max-height: none !important;
    }
    
    body, html {
        height: auto !important;
        overflow: visible !important;
    }
    
    /* Remove any modal constraints for printing */
    .fi-modal-content,
    .fi-modal-body,
    .fi-modal-scrollable {
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }
    
    .booking-estimate-modal {
        padding: 0;
        background: white !important;
        height: auto !important;
        overflow: visible !important;
    }
    
    .booking-estimate-section {
        background: white !important;
        border: 1px solid #000 !important;
        box-shadow: none !important;
        margin-bottom: 1rem;
        break-inside: avoid;
        page-break-inside: avoid;
        height: auto !important;
        overflow: visible !important;
    }
    
    .booking-estimate-section h3 {
        color: #000 !important;
        border-bottom: 2px solid #000 !important;
    }
    
    .booking-estimate-info-item {
        background: #f9f9f9 !important;
        border: 1px solid #ddd !important;
    }
    
    .booking-estimate-info-label {
        color: #333 !important;
    }
    
    .booking-estimate-info-value {
        color: #000 !important;
    }
    
    .booking-estimate-table {
        border: 1px solid #000 !important;
    }
    
    .booking-estimate-table th {
        background: #f0f0f0 !important;
        color: #000 !important;
        border: 1px solid #000 !important;
    }
    
    .booking-estimate-table td {
        border: 1px solid #ddd !important;
        color: #000 !important;
    }
    
    .booking-estimate-total {
        background: #f0f8f0 !important;
        border: 2px solid #000 !important;
    }
    
    .booking-estimate-total-amount {
        color: #000 !important;
    }
    
    .booking-estimate-total-label {
        color: #000 !important;
    }
    
    .booking-estimate-total-disclaimer {
        color: #333 !important;
    }
    
    .booking-estimate-empty {
        color: #333 !important;
    }
    
    /* Hide print button when printing */
    .print-button {
        display: none !important;
    }
}

.print-button {
    margin-bottom: 1rem;
}
</style>

<div class="booking-estimate-modal">
    <!-- Print Button -->
    <div class="print-button">
        <div class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer" style="text-decoration: none; user-select: none;" id="print-estimate-btn">
            <svg style="width: 16px !important; height: 16px !important;" class="mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Печать сметы
        </div>
    </div>

    <script>
    function printEstimate(event) {
        // Prevent default button behavior
        if (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
        }
        
        // Store the content data before any modal changes
        const estimateData = {
            reference: '{{ $record->reference }}',
            customer: '{{ $record->customer?->name ?? "—" }}',
            tour: '{{ $record->tour?->title ?? "—" }}',
            startDate: '{{ $record->start_date?->format("d.m.Y") ?? "—" }}',
            endDate: '{{ $record->end_date?->format("d.m.Y") ?? "—" }}',
            paxTotal: '{{ $record->pax_total ?? "—" }}',
            currency: '{{ $record->currency ?? "USD" }}',
            totalCost: {{ $totalCost }},
            costBreakdown: @json($costBreakdown)
        };
        
        // Create a new window for printing
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        
        if (!printWindow) {
            alert('Пожалуйста, разрешите всплывающие окна для печати');
            return;
        }
        
        // Generate the cost breakdown table HTML
        let tableHtml = '';
        if (estimateData.costBreakdown.length > 0) {
            tableHtml = `
                <table class="booking-estimate-table">
                    <thead>
                        <tr>
                            <th>Категория</th>
                            <th>Наименование</th>
                            <th class="text-center">Количество</th>
                            <th class="text-right">Цена за единицу</th>
                            <th class="text-right">Общая стоимость</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${estimateData.costBreakdown.map(item => `
                            <tr>
                                <td>
                                    <span class="badge badge-${item.category === 'guide' ? 'success' : item.category === 'restaurant' ? 'warning' : item.category === 'hotel' ? 'info' : item.category === 'transport' ? 'danger' : 'gray'}">
                                        ${item.category === 'guide' ? 'Гид' : item.category === 'restaurant' ? 'Ресторан' : item.category === 'hotel' ? 'Гостиница' : item.category === 'transport' ? 'Транспорт' : 'Другое'}
                                    </span>
                                </td>
                                <td>${item.item}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-right">$${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td class="text-right font-bold">$${parseFloat(item.total_price).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        } else {
            tableHtml = `
                <div class="booking-estimate-empty">
                    <h3>Нет данных</h3>
                    <p>Нет назначенных поставщиков для расчета стоимости.</p>
                </div>
            `;
        }
        
        // Write the complete HTML structure for printing
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Смета тура - ${estimateData.reference}</title>
                <meta charset="utf-8">
                <style>
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        margin: 0;
                        padding: 20px;
                        background: white;
                        color: #000;
                        line-height: 1.5;
                    }
                    
                    .booking-estimate-modal {
                        padding: 0;
                        background: white !important;
                        height: auto !important;
                        overflow: visible !important;
                    }
                    
                    .booking-estimate-section {
                        background: white !important;
                        border: 1px solid #000 !important;
                        box-shadow: none !important;
                        margin-bottom: 1rem;
                        break-inside: avoid;
                        page-break-inside: avoid;
                        height: auto !important;
                        overflow: visible !important;
                        padding: 1.5rem;
                        border-radius: 0.75rem;
                    }
                    
                    .booking-estimate-section h3 {
                        color: #000 !important;
                        border-bottom: 2px solid #000 !important;
                        margin-bottom: 1.5rem !important;
                        padding-bottom: 0.75rem;
                        font-size: 1.25rem;
                        font-weight: bold;
                    }
                    
                    .booking-estimate-info-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                        gap: 1.5rem;
                    }
                    
                    .booking-estimate-info-item {
                        padding: 1rem;
                        background: #f9f9f9 !important;
                        border: 1px solid #ddd !important;
                        border-radius: 0.5rem;
                    }
                    
                    .booking-estimate-info-label {
                        font-size: 0.875rem;
                        font-weight: 500;
                        color: #333 !important;
                        margin-bottom: 0.5rem;
                    }
                    
                    .booking-estimate-info-value {
                        font-size: 1.125rem;
                        font-weight: 600;
                        color: #000 !important;
                    }
                    
                    .booking-estimate-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 1rem;
                        border: 1px solid #000 !important;
                    }
                    
                    .booking-estimate-table th {
                        padding: 1rem 0.75rem;
                        text-align: left;
                        font-weight: 600;
                        font-size: 0.875rem;
                        color: #000 !important;
                        background: #f0f0f0 !important;
                        border: 1px solid #000 !important;
                    }
                    
                    .booking-estimate-table td {
                        padding: 1rem 0.75rem;
                        border: 1px solid #ddd !important;
                        color: #000 !important;
                    }
                    
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .font-bold { font-weight: bold; }
                    
                    .badge {
                        padding: 0.25rem 0.5rem;
                        border-radius: 0.375rem;
                        font-size: 0.75rem;
                        font-weight: 500;
                        color: white;
                    }
                    .badge-success { background-color: #10b981; }
                    .badge-warning { background-color: #f59e0b; }
                    .badge-info { background-color: #3b82f6; }
                    .badge-danger { background-color: #ef4444; }
                    .badge-gray { background-color: #6b7280; }
                    
                    .booking-estimate-total {
                        text-align: center;
                        padding: 2rem;
                        background: #f0f8f0 !important;
                        border-radius: 0.75rem;
                        border: 2px solid #000 !important;
                    }
                    
                    .booking-estimate-total-amount {
                        font-size: 3rem;
                        font-weight: 700;
                        color: #000 !important;
                        margin-bottom: 0.5rem;
                    }
                    
                    .booking-estimate-total-label {
                        font-size: 1rem;
                        font-weight: 500;
                        color: #000 !important;
                        margin-bottom: 0.5rem;
                    }
                    
                    .booking-estimate-total-disclaimer {
                        font-size: 0.875rem;
                        color: #333 !important;
                    }
                    
                    .booking-estimate-empty {
                        text-align: center;
                        padding: 3rem 2rem;
                        color: #333 !important;
                    }
                    
                    .booking-estimate-empty h3 {
                        font-size: 1.125rem;
                        font-weight: 600;
                        margin-bottom: 0.5rem;
                    }
                    
                    .booking-estimate-empty p {
                        font-size: 0.875rem;
                    }
                    
                    @page {
                        margin: 1cm;
                        size: A4;
                    }
                    
                    @media print {
                        body { margin: 0; }
                    }
                </style>
            </head>
            <body>
                <div class="booking-estimate-modal">
                    <!-- Tour Information Section -->
                    <div class="booking-estimate-section">
                        <h3>Информация о туре</h3>
                        <div class="booking-estimate-info-grid">
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Номер бронирования</div>
                                <div class="booking-estimate-info-value">${estimateData.reference}</div>
                            </div>
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Клиент</div>
                                <div class="booking-estimate-info-value">${estimateData.customer}</div>
                            </div>
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Тур</div>
                                <div class="booking-estimate-info-value">${estimateData.tour}</div>
                            </div>
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Дата начала</div>
                                <div class="booking-estimate-info-value">${estimateData.startDate}</div>
                            </div>
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Дата окончания</div>
                                <div class="booking-estimate-info-value">${estimateData.endDate}</div>
                            </div>
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Количество участников</div>
                                <div class="booking-estimate-info-value">${estimateData.paxTotal}</div>
                            </div>
                            <div class="booking-estimate-info-item">
                                <div class="booking-estimate-info-label">Валюта</div>
                                <div class="booking-estimate-info-value">${estimateData.currency}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Breakdown Section -->
                    <div class="booking-estimate-section">
                        <h3>Детализация расходов</h3>
                        ${tableHtml}
                    </div>

                    <!-- Total Cost Section -->
                    <div class="booking-estimate-section">
                        <h3>Итоговая сумма</h3>
                        <div class="booking-estimate-total">
                            <div class="booking-estimate-total-amount">
                                $${parseFloat(estimateData.totalCost).toFixed(2)}
                            </div>
                            <div class="booking-estimate-total-label">
                                Общая стоимость тура
                            </div>
                            <div class="booking-estimate-total-disclaimer">
                                Все цены указаны в долларах США (USD)
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        
        // Wait for content to load, then print
        setTimeout(function() {
            printWindow.print();
        }, 500);
        
        // Return false to prevent any further event handling
        return false;
    }
    
    // Simple approach - use direct event assignment
    document.getElementById('print-estimate-btn').onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        console.log('Print button clicked directly');
        printEstimate(e);
        return false;
    };
    </script>

    <!-- Tour Information Section -->
    <div class="booking-estimate-section">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            Информация о туре
        </h3>
        
        <div class="booking-estimate-info-grid">
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Номер бронирования</div>
                <div class="booking-estimate-info-value">{{ $record->reference }}</div>
            </div>
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Клиент</div>
                <div class="booking-estimate-info-value">{{ $record->customer?->name ?? '—' }}</div>
            </div>
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Тур</div>
                <div class="booking-estimate-info-value">{{ $record->tour?->title ?? '—' }}</div>
            </div>
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Дата начала</div>
                <div class="booking-estimate-info-value">{{ $record->start_date?->format('d.m.Y') ?? '—' }}</div>
            </div>
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Дата окончания</div>
                <div class="booking-estimate-info-value">{{ $record->end_date?->format('d.m.Y') ?? '—' }}</div>
            </div>
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Количество участников</div>
                <div class="booking-estimate-info-value">{{ $record->pax_total ?? '—' }}</div>
            </div>
            <div class="booking-estimate-info-item">
                <div class="booking-estimate-info-label">Валюта</div>
                <div class="booking-estimate-info-value">{{ $record->currency ?? 'USD' }}</div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown Section -->
    <div class="booking-estimate-section">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            Детализация расходов
        </h3>
        
        @if(count($costBreakdown) > 0)
            <div class="overflow-x-auto">
                <table class="booking-estimate-table">
                    <thead>
                        <tr>
                            <th>Категория</th>
                            <th>Наименование</th>
                            <th class="text-center">Количество</th>
                            <th class="text-right">Цена за единицу</th>
                            <th class="text-right">Общая стоимость</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costBreakdown as $item)
                            <tr>
                                <td>
                                    <x-filament::badge 
                                        :color="match($item['category']) {
                                            'guide' => 'success',
                                            'restaurant' => 'warning',
                                            'hotel' => 'info',
                                            'transport' => 'danger',
                                            default => 'gray'
                                        }">
                                        @switch($item['category'])
                                            @case('guide') Гид @break
                                            @case('restaurant') Ресторан @break
                                            @case('hotel') Гостиница @break
                                            @case('transport') Транспорт @break
                                            @default Другое @break
                                        @endswitch
                                    </x-filament::badge>
                                </td>
                                <td class="text-gray-900 dark:text-white">{{ $item['item'] }}</td>
                                <td class="text-center">
                                    <x-filament::badge color="gray">{{ $item['quantity'] }}</x-filament::badge>
                                </td>
                                <td class="text-right text-gray-900 dark:text-white">${{ number_format($item['unit_price'], 2) }}</td>
                                <td class="text-right font-bold text-gray-900 dark:text-white">${{ number_format($item['total_price'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="booking-estimate-empty">
                <h3>Нет данных</h3>
                <p>Нет назначенных поставщиков для расчета стоимости.</p>
            </div>
        @endif
    </div>

    <!-- Total Cost Section -->
    <div class="booking-estimate-section">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
            Итоговая сумма
        </h3>
        
        <div class="booking-estimate-total">
            <div class="booking-estimate-total-amount">
                ${{ number_format($totalCost, 2) }}
            </div>
            <div class="booking-estimate-total-label">
                Общая стоимость тура
            </div>
            <div class="booking-estimate-total-disclaimer">
                Все цены указаны в долларах США (USD)
            </div>
        </div>
    </div>
</div>
