<x-filament-widgets::widget>
    <x-filament::section>
        @if($this->record)
            @livewire(\App\Livewire\LeadAICopilot::class, ['lead' => $this->record], key('lead-copilot-'.$this->record->id))
        @else
            <div class="p-4 text-red-500">
                Error: No record found. Record is null.
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
