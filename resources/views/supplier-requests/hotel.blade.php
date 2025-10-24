<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заявка на бронирование отеля</title>
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

        .label {
            font-weight: bold;
            margin-right: 5px;
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
            <strong>To:</strong> Hotel <span class="highlight">{{ $requestData['hotel_name'] }}</span><br>
            <strong>City:</strong> <span class="highlight">{{ $booking->city ?? 'TASHKENT' }}</span><br>
            <strong>Att:</strong> Booking Department
        </div>
    </div>

    <!-- Title -->
    <div class="title">ЗАЯВКА / APPLICATION</div>

    <!-- Introduction -->
    <div class="intro-text">
        <em>Пожалуйста, предоставьте и подтвердите письменно следующие услуги для группы туристов:</em><br>
        <em>Please, give and confirm the following services for tourists' group:</em>
    </div>

    <!-- Main booking table -->
    <table>
        <tr>
            <th>1. Страна<br>Country</th>
            <td class="highlight">{{ $booking->country ?? 'JAPAN' }}</td>
            <th>кол-во<br>qty</th>
            <td class="highlight">{{ $requestData['pax_total'] }}</td>
            <th>им.г.<br>ref.</th>
            <td class="highlight">{{ $requestData['booking_reference'] }}</td>
            <th>откуда<br>from</th>
            <td class="highlight">{{ $booking->source_city ?? 'TY-528' }}</td>
        </tr>
    </table>

    <!-- Arrival/Departure -->
    <table>
        <tr>
            <th style="width: 120px">1-заезд:<br>1-arr.:</th>
            <td>дата заезда / Date of arrival</td>
            <td class="highlight">{{ $requestData['check_in'] }}</td>
            <td>время / time</td>
            <td class="highlight">{{ $booking->arrival_time ?? '16:30' }}</td>
            <td>(сутки)<br>(days)</td>
            <td class="highlight" rowspan="2" style="font-size: 16pt; text-align: center; vertical-align: middle;">
                {{ $requestData['nights'] }}
            </td>
        </tr>
        <tr>
            <th>1-отт.:<br>1-arr.:</th>
            <td>дата выезда / Date of departure</td>
            <td class="highlight">{{ $requestData['check_out'] }}</td>
            <td>время / time</td>
            <td class="highlight">{{ $booking->departure_time ?? '6:10' }}</td>
            <td>(days)</td>
        </tr>
    </table>

    <!-- Accommodation -->
    <div class="section-header">2. Размещение / Accommodation:</div>
    <table>
        <tr>
            <th style="width: 150px">заезд / arrival</th>
            <th>SINGLE<br>{{ $booking->single_rooms ?? '0' }}</th>
            <th>TWIN<br><span class="highlight">{{ $booking->twin_rooms ?? '1' }}</span></th>
            <th>DOUBLE<br>{{ $booking->double_rooms ?? '0' }}</th>
            <th>TSU<br>{{ $booking->triple_rooms ?? '0' }}</th>
            <th>TRIPLE<br>{{ $booking->extra_rooms ?? '0' }}</th>
        </tr>
    </table>

    <!-- Meals -->
    <div class="section-header">3. Питание / Meals:</div>
    <table>
        <tr>
            <th style="width: 150px">1-завтрак</th>
            <td class="highlight">{{ $requestData['nights'] }}</td>
            <td>обед</td>
            <td>{{ $booking->lunches_count ?? '_____' }}</td>
            <td>ужин</td>
            <td>{{ $booking->dinners_count ?? '_____' }}</td>
        </tr>
        <tr>
            <th>завтрак</th>
            <td>_____</td>
            <td>обед</td>
            <td>_____</td>
            <td>ужин</td>
            <td>_____</td>
        </tr>
        <tr>
            <th>завтрак</th>
            <td>_____</td>
            <td>обед</td>
            <td>_____</td>
            <td>ужин</td>
            <td>_____</td>
        </tr>
    </table>

    <!-- Client Name -->
    <div class="section-header">4. Client's name:</div>
    <div style="background: #ffff00; padding: 8px; font-weight: bold; text-align: center; margin-bottom: 15px;">
        {{ strtoupper($requestData['customer_name']) }}
    </div>

    <!-- Notes -->
    <div class="section-header">5. Примечание / Note:</div>
    <div class="note-box">
        <strong class="highlight">{{ $requestData['special_requirements'] }}</strong>
    </div>

    <!-- Additional Services -->
    <div class="section-header">6. Доп. услуги / Add.services:</div>
    <div class="note-box" style="min-height: 40px;">
        {{ $booking->additional_services ?? '' }}
    </div>

    <!-- Changes -->
    <div class="section-header">7. Изменения / Changes:</div>
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
