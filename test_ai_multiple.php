<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lead;
use App\Services\AIEmailService;

echo "==========================================\n";
echo "AI Email Generation - Multiple Scenarios\n";
echo "==========================================\n\n";

$aiService = new AIEmailService();

// Test 1: Lead WITH Uzbekistan partner - Professional tone
echo "📧 TEST 1: Lead WITH Uzbekistan Partner (Professional)\n";
echo "==========================================\n";
$leadWithPartner = Lead::where('has_uzbekistan_partner', true)->first();
if ($leadWithPartner) {
    echo "Lead: {$leadWithPartner->company_name} ({$leadWithPartner->country})\n\n";

    $result = $aiService->generateEmail($leadWithPartner, 'initial', 'professional');
    if ($result['success']) {
        echo "✅ SUBJECT: {$result['subject']}\n";
        echo "Body preview: " . substr(strip_tags($result['body']), 0, 150) . "...\n\n";
    }
}

// Test 2: Lead WITHOUT Uzbekistan partner - Friendly tone
echo "📧 TEST 2: Lead WITHOUT Uzbekistan Partner (Friendly)\n";
echo "==========================================\n";
$leadWithoutPartner = Lead::where('has_uzbekistan_partner', false)->first();
if ($leadWithoutPartner) {
    echo "Lead: {$leadWithoutPartner->company_name} ({$leadWithoutPartner->country})\n\n";

    $result = $aiService->generateEmail($leadWithoutPartner, 'initial', 'friendly');
    if ($result['success']) {
        echo "✅ SUBJECT: {$result['subject']}\n";
        echo "Body preview: " . substr(strip_tags($result['body']), 0, 150) . "...\n\n";
    }
}

// Test 3: Same lead - Persuasive tone
echo "📧 TEST 3: Follow-up Email (Persuasive)\n";
echo "==========================================\n";
if ($leadWithoutPartner) {
    echo "Lead: {$leadWithoutPartner->company_name}\n\n";

    $result = $aiService->generateEmail($leadWithoutPartner, 'followup', 'persuasive');
    if ($result['success']) {
        echo "✅ SUBJECT: {$result['subject']}\n";
        echo "Body preview: " . substr(strip_tags($result['body']), 0, 150) . "...\n\n";
    }
}

// Test 4: Generate subject line variations
echo "📧 TEST 4: Subject Line Variations\n";
echo "==========================================\n";
$anyLead = Lead::first();
if ($anyLead) {
    echo "Lead: {$anyLead->company_name}\n\n";

    $subjects = $aiService->generateSubjectLines($anyLead, 3);
    echo "✅ Generated " . count($subjects) . " subject variations:\n";
    foreach ($subjects as $i => $subject) {
        echo "   " . ($i + 1) . ". {$subject}\n";
    }
}

echo "\n✅ All tests completed!\n";
