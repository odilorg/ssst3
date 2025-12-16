<?php

namespace App\Filament\Resources\Cities\Pages;

use App\Filament\Resources\Cities\CityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCity extends CreateRecord
{
    use \LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

    protected static string $resource = CityResource::class;
}
