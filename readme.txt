=== Tomatillo Design ~ Yak Events Calendar ===
Contributors: tomatillodesign
Tags: events, calendar, acf, gutenberg, multi-session
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A powerful, flexible events calendar plugin with multi-session support, smart date formatting, and dynamic timestamp management.

== Description ==

**Yak Events Calendar** is a professional WordPress events management plugin designed for organizations that need more than just simple event listings. Built on Advanced Custom Fields (ACF) and Gutenberg blocks, it provides sophisticated event management with an intuitive interface.

= Key Features =

* **Multi-Session Events** - Run conferences, workshops, or courses with multiple time slots
* **Smart Date Formatting** - Automatically condenses redundant date information for better UX
* **Dynamic Timestamp System** - Events sort correctly based on next upcoming session
* **Two Display Modes** - Interactive calendar view and sortable list view
* **All-Day or Timed Events** - Flexible scheduling for any event type
* **Event Categories** - Custom taxonomy for organizing events
* **In-Person or Online** - Dedicated gathering mode settings
* **Featured Events** - Highlight important events
* **Past Event Detection** - Automatic visual indicators for completed events
* **Admin Settings & Debug** - Comprehensive monitoring and configuration

= Perfect For =

* Educational institutions
* Conference organizers
* Community centers
* Business event coordinators
* Nonprofit organizations
* Training providers
* Any organization with complex event scheduling needs

= Multi-Session Events =

One of the most powerful features is multi-session event support:

* Single event post can represent a multi-day conference or recurring workshop
* Each session has its own date/time and optional description
* Events appear on calendar for ALL session dates
* Automatic "upcoming" detection - event stays upcoming until last session ends
* Perfect for: Conference series, workshop sequences, training programs

= Smart Sorting System =

The plugin includes an intelligent timestamp system:

* Multi-session events sort by NEXT upcoming session
* Configurable cache (1-24 hours) for performance
* Async background processing - zero impact on page load
* Automatic recalculation triggers
* Admin monitoring and manual override available

= Developer Friendly =

* REST API enabled for custom integrations
* Genesis framework compatible
* Clean, well-documented code
* Custom hooks and filters
* Extensible ACF field groups

== Installation ==

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* **Advanced Custom Fields PRO** (required dependency)

= Installation Steps =

1. Install and activate **Advanced Custom Fields PRO**
2. Upload the plugin files to `/wp-content/plugins/tomatillo-design-yak-events-calendar/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Events → Settings & Debug**
5. Click "Force Recalculation Now" to initialize the timestamp system
6. Add your first event via **Events → Add New**
7. Add event blocks to pages using the Gutenberg editor

= First-Time Setup =

After activation:

1. Go to **Events → Settings & Debug** in WordPress admin
2. Review the default cache interval (2 hours recommended)
3. Click "Force Recalculation Now" button
4. Create a test event to verify functionality
5. Add an Events Calendar or Events List block to a page

== Frequently Asked Questions ==

= Do I need Advanced Custom Fields? =

Yes, ACF Pro is a required dependency. The plugin will display an admin notice if ACF is not installed.

= Can one event span multiple days? =

Yes! You can create multi-session events where each session has its own date/time range, even spanning multiple days per session.

= How do events sort in "upcoming" lists? =

* Regular events: Sort by start date
* Multi-session events: Sort by next upcoming session date
* Past events: All sessions have ended
* System recalculates automatically based on your configured cache interval

= What's the cache interval for? =

The cache interval controls how often the system recalculates event timestamps. Default is 2 hours, balancing accuracy with server performance. Lower intervals = more accurate sorting, higher intervals = less server load.

= Can I manually trigger timestamp recalculation? =

Yes! Go to **Events → Settings & Debug** and click "Force Recalculation Now".

= Does this work with page builders? =

The plugin uses Gutenberg blocks. Compatibility with page builders depends on their Gutenberg block support.

= Is this compatible with Genesis framework? =

Yes, the plugin includes specific Genesis framework integration.

= Can visitors filter events by category? =

Yes, the Events List block includes category filtering options.

== Screenshots ==

1. Interactive calendar view showing events across the month
2. Events list view with upcoming/past filtering
3. Single event page with multi-session schedule
4. Admin event editor with ACF fields
5. Settings & Debug page with timestamp monitoring
6. Activity log showing system operations

== Changelog ==

= 1.3 - 2025-11-11 =
* Added: Dynamic timestamp calculation for multi-session events
* Added: Transient-based lazy update system with configurable cache
* Added: Async background processing for timestamp recalculation
* Added: Settings & Debug admin page with monitoring tools
* Added: Activity log system for debugging
* Added: Event organizer field display
* Added: Session-specific "All Day" toggle
* Improved: Smart date formatting for all-day multi-day events
* Improved: Separate "Date" and "Time" rows for timed events
* Improved: Admin calendar preview with actual event data
* Fixed: Multi-session events sorting by first session instead of next session
* Fixed: Timezone conversion accuracy for Unix timestamps
* Fixed: Calendar JavaScript duplicate declaration errors
* Fixed: All-day events showing on wrong date in calendar

= 1.2 - 2025-11-10 =
* Added: Multi-session event support with repeater fields
* Added: Conditional ACF logic for multi-session vs single events
* Added: Custom date description field for multi-session events
* Added: Session display on single event pages
* Added: Calendar support for multi-session events
* Improved: Date range formatting to reduce redundancy
* Improved: ACF field layout and organization
* Fixed: Event metabox display issues
* Fixed: Calendar month/day filtering logic

= 1.1 =
* Initial public release
* Custom Events post type
* Event Categories taxonomy
* Two Gutenberg blocks (Calendar and List views)
* ACF field groups for event management
* Smart date formatting
* Featured event support
* Genesis template integration

== Upgrade Notice ==

= 1.3 =
Major update with dynamic timestamp system for multi-session events. After upgrading, visit Events → Settings & Debug and click "Force Recalculation Now" to initialize the new system.

= 1.2 =
Adds powerful multi-session event support. Existing events will continue working normally.

== Advanced Usage ==

= Creating a Multi-Session Event =

1. Create a new event
2. Enable "Multi-Session Event" toggle
3. Add custom date description (e.g., "Three days: June 10-12, 2025")
4. Add session rows with start/end dates and optional descriptions
5. Main date fields will be hidden (not needed for multi-session)

= Monitoring System Performance =

Navigate to **Events → Settings & Debug** to:

* View current timestamp for each event
* Check if events are detected as upcoming or past
* Review activity log for system operations
* Monitor background process duration
* Adjust cache interval if needed

= Custom Development =

Developers can extend the plugin:

```php
// Trigger timestamp recalculation
yak_maybe_trigger_timestamp_recalc();

// Get event timestamp
$timestamp = yak_calculate_event_unix_timestamp( $post_id );

// Add custom logging
yak_log_event( 'Custom message', array( 'data' => $value ) );
```

= Performance Optimization =

For sites with many events:

* Increase cache interval to 3-4 hours
* Monitor background process duration in activity log
* Use force recalculation after bulk operations only
* Review timestamp table weekly for accuracy

== Credits ==

Developed by Chris Liu-Beers, Tomatillo Design
http://www.tomatillodesign.com

Built with Advanced Custom Fields Pro
https://www.advancedcustomfields.com/

== Support ==

For issues or questions:

1. Check the Activity Log in Settings & Debug page
2. Review the Event Timestamps table for data accuracy
3. Try Force Recalculation if timestamps seem incorrect
4. Check WordPress debug.log for PHP errors

== Privacy ==

This plugin:
* Does not collect any user data
* Does not make external API calls
* Does not set cookies
* Stores all data in your WordPress database

