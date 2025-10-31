<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalFaqsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What should I bring?',
                'answer' => 'Comfortable walking shoes, sun protection (hat, sunscreen, sunglasses), camera, water bottle, and local currency (Uzbek som) for tips and souvenirs. We also recommend bringing a scarf for women to cover shoulders when entering religious sites.',
            ],
            [
                'question' => 'What is not allowed on this tour?',
                'answer' => 'Smoking inside historical monuments, touching ancient artifacts or walls, flash photography inside certain buildings (external photography is always allowed), and climbing on ancient structures. Please be respectful of these UNESCO World Heritage sites.',
            ],
            [
                'question' => 'Is the tour suitable for children?',
                'answer' => 'Yes, this tour is family-friendly and suitable for children aged 6 and above. The walking pace is moderate, and we can adjust the tour content to keep younger visitors engaged. Children under 12 receive a 50% discount.',
            ],
            [
                'question' => 'What happens if it rains?',
                'answer' => 'The tour operates in most weather conditions. Samarkand has relatively little rain, but if heavy rain is forecasted, we\'ll contact you to reschedule or offer a full refund. Light rain doesn\'t typically affect the tour as many sites have covered areas.',
            ],
        ];

        Setting::set('global_faqs', $faqs, 'json', 'faqs');

        $this->command->info('Global FAQs seeded successfully!');
    }
}
