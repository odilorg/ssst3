<x-filament-panels::page>
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach($locales as $locale)
            @php
                $summary = $report['summary'][$locale] ?? ['total' => 0, 'translated' => 0, 'missing' => 0, 'percentage' => 0];
                $localeInfo = $localeNames[$locale] ?? ['name' => $locale, 'native' => $locale, 'flag' => '🌐'];
            @endphp
            <x-filament::section>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">{{ $localeInfo['flag'] ?? '🌐' }}</span>
                        <div>
                            <h3 class="text-lg font-semibold">{{ $localeInfo['name'] }}</h3>
                            <span class="text-sm text-gray-500">{{ $localeInfo['native'] }}</span>
                        </div>
                    </div>
                    <x-filament::badge :color="$this->getBadgeColor($summary['percentage'])">
                        {{ $summary['percentage'] }}%
                    </x-filament::badge>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span>Total Items</span>
                        <span class="font-medium">{{ $summary['total'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Translated</span>
                        <span class="font-medium text-success-600">{{ $summary['translated'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Missing</span>
                        <span class="font-medium {{ $summary['missing'] > 0 ? 'text-danger-600' : '' }}">{{ $summary['missing'] }}</span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                        <div class="h-2 rounded-full {{ $summary['percentage'] >= 100 ? 'bg-success-500' : ($summary['percentage'] >= 75 ? 'bg-warning-500' : 'bg-danger-500') }}"
                             style="width: {{ $summary['percentage'] }}%"></div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>

    {{-- Detailed Report by Content Type --}}
    <div class="space-y-6">
        {{-- Tours Section --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-map class="w-5 h-5" />
                    <span>Tours</span>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 px-3">Locale</th>
                            <th class="text-center py-2 px-3">Total</th>
                            <th class="text-center py-2 px-3">Translated</th>
                            <th class="text-center py-2 px-3">Missing</th>
                            <th class="text-center py-2 px-3">Coverage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locales as $locale)
                            @php
                                $data = $report['tours'][$locale] ?? ['total' => 0, 'translated' => 0, 'missing' => 0, 'percentage' => 0, 'missing_items' => []];
                                $localeInfo = $localeNames[$locale] ?? ['name' => $locale, 'flag' => '🌐'];
                            @endphp
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-2 px-3">
                                    <span class="mr-1">{{ $localeInfo['flag'] ?? '🌐' }}</span>
                                    {{ $localeInfo['name'] }}
                                </td>
                                <td class="text-center py-2 px-3">{{ $data['total'] }}</td>
                                <td class="text-center py-2 px-3 text-success-600">{{ $data['translated'] }}</td>
                                <td class="text-center py-2 px-3 {{ $data['missing'] > 0 ? 'text-danger-600 font-medium' : '' }}">
                                    {{ $data['missing'] }}
                                </td>
                                <td class="text-center py-2 px-3">
                                    <x-filament::badge :color="$this->getBadgeColor($data['percentage'])" size="sm">
                                        {{ $data['percentage'] }}%
                                    </x-filament::badge>
                                </td>
                            </tr>

                            {{-- Missing items expandable --}}
                            @if(count($data['missing_items']) > 0)
                                <tr>
                                    <td colspan="5" class="py-2 px-3 bg-gray-50 dark:bg-gray-800">
                                        <details class="cursor-pointer">
                                            <summary class="text-sm text-danger-600 hover:text-danger-700">
                                                View {{ count($data['missing_items']) }} missing translations for {{ $localeInfo['name'] }}
                                            </summary>
                                            <div class="mt-2 space-y-1 pl-4">
                                                @foreach($data['missing_items'] as $item)
                                                    <div class="flex items-center justify-between py-1">
                                                        <span class="text-gray-700 dark:text-gray-300">{{ $item['title'] }}</span>
                                                        <a href="{{ $item['edit_url'] }}"
                                                           target="_blank"
                                                           class="text-primary-600 hover:text-primary-700 text-xs">
                                                            Edit →
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Cities Section --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-building-office-2 class="w-5 h-5" />
                    <span>Cities / Destinations</span>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 px-3">Locale</th>
                            <th class="text-center py-2 px-3">Total</th>
                            <th class="text-center py-2 px-3">Translated</th>
                            <th class="text-center py-2 px-3">Missing</th>
                            <th class="text-center py-2 px-3">Coverage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locales as $locale)
                            @php
                                $data = $report['cities'][$locale] ?? ['total' => 0, 'translated' => 0, 'missing' => 0, 'percentage' => 0, 'missing_items' => []];
                                $localeInfo = $localeNames[$locale] ?? ['name' => $locale, 'flag' => '🌐'];
                            @endphp
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-2 px-3">
                                    <span class="mr-1">{{ $localeInfo['flag'] ?? '🌐' }}</span>
                                    {{ $localeInfo['name'] }}
                                </td>
                                <td class="text-center py-2 px-3">{{ $data['total'] }}</td>
                                <td class="text-center py-2 px-3 text-success-600">{{ $data['translated'] }}</td>
                                <td class="text-center py-2 px-3 {{ $data['missing'] > 0 ? 'text-danger-600 font-medium' : '' }}">
                                    {{ $data['missing'] }}
                                </td>
                                <td class="text-center py-2 px-3">
                                    <x-filament::badge :color="$this->getBadgeColor($data['percentage'])" size="sm">
                                        {{ $data['percentage'] }}%
                                    </x-filament::badge>
                                </td>
                            </tr>

                            {{-- Missing items expandable --}}
                            @if(count($data['missing_items']) > 0)
                                <tr>
                                    <td colspan="5" class="py-2 px-3 bg-gray-50 dark:bg-gray-800">
                                        <details class="cursor-pointer">
                                            <summary class="text-sm text-danger-600 hover:text-danger-700">
                                                View {{ count($data['missing_items']) }} missing translations for {{ $localeInfo['name'] }}
                                            </summary>
                                            <div class="mt-2 space-y-1 pl-4">
                                                @foreach($data['missing_items'] as $item)
                                                    <div class="flex items-center justify-between py-1">
                                                        <span class="text-gray-700 dark:text-gray-300">{{ $item['title'] }}</span>
                                                        <a href="{{ $item['edit_url'] }}"
                                                           target="_blank"
                                                           class="text-primary-600 hover:text-primary-700 text-xs">
                                                            Edit →
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Blog Posts Section --}}
        <x-filament::section collapsible>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-document-text class="w-5 h-5" />
                    <span>Blog Posts / Insights</span>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 px-3">Locale</th>
                            <th class="text-center py-2 px-3">Total</th>
                            <th class="text-center py-2 px-3">Translated</th>
                            <th class="text-center py-2 px-3">Missing</th>
                            <th class="text-center py-2 px-3">Coverage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locales as $locale)
                            @php
                                $data = $report['blog_posts'][$locale] ?? ['total' => 0, 'translated' => 0, 'missing' => 0, 'percentage' => 0, 'missing_items' => []];
                                $localeInfo = $localeNames[$locale] ?? ['name' => $locale, 'flag' => '🌐'];
                            @endphp
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="py-2 px-3">
                                    <span class="mr-1">{{ $localeInfo['flag'] ?? '🌐' }}</span>
                                    {{ $localeInfo['name'] }}
                                </td>
                                <td class="text-center py-2 px-3">{{ $data['total'] }}</td>
                                <td class="text-center py-2 px-3 text-success-600">{{ $data['translated'] }}</td>
                                <td class="text-center py-2 px-3 {{ $data['missing'] > 0 ? 'text-danger-600 font-medium' : '' }}">
                                    {{ $data['missing'] }}
                                </td>
                                <td class="text-center py-2 px-3">
                                    <x-filament::badge :color="$this->getBadgeColor($data['percentage'])" size="sm">
                                        {{ $data['percentage'] }}%
                                    </x-filament::badge>
                                </td>
                            </tr>

                            {{-- Missing items expandable --}}
                            @if(count($data['missing_items']) > 0)
                                <tr>
                                    <td colspan="5" class="py-2 px-3 bg-gray-50 dark:bg-gray-800">
                                        <details class="cursor-pointer">
                                            <summary class="text-sm text-danger-600 hover:text-danger-700">
                                                View {{ count($data['missing_items']) }} missing translations for {{ $localeInfo['name'] }}
                                            </summary>
                                            <div class="mt-2 space-y-1 pl-4">
                                                @foreach($data['missing_items'] as $item)
                                                    <div class="flex items-center justify-between py-1">
                                                        <span class="text-gray-700 dark:text-gray-300">{{ $item['title'] }}</span>
                                                        <a href="{{ $item['edit_url'] }}"
                                                           target="_blank"
                                                           class="text-primary-600 hover:text-primary-700 text-xs">
                                                            Edit →
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>

    {{-- Refresh Button --}}
    <div class="mt-6 flex justify-end">
        <x-filament::button wire:click="refreshReport" icon="heroicon-o-arrow-path">
            Refresh Report
        </x-filament::button>
    </div>
</x-filament-panels::page>
