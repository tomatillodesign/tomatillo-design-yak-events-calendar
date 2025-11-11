# Timezone Fix - Unix Timestamp Conversion Issue

**Date:** November 11, 2025

---

## 🐛 Issue Identified

**Problem:** Unix timestamps were displaying incorrectly due to timezone conversion issues.

**Example:**
- User sets in ACF: `November 17, 2025 1:00 am`
- System showed: `2025-11-17 08:00:00` (7 hours off)

**Root Cause:** Using PHP's `date()` function instead of WordPress's `wp_date()` function. PHP's `date()` uses the server's default timezone, not WordPress's configured timezone.

---

## ✅ What Was Fixed

### 1. Debug Table Display
**File:** `yak-events-calendar.php`

**Changed:**
```php
// BEFORE (Wrong - uses server timezone)
echo date('Y-m-d H:i:s', $unix_timestamp);

// AFTER (Correct - uses WordPress timezone)
echo wp_date('Y-m-d H:i:s', $unix_timestamp);
```

**Now displays:**
- **Timestamp Date (WP TZ):** Shows time in WordPress configured timezone
- **Timestamp Date (UTC):** Shows UTC time for reference

### 2. Settings Page Display
**Changed:** All `date()` calls to `wp_date()` in:
- Last Recalculation time
- Next Recalculation time

### 3. Event Timestamps Table
**Changed:**
- Event date/time now shows in WordPress timezone
- Added UTC reference for debugging
- Both use proper timezone-aware functions

---

## 🔍 Technical Details

### WordPress Timezone Functions

**Correct Functions:**
- ✅ `wp_date()` - Respects WordPress timezone setting
- ✅ `wp_timezone()` - Gets WordPress timezone object
- ✅ `gmdate()` - Always returns UTC (good for reference)

**Incorrect Functions:**
- ❌ `date()` - Uses PHP server timezone (not WordPress setting)
- ❌ `strtotime()` - Can be ambiguous with timezones

### How DateTimeImmutable Works

```php
$tz = wp_timezone(); // Get WordPress timezone (e.g., "America/Los_Angeles")
$dt = DateTimeImmutable::createFromFormat('F j, Y g:i a', 'November 17, 2025 1:00 am', $tz);
$timestamp = $dt->getTimestamp(); // Returns Unix timestamp (UTC seconds since 1970)
```

**Key Points:**
1. The `$tz` parameter tells PHP: "Interpret this string as if it's in this timezone"
2. `getTimestamp()` always returns UTC timestamp
3. When displaying, we must use `wp_date()` to convert back to WordPress timezone

---

## 📊 What You'll See Now

### Complete Event Metadata Report
Each event now shows:
```
Unix Timestamp: 1731819600
Timestamp Date (WP TZ): 2025-11-17 01:00:00  ← Correct!
Timestamp Date (UTC): 2025-11-17 09:00:00    ← Reference
```

### Event Timestamps Table
Each event shows:
```
November 17, 2025
1:00 am           ← Your local time
UTC: 2025-11-17 09:00  ← UTC reference
```

---

## 🚀 How to Test

### Step 1: Force Recalculation
1. Go to **WordPress Admin → Events → Settings & Debug**
2. Click **"Force Recalculation Now"** button
3. Wait for success message

### Step 2: Check the Activity Log
Scroll down to Activity Log section and look for:
```
Background timestamp recalculation completed
Details:
  total_events: X
  updated_events: Y
  duration_seconds: Z
```

### Step 3: Verify Timestamps
Scroll down to **"Complete Event Metadata Report"** section:
- Find your November 17, 1am event
- Check **"Timestamp Date (WP TZ)"** - should now show `2025-11-17 01:00:00`
- Check **"Timestamp Date (UTC)"** - will show the UTC equivalent

### Step 4: Check Event Timestamps Table
Scroll to **"Event Timestamps"** section:
- Find your event
- Verify it shows `November 17, 2025` and `1:00 am`
- Check the UTC reference time below

---

## 🔧 What WordPress Timezone Are You Using?

To check your WordPress timezone setting:
1. Go to **Settings → General**
2. Look for **Timezone**
3. Common values:
   - Los Angeles (PST/PDT) = UTC-8 / UTC-7
   - New York (EST/EDT) = UTC-5 / UTC-4
   - Chicago (CST/CDT) = UTC-6 / UTC-5
   - Denver (MST/MDT) = UTC-7 / UTC-6

**Example:**
If your timezone is "America/Los_Angeles" (PST = UTC-8):
- You set: November 17, 1:00 am PST
- Unix timestamp: 1731837600
- Display (WP TZ): 2025-11-17 01:00:00 ✅
- Display (UTC): 2025-11-17 09:00:00 ✅

---

## 🎯 Expected Behavior

### For Single Events
- Set date/time in ACF → Stores as Unix timestamp in site timezone
- Display in admin → Shows in site timezone (via `wp_date()`)
- Display on front-end → Shows in site timezone

### For Multi-Session Events
- Each session uses its own start/end datetime
- Next upcoming session determines the main timestamp
- All times respect WordPress timezone setting
- Display always uses `wp_date()` for consistency

---

## 📝 Files Modified

### yak-events-calendar.php
**Line ~1551-1552:** Debug table timestamp display
- Added `wp_date()` for WordPress timezone
- Added `gmdate()` for UTC reference

**Line ~2012:** Settings page - Last recalculation
- Changed `date()` to `wp_date()`

**Line ~2015:** Settings page - Next recalculation  
- Changed `date()` to `wp_date()`

**Line ~2163-2165:** Event Timestamps table
- Changed `date()` to `wp_date()`
- Added UTC reference with `gmdate()`

**Line ~944-960:** `yak_parse_datetime_string()` function
- Simplified fallback logic
- Removed timezone offset adjustments (handled by `DateTimeImmutable`)

**Line ~966-990:** `yak_parse_date_string()` function
- Simplified fallback logic
- Removed timezone offset adjustments

---

## ✅ Verification Checklist

After forcing recalculation:

- [ ] Debug report shows correct "Timestamp Date (WP TZ)"
- [ ] Times match what you set in ACF
- [ ] UTC times show proper offset from your timezone
- [ ] Multi-session events use correct session timestamps
- [ ] "Last Recalculation" shows current time in your timezone
- [ ] Activity log shows successful completion

---

## 🐛 If Times Are Still Wrong

### Check These:
1. **WordPress Timezone:** Settings → General → Timezone
2. **Server Timezone:** PHP `date_default_timezone_get()`
3. **ACF Field Format:** Should be `F j, Y g:i a` for datetime
4. **Database:** Timestamps should be integers (Unix timestamps)

### Debug Commands:
```php
// Check WordPress timezone
echo wp_timezone_string(); // e.g., "America/Los_Angeles"

// Check PHP timezone
echo date_default_timezone_get(); // e.g., "UTC"

// Test conversion
$ts = 1731837600;
echo wp_date('Y-m-d H:i:s', $ts); // Should match your timezone
echo gmdate('Y-m-d H:i:s', $ts);  // Should show UTC
```

---

## 📞 Next Steps

1. **Force recalculation** via Settings & Debug page
2. **Check Activity Log** for completion message
3. **Verify timestamps** in both tables
4. **Test a new event** - create and save, check timestamp immediately
5. **Report back** if times are now correct!

---

**End of Documentation**

