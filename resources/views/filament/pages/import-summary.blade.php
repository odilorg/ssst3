<div class="space-y-2 rounded-lg bg-blue-50 p-4 text-sm">
    <div class="flex items-center gap-2">
        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <strong class="text-blue-900">Ready to Import</strong>
    </div>

    <ul class="ml-7 space-y-1 text-blue-800">
        <li>📄 <strong>File:</strong> {{ $filename }}</li>
        <li>📊 <strong>Total Rows:</strong> {{ number_format($rowCount) }}</li>
        <li>🗂️ <strong>Mapped Fields:</strong> {{ $mappedFields }}</li>
    </ul>

    <div class="mt-3 border-t border-blue-200 pt-3 text-xs text-blue-700">
        ℹ️ The import process may take a few moments for large files. You'll be notified when it's complete.
    </div>
</div>
