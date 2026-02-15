<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Details - {{ $booking->reference }} | Jahongir Travel</title>
    <meta name="robots" content="noindex, nofollow">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .tour-badge {
            display: inline-block;
            margin-top: 0.75rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .content {
            padding: 2rem;
        }

        .success-banner {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .success-banner svg {
            flex-shrink: 0;
            color: #16a34a;
        }

        .success-banner p {
            color: #166534;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .intro-text {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.75rem;
            padding-bottom: 0.4rem;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .field-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.3rem;
        }

        label .optional {
            color: #9ca3af;
            font-weight: 400;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
        }

        .footer {
            background: #f9fafb;
            padding: 1.5rem 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.8rem;
        }

        .field-hint {
            font-size: 0.78rem;
            color: #9ca3af;
            margin-top: 0.2rem;
        }

        @media (max-width: 640px) {
            body { padding: 1rem 0.5rem; }
            .content { padding: 1.25rem; }
            .row { grid-template-columns: 1fr; }
            h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            </div>
            <h1>Help Us Personalize Your Trip</h1>
            <p class="subtitle">{{ $booking->reference }}</p>
            <div class="tour-badge">{{ $booking->tour->title }}</div>
        </div>

        <div class="content">
            @if(session('success'))
            <div class="success-banner">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <p>Trip details saved! You can update them anytime before your tour.</p>
            </div>
            @endif

            <p class="intro-text">
                @if($isMini)
                    Just a few quick details so we can arrange your pickup and stay in touch.
                @else
                    Share your travel details so we can arrange airport pickup, hotel transfers, and keep you updated.
                @endif
            </p>

            <form action="{{ route('trip-details.store', ['token' => $token]) }}" method="POST">
                @csrf

                {{-- Accommodation --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Accommodation
                    </div>
                    <div class="row">
                        <div class="field-group">
                            <label>Hotel Name <span class="optional">(if booked)</span></label>
                            <input type="text" name="hotel_name" value="{{ old('hotel_name', $tripDetail->hotel_name) }}" placeholder="e.g. Hotel Registan">
                        </div>
                        <div class="field-group">
                            <label>Hotel Address <span class="optional">(optional)</span></label>
                            <input type="text" name="hotel_address" value="{{ old('hotel_address', $tripDetail->hotel_address) }}" placeholder="Street address or area">
                        </div>
                    </div>
                </div>

                {{-- Communication --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        Communication
                    </div>
                    <div class="field-group">
                        <label>WhatsApp Number</label>
                        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', $tripDetail->whatsapp_number) }}" placeholder="+1 234 567 8900">
                        <p class="field-hint">We'll send your guide details and pickup info via WhatsApp</p>
                    </div>
                </div>

                {{-- Flight Details (long tours only) --}}
                @unless($isMini)
                <div class="form-section">
                    <div class="form-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path></svg>
                        Flight Details
                    </div>
                    <div class="row">
                        <div class="field-group">
                            <label>Arrival Date</label>
                            <input type="date" name="arrival_date" value="{{ old('arrival_date', $tripDetail->arrival_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="field-group">
                            <label>Arrival Flight <span class="optional">(e.g. TK 364)</span></label>
                            <input type="text" name="arrival_flight" value="{{ old('arrival_flight', $tripDetail->arrival_flight) }}" placeholder="Airline + flight number">
                        </div>
                    </div>
                    <div class="row">
                        <div class="field-group">
                            <label>Arrival Time <span class="optional">(optional)</span></label>
                            <input type="time" name="arrival_time" value="{{ old('arrival_time', $tripDetail->arrival_time) }}">
                        </div>
                        <div class="field-group">&nbsp;</div>
                    </div>

                    <div style="margin-top: 0.75rem;"></div>

                    <div class="row">
                        <div class="field-group">
                            <label>Departure Date</label>
                            <input type="date" name="departure_date" value="{{ old('departure_date', $tripDetail->departure_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="field-group">
                            <label>Departure Flight <span class="optional">(e.g. HY 602)</span></label>
                            <input type="text" name="departure_flight" value="{{ old('departure_flight', $tripDetail->departure_flight) }}" placeholder="Airline + flight number">
                        </div>
                    </div>
                    <div class="row">
                        <div class="field-group">
                            <label>Departure Time <span class="optional">(optional)</span></label>
                            <input type="time" name="departure_time" value="{{ old('departure_time', $tripDetail->departure_time) }}">
                        </div>
                        <div class="field-group">&nbsp;</div>
                    </div>
                </div>
                @endunless

                {{-- Preferences --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Preferences
                    </div>
                    <div class="row">
                        <div class="field-group">
                            <label>Preferred Language</label>
                            <select name="language_preference">
                                <option value="">-- Select --</option>
                                <option value="english" {{ old('language_preference', $tripDetail->language_preference) === 'english' ? 'selected' : '' }}>English</option>
                                <option value="french" {{ old('language_preference', $tripDetail->language_preference) === 'french' ? 'selected' : '' }}>French</option>
                                <option value="german" {{ old('language_preference', $tripDetail->language_preference) === 'german' ? 'selected' : '' }}>German</option>
                                <option value="spanish" {{ old('language_preference', $tripDetail->language_preference) === 'spanish' ? 'selected' : '' }}>Spanish</option>
                                <option value="russian" {{ old('language_preference', $tripDetail->language_preference) === 'russian' ? 'selected' : '' }}>Russian</option>
                                <option value="japanese" {{ old('language_preference', $tripDetail->language_preference) === 'japanese' ? 'selected' : '' }}>Japanese</option>
                                <option value="other" {{ old('language_preference', $tripDetail->language_preference) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="field-group">
                            <label>How did you find us? <span class="optional">(optional)</span></label>
                            <select name="referral_source">
                                <option value="">-- Select --</option>
                                <option value="google" {{ old('referral_source', $tripDetail->referral_source) === 'google' ? 'selected' : '' }}>Google Search</option>
                                <option value="tripadvisor" {{ old('referral_source', $tripDetail->referral_source) === 'tripadvisor' ? 'selected' : '' }}>TripAdvisor</option>
                                <option value="instagram" {{ old('referral_source', $tripDetail->referral_source) === 'instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="facebook" {{ old('referral_source', $tripDetail->referral_source) === 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="friend" {{ old('referral_source', $tripDetail->referral_source) === 'friend' ? 'selected' : '' }}>Friend / Word of mouth</option>
                                <option value="blog" {{ old('referral_source', $tripDetail->referral_source) === 'blog' ? 'selected' : '' }}>Blog / Article</option>
                                <option value="other" {{ old('referral_source', $tripDetail->referral_source) === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Additional Notes --}}
                <div class="field-group">
                    <label>Anything else we should know? <span class="optional">(optional)</span></label>
                    <textarea name="additional_notes" placeholder="Dietary needs, mobility concerns, special celebrations...">{{ old('additional_notes', $tripDetail->additional_notes) }}</textarea>
                </div>

                <button type="submit" class="btn-submit">Save Trip Details</button>
            </form>
        </div>

        <div class="footer">
            <p><strong>Jahongir Travel</strong></p>
            <p>Questions? Contact us at support@jahongir-hotels.uz</p>
        </div>
    </div>
</body>
</html>
