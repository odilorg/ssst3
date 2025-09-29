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
            // Major World Languages
            'English',
            'Mandarin Chinese',
            'Spanish',
            'Hindi',
            'Arabic',
            'Portuguese',
            'Bengali',
            'Russian',
            'Japanese',
            'German',
            'French',
            'Korean',
            'Turkish',
            'Vietnamese',
            'Italian',
            'Persian (Farsi)',
            'Dutch',
            'Indonesian',
            'Thai',
            'Polish',
            
            // Regional Languages (Central Asia & Middle East)
            'Uzbek',
            'Kazakh',
            'Kyrgyz',
            'Tajik',
            'Turkmen',
            'Afghan Persian (Dari)',
            'Pashto',
            'Kurdish',
            'Hebrew',
            'Urdu',
            'Punjabi',
            
            // European Languages
            'Swedish',
            'Norwegian',
            'Danish',
            'Finnish',
            'Greek',
            'Czech',
            'Hungarian',
            'Romanian',
            'Bulgarian',
            'Serbian',
            'Croatian',
            'Slovak',
            'Ukrainian',
            'Belarusian',
            'Lithuanian',
            'Latvian',
            'Estonian',
            'Icelandic',
            
            // Asian Languages
            'Cantonese',
            'Tamil',
            'Telugu',
            'Marathi',
            'Gujarati',
            'Kannada',
            'Malayalam',
            'Oriya',
            'Assamese',
            'Nepali',
            'Sinhala',
            'Burmese',
            'Khmer',
            'Lao',
            'Filipino (Tagalog)',
            'Malay',
            'Bahasa Indonesia',
            
            // African Languages
            'Swahili',
            'Amharic',
            'Yoruba',
            'Hausa',
            'Igbo',
            'Zulu',
            'Xhosa',
            'Afrikaans',
            
            // American Languages
            'Quechua',
            'Aymara',
            'Guarani',
            'Nahuatl',
            
            // Sign Languages
            'American Sign Language (ASL)',
            'British Sign Language (BSL)',
            'International Sign Language',
        ];

        foreach ($languages as $language) {
            SpokenLanguage::firstOrCreate(['name' => $language]);
        }
    }
}
