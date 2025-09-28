<?php

namespace Database\Seeders;

use App\Models\SpokenLanguage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpokenLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            'English',
            'Russian',
            'Uzbek',
            'French',
            'German',
            'Spanish',
            'Chinese',
            'Japanese',
            'Korean',
            'Arabic',
            'Turkish',
            'Persian',
        ];

        foreach ($languages as $language) {
            SpokenLanguage::create(['name' => $language]);
        }
    }
}
