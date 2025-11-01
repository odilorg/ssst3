# Known Issues - SSST3 Project

**Last Updated:** November 1, 2025

---

## 🐛 Active Issues

### Issue #1: ImportLeads Wizard Page - Filament 4 Compatibility

**Status:** 🔴 BLOCKED
**Priority:** Medium
**Affected File:** `app/Filament/Pages/ImportLeads.php` (currently disabled)

**Description:**
The multi-step wizard CSV import page uses `Filament\Forms\Form` which has compatibility issues with Filament 4 Schema system used in this project.

**Error:**
```
Could not check compatibility between form methods - Filament\Forms\Form is not available
```

**Root Cause:**
- Filament 4 in this project uses `Filament\Schemas\Schema` for Resources
- Custom Pages with complex forms (Wizard) need different implementation
- The Wizard component integration needs to be researched for Filament 4

**Temporary Workaround:**
File has been renamed to `ImportLeads.php.disabled` to allow application to load.

**Impact:**
- Users cannot use the wizard-based CSV import UI
- Import History resource still works
- LeadImport model and LeadsImport class are functional
- Direct programmatic imports work fine

**Solution Options:**

1. **Rewrite with Livewire Components** (Recommended)
   - Create custom Livewire component for file upload
   - Build step-by-step UI without Filament Wizard
   - Use standard Livewire file uploads
   - Estimated time: 2-3 hours

2. **Research Filament 4 Custom Page Forms**
   - Find correct pattern for Pages with forms in Filament 4
   - May need to use different traits/interfaces
   - Check if Wizard is supported in Pages
   - Estimated time: 1-2 hours research + implementation

3. **Simplify to Basic Upload**
   - Remove wizard, create single-page upload
   - Auto-detect field mapping (no user customization)
   - Quick solution but less user-friendly
   - Estimated time: 1 hour

**Recommended Action:**
Option 1 (Livewire) - provides best UX and avoids Filament form complexities.

**Related Files:**
- `app/Filament/Pages/ImportLeads.php.disabled` - The problematic page
- `app/Imports/LeadsImport.php` - Import logic (works fine)
- `app/Models/LeadImport.php` - Model (works fine)
- `app/Filament/Resources/LeadImports/LeadImportResource.php` - History view (works fine)

**Workaround for Users:**
Until fixed, users can:
1. Use programmatic import via tinker
2. View import history in Admin → Lead Management → Import History
3. Manual lead entry still works

---

## ✅ Resolved Issues

### Issue #1: Navigation Group Type Error
**Fixed:** October 23, 2025
**Solution:** Changed from static properties to methods (getNavigationGroup(), etc.)

### Issue #2: Form vs Schema Incompatibility
**Fixed:** October 23, 2025
**Solution:** Updated LeadImportResource to use Schema instead of Form

### Issue #4: Claude Code "Stop Hook Error" Message
**Fixed:** November 1, 2025
**Priority:** 🟡 Medium
**Affected Component:** Claude Code hooks in `~/.claude/settings.json`

**Description:**
"Stop hook error" message was appearing when ending Claude Code sessions.

**Root Cause:**
Hooks were failing because they didn't:
- Check if directories exist before accessing them
- Exit cleanly with `exit 0`
- Handle errors gracefully

**Solution:**
Updated all hooks in `~/.claude/settings.json` to:
```bash
# Before (failing)
ls /path/*.json 2>/dev/null | wc -l

# After (robust)
INBOX_DIR="/path";
if [ -d "$INBOX_DIR" ]; then
  ls "$INBOX_DIR"/*.json 2>/dev/null | wc -l;
fi;
exit 0
```

**Verification:**
```bash
bash -c 'INBOX_DIR="/c/Users/Admin/.swarm/agents/agent1/inbox"; if [ -d "$INBOX_DIR" ]; then task_count=$(ls "$INBOX_DIR"/*.json 2>/dev/null | wc -l); if [ "$task_count" -gt 0 ]; then echo "OK"; fi; fi; exit 0'
```

**Documentation:** See `HOOK_ERROR_FIX.md` for complete details and prevention guidelines.

---

### Issue #3: Images Not Loading in Filament Edit Pages
**Fixed:** November 1, 2025
**Priority:** 🔴 High
**Affected Components:** BlogPostResource, TourResource (FileUpload fields)

**Description:**
Uploaded images were not displaying in Filament edit pages. When editing a blog post or tour, the FileUpload component showed broken/missing image previews.

**Root Cause:**
1. `APP_URL` in `.env` was set to `http://localhost` but Laravel server runs on `http://127.0.0.1:8000`
2. This caused incorrect URL generation for image assets
3. Some FileUpload fields were missing `visibility('public')` parameter

**Error Symptoms:**
- Broken image previews in Filament admin edit pages
- 404 errors for image URLs pointing to wrong host
- URL mismatch: `http://localhost/storage/...` vs `http://127.0.0.1:8000/storage/...`

**Solution:**
```diff
# .env file
- APP_URL=http://localhost
+ APP_URL=http://127.0.0.1:8000
```

```php
// TourForm.php - Added visibility('public')
FileUpload::make('hero_image')
    ->disk('public')
+   ->visibility('public')  // ← Added
    ->directory('tours/heroes')

FileUpload::make('path') // Gallery images
    ->disk('public')
+   ->visibility('public')  // ← Added
    ->directory('tours/gallery')
```

**Verification Steps:**
1. Check storage symlink exists: `ls -la public/storage`
2. Verify APP_URL: `php artisan tinker --execute="echo config('app.url');"`
3. Test image URL: `Storage::disk('public')->url('test.jpg')`
4. Clear caches: `php artisan config:clear && php artisan cache:clear`

**Affected Files:**
- `.env` - Updated APP_URL
- `app/Filament/Resources/Tours/Schemas/TourForm.php` - Added visibility()
- `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php` - Already had visibility()

**Impact:**
All FileUpload image previews now work correctly in Filament admin panel.

**Prevention:**
- Always set `visibility('public')` on public FileUpload fields
- Ensure APP_URL matches actual server URL in development
- Run `php artisan storage:link` after cloning project

---

### Issue #5: Blog Infinite JavaScript Loop and 500 Error
**Fixed:** November 1, 2025
**Priority:** 🔴 CRITICAL
**Affected Components:** BlogController, blog-article.html, HTMX integration

**Description:**
Blog pages were experiencing an infinite JavaScript loop with "Identifier 'nav' has already been declared" errors repeating hundreds of times. The console showed VM scripts being executed repeatedly, causing browser performance issues.

**Root Cause:**
Two interconnected problems caused this critical bug:

1. **Database Column Mismatch (Primary)**
   - `BlogController::content()` line 45 called `increment('views')`
   - Actual database column name is `view_count`, not `views`
   - This caused SQLSTATE[42S22] 500 Internal Server Error
   - HTMX received Laravel's error page HTML (with embedded CSS/JS) instead of blog partial
   - HTMX injected this error HTML into the page, executing embedded scripts
   - Scripts ran in infinite loop because error HTML was repeatedly injected

2. **HTMX Initialization Race Condition (Secondary)**
   - HTMX library loaded before URLs were set
   - `hx-get=""` attributes were empty when HTMX initialized
   - `hx-trigger="load"` (without `once`) could retrigger on DOM events
   - Execution order: HTMX loaded → DOMContentLoaded → URLs set (too late)

**Error Symptoms:**
```javascript
VM361 main.js:1 Uncaught SyntaxError: Identifier 'nav' has already been declared
VM362 blog-article.js:1 Uncaught SyntaxError: Identifier 'commentForm' has already been declared
// Repeated 100+ times with incrementing VM numbers
```

```
GET http://127.0.0.1:8000/partials/blog/.../content 500 (Internal Server Error)
```

**Console Pattern:**
- "Blog slug detected" logging hundreds of times (VM368-VM630+)
- Scripts being evaluated as VMxxx (dynamically evaluated code)
- Entire HTML page being re-executed infinitely

**Solution:**

1. **Fixed Database Column Name** (`BlogController.php:45`)
```php
// Before (WRONG - column doesn't exist)
BlogPost::where('id', $post->id)->increment('views');

// After (CORRECT)
BlogPost::where('id', $post->id)->increment('view_count');
```

2. **Fixed HTMX Initialization Order** (`blog-article.html:361-404`)
```javascript
// Before - Waited for DOMContentLoaded (too late)
document.addEventListener('DOMContentLoaded', function() {
  // Set HTMX URLs...
});

// After - Set URLs IMMEDIATELY before HTMX loads
(function() {
  if (window.BLOG_HTMX_INITIALIZED) return;
  window.BLOG_HTMX_INITIALIZED = true;

  // Set all HTMX URLs synchronously
  const heroSection = document.querySelector('[data-blog-section="hero"]');
  heroSection.setAttribute('hx-get', `${window.BACKEND_URL}/partials/blog/${window.BLOG_SLUG}/hero`);
  // ... set other URLs
})();
```

3. **Added `once` Modifier to HTMX Triggers**
```html
<!-- Before -->
<div hx-get="" hx-trigger="load" hx-swap="innerHTML">

<!-- After -->
<div hx-get="" hx-trigger="load once" hx-swap="innerHTML">
```

4. **Synced Files Between Projects**
   - Frontend: `D:\xampp82\htdocs\jahongir-custom-website\`
   - Laravel: `D:\xampp82\htdocs\ssst3\public\`
   - User was loading from Laravel server but files were outdated (Oct 31)

**Correct Execution Order (After Fix):**
1. `BLOG_SLUG` set from URL parameter
2. HTMX URLs set immediately (synchronous execution)
3. HTMX library loads (`htmx.min.js`)
4. HTMX triggers once per element
5. Main JavaScript files load with `defer`

**Affected Files:**
- `app/Http/Controllers/Partials/BlogController.php` - Fixed column name
- `public/blog-article.html` - Fixed HTMX initialization order
- `public/blog-article.js` - Added defer attribute
- `public/js/main.js` - Already had IIFE wrapper

**Commits:**
- Backend: `ca5af71` (ssst3 repo, branch: feature/tour-listing-load-more)
- Frontend: `6f43738` (jahongir-custom-website repo, branch: polish-single-pages)

**Impact:**
Blog pages now load correctly without JavaScript errors. Performance is normal, no infinite loops.

**Prevention Guidelines:**
1. Always verify database column names match Eloquent calls
2. Set HTMX attributes BEFORE loading htmx.min.js
3. Use `hx-trigger="load once"` to prevent re-triggering
4. Keep frontend and Laravel public folder files in sync
5. Check Laravel logs for 500 errors when debugging HTMX issues
6. Remember: HTMX receiving error HTML can cause infinite script execution

**Testing:**
```bash
# Test endpoint returns 200 OK
curl -I http://127.0.0.1:8000/partials/blog/{slug}/content

# Should return HTTP/1.1 200 OK, not 500
```

**Related Issues:**
- Similar pattern could occur with tour details if endpoints return 500 errors
- Always check backend endpoint status when HTMX content fails to load

---

## 📝 Notes for Developers

### Filament 4 Patterns in This Project

**Resources:**
```php
public static function form(Schema $schema): Schema
{
    return $schema->components([...]);
}
```

**Custom Pages:**
- Research needed for forms
- May need `InteractsWithForms` trait
- Or use plain Livewire without Filament forms

**Icons:**
```php
protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIconName;
```

**Navigation:**
```php
public static function getNavigationGroup(): ?string { return 'Group Name'; }
public static function getNavigationSort(): ?int { return 1; }
```

---

## 🔍 How to Report New Issues

1. Add to this file under "Active Issues"
2. Include: Status, Priority, Description, Error, Solution options
3. Update PROJECT_COORDINATION.md if it blocks development
4. Tag with priority: 🔴 High, 🟡 Medium, 🟢 Low

---

**Maintainer:** Development Team
**File:** `KNOWN_ISSUES.md`
