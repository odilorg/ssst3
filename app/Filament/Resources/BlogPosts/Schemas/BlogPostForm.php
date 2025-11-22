<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->default(null),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) =>
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                    ),
                TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('excerpt')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'strike',
                        'link',
                        'h2',
                        'h3',
                        'bulletList',
                        'orderedList',
                        'blockquote',
                        'codeBlock',
                        'redo',
                        'undo',
                    ])
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('featured_image')
                    ->label('Featured Image')
                    ->image()
                    ->disk('public')
                    ->directory('blog/featured')
                    ->visibility('public')
                    ->imageEditor()
                    ->columnSpanFull(),
                TextInput::make('author_name')
                    ->required()
                    ->default('Jahongir Travel Team'),
                FileUpload::make('author_image')
                    ->label('Author Image')
                    ->image()
                    ->disk('public')
                    ->directory('blog/authors')
                    ->visibility('public')
                    ->avatar()
                    ->imageEditor(),
                TextInput::make('reading_time')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                DateTimePicker::make('published_at'),
                TextInput::make('meta_title')
                    ->default(null),
                TextInput::make('meta_description')
                    ->default(null),
            ]);
    }
}
