<?php

namespace App\Filament\Helpers;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;

class TranslatableField
{
    public static function make(string $fieldName, string $fieldType = 'text', array $config = []): Tabs
    {
        $locales = [
            'en' => 'English',
            'ru' => 'Русский',
            'uz' => 'O\'zbek',
        ];

        $tabs = [];

        foreach ($locales as $locale => $label) {
            $field = match ($fieldType) {
                'textarea' => Textarea::make("$fieldName.$locale"),
                'richtext' => RichEditor::make("$fieldName.$locale"),
                default => TextInput::make("$fieldName.$locale"),
            };

            // Apply label
            if (isset($config['label'])) {
                $field->label($config['label'] . " ($label)");
            }

            // Apply required
            if (isset($config['required']) && $config['required'] === true) {
                if ($locale === 'en') {
                    $field->required();
                }
            }

            // Apply maxLength
            if (isset($config['maxLength'])) {
                $field->maxLength($config['maxLength']);
            }

            // Apply rows (for textarea)
            if (isset($config['rows']) && $fieldType === 'textarea') {
                $field->rows($config['rows']);
            }

            // Apply columnSpanFull
            if (isset($config['columnSpanFull']) && $config['columnSpanFull'] === true) {
                $field->columnSpanFull();
            }

            $tabs[] = Tabs\Tab::make($label)
                ->schema([$field]);
        }

        return Tabs::make('Language')
            ->tabs($tabs);
    }

    public static function text(string $fieldName, array $config = []): Tabs
    {
        return self::make($fieldName, 'text', $config);
    }

    public static function textarea(string $fieldName, array $config = []): Tabs
    {
        return self::make($fieldName, 'textarea', $config);
    }

    public static function richtext(string $fieldName, array $config = []): Tabs
    {
        return self::make($fieldName, 'richtext', $config);
    }
}
