@extends('layouts.main')

@section('title', 'Terms & Conditions - Jahongir Travel')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
@endpush

@section('content')
<div class="legal-page">
    <div class="container">
        <div class="legal-page__header">
            <h1>Terms & Conditions</h1>
            <p class="legal-page__updated">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="legal-page__content">
            <section class="legal-section">
                <h2>Introduction</h2>
                <p>These Terms and Conditions govern your use of Jahongir Travel's website and services. By accessing our website or booking a tour, you agree to be bound by these terms.</p>
            </section>

            <section class="legal-section">
                <h2>Booking and Payment</h2>
                <h3>Reservations</h3>
                <ul>
                    <li>All bookings are subject to availability</li>
                    <li>A booking is confirmed only when we send you a confirmation email</li>
                    <li>Prices are quoted in USD and are subject to change until booking is confirmed</li>
                </ul>

                <h3>Payment Terms</h3>
                <ul>
                    <li>A deposit of 30% is required to confirm your booking</li>
                    <li>Full payment must be received 30 days before tour departure</li>
                    <li>We accept payment via bank transfer, credit card, or other agreed methods</li>
                    <li>All bank charges and transaction fees are the responsibility of the client</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Cancellation Policy</h2>
                <p>Cancellations must be notified in writing. The following cancellation fees apply:</p>
                <ul>
                    <li><strong>More than 60 days before departure:</strong> Loss of deposit (30%)</li>
                    <li><strong>30-60 days before departure:</strong> 50% of total tour price</li>
                    <li><strong>15-29 days before departure:</strong> 75% of total tour price</li>
                    <li><strong>Less than 15 days before departure:</strong> 100% of total tour price (no refund)</li>
                </ul>
                <p>We strongly recommend purchasing travel insurance to protect against unforeseen circumstances.</p>
            </section>

            <section class="legal-section">
                <h2>Changes and Modifications</h2>
                <h3>Changes by Client</h3>
                <p>Changes to confirmed bookings may be possible but are subject to availability and additional charges. Changes requested within 30 days of departure may not be possible.</p>

                <h3>Changes by Jahongir Travel</h3>
                <p>We reserve the right to make changes to tour itineraries due to circumstances beyond our control, including but not limited to weather conditions, political situations, or operational requirements.</p>
            </section>

            <section class="legal-section">
                <h2>Travel Documents</h2>
                <p>You are responsible for ensuring you have:</p>
                <ul>
                    <li>Valid passport (with at least 6 months validity)</li>
                    <li>Appropriate visas for Uzbekistan and any transit countries</li>
                    <li>Required vaccinations and health certificates</li>
                    <li>Adequate travel insurance</li>
                </ul>
                <p>Jahongir Travel is not responsible for denied entry due to inadequate documentation.</p>
            </section>

            <section class="legal-section">
                <h2>Health and Safety</h2>
                <ul>
                    <li>You must inform us of any medical conditions, allergies, or dietary requirements at time of booking</li>
                    <li>You are responsible for obtaining necessary vaccinations</li>
                    <li>You must have adequate travel insurance covering medical expenses and repatriation</li>
                    <li>You participate in activities at your own risk</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Liability</h2>
                <p>While we strive to ensure a safe and enjoyable experience:</p>
                <ul>
                    <li>We are not liable for delays, cancellations, or changes due to circumstances beyond our control</li>
                    <li>We are not responsible for loss or damage to personal belongings</li>
                    <li>Our liability is limited to the total value of the tour booked</li>
                    <li>We strongly recommend comprehensive travel insurance</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Behavior and Conduct</h2>
                <p>We expect all travelers to:</p>
                <ul>
                    <li>Respect local customs, traditions, and laws</li>
                    <li>Follow instructions from tour guides and staff</li>
                    <li>Respect other travelers and local communities</li>
                    <li>Not engage in illegal activities</li>
                </ul>
                <p>We reserve the right to remove any traveler whose behavior is deemed unacceptable or dangerous.</p>
            </section>

            <section class="legal-section">
                <h2>Intellectual Property</h2>
                <p>All content on this website, including text, images, logos, and design, is the property of Jahongir Travel and protected by copyright laws. Unauthorized use is prohibited.</p>
            </section>

            <section class="legal-section">
                <h2>Governing Law</h2>
                <p>These terms are governed by the laws of Uzbekistan. Any disputes will be subject to the exclusive jurisdiction of Uzbek courts.</p>
            </section>

            <section class="legal-section">
                <h2>Contact Us</h2>
                <p>If you have questions about these terms and conditions, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:info@jahongirtravel.com">info@jahongirtravel.com</a></li>
                    <li><strong>Phone:</strong> <a href="tel:+998991234567">+998 99 123 4567</a></li>
                    <li><strong>Address:</strong> Samarkand, Uzbekistan</li>
                </ul>
            </section>
        </div>
    </div>
</div>
    <div class="pre-footer-spacer"></div>
@endsection
