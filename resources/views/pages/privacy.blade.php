@extends('layouts.main')

@section('title', 'Privacy Policy - Jahongir Travel')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
@endpush

@section('content')
<div class="legal-page">
    <div class="container">
        <div class="legal-page__header">
            <h1>Privacy Policy</h1>
            <p class="legal-page__updated">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="legal-page__content">
            <section class="legal-section">
                <h2>Introduction</h2>
                <p>Welcome to Jahongir Travel. We respect your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you visit our website or use our services.</p>
            </section>

            <section class="legal-section">
                <h2>Information We Collect</h2>
                <p>We collect several types of information to provide and improve our services:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, phone number, and other contact details you provide when making a booking or inquiry</li>
                    <li><strong>Booking Information:</strong> Travel dates, tour preferences, number of travelers, and special requirements</li>
                    <li><strong>Payment Information:</strong> Processed securely through our payment providers (we do not store credit card details)</li>
                    <li><strong>Usage Data:</strong> Information about how you use our website, including IP address, browser type, and pages visited</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>How We Use Your Information</h2>
                <p>We use your information for the following purposes:</p>
                <ul>
                    <li>Processing and managing your tour bookings</li>
                    <li>Communicating with you about your bookings and inquiries</li>
                    <li>Sending booking confirmations and travel information</li>
                    <li>Improving our services and website functionality</li>
                    <li>Complying with legal obligations</li>
                    <li>Sending marketing communications (with your consent)</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Data Security</h2>
                <p>We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.</p>
            </section>

            <section class="legal-section">
                <h2>Third-Party Services</h2>
                <p>We may share your information with trusted third parties who assist us in operating our website and conducting our business, including:</p>
                <ul>
                    <li>Payment processors</li>
                    <li>Email service providers</li>
                    <li>Tour operators and local guides</li>
                    <li>Analytics providers</li>
                </ul>
                <p>These third parties are obligated to protect your information and use it only for the purposes we specify.</p>
            </section>

            <section class="legal-section">
                <h2>Your Rights</h2>
                <p>You have the right to:</p>
                <ul>
                    <li>Access the personal data we hold about you</li>
                    <li>Request correction of inaccurate data</li>
                    <li>Request deletion of your personal data</li>
                    <li>Object to processing of your personal data</li>
                    <li>Withdraw consent for marketing communications</li>
                </ul>
                <p>To exercise these rights, please contact us at <a href="mailto:info@jahongirtravel.com">info@jahongirtravel.com</a></p>
            </section>

            <section class="legal-section">
                <h2>Cookies</h2>
                <p>We use cookies to enhance your browsing experience. For detailed information about the cookies we use, please see our <a href="{{ url('/cookies') }}">Cookie Policy</a>.</p>
            </section>

            <section class="legal-section">
                <h2>Changes to This Policy</h2>
                <p>We may update this privacy policy from time to time. We will notify you of any significant changes by posting the new policy on this page with an updated revision date.</p>
            </section>

            <section class="legal-section">
                <h2>Contact Us</h2>
                <p>If you have any questions about this privacy policy or our data practices, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:info@jahongirtravel.com">info@jahongirtravel.com</a></li>
                    <li><strong>Phone:</strong> <a href="tel:+998991234567">+998 99 123 4567</a></li>
                    <li><strong>Address:</strong> Samarkand, Uzbekistan</li>
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection
