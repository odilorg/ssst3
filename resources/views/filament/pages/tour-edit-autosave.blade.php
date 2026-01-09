@if($autoSaveEnabled)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let autoSaveTimer;
        const interval = {{ $autoSaveInterval }} * 1000;
        
        function startAutoSave() {
            autoSaveTimer = setInterval(function() {
                if (window.Livewire) {
                    Livewire.dispatch('auto-save');
                }
            }, interval);
        }
        
        // Start auto-save
        startAutoSave();
        
        // Also save on Ctrl+S
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                if (window.Livewire) {
                    Livewire.dispatch('auto-save');
                }
            }
        });
    });
</script>
@endif
