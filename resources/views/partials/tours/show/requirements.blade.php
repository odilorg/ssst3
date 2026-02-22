{{-- Tour Requirements / Know Before You Go Partial --}}
<h2 class="section-title">{{ __('ui.sections.know_before') }}</h2>

@php
    // Legacy icon key → Font Awesome class mapping (backward compatibility)
    $legacyToFa = [
        'walking' => 'fa-person-walking',
        'tshirt' => 'fa-shirt',
        'money' => 'fa-money-bill-wave',
        'camera' => 'fa-camera',
        'sun' => 'fa-sun',
        'wheelchair' => 'fa-wheelchair',
        'info' => 'fa-circle-info',
        'clock' => 'fa-clock',
        'utensils' => 'fa-utensils',
        'bag' => 'fa-suitcase',
        'shoe' => 'fa-shoe-prints',
        'clothing' => 'fa-shirt',
    ];

    // Resolve icon key to FA class (handles both legacy keys and new fa-* values)
    $resolveIcon = function($icon) use ($legacyToFa) {
        if (!$icon) return 'fa-solid fa-circle-info';
        // Already a FA class (new format)
        if (str_starts_with($icon, 'fa-')) return 'fa-solid ' . $icon;
        // Legacy key → map to FA
        return 'fa-solid ' . ($legacyToFa[$icon] ?? 'fa-circle-info');
    };

    // Use translated requirements if available, otherwise fall back to tour requirements
    // Ensure requirements_json is always an array (handle string JSON edge case)
    $rawReqs = $translation->requirements_json ?? null;
    $translatedRequirements = is_array($rawReqs) ? $rawReqs : (is_string($rawReqs) ? json_decode($rawReqs, true) : null);

    $tourReqs = $tour->requirements ?? [];
    $tourReqs = is_array($tourReqs) ? $tourReqs : (is_string($tourReqs) ? json_decode($tourReqs, true) : []);
    $hasCustomRequirements = !empty($tourReqs);
    $shouldShowGlobal = (!$translatedRequirements && !$hasCustomRequirements) || $tour->include_global_requirements;

    // Determine which requirements to show (prioritize translation JSON)
    $requirementsToShow = $translatedRequirements ?? ($hasCustomRequirements ? $tourReqs : null);
@endphp

<div class="know-before-content">
    <ul class="know-before-list">
        @if($translatedRequirements && count($translatedRequirements) > 0)
            {{-- Translated requirements from JSON --}}
            @foreach($translatedRequirements as $requirement)
                <li>
                    @if(is_array($requirement) && isset($requirement['icon']))
                        <i class="{{ $resolveIcon($requirement['icon']) }} icon" aria-hidden="true"></i>
                        <div>
                            @if(isset($requirement['title']))
                                <strong>{{ $requirement['title'] }}:</strong> {{ $requirement['text'] }}
                            @else
                                <span>{{ $requirement['text'] }}</span>
                            @endif
                        </div>
                    @else
                        <i class="fa-solid fa-circle-info icon" aria-hidden="true"></i>
                        <div>
                            <span>{{ is_string($requirement) ? $requirement : ($requirement['text'] ?? 'Requirement') }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        @elseif($hasCustomRequirements)
            {{-- Tour-specific requirements --}}
            @foreach($tourReqs as $requirement)
                <li>
                    @if(is_array($requirement) && isset($requirement['icon']))
                        <i class="{{ $resolveIcon($requirement['icon']) }} icon" aria-hidden="true"></i>
                        <div>
                            <strong>{{ $requirement['title'] }}:</strong> {{ $requirement['text'] }}
                        </div>
                    @else
                        {{-- Old simple string format (backward compatibility) --}}
                        <i class="fa-solid fa-circle-info icon" aria-hidden="true"></i>
                        <div>
                            <span>{{ is_string($requirement) ? $requirement : ($requirement['text'] ?? 'Requirement') }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        @endif

        @if($shouldShowGlobal && isset($globalRequirements) && is_array($globalRequirements) && count($globalRequirements) > 0)
            {{-- Global requirements from database --}}
            @foreach($globalRequirements as $requirement)
                <li>
                    <i class="{{ $resolveIcon($requirement['icon'] ?? null) }} icon" aria-hidden="true"></i>
                    <div>
                        <strong>{{ $requirement['title'] }}:</strong> {{ $requirement['text'] }}
                    </div>
                </li>
            @endforeach
        @elseif(!$hasCustomRequirements && $shouldShowGlobal)
            {{-- Fallback: Translated default requirements --}}
            <li>
                <i class="fa-solid fa-person-walking icon" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('ui.requirements_default.walking.title') }}:</strong> {{ __('ui.requirements_default.walking.text') }}
                </div>
            </li>
            <li>
                <i class="fa-solid fa-shirt icon" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('ui.requirements_default.dress_code.title') }}:</strong> {{ __('ui.requirements_default.dress_code.text') }}
                </div>
            </li>
            <li>
                <i class="fa-solid fa-money-bill-wave icon" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('ui.requirements_default.cash.title') }}:</strong> {{ __('ui.requirements_default.cash.text') }}
                </div>
            </li>
            <li>
                <i class="fa-solid fa-camera icon" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('ui.requirements_default.photography.title') }}:</strong> {{ __('ui.requirements_default.photography.text') }}
                </div>
            </li>
            <li>
                <i class="fa-solid fa-sun icon" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('ui.requirements_default.weather.title') }}:</strong> {{ __('ui.requirements_default.weather.text') }}
                </div>
            </li>
            <li>
                <i class="fa-solid fa-wheelchair icon" aria-hidden="true"></i>
                <div>
                    <strong>{{ __('ui.requirements_default.accessibility.title') }}:</strong> {{ __('ui.requirements_default.accessibility.text') }}
                </div>
            </li>
        @endif
    </ul>
</div>
