<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Initial Outreach - Partnership Proposal',
                'slug' => 'initial-outreach-partnership',
                'type' => 'initial_contact',
                'subject' => 'Partnership Opportunity with {{sender_company}} - {{company_name}}',
                'body' => '<p>Dear {{contact_name}},</p>

<p>I hope this message finds you well. My name is {{sender_name}}, and I\'m reaching out from {{sender_company}}, a premier tourism services provider based in Uzbekistan.</p>

<p>I came across {{company_name}} while researching tour operators in {{country}}, and I was impressed by your focus on delivering exceptional travel experiences. We believe there\'s a great opportunity for us to collaborate and offer your clients unforgettable journeys to Central Asia.</p>

<h3>What We Offer:</h3>
<ul>
    <li><strong>Comprehensive Ground Services:</strong> Hotels, transportation, local guides, and restaurant arrangements</li>
    <li><strong>Competitive Pricing:</strong> Direct partnerships with suppliers ensure the best rates</li>
    <li><strong>24/7 Support:</strong> Dedicated team for your clients throughout their journey</li>
    <li><strong>Customizable Tours:</strong> Tailor-made itineraries to match your clients\' preferences</li>
</ul>

<p>We specialize in cultural, adventure, and educational tours across Uzbekistan, Kazakhstan, Kyrgyzstan, and Tajikistan. Our expertise in the Silk Road destinations can help you expand your portfolio with unique offerings.</p>

<p>Would you be interested in a brief call next week to explore how we can work together? I\'d love to learn more about {{company_name}}\'s needs and share details about our services.</p>

<p>Looking forward to hearing from you!</p>

<p>Best regards,<br>
{{sender_name}}<br>
{{sender_company}}<br>
{{sender_email}}</p>',
                'description' => 'Professional initial contact email for tour operators',
                'is_active' => true,
            ],

            [
                'name' => 'Follow-up #1 - Gentle Reminder',
                'slug' => 'follow-up-1-gentle-reminder',
                'type' => 'follow_up_1',
                'subject' => 'Following up - Partnership with {{company_name}}',
                'body' => '<p>Hi {{contact_name}},</p>

<p>I wanted to follow up on my previous email regarding a potential partnership between {{sender_company}} and {{company_name}}.</p>

<p>I understand you\'re likely busy, but I believe our Central Asia tour packages could be a valuable addition to your offerings. Many tour operators in {{country}} have found success partnering with us to provide their clients with authentic Silk Road experiences.</p>

<p><strong>Quick highlights of what we bring to the table:</strong></p>
<ul>
    <li>15+ years of experience in Uzbekistan tourism</li>
    <li>Over 50 trusted supplier partnerships (hotels, restaurants, guides)</li>
    <li>Flexible commission structures</li>
    <li>Multi-language support for your clients</li>
</ul>

<p>If you\'d like to explore this opportunity, I\'m happy to schedule a quick 15-minute call at your convenience or answer any questions via email.</p>

<p>You can also visit our website at {{website}} to learn more about us.</p>

<p>Best regards,<br>
{{sender_name}}<br>
{{sender_company}}</p>',
                'description' => 'First follow-up email, sent 3-5 days after initial contact',
                'is_active' => true,
            ],

            [
                'name' => 'Follow-up #2 - Value Proposition',
                'slug' => 'follow-up-2-value-proposition',
                'type' => 'follow_up_2',
                'subject' => 'Quick question for {{contact_name}} at {{company_name}}',
                'body' => '<p>Hi {{contact_name}},</p>

<p>I hope you\'re doing well! I wanted to reach out one more time because I genuinely believe {{company_name}} and {{sender_company}} could create something special together.</p>

<p><strong>Here\'s why this partnership makes sense:</strong></p>

<p><strong>For your clients:</strong> Unique, off-the-beaten-path destinations in Central Asia with rich cultural heritage</p>

<p><strong>For your business:</strong> New revenue streams, competitive pricing, and reliable ground services</p>

<p><strong>For you:</strong> A dedicated partner who handles all logistics, so you can focus on sales</p>

<p>We\'ve recently partnered with tour operators similar to {{company_name}} who are now offering Central Asia tours with great success. I\'d love to share some case studies and discuss how we can replicate that success for you.</p>

<p>Are you open to a brief conversation? Even if it\'s just to learn about emerging trends in Central Asia tourism?</p>

<p>Looking forward to connecting!</p>

<p>Warm regards,<br>
{{sender_name}}<br>
{{sender_email}}</p>',
                'description' => 'Second follow-up with stronger value proposition, sent 7-10 days after first follow-up',
                'is_active' => true,
            ],

            [
                'name' => 'Follow-up #3 - Last Attempt',
                'slug' => 'follow-up-3-last-attempt',
                'type' => 'follow_up_3',
                'subject' => 'Last attempt - {{company_name}} & {{sender_company}}',
                'body' => '<p>Hi {{contact_name}},</p>

<p>I wanted to reach out one final time regarding a potential partnership between {{company_name}} and {{sender_company}}.</p>

<p>I completely understand if now isn\'t the right time or if Central Asia tours don\'t align with your current focus. I just didn\'t want to give up without giving you one last opportunity to connect.</p>

<p><strong>If you\'re interested:</strong> I\'m here and ready to discuss how we can work together.</p>

<p><strong>If timing isn\'t right:</strong> No problem at all! Would you mind letting me know if I should reach back out in 6 months, or if there\'s someone else at {{company_name}} who handles partnerships?</p>

<p><strong>If not interested:</strong> Just let me know, and I\'ll stop reaching out. I respect your time and decision.</p>

<p>Either way, I wish you and {{company_name}} continued success, and I hope our paths cross in the future!</p>

<p>Best wishes,<br>
{{sender_name}}<br>
{{sender_company}}<br>
{{sender_email}}</p>',
                'description' => 'Final follow-up email, sent 14+ days after previous attempt',
                'is_active' => true,
            ],

            [
                'name' => 'Partnership Proposal - Detailed',
                'slug' => 'partnership-proposal-detailed',
                'type' => 'proposal',
                'subject' => 'Partnership Proposal for {{company_name}} - {{sender_company}}',
                'body' => '<p>Dear {{contact_name}},</p>

<p>Thank you for expressing interest in partnering with {{sender_company}}! I\'m excited to present our proposal for collaboration with {{company_name}}.</p>

<h3>Partnership Structure:</h3>

<p><strong>Commission Model:</strong></p>
<ul>
    <li>Standard Tours: 15% commission on total package price</li>
    <li>Custom Tours: 20% commission (for groups 10+ pax)</li>
    <li>Long-term Partnership (12+ bookings/year): 25% commission</li>
</ul>

<p><strong>Our Services Include:</strong></p>
<ul>
    <li>Hotel reservations at negotiated rates (3-5 star properties)</li>
    <li>Private & group transportation (modern, air-conditioned vehicles)</li>
    <li>Professional English-speaking guides</li>
    <li>Restaurant bookings & meal arrangements</li>
    <li>Entry tickets to monuments & attractions</li>
    <li>24/7 emergency support</li>
</ul>

<p><strong>Payment Terms:</strong></p>
<ul>
    <li>Net 30 days from service delivery</li>
    <li>Multiple currency options (USD, EUR)</li>
    <li>Flexible payment methods (wire transfer, PayPal, etc.)</li>
</ul>

<h3>Next Steps:</h3>
<ol>
    <li>Review this proposal</li>
    <li>Schedule a call to discuss details and answer questions</li>
    <li>Sign partnership agreement</li>
    <li>Begin promoting Central Asia tours to your clients!</li>
</ol>

<p>I\'ve attached our full service catalog and sample itineraries for your review. Please let me know if you have any questions or would like to modify any terms.</p>

<p>I\'m confident that together, {{company_name}} and {{sender_company}} can create exceptional experiences for your clients!</p>

<p>Looking forward to our partnership,</p>

<p>{{sender_name}}<br>
{{sender_company}}<br>
{{sender_email}}<br>
Direct: [Your Phone Number]</p>',
                'description' => 'Detailed partnership proposal with terms and pricing',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::create($templateData);
        }
    }
}
