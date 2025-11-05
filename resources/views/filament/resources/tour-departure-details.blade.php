<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Тур</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $departure->tour->title }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Даты</h4>
            <p class="text-sm text-gray-900 dark:text-white">
                {{ $departure->start_date->format('d M Y') }} - {{ $departure->end_date->format('d M Y') }}
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Статус</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ ucfirst($departure->status) }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Тип</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ ucfirst($departure->departure_type) }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Вместимость</h4>
            <p class="text-sm text-gray-900 dark:text-white">
                {{ $departure->booked_pax }} / {{ $departure->max_pax }} забронировано
                @if($departure->min_pax)
                    (мин: {{ $departure->min_pax }})
                @endif
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Заполненность</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ number_format($departure->getOccupancyPercentage(), 1) }}%</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Осталось мест</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $departure->spotsRemaining() }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Цена</h4>
            <p class="text-sm text-gray-900 dark:text-white">${{ number_format($departure->getEffectivePrice(), 2) }} / чел</p>
        </div>
    </div>

    @if($departure->notes)
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Примечания</h4>
            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $departure->notes }}</p>
        </div>
    @endif

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Бронирования ({{ $departure->confirmedBookings()->count() }})
        </h4>
        @if($departure->confirmedBookings()->count() > 0)
            <ul class="space-y-2">
                @foreach($departure->confirmedBookings()->get() as $booking)
                    <li class="text-sm text-gray-900 dark:text-white">
                        {{ $booking->reference }} - {{ $booking->customer_name }} ({{ $booking->pax_total }} чел)
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">Нет подтвержденных бронирований</p>
        @endif
    </div>
</div>
