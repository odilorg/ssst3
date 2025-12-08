# AI-Powered Tour Image Assignment Plan

## Executive Summary

Create an intelligent script that uses OpenAI's GPT-4 Vision API to automatically assign appropriate images to each tour from an available image pool. The system will analyze tour context (title, description, location) and match it with the most suitable images from the existing collection.

---

## 1. Current System Analysis

### Database Schema

**Tours Table** (main fields relevant to images):
- `id` - Tour identifier
- `title` - Tour name (e.g., "Classic Uzbekistan Tour")
- `slug` - URL-friendly identifier
- `short_description` - Brief tour summary (up to 255 chars)
- `long_description` - Detailed tour description (rich text)
- `city_id` - Primary city (relation to `cities` table)
- `duration_days` - Tour length
- `tour_type` - private_only, group_only, or hybrid
- `highlights` - JSON array of key features
- **`hero_image`** - Main featured image (single string path)
- **`gallery_images`** - Additional images (JSON array)

**Gallery Images Structure** (JSON):
```json
[
  {
    "path": "tours/gallery/image1.jpg",
    "alt": "Description for SEO"
  },
  {
    "path": "tours/gallery/image2.webp",
    "alt": "Another description"
  }
]
```

### Current Image Storage

**Location**: `/public/images/tours/{tour-slug}/`

**Statistics**:
- **Total tours in database**: 28 tours
- **Total images available**: 51 images across all tour directories
- **Tours with hero images**: 26 tours (2 missing)
- **Tours with gallery images**: Needs investigation

**Storage Paths**:
- Hero images: `images/tours/{slug}/hero.jpg` OR `tours/heroes/{filename}.webp` (Filament upload)
- Gallery images: `images/tours/{slug}/*.webp` or `tours/gallery/{filename}.webp`

**Example tour directory** (`kz-kg-nature`):
- charyn-canyon-valley-castles.webp
- issyk-kul-mountains.webp
- big-almaty-lake.webp
- jeti-oguz-red-rocks.webp
- ala-archa-gorge.webp
- skazka-canyon.webp
- altyn-emel-singing-dunes.webp
(7 images total - perfect for 1 hero + 6 gallery images, allowing selection of top 5)

---

## 2. Script Requirements

### Objective
For each tour, automatically:
1. Assign 1 **hero image** (featured/main image)
2. Assign 4 **gallery images** (additional photos)

### Selection Criteria
- **Relevance**: Images must match tour content (location, landmarks, activities)
- **Visual Quality**: High-resolution, professional, appealing
- **Diversity**: Gallery should show variety (landscapes, architecture, culture, activities)
- **Context Awareness**: Consider tour type, duration, highlights

---

## 3. Technical Architecture

### A. Image Discovery Module

**Purpose**: Scan and catalog all available images

```php
class ImageDiscoveryService
{
    /**
     * Scan public/images/tours directory
     * Return: Array of all images with metadata
     */
    public function discoverImages(): array
    {
        // 1. Find all image files in public/images/tours/**
        // 2. Extract metadata:
        //    - Full path
        //    - Relative path (for database)
        //    - Filename
        //    - Directory/tour slug hint
        //    - File size
        //    - Dimensions (optional, via getimagesize())
        //    - File extension

        return [
            [
                'full_path' => '/public/images/tours/classic-uzbekistan/registan-square-night.webp',
                'relative_path' => 'images/tours/classic-uzbekistan/registan-square-night.webp',
                'filename' => 'registan-square-night.webp',
                'tour_hint' => 'classic-uzbekistan',
                'extension' => 'webp'
            ],
            // ... more images
        ];
    }
}
```

### B. AI Vision Analysis Module

**Purpose**: Use OpenAI GPT-4 Vision to analyze images and match them to tours

**OpenAI API Integration**:

```php
class AIImageMatchingService
{
    private string $apiKey;

    public function __construct(string $openAiApiKey)
    {
        $this->apiKey = $openAiApiKey;
    }

    /**
     * Analyze a batch of images and match to tour context
     *
     * @param Tour $tour - Tour model with all data
     * @param array $candidateImages - Array of image paths to consider
     * @return array - Selected images with reasoning
     */
    public function selectImagesForTour(Tour $tour, array $candidateImages): array
    {
        // 1. Build tour context for AI
        $tourContext = $this->buildTourContext($tour);

        // 2. Encode images as base64 (GPT-4 Vision requirement)
        $encodedImages = $this->encodeImages($candidateImages);

        // 3. Call OpenAI GPT-4 Vision API
        $response = $this->callOpenAI($tourContext, $encodedImages);

        // 4. Parse response to get selected images
        return $this->parseAIResponse($response);
    }

    private function buildTourContext(Tour $tour): array
    {
        return [
            'title' => $tour->title,
            'description' => strip_tags($tour->short_description ?? $tour->long_description),
            'duration' => $tour->duration_days . ' days',
            'city' => $tour->city?->name,
            'type' => $tour->tour_type,
            'highlights' => $tour->highlights ?? [],
        ];
    }

    private function callOpenAI(array $tourContext, array $encodedImages): array
    {
        $client = new \GuzzleHttp\Client();

        // Build messages for Vision API
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert travel photographer and tour marketing specialist. Your task is to select the most appropriate images for a tour package based on the tour details.'
            ],
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $this->buildPrompt($tourContext)
                    ],
                    // Add each image
                    ...$this->buildImageContent($encodedImages)
                ]
            ]
        ];

        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o', // or gpt-4-vision-preview
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.3, // Lower for more consistent results
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    private function buildPrompt(array $tourContext): string
    {
        return <<<PROMPT
I need you to select 5 images for this tour:

**Tour Details:**
- Title: {$tourContext['title']}
- Description: {$tourContext['description']}
- Duration: {$tourContext['duration']}
- Main City: {$tourContext['city']}
- Type: {$tourContext['type']}
- Highlights: {$tourContext['highlights']}

**Task:**
From the provided images, select exactly 5 images:
1. **ONE hero image** - The most compelling, iconic image that best represents this tour
2. **FOUR gallery images** - Supporting images showing variety (landscapes, architecture, culture, activities)

**Selection Criteria:**
- Images MUST be relevant to the tour location and theme
- Hero image should be stunning, high-quality, and immediately captivating
- Gallery should show diversity of experiences
- Prioritize authentic cultural and architectural shots

**Output Format (JSON only):**
{
  "hero": {
    "index": <image_number>,
    "reason": "<brief reason why this is the best hero image>"
  },
  "gallery": [
    {
      "index": <image_number>,
      "reason": "<why this supports the tour>"
    },
    // ... 3 more
  ]
}

Respond ONLY with valid JSON, no other text.
PROMPT;
    }

    private function buildImageContent(array $encodedImages): array
    {
        $content = [];
        foreach ($encodedImages as $index => $imageData) {
            $content[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:image/{$imageData['extension']};base64,{$imageData['base64']}"
                ]
            ];
            $content[] = [
                'type' => 'text',
                'text' => "Image #{$index}: {$imageData['filename']}"
            ];
        }
        return $content;
    }
}
```

### C. Assignment Strategy

**Strategy 1: Directory-Based (Recommended for Phase 1)**
- Each tour has its own directory with pre-curated images
- AI selects the best 5 images from that tour's directory
- Fallback to generic images if directory has < 5 images

**Strategy 2: Global Pool (Phase 2, Optional)**
- All images in a shared pool
- AI matches based purely on content analysis
- More flexible but requires more AI processing

### D. Database Update Module

```php
class TourImageAssignmentService
{
    public function assignImagesToTour(Tour $tour, array $selectedImages): void
    {
        // 1. Update hero_image
        $tour->update([
            'hero_image' => $selectedImages['hero']['path']
        ]);

        // 2. Build gallery_images JSON
        $galleryImages = [];
        foreach ($selectedImages['gallery'] as $image) {
            $galleryImages[] = [
                'path' => $image['path'],
                'alt' => $this->generateAltText($tour, $image)
            ];
        }

        $tour->update([
            'gallery_images' => $galleryImages
        ]);
    }

    private function generateAltText(Tour $tour, array $image): string
    {
        // Generate SEO-friendly alt text
        // Example: "Classic Uzbekistan Tour - Registan Square at night"
        $tourName = $tour->title;
        $imageName = pathinfo($image['filename'], PATHINFO_FILENAME);
        $imageName = str_replace(['-', '_'], ' ', $imageName);
        $imageName = ucwords($imageName);

        return "{$tourName} - {$imageName}";
    }
}
```

---

## 4. Script Implementation Plan

### Main Script: `assign_tour_images.php`

```php
#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Models\Tour;
use App\Services\ImageDiscoveryService;
use App\Services\AIImageMatchingService;
use App\Services\TourImageAssignmentService;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Configuration
$openAiApiKey = env('OPENAI_API_KEY');
if (!$openAiApiKey) {
    echo "Error: OPENAI_API_KEY not set in .env file\n";
    exit(1);
}

// Initialize services
$imageDiscovery = new ImageDiscoveryService();
$aiMatcher = new AIImageMatchingService($openAiApiKey);
$assignmentService = new TourImageAssignmentService();

// Options
$dryRun = in_array('--dry-run', $argv);
$tourId = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--tour=')) {
        $tourId = (int) str_replace('--tour=', '', $arg);
    }
}

echo "=== AI Tour Image Assignment ===\n\n";
echo "Mode: " . ($dryRun ? "DRY RUN (no changes)" : "LIVE") . "\n\n";

// Get tours to process
$toursQuery = Tour::query();
if ($tourId) {
    $toursQuery->where('id', $tourId);
    echo "Processing single tour: ID {$tourId}\n\n";
}
$tours = $toursQuery->get();

echo "Found {$tours->count()} tour(s) to process\n\n";

// Process each tour
foreach ($tours as $tour) {
    echo "─────────────────────────────────────\n";
    echo "Tour #{$tour->id}: {$tour->title}\n";
    echo "Slug: {$tour->slug}\n";
    echo "Current hero: " . ($tour->hero_image ?? 'NONE') . "\n";
    echo "Current gallery: " . (count($tour->gallery_images ?? []) . " images") . "\n\n";

    // 1. Discover candidate images
    echo "📸 Discovering images...\n";
    $candidateImages = $imageDiscovery->discoverImagesForTour($tour);
    echo "   Found {$candidateImages['count']} candidate images\n";

    if ($candidateImages['count'] < 5) {
        echo "   ⚠️  Warning: Only {$candidateImages['count']} images found, need at least 5\n";
        echo "   Skipping tour...\n\n";
        continue;
    }

    // 2. AI selection
    echo "🤖 Asking AI to select best images...\n";
    try {
        $selectedImages = $aiMatcher->selectImagesForTour($tour, $candidateImages['images']);

        echo "   ✅ AI selected:\n";
        echo "      Hero: {$selectedImages['hero']['filename']}\n";
        echo "      Reason: {$selectedImages['hero']['reason']}\n";
        echo "      Gallery:\n";
        foreach ($selectedImages['gallery'] as $i => $img) {
            echo "        " . ($i + 1) . ". {$img['filename']}\n";
            echo "           {$img['reason']}\n";
        }

        // 3. Assign to database
        if (!$dryRun) {
            echo "\n💾 Updating database...\n";
            $assignmentService->assignImagesToTour($tour, $selectedImages);
            echo "   ✅ Tour images updated successfully\n";
        } else {
            echo "\n   [DRY RUN - No database changes made]\n";
        }

    } catch (\Exception $e) {
        echo "   ❌ Error: {$e->getMessage()}\n";
    }

    echo "\n";
    sleep(2); // Rate limiting for API
}

echo "=== Process Complete ===\n";
```

---

## 5. Implementation Steps

### Phase 1: Setup (Day 1)

1. **Create service classes**:
   - `app/Services/ImageDiscoveryService.php`
   - `app/Services/AIImageMatchingService.php`
   - `app/Services/TourImageAssignmentService.php`

2. **Add OpenAI API key to `.env`**:
   ```
   OPENAI_API_KEY=sk-...
   ```

3. **Install Guzzle** (if not already):
   ```bash
   composer require guzzlehttp/guzzle
   ```

### Phase 2: Development (Day 2-3)

4. **Implement ImageDiscoveryService**
   - Scan directories
   - Filter by tour slug
   - Return structured image data

5. **Implement AIImageMatchingService**
   - Encode images to base64
   - Build prompts
   - Call OpenAI Vision API
   - Parse responses

6. **Implement TourImageAssignmentService**
   - Update database records
   - Generate alt text
   - Handle errors

7. **Create main script** `scripts/assign_tour_images.php`

### Phase 3: Testing (Day 3)

8. **Test on single tour**:
   ```bash
   php scripts/assign_tour_images.php --dry-run --tour=11
   ```

9. **Review AI selections** for quality

10. **Adjust prompts** if needed for better results

### Phase 4: Production Run (Day 4)

11. **Dry run on all tours**:
    ```bash
    php scripts/assign_tour_images.php --dry-run
    ```

12. **Review output logs**

13. **Execute live run**:
    ```bash
    php scripts/assign_tour_images.php
    ```

14. **Verify database updates**

15. **Check frontend display**

---

## 6. Cost Estimation

**OpenAI API Costs** (GPT-4 Vision):
- Model: `gpt-4o` or `gpt-4-vision-preview`
- Cost: ~$0.01 per image analyzed
- Per tour: ~5-10 images analyzed = $0.05 - $0.10
- **28 tours × $0.10 = ~$2.80 total**

Very affordable for one-time assignment.

---

## 7. Fallback & Edge Cases

### Not Enough Images
- **Scenario**: Tour directory has < 5 images
- **Solution**:
  - Skip tour with warning
  - OR use generic placeholder images
  - OR fetch from related tours' images

### AI Selection Failure
- **Scenario**: OpenAI API error or invalid response
- **Solution**:
  - Retry with exponential backoff
  - Log error and skip tour
  - Manual review required

### Duplicate Selections
- **Scenario**: AI selects same image for hero and gallery
- **Solution**: Validation step to ensure uniqueness

---

## 8. Future Enhancements

1. **Image Quality Analysis**
   - Check resolution before assignment
   - Reject blurry or low-quality images

2. **Automated Image Sourcing**
   - Integrate with Unsplash/Pexels APIs
   - Download images automatically based on tour keywords

3. **WebP Conversion**
   - Convert all images to WebP format
   - Generate responsive sizes

4. **A/B Testing**
   - Track which hero images get more clicks
   - Re-optimize selections based on engagement

5. **Seasonal Images**
   - Different hero images for different seasons
   - Schedule changes automatically

---

## 9. File Structure

```
ssst3/
├── app/
│   └── Services/
│       ├── ImageDiscoveryService.php
│       ├── AIImageMatchingService.php
│       └── TourImageAssignmentService.php
├── scripts/
│   └── assign_tour_images.php
├── storage/
│   └── logs/
│       └── image-assignment-{date}.log
└── public/
    └── images/
        └── tours/
            ├── classic-uzbekistan/
            ├── bukhara-city-tour/
            └── ...
```

---

## 10. Next Steps

Ready to proceed? I can:
1. ✅ Create the service classes
2. ✅ Build the main assignment script
3. ✅ Add error handling and logging
4. ✅ Test on a single tour first

**What would you like me to start with?**
