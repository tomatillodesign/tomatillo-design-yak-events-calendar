# Multi-Session Events - Implementation Complete

## Overview
Implemented comprehensive multi-session event support with conditional logic that hides standard date fields when enabled.

---

## ✅ What Was Implemented

### **1. ACF Fields Added**

#### **Toggle Field**: "This event has multiple sessions"
- **Field Name**: `event_has_sessions`
- **Type**: True/False
- **Purpose**: Activates multi-session mode
- **Location**: Event Info metabox (after "All Day" toggle)

#### **Custom Date Description** (conditional)
- **Field Name**: `event_date_description`
- **Type**: Text field
- **Shows When**: `event_has_sessions` is TRUE
- **Purpose**: Free-form text for clients to describe dates
- **Examples Provided in UI**:
  - "Three days: June 10-12, 2025"
  - "Saturdays in March (3rd, 10th, 17th, 24th)"
  - "Every Tuesday in Fall 2025"
  - "Multiple dates - see schedule below"

#### **Updated Sessions Repeater**
- **Shows When**: `event_has_sessions` is TRUE
- **Fields Changed**:
  - ✅ `session_start_datetime` (DateTime Picker) - was date + time pickers
  - ✅ `session_end_datetime` (DateTime Picker) - was date + time pickers
  - ✅ `session_description` (Text) - unchanged
- **Purpose**: Allow sessions to span multiple days

#### **Hidden Fields When Multi-Session is ON**
All four main date fields hide when `event_has_sessions` is TRUE:
- `event_start_date_time`
- `event_end_date_time`
- `event_start_date`
- `event_end_date`
- `event_all_day` toggle

---

### **2. Display Logic Updated**

#### **Helper Function** (`clb_get_event()`)
- Checks for `event_has_sessions` first
- If TRUE: displays custom `event_date_description`
- If FALSE: uses existing smart date formatting

#### **Single Event Template** (`single-events.php`)
- Same logic as helper function
- Session display updated to use new datetime fields
- Sessions now use smart date formatter

#### **Session Display**
- Uses `yak_format_event_date_range()` for smart formatting
- Shows datetime range per session
- Sessions can span multiple days properly

---

### **3. Calendar JavaScript Updated**

#### **Month Filtering**
- Checks if event has sessions
- For multi-session: shows if ANY session falls in that month
- For regular events: existing logic unchanged

#### **Day Filtering**
- Checks if event has sessions
- For multi-session: shows if ANY session overlaps that day
- Shows session-specific times on calendar

#### **Display Enhancement**
- Multi-session events show start/end times from the specific session
- Times formatted using JavaScript `toLocaleTimeString()`

---

### **4. List View Filtering**

#### **Upcoming/Past Logic**
- **Multi-session events**: 
  - "Upcoming" if ANY session is in future
  - "Past" if event has sessions but NONE are future
- **Standard events**: existing unix timestamp logic

#### **Year Filtering**
- Multi-session: checks if any session is in current year
- Standard: existing logic

---

### **5. JavaScript Data**

Added to `dhmEvents` array:
- `event_has_sessions`
- `event_sessions` array with:
  - `session_start_datetime`
  - `session_end_datetime`
  - `session_description`

---

## ⏸️ TODO: Complex Sorting Logic

**Not Yet Implemented** (to be tackled separately):

The unix timestamp for multi-session events should be **the NEXT UPCOMING SESSION**, not the first session.

### **Current Behavior**:
Multi-session events use the main event unix timestamp (which may not exist or be inaccurate)

### **Desired Behavior**:
```
Event with 3 sessions:
- Session 1: Nov 1 (PAST)
- Session 2: Nov 8 (PAST)
- Session 3: Nov 15 (FUTURE) <- Use this timestamp

Result: Event appears between Nov 10 and Nov 20 events
```

### **Implementation Plan** (Next Phase):
1. In `my_acf_save_post_update_dhm_event_unix_timestamp()`
2. Check if `event_has_sessions` is true
3. Loop through all sessions
4. Find the next future session from current time
5. Use that session's start datetime as unix timestamp
6. If no future sessions, use last session end (for archive sorting)

**TODO comment added in code** at line 665 of `yak-events-calendar.php`

---

## 📝 Files Modified

1. **yak-events-calendar.php**
   - Added toggle and date description fields
   - Updated conditional logic for all main date fields
   - Updated sessions repeater to datetime pickers
   - Updated helper function display logic
   - Updated JS data array
   - Added TODO comment for sorting logic

2. **templates/single-events.php**
   - Updated display logic for multi-session
   - Updated session loop to use new datetime fields
   - Uses smart date formatter for sessions

3. **blocks/events_list/clb_events_list.php**
   - Added upcoming/past logic for multi-session events
   - Checks ANY session for future/past determination
   - Year filtering for multi-session events

4. **blocks/events_calendar/clb_events_calendar.css**
   - Updated session CSS from separate date/time to single datetime

5. **blocks/events_calendar/js/clb-events-calendar-view.js**
   - Updated month filtering for multi-session
   - Updated day filtering for multi-session
   - Display session-specific times on calendar
   - Handles multi-day sessions properly

---

## 🎯 Testing Checklist

### **Create Test Events**:

1. **Regular Event** (no sessions)
   - Should work as before
   - Date fields visible
   - Smart formatting works

2. **Multi-Session Event** (3 sessions)
   - Toggle "has multiple sessions" = YES
   - Date fields should HIDE
   - Custom date description field appears
   - Add 3 sessions with different dates/times
   - Save and check:
     - ✅ Custom description shows in list view
     - ✅ Sessions display on single page
     - ✅ Event appears on calendar on each session date
     - ✅ Session times show on calendar
     - ✅ Event appears in "upcoming" if ANY session is future

3. **Multi-Day Session Event**
   - Create session: Nov 10 9am - Nov 12 5pm
   - Check calendar shows event on Nov 10, 11, AND 12

---

## 🚀 Next Steps

1. **Test thoroughly** with various event configurations
2. **Tackle sorting logic** together (complex - marked with TODO)
3. **Monitor** how clients use the custom date description field
4. **Consider** additional session fields if needed (location per session, etc.)

---

## Summary

✅ **Complete**: Conditional multi-session UI with datetime fields
✅ **Complete**: Display logic for custom date descriptions  
✅ **Complete**: Calendar showing events on all session dates
✅ **Complete**: Upcoming/past filtering for multi-session events
⏸️ **Pending**: Dynamic unix timestamp for next upcoming session

**Status**: Ready for testing! Sorting logic to be implemented in follow-up.

