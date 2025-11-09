@extends('layouts.main')

@section('title', 'Layout Test Page - Jahongir Travel')

@section('meta_description', 'Testing the Blade layout system')

@section('content')
    <div style="min-height: 60vh; padding: 4rem 2rem; background: linear-gradient(to bottom, #f8f9fa, #e9ecef);">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <h1 style="font-size: 3rem; color: #1C54B2; margin-bottom: 1rem;">
                Layout Test Page
            </h1>
            
            <p style="font-size: 1.25rem; color: #495057; margin-bottom: 2rem;">
                If you can see the header above and footer below, the layout system is working!
            </p>
            
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <h2 style="color: #FFB703; margin-bottom: 1rem;">Layout Components Check</h2>
                
                <ul style="list-style: none; padding: 0; text-align: left; max-width: 400px; margin: 0 auto;">
                    <li style="padding: 0.5rem; border-bottom: 1px solid #dee2e6;">
                        Header with navigation
                    </li>
                    <li style="padding: 0.5rem; border-bottom: 1px solid #dee2e6;">
                        This content section
                    </li>
                    <li style="padding: 0.5rem; border-bottom: 1px solid #dee2e6;">
                        Footer with 4 columns
                    </li>
                    <li style="padding: 0.5rem;">
                        WhatsApp float button (bottom right)
                    </li>
                </ul>
            </div>
            
            <div style="margin-top: 2rem; padding: 1rem; background: #fff3cd; border-radius: 4px; border-left: 4px solid #ffc107;">
                <strong>Note:</strong> This is a temporary test page. It will be deleted after Phase 1.
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Test page specific styles */
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
@endpush

@push('scripts')
    <script>
        console.log('Test page loaded - Blade layout system working!');
        console.log('Header exists:', document.querySelector('.nav') !== null);
        console.log('Footer exists:', document.querySelector('.site-footer') !== null);
        console.log('WhatsApp button exists:', document.querySelector('.whatsapp-float') !== null);
    </script>
@endpush
