<?php

namespace App\Filament\Resources\Leads\Widgets;

use App\Models\Lead;
use Filament\Widgets\Widget;

class LeadAICopilotWidget extends Widget
{
    protected string $view = 'filament.resources.leads.widgets.lead-a-i-copilot-widget';

    public ?Lead $record = null;

    protected int | string | array $columnSpan = 'full';

    public function mount(): void
    {
        // Filament automatically sets $this->record on Edit pages
    }

    public function getColumnSpan(): string | array | int
    {
        return $this->columnSpan;
    }
}
