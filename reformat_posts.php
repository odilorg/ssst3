<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Reformatting Posts 7-10...\n\n";

// POST 7 - Already has content, just needs proper HTML structure
$post7 = \App\Models\BlogPost::find(7);
$content7 = file_get_contents(__DIR__ . '/post7_formatted.html');
$post7->content = $content7;
$post7->save();
echo "✓ Post 7 reformatted\n";

// POST 8
$post8 = \App\Models\BlogPost::find(8);
if (file_exists(__DIR__ . '/post8_formatted.html')) {
    $content8 = file_get_contents(__DIR__ . '/post8_formatted.html');
    $post8->content = $content8;
    $post8->save();
    echo "✓ Post 8 reformatted\n";
}

// POST 9
$post9 = \App\Models\BlogPost::find(9);
if (file_exists(__DIR__ . '/post9_formatted.html')) {
    $content9 = file_get_contents(__DIR__ . '/post9_formatted.html');
    $post9->content = $content9;
    $post9->save();
    echo "✓ Post 9 reformatted\n";
}

// POST 10
$post10 = \App\Models\BlogPost::find(10);
if (file_exists(__DIR__ . '/post10_formatted.html')) {
    $content10 = file_get_contents(__DIR__ . '/post10_formatted.html');
    $post10->content = $content10;
    $post10->save();
    echo "✓ Post 10 reformatted\n";
}

echo "\nAll posts reformatted successfully!\n";
