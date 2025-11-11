# Yak Events Calendar

A powerful, flexible WordPress events management plugin with multi-session support, smart date formatting, and dynamic timestamp management.

![Version](https://img.shields.io/badge/version-1.3-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.8%2B-blue.svg)
![PHP](https://img.shields.io/badge/php-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)

---

## 🎯 Overview

**Yak Events Calendar** goes beyond simple event listings to provide sophisticated event management capabilities. Built on Advanced Custom Fields (ACF) and Gutenberg blocks, it's designed for organizations that need complex scheduling with an intuitive interface.

### Key Capabilities

- **Multi-Session Events** - Single events with multiple time slots (conferences, workshops, courses)
- **Dynamic Timestamp System** - Intelligent sorting based on next upcoming session
- **Smart Date Formatting** - Automatic condensing of redundant date information
- **Dual Display Modes** - Interactive calendar view and filterable list view
- **Flexible Scheduling** - All-day or timed events, single or multi-session
- **Admin Tools** - Comprehensive monitoring, debugging, and configuration
- **Developer Friendly** - REST API enabled, well-documented, extensible

---

## 📦 Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **[Advanced Custom Fields PRO](https://www.advancedcustomfields.com/)**: Required dependency

---

## 🚀 Installation

### Via WordPress Admin

1. Install and activate **Advanced Custom Fields PRO**
2. Upload plugin folder to `/wp-content/plugins/`
3. Activate via **Plugins** menu
4. Navigate to **Events → Settings & Debug**
5. Click **"Force Recalculation Now"** to initialize

### Via Composer

```bash
# Add to your composer.json
{
  "require": {
    "wpackagist-plugin/advanced-custom-fields-pro": "*"
  }
}
```

### First-Time Setup

```bash
1. Activate ACF Pro
2. Activate Yak Events Calendar
3. Visit Events → Settings & Debug
4. Force initial timestamp recalculation
5. Create test event
6. Add blocks to pages
```

---

## 🎨 Features

### Multi-Session Events

Create complex event schedules with multiple time slots:

```
Conference Event:
├── Session 1: Nov 10, 9am-12pm (Day 1: Keynote)
├── Session 2: Nov 10, 1pm-5pm (Day 1: Workshops)
├── Session 3: Nov 11, 9am-3pm (Day 2: Panel Discussions)
└── Session 4: Nov 12, 9am-1pm (Day 3: Closing Session)
```

**Benefits:**
- Single event post, multiple appearances on calendar
- Each session has its own description
- Sessions can span multiple days
- Event stays "upcoming" until last session ends

### Smart Date Formatting

The plugin automatically condenses date information for better UX:

| Input | Output |
|-------|--------|
| November 12, 2025 → November 12, 2025 | November 12, 2025 |
| November 12, 2025 → November 13, 2025 | November 12-13, 2025 |
| November 12, 2025 → December 2, 2025 | November 12 – December 2, 2025 |
| December 30, 2024 → January 2, 2025 | December 30, 2024 – January 2, 2025 |

For timed events, date and time are split into separate rows:

```
Date: November 12, 2025
Time: 12:00 am – 2:00 pm
Location: Online
```

### Dynamic Timestamp System

Multi-session events use intelligent timestamp logic:

```php
Event with 3 sessions:
├── Session 1: Nov 1 (PAST) ⏭️
├── Session 2: Nov 8 (PAST) ⏭️
└── Session 3: Nov 15 (FUTURE) ← Uses this for sorting

Result: Event appears between Nov 10 and Nov 20 events
```

**Technical Implementation:**
- Transient-based lazy update (configurable 1-24 hours)
- Async background processing via WordPress AJAX
- Zero impact on page load performance
- Multiple trigger points (frontend, admin, heartbeat, manual)
- Comprehensive logging and monitoring

---

## 📚 Documentation

Complete documentation available in `/docs/`:

- **[VERSION-1.3-CHANGELOG.md](docs/VERSION-1.3-CHANGELOG.md)** - Complete v1.3 technical documentation
- **[MULTI-SESSION-IMPLEMENTATION.md](docs/MULTI-SESSION-IMPLEMENTATION.md)** - Multi-session feature details
- **[QUICK-START-GUIDE.md](docs/QUICK-START-GUIDE.md)** - 2-minute setup guide
- **[ADMIN-PAGE-CONSOLIDATION.md](docs/ADMIN-PAGE-CONSOLIDATION.md)** - Admin interface details
- **[TIMEZONE-FIX.md](docs/TIMEZONE-FIX.md)** - Timezone handling documentation
- **[NONCE-FIX.md](docs/NONCE-FIX.md)** - Security implementation notes
- **[BUGFIXES-COMPLETED.md](docs/BUGFIXES-COMPLETED.md)** - Bug fix history

---

## 🔧 Usage

### Creating Events

**Single Event:**
1. Events → Add New
2. Set event title and details
3. Choose "All Day" or set specific times
4. Set start/end dates
5. Select gathering mode (Online/In-Person)
6. Add location, organizer, categories
7. Publish

**Multi-Session Event:**
1. Events → Add New
2. Enable "Multi-Session Event" toggle
3. Add custom date description text
4. Add session rows with dates/times
5. Add optional session descriptions
6. Publish

### Adding to Pages

**Calendar Block:**
```
1. Edit page in Gutenberg
2. Add block → Search "Events Calendar"
3. Interactive month view displays automatically
```

**List Block:**
```
1. Edit page in Gutenberg
2. Add block → Search "Events List"
3. Configure view options:
   - Upcoming vs All Events
   - Category filtering
   - Display count
```

### Admin Monitoring

Navigate to **Events → Settings & Debug**:

- **Cache Configuration** - Set recalculation interval
- **Manual Trigger** - Force immediate recalculation
- **Event Timestamps Table** - View current sorting data
- **Activity Log** - Monitor system operations
- **Session Details** - Inspect multi-session events

---

## 🛠️ Development

### Architecture

```
yak-events-calendar/
├── yak-events-calendar.php     # Main plugin file
├── templates/
│   └── single-events.php       # Genesis single event template
├── blocks/
│   ├── events_calendar/        # Calendar block
│   │   ├── clb_events_calendar.php
│   │   ├── clb_events_calendar.css
│   │   └── js/
│   │       └── clb-events-calendar-view.js
│   └── events_list/            # List block
│       ├── clb_events_list.php
│       └── clb_events_list.css
└── docs/                       # Documentation
```

### Key Functions

**Timestamp Calculation:**
```php
// Calculate appropriate timestamp for any event
$timestamp = yak_calculate_event_unix_timestamp( $post_id );
```

**Trigger Recalculation:**
```php
// Check transient and trigger if needed
yak_maybe_trigger_timestamp_recalc();

// Force immediate recalculation
delete_transient( 'yak_events_last_recalc' );
yak_trigger_async_recalc();
```

**Custom Logging:**
```php
// Add to activity log
yak_log_event( 'Custom event', array(
    'event_id' => $post_id,
    'data' => $custom_data,
));
```

**Date Formatting:**
```php
// Smart date range formatting
$formatted = yak_format_event_date_range( $start, $end, $is_all_day );
```

### ACF Field Groups

The plugin registers comprehensive ACF field groups:

- **Event Info** - Core event data (dates, times, location)
- **Event Sessions** - Repeater for multi-session events
- **Event Action Button** - Optional CTA configuration
- **Hidden timestamp** - Auto-calculated sorting field

### Hooks & Filters

**Custom Triggers:**
```php
// Add custom recalculation trigger
add_action( 'your_custom_hook', function() {
    yak_maybe_trigger_timestamp_recalc();
});
```

**Modify Cache Interval:**
```php
// Filter cache hours (default: 2)
add_filter( 'yak_timestamp_cache_hours', function( $hours ) {
    return 3; // Increase to 3 hours
});
```

### REST API

Events are REST API enabled:

```bash
# Get all events
GET /wp-json/wp/v2/events

# Get single event
GET /wp-json/wp/v2/events/{id}

# Get event categories
GET /wp-json/wp/v2/event_categories
```

---

## 🎯 Use Cases

### Educational Institutions
- Course schedules with multiple class sessions
- Workshop series tracking
- Academic event calendars
- Faculty event coordination

### Conference Organizers
- Multi-day conference management
- Session-by-session scheduling
- Speaker tracking per session
- Attendee-facing schedules

### Community Centers
- Recurring program schedules
- Drop-in session tracking
- Special event promotion
- Online/in-person designation

### Training Providers
- Course catalogs with session times
- Multi-week program management
- Per-session descriptions
- Automated past event handling

---

## 🧪 Testing

### Test Scenarios

**Single Event:**
```
✓ All-day event displays correctly
✓ Timed event shows date and time separately
✓ Event appears on correct calendar date
✓ Past events show warning banner
✓ Featured events have special styling
```

**Multi-Session Event:**
```
✓ Custom date description displays
✓ All sessions appear on calendar
✓ Each session shows its own time
✓ Event stays upcoming until last session
✓ Session descriptions render properly
```

**Admin Functions:**
```
✓ Timestamp recalculation completes
✓ Activity log records operations
✓ Event timestamps table accurate
✓ Cache interval saves correctly
✓ Manual trigger works immediately
```

---

## 📊 Performance

### Benchmarks

**Page Load Impact:**
- Front-end: 0ms (transient check is instant)
- Admin list: ~5-10ms for transient check
- Background process: 0.5-2 seconds for 100 events

**Database Queries:**
- Transient check: 1 query (WordPress cached)
- Background process: 2 queries per event (get + conditional update)
- Settings page: 1-2 queries per page load

**Scalability:**
- 100 events: ~1-2 seconds background processing
- 500 events: ~5-10 seconds background processing
- 1000+ events: Consider increasing cache interval

---

## 🔒 Security

- ✅ Nonce verification for all AJAX requests
- ✅ Capability checks (`manage_options` for settings)
- ✅ Input sanitization and output escaping
- ✅ No external API calls or data collection
- ✅ WordPress coding standards compliance

---

## 🐛 Troubleshooting

### Common Issues

**Multi-session event shows as PAST when sessions are upcoming:**
```
Solution: Force recalculation via Settings page
```

**Timestamps not updating:**
```
Solution: Check activity log for errors, verify session dates are valid
```

**Background process taking too long:**
```
Solution: Increase cache interval to reduce frequency
```

**Calendar JavaScript errors:**
```
Solution: Hard refresh browser (Cmd+Shift+R / Ctrl+Shift+R)
```

---

## 📈 Roadmap

Potential future enhancements:

- [ ] Batch processing for 1000+ events
- [ ] iCal export functionality
- [ ] Email notifications for upcoming events
- [ ] Advanced filtering in admin tables
- [ ] CSV export of activity log
- [ ] Recurring event series support
- [ ] Guest/attendee management
- [ ] Integration with external calendar services

---

## 🤝 Contributing

This is a proprietary plugin developed by Tomatillo Design. For bugs or feature requests, contact the development team.

---

## 📄 License

**GPL v2 or later**

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 👨‍💻 Credits

**Developed by:** Chris Liu-Beers, Tomatillo Design  
**Website:** [http://www.tomatillodesign.com](http://www.tomatillodesign.com)  
**Built with:** Advanced Custom Fields Pro  
**Version:** 1.3  
**Last Updated:** November 11, 2025

---

## 📞 Support

For questions, issues, or custom development:

1. Check **Events → Settings & Debug** page for diagnostics
2. Review Activity Log for system errors
3. Verify Event Timestamps table for data accuracy
4. Check WordPress `debug.log` for PHP errors
5. Contact Tomatillo Design for professional support

---

**Made with ❤️ by Tomatillo Design**

