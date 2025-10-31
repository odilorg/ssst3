<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $globalRequirements = [
            [
                'icon' => 'walking',
                'title' => 'Moderate walking required',
                'text' => 'This tour involves approximately 3km of walking, including climbing stairs at Shah-i-Zinda (40+ steps). Wear comfortable walking shoes.',
            ],
            [
                'icon' => 'tshirt',
                'title' => 'Dress code',
                'text' => 'Shoulders and knees should be covered when entering religious sites. Women may want to bring a scarf to cover shoulders. Lightweight, breathable clothing recommended.',
            ],
            [
                'icon' => 'money',
                'title' => 'Cash for purchases',
                'text' => 'Bring Uzbek som (UZS) for tips, souvenirs, and snacks. ATMs available near Registan Square. Credit cards are not widely accepted at small vendors.',
            ],
            [
                'icon' => 'camera',
                'title' => 'Photography',
                'text' => 'Photography is allowed at all sites. Flash photography may be restricted inside certain buildings. Always ask permission before photographing people.',
            ],
            [
                'icon' => 'sun',
                'title' => 'Weather considerations',
                'text' => 'Samarkand summers are hot (35-40°C/95-104°F). Bring sun protection, hat, and water. Spring and autumn are most comfortable (15-25°C/59-77°F).',
            ],
            [
                'icon' => 'wheelchair',
                'title' => 'Accessibility',
                'text' => 'This tour is not wheelchair accessible due to uneven historic surfaces and stairs. Contact us if you have specific mobility concerns and we\'ll suggest alternatives.',
            ],
        ];

        Setting::updateOrCreate(
            ['key' => 'global_requirements'],
            [
                'value' => json_encode($globalRequirements),
                'type' => 'json',
                'group' => 'requirements',
                'description' => 'Default requirements shown when tour has no specific requirements',
            ]
        );
    }
}
