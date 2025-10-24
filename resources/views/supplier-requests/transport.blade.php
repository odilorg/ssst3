<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявка на транспорт</title>
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
            <strong>To:</strong> Transport Company <span class="highlight">{{ $requestData['vehicle_make'] ?? 'TRANSPORT' }}</span><br>
            <strong>Vehicle:</strong> <span class="highlight">{{ $requestData['transport_name'] }} - {{ $requestData['plate_number'] }}</span><br>
            <strong>Att:</strong> Dispatch Department
        </div>
    </div>

    <!-- Title -->
    <div class="title">ЗАЯВКА НА ТРАНСПОРТ / TRANSPORT REQUEST</div>

    <!-- Introduction -->
    <div class="intro-text">
        <em>Пожалуйста, предоставьте и подтвердите письменно следующие транспортные услуги для группы туристов:</em><br>
        <em>Please, give and confirm the following transport services for tourists' group:</em>
    </div>

    <!-- Main booking table -->
    <table>
        <tr>
            <th>1. Страна<br>Country</th>
            <td class="highlight">{{ $booking->country ?? 'UZBEKISTAN' }}</td>
            <th>Пассажиров<br>Passengers</th>
            <td class="highlight">{{ $requestData['pax_total'] }}</td>
            <th>Номер заказа<br>Ref.</th>
            <td class="highlight">{{ $requestData['booking_reference'] }}</td>
        </tr>
    </table>

    <!-- Transport Details -->
    <div class="section-header">2. Информация о транспорте / Transport Information:</div>
    <table>
        <tr>
            <th style="width: 180px">Тип транспорта<br>Transport Type</th>
            <td class="highlight" colspan="3">{{ $requestData['transport_name'] }}</td>
        </tr>
        <tr>
            <th>Производитель<br>Make</th>
            <td class="highlight">{{ $requestData['vehicle_make'] ?? 'N/A' }}</td>
            <th style="width: 150px">Модель<br>Model</th>
            <td class="highlight">{{ $requestData['vehicle_model'] }}</td>
        </tr>
        <tr>
            <th>Номер<br>Plate Number</th>
            <td class="highlight">{{ $requestData['plate_number'] }}</td>
            <th>Вместимость<br>Capacity</th>
            <td class="highlight">{{ $requestData['capacity'] }} пассажиров</td>
        </tr>
        <tr>
            <th>Водитель<br>Driver</th>
            <td class="highlight">{{ $requestData['driver_required'] ? 'Требуется / Required' : 'Не требуется / Not required' }}</td>
            <th>Тип тарифа<br>Price Type</th>
            <td class="highlight">{{ $requestData['price_type'] }}</td>
        </tr>
    </table>

    <!-- Service Period -->
    <div class="section-header">3. Период использования / Service Period:</div>
    <table>
        <tr>
            <th style="width: 150px">Начало<br>Start Date</th>
            <td class="highlight">{{ $requestData['start_date'] }}</td>
            <th style="width: 150px">Окончание<br>End Date</th>
            <td class="highlight">{{ $requestData['end_date'] }}</td>
        </tr>
        @if(!empty($requestData['start_time']) && $requestData['start_time'] !== 'Не указано')
        <tr>
            <th>Время начала<br>Start Time</th>
            <td class="highlight">{{ $requestData['start_time'] }}</td>
            @if(!empty($requestData['end_time']) && $requestData['end_time'] !== 'Не указано')
            <th>Время окончания<br>End Time</th>
            <td class="highlight">{{ $requestData['end_time'] }}</td>
            @else
            <th colspan="2"></th>
            @endif
        </tr>
        @endif
    </table>

    <!-- Route Information -->
    @if(!empty($requestData['route_info']))
    <div class="section-header">4. Информация о маршруте / Route Information:</div>
    <table>
        <tr>
            <th style="width: 180px">Место посадки<br>Pickup Location</th>
            <td class="highlight" colspan="3">{{ $requestData['route_info']['pickup_location'] }}</td>
        </tr>
        <tr>
            <th>Место высадки<br>Dropoff Location</th>
            <td class="highlight" colspan="3">{{ $requestData['route_info']['dropoff_location'] }}</td>
        </tr>
        @if(!empty($requestData['route_info']['route_description']))
        <tr>
            <th>Описание маршрута<br>Route Description</th>
            <td colspan="3">{{ $requestData['route_info']['route_description'] }}</td>
        </tr>
        @endif
    </table>
    @endif

    <!-- Usage Dates -->
    @if(is_array($requestData['usage_dates']) && count($requestData['usage_dates']) > 0)
    <div class="section-header">5. Даты использования / Usage Dates:</div>
    <table>
        <tr>
            <th>Дата<br>Date</th>
            <th>День<br>Day</th>
            <th>Время<br>Time</th>
        </tr>
        @foreach($requestData['usage_dates'] as $dateInfo)
        <tr>
            <td class="highlight">{{ $dateInfo['date'] }}</td>
            <td>{{ $dateInfo['day_title'] }}</td>
            <td>{{ $dateInfo['start_time'] ?? '-' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- Pricing -->
    <div class="section-header">6. Стоимость / Pricing:</div>
    <table>
        <tr>
            <th style="width: 180px">Тип тарифа<br>Price Type</th>
            <td>{{ $requestData['price_type'] }}</td>
            <th style="width: 180px">Цена за единицу<br>Unit Price</th>
            <td class="highlight">${{ number_format($requestData['unit_price'], 2) }}</td>
        </tr>
        <tr>
            <th>Количество<br>Quantity</th>
            <td>{{ $requestData['quantity'] }}</td>
            <th>ОБЩАЯ СТОИМОСТЬ<br>TOTAL COST</th>
            <td class="highlight" style="font-size: 12pt;">${{ number_format($requestData['unit_price'] * $requestData['quantity'], 2) }}</td>
        </tr>
        <tr>
            <th>Валюта<br>Currency</th>
            <td colspan="3">{{ $requestData['currency'] }}</td>
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

    <!-- Additional Notes -->
    <div class="section-header">9. Дополнительные заметки / Additional Notes:</div>
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
