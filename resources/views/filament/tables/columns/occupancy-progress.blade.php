<div class="flex items-center gap-2">
    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
        <div
            class="h-full rounded-full transition-all duration-300 {{ $getState()['percentage'] >= 100 ? 'bg-red-500' : ($getState()['percentage'] >= 80 ? 'bg-yellow-500' : 'bg-green-500') }}"
            style="width: {{ min($getState()['percentage'], 100) }}%"
        ></div>
    </div>
    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
        {{ number_format($getState()['percentage'], 1) }}%
    </span>
</div>
