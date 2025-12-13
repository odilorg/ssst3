<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Jobs\GenerateBlogPostWithAI;
use App\Models\BlogAIGeneration;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBlogPosts extends ListRecords
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_with_ai')
                ->label('✨ Generate with AI')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->form([
                    TextInput::make('topic')
                        ->label('Blog Topic')
                        ->placeholder('e.g., Top 10 Things to Do in Samarkand')
                        ->required()
                        ->helperText('What should this blog post be about?')
                        ->columnSpanFull(),

                    Textarea::make('keywords')
                        ->label('Keywords (Optional)')
                        ->placeholder('e.g., Samarkand, Registan Square, Silk Road')
                        ->helperText('Keywords to include naturally in the content')
                        ->rows(2)
                        ->columnSpanFull(),

                    Select::make('target_audience')
                        ->label('Target Audience')
                        ->options([
                            'budget_travelers' => '💰 Budget Travelers',
                            'luxury_travelers' => '💎 Luxury Travelers',
                            'families' => '👨‍👩‍👧‍👦 Families',
                            'solo_travelers' => '🎒 Solo Travelers',
                            'photographers' => '📸 Photographers',
                            'history_buffs' => '🏛️ History Enthusiasts',
                            'general' => '👥 General Audience',
                        ])
                        ->default('general')
                        ->required(),

                    Select::make('tone')
                        ->label('Writing Tone')
                        ->options([
                            'professional' => 'Professional & Informative',
                            'casual' => 'Casual & Friendly',
                            'adventurous' => 'Adventurous & Exciting',
                            'luxury' => 'Sophisticated & Elegant',
                        ])
                        ->default('casual')
                        ->required(),

                    TextInput::make('word_count')
                        ->label('Approximate Word Count')
                        ->numeric()
                        ->default(1000)
                        ->minValue(300)
                        ->maxValue(3000)
                        ->suffix('words'),

                    Textarea::make('additional_notes')
                        ->label('Additional Notes (Optional)')
                        ->placeholder('Any specific points to cover or style preferences')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $recentGenerations = BlogAIGeneration::where('user_id', auth()->id())
                        ->where('created_at', '>', now()->subHour())
                        ->count();

                    if ($recentGenerations >= 5) {
                        Notification::make()
                            ->warning()
                            ->title('Rate Limit Reached')
                            ->body('You can generate up to 5 blog posts per hour. Please wait before generating another.')
                            ->send();
                        return;
                    }

                    $generation = BlogAIGeneration::create([
                        'user_id' => auth()->id(),
                        'status' => 'pending',
                        'input_parameters' => $data,
                    ]);

                    GenerateBlogPostWithAI::dispatch($generation);

                    Notification::make()
                        ->success()
                        ->title('Blog Generation Started')
                        ->body('Your AI blog post is being generated. You will receive a notification when it\'s ready.')
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
