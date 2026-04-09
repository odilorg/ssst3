<?php

namespace App\Support;

/**
 * Single source of truth for estimate category labels, icons, and sort order.
 * Previously this match() block was duplicated 3× inside the routes/web.php closure.
 */
class EstimateCategoryMapper
{
    private const LABELS = [
        'hotel'     => 'Гостиница',
        'transport' => 'Транспорт',
        'restaurant'=> 'Ресторан',
        'guide'     => 'Гид',
        'monument'  => 'Достопримечательности',
        'other'     => 'Другое',
    ];

    private const ICONS = [
        'hotel'     => '🏨',
        'transport' => '🚗',
        'restaurant'=> '🍽️',
        'guide'     => '👨‍🏫',
        'monument'  => '📍',
        'other'     => '📋',
    ];

    /** Predefined display order for a day's category sections. */
    public const ORDER = ['hotel', 'transport', 'restaurant', 'guide', 'monument', 'other'];

    public static function label(string $category): string
    {
        return self::LABELS[$category] ?? 'Другое';
    }

    public static function icon(string $category): string
    {
        return self::ICONS[$category] ?? '📋';
    }
}
