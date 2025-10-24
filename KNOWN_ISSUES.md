# Known Issues - SSST3 Project

**Last Updated:** October 23, 2025

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
