<?php

namespace App\Filament\Resources\TourCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TourCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->translatable()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Auto-generate slug from name
                                if (!$get('slug')) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText('URL-friendly identifier'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->translatable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Visual Settings')
                    ->description('Manage icons and images for this category')
                    ->schema([
                        TextInput::make('icon')
                            ->label('Icon')
                            ->helperText('Font Awesome class (e.g., "fas fa-landmark") or emoji')
                            ->placeholder('fas fa-landmark')
                            ->columnSpanFull(),

                        FileUpload::make('image_path')
                            ->label('Card Background Image')
                            ->image()
                            ->directory('categories')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->maxSize(5120) // 5MB
                            ->helperText('Used on homepage category cards. Recommended: 800x600px (4:3 ratio)')
                            ->columnSpanFull(),

                        FileUpload::make('hero_image')
                            ->label('Hero Image (Category Landing Page)')
                            ->image()
                            ->directory('categories/heroes')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '21:9',
                                '16:9',
                                null, // Free aspect ratio
                            ])
                            ->maxSize(8192) // 8MB
                            ->helperText('Used as background on category landing pages. Recommended: 1920x1080px (16:9 ratio)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Display Settings')
                    ->schema([
                        TextInput::make('display_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Show/hide this category'),

                        Toggle::make('show_on_homepage')
                            ->label('Show on Homepage')
                            ->default(false)
                            ->helperText('Display in homepage section (max 6)'),
                    ])
                    ->columns(3),

                Section::make('SEO Settings')
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->translatable()
                            ->helperText('SEO title for search engines (leave empty to use category name)'),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2)
                            ->maxLength(160)
                            ->translatable()
                            ->helperText('SEO description for search engines'),
                    ])
                    ->columns(2),
            ]);
    }
}
