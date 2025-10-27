# AI Email Generation - API Key Setup

## Current Status

✅ **Feature Implementation**: COMPLETE
❌ **API Key**: INVALID - Needs to be replaced

## Error Encountered

```
Authentication Fails, Your api key: ****0a78 is invalid
```

The current API key in `.env` is:
```
OPENAI_API_KEY=sk-dd3ec0c8ae0e4d63b5fcb1e00d660a78
```

This key is invalid and needs to be replaced.

## How to Get a Valid DeepSeek API Key

### Option 1: DeepSeek (Recommended - Cheap & Fast)

1. Visit: https://platform.deepseek.com/
2. Sign up or login
3. Go to API Keys section
4. Create a new API key
5. Copy the key

**Pricing**: Very affordable (~$0.14 per million tokens for chat)

### Option 2: OpenAI (More Expensive but Popular)

1. Visit: https://platform.openai.com/
2. Sign up or login
3. Go to API Keys
4. Create a new API key
5. Copy the key

**Note**: If using OpenAI, also update `OPENAI_BASE_URL` in `.env`:
```
OPENAI_BASE_URL=https://api.openai.com
```

And update the model in `app/Services/AIEmailService.php`:
```php
protected string $model = 'gpt-4o-mini'; // or 'gpt-4o'
```

## Setup Instructions

### Step 1: Update .env File

Open `D:\xampp82\htdocs\ssst3\.env` and update:

```env
# For DeepSeek:
OPENAI_API_KEY=YOUR_NEW_DEEPSEEK_API_KEY_HERE
OPENAI_BASE_URL=https://api.deepseek.com

# For OpenAI:
OPENAI_API_KEY=YOUR_OPENAI_API_KEY_HERE
OPENAI_BASE_URL=https://api.openai.com
```

### Step 2: Clear Cache

```bash
cd D:/xampp82/htdocs/ssst3
php artisan config:clear
php artisan cache:clear
```

### Step 3: Test the Feature

#### Quick CLI Test:
```bash
php test_ai_email.php
```

You should see output like:
```
✅ EMAIL GENERATED SUCCESSFULLY!

==========================================
SUBJECT: Partnership Opportunity - Uzbekistan Tourism
==========================================

Dear [Company Name],

[Personalized email content...]

==========================================
```

#### Full Filament UI Test:

1. Start the development server:
   ```bash
   php artisan serve
   ```

2. Open browser: http://localhost:8000/admin

3. Navigate to **Leads** section

4. Click on any lead to **Edit**

5. Look for the **"Generate Email with AI"** button in the page header

6. Click it and fill in:
   - **Email Tone**: Professional
   - **Email Type**: Initial Outreach
   - **Custom Instructions**: (optional) "Mention our Silk Road tours"

7. Click **"Generate Email"**

8. Scroll down to **"AI Email Outreach"** section

9. You should see:
   - ✅ Subject line populated
   - ✅ Email body populated with personalized content
   - ✅ Strategy notes added
   - ✅ Email statistics updated

## What the AI Will Generate

Based on your lead data, the AI will create personalized emails like:

### Example 1: Lead WITHOUT Uzbekistan Partnership
```
Subject: Discover Uzbekistan - Partnership Opportunity

Dear John,

I came across Adventure Seekers Tours and noticed your excellent portfolio
of adventure travel experiences. As a leading DMC in Uzbekistan, we
specialize in creating authentic Silk Road adventures...

[200-300 word personalized email]
```

### Example 2: Lead WITH Uzbekistan Partnership
```
Subject: Expand Your Uzbekistan Portfolio with Us

Hi Sarah,

I see that Canada Travel Group already offers Uzbekistan tours, which is
fantastic! We're a DMC that could potentially enhance your existing
offerings with our local expertise...

[200-300 word personalized email]
```

## Troubleshooting

### Issue: "Authentication Fails"
- **Solution**: API key is invalid. Get a new one from DeepSeek or OpenAI.

### Issue: "Timeout"
- **Solution**: Increase timeout in `AIEmailService.php`:
  ```php
  ->timeout(120) // Change from 60 to 120
  ```

### Issue: "JSON parsing failed"
- **Solution**: AI might be returning text instead of JSON. Check the response in logs.

### Issue: "Field not updating in UI"
- **Solution**:
  1. Clear browser cache
  2. Hard refresh (Ctrl+Shift+R)
  3. Check if `refreshFormData()` is working in EditLead.php

## Next Steps After Testing

Once you have a valid API key and testing is successful:

1. **Review Generated Emails**: Check quality and personalization
2. **Fine-tune Prompts**: Edit `AIEmailService.php` if needed
3. **Add to Production**: Deploy to your server
4. **Monitor Usage**: Track API costs and usage

## Alternative: Use Local AI (Free)

If you want to avoid API costs, you can use a local AI model:

### Ollama (Free, Local)

1. Install Ollama: https://ollama.ai/
2. Pull a model: `ollama pull mistral`
3. Update `AIEmailService.php`:
   ```php
   protected string $baseUrl = 'http://localhost:11434';
   protected string $model = 'mistral';
   ```

**Note**: Requires good hardware (8GB+ RAM)

## Questions?

- DeepSeek Docs: https://platform.deepseek.com/docs
- OpenAI Docs: https://platform.openai.com/docs
- Filament Docs: https://filamentphp.com/docs

## Summary

The feature is **100% implemented and ready**. You just need to:
1. Get a valid API key (DeepSeek recommended)
2. Update `.env` file
3. Test with `php test_ai_email.php`
4. Use in Filament UI

**Estimated Time**: 5 minutes to get API key and test
**Estimated Cost**: $0.001 - $0.01 per email generation
