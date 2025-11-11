# Admin Page Consolidation - Administrator Only Access

**Date:** November 11, 2025

---

## 🎯 Changes Summary

Consolidated two separate admin pages into **one comprehensive settings page** that is **restricted to WordPress Administrators only**.

---

## 📋 What Changed

### Before (Version 1.3)
- **Two separate menu items:**
  1. "Debug Report" - Basic event metadata viewer
  2. "Settings & Debug" - Cache settings and timestamps
- Both required `manage_options` capability (accessible to Editors and above)

### After (Current)
- **One unified menu item:**
  - "Settings & Debug" - Complete admin interface
- Requires **Administrator role only** (not accessible to Editors)

---

## 🔐 Administrator-Only Security

### Menu Registration
```php
add_submenu_page(
    'edit.php?post_type=events',
    'Events Calendar Settings',
    'Settings & Debug',
    'administrator',  // ← Administrator only
    'yak-events-settings',
    'yak_render_settings_page'
);
```

### Additional Checks
1. **Menu visibility check** - Page won't appear in menu for non-administrators
2. **Render function check** - Double-checks capability before displaying content
3. **Shortcode check** - Front-end shortcode also requires administrator role

---

## 📊 Unified Page Structure

The consolidated page now includes **4 major sections**:

### 1. ⚙️ Cache Settings
- Configure timestamp recalculation interval (1-24 hours)
- View last recalculation time and next scheduled time
- Manual "Force Recalculation Now" button
- Save settings form

### 2. 📊 Event Timestamps
- Table showing current timestamp for each event
- Event type (MULTI-SESSION or SINGLE)
- Unix timestamp and human-readable format
- Status indicators (🟢 UPCOMING / 🔴 PAST)
- Expandable session details for multi-session events

### 3. 📋 Activity Log
- Last 100 system operations
- Event saves, recalculations, completions
- Expandable context details
- Performance metrics (duration, counts)
- Clear log button

### 4. 📝 Complete Event Metadata Report
- **NEW:** Full debug report now integrated
- All event data in one comprehensive table
- Date/time information
- Location and organizer details
- Session counts
- Featured status and categories
- Action button details
- Visual color coding (past/upcoming/featured)

---

## 🚫 What Was Removed

### Removed Menu Item
- ❌ "Debug Report" (separate menu item)

### Removed Functions
- ❌ `yak_events_debug_menu()` - No longer needed
- ❌ `yak_events_debug_page()` - No longer needed

### Kept Functions
- ✅ `yak_events_debug_output()` - Still used, now called from unified page
- ✅ `yak_events_debug_shortcode()` - Still available for front-end use

---

## 🔑 Access Control Summary

### Who Can Access

**Administrators ONLY** ✅
- Super Admin (multisite)
- Administrator (single site)

### Who CANNOT Access

**Everyone else** ❌
- Editors
- Authors
- Contributors
- Subscribers
- Custom roles (unless they have administrator capability)

---

## 🎨 Visual Improvements

### Administrator Notice
At the top of the page:
```
⚠️ Administrator Only: This page is only visible to WordPress Administrators. 
Other user roles (Editors, Authors, etc.) cannot access this page.
```

### Section Organization
Each section is in a white box with:
- Clear headers with emoji icons
- Border and padding for readability
- Logical top-to-bottom flow:
  1. Settings (most important)
  2. Timestamps (frequently needed)
  3. Activity log (monitoring)
  4. Full metadata report (deep debugging)

---

## 📍 Navigation Path

**WordPress Admin → Events → Settings & Debug**

The menu item only appears in the submenu for administrators.

---

## 🔄 Shortcode Still Available

The front-end shortcode `[yak_events_debug]` still works but now requires **administrator** capability:

```php
add_shortcode('yak_events_debug', 'yak_events_debug_shortcode');
function yak_events_debug_shortcode($atts) {
    if (!current_user_can('administrator')) {
        return '<p>You do not have permission to view this page.</p>';
    }
    return yak_events_debug_output();
}
```

---

## 🧪 Testing Checklist

After update, verify:

- [ ] Administrator can see "Settings & Debug" menu item
- [ ] Editor CANNOT see "Settings & Debug" menu item
- [ ] Page loads without errors for administrator
- [ ] All 4 sections display correctly
- [ ] Settings can be saved
- [ ] Force recalculation works
- [ ] Activity log displays entries
- [ ] Event timestamps table shows all events
- [ ] Complete metadata report shows at bottom
- [ ] Non-admin gets "You do not have sufficient permissions" message if they try to access directly

---

## 📁 Files Modified

### yak-events-calendar.php
**Lines changed:**
- ~1393-1413: Removed old debug menu registration and page callback
- ~1396: Updated shortcode to require 'administrator' instead of 'manage_options'
- ~1872-1876: Added administrator check in menu registration
- ~1882: Changed capability from 'manage_options' to 'administrator'
- ~1891-1895: Added double-check for administrator capability in render function
- ~1929-1932: Added administrator-only notice at top of page
- ~2037-2042: Added full metadata report section at bottom

**Net change:** ~15 lines modified, ~5 lines removed

---

## 🔐 Security Benefits

### Why Administrator-Only?

1. **Sensitive data exposure** - Full event metadata visible
2. **System operations** - Can force recalculation affecting all events
3. **Performance metrics** - Shows internal system operation details
4. **Configuration control** - Changes affect entire site behavior

### What This Prevents

- ❌ Editors seeing unix timestamps and system internals
- ❌ Non-technical users accessing debug information
- ❌ Accidental triggering of system-wide recalculations
- ❌ Confusion from seeing technical data

---

## 💡 Best Practices

### For Site Managers

If you need to give someone **temporary access** to debug information:
1. Promote them to Administrator
2. Let them review the page
3. Demote back to their original role

### For Developers

If you need to programmatically check access:
```php
if ( current_user_can( 'administrator' ) ) {
    // User can access settings & debug page
}
```

---

## 🎓 Technical Notes

### WordPress Capability System

- `manage_options` - Given to administrators by default
- `administrator` - Specific role check (more restrictive)
- Using role name directly is more explicit than capability check
- Both menu registration and render function check for security

### Why Check Twice?

1. **Menu check** - Hides menu item from sidebar
2. **Render check** - Prevents direct URL access

Even if someone guesses the URL, they'll get a permissions error.

---

## 📞 Support

If an administrator needs access and doesn't see the menu:
1. Verify they have the Administrator role (not just Editor with extra capabilities)
2. Clear browser cache
3. Check WordPress user roles and capabilities
4. Verify plugin is activated

---

**End of Documentation**

