<?php

namespace App\View\Components\Icons;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ExtraServiceIcon extends Component
{
    public string $name;
    public string $size;

    /**
     * Icon library mapping icon names to SVG markup
     */
    public static array $icons = [
        'car' => '<svg class="icon icon--car" width="22" height="18" viewBox="0 0 22 18" fill="currentColor" aria-hidden="true"><path d="M18 7l-2-4H6L4 7H0v8h2v3h3v-3h12v3h3v-3h2V7h-4zM7 4h8l1.5 3h-11L7 4zM5 13a2 2 0 110-4 2 2 0 010 4zm12 0a2 2 0 110-4 2 2 0 010 4z"/></svg>',

        'utensils' => '<svg class="icon icon--utensils" width="18" height="20" viewBox="0 0 18 20" fill="currentColor" aria-hidden="true"><path d="M4 0v7a2 2 0 002 2v11h2V9a2 2 0 002-2V0H8v7H6V0H4zm10 0v6c0 1.1-.9 2-2 2v12h2V8c1.1 0 2-.9 2-2V0h-2z"/></svg>',

        'camera' => '<svg class="icon icon--camera" width="20" height="18" viewBox="0 0 20 18" fill="currentColor" aria-hidden="true"><path d="M10 5a4 4 0 100 8 4 4 0 000-8zM2 4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2h-3L13 0H7L5 4H2zm8 11a5 5 0 110-10 5 5 0 010 10z"/></svg>',

        'gift' => '<svg class="icon icon--gift" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M18 6h-3.17A3 3 0 0012 2a3 3 0 00-2.83 4H2a2 2 0 00-2 2v2h20V8a2 2 0 00-2-2zM9 4a1 1 0 112 0 1 1 0 01-2 0zM0 18a2 2 0 002 2h7V10H0v8zm11 2h7a2 2 0 002-2v-8h-9v10z"/></svg>',

        'shopping-bag' => '<svg class="icon icon--shopping-bag" width="20" height="22" viewBox="0 0 20 22" fill="currentColor" aria-hidden="true"><path d="M4 7V5a6 6 0 1112 0v2h2a2 2 0 012 2v10a2 2 0 01-2 2H2a2 2 0 01-2-2V9a2 2 0 012-2h2zm10 0V5a4 4 0 00-8 0v2h8z"/></svg>',

        'star' => '<svg class="icon icon--star" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 0l2.5 7.5h7.5l-6 4.5 2.5 7.5-6-4.5-6 4.5 2.5-7.5-6-4.5h7.5z"/></svg>',

        'map' => '<svg class="icon icon--map" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M0 4l6-2v16l-6 2V4zm8-2l6 2v16l-6-2V2zm8 2l4-2v16l-4 2V4z"/></svg>',

        'ticket' => '<svg class="icon icon--ticket" width="22" height="16" viewBox="0 0 22 16" fill="currentColor" aria-hidden="true"><path d="M20 4h-4V2a2 2 0 00-2-2H8a2 2 0 00-2 2v2H2a2 2 0 00-2 2v2a2 2 0 002 2v4a2 2 0 002 2h16a2 2 0 002-2v-4a2 2 0 002-2V6a2 2 0 00-2-2zM8 2h6v2H8V2z"/></svg>',

        'users' => '<svg class="icon icon--users" width="24" height="16" viewBox="0 0 24 16" fill="currentColor" aria-hidden="true"><path d="M12 8a4 4 0 100-8 4 4 0 000 8zm-5 2a3 3 0 00-3 3v3h16v-3a3 3 0 00-3-3H7zm13-2a3 3 0 100-6 3 3 0 000 6zm2 2h-2a3 3 0 013 3v3h2v-3a5 5 0 00-3-3zM4 8a3 3 0 100-6 3 3 0 000 6zm-2 2H0a5 5 0 00-3 3v3h2v-3a3 3 0 013-3z"/></svg>',

        'plane' => '<svg class="icon icon--plane" width="24" height="20" viewBox="0 0 24 20" fill="currentColor" aria-hidden="true"><path d="M24 8l-8-6v4l-16-4v4l16 6v4l8-6z"/></svg>',

        'hotel' => '<svg class="icon icon--hotel" width="20" height="20" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 0a2 2 0 00-2 2v16a2 2 0 002 2h16a2 2 0 002-2V2a2 2 0 00-2-2H2zm3 14a2 2 0 110-4 2 2 0 010 4zm10-6H8V6h7v2zm0 4H8v-2h7v2z"/></svg>',

        'music' => '<svg class="icon icon--music" width="18" height="20" viewBox="0 0 18 20" fill="currentColor" aria-hidden="true"><path d="M18 0v12a4 4 0 11-2-3.465V2H6v12a4 4 0 11-2-3.465V0h14z"/></svg>',
    ];

    /**
     * Get icon options for Filament select field
     */
    public static function getIconOptions(): array
    {
        return [
            'car' => '🚗 Car / Transport',
            'utensils' => '🍴 Food / Dining',
            'camera' => '📷 Photography',
            'gift' => '🎁 Gift / Souvenir',
            'shopping-bag' => '🛍️ Shopping',
            'star' => '⭐ Premium / VIP',
            'map' => '🗺️ Tour / Guide',
            'ticket' => '🎫 Ticket / Entry',
            'users' => '👥 Group / Private',
            'plane' => '✈️ Airport / Flight',
            'hotel' => '🏨 Hotel / Accommodation',
            'music' => '🎵 Entertainment / Show',
        ];
    }

    /**
     * Create a new component instance.
     */
    public function __construct(string $name, string $size = 'default')
    {
        $this->name = $name;
        $this->size = $size;
    }

    /**
     * Get the SVG markup for the icon
     */
    public function getSvg(): string
    {
        return self::$icons[$this->name] ?? self::$icons['star'];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.icons.extra-service-icon');
    }
}
