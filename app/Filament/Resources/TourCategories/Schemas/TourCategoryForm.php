<?php

namespace App\Filament\Resources\TourCategories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
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
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Auto-generate slug from English name
                                        if (!$get('slug')) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('URL-friendly identifier'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name.ru')
                                    ->label('Name (Russian)'),

                                TextInput::make('name.fr')
                                    ->label('Name (French)'),
                            ]),

                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Textarea::make('description.ru')
                                    ->label('Description (Russian)')
                                    ->rows(3),

                                Textarea::make('description.fr')
                                    ->label('Description (French)')
                                    ->rows(3),
                            ]),
                    ]),

                Section::make('Visual Settings')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('icon')
                                    ->label('Icon')
                                    ->helperText('Font Awesome class (e.g., "fas fa-landmark") or emoji'),

                                FileUpload::make('image_path')
                                    ->label('Card Background Image')
                                    ->image()
                                    ->directory('categories')
                                    ->helperText('Used on homepage category cards'),
                            ]),

                        FileUpload::make('hero_image')
                            ->label('Hero Image')
                            ->image()
                            ->directory('categories/heroes')
                            ->helperText('Used on category landing pages')
                            ->columnSpanFull(),
                    ]),

                Section::make('Display Settings')
                    ->schema([
                        Grid::make(3)
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
                            ]),
                    ]),

                Section::make('SEO Settings')
                    ->collapsed()
                    ->schema([
                        TextInput::make('meta_title.en')
                            ->label('Meta Title (English)')
                            ->maxLength(60),

                        Textarea::make('meta_description.en')
                            ->label('Meta Description (English)')
                            ->rows(2)
                            ->maxLength(160),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('meta_title.ru')
                                    ->label('Meta Title (Russian)')
                                    ->maxLength(60),

                                TextInput::make('meta_title.fr')
                                    ->label('Meta Title (French)')
                                    ->maxLength(60),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Textarea::make('meta_description.ru')
                                    ->label('Meta Description (Russian)')
                                    ->rows(2)
                                    ->maxLength(160),

                                Textarea::make('meta_description.fr')
                                    ->label('Meta Description (French)')
                                    ->rows(2)
                                    ->maxLength(160),
                            ]),
                    ]),
            ]);
    }
}
