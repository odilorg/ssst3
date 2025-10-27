<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lead;
use App\Services\AIEmailService;

echo "==========================================\n";
echo "AI Email Generation Test\n";
echo "==========================================\n\n";

// Get first lead
$lead = Lead::first();

if (!$lead) {
    echo "❌ No leads found in database!\n";
    exit(1);
}

echo "✅ Found Lead: {$lead->company_name}\n";
echo "   Country: {$lead->country}\n";
echo "   Website: {$lead->website}\n";
echo "   Has Uzbekistan Partner: " . ($lead->has_uzbekistan_partner ? 'Yes' : 'No') . "\n\n";

echo "🤖 Generating email with AI...\n\n";

try {
    $aiService = new AIEmailService();

    $result = $aiService->generateEmail(
        $lead,
        'initial',
        'professional',
        true
    );

    if ($result['success']) {
        echo "✅ EMAIL GENERATED SUCCESSFULLY!\n\n";
        echo "==========================================\n";
        echo "SUBJECT: {$result['subject']}\n";
        echo "==========================================\n\n";
        echo "{$result['body']}\n\n";
        echo "==========================================\n";
        echo "Metadata:\n";
        echo "  - Generated at: {$result['metadata']['generated_at']}\n";
        echo "  - Email type: {$result['metadata']['email_type']}\n";
        echo "  - Tone: {$result['metadata']['tone']}\n";
        echo "  - AI Model: {$result['metadata']['ai_model']}\n";
        echo "==========================================\n";
    } else {
        echo "❌ EMAIL GENERATION FAILED!\n";
        echo "Error: {$result['error']}\n";
    }

} catch (Exception $e) {
    echo "❌ EXCEPTION OCCURRED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n✅ Test completed!\n";
