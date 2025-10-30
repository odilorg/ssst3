{{-- Tour List Partial - Phase 1 Test Version --}}
<div class="tours-test" style="padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <div style="background: white; padding: 30px; border-radius: 8px;">
        <h2 style="color: #4CAF50; margin: 0 0 20px 0; font-size: 28px;">
            ✅ Partials Backend Working!
        </h2>

        <div style="background: #f0f9ff; padding: 15px; border-left: 4px solid #3b82f6; margin-bottom: 20px;">
            <p style="margin: 0 0 10px 0;"><strong>🎯 Status:</strong> Laravel serving HTML partials successfully</p>
            <p style="margin: 0 0 10px 0;"><strong>📊 Tours Found:</strong> {{ $tours->count() }}</p>
            <p style="margin: 0;"><strong>🔗 Endpoint:</strong> <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 3px;">/partials/tours</code></p>
        </div>

        @if ($tours->isNotEmpty())
            <h3 style="color: #333; margin: 20px 0 15px 0;">Sample Tours:</h3>
            <div style="display: grid; gap: 15px;">
                @foreach ($tours->take(3) as $tour)
                    <div style="background: #fff; border: 1px solid #e5e7eb; padding: 15px; border-radius: 6px;">
                        <h4 style="margin: 0 0 10px 0; color: #1f2937; font-size: 18px;">
                            {{ $tour->title }}
                        </h4>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 14px; color: #6b7280;">
                            <span>💰 ${{ number_format($tour->price_per_person, 0) }}</span>
                            <span>⏱️ {{ $tour->duration_text }}</span>
                            @if ($tour->city)
                                <span>📍 {{ $tour->city->name }}</span>
                            @endif
                            @if ($tour->rating > 0)
                                <span>⭐ {{ number_format($tour->rating, 1) }} ({{ $tour->review_count }})</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($tours->count() > 3)
                <p style="margin-top: 15px; color: #6b7280; font-size: 14px;">
                    ... and {{ $tours->count() - 3 }} more tours available
                </p>
            @endif
        @else
            <div style="background: #fef2f2; border: 1px solid #fca5a5; padding: 15px; border-radius: 6px;">
                <p style="color: #dc2626; margin: 0;">
                    ⚠️ No tours found. Run seeders: <code>php artisan db:seed --class=TourSeeder</code>
                </p>
            </div>
        @endif

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6b7280;">
                <strong>✅ Next Steps:</strong>
            </p>
            <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #6b7280;">
                <li>Frontend AI can now integrate with HTMX</li>
                <li>Test endpoint: <code style="background: #e5e7eb; padding: 2px 6px; border-radius: 3px;">http://localhost/ssst3/partials/tours</code></li>
                <li>Full design coming in Phase 2</li>
            </ul>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #ecfdf5; border-radius: 6px;">
            <p style="margin: 0; font-size: 12px; color: #059669;">
                <strong>🎉 Phase 1 Backend Complete!</strong> This is a test partial. Styled tour cards coming in Phase 2.
            </p>
        </div>
    </div>
</div>
