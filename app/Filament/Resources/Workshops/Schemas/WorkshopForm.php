<?php

namespace App\Filament\Resources\Workshops\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WorkshopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('subtitle')
                            ->maxLength(255),
                        Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('craft_type')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->default(false),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
                
                Section::make('Hero & Gallery')
                    ->schema([
                        FileUpload::make('hero_image')
                            ->image()
                            ->disk('local')
                            ->directory('workshops')
                            ->columnSpanFull(),
                        FileUpload::make('gallery')
                            ->image()
                            ->multiple()
                            ->disk('local')
                            ->directory('workshops/gallery')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Description')
                    ->schema([
                        RichEditor::make('description')
                            ->columnSpanFull(),
                        TagsInput::make('craft_highlights')
                            ->separator(','),
                    ]),
                
                Section::make('Master Information')
                    ->schema([
                        TextInput::make('master_name')
                            ->required(),
                        TextInput::make('master_title'),
                        Textarea::make('master_bio')
                            ->rows(3),
                        FileUpload::make('master_image')
                            ->image()
                            ->disk('local')
                            ->directory('workshops/masters'),
                        TextInput::make('master_experience_years')
                            ->numeric(),
                        TextInput::make('generation_craftsman'),
                    ])
                    ->columns(2),
                
                Section::make('Pricing & Duration')
                    ->schema([
                        TextInput::make('duration_display')
                            ->placeholder('3 hours'),
                        TextInput::make('group_size_display')
                            ->placeholder('1-4 people'),
                        TextInput::make('price_from')
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('price_display')
                            ->placeholder('From $45 per person'),
                        TextInput::make('rating')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5),
                        TextInput::make('reviews_count')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
                
                Section::make('Languages & Items')
                    ->schema([
                        TagsInput::make('languages')
                            ->separator(','),
                        TagsInput::make('included_items')
                            ->separator(','),
                        TagsInput::make('excluded_items')
                            ->separator(','),
                    ])
                    ->columns(3),
                
                Section::make('What You Will Do')
                    ->schema([
                        Repeater::make('what_you_will_do')
                            ->schema([
                                TextInput::make('step')
                                    ->required(),
                                TextInput::make('title')
                                    ->required(),
                                Textarea::make('description')
                                    ->rows(2),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
                
                Section::make('Who Is It For')
                    ->schema([
                        Repeater::make('who_is_it_for')
                            ->schema([
                                TextInput::make('icon')
                                    ->placeholder('users'),
                                TextInput::make('title')
                                    ->required(),
                                Textarea::make('description')
                                    ->rows(2),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
                
                Section::make('Practical Info')
                    ->schema([
                        Repeater::make('practical_info')
                            ->schema([
                                TextInput::make('icon')
                                    ->placeholder('clock'),
                                TextInput::make('label')
                                    ->required(),
                                TextInput::make('value')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
                
                Section::make('FAQs')
                    ->schema([
                        Repeater::make('faqs')
                            ->schema([
                                TextInput::make('question')
                                    ->required()
                                    ->columnSpanFull(),
                                Textarea::make('answer')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->collapsible(),
                    ]),
                
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(70),
                        Textarea::make('meta_description')
                            ->maxLength(160)
                            ->rows(2),
                    ])
                    ->collapsed(),
            ]);
    }
}
