# Guide Language Proficiency Implementation Note

## Current Status

The language proficiency level field has been added to the database (`guide_spoken_language` pivot table with `proficiency_level` column).

## Database Structure

```sql
guide_spoken_language table:
- id
- guide_id
- spoken_language_id
- proficiency_level (enum: A1, A2, B1, B2, C1, C2, Native) - NULLABLE
- timestamps
```

## Form Implementation

Currently, the form uses a simple multi-select for languages. To add proficiency levels, you have two options:

### Option 1: Keep Simple (Current Implementation)
- Guide form has multi-select for languages
- Proficiency level defaults to NULL
- Simple and fast for data entry
- Proficiency can be added later if needed

### Option 2: Use Repeater for Full Control
Replace the current language select with a repeater that allows selecting both language and proficiency:

```php
Repeater::make('languagesWithProficiency')
    ->label('Языки и уровень владения')
    ->schema([
        Select::make('spoken_language_id')
            ->label('Язык')
            ->options(SpokenLanguage::all()->pluck('name', 'id'))
            ->required()
            ->searchable(),
        Select::make('proficiency_level')
            ->label('Уровень владения')
            ->options([
                'A1' => 'A1 - Начальный',
                'A2' => 'A2 - Элементарный',
                'B1' => 'B1 - Средний',
                'B2' => 'B2 - Средне-продвинутый',
                'C1' => 'C1 - Продвинутый',
                'C2' => 'C2 - Профессиональный',
                'Native' => 'Родной язык',
            ])
            ->required(),
    ])
    ->columns(2)
    ->addActionLabel('Добавить язык')
    ->columnSpanFull()
```

### Option 3: Use Filament's BelongsToMany with Pivot Data (Advanced)

Filament supports pivot data through the relationship, but requires custom handling.

## Recommendation

For now, I've kept Option 1 (simple multi-select) since:
1. It's faster for data entry
2. Proficiency level is optional (nullable)
3. You can manually add proficiency data directly in the database if needed
4. Most guides will likely have native or near-native proficiency

If you want full proficiency control in the form, let me know and I can implement Option 2 or 3.

## Accessing Language Proficiency in Code

```php
// Get all languages with proficiency
$guide->spokenLanguages; // Returns collection of languages

// Access pivot data
foreach ($guide->spokenLanguages as $language) {
    echo $language->name; // Language name
    echo $language->pivot->proficiency_level; // A1, B1, etc.
}
```

## Future Enhancement

You could add a custom display in the guide table/view to show languages with their proficiency levels:
```
English (C2), Russian (Native), Uzbek (Native)
```
