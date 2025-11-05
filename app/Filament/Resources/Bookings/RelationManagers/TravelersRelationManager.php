<?php

namespace App\Filament\Resources\Bookings\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

class TravelersRelationManager extends RelationManager
{
    protected static string $relationship = 'travelers';

    protected static ?string $title = 'Путешественники';

    protected static ?string $modelLabel = 'Путешественник';

    protected static ?string $pluralModelLabel = 'Путешественники';

    protected static ?string $recordTitleAttribute = 'full_name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Личная информация')
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Полное имя')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        DatePicker::make('date_of_birth')
                            ->label('Дата рождения')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->maxDate(now())
                            ->helperText('Для расчета возраста'),

                        TextInput::make('nationality')
                            ->label('Национальность')
                            ->required()
                            ->maxLength(100)
                            ->helperText('Страна гражданства'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Паспортные данные')
                    ->schema([
                        TextInput::make('passport_number')
                            ->label('Номер паспорта')
                            ->required()
                            ->maxLength(50),

                        DatePicker::make('passport_expiry')
                            ->label('Срок действия паспорта')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(now())
                            ->helperText('Дата истечения срока действия')
                            ->rules([
                                fn (Forms\Get $get) => function ($attribute, $value, $fail) use ($get) {
                                    $dob = $get('date_of_birth');
                                    if ($dob && $value <= $dob) {
                                        $fail('Срок действия паспорта должен быть после даты рождения.');
                                    }
                                },
                            ]),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Особые требования')
                    ->schema([
                        Textarea::make('dietary_requirements')
                            ->label('Диетические требования')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Напр.: вегетарианец, без глютена, халяль')
                            ->helperText('Любые диетические ограничения или предпочтения')
                            ->columnSpanFull(),

                        Textarea::make('special_needs')
                            ->label('Особые потребности')
                            ->maxLength(500)
                            ->rows(2)
                            ->placeholder('Напр.: инвалидная коляска, медицинские условия')
                            ->helperText('Любые особые потребности или медицинские условия')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Полное имя')
                    ->searchable()
                    ->weight(FontWeight::Medium)
                    ->sortable(),

                Tables\Columns\TextColumn::make('initials')
                    ->label('Инициалы')
                    ->getStateUsing(fn ($record) => $record->getInitials())
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('age')
                    ->label('Возраст')
                    ->getStateUsing(fn ($record) => $record->getAge() ? $record->getAge() . ' лет' : 'Н/Д')
                    ->badge()
                    ->color(fn ($record) => $record->isAdult() ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('nationality')
                    ->label('Национальность')
                    ->searchable(),

                Tables\Columns\TextColumn::make('passport_number')
                    ->label('Паспорт')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('passport_valid')
                    ->label('Паспорт действителен')
                    ->getStateUsing(fn ($record) => $record->hasValidPassport())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('passport_expiry')
                    ->label('Срок действия')
                    ->date('d M Y')
                    ->color(fn ($record) => $record->hasValidPassport() ? 'success' : 'danger')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_dietary')
                    ->label('Диета')
                    ->getStateUsing(fn ($record) => $record->hasDietaryRequirements())
                    ->boolean()
                    ->toggleable()
                    ->tooltip(fn ($record) => $record->dietary_requirements ?? 'Нет требований'),

                Tables\Columns\IconColumn::make('has_special_needs')
                    ->label('Особые')
                    ->getStateUsing(fn ($record) => $record->hasSpecialNeeds())
                    ->boolean()
                    ->toggleable()
                    ->tooltip(fn ($record) => $record->special_needs ?? 'Нет потребностей'),
            ])
            ->filters([
                Tables\Filters\Filter::make('adults')
                    ->label('Только взрослые')
                    ->query(fn ($query) => $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')),

                Tables\Filters\Filter::make('children')
                    ->label('Только дети')
                    ->query(fn ($query) => $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18')),

                Tables\Filters\Filter::make('expired_passport')
                    ->label('Паспорт просрочен')
                    ->query(fn ($query) => $query->where('passport_expiry', '<', now())),

                Tables\Filters\Filter::make('dietary_requirements')
                    ->label('С диетическими требованиями')
                    ->query(fn ($query) => $query->whereNotNull('dietary_requirements')),

                Tables\Filters\Filter::make('special_needs')
                    ->label('С особыми потребностями')
                    ->query(fn ($query) => $query->whereNotNull('special_needs')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить путешественника'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Детали путешественника')
                    ->modalContent(fn ($record) => view('filament.resources.traveler-details', [
                        'traveler' => $record,
                    ]))
                    ->modalWidth('2xl'),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Нет путешественников')
            ->emptyStateDescription('Добавьте информацию о пассажирах для этого бронирования')
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Добавить первого путешественника'),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
