# Quick Start Guide - Version 1.3

## 🎯 What's New?

Multi-session events now sort correctly! The plugin automatically uses the **next upcoming session** for sorting, ensuring your 3-day conference doesn't disappear from "upcoming events" until the last session ends.

---

## 🚀 Getting Started (2 Minutes)

### 1. Access Settings Page

Navigate to: **WordPress Admin → Events → Settings & Debug**

### 2. Configure Cache Interval (Optional)

- Default: **2 hours** (recommended)
- Range: 1-24 hours
- Lower = more accurate, higher = less server load

### 3. Force Initial Recalculation

Click **"Force Recalculation Now"** button to initialize the system.

### 4. Verify Timestamps

Scroll down to the **"Event Timestamps"** table to see how WordPress is sorting your events.

---

## 📊 What to Monitor

### Green Indicators (Good)
- ✅ Events show as UPCOMING when they have future sessions
- ✅ Activity log shows successful recalculations
- ✅ Background process completes in < 5 seconds

### Red Flags (Check These)
- ⚠️ Multi-session event shows as PAST when sessions are upcoming
- ⚠️ Activity log shows "Invalid nonce" errors
- ⚠️ Background process takes > 30 seconds

---

## 🔧 Common Tasks

### Force Recalculation
**When:** After bulk-editing events or importing new events  
**How:** Settings page → Click "Force Recalculation Now"

### Clear Activity Log
**When:** Log is cluttered or full  
**How:** Settings page → Click "Clear Log"

### Adjust Cache Interval
**When:** Events aren't sorting accurately enough  
**How:** Settings page → Change hours → Save Settings

---

## 🐛 Troubleshooting

### Problem: Multi-session event shows as PAST
**Solution:** Force recalculation or wait for next automatic trigger

### Problem: Timestamps not updating
**Solution:** Check activity log for errors, ensure sessions have valid dates

### Problem: Background process taking too long
**Solution:** Increase cache interval to reduce frequency

---

## 📖 Understanding the System

### How It Works

1. **Event is saved** → Timestamp calculated immediately
2. **X hours pass** → Transient expires
3. **Page loads** → System detects expired transient
4. **Background process** → Recalculates all event timestamps
5. **Repeat** → System maintains accurate timestamps automatically

### What Gets Logged

- Event saves with new timestamps
- Recalculation triggers
- Background process completions (with stats)
- Any errors or warnings

### Multiple Trigger Points

The system can trigger from:
- ✓ Front-end page loads
- ✓ Admin events list views
- ✓ WordPress Heartbeat API (admin)
- ✓ Manual "Force Recalculation" button

---

## 💡 Pro Tips

1. **Check the log weekly** to ensure system is running smoothly
2. **Use 2-3 hour cache** for best balance of accuracy and performance
3. **Force recalc after bulk edits** to see changes immediately
4. **Watch the "Updated events" count** in logs - should be low after first recalc
5. **Green background = upcoming** in the timestamps table

---

## 🎓 For Advanced Users

### View Raw Timestamp
In the Event Timestamps table, click on any event's unix timestamp to see the raw number.

### Interpret Activity Log Context
Click "View details" in any log entry to see:
- Duration of background processes
- Number of events processed
- Number of timestamps updated
- Multi-session event counts

### Custom Triggers
If you need to trigger recalculation from custom code:
```php
delete_transient( 'yak_events_last_recalc' );
yak_trigger_async_recalc();
```

---

## ✅ Success Checklist

After updating to v1.3, verify:

- [ ] Settings page loads without errors
- [ ] Forced recalculation completes successfully
- [ ] Activity log shows completion entry
- [ ] Event Timestamps table displays all events
- [ ] Multi-session events show correct status
- [ ] Upcoming events list on front-end is accurate

---

## 📞 Need Help?

1. **First:** Check the Activity Log for error messages
2. **Second:** Review Event Timestamps table for accuracy
3. **Third:** Try Force Recalculation
4. **Fourth:** Check WordPress debug.log for PHP errors

---

**Quick Reference Complete!**

For full technical details, see `VERSION-1.3-CHANGELOG.md`

