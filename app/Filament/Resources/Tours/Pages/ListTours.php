<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Jobs\GenerateTourWithAI;
use App\Models\TourAIGeneration;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTours extends ListRecords
{
    protected static string $resource = TourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_with_ai')
                ->label('✨ Generate with AI')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->form([
                    TextInput::make('destinations')
                        ->label('Destinations')
                        ->placeholder('e.g., Tashkent, Samarkand, Bukhara')
                        ->required()
                        ->helperText('Cities or countries to visit, separated by commas')
                        ->columnSpanFull(),

                    TextInput::make('duration_days')
                        ->label('Duration')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(30)
                        ->suffix('days')
                        ->default(8),

                    Select::make('tour_style')
                        ->label('Tour Style')
                        ->options([
                            'cultural_heritage' => '🏛️ Cultural Heritage',
                            'adventure_nature' => '🏔️ Adventure & Nature',
                            'luxury_experience' => '💎 Luxury Experience',
                            'budget_friendly' => '💰 Budget Friendly',
                            'family_friendly' => '👨‍👩‍👧‍👦 Family Friendly',
                            'photography' => '📸 Photography Focused',
                        ])
                        ->required()
                        ->default('cultural_heritage'),

                    Textarea::make('special_interests')
                        ->label('Special Interests (Optional)')
                        ->placeholder('e.g., Architecture, Food, History, Local Markets')
                        ->rows(2)
                        ->helperText('Specific themes or activities to include')
                        ->columnSpanFull(),

                    Textarea::make('additional_notes')
                        ->label('Additional Notes (Optional)')
                        ->placeholder('Any specific requirements or preferences')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data) {
                    $recentGenerations = TourAIGeneration::where('user_id', auth()->id())
                        ->where('created_at', '>', now()->subHour())
                        ->count();

                    if ($recentGenerations >= 5) {
                        Notification::make()
                            ->warning()
                            ->title('Rate Limit Reached')
                            ->body('You can generate up to 5 tours per hour. Please wait before generating another.')
                            ->send();
                        return;
                    }

                    $generation = TourAIGeneration::create([
                        'user_id' => auth()->id(),
                        'status' => 'pending',
                        'input_parameters' => $data,
                    ]);

                    GenerateTourWithAI::dispatch($generation);

                    Notification::make()
                        ->success()
                        ->title('Tour Generation Started')
                        ->body('Your AI tour is being generated. You will receive a notification when it\'s ready.')
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
