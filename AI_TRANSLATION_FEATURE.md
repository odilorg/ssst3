# AI Translation Feature Documentation

## Overview

The AI Translation feature allows automatic translation of tour content using OpenAI's GPT-4 Turbo model. This feature translates all tour sections while preserving HTML formatting and maintaining natural tourism language.

## Features

- ✅ **Automatic Translation** - Translate entire tours with one click
- ✅ **HTML Preservation** - All HTML tags preserved exactly
- ✅ **Tourism-Optimized** - AI trained with tourism-specific prompts
- ✅ **Cost Tracking** - Track tokens used and USD costs per translation
- ✅ **Usage Statistics** - Monitor monthly/daily translations and costs
- ✅ **Multiple Sections** - Translates all 12 tour sections:
  - Title
  - URL Slug
  - Excerpt
  - Description
  - Highlights
  - Itinerary
  - Included Items
  - Excluded Items
  - FAQs
  - Requirements
  - Cancellation Policy
  - Meeting Instructions

## Setup

### 1. Configure OpenAI API Key

1. Log into Filament admin panel: `/admin`
2. Navigate to **Settings → AI Translation**
3. Enter your OpenAI API key (starts with `sk-proj-...`)
4. Select translation model (recommended: **GPT-4 Turbo**)
5. Click **Test API Key** to verify connection
6. Click **Save Settings**

### 2. Cost Estimates

**Per Tour Translation (using GPT-4 Turbo):**
- Approximate cost: **$0.10 - $0.20 USD** per tour
- Translation time: **30-60 seconds**
- Token usage: ~4,000-8,000 tokens (depending on tour length)

**Model Comparison:**
| Model | Quality | Speed | Cost per Tour |
|-------|---------|-------|---------------|
| GPT-4 Turbo | ⭐⭐⭐⭐⭐ Best | Medium | ~$0.16 |
| GPT-4 | ⭐⭐⭐⭐ High | Slower | ~$0.30 |
| GPT-3.5 Turbo | ⭐⭐⭐ Good | Fastest | ~$0.02 |

**Recommendation:** Use **GPT-4 Turbo** for production translations.

## Usage

### Translate a Tour

1. Go to **Tours** in admin panel
2. Click on a tour to edit
3. Go to **Translations** tab
4. Find the translation record you want to auto-translate (e.g., Russian, Uzbek)
5. Click **🤖 AI Translate** button
6. Wait 30-60 seconds for translation to complete
7. Success notification will appear
8. Review the translated content

### Bulk Translation Workflow

To translate a tour to multiple languages:

1. Create empty translation records for target languages
2. Click **🤖 AI Translate** on each record sequentially
3. Review translations after completion
4. Make manual adjustments if needed

### Best Practices

**✅ DO:**
- Ensure English translation is complete and accurate (used as source)
- Review translations after AI generates them
- Use GPT-4 Turbo for best quality
- Monitor costs in Settings → AI Translation
- Test on one tour first before bulk translating

**❌ DON'T:**
- Translate tours with missing English content
- Rely 100% on AI without manual review
- Ignore cost limits (set monthly budget)
- Translate during peak traffic hours (can slow down server)

## Technical Details

### Architecture

**Service Layer:**
- `/app/Services/OpenAI/TranslationService.php` - Core translation logic
- `/config/ai-translation.php` - Configuration file

**Database:**
- `tour_translations` table - Stores translated content
- `translation_logs` table - Tracks usage, costs, status

**Filament UI:**
- `/app/Filament/Pages/AITranslationSettings.php` - Settings page
- `/app/Filament/Resources/Tours/RelationManagers/TourTranslationsRelationManager.php` - Action button

### Translation Process

1. **Source Detection:** Finds English translation of tour
2. **Section Iteration:** Loops through all configured sections
3. **JSON Handling:** Recursively translates array fields (highlights, FAQ, etc.)
4. **API Call:** Sends content to OpenAI with tourism-specific prompts
5. **HTML Preservation:** AI instructed to keep all HTML tags intact
6. **Database Update:** Saves translated content to tour_translations
7. **Logging:** Records tokens used, cost, status in translation_logs

### Prompts

**System Prompt:**
```
You are a professional tourism translator specializing in {locale}.
- Preserve all HTML tags exactly as they appear
- Use natural, engaging tourism language
- Maintain the original tone (informative, inviting, professional)
- Keep all numbers, dates, prices, and proper nouns unchanged
- Use culturally appropriate expressions for the target language

CRITICAL: Return ONLY the translated text without explanations.
```

**User Prompt Template:**
```
Translate this tour {section} from {source_language} to {target_language}:

{content}

Remember: Preserve ALL HTML tags, use natural tourism language.
```

### Security

- API key stored **encrypted** in database via Setting model
- Only admin users can access AI Translation settings
- Rate limiting configured (10/hour, 50/day)
- Cost limits configurable (daily: $10, monthly: $100)

## Usage Statistics

View real-time statistics in **Settings → AI Translation**:

- **Translations This Month** - Total count
- **Cost This Month** - Total USD spent
- **Translations Today** - Daily count
- **Cost Today** - Daily USD spent

## Troubleshooting

### "API Key Not Configured" Error

**Solution:** Go to Settings → AI Translation → Enter API key → Save

### "Source Translation Not Found" Error

**Solution:** Ensure the tour has a complete English translation record

### Translation Takes Too Long

**Possible causes:**
- Large tour with many sections (~60+ sec is normal)
- OpenAI API slow response
- Network latency

**Solution:** Wait up to 2 minutes. If still pending, check translation_logs table for status.

### Translation Failed

**Check translation_logs table:**
```sql
SELECT * FROM translation_logs WHERE status = 'failed' ORDER BY created_at DESC LIMIT 5;
```

**Common reasons:**
- Invalid API key
- OpenAI API rate limit exceeded
- Network timeout
- Insufficient OpenAI credits

### HTML Tags Lost in Translation

**This should not happen** - AI is instructed to preserve HTML. If it does:
1. Check config/ai-translation.php prompts
2. Verify system prompt includes HTML preservation instruction
3. Re-translate the section

## Cost Management

### Set Monthly Budget

Edit `/config/ai-translation.php`:

```php
'cost_limits' => [
    'daily_usd' => 10.00,        // Max $10/day
    'monthly_usd' => 100.00,     // Max $100/month
    'alert_threshold' => 0.80,   // Alert at 80%
],
```

### Monitor Costs

Query translation logs:

```php
use App\Models\TranslationLog;

// This month's cost
$monthlyCost = TranslationLog::getTotalCost('month');

// Today's cost
$todayCost = TranslationLog::getTotalCost('day');

// Total translations this month
$monthlyCount = TranslationLog::getTotalCount('month');
```

### Reduce Costs

1. **Use GPT-3.5 Turbo** for drafts (87% cheaper)
2. **Translate selectively** - Only critical sections
3. **Batch translations** - Plan ahead to avoid rush translations
4. **Review before publishing** - Reduce re-translation needs

## Database Schema

### translation_logs Table

```sql
CREATE TABLE translation_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tour_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    source_locale VARCHAR(10) NOT NULL,
    target_locale VARCHAR(10) NOT NULL,
    sections_translated JSON NOT NULL,
    tokens_used INT UNSIGNED NOT NULL,
    cost_usd DECIMAL(8,4) NOT NULL,
    model VARCHAR(50) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    error_message TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_status (status)
);
```

## Performance Metrics

**Test Results (Ceramics Tour → French):**
- **Sections translated:** 4 (title, slug, excerpt, content)
- **Translation time:** 30.91 seconds
- **Total characters:** ~15,624
- **HTML preserved:** ✅ Yes
- **Natural language:** ✅ Yes
- **French accuracy:** ✅ High quality

## Future Enhancements (Not Yet Implemented)

- [ ] Preview modal before saving translation
- [ ] Batch translate multiple tours at once
- [ ] Selective section translation (choose which sections)
- [ ] Translation from non-English sources (e.g., Russian → Uzbek)
- [ ] Translation memory (reuse previous translations)
- [ ] Custom terminology dictionary
- [ ] Quality scoring (AI confidence levels)

## Support

For issues or questions:
1. Check translation_logs table for error details
2. Verify OpenAI API key is valid and has credits
3. Review system prompts in config/ai-translation.php
4. Test with a simple tour first before bulk translating

---

**Implementation Date:** January 4, 2026
**Model Used:** GPT-4 Turbo
**Status:** ✅ Production Ready
