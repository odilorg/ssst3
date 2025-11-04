<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Toggle;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('City Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if (empty($get('slug'))) {
                                            $set('slug', \Illuminate\Support\Str::slug($state));
                                        }
                                    }),

                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Auto-generated from name, but can be customized'),
                            ]),

                        TextInput::make('tagline')
                            ->label('Tagline')
                            ->maxLength(255)
                            ->helperText('Short catchy phrase (e.g., "The Jewel of the Silk Road")')
                            ->columnSpanFull(),

                        Textarea::make('short_description')
                            ->label('Short Description')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Brief description for cards and previews (max 500 characters)')
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(5)
                            ->helperText('Main description for the city')
                            ->columnSpanFull(),

                        Textarea::make('long_description')
                            ->label('Long Description')
                            ->rows(8)
                            ->helperText('Detailed description for dedicated city pages')
                            ->columnSpanFull(),
                    ]),

                Section::make('Images')
                    ->schema([
                        FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->directory('cities/featured')
                            ->imageEditor()
                            ->helperText('Main image for city cards on homepage'),

                        FileUpload::make('hero_image')
                            ->label('Hero Image')
                            ->image()
                            ->directory('cities/hero')
                            ->imageEditor()
                            ->helperText('Large hero image for city detail pages'),

                        FileUpload::make('images')
                            ->label('Gallery Images')
                            ->image()
                            ->multiple()
                            ->directory('cities/gallery')
                            ->imageEditor()
                            ->helperText('Additional images for city gallery'),
                    ]),

                Section::make('Location')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step('0.000001')
                                    ->minValue(-90)
                                    ->maxValue(90)
                                    ->helperText('e.g., 39.6542'),

                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step('0.000001')
                                    ->minValue(-180)
                                    ->maxValue(180)
                                    ->helperText('e.g., 66.9597'),
                            ]),
                    ]),

                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('SEO title for search engines (leave empty to use city name)')
                            ->columnSpanFull(),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(255)
                            ->helperText('SEO description for search engines')
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
                                    ->required()
                                    ->helperText('Lower numbers appear first'),

                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->default(false)
                                    ->helperText('Show on homepage'),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->required()
                                    ->helperText('Visible on website'),
                            ]),

                        TextInput::make('tour_count_cache')
                            ->label('Cached Tour Count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Automatically updated')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
