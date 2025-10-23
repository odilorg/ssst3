# CI/CD Workflows

This directory contains GitHub Actions workflows for automated testing and deployment.

## 📋 Available Workflows

### 1. `ci.yml` - Continuous Integration (Automated)

**Triggers:** Automatically runs on every push and pull request

**What it does:**
- ✅ Runs all tests (`php artisan test`)
- ✅ Checks code syntax
- ✅ Runs database migrations in test environment
- ✅ Verifies code quality

**When it runs:**
- Every push to `main`, `develop`, or `feature/*` branches
- Every pull request to `main` or `develop`

**Status:** You can see test results in the "Actions" tab on GitHub

---

### 2. `deploy-production.yml` - Production Deployment (Manual)

**Triggers:** Manual only (for safety)

**What it does:**
- 🚀 Connects to production server via SSH
- 📥 Pulls latest code from `main` branch
- 📦 Installs composer dependencies
- 🗄️ Runs database migrations
- 🧹 Clears all caches
- 🔄 Restarts queue workers

**How to use:**
1. Go to GitHub → Actions → "Deploy to Production"
2. Click "Run workflow"
3. Type `deploy` to confirm
4. Click "Run workflow" button
5. Watch the deployment progress

---

## 🔧 Setup Required

### For CI (Testing) - Already works!

No setup needed! Just push your code and tests will run automatically.

### For CD (Deployment) - Requires secrets

You need to add these secrets to GitHub:

**Go to:** Settings → Secrets and variables → Actions → New repository secret

1. **PROD_HOST**
   - Name: `PROD_HOST`
   - Value: Your server IP or domain (e.g., `123.45.67.89` or `tour.example.com`)

2. **PROD_USERNAME**
   - Name: `PROD_USERNAME`
   - Value: SSH username (e.g., `root` or `ubuntu`)

3. **PROD_SSH_KEY**
   - Name: `PROD_SSH_KEY`
   - Value: Your private SSH key content (from `~/.ssh/id_ed25519`)
   - Get it with: `cat ~/.ssh/id_ed25519`

---

## 📊 Current Status

- ✅ CI workflow configured
- ⚠️ Only 2 tests exist - need to write more tests!
- ⚠️ CD workflow configured but needs secrets

---

## 🎯 Next Steps

### Phase 1: Improve Testing (DO THIS FIRST!)

Your app has critical business logic but only 2 tests. Add tests for:

1. **Booking Creation** - Ensure bookings are created correctly
2. **Pricing Calculation** - Test transport pricing fallback logic
3. **Contract Pricing** - Verify contract prices override base prices
4. **Payment Processing** - Test payment calculations
5. **Authorization** - Ensure users can only access their own data

**Example test to create:**

```php
// tests/Feature/TransportPricingTest.php
public function test_transport_uses_instance_price_over_type_price()
{
    $transport = Transport::factory()->create([
        'transport_type_id' => $sedan->id,
    ]);

    $instancePrice = TransportInstancePrice::create([
        'transport_id' => $transport->id,
        'price_type' => 'per_day',
        'cost' => 150, // Override type price
    ]);

    $service = new PricingService();
    $price = $service->getPrice('App\Models\Transport', $transport->id, $instancePrice->id);

    $this->assertEquals(150, $price);
}
```

### Phase 2: Set Up Deployment Secrets

Follow the setup instructions above to enable automated deployments.

### Phase 3: Add More Workflows (Optional)

- **Staging deployment** - Deploy to test server first
- **Database backups** - Backup before deployment
- **Slack notifications** - Get notified of deployments
- **Code coverage** - Track test coverage over time

---

## 🐛 Troubleshooting

**Tests failing?**
- Check the Actions tab for error details
- Run tests locally: `php artisan test`
- Fix errors before pushing

**Deployment failing?**
- Check SSH connection: `ssh user@server`
- Verify secrets are set correctly
- Check server permissions

**Need help?**
- Check workflow logs in Actions tab
- Look for red ❌ marks for failed steps

---

## 🚀 Benefits

**With CI:**
- ✅ Catch bugs before production
- ✅ Prevent breaking changes
- ✅ Code quality assurance
- ✅ Safe merges

**With CD:**
- ✅ One-click deployments
- ✅ Consistent deployment process
- ✅ No forgotten migration steps
- ✅ Automatic cache clearing

---

**Remember:** The CI workflow is already active! Just push your code and it will run automatically.
