@extends('layouts.main')

@section('title', 'Cookie Policy - Jahongir Travel')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/legal-pages.css') }}">
@endpush

@section('content')
<div class="legal-page">
    <div class="container">
        <div class="legal-page__header">
            <h1>Cookie Policy</h1>
            <p class="legal-page__updated">Last Updated: {{ date('F d, Y') }}</p>
        </div>

        <div class="legal-page__content">
            <section class="legal-section">
                <h2>What Are Cookies?</h2>
                <p>Cookies are small text files that are placed on your device when you visit a website. They are widely used to make websites work more efficiently and provide information to website owners.</p>
            </section>

            <section class="legal-section">
                <h2>How We Use Cookies</h2>
                <p>Jahongir Travel uses cookies to:</p>
                <ul>
                    <li>Remember your preferences and settings</li>
                    <li>Understand how you use our website</li>
                    <li>Improve your browsing experience</li>
                    <li>Analyze website traffic and performance</li>
                    <li>Ensure security and prevent fraud</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Types of Cookies We Use</h2>

                <h3>Essential Cookies</h3>
                <p>These cookies are necessary for the website to function properly. They enable core functionality such as security, network management, and accessibility.</p>
                <ul>
                    <li><strong>Session cookies:</strong> Temporary cookies that expire when you close your browser</li>
                    <li><strong>Security cookies:</strong> Help protect against fraudulent activity and secure user authentication</li>
                </ul>

                <h3>Analytical/Performance Cookies</h3>
                <p>These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously.</p>
                <ul>
                    <li><strong>Google Analytics:</strong> Tracks website usage, page views, and user behavior</li>
                    <li><strong>Performance monitoring:</strong> Helps identify and fix technical issues</li>
                </ul>

                <h3>Functionality Cookies</h3>
                <p>These cookies allow the website to remember choices you make and provide enhanced, personalized features.</p>
                <ul>
                    <li>Language preferences</li>
                    <li>Region selection</li>
                    <li>User interface customizations</li>
                </ul>

                <h3>Targeting/Advertising Cookies</h3>
                <p>These cookies may be set through our site by our advertising partners to build a profile of your interests.</p>
                <ul>
                    <li>Display relevant advertisements</li>
                    <li>Measure ad campaign effectiveness</li>
                    <li>Limit the number of times you see an ad</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Third-Party Cookies</h2>
                <p>Some cookies on our website are placed by third-party services. We use the following third-party services:</p>
                <ul>
                    <li><strong>Google Analytics:</strong> Web analytics service to understand website usage</li>
                    <li><strong>Facebook Pixel:</strong> Track conversions and optimize advertising campaigns</li>
                    <li><strong>Payment Processors:</strong> Secure payment transaction processing</li>
                </ul>
                <p>These third parties have their own privacy policies and cookie policies, which we encourage you to review.</p>
            </section>

            <section class="legal-section">
                <h2>Managing Cookies</h2>
                <p>You have the right to decide whether to accept or reject cookies. You can exercise your cookie preferences by:</p>

                <h3>Browser Settings</h3>
                <p>Most web browsers allow you to manage cookie preferences through the settings menu. You can set your browser to:</p>
                <ul>
                    <li>Reject all cookies</li>
                    <li>Accept only first-party cookies</li>
                    <li>Delete cookies when you close your browser</li>
                    <li>Notify you when a cookie is set</li>
                </ul>

                <h3>Browser-Specific Instructions</h3>
                <ul>
                    <li><strong>Chrome:</strong> Settings > Privacy and Security > Cookies and other site data</li>
                    <li><strong>Firefox:</strong> Settings > Privacy & Security > Cookies and Site Data</li>
                    <li><strong>Safari:</strong> Preferences > Privacy > Cookies and website data</li>
                    <li><strong>Edge:</strong> Settings > Privacy, search, and services > Cookies and site permissions</li>
                </ul>

                <p><strong>Note:</strong> Disabling cookies may affect your ability to use certain features of our website.</p>
            </section>

            <section class="legal-section">
                <h2>Cookie Duration</h2>
                <p>Cookies may be temporary (session cookies) or persistent:</p>
                <ul>
                    <li><strong>Session cookies:</strong> Deleted automatically when you close your browser</li>
                    <li><strong>Persistent cookies:</strong> Remain on your device for a set period or until manually deleted. Duration ranges from a few days to several years depending on the cookie's purpose.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Updates to This Policy</h2>
                <p>We may update this Cookie Policy from time to time to reflect changes in technology, legislation, or our business operations. Please check this page periodically for updates.</p>
            </section>

            <section class="legal-section">
                <h2>Contact Us</h2>
                <p>If you have questions about our use of cookies, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:info@jahongir-travel.uz">info@jahongir-travel.uz</a></li>
                    <li><strong>Phone:</strong> <a href="tel:+998915550808">+998 91 555 08 08</a></li>
                    <li><strong>Address:</strong> Samarkand, Chirokchi 4</li>
                </ul>
            </section>
        </div>
    </div>
</div>
    <div class="pre-footer-spacer"></div>
@endsection
