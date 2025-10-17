<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Смета тура - {{ $record->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            color: #2563eb;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header .subtitle {
            color: #666;
            font-size: 1.2rem;
        }

        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #f8fafc;
            color: #1e293b;
            padding: 15px 20px;
            font-size: 1.3rem;
            font-weight: 600;
            border-left: 4px solid #2563eb;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .info-label {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .info-value {
            font-size: 1.1rem;
            color: #1e293b;
            font-weight: 600;
        }

        .day-section {
            margin-bottom: 40px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .day-header {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .day-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
        }

        .day-date {
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        .day-total {
            text-align: right;
        }

        .day-total-amount {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
        }

        .day-total-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 5px;
        }

        .category-section {
            margin-bottom: 25px;
            border-bottom: 1px solid #f1f5f9;
        }

        .category-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .category-header {
            background: #f8fafc;
            padding: 15px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-title {
            display: flex;
            align-items: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .category-icon {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .category-subtotal {
            font-size: 1.1rem;
            font-weight: 600;
            color: #059669;
        }

        .category-items {
            padding: 0 25px 20px 25px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            flex: 1;
            font-weight: 500;
            color: #374151;
        }

        .item-details {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 0.9rem;
            color: #6b7280;
        }

        .item-quantity {
            min-width: 60px;
            text-align: center;
        }

        .item-unit-price {
            min-width: 80px;
            text-align: right;
        }

        .item-total-price {
            min-width: 100px;
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }

        .collapsible {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .collapsible:hover {
            background: #f1f5f9;
        }

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .collapsible-content.expanded {
            max-height: 1000px;
        }

        .collapse-icon {
            transition: transform 0.3s ease;
        }

        .category-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .summary-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .summary-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .summary-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .summary-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .summary-category {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .summary-amount {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 3px;
        }

        .summary-percentage {
            font-size: 0.85rem;
            color: #059669;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
        }

        .badge-success { background: #059669; }
        .badge-warning { background: #d97706; }
        .badge-info { background: #0284c7; }
        .badge-danger { background: #dc2626; }
        .badge-gray { background: #6b7280; }

        .total-section {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-top: 30px;
        }

        .total-amount {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .total-label {
            font-size: 1.2rem;
            margin-bottom: 8px;
            opacity: 0.9;
        }

        .total-disclaimer {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .print-button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #475569;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
        }

        @media print {
            .print-button {
                display: none !important;
            }
            
            body {
                padding: 0;
            }
            
            .container {
                max-width: none;
            }
            
            .section {
                page-break-inside: avoid;
            }
        }

        @page {
            margin: 1cm;
            size: A4;
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">🖨️ Печать</button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Смета тура</h1>
            <div class="subtitle">{{ $record->reference }}</div>
        </div>

        <!-- Tour Information -->
        <div class="section">
            <div class="section-title">Информация о туре</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Номер бронирования</div>
                    <div class="info-value">{{ $record->reference }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Клиент</div>
                    <div class="info-value">{{ $record->customer?->name ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Тур</div>
                    <div class="info-value">{{ $record->tour?->title ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Дата начала</div>
                    <div class="info-value">{{ $record->start_date?->format('d.m.Y') ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Дата окончания</div>
                    <div class="info-value">{{ $record->end_date?->format('d.m.Y') ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Количество участников</div>
                    <div class="info-value">{{ $record->pax_total ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Валюта</div>
                    <div class="info-value">{{ $record->currency ?? 'USD' }}</div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="section">
            <div class="section-title">Детализация расходов</div>
            
            @if(count($dayBreakdown) > 0)
                @foreach($dayBreakdown as $dayIndex => $day)
                    <div class="day-section">
                        <!-- Day Header -->
                        <div class="day-header">
                            <div>
                                <h3 class="day-title">{{ $day['day_title'] }}</h3>
                                <div class="day-date">{{ $day['formatted_date'] }}</div>
                            </div>
                            <div class="day-total">
                                <div class="day-total-amount">${{ number_format($day['day_total'], 2) }}</div>
                                <div class="day-total-label">Итого за день</div>
                            </div>
                        </div>

                        <!-- Categories within the day -->
                        @foreach($day['categories'] as $categoryKey => $category)
                            <div class="category-section">
                                <!-- Category Header -->
                                <div class="category-header collapsible" onclick="toggleCategory({{ $dayIndex }}, '{{ $categoryKey }}')">
                                    <h4 class="category-title">
                                        <span class="category-icon">{{ $category['category_icon'] }}</span>
                                        {{ $category['category_name'] }}
                                    </h4>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <span class="category-subtotal">${{ number_format($category['subtotal'], 2) }}</span>
                                        <span class="collapse-icon" id="icon-{{ $dayIndex }}-{{ $categoryKey }}">▼</span>
                                    </div>
                                </div>

                                <!-- Category Items -->
                                <div class="collapsible-content expanded" id="content-{{ $dayIndex }}-{{ $categoryKey }}">
                                    <div class="category-items">
                                        @foreach($category['items'] as $item)
                                            <div class="item-row">
                                                <div class="item-name">{{ $item['name'] }}</div>
                                                <div class="item-details">
                                                    <div class="item-quantity">{{ $item['quantity'] }}</div>
                                                    <div class="item-unit-price">${{ number_format($item['unit_price'], 2) }}</div>
                                                    <div class="item-total-price">${{ number_format($item['total_price'], 2) }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <h3>Нет данных</h3>
                    <p>Нет назначенных поставщиков для расчета стоимости.</p>
                </div>
            @endif
        </div>

        <!-- Category Summary -->
        @if(count($categorySummary) > 0)
            <div class="category-summary">
                <div class="summary-title">Сводка по категориям</div>
                <div class="summary-grid">
                    @foreach($categorySummary as $summary)
                        <div class="summary-item">
                            <div class="summary-icon">{{ $summary['category_icon'] }}</div>
                            <div class="summary-category">{{ $summary['category_name'] }}</div>
                            <div class="summary-amount">${{ number_format($summary['total'], 2) }}</div>
                            <div class="summary-percentage">{{ $summary['percentage'] }}%</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Total Cost -->
        <div class="total-section">
            <div class="total-amount">${{ number_format($totalCost, 2) }}</div>
            <div class="total-label">Общая стоимость тура</div>
            <div class="total-disclaimer">Все цены указаны в долларах США (USD)</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Смета сгенерирована {{ now()->format('d.m.Y в H:i') }}</p>
        </div>
    </div>

    <script>
        function toggleCategory(dayIndex, categoryKey) {
            const content = document.getElementById(`content-${dayIndex}-${categoryKey}`);
            const icon = document.getElementById(`icon-${dayIndex}-${categoryKey}`);
            
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                icon.classList.add('rotated');
            } else {
                content.classList.add('expanded');
                icon.classList.remove('rotated');
            }
        }

        // Auto-print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };
    </script>
</body>
</html>

