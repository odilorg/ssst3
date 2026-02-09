{{-- Tour FAQs Partial --}}
@php
    // Use translated FAQ if available, otherwise fall back to tour FAQ
    // Ensure faq_json is always an array (handle string JSON edge case)
    $rawFaqs = $translation->faq_json ?? null;
    $translatedFaqs = is_array($rawFaqs) ? $rawFaqs : (is_string($rawFaqs) ? json_decode($rawFaqs, true) : null);
    $hasCustomFaqs = $tour->faqs && $tour->faqs->isNotEmpty();
    $shouldShowGlobal = (!$translatedFaqs && !$hasCustomFaqs) || $tour->include_global_faqs;

    // Determine which FAQs to show (prioritize translation JSON)
    $faqsToShow = $translatedFaqs ?? ($hasCustomFaqs ? $tour->faqs : null);
@endphp

<h2 class="section-title">{{ __('ui.sections.frequently_asked') }}</h2>

<div class="faq-accordion">
        @if($translatedFaqs && count($translatedFaqs) > 0)
            {{-- Translated FAQs from JSON --}}
            @foreach($translatedFaqs as $faq)
                <details class="faq-item">
                    <summary class="faq-question">
                        <span>{{ $faq['question'] ?? '' }}</span>
                        <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                        </svg>
                    </summary>
                    <div class="faq-answer">
                        <p>{!! nl2br(e($faq['answer'] ?? '')) !!}</p>
                    </div>
                </details>
            @endforeach
        @elseif($hasCustomFaqs)
            {{-- Tour-specific FAQs --}}
            @foreach($tour->faqs as $faq)
                <details class="faq-item">
                    <summary class="faq-question">
                        <span>{{ $faq->question_text }}</span>
                        <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                        </svg>
                    </summary>
                    <div class="faq-answer">
                        <p>{!! nl2br(e($faq->answer_text)) !!}</p>
                    </div>
                </details>
            @endforeach
        @endif

        @if($shouldShowGlobal && isset($globalFaqs) && is_array($globalFaqs) && count($globalFaqs) > 0)
            {{-- Global FAQs --}}
            @foreach($globalFaqs as $faq)
                <details class="faq-item">
                    <summary class="faq-question">
                        <span>{{ $faq['question'] }}</span>
                        <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                        </svg>
                    </summary>
                    <div class="faq-answer">
                        <p>{!! nl2br(e($faq['answer'])) !!}</p>
                    </div>
                </details>
            @endforeach
        @elseif(!$translatedFaqs && !$hasCustomFaqs)
            {{-- Fallback: Global default FAQs translated --}}
            <details class="faq-item">
                <summary class="faq-question">
                    <span>{{ __('ui.faq_default.what_bring.question') }}</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>{{ __('ui.faq_default.what_bring.answer') }}</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span>{{ __('ui.faq_default.not_allowed.question') }}</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>{{ __('ui.faq_default.not_allowed.answer') }}</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span>{{ __('ui.faq_default.suitable_children.question') }}</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>{{ __('ui.faq_default.suitable_children.answer') }}</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span>{{ __('ui.faq_default.if_rains.question') }}</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>{{ __('ui.faq_default.if_rains.answer') }}</p>
                </div>
            </details>
        @endif
</div>
