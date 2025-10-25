<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form (2/3 width) --}}
        <div class="lg:col-span-2">
            {{ $this->form }}
        </div>

        {{-- AI Copilot Sidebar (1/3 width) --}}
        <div class="lg:col-span-1">
            <div class="sticky top-4 h-[calc(100vh-8rem)] overflow-hidden rounded-lg shadow-sm">
                @livewire('lead-a-i-copilot', ['lead' => $this->record])
            </div>
        </div>
    </div>
</x-filament-panels::page>
