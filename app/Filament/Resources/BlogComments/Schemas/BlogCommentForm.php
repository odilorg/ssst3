<?php

namespace App\Filament\Resources\BlogComments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BlogCommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Comment Information')
                    ->description('Author details and comment content')
                    ->schema([
                        TextInput::make('author_name')
                            ->label('Author Name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('author_email')
                            ->label('Author Email')
                            ->email()
                            ->required()
                            ->maxLength(150),

                        TextInput::make('author_website')
                            ->label('Author Website')
                            ->url()
                            ->maxLength(200)
                            ->placeholder('https://example.com'),

                        Textarea::make('comment')
                            ->label('Comment Text')
                            ->required()
                            ->rows(6)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Blog Post Context')
                    ->description('Related blog post and parent comment')
                    ->schema([
                        Select::make('blog_post_id')
                            ->label('Blog Post')
                            ->relationship('post', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('parent_id')
                            ->label('Parent Comment (if reply)')
                            ->relationship('parent', 'id')
                            ->searchable()
                            ->preload()
                            ->placeholder('None (top-level comment)')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "#{$record->id} - {$record->author_name}: " . \Illuminate\Support\Str::limit($record->comment, 50)),
                    ])
                    ->columns(2),

                Section::make('Moderation')
                    ->description('Status, spam detection, and approval information')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'spam' => 'Spam',
                                'trash' => 'Trash',
                            ])
                            ->required()
                            ->default('pending'),

                        Slider::make('spam_score')
                            ->label('Spam Score')
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->helperText('0-29: Low risk, 30-69: Medium risk, 70-100: High risk'),

                        TextInput::make('flag_count')
                            ->label('Flag Count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Number of times this comment was flagged by users'),

                        DateTimePicker::make('approved_at')
                            ->label('Approved At')
                            ->disabled()
                            ->placeholder('Not approved yet'),

                        Select::make('approved_by')
                            ->label('Approved By')
                            ->relationship('approvedBy', 'name')
                            ->disabled()
                            ->placeholder('N/A'),
                    ])
                    ->columns(2),

                Section::make('Additional Information')
                    ->description('Technical details and metadata')
                    ->schema([
                        Placeholder::make('id')
                            ->label('Comment ID')
                            ->content(fn ($record) => $record?->id ?? 'Will be generated'),

                        TextInput::make('author_ip')
                            ->label('IP Address')
                            ->disabled(),

                        TextInput::make('author_user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->columnSpanFull(),

                        Placeholder::make('created_at')
                            ->label('Created At')
                            ->content(fn ($record) => $record?->created_at?->format('Y-m-d H:i:s') ?? 'N/A'),

                        Placeholder::make('updated_at')
                            ->label('Updated At')
                            ->content(fn ($record) => $record?->updated_at?->format('Y-m-d H:i:s') ?? 'N/A'),

                        Placeholder::make('gravatar')
                            ->label('Gravatar')
                            ->content(fn ($record) => $record ? new \Illuminate\Support\HtmlString(
                                '<img src="' . $record->gravatar_url . '" alt="Avatar" class="rounded-full" width="64" height="64">'
                            ) : 'N/A'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
