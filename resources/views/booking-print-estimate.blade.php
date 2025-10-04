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

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background: #1e293b;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table th.center {
            text-align: center;
        }

        .table th.right {
            text-align: right;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .table tr:hover {
            background: #f8fafc;
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
            
            @if(count($costBreakdown) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Категория</th>
                            <th>Наименование</th>
                            <th class="center">Количество</th>
                            <th class="right">Цена за единицу</th>
                            <th class="right">Общая стоимость</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costBreakdown as $item)
                            <tr>
                                <td>
                                    <span class="badge badge-{{ $item['category'] === 'guide' ? 'success' : ($item['category'] === 'restaurant' ? 'warning' : ($item['category'] === 'hotel' ? 'info' : ($item['category'] === 'transport' ? 'danger' : 'gray'))) }}">
                                        @switch($item['category'])
                                            @case('guide') Гид @break
                                            @case('restaurant') Ресторан @break
                                            @case('hotel') Гостиница @break
                                            @case('transport') Транспорт @break
                                            @default Другое @break
                                        @endswitch
                                    </span>
                                </td>
                                <td>{{ $item['item'] }}</td>
                                <td class="center">{{ $item['quantity'] }}</td>
                                <td class="right">${{ number_format($item['unit_price'], 2) }}</td>
                                <td class="right"><strong>${{ number_format($item['total_price'], 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <h3>Нет данных</h3>
                    <p>Нет назначенных поставщиков для расчета стоимости.</p>
                </div>
            @endif
        </div>

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
        // Auto-print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 1000);
        // };
    </script>
</body>
</html>

