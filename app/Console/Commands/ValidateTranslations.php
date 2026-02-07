<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ValidateTranslations extends Command
{
    protected $signature = 'translations:validate';
    protected $description = 'Check translation files for duplicate keys';

    public function handle()
    {
        $langPath = lang_path();
        $errors = [];

        foreach (File::directories($langPath) as $localeDir) {
            $locale = basename($localeDir);
            
            foreach (File::files($localeDir) as $file) {
                if ($file->getExtension() !== 'php') continue;
                
                $content = file_get_contents($file->getPathname());
                $filename = $file->getFilename();
                
                // Find all array keys
                preg_match_all("/['\"]([^'\"]+)['\"]\s*=>/", $content, $matches);
                
                $keys = $matches[1];
                $duplicates = array_diff_assoc($keys, array_unique($keys));
                
                if (!empty($duplicates)) {
                    foreach (array_unique($duplicates) as $dup) {
                        $errors[] = "[$locale/$filename] Duplicate key: '$dup'";
                    }
                }
            }
        }

        if (empty($errors)) {
            $this->info('✅ All translation files are valid!');
            return 0;
        }

        $this->error('❌ Found duplicate keys:');
        foreach ($errors as $error) {
            $this->line("  - $error");
        }
        
        return 1;
    }
}
