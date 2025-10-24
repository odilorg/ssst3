# CI/CD Setup Guide - SSST3 Tour Management

## 📋 Table of Contents
- [What is CI/CD?](#what-is-cicd)
- [What We Set Up](#what-we-set-up)
- [How to Use It](#how-to-use-it)
- [Next Steps](#next-steps)
- [Troubleshooting](#troubleshooting)

---

## 🤔 What is CI/CD?

### **CI = Continuous Integration**
Think of it as an automatic quality checker for your code.

**Without CI:**
```
You: "I'll push this code, it should work..."
(2 hours later in production)
Customer: "The booking system is broken! 😱"
You: "Oh no, I forgot to test that!" 🤦
```

**With CI:**
```
You: Push code to GitHub
Robot: "Running tests... ❌ FAILED! Pricing calculation is broken!"
You: "Thanks robot, I'll fix it before deploying"
Customer: Happy, never knew there was a bug! 😊
```

### **CD = Continuous Deployment**
Think of it as an automatic deployment assistant.

**Without CD:**
```
Manual deployment checklist:
☐ SSH to server
☐ Pull code
☐ Run composer install
☐ Run migrations (don't forget this!)
☐ Clear cache
☐ Restart queue workers
☐ Oh no, forgot to clear cache! 🤦
☐ Go back and clear cache
☐ 30 minutes wasted
```

**With CD:**
```
Click "Deploy" button →
Robot does everything perfectly in 2 minutes ✅
```

---

## ✅ What We Set Up

### **1. Automated Testing (CI)**

**Location:** `.github/workflows/ci.yml`

**Triggers automatically on:**
- Every push to `main`, `develop`, or `feature/*` branches
- Every pull request

**What it does:**
```bash
1. Creates fresh test database
2. Installs your code
3. Runs migrations
4. Runs all tests
5. Shows ✅ or ❌ on GitHub
```

**View results:**
👉 https://github.com/odilorg/ssst3/actions

**Example:**
![CI Workflow](https://i.imgur.com/example.png)
- ✅ Green checkmark = All tests passed, safe to merge
- ❌ Red X = Tests failed, fix before merging

---

### **2. Production Deployment (CD)**

**Location:** `.github/workflows/deploy-production.yml`

**Triggers:** Manually (for safety!)

**What it does:**
```bash
1. Connects to /var/www/tour_app via SSH
2. git pull origin main
3. composer install --no-dev --optimize-autoloader
4. php artisan migrate --force
5. php artisan config:cache
6. php artisan route:cache
7. php artisan view:cache
8. php artisan queue:restart
9. ✅ Done!
```

**Time saved:** ~15 minutes per deployment

---

### **3. Tests We Created**

**Location:** `tests/Feature/TransportPricingTest.php`

**What we test:**
1. ✅ Instance prices override type prices (VIP pricing)
2. ✅ Type prices used as fallback (standard pricing)
3. ✅ New transports auto-copy prices (observer)

**Why this matters:**
Your transport pricing is critical business logic. These tests ensure:
- VIP vehicles keep their premium pricing
- Standard vehicles get default pricing
- New vehicles automatically get prices (no manual work)

---

## 🚀 How to Use It

### **Using CI (Automatic Testing)**

**It's already working!** Just push your code:

```bash
git add .
git commit -m "feat: add new feature"
git push origin feature/new-feature
```

GitHub automatically:
1. Runs all tests
2. Shows results in pull request
3. Blocks merge if tests fail (optional, can configure)

**Check results:**
1. Go to your pull request on GitHub
2. Scroll down to "Checks"
3. See ✅ or ❌ next to "CI - Tests & Code Quality"
4. Click "Details" to see what failed

---

### **Using CD (Manual Deployment)**

**⚠️ SETUP REQUIRED FIRST:**

#### Step 1: Add GitHub Secrets

Go to: https://github.com/odilorg/ssst3/settings/secrets/actions

Click "New repository secret" and add these 4 secrets:

**1. PROD_HOST**
```
Name: PROD_HOST
Value: YOUR_SERVER_IP or tour.example.com
```

**2. PROD_PORT**
```
Name: PROD_PORT
Value: 2222 (or 22 for default port)
```

**3. PROD_USERNAME**
```
Name: PROD_USERNAME
Value: root (or ubuntu, or your SSH username)
```

**4. PROD_SSH_KEY**
```
Name: PROD_SSH_KEY
Value: (your private SSH key content)

To get your key:
cat ~/.ssh/id_ed25519_new

Copy the ENTIRE output including:
-----BEGIN OPENSSH PRIVATE KEY-----
... (all the lines)
-----END OPENSSH PRIVATE KEY-----
```

#### Step 2: Deploy to Production

**After merging to `main` branch:**

1. Go to: https://github.com/odilorg/ssst3/actions
2. Click "Deploy to Production" workflow
3. Click "Run workflow" button
4. Type `deploy` in the confirmation box
5. Click green "Run workflow" button
6. Watch it deploy! 🚀

**Timeline:**
```
0:00 - Connecting to server...
0:10 - Pulling latest code...
0:30 - Installing dependencies...
1:00 - Running migrations...
1:30 - Clearing caches...
2:00 - ✅ Deployment complete!
```

---

## 📊 Current Status

### ✅ What's Working
- CI workflow configured and active
- Tests created for transport pricing
- Factories created for test data
- CD workflow configured (needs secrets)
- Documentation complete

### ⚠️ What Needs Attention

**1. Fix Migration Issue**
```
Error: contracts migration trying to drop non-existent column
Impact: Tests fail (but production is fine)
Fix: Clean up migrations or use MySQL for tests
```

**2. Add More Tests** (Critical!)
```
Current: 5 tests total
Need: 50+ tests for critical features

Priority tests to add:
- Booking creation and validation
- Payment calculations
- Contract pricing override
- User authorization
- Email sending
- Lead management
```

**3. Set Up Deployment Secrets**
Follow "Using CD" section above to enable deployments.

---

## 🎯 Next Steps

### **Week 1: Fix Tests**

**Priority 1: Fix migration issue**
```bash
# Option A: Use MySQL for tests (better)
Edit: phpunit.xml
Change: DB_CONNECTION=mysql

# Option B: Fix migrations
Check all migrations for:
- Dropping non-existent columns
- Foreign key issues
```

**Priority 2: Run tests locally**
```bash
cd /mnt/d/xampp82/htdocs/ssst3
php artisan test

# All tests should pass ✅
```

---

### **Week 2: Add Critical Tests**

**Booking Tests:**
```php
// tests/Feature/BookingTest.php
test_can_create_booking()
test_cannot_create_booking_with_invalid_dates()
test_booking_calculates_correct_total()
test_booking_requires_authentication()
```

**Pricing Tests:**
```php
// tests/Feature/PricingTest.php
test_contract_pricing_overrides_base_pricing()
test_seasonal_pricing_works()
test_pricing_breakdown_calculation()
```

**Goal:** 20+ tests covering critical business logic

---

### **Week 3: Enable Automated Deployment**

1. Add GitHub secrets (see "Using CD" section)
2. Do a test deployment to staging (if you have one)
3. Deploy to production using workflow
4. Celebrate! 🎉

---

### **Month 2: Advanced Setup**

**Add staging environment:**
```yaml
# .github/workflows/deploy-staging.yml
- Deploys to /var/www/tour_app_staging
- Automatic deployment on merge to develop
- Test before production
```

**Add test coverage:**
```yaml
# .github/workflows/ci.yml
- Track code coverage %
- Require 80% coverage
- Show coverage badge in README
```

**Add notifications:**
```yaml
# Slack or email notifications
- Notify on deployment
- Notify on test failures
- Notify on production errors
```

---

## 🐛 Troubleshooting

### **Tests Failing on GitHub but Pass Locally**

**Cause:** Different PHP versions or databases

**Fix:**
```yaml
# Match local environment in ci.yml
php-version: 8.2  # Your XAMPP PHP version
```

---

### **Deployment Failing with "Permission Denied"**

**Cause:** SSH key not added to server

**Fix:**
```bash
# On your server (/var/www/tour_app):
# Add GitHub Actions SSH key to authorized_keys
```

---

### **Migrations Failing During Deployment**

**Cause:** Production database is out of sync

**Fix:**
```bash
# SSH to production
cd /var/www/tour_app
php artisan migrate:status  # Check status
php artisan migrate --force # Run manually first time
```

---

### **Tests Timing Out**

**Cause:** Tests taking too long (factories, API calls)

**Fix:**
```yaml
# Increase timeout in ci.yml
timeout-minutes: 15  # Default is 10
```

---

## 📈 Benefits You'll See

### **Week 1:**
- ✅ Catch bugs before production
- ✅ Confidence in code changes
- ✅ Visible test status on GitHub

### **Month 1:**
- ✅ 50% faster deployments
- ✅ Zero forgotten migration steps
- ✅ Consistent deployment process
- ✅ 20+ automated tests

### **Month 3:**
- ✅ Deploy 5x per day with confidence
- ✅ 100+ tests covering all features
- ✅ Zero production bugs from untested code
- ✅ Team can deploy without fear

---

## 📊 Success Metrics

Track these over time:

```
Deployments per week:
Before CI/CD: 1-2
After CI/CD: 10+

Time per deployment:
Before: 30 minutes
After: 2 minutes

Production bugs:
Before: 2-3 per week
After: 0-1 per month

Developer confidence:
Before: 😰
After: 😎
```

---

## 🎓 Learning Resources

**GitHub Actions:**
- https://docs.github.com/en/actions
- https://laracasts.com/series/github-actions-for-laravel

**Laravel Testing:**
- https://laravel.com/docs/testing
- https://laracasts.com/series/testing-laravel

**CI/CD Best Practices:**
- https://martinfowler.com/articles/continuousIntegration.html

---

## 🤝 Support

**Questions about CI/CD?**
- Check workflow logs: https://github.com/odilorg/ssst3/actions
- Read this guide: `.github/workflows/README.md`
- Check Laravel docs: https://laravel.com/docs/testing

**Common issues:**
- Tests failing → Check Actions tab for details
- Deployment failing → Check SSH connection
- Secrets not working → Re-add them in Settings

---

## ✅ Checklist

Use this checklist to track your CI/CD setup:

**Week 1:**
- [ ] Fix migration issue with tests
- [ ] Run `php artisan test` locally (all pass)
- [ ] Verify CI runs on GitHub Actions
- [ ] Create 5 more tests for bookings

**Week 2:**
- [ ] Add GitHub deployment secrets
- [ ] Test manual deployment
- [ ] Create 10 more tests
- [ ] Document deployment process

**Week 3:**
- [ ] Use CI/CD for feature branch
- [ ] Deploy to production via workflow
- [ ] Train team on CI/CD usage
- [ ] Celebrate success! 🎉

---

**Remember:** CI/CD is a journey, not a destination. Start simple, improve over time!

---

**Created:** October 23, 2025
**Last Updated:** October 23, 2025
**Version:** 1.0
