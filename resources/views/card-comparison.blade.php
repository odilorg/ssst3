<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Card Design Comparison | Jahongir Travel</title>
    
    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Load all 3 card option CSS files --}}
    <link rel="stylesheet" href="{{ asset('css/tour-card-option1.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tour-card-option2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tour-card-option3.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fa;
            padding: 40px 20px;
            line-height: 1.6;
        }

        .comparison-header {
            max-width: 1400px;
            margin: 0 auto 40px;
            text-align: center;
        }

        .comparison-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .comparison-header p {
            font-size: 18px;
            color: #666;
            margin-bottom: 8px;
        }

        .comparison-header .note {
            display: inline-block;
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            margin-top: 16px;
        }

        .section {
            max-width: 1400px;
            margin: 0 auto 60px;
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #1a5490;
        }

        .section-header h2 {
            font-size: 28px;
            font-weight: 600;
            color: #1a5490;
            margin-bottom: 8px;
        }

        .section-header p {
            font-size: 15px;
            color: #666;
        }

        .option-specs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .spec {
            font-size: 14px;
        }

        .spec strong {
            color: #1a5490;
            display: block;
            margin-bottom: 4px;
        }

        .cards-grid-1 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .cards-grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .cards-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .pros-cons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 20px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .pros, .cons {
            font-size: 14px;
        }

        .pros h4 {
            color: #28a745;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cons h4 {
            color: #dc3545;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pros ul, .cons ul {
            list-style: none;
            padding-left: 0;
        }

        .pros li::before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
        }

        .cons li::before {
            content: "✗ ";
            color: #dc3545;
            font-weight: bold;
        }

        .comparison-footer {
            max-width: 1400px;
            margin: 40px auto;
            text-align: center;
            padding: 24px;
            background: #e3f2fd;
            border-radius: 12px;
        }

        .comparison-footer h3 {
            font-size: 24px;
            color: #1a5490;
            margin-bottom: 12px;
        }

        .comparison-footer p {
            font-size: 16px;
            color: #666;
        }

        @media (max-width: 768px) {
            .comparison-header h1 {
                font-size: 28px;
            }

            .option-specs {
                grid-template-columns: 1fr;
            }

            .pros-cons {
                grid-template-columns: 1fr;
            }

            .cards-grid-2, .cards-grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="comparison-header">
        <h1>🎨 Tour Card Design Comparison</h1>
        <p>Comparing 3 different card layout options for better UI/UX</p>
        <div class="note">
            <strong>📌 Goal:</strong> Fix the tall aspect ratio (1:4) and improve scannability
        </div>
    </div>

    {{-- OPTION 1: Horizontal Card --}}
    <div class="section">
        <div class="section-header">
            <h2>Option 1: Horizontal Card Layout</h2>
            <p>Desktop-optimized layout with image on left, content on right</p>
        </div>

        <div class="option-specs">
            <div class="spec">
                <strong>Aspect Ratio:</strong>
                ~1:0.4 landscape (650×280px)
            </div>
            <div class="spec">
                <strong>Best For:</strong>
                Desktop/tablet viewing
            </div>
            <div class="spec">
                <strong>Layout:</strong>
                Image 40% | Content 60%
            </div>
        </div>

        <div class="cards-grid-1">
            @foreach (->take(3) as $tour)
                @include('partials.tours.card-option1-horizontal', ['tour' => $tour])
            @endforeach
        </div>

        <div class="pros-cons">
            <div class="pros">
                <h4><i class="fas fa-check-circle"></i> Pros</h4>
                <ul>
                    <li>Better scannability - all info visible</li>
                    <li>Price immediately visible</li>
                    <li>More content without scrolling</li>
                    <li>Better horizontal space usage</li>
                </ul>
            </div>
            <div class="cons">
                <h4><i class="fas fa-times-circle"></i> Cons</h4>
                <ul>
                    <li>Takes more vertical space per card</li>
                    <li>Shows fewer cards per screen</li>
                    <li>Less familiar pattern</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- OPTION 2: Compact Vertical Card --}}
    <div class="section">
        <div class="section-header">
            <h2>Option 2: Compact Vertical Card</h2>
            <p>Familiar vertical layout with reduced height and better proportions</p>
        </div>

        <div class="option-specs">
            <div class="spec">
                <strong>Aspect Ratio:</strong>
                ~1:1.5 portrait (300×450px)
            </div>
            <div class="spec">
                <strong>Best For:</strong>
                Mobile, grid layouts
            </div>
            <div class="spec">
                <strong>Height Reduction:</strong>
                50% shorter than current
            </div>
        </div>

        <div class="cards-grid-2">
            @foreach (->take(3) as $tour)
                @include('partials.tours.card-option2-compact', ['tour' => $tour])
            @endforeach
        </div>

        <div class="pros-cons">
            <div class="pros">
                <h4><i class="fas fa-check-circle"></i> Pros</h4>
                <ul>
                    <li>Familiar vertical pattern</li>
                    <li>Better aspect ratio (1:1.5 vs 1:4)</li>
                    <li>Floating badges save space</li>
                    <li>Works well in grids</li>
                </ul>
            </div>
            <div class="cons">
                <h4><i class="fas fa-times-circle"></i> Cons</h4>
                <ul>
                    <li>Still requires some scrolling</li>
                    <li>Text truncation with line-clamp</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- OPTION 3: Overlay Card --}}
    <div class="section">
        <div class="section-header">
            <h2>Option 3: Grid Card with Info Overlay</h2>
            <p>Modern design with content overlaid on image</p>
        </div>

        <div class="option-specs">
            <div class="spec">
                <strong>Aspect Ratio:</strong>
                ~1:1.3 portrait (300×400px)
            </div>
            <div class="spec">
                <strong>Best For:</strong>
                Image-heavy tours, modern design
            </div>
            <div class="spec">
                <strong>Space Efficiency:</strong>
                Most compact option
            </div>
        </div>

        <div class="cards-grid-3">
            @foreach (->take(3) as $tour)
                @include('partials.tours.card-option3-overlay', ['tour' => $tour])
            @endforeach
        </div>

        <div class="pros-cons">
            <div class="pros">
                <h4><i class="fas fa-check-circle"></i> Pros</h4>
                <ul>
                    <li>Most space-efficient</li>
                    <li>Visually striking, modern</li>
                    <li>Image is hero element</li>
                    <li>No wasted space</li>
                </ul>
            </div>
            <div class="cons">
                <h4><i class="fas fa-times-circle"></i> Cons</h4>
                <ul>
                    <li>Text readability depends on image</li>
                    <li>Requires good gradient overlay</li>
                    <li>Less content visible</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Footer Recommendation --}}
    <div class="comparison-footer">
        <h3>💡 Recommendation</h3>
        <p><strong>Hybrid Approach:</strong> Use Option 1 (Horizontal) for desktop ≥1024px, Option 2 (Compact Vertical) for mobile/tablet.</p>
        <p>This gives the best of both worlds: scannability on desktop, familiarity on mobile.</p>
    </div>

</body>
</html>
