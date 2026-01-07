<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourPlatformMapping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TourMatcher
{
    /**
     * Smart tour matching with Claude AI:
     * 1. Try exact name match (fastest)
     * 2. Use Claude AI for intelligent matching (handles all edge cases)
     */
    public function match(string $platform, string $externalTourName): array
    {
        Log::info("[TourMatcher] Matching tour: {$externalTourName} on {$platform}");

        // 1. Try exact name match first
        $exactMatch = TourPlatformMapping::active()
            ->forPlatform($platform)
            ->where('external_tour_name', $externalTourName)
            ->first();

        if ($exactMatch) {
            Log::info("[TourMatcher] Exact match found: Tour #{$exactMatch->tour_id}");
            return [
                'found' => true,
                'mapping' => $exactMatch,
                'tour_id' => $exactMatch->tour_id,
                'confidence' => 100,
                'method' => 'exact',
            ];
        }

        // 2. Use Claude AI for intelligent matching
        $claudeResult = $this->matchWithClaude($platform, $externalTourName);

        if ($claudeResult && $claudeResult['found']) {
            Log::info("[TourMatcher] Claude match: Tour #{$claudeResult['tour_id']} ({$claudeResult['confidence']}%)");

            // Auto-learn: create mapping for high-confidence matches
            if ($claudeResult['confidence'] >= 80) {
                $this->learnFromMatch($platform, $externalTourName, $claudeResult);
            }

            return $claudeResult;
        }

        Log::info("[TourMatcher] No match found for: {$externalTourName}");

        return [
            'found' => false,
            'mapping' => null,
            'tour_id' => null,
            'confidence' => 0,
            'method' => $claudeResult['method'] ?? 'none',
            'reason' => $claudeResult['reason'] ?? 'No matching tour found',
        ];
    }

    /**
     * Use Claude AI to intelligently match tour
     */
    protected function matchWithClaude(string $platform, string $externalTourName): ?array
    {
        try {
            // Get all available tours with details
            $tours = Tour::where('is_active', true)
                ->get()
                ->map(fn($tour) => [
                    'id' => $tour->id,
                    'title' => $tour->title,
                    'slug' => $tour->slug,
                ])
                ->toArray();

            if (empty($tours)) {
                Log::warning('[TourMatcher] No active tours in database');
                return null;
            }

            $toursJson = json_encode($tours, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Comprehensive prompt with clear instructions
            $prompt = $this->buildPrompt($platform, $externalTourName, $toursJson);

            // Write prompt to temp file
            $tempFile = '/tmp/claude-match-' . uniqid() . '.txt';
            file_put_contents($tempFile, $prompt);

            // Call Claude CLI with continue flag for context
            $result = Process::timeout(60)->run(
                "cat \"{$tempFile}\" | /usr/bin/claude -p - --continue --output-format json 2>&1"
            );

            @unlink($tempFile);

            if (!$result->successful()) {
                Log::warning('[TourMatcher] Claude CLI error: ' . $result->errorOutput());
                return null;
            }

            $output = trim($result->output());
            Log::debug('[TourMatcher] Claude response: ' . substr($output, 0, 500));

            return $this->parseClaudeResponse($output);

        } catch (\Exception $e) {
            Log::error('[TourMatcher] Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build comprehensive prompt for Claude
     */
    protected function buildPrompt(string $platform, string $tourName, string $toursJson): string
    {
        return <<<PROMPT
# Tour Matching Task for Jahongir Travel (Uzbekistan)

You are a tour matching expert. Your job is to match external OTA tour names to our internal tour catalog.

## External Tour to Match
- Platform: {$platform}
- Tour Name: "{$tourName}"

## Our Tour Catalog
{$toursJson}

## About Our Tours

### DESTINATIONS WE COVER:
- **Samarkand** - Our main hub (city tours, day trips, yurt camps)
- **Bukhara** - Day trips from Samarkand
- **Shahrisabz** - Day trips from Samarkand (Timur's birthplace)
- **Seven Lakes (Tajikistan)** - Day trips from Samarkand
- **Fergana Valley** - Multi-day craft tours
- **Desert/Yurt camps** - Overnight experiences near Samarkand

### DESTINATIONS WE DO NOT COVER:
- Khiva (too far from Samarkand)
- Tashkent city tours (we're based in Samarkand)
- Nukus/Karakalpakstan as standalone (only in multi-day)
- Termez
- Any other Uzbek cities not listed above

### TOUR TYPES WE OFFER:
- City sightseeing tours (Registan, mosques, madrasas)
- Historical/cultural day trips
- Craft/artisan workshops (pottery, silk, textiles)
- Desert yurt camp overnights
- Multi-day regional tours

### TOUR TYPES WE DO NOT OFFER:
- Wine tours / wine tasting
- Cooking classes
- Adventure sports (hiking, cycling, rafting)
- Food tours
- Shopping tours
- Photography tours
- Airport transfers
- Hotel bookings only

## Matching Rules

1. **Destination must match**: "Shahrisabz Tour" → must be a Shahrisabz tour
2. **Activity type must match**: "Yurt camp" → must be yurt/desert tour, not city tour
3. **Synonyms to recognize**:
   - "Blue Domes" / "Registan" / "Samarkand Day" = Samarkand City Tour
   - "Yurt" / "Desert" / "Camp" / "Overnight" / "Camel" = Desert Yurt Camp Tour
   - "Pottery" / "Ceramics" / "Craft" = Craft workshop tours
   - "Paper factory" / "Konigil" = Shahrisabz area tour

4. **Confidence scoring**:
   - 95-100%: Exact destination AND activity match
   - 80-94%: Clear destination match, slight activity variation
   - 60-79%: Probable match but some uncertainty
   - Below 60%: Don't match, ask for manual review

5. **When NOT to match** (return found: false):
   - Unknown destination (Khiva, Tashkent, etc.)
   - Activity type we don't offer (wine, cooking, etc.)
   - Too vague to determine (just "Uzbekistan Tour")

## Response Format

Return ONLY valid JSON (no markdown, no explanation):

If match found:
{"found": true, "tour_id": <number>, "confidence": <60-100>, "reason": "<brief explanation>"}

If no match:
{"found": false, "tour_id": null, "confidence": 0, "reason": "<why no match>"}

Now analyze "{$tourName}" and return your JSON response:
PROMPT;
    }

    /**
     * Parse Claude's response
     */
    protected function parseClaudeResponse(string $output): ?array
    {
        $parsed = json_decode($output, true);

        // Handle Claude's wrapped response format
        if (isset($parsed['result'])) {
            $inner = $parsed['result'];
            // Remove markdown if present
            $inner = preg_replace('/```json\s*/', '', $inner);
            $inner = preg_replace('/```\s*/', '', $inner);
            $inner = trim($inner);

            // Extract JSON object
            if (preg_match('/\{[^{}]*"found"[^{}]*\}/', $inner, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!isset($parsed['found'])) {
            Log::warning('[TourMatcher] Could not parse response');
            return null;
        }

        if ($parsed['found'] && $parsed['tour_id']) {
            // Verify tour exists
            $tour = Tour::find($parsed['tour_id']);
            if (!$tour) {
                Log::warning("[TourMatcher] Invalid tour ID #{$parsed['tour_id']}");
                return null;
            }

            return [
                'found' => true,
                'mapping' => null,
                'tour_id' => (int) $parsed['tour_id'],
                'confidence' => (int) ($parsed['confidence'] ?? 70),
                'method' => 'claude',
                'reason' => $parsed['reason'] ?? '',
            ];
        }

        return [
            'found' => false,
            'mapping' => null,
            'tour_id' => null,
            'confidence' => 0,
            'method' => 'claude',
            'reason' => $parsed['reason'] ?? 'No suitable match',
        ];
    }

    /**
     * Learn from Claude's match - create mapping for future
     */
    protected function learnFromMatch(string $platform, string $externalTourName, array $claudeResult): void
    {
        try {
            $exists = TourPlatformMapping::where('platform', $platform)
                ->where('external_tour_name', $externalTourName)
                ->exists();

            if (!$exists) {
                TourPlatformMapping::create([
                    'platform' => $platform,
                    'external_tour_name' => $externalTourName,
                    'tour_id' => $claudeResult['tour_id'],
                    'match_confidence' => $claudeResult['confidence'],
                    'auto_confirm' => false,
                    'is_active' => true,
                    'notes' => 'Auto-learned by Claude AI: ' . ($claudeResult['reason'] ?? ''),
                ]);

                Log::info("[TourMatcher] Learned: '{$externalTourName}' → Tour #{$claudeResult['tour_id']}");
            }
        } catch (\Exception $e) {
            Log::error('[TourMatcher] Failed to learn: ' . $e->getMessage());
        }
    }
}
