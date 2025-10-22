<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка на ресторан</title>
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

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #d97706;
        }

        .header h1 {
            color: #d97706;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header .subtitle {
            color: #666;
            font-size: 1.2rem;
        }

        .content {
            max-width: 800px;
            margin: 0 auto;
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
            border-left: 4px solid #d97706;
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

        .restaurant-details {
            background: #fef3c7;
            border: 1px solid #d97706;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .restaurant-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 10px;
        }

        .restaurant-address {
            color: #a16207;
            font-size: 1rem;
        }

        .meal-details {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .meal-details h3 {
            color: #0c4a6e;
            margin-bottom: 15px;
        }

        .meal-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .meal-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .meal-type {
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 5px;
        }

        .meal-time {
            color: #64748b;
            font-size: 0.9rem;
        }

        .requirements {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .requirements h3 {
            color: #92400e;
            margin-bottom: 10px;
        }

        .dietary {
            background: #f0fdf4;
            border: 1px solid #059669;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .dietary h3 {
            color: #14532d;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
        }

        .urgent {
            background: #fef2f2;
            border: 2px solid #ef4444;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .urgent-text {
            color: #dc2626;
            font-weight: 700;
            font-size: 1.1rem;
        }

        @media print {
            body {
                padding: 0;
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
    <div class="content">
        <!-- Header -->
        <div class="header">
            <h1>ЗАЯВКА НА РЕСТОРАН</h1>
            <div class="subtitle">Jahongir Travel OOO</div>
        </div>

        <!-- Urgent Notice -->
        <div class="urgent">
            <div class="urgent-text">
                ⏰ СРОК ПОДТВЕРЖДЕНИЯ: {{ $requestData['expires_at'] }}
            </div>
        </div>

        <!-- Restaurant Details -->
        <div class="restaurant-details">
            <div class="restaurant-name">🍽️ {{ $requestData['restaurant_name'] }}</div>
            <div class="restaurant-address">📍 {{ $requestData['restaurant_address'] }}</div>
        </div>

        <!-- Booking Information -->
        <div class="section">
            <div class="section-title">Информация о заказе</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Номер бронирования</div>
                    <div class="info-value">{{ $requestData['booking_reference'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Клиент</div>
                    <div class="info-value">{{ $requestData['customer_name'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Размер группы</div>
                    <div class="info-value">{{ $requestData['group_size'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Валюта</div>
                    <div class="info-value">{{ $requestData['currency'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Дата создания заявки</div>
                    <div class="info-value">{{ $requestData['generated_at'] }}</div>
                </div>
            </div>
        </div>

        <!-- Meal Details -->
        <div class="meal-details">
            <h3>🍴 Детали питания</h3>
            <div class="meal-info">
                <div class="meal-item">
                    <div class="meal-type">{{ $requestData['meal_type'] }}</div>
                    <div class="meal-time">Время: {{ $requestData['meal_time'] }}</div>
                </div>
            </div>
        </div>

        <!-- Dietary Requirements -->
        <div class="dietary">
            <h3>🥗 Диетические требования</h3>
            <p>{{ $requestData['dietary_requirements'] }}</p>
        </div>

        <!-- Special Requirements -->
        <div class="requirements">
            <h3>Особые требования и пожелания</h3>
            <p>{{ $requestData['special_requirements'] }}</p>
        </div>

        <!-- Contact Information -->
        <div class="section">
            <div class="section-title">Контактная информация</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Туристическая компания</div>
                    <div class="info-value">Jahongir Travel OOO</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Пожалуйста, подтвердите или отклоните данную заявку в течение указанного срока.</p>
            <p>Спасибо за сотрудничество!</p>
        </div>
    </div>
</body>
</html>
