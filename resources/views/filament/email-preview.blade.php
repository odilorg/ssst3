<div class="space-y-4">
    @php
        $rendered = $template->render($sampleData);
    @endphp

    {{-- Subject Preview --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject:</h3>
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium">{{ $rendered['subject'] }}</p>
        </div>
    </div>

    {{-- Body Preview --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Body:</h3>
        <div class="bg-white dark:bg-gray-900 rounded-lg p-6 border border-gray-200 dark:border-gray-700 prose prose-sm dark:prose-invert max-w-none">
            {!! $rendered['body'] !!}
        </div>
    </div>

    {{-- Sample Data Used --}}
    <div>
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sample Data Used:</h3>
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
            <dl class="grid grid-cols-2 gap-2 text-xs">
                @foreach($sampleData as $key => $value)
                    <div>
                        <dt class="font-medium text-blue-900 dark:text-blue-300">{{ $key }}:</dt>
                        <dd class="text-blue-700 dark:text-blue-400">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            These are sample values. Actual emails will use real lead data.
        </p>
    </div>
</div>
