# AI Tour Image Assignment System

## Overview

Automatically assign hero and gallery images to your tours using OpenAI's GPT-4 Vision API. The system intelligently analyzes tour context and selects the most appropriate images from your image library.

---

## Features

✅ **AI-Powered Selection** - Uses GPT-4 Vision to understand tour context and match appropriate images
✅ **Intelligent Image Discovery** - Scans tour directories and finds related images
✅ **Smart Fallback** - Falls back to similar tours or city-based images if needed
✅ **Dry Run Mode** - Preview selections before committing to database
✅ **Batch Processing** - Process all tours or specific ones
✅ **Safety First** - Only processes tours without images unless forced
✅ **SEO Optimization** - Automatically generates alt text for gallery images
✅ **Statistics** - View image assignment status across all tours

---

## Installation & Setup

### 1. Files Created

The system includes:
- `app/Services/ImageDiscoveryService.php` - Image scanning and discovery
- `app/Services/AIImageMatchingService.php` - AI-powered image selection
- `app/Services/TourImageAssignmentService.php` - Database updates and validation
- `app/Console/Commands/AssignTourImages.php` - CLI command interface
- `config/services.php` - Updated with OpenAI configuration

### 2. Add OpenAI API Key

Add to your `.env` file:

```env
OPENAI_API_KEY=sk-proj-your-api-key-here
OPENAI_VISION_MODEL=gpt-4o  # Optional, defaults to gpt-4o
```

Get your API key from: https://platform.openai.com/api-keys

### 3. Install Dependencies (if needed)

```bash
composer require guzzlehttp/guzzle
```

---

## Usage

### View Statistics

Check current image assignment status:

```bash
php artisan tours:assign-images --stats
```

Output example:
```
╔════════════════════════════════════════════════╗
║          Tour Image Statistics                 ║
╚════════════════════════════════════════════════╝

┌────────────────────────────┬───────┐
│ Metric                     │ Value │
├────────────────────────────┼───────┤
│ Total Tours                │ 28    │
│ Tours with Hero Image      │ 26    │
│ Tours with Gallery Images  │ 15    │
│ Tours without Images       │ 2     │
│ Completion %               │ 92.86%│
└────────────────────────────┴───────┘

Tours without hero images (2):
  - #8: 5-Day Silk Road Classic
  - #9: Full Day Bukhara City Tour
```

---

### Test on Single Tour (Recommended First Step)

Always test on one tour first to verify everything works:

```bash
php artisan tours:assign-images --tour=11 --dry-run
```

This will:
- ✅ Discover images for tour ID 11
- ✅ Ask AI to select best images
- ✅ Show selections with reasoning
- ❌ NOT save to database

Example output:
```
╔════════════════════════════════════════════════╗
║   AI-Powered Tour Image Assignment System     ║
╚════════════════════════════════════════════════╝

🔍 DRY RUN MODE - No changes will be saved to database

Processing single tour: ID 11
Found 1 tour(s) to process

─────────────────────────────────────────────────
Tour #11: Golden Ring of Samarkand: A Historical Journey
Slug: golden-ring-samarkand

📸 Discovering candidate images...
   Found 7 candidate images

🤖 Asking AI to select best images...

   ✅ AI selections:
   Hero:   registan-square-panorama.webp
   Reason: Iconic landmark that represents Samarkand's UNESCO heritage

   Gallery:
   1. gur-emir-mausoleum.webp
      Shows Tamerlane's tomb, key historical site
   2. shah-i-zinda.webp
      Showcases stunning blue tile architecture
   3. bibi-khanym-mosque.webp
      Demonstrates scale of Islamic architecture
   4. ulugbek-observatory.webp
      Highlights scientific history aspect

   [DRY RUN - No database changes made]

╔════════════════════════════════════════════════╗
║                   SUMMARY                      ║
╚════════════════════════════════════════════════╝
┌──────────────┬───────┐
│ Status       │ Count │
├──────────────┼───────┤
│ ✅ Processed │ 1     │
│ ❌ Failed    │ 0     │
│ ⏭️  Skipped  │ 0     │
│ 📊 Total     │ 1     │
└──────────────┴───────┘

💡 This was a dry run. Run without --dry-run to save changes.
```

---

### Process Single Tour (Live)

After verifying dry run looks good:

```bash
php artisan tours:assign-images --tour=11
```

This will update the database for tour ID 11.

---

### Process All Tours Without Images

Safe batch processing (only tours missing hero images):

```bash
php artisan tours:assign-images --dry-run
```

Review output, then run live:

```bash
php artisan tours:assign-images
```

---

### Force Update All Tours

Re-process ALL tours, overwriting existing images:

```bash
php artisan tours:assign-images --force --dry-run
```

⚠️ **Warning**: This will replace existing image assignments!

---

## Command Options

| Option | Description |
|--------|-------------|
| `--tour=ID` | Process specific tour by ID |
| `--dry-run` | Preview without saving to database |
| `--force` | Overwrite existing images |
| `--stats` | Show statistics only |

---

## How It Works

### 1. Image Discovery

The system searches for images in this order:

1. **Tour's dedicated directory**: `public/images/tours/{tour-slug}/`
2. **Similar tour directories**: Matches based on slug keywords
3. **City-based images**: Finds images from same city

### 2. AI Selection Process

For each tour, the AI:

1. Receives tour context:
   - Title
   - Description (first 500 chars)
   - Duration
   - City
   - Tour type
   - Highlights

2. Analyzes each candidate image

3. Selects:
   - **1 hero image** - Most iconic, compelling shot
   - **4 gallery images** - Diverse supporting images

4. Provides reasoning for each selection

### 3. Validation

Before saving, the system validates:
- ✅ Hero image selected
- ✅ Exactly 4 gallery images
- ✅ All images have valid paths
- ✅ No duplicate selections
- ✅ Image files exist

### 4. Database Update

Updates `tours` table:
```php
[
    'hero_image' => 'images/tours/classic-uzbekistan/registan-square.webp',
    'gallery_images' => [
        [
            'path' => 'images/tours/classic-uzbekistan/bibi-khanym.webp',
            'alt' => 'Classic Uzbekistan Tour - Bibi Khanym Mosque'
        ],
        // ... 3 more
    ]
]
```

---

## Cost Estimate

**OpenAI API Pricing** (GPT-4o Vision):
- ~$0.01 per image analyzed
- 5-15 images per tour = **$0.05 - $0.15 per tour**
- **28 tours = ~$2.80 total** (one-time cost)

Very affordable for improving your tour catalog!

---

## Troubleshooting

### Error: "OpenAI API key not configured"

**Solution**: Add `OPENAI_API_KEY` to your `.env` file

### Error: "Not enough images (need at least 5)"

**Reason**: Tour directory has fewer than 5 images

**Solutions**:
1. Add more images to `public/images/tours/{tour-slug}/`
2. The system will automatically search similar tours if available
3. Manually assign images for this tour

### Error: "Invalid API response"

**Possible causes**:
- Network issues
- Invalid API key
- Rate limiting

**Solution**:
- Check your API key
- Wait a few seconds and retry
- Check OpenAI status: https://status.openai.com

### Tours Being Skipped

**Reason**: Not enough candidate images found

**Check**:
```bash
php artisan tours:assign-images --tour=<ID> --dry-run
```

Look for: "Found X candidate images" - needs at least 5

---

## Image Organization Best Practices

### Recommended Directory Structure

```
public/images/tours/
├── classic-uzbekistan/
│   ├── hero.jpg (or any descriptive name)
│   ├── registan-square.webp
│   ├── bibi-khanym-mosque.webp
│   ├── shah-i-zinda.webp
│   ├── gur-emir-mausoleum.webp
│   └── samarkand-market.webp
│
├── bukhara-city-tour/
│   ├── hero.jpg
│   ├── ark-fortress.webp
│   ├── poi-kalyan-complex.webp
│   ├── lyabi-hauz.webp
│   └── ...
```

### Naming Conventions

Use descriptive filenames:
- ✅ `registan-square-sunset.webp`
- ✅ `khiva-old-city.webp`
- ✅ `bukhara-ark-fortress.webp`
- ❌ `IMG_1234.jpg`
- ❌ `photo1.jpg`

Better names help the AI understand image content!

---

## API Rate Limiting

The command includes automatic rate limiting:
- **2 second delay** between tours
- Prevents API throttling
- Safe for batch processing

For large batches (>10 tours), consider:
```bash
# Process in smaller batches
php artisan tours:assign-images --force --dry-run
# Then run live for specific tours
```

---

## Future Enhancements

Potential improvements:
- [ ] Image quality analysis (resolution check)
- [ ] Automatic WebP conversion
- [ ] Responsive image size generation
- [ ] A/B testing for hero images
- [ ] Integration with Unsplash/Pexels APIs
- [ ] Seasonal image rotation
- [ ] Multi-language alt text generation

---

## Support & Logs

### Check Logs

All operations are logged to Laravel's log files:

```bash
tail -f storage/logs/laravel.log
```

Look for:
- `Tour image assignment failed` - Errors
- `AI image selection parsed successfully` - Successes
- `OpenAI API response received` - API calls

### Need Help?

1. Check logs first
2. Run with `--dry-run` to diagnose
3. Test single tour: `--tour=ID`
4. Verify `.env` has `OPENAI_API_KEY`

---

## Example Workflow

Complete workflow for first-time use:

```bash
# 1. Check current status
php artisan tours:assign-images --stats

# 2. Test on one tour
php artisan tours:assign-images --tour=11 --dry-run

# 3. If satisfied, run live for that tour
php artisan tours:assign-images --tour=11

# 4. Process all remaining tours (dry run first)
php artisan tours:assign-images --dry-run

# 5. Review output, then run live
php artisan tours:assign-images

# 6. Check final stats
php artisan tours:assign-images --stats
```

---

## License

Part of the SSST3 Laravel application.

---

**Ready to use!** 🚀

Run `php artisan tours:assign-images --stats` to get started.
