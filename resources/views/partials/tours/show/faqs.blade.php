{{-- Tour FAQs Partial --}}
@php
    // Use translated FAQ if available, otherwise fall back to tour FAQ
    $translatedFaqs = $translation->faq_json ?? null;
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

        @if($shouldShowGlobal && isset($globalFaqs) && count($globalFaqs) > 0)
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
            {{-- Fallback: Default FAQs if none in database --}}
            <details class="faq-item">
                <summary class="faq-question">
                    <span>What should I bring?</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>Comfortable walking shoes, sun protection (hat, sunscreen, sunglasses), camera, water bottle, and local currency (Uzbek som) for tips and souvenirs. We also recommend bringing a scarf for women to cover shoulders when entering religious sites.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span>What is not allowed on this tour?</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>Smoking inside historical monuments, touching ancient artifacts or walls, flash photography inside certain buildings (external photography is always allowed), and climbing on ancient structures. Please be respectful of these UNESCO World Heritage sites.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span>Is the tour suitable for children?</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>Yes, this tour is family-friendly and suitable for children aged 6 and above. The walking pace is moderate, and we can adjust the tour content to keep younger visitors engaged. Children under 12 receive a 50% discount.</p>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span>What happens if it rains?</span>
                    <svg class="icon icon--chevron-down" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path d="M3.646 5.646a.5.5 0 01.708 0L8 9.293l3.646-3.647a.5.5 0 01.708.708l-4 4a.5.5 0 01-.708 0l-4-4a.5.5 0 010-.708z"/>
                    </svg>
                </summary>
                <div class="faq-answer">
                    <p>The tour operates in most weather conditions. Samarkand has relatively little rain, but if heavy rain is forecasted, we'll contact you to reschedule or offer a full refund. Light rain doesn't typically affect the tour as many sites have covered areas.</p>
                </div>
            </details>
        @endif
</div>
