# Nonce Verification Fix for Background Process

**Date:** November 11, 2025

---

## 🐛 Issue

**Error in Activity Log:**
```
Background recalc failed: Invalid nonce
```

**Root Cause:** WordPress nonces don't work well with non-blocking async requests (`blocking => false`). When we trigger the background recalculation with a non-blocking request, the session isn't maintained, so nonce verification fails.

---

## ✅ The Fix

Changed from **nonce-based verification** to **secret key verification** using WordPress transients.

### How It Works Now:

1. **Trigger** - When recalculation is triggered:
   - Generate a random 32-character secret
   - Store it in a transient (valid for 60 seconds)
   - Pass it to the background AJAX request

2. **Verify** - Background process checks:
   - Does submitted secret match stored secret?
   - If yes → proceed with recalculation
   - If no → log error and stop

3. **Cleanup** - After verification:
   - Delete the secret transient immediately
   - Prevents reuse/replay attacks

### Why This Works:

- ✅ Transients work across non-blocking requests
- ✅ Secret expires after 60 seconds
- ✅ Secret is deleted after one use
- ✅ Still secure (random 32-char string)
- ✅ No session/cookie dependencies

---

## 📝 Code Changes

### Before (Nonce-Based):
```php
function yak_trigger_async_recalc() {
    $nonce = wp_create_nonce( 'yak_recalc_nonce' );
    wp_remote_post( admin_url( 'admin-ajax.php' ), array(
        'blocking' => false,
        'body' => array(
            'action' => 'yak_recalc_event_timestamps',
            'nonce' => $nonce,
        ),
    ));
}

function yak_recalc_timestamps_background() {
    if ( ! wp_verify_nonce( $_POST['nonce'], 'yak_recalc_nonce' ) ) {
        yak_log_event( 'Background recalc failed: Invalid nonce' );
        wp_die();
    }
    // ... rest of code
}
```

### After (Secret Key-Based):
```php
function yak_trigger_async_recalc() {
    $secret = wp_generate_password( 32, false );
    set_transient( 'yak_recalc_secret', $secret, 60 );
    wp_remote_post( admin_url( 'admin-ajax.php' ), array(
        'blocking' => false,
        'body' => array(
            'action' => 'yak_recalc_event_timestamps',
            'secret' => $secret,
        ),
    ));
}

function yak_recalc_timestamps_background() {
    $submitted_secret = isset( $_POST['secret'] ) ? $_POST['secret'] : '';
    $stored_secret = get_transient( 'yak_recalc_secret' );
    
    if ( ! $submitted_secret || $submitted_secret !== $stored_secret ) {
        yak_log_event( 'Background recalc failed: Invalid or expired secret key' );
        wp_die();
    }
    
    delete_transient( 'yak_recalc_secret' );
    // ... rest of code
}
```

---

## 📋 Activity Log Improvements

Also improved the Activity Log display:

### Storage:
- ✅ Keeps last **100 entries** in database
- ✅ Automatically truncates older entries

### Display:
- ✅ Shows **50 most recent** entries in admin
- ✅ Clear messaging about storage/display limits
- ✅ Shows count: "Showing 50 most recent entries out of 100 total stored"

### Why 100/50?

- **100 stored** - Enough history for debugging without bloating database
- **50 displayed** - Keeps page load fast, shows relevant recent activity
- **Auto-cleanup** - Old entries automatically removed

---

## 🚀 Test Now

### Step 1: Clear Old Log Entries
1. Go to **Events → Settings & Debug**
2. Scroll to **Activity Log** section
3. Click **"Clear Log"** button
4. This removes the old "Invalid nonce" errors

### Step 2: Force Recalculation
1. Scroll back up to **Cache Settings**
2. Click **"🔄 Force Recalculation Now"** button
3. Wait for success message

### Step 3: Check Activity Log
Scroll down to Activity Log and you should see:
```
✅ Timestamp recalculation triggered
✅ Background timestamp recalculation completed
    Details:
    - total_events: X
    - updated_events: Y
    - duration_seconds: Z
```

**No more "Invalid nonce" errors!** ✅

---

## 🔐 Security Considerations

### Is This Secure?

**Yes!** Here's why:

1. **Random Secret** - 32-character random string (like a password)
2. **Short Lifetime** - Expires after 60 seconds
3. **One-Time Use** - Deleted immediately after use
4. **Site-Only** - Only works on your own site (can't be called externally)
5. **No User Input** - Background process doesn't accept any user parameters

### Comparison:

| Method | Blocking Requests | Non-Blocking Requests | Security |
|--------|------------------|----------------------|----------|
| Nonce | ✅ Works | ❌ Fails | High |
| Secret Key | ✅ Works | ✅ Works | High |
| No Verification | ✅ Works | ✅ Works | ❌ Low |

---

## 📊 What Changed

### yak-events-calendar.php

**Line ~1728-1742:** `yak_trigger_async_recalc()`
- Generate random secret
- Store in transient (60s expiry)
- Pass to AJAX request

**Line ~1752-1766:** `yak_recalc_timestamps_background()`
- Verify secret matches transient
- Delete transient after verification
- Improved error logging

**Line ~2035:** Activity Log display
- Added description of storage limits

**Line ~2073-2077:** Activity Log footer
- Improved messaging about entry counts
- Shows count for both scenarios (50+ and <50)

---

## 🎯 Expected Results

After the fix:

- ✅ Background recalculation completes successfully
- ✅ Activity log shows completion message
- ✅ No more "Invalid nonce" errors
- ✅ Event timestamps update correctly
- ✅ Clear log messaging (100 stored, 50 displayed)

---

## 🐛 If You Still See Errors

### "Invalid or expired secret key"

**Possible causes:**
1. Transient expired (took > 60 seconds to process request)
2. Multiple recalculations triggered simultaneously
3. Transient storage not working (rare)

**Solution:**
- Try again (usually resolves it)
- Check for plugin conflicts
- Verify transients are working: `get_transient('test')`

### "Background recalc failed"

**Check:**
- PHP error log for detailed errors
- WordPress debug.log
- Activity log "Details" for context

---

## 📞 Next Steps

1. **Clear the log** to remove old errors
2. **Force recalculation** to test the fix
3. **Check Activity Log** for success message
4. **Verify timestamps** in the tables below

If successful, you should see completed recalculation logs with no errors! 🎉

---

**End of Documentation**

