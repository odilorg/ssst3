{{-- Tour Requirements / Know Before You Go Partial --}}
<h2 class="section-title">Know Before You Go</h2>

@php
    // Icon SVG mapping
    $iconSvgs = [
        'walking' => '<svg class="icon icon--walking" width="16" height="20" viewBox="0 0 16 20" fill="currentColor" aria-hidden="true"><path d="M8 0a2 2 0 110 4 2 2 0 010-4zM5.5 6l.5-1.5L7.5 6l1 3.5L10 12v8h-2v-6l-2-3-1.5 5L3 18.5 2.5 17l1.5-3.5L5.5 6z"/></svg>',
        'tshirt' => '<svg class="icon icon--tshirt" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M14 0L10 3 6 0 0 3v5l3 1v11h14V9l3-1V3l-6-3zM5 7l-2-.67V4.5L6 2.8v1.7a2.5 2.5 0 104 0V2.8L13 4.5v1.83L11 7v11H9V9H7v9H5V7z"/></svg>',
        'money' => '<svg class="icon icon--money" width="22" height="16" viewBox="0 0 22 16" fill="currentColor" aria-hidden="true"><path d="M2 2c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h18c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H2zm9 2c1.66 0 3 1.79 3 4s-1.34 4-3 4-3-1.79-3-4 1.34-4 3-4zM4 5c.83 0 1.5.67 1.5 1.5S4.83 8 4 8s-1.5-.67-1.5-1.5S3.17 5 4 5zm14 0c.83 0 1.5.67 1.5 1.5S18.83 8 18 8s-1.5-.67-1.5-1.5S17.17 5 18 5z"/></svg>',
        'camera' => '<svg class="icon icon--camera" width="20" height="18" viewBox="0 0 20 18" fill="currentColor" aria-hidden="true"><path d="M10 13a3 3 0 100-6 3 3 0 000 6z"/><path d="M2 4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2h-2.586l-.707-.707A2 2 0 0013.293 2H6.707a2 2 0 00-1.414.586L4.586 4H2zm8 11a5 5 0 110-10 5 5 0 010 10z"/></svg>',
        'sun' => '<svg class="icon icon--sun" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm0 12a4 4 0 100-8 4 4 0 000 8zm-8-4a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm13 0a1 1 0 011-1h1a1 1 0 110 2h-1a1 1 0 01-1-1zm-8.95 5.36a1 1 0 010-1.41l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41 0zm7.78-9.9a1 1 0 010-1.42l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41 0zM4.93 15.36l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41-1.41zm9.9-10.61l.7-.7a1 1 0 111.42 1.41l-.71.7a1 1 0 01-1.41-1.41zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"/></svg>',
        'wheelchair' => '<svg class="icon icon--wheelchair" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M8 3a2 2 0 110-4 2 2 0 010 4zm5.5 7H11V6H9v8.5A2.5 2.5 0 0011.5 17h3.5l2 3h2l-2.5-4a2 2 0 00-1.5-.68h-1.5zM8 8a6 6 0 100 12 6 6 0 000-12zm0 10a4 4 0 110-8 4 4 0 010 8z"/></svg>',
        'info' => '<svg class="icon icon--info" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9V9h2v6zm0-8H9V5h2v2z"/></svg>',
        'clock' => '<svg class="icon icon--clock" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0a10 10 0 110 20 10 10 0 010-20zm0 2a8 8 0 100 16 8 8 0 000-16zm1 3v6l4 2-1 1.5-5-3V5h2z"/></svg>',
        'utensils' => '<svg class="icon icon--utensils" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 0v7c0 .55-.45 1-1 1H5v12H3V8H2c-.55 0-1-.45-1-1V0h6zm11 0v8.5a2.5 2.5 0 01-5 0V0h2v8.5a.5.5 0 001 0V0h2zm-1 10v10h-2V10h2z"/></svg>',
        'bag' => '<svg class="icon icon--bag" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0a5 5 0 00-5 5v1H2l-1 1v12l1 1h16l1-1V7l-1-1h-3V5a5 5 0 00-5-5zm0 2a3 3 0 013 3v1H7V5a3 3 0 013-3zM3 8h14v10H3V8z"/></svg>',
    ];

    $hasCustomRequirements = $tour->requirements && count($tour->requirements) > 0;
    $shouldShowGlobal = !$hasCustomRequirements || $tour->include_global_requirements;
@endphp

<div class="know-before-content">
    <ul class="know-before-list">
        @if($hasCustomRequirements)
            {{-- Tour-specific requirements --}}
            @foreach($tour->requirements as $requirement)
                <li>
                    @if(is_array($requirement) && isset($requirement['icon']))
                        {{-- New structured format with icon, title, text --}}
                        {!! $iconSvgs[$requirement['icon']] ?? $iconSvgs['info'] !!}
                        <div>
                            <strong>{{ $requirement['title'] }}:</strong> {{ $requirement['text'] }}
                        </div>
                    @else
                        {{-- Old simple string format (backward compatibility) --}}
                        <svg class="icon icon--info" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9V9h2v6zm0-8H9V5h2v2z"/>
                        </svg>
                        <div>
                            <span>{{ is_string($requirement) ? $requirement : ($requirement['text'] ?? 'Requirement') }}</span>
                        </div>
                    @endif
                </li>
            @endforeach
        @endif

        @if($shouldShowGlobal && isset($globalRequirements) && count($globalRequirements) > 0)
            {{-- Global requirements from database --}}
            @foreach($globalRequirements as $requirement)
                <li>
                    {!! $iconSvgs[$requirement['icon']] ?? $iconSvgs['info'] !!}
                    <div>
                        <strong>{{ $requirement['title'] }}:</strong> {{ $requirement['text'] }}
                    </div>
                </li>
            @endforeach
        @endif

        @if(!$hasCustomRequirements && (!isset($globalRequirements) || count($globalRequirements) === 0))
            {{-- Fallback if no requirements at all --}}
            <li>
                <svg class="icon icon--info" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm1 15H9V9h2v6zm0-8H9V5h2v2z"/>
                </svg>
                <div>
                    <span>Please contact us for specific requirements for this tour.</span>
                </div>
            </li>
        @endif
    </ul>
</div>
