<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявка на ресторан</title>
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .letterhead {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company-info {
            font-size: 10pt;
            line-height: 1.3;
        }

        .company-name {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 3px;
        }

        .meta-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .meta-left {
            display: table-cell;
            width: 40%;
        }

        .meta-right {
            display: table-cell;
            width: 60%;
            text-align: right;
        }

        .field-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 120px;
            padding: 0 5px;
        }

        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0 15px 0;
            letter-spacing: 2px;
        }

        .intro-text {
            margin-bottom: 15px;
            font-size: 10pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table td, table th {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10pt;
        }

        table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: left;
        }

        .highlight {
            background: #ffff00;
            font-weight: bold;
        }

        .section-header {
            font-weight: bold;
            background: #e0e0e0;
            padding: 4px 6px;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .note-box {
            border: 1px solid #000;
            padding: 10px;
            min-height: 60px;
            margin-bottom: 15px;
        }

        .footer-text {
            font-size: 9pt;
            margin-top: 20px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Letterhead -->
    <div class="letterhead">
        <div class="company-name">Jahongir Travel OOO</div>
        <div class="company-info">
            48, Usto Umar Jurakulov str., Samarkand, Uzbekistan 140100<br>
            Tel: +998 55 7045000; E-mail: info@jahongir-travel.com; Web: www.jahongir-travel.com
        </div>
    </div>

    <!-- Meta information -->
    <div class="meta-row">
        <div class="meta-left">
            № <span class="field-line">{{ $requestData['booking_reference'] }}</span><br>
            № <span class="field-line">{{ date('d.m.Y') }}</span>
        </div>
        <div class="meta-right">
            <strong>To:</strong> Restaurant <span class="highlight">{{ $requestData['restaurant_name'] }}</span><br>
            <strong>Address:</strong> <span class="highlight">{{ $requestData['restaurant_address'] }}</span><br>
            <strong>Att:</strong> Restaurant Manager
        </div>
    </div>

    <!-- Title -->
    <div class="title">ЗАЯВКА НА РЕСТОРАН / RESTAURANT REQUEST</div>

    <!-- Introduction -->
    <div class="intro-text">
        <em>Пожалуйста, предоставьте и подтвердите письменно следующие услуги ресторана для группы туристов:</em><br>
        <em>Please, give and confirm the following restaurant services for tourists' group:</em>
    </div>

    <!-- Main booking table -->
    <table>
        <tr>
            <th>1. Страна<br>Country</th>
            <td class="highlight">{{ $booking->country ?? 'UZBEKISTAN' }}</td>
            <th>Размер группы<br>Group Size</th>
            <td class="highlight">{{ $requestData['group_size'] ?? $requestData['pax_total'] }}</td>
            <th>Номер заказа<br>Ref.</th>
            <td class="highlight">{{ $requestData['booking_reference'] }}</td>
        </tr>
    </table>

    <!-- Restaurant Information -->
    <div class="section-header">2. Информация о ресторане / Restaurant Information:</div>
    <table>
        <tr>
            <th style="width: 180px">Название<br>Restaurant Name</th>
            <td class="highlight" colspan="3">{{ $requestData['restaurant_name'] }}</td>
        </tr>
        <tr>
            <th>Адрес<br>Address</th>
            <td class="highlight" colspan="3">{{ $requestData['restaurant_address'] }}</td>
        </tr>
    </table>

    <!-- Meal Details -->
    <div class="section-header">3. Детали питания / Meal Details:</div>
    <table>
        <tr>
            <th style="width: 180px">Тип питания<br>Meal Type</th>
            <td class="highlight">{{ $requestData['meal_type'] }}</td>
            <th style="width: 180px">Время<br>Time</th>
            <td class="highlight">{{ $requestData['meal_time'] }}</td>
        </tr>
        <tr>
            <th>Размер группы<br>Group Size</th>
            <td class="highlight">{{ $requestData['group_size'] ?? $requestData['pax_total'] }} человек</td>
            <th>Дата<br>Date</th>
            <td class="highlight">{{ $requestData['start_date'] }}</td>
        </tr>
    </table>

    <!-- Service Period -->
    <div class="section-header">4. Период обслуживания / Service Period:</div>
    <table>
        <tr>
            <th style="width: 180px">Начало<br>Start Date</th>
            <td class="highlight">{{ $requestData['start_date'] }}</td>
            <th style="width: 180px">Окончание<br>End Date</th>
            <td class="highlight">{{ $requestData['end_date'] }}</td>
        </tr>
    </table>

    <!-- Menu Requirements -->
    <div class="section-header">5. Требования к меню / Menu Requirements:</div>
    <table>
        <tr>
            <th style="width: 180px">Тип меню<br>Menu Type</th>
            <td colspan="3">Стандартное меню / Standard menu</td>
        </tr>
        <tr>
            <th>Диетические требования<br>Dietary Requirements</th>
            <td colspan="3" class="highlight">{{ $requestData['dietary_requirements'] }}</td>
        </tr>
    </table>

    <!-- Pricing -->
    <div class="section-header">6. Стоимость / Pricing:</div>
    <table>
        <tr>
            <th style="width: 180px">Тип питания<br>Meal Type</th>
            <td>{{ $requestData['meal_type'] }}</td>
            <th style="width: 180px">Количество персон<br>Persons</th>
            <td class="highlight">{{ $requestData['group_size'] ?? $requestData['pax_total'] }}</td>
        </tr>
        <tr>
            <th>Валюта<br>Currency</th>
            <td colspan="3" class="highlight">{{ $requestData['currency'] }}</td>
        </tr>
    </table>

    <!-- Client Name -->
    <div class="section-header">7. Client's name:</div>
    <div style="background: #ffff00; padding: 8px; font-weight: bold; text-align: center; margin-bottom: 15px;">
        {{ strtoupper($requestData['customer_name']) }}
    </div>

    <!-- Special Requirements -->
    <div class="section-header">8. Особые требования / Special Requirements:</div>
    <div class="note-box">
        <strong>{{ $requestData['special_requirements'] }}</strong>
    </div>

    <!-- Seating Preferences -->
    <div class="section-header">9. Предпочтения по размещению / Seating Preferences:</div>
    <div class="note-box" style="min-height: 40px;">
        Просим предоставить отдельный зал или столы для группы<br>
        Please provide separate hall or tables for the group
    </div>

    <!-- Additional Notes -->
    <div class="section-header">10. Дополнительные заметки / Additional Notes:</div>
    <div class="note-box" style="min-height: 40px;">

    </div>

    <!-- Footer -->
    <div class="footer-text">
        Пожалуйста, подтвердите или отклоните данную заявку до {{ $requestData['expires_at'] }}<br>
        Please confirm or reject this application before {{ $requestData['expires_at'] }}<br>
        Спасибо за сотрудничество! / Thank you for cooperation!
    </div>
</body>
</html>
