@extends('layouts.main')

@section('title', 'Which Craft Journey Is Right for You? | Compare Our Tours')
@section('meta_description', 'Compare our 3 craft immersion journeys: Weekend Taster ($850), 6-Day Intensive ($1,850), or 12-Day Grand Journey ($3,800). Find your perfect match based on time, budget, and craft interests.')
@section('canonical', url('/tours/compare'))

@push('styles')
<style>
    .compare-hero {
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        color: white;
        padding: 100px 0 60px;
        text-align: center;
    }

    .compare-hero__title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        font-family: 'Playfair Display', serif;
    }

    .compare-hero__subtitle {
        font-size: 1.125rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
    }

    .comparison-table {
        margin: 60px 0;
        overflow-x: auto;
    }

    .comparison-table table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border-radius: 12px;
        overflow: hidden;
    }

    .comparison-table th {
        background: #f9fafb;
        padding: 1.5rem 1rem;
        text-align: center;
        font-weight: 600;
        border-bottom: 2px solid #e5e7eb;
    }

    .comparison-table th:first-child {
        text-align: left;
        background: white;
        font-weight: 700;
        color: #1a1a1a;
    }

    .comparison-table td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        text-align: center;
    }

    .comparison-table td:first-child {
        text-align: left;
        font-weight: 600;
        color: #444;
        background: #fafafa;
    }

    .comparison-table tr:last-child td {
        border-bottom: none;
    }

    .tour-name {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1a5490;
        margin-bottom: 0.25rem;
    }

    .tour-duration {
        font-size: 0.875rem;
        color: #666;
    }

    .price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #27ae60;
    }

    .price-note {
        font-size: 0.875rem;
        color: #666;
        margin-top: 0.25rem;
    }

    .checkmark {
        color: #27ae60;
        font-size: 1.25rem;
    }

    .dash {
        color: #ccc;
    }

    .highlight {
        background: #e8f5e9 !important;
    }

    .decision-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin: 60px 0;
    }

    .decision-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-top: 4px solid #27ae60;
    }

    .decision-card__title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1rem;
    }

    .decision-card__list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .decision-card__list li {
        padding: 0.5rem 0;
        padding-left: 1.5rem;
        position: relative;
    }

    .decision-card__list li:before {
        content: "→";
        position: absolute;
        left: 0;
        color: #27ae60;
        font-weight: 700;
    }

    .cta-section {
        background: linear-gradient(135deg, #1a5490 0%, #2c7abf 100%);
        color: white;
        padding: 60px 0;
        text-align: center;
        border-radius: 12px;
        margin: 60px 0;
    }

    .cta-section h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .cta-section p {
        font-size: 1.125rem;
        margin-bottom: 2rem;
        opacity: 0.95;
    }

    .btn {
        display: inline-block;
        padding: 1rem 2rem;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn--white {
        background: white;
        color: #1a5490;
    }

    .btn--white:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .comparison-table {
            font-size: 0.875rem;
        }

        .comparison-table th,
        .comparison-table td {
            padding: 0.75rem 0.5rem;
        }

        .tour-name {
            font-size: 1rem;
        }

        .price {
            font-size: 1.25rem;
        }
    }
</style>
@endpush

@section('content')
    <section class="compare-hero">
        <div class="container">
            <h1 class="compare-hero__title">Which Craft Journey Is Right for You?</h1>
            <p class="compare-hero__subtitle">Compare our 3 craft immersion tours side-by-side to find your perfect match based on time, budget, and craft interests.</p>
        </div>
    </section>

    <div class="container">
        <div class="comparison-table">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>
                            <div class="tour-name">Weekend Taster</div>
                            <div class="tour-duration">3 days / 2 nights</div>
                        </th>
                        <th>
                            <div class="tour-name">Pottery & Suzani</div>
                            <div class="tour-duration">6 days / 5 nights</div>
                        </th>
                        <th>
                            <div class="tour-name">Silk to Canvas</div>
                            <div class="tour-duration">12 days / 11 nights</div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Price</td>
                        <td>
                            <div class="price">$850</div>
                            <div class="price-note">$283/day</div>
                        </td>
                        <td>
                            <div class="price">$1,850</div>
                            <div class="price-note">$308/day</div>
                        </td>
                        <td>
                            <div class="price">$3,800</div>
                            <div class="price-note">$317/day</div>
                        </td>
                    </tr>
                    <tr>
                        <td>Crafts Covered</td>
                        <td>Pottery only</td>
                        <td>Pottery + Suzani</td>
                        <td>All major crafts (10 workshops)</td>
                    </tr>
                    <tr>
                        <td>Workshop Hours</td>
                        <td>7 hours</td>
                        <td>20+ hours</td>
                        <td>32+ hours</td>
                    </tr>
                    <tr>
                        <td>Artisan Homestays</td>
                        <td>1 night</td>
                        <td>4 nights</td>
                        <td>5 nights</td>
                    </tr>
                    <tr>
                        <td>Cities Visited</td>
                        <td>Samarkand, Gijduvan</td>
                        <td>Samarkand, Gijduvan, Bukhara</td>
                        <td>8 cities (Tashkent to Nukus)</td>
                    </tr>
                    <tr>
                        <td>UNESCO Sites</td>
                        <td><span class="checkmark">✓</span> Registan</td>
                        <td><span class="checkmark">✓</span> Registan, Bukhara</td>
                        <td><span class="checkmark">✓</span> All major sites</td>
                    </tr>
                    <tr>
                        <td>Savitsky Museum</td>
                        <td><span class="dash">—</span></td>
                        <td><span class="dash">—</span></td>
                        <td><span class="checkmark">✓</span> 3-hour curator tour</td>
                    </tr>
                    <tr>
                        <td>Best For</td>
                        <td>First-timers, budget travelers</td>
                        <td>One-week vacation, focused learning</td>
                        <td>Art collectors, comprehensive experience</td>
                    </tr>
                    <tr>
                        <td>View Tour</td>
                        <td><a href="/tours/samarkand-pottery-weekend-craft-taster" style="color: #1a5490; text-decoration: underline;">Details →</a></td>
                        <td><a href="/tours/pottery-suzani-intensive-uzbekistan" style="color: #1a5490; text-decoration: underline;">Details →</a></td>
                        <td><a href="/tours/silk-to-canvas-fergana-karakalpakstan" style="color: #1a5490; text-decoration: underline;">Details →</a></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 style="text-align: center; font-size: 2rem; margin: 60px 0 2rem; font-family: 'Playfair Display', serif;">Choose Based On Your Priorities</h2>

        <div class="decision-cards">
            <div class="decision-card">
                <h3 class="decision-card__title">Choose Weekend Taster If:</h3>
                <ul class="decision-card__list">
                    <li>You only have 3-4 days available</li>
                    <li>Budget under $1,000</li>
                    <li>Want to "try before you commit"</li>
                    <li>First time in Uzbekistan</li>
                    <li>Prefer pottery over embroidery</li>
                    <li>Testing if craft tourism suits you</li>
                </ul>
            </div>

            <div class="decision-card">
                <h3 class="decision-card__title">Choose 6-Day Intensive If:</h3>
                <ul class="decision-card__list">
                    <li>You have one week for vacation</li>
                    <li>Budget $1,500-2,500</li>
                    <li>Want both pottery AND embroidery</li>
                    <li>Prefer focused depth over variety</li>
                    <li>Enjoy hands-on learning (20+ hours workshops)</li>
                    <li>Want Bukhara's Old City + Samarkand</li>
                </ul>
            </div>

            <div class="decision-card">
                <h3 class="decision-card__title">Choose 12-Day Grand Journey If:</h3>
                <ul class="decision-card__list">
                    <li>You have 2 weeks available</li>
                    <li>Budget $3,500-4,500</li>
                    <li>Want ALL crafts (silk, pottery, embroidery, etc.)</li>
                    <li>Savitsky Museum is on your bucket list</li>
                    <li>Art collector or textile professional</li>
                    <li>This is your one big Uzbekistan trip</li>
                </ul>
            </div>
        </div>

        <div class="cta-section">
            <div class="container">
                <h2>Still Not Sure?</h2>
                <p>Read our detailed guide or contact us for personalized recommendations</p>
                <a href="/blog/how-to-choose-first-craft-journey-uzbekistan" class="btn btn--white" style="margin-right: 1rem;">Read Full Guide</a>
                <a href="/contact" class="btn btn--white">Get Help Choosing</a>
            </div>
        </div>
    </div>
@endsection
