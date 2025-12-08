# AI Tour Image Assignment - Setup Checklist ✅

## Pre-Flight Checklist

Before running the image assignment system, complete these steps:

---

### ☐ 1. Add OpenAI API Key

**Action**: Add to `.env` file

```env
OPENAI_API_KEY=sk-proj-your-actual-key-here
```

**Get your key**: https://platform.openai.com/api-keys

**Test it works**:
```bash
php artisan tinker
>>> config('services.openai.api_key')
=> "sk-proj-..." ✅
```

---

### ☐ 2. Verify Guzzle HTTP Client

**Check if installed**:
```bash
composer show guzzlehttp/guzzle
```

**If not installed**:
```bash
composer require guzzlehttp/guzzle
```

---

### ☐ 3. Verify Image Directory Structure

**Check tour images exist**:
```bash
ls -la public/images/tours/
```

**Should see directories like**:
- classic-uzbekistan/
- bukhara-city-tour/
- fergana-valley-tour/
- etc.

**Verify images in directories**:
```bash
find public/images/tours -name "*.jpg" -o -name "*.webp" -o -name "*.png" | head -10
```

---

### ☐ 4. Check Database Connection

**Test connection**:
```bash
php artisan tinker
>>> \DB::connection()->getPdo()
=> PDO {#...} ✅
```

**Count tours**:
```bash
php artisan tinker
>>> Tour::count()
=> 28 ✅
```

---

### ☐ 5. View Current Image Status

**Run statistics**:
```bash
php artisan tours:assign-images --stats
```

**Expected output**: Shows how many tours have/don't have images

---

### ☐ 6. Test on Single Tour (DRY RUN)

**Pick a tour ID that needs images**:
```bash
php artisan tours:assign-images --stats
```

Look for tours listed under "Tours without hero images"

**Test dry run**:
```bash
php artisan tours:assign-images --tour=<ID> --dry-run
```

**What to check**:
- ✅ Images discovered (at least 5)
- ✅ AI selects hero + 4 gallery
- ✅ Reasoning makes sense
- ✅ No errors

---

### ☐ 7. Run Live on Single Tour

**If dry run looks good**:
```bash
php artisan tours:assign-images --tour=<ID>
```

**Verify in database**:
```bash
php artisan tinker
>>> $tour = Tour::find(<ID>)
>>> $tour->hero_image
=> "images/tours/..." ✅
>>> $tour->gallery_images
=> [array of 4 images] ✅
```

---

### ☐ 8. Verify on Website

**Check tour page**:
1. Open browser to tour detail page
2. Verify hero image displays
3. Check gallery section shows 4 images
4. Verify images are relevant to tour

---

### ☐ 9. Process Remaining Tours

**Dry run all tours without images**:
```bash
php artisan tours:assign-images --dry-run
```

**Review output carefully**

**Run live**:
```bash
php artisan tours:assign-images
```

**This will process all tours that don't have hero images**

---

### ☐ 10. Final Verification

**Check stats again**:
```bash
php artisan tours:assign-images --stats
```

**Should show**:
- Tours with Hero Image: 28/28 ✅
- Completion %: 100% ✅

---

## Quick Commands Reference

| Task | Command |
|------|---------|
| View stats | `php artisan tours:assign-images --stats` |
| Test single tour | `php artisan tours:assign-images --tour=11 --dry-run` |
| Process single tour | `php artisan tours:assign-images --tour=11` |
| Dry run all | `php artisan tours:assign-images --dry-run` |
| Process all missing | `php artisan tours:assign-images` |
| Force update all | `php artisan tours:assign-images --force` |

---

## Troubleshooting Quick Fixes

### "OpenAI API key not configured"
```bash
# Add to .env
echo "OPENAI_API_KEY=sk-proj-your-key" >> .env
php artisan config:clear
```

### "Not enough images"
```bash
# Check what images exist
ls public/images/tours/<tour-slug>/
# Add more images to that directory
```

### "Class not found" errors
```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

---

## Cost Tracking

**Monitor your OpenAI API usage**: https://platform.openai.com/usage

**Expected costs**:
- Single tour: ~$0.10
- 28 tours: ~$2.80 total
- Very affordable! ✅

---

## All Set? 🚀

If all checkboxes are complete, you're ready to process all tours:

```bash
php artisan tours:assign-images
```

Good luck! 🎉
