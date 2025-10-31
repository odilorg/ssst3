{{-- Tour Extras/Add-ons Partial --}}
    <h2 class="section-title">Extra Services</h2>

    <p class="section-intro">Enhance your tour experience with these optional services. Select any add-ons when booking.</p>

    <div class="extras-grid">
        @if($extras && $extras->isNotEmpty())
            @foreach($extras as $extra)
                <div class="extra-service-card">
                    <div class="extra-service__icon">
                        @if($extra->icon)
                            {!! $extra->icon !!}
                        @else
                            <svg class="icon icon--star" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 0l2.5 7.5h7.5l-6 4.5 2.5 7.5-6-4.5-6 4.5 2.5-7.5-6-4.5h7.5z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="extra-service__content">
                        <h3 class="extra-service__title">{{ $extra->name }}</h3>
                        <p class="extra-service__description">
                            {{ $extra->description }}
                        </p>
                        <div class="extra-service__price">
                            <span class="price-label">From</span>
                            <span class="price-value" data-service-price="{{ number_format($extra->price, 2) }}">${{ number_format($extra->price, 2) }}</span>
                            <span class="price-unit">{{ $extra->price_unit ?? 'per person' }}</span>
                        </div>
                    </div>
                    <div class="extra-service__action">
                        <input
                            type="checkbox"
                            id="extra-{{ $extra->id }}"
                            name="extra-{{ $extra->id }}"
                            value="{{ $extra->price }}"
                            class="extra-checkbox"
                            aria-label="Add {{ $extra->name }}">
                        <label for="extra-{{ $extra->id }}" class="extra-label">Add to booking</label>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Fallback: Default extras if none in database --}}
            <div class="extra-service-card">
                <div class="extra-service__icon">
                    <svg class="icon icon--car" width="22" height="18" viewBox="0 0 22 18" fill="currentColor" aria-hidden="true">
                        <path d="M18 7l-2-4H6L4 7H0v8h2v3h3v-3h12v3h3v-3h2V7h-4zM7 4h8l1.5 3h-11L7 4zM5 13a2 2 0 110-4 2 2 0 010 4zm12 0a2 2 0 110-4 2 2 0 010 4z"/>
                    </svg>
                </div>
                <div class="extra-service__content">
                    <h3 class="extra-service__title">Private Transport Upgrade</h3>
                    <p class="extra-service__description">
                        Upgrade to a private, air-conditioned vehicle for your group only. Includes flexible pickup times and the option to customize the route.
                    </p>
                    <div class="extra-service__price">
                        <span class="price-label">From</span>
                        <span class="price-value" data-service-price="30.00">$30.00</span>
                        <span class="price-unit">per group</span>
                    </div>
                </div>
                <div class="extra-service__action">
                    <input
                        type="checkbox"
                        id="extra-transport"
                        name="extra-transport"
                        value="30.00"
                        class="extra-checkbox"
                        aria-label="Add private transport upgrade">
                    <label for="extra-transport" class="extra-label">Add to booking</label>
                </div>
            </div>

            <div class="extra-service-card">
                <div class="extra-service__icon">
                    <svg class="icon icon--utensils" width="18" height="20" viewBox="0 0 18 20" fill="currentColor" aria-hidden="true">
                        <path d="M4 0v7a2 2 0 002 2v11h2V9a2 2 0 002-2V0H8v7H6V0H4zm10 0v6c0 1.1-.9 2-2 2v12h2V8c1.1 0 2-.9 2-2V0h-2z"/>
                    </svg>
                </div>
                <div class="extra-service__content">
                    <h3 class="extra-service__title">Traditional Uzbek Lunch</h3>
                    <p class="extra-service__description">
                        Enjoy an authentic 3-course Uzbek meal at a traditional restaurant. Includes plov (pilaf), fresh salads, samsa, and green tea. Vegetarian options available.
                    </p>
                    <div class="extra-service__price">
                        <span class="price-label">From</span>
                        <span class="price-value" data-service-price="15.00">$15.00</span>
                        <span class="price-unit">per person</span>
                    </div>
                </div>
                <div class="extra-service__action">
                    <input
                        type="checkbox"
                        id="extra-lunch"
                        name="extra-lunch"
                        value="15.00"
                        class="extra-checkbox"
                        aria-label="Add traditional Uzbek lunch">
                    <label for="extra-lunch" class="extra-label">Add to booking</label>
                </div>
            </div>

            <div class="extra-service-card">
                <div class="extra-service__icon">
                    <svg class="icon icon--camera" width="20" height="18" viewBox="0 0 20 18" fill="currentColor" aria-hidden="true">
                        <path d="M10 5a4 4 0 100 8 4 4 0 000-8zM2 4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2h-3L13 0H7L5 4H2zm8 11a5 5 0 110-10 5 5 0 010 10z"/>
                    </svg>
                </div>
                <div class="extra-service__content">
                    <h3 class="extra-service__title">Professional Photo Session</h3>
                    <p class="extra-service__description">
                        1-hour professional photography session at iconic locations. Includes 50+ edited high-resolution photos delivered within 48 hours.
                    </p>
                    <div class="extra-service__price">
                        <span class="price-label">From</span>
                        <span class="price-value" data-service-price="80.00">$80.00</span>
                        <span class="price-unit">per session</span>
                    </div>
                </div>
                <div class="extra-service__action">
                    <input
                        type="checkbox"
                        id="extra-photo"
                        name="extra-photo"
                        value="80.00"
                        class="extra-checkbox"
                        aria-label="Add professional photo session">
                    <label for="extra-photo" class="extra-label">Add to booking</label>
                </div>
            </div>

            <div class="extra-service-card">
                <div class="extra-service__icon">
                    <svg class="icon icon--gift" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M18 6h-3.17A3 3 0 0012 2a3 3 0 00-2.83 4H2a2 2 0 00-2 2v2h20V8a2 2 0 00-2-2zM9 4a1 1 0 112 0 1 1 0 01-2 0zM0 18a2 2 0 002 2h7V10H0v8zm11 2h7a2 2 0 002-2v-8h-9v10z"/>
                    </svg>
                </div>
                <div class="extra-service__content">
                    <h3 class="extra-service__title">Souvenir Package</h3>
                    <p class="extra-service__description">
                        Curated collection of authentic Uzbek souvenirs including traditional ceramics, silk scarves, and local spices. Ready to take home or gift.
                    </p>
                    <div class="extra-service__price">
                        <span class="price-label">From</span>
                        <span class="price-value" data-service-price="20.00">$20.00</span>
                        <span class="price-unit">per package</span>
                    </div>
                </div>
                <div class="extra-service__action">
                    <input
                        type="checkbox"
                        id="extra-souvenir"
                        name="extra-souvenir"
                        value="20.00"
                        class="extra-checkbox"
                        aria-label="Add souvenir package">
                    <label for="extra-souvenir" class="extra-label">Add to booking</label>
                </div>
            </div>
        @endif
    </div>
