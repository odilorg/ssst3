<?php

namespace App\Filament\Resources\Workshops\Schemas;

use App\Models\City;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WorkshopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Tabs::make('Workshop')
                    ->columnSpan(2)
                    ->tabs([
                        Tab::make('Basic Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                                
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                
                                TextInput::make('subtitle')
                                    ->maxLength(255),
                                
                                Textarea::make('excerpt')
                                    ->rows(3)
                                    ->maxLength(500),
                                
                                RichEditor::make('description')
                                    ->columnSpanFull(),
                                
                                TextInput::make('hero_image')
                                    ->url()
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                                
                                Select::make('city_id')
                                    ->label('City')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                
                                TextInput::make('craft_type')
                                    ->maxLength(100),
                                
                                TagsInput::make('craft_highlights')
                                    ->placeholder('Add highlight'),
                            ]),
                        
                        Tab::make('Details')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('duration_hours')
                                        ->numeric()
                                        ->step(0.5),
                                    
                                    TextInput::make('duration_display')
                                        ->maxLength(50)
                                        ->placeholder('e.g., 2-3 hours'),
                                    
                                    TextInput::make('group_size_min')
                                        ->numeric()
                                        ->default(1),
                                    
                                    TextInput::make('group_size_max')
                                        ->numeric()
                                        ->default(10),
                                    
                                    TextInput::make('difficulty_level')
                                        ->maxLength(50),
                                    
                                    TextInput::make('price_from')
                                        ->numeric()
                                        ->prefix('$'),
                                    
                                    TextInput::make('price_currency')
                                        ->default('USD')
                                        ->maxLength(3),
                                ]),
                                
                                TagsInput::make('languages')
                                    ->placeholder('Add language'),
                            ]),
                        
                        Tab::make('Master')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('master_name')
                                    ->maxLength(255),
                                
                                TextInput::make('master_title')
                                    ->maxLength(255),
                                
                                TextInput::make('master_image')
                                    ->url()
                                    ->maxLength(500),
                                
                                Textarea::make('master_bio')
                                    ->rows(5),
                                
                                TextInput::make('master_experience_years')
                                    ->numeric(),
                            ]),
                        
                        Tab::make('Experience')
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Repeater::make('what_you_will_do')
                                    ->schema([
                                        TextInput::make('step')
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('title')
                                            ->required(),
                                        Textarea::make('description')
                                            ->rows(2),
                                        TextInput::make('duration'),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->defaultItems(0),
                                
                                TagsInput::make('included_items')
                                    ->placeholder('Add included item'),
                                
                                TagsInput::make('excluded_items')
                                    ->placeholder('Add excluded item'),
                            ]),
                        
                        Tab::make('Audience')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Repeater::make('who_is_it_for')
                                    ->schema([
                                        TextInput::make('icon')
                                            ->placeholder('FontAwesome icon name'),
                                        TextInput::make('title')
                                            ->required(),
                                        TextInput::make('description'),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->defaultItems(0),
                            ]),
                        
                        Tab::make('Practical Info')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Repeater::make('practical_info')
                                    ->schema([
                                        TextInput::make('icon')
                                            ->placeholder('FontAwesome icon'),
                                        TextInput::make('title')
                                            ->required(),
                                        Textarea::make('content')
                                            ->rows(2),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->defaultItems(0),
                                
                                Section::make('Location')
                                    ->schema([
                                        TextInput::make('location_address')
                                            ->maxLength(255),
                                        
                                        Grid::make(2)->schema([
                                            TextInput::make('location_lat')
                                                ->numeric()
                                                ->step(0.0001),
                                            TextInput::make('location_lng')
                                                ->numeric()
                                                ->step(0.0001),
                                        ]),
                                        
                                        Textarea::make('location_instructions')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        Tab::make('FAQs')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema([
                                Repeater::make('faqs')
                                    ->schema([
                                        TextInput::make('question')
                                            ->required(),
                                        Textarea::make('answer')
                                            ->rows(3)
                                            ->required(),
                                    ])
                                    ->collapsible()
                                    ->defaultItems(0),
                            ]),
                        
                        Tab::make('Gallery')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                TagsInput::make('gallery_images')
                                    ->placeholder('Add image URL'),
                            ]),
                    ]),
                
                // Sidebar
                Section::make('Status')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        
                        Toggle::make('is_featured')
                            ->label('Featured'),
                        
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        
                        TextInput::make('rating')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5),
                        
                        TextInput::make('review_count')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }
}
