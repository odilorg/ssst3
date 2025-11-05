<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Полное имя</h4>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $traveler->full_name }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Инициалы</h4>
            <p class="text-sm text-gray-900 dark:text-white">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 font-semibold">
                    {{ $traveler->getInitials() }}
                </span>
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Дата рождения</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $traveler->date_of_birth->format('d M Y') }}</p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Возраст</h4>
            <p class="text-sm text-gray-900 dark:text-white">
                {{ $traveler->getAge() }} лет
                @if($traveler->isAdult())
                    <span class="text-green-600 font-medium">(Взрослый)</span>
                @else
                    <span class="text-yellow-600 font-medium">(Ребенок)</span>
                @endif
            </p>
        </div>
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Национальность</h4>
            <p class="text-sm text-gray-900 dark:text-white">{{ $traveler->nationality }}</p>
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Паспортные данные</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Номер паспорта:</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $traveler->passport_number }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Срок действия:</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $traveler->passport_expiry->format('d M Y') }}</span>
            </div>
            <div class="col-span-2">
                <span class="text-sm text-gray-700 dark:text-gray-300">Статус:</span>
                @if($traveler->hasValidPassport())
                    <span class="text-green-600 font-medium">✓ Действителен</span>
                @else
                    <span class="text-red-600 font-medium">✗ Просрочен или истекает скоро</span>
                @endif
            </div>
        </div>
    </div>

    @if($traveler->hasDietaryRequirements() || $traveler->hasSpecialNeeds())
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Особые требования</h4>

            @if($traveler->hasDietaryRequirements())
                <div class="mb-3">
                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Диетические требования:</h5>
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $traveler->dietary_requirements }}</p>
                </div>
            @endif

            @if($traveler->hasSpecialNeeds())
                <div>
                    <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Особые потребности:</h5>
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $traveler->special_needs }}</p>
                </div>
            @endif
        </div>
    @endif

    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Информация о бронировании</h4>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Номер бронирования:</span>
                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $traveler->booking->reference }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-700 dark:text-gray-300">Тур:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ $traveler->booking->tour->title }}</span>
            </div>
        </div>
    </div>
</div>
