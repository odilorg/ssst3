# Phase 2 AI Translation - Deployment Checklist

## Pre-Deployment Verification

### Database Migrations ✅
- [x] Migration: `2026_01_04_073428_add_content_json_to_tour_translations_table.php` - **RAN**
- [x] Migration: `2026_01_04_123501_create_translation_logs_table.php` - **RAN**

### Code Committed ✅
- [x] Commit: `4a41627` - "feat(i18n): implement AI-powered tour translation with OpenAI GPT-4 Turbo"
- [x] Pushed to branch: `feature/octobank-payment-integration`
- [x] 50 files changed (7671 insertions, 377 deletions)

### Dependencies ✅
- [x] openai-php/client v0.10.1 installed via Composer
- [x] All Composer dependencies resolved (no conflicts)

### Configuration Files ✅
- [x] `/config/ai-translation.php` created
- [x] Translation prompts configured
- [x] Section priorities defined
- [x] Cost limits configured

### Feature Testing ✅
- [x] Russian translation tested (Ceramics tour - 63 fields)
- [x] French AI translation tested (30.91 seconds, $0.16 USD)
- [x] HTML preservation verified
- [x] All 4 bugs fixed and verified
- [x] API key validation working

---

## Deployment Steps

### Step 1: Code Deployment
```bash
# SSH to staging server
ssh -i /home/odil/projects/id_rsa -p 2222 root@62.72.22.205

# Navigate to project
cd /var/www/staging.jahongir-travel.uz

# Stash any local changes (if needed)
git stash

# Pull latest code
git fetch origin
git pull origin feature/octobank-payment-integration

# Verify commit hash
git log -1 --oneline
# Should show: 4a41627 feat(i18n): implement AI-powered tour translation with OpenAI GPT-4 Turbo
```

### Step 2: Install Dependencies
```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Verify openai-php/client installed
composer show openai-php/client
# Should show: v0.10.1
```

### Step 3: Run Database Migrations
```bash
# Run migrations (migrations should already be run, but verify)
php artisan migrate --force

# Check migration status
php artisan migrate:status | grep -E "2026_01_04"
# Should show both migrations as "Ran"
```

### Step 4: Clear Caches
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild config cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Configure OpenAI API Key
```bash
# Option A: Via Admin Panel (Recommended)
# 1. Login to /admin
# 2. Go to Settings → AI Translation
# 3. Enter your OpenAI API key (starts with sk-proj-...)
# 4. Select model: GPT-4 Turbo
# 5. Test connection
# 6. Save

# Option B: Via Tinker (if admin not accessible)
php artisan tinker --execute="
\App\Models\Setting::set('ai_translation_api_key', 'YOUR_OPENAI_API_KEY_HERE');
\App\Models\Setting::set('ai_translation_model', 'gpt-4-turbo');
echo '✅ API key configured' . PHP_EOL;
"

# Note: Replace YOUR_OPENAI_API_KEY_HERE with actual API key from OpenAI dashboard
# Get API key from: https://platform.openai.com/api-keys
```

### Step 6: Verify Deployment
```bash
# Test OpenAI connection
php artisan tinker --execute="
\$service = new \App\Services\OpenAI\TranslationService();
if (\$service->validateApiKey()) {
    echo '✅ OpenAI API key validated successfully' . PHP_EOL;
} else {
    echo '❌ OpenAI API key validation failed' . PHP_EOL;
}
"

# Check translation_logs table exists
php artisan tinker --execute="
\$count = \App\Models\TranslationLog::count();
echo '✅ translation_logs table exists (' . \$count . ' records)' . PHP_EOL;
"

# Check tour_translations.content_json column exists
php artisan tinker --execute="
\$columns = \Illuminate\Support\Facades\Schema::getColumnListing('tour_translations');
if (in_array('highlights_json', \$columns)) {
    echo '✅ content_json columns added successfully' . PHP_EOL;
} else {
    echo '❌ content_json columns missing' . PHP_EOL;
}
"
```

### Step 7: Test AI Translation Feature
```bash
# Test via admin panel:
# 1. Login to /admin
# 2. Go to Tours
# 3. Select any tour with English translation
# 4. Go to Translations tab
# 5. Create new translation record (e.g., Uzbek)
# 6. Click "🤖 AI Translate" button
# 7. Wait 30-60 seconds
# 8. Verify translation completed successfully
# 9. Check translation_logs table for cost tracking
```

---

## Post-Deployment Verification

### Checklist

#### Database ✅
- [ ] Migrations completed: `php artisan migrate:status`
- [ ] translation_logs table exists
- [ ] tour_translations.content_json columns exist

#### Configuration ✅
- [ ] OpenAI API key configured in Settings
- [ ] API key validated successfully
- [ ] Model set to GPT-4 Turbo

#### Admin Panel ✅
- [ ] Settings → AI Translation page accessible
- [ ] Usage statistics displaying (0 translations initially)
- [ ] Test API Key button working
- [ ] Tours → Translations tab shows "🤖 AI Translate" button

#### Frontend ✅
- [ ] Russian tour pages displaying correctly
- [ ] No mixed-language content
- [ ] Mobile section tabs translated
- [ ] All tour sections using translation priority

#### Bug Fixes ✅
- [ ] Mobile tabs show translated labels (not raw keys)
- [ ] FAQ section no mixed languages
- [ ] Requirements section no mixed languages
- [ ] Meeting instructions translation working

#### AI Translation ✅
- [ ] Test translation completed successfully
- [ ] HTML preserved in translated content
- [ ] translation_logs record created
- [ ] Cost tracking accurate

---

## Rollback Plan (If Needed)

### Emergency Rollback
```bash
# Revert to previous commit
git log -5 --oneline  # Find previous commit
git reset --hard <previous-commit-hash>

# Rollback migrations
php artisan migrate:rollback --step=2

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Production Deployment (After Staging Verification)

### Prerequisites
- [ ] Staging testing complete (all checkboxes above ✅)
- [ ] User acceptance testing passed
- [ ] OpenAI API key has sufficient credits
- [ ] Production database backup created

### Steps
Same as staging deployment above, but on production server:
1. Pull code to production
2. Install dependencies
3. Run migrations
4. Configure API key
5. Clear caches
6. Verify functionality

---

## Monitoring

### What to Monitor

#### Daily (First Week)
- Translation usage (Settings → AI Translation)
- Cost tracking (should be <$10/day)
- Error logs: `tail -f storage/logs/laravel.log | grep Translation`
- translation_logs table for failed translations

#### Weekly
- Monthly cost trends
- Translation quality (manual review sample)
- User feedback on translations

#### Monthly
- Total translations count
- Total cost vs budget ($100/month)
- Most translated tours
- Average translation time

### Alerts to Set

- Daily cost exceeds $8 USD (80% of $10 limit)
- Monthly cost exceeds $80 USD (80% of $100 limit)
- Failed translation count >5 per day
- API rate limit exceeded

---

## Known Limitations

1. **English Source Only** - Currently only translates from English
2. **No Batch Mode** - Must translate tours one-by-one
3. **No Preview** - Translation saves immediately
4. **Rate Limits** - 10/hour, 50/day (configured)

---

## Documentation Links

- **Feature Guide:** `/AI_TRANSLATION_FEATURE.md`
- **Implementation Summary:** `/PHASE2_COMPLETE_SUMMARY.md`
- **Bug Fixes:** `/ALL_TRANSLATION_BUGS_FIXED.md`
- **Test Results:** `/CERAMICS_TOUR_RUSSIAN_TRANSLATION.md`

---

## Support Contacts

**Technical Issues:**
- Check translation_logs table for errors
- Review Laravel logs: `storage/logs/laravel.log`
- Verify OpenAI API credits: https://platform.openai.com/usage

**OpenAI API Issues:**
- API status: https://status.openai.com/
- Rate limits: 10/hour, 50/day (configurable)
- Cost calculator: ~$0.16/tour (GPT-4 Turbo)

---

## Success Criteria

Deployment is successful when:
- ✅ All migrations run without errors
- ✅ OpenAI API key validated
- ✅ Test AI translation completes successfully
- ✅ Frontend shows translated content correctly
- ✅ No mixed-language bugs
- ✅ Cost tracking functional
- ✅ translation_logs recording usage

---

**Deployment Date:** _________________
**Deployed By:** _________________
**Verified By:** _________________
**Status:** ⬜ Pending | ⬜ In Progress | ⬜ Complete | ⬜ Rollback

**Notes:**
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________
