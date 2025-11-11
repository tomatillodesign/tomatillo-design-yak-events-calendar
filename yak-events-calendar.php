<?php
/*
Plugin Name: Tomatillo Design ~ Yak Events Calendar
Description: Custom Events Calendar for WordPress with CPT + Block. Requires ACF installed and activated.
Author: Chris Liu-Beers, Tomatillo Design
Author URI: http://www.tomatillodesign.com
Version: 1.2
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/


/* Start Adding Functions Below this Line */

// Hook into 'plugins_loaded' to check for the required plugin.
add_action('plugins_loaded', 'my_custom_plugin_check_dependency');

function my_custom_plugin_check_dependency() {
    // Specify the required plugin's file path relative to the plugins directory.
    $required_plugin = 'advanced-custom-fields-pro/acf.php';

    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    // Check if the required plugin is active.
    if (!is_plugin_active($required_plugin)) {
        // Display an admin notice.
        add_action('admin_notices', 'my_custom_plugin_dependency_notice');

        // Deactivate this plugin.
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

// Display an admin notice if the required plugin is not active.
function my_custom_plugin_dependency_notice() {
    ?>
    <div class="notice notice-error">
        <p><strong>Tomatillo Design Events Calendar</strong> has been deactivated because it requires <strong>Advanced Custom Fields</strong> to be active.</p>
    </div>
    <?php
}




//Create Custom Post Types
add_action( 'init', 'clb_add_event_cpt_121' );
function clb_add_event_cpt_121() {

    clb_create_post_types_tomatillo_design_events_calendar_121('Event', 'Events', 'events', 'calendar' );

}


// Flush permalinks on plugin activation
function yak_events_calendar_activate() {
	// Ensure CPT is registered before flushing
	clb_add_event_cpt_121();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'yak_events_calendar_activate' );


//Create Custom Taxonomies
add_action( 'init', 'clb_add_event_category_121' );
function clb_add_event_category_121() {

    clb_create_taxonomies_tomatillo_design_events_calendar_121('Event Category', 'Event Categories', 'event_categories', true, array( 'events' ));

}


//Loops through and creates the post types
function clb_create_post_types_tomatillo_design_events_calendar_121($singular, $plural, $slug, $dashicon = 'admin-post', $supports = array( 'title', 'editor', 'thumbnail', 'genesis-cpt-archives-settings', 'page-attributes', 'author', 'revisions', 'custom-fields', 'excerpt' )) {

    register_post_type( $slug,
        array(
            'labels' => array(
                'name' => __( $plural ),
                'singular_name' => __( $singular ),
                'add_new' => _x('Add new ' . $singular, $plural),
                'add_new_item' => __('Add new ' . $singular),
                'edit_item' => __('Edit ' . $singular),
                'new_item' => __('New ' . $singular),
                'view_item' => __('View ' . $singular),
                'all_items' => __( 'All ' . $plural),
                'search_items' => __( 'Search ' . $plural),
                'not_found' => __( 'No ' . $plural . ' found.' ),
            ),
            'has_archive' => false, // by default, usually set to TRUE but I wanted to use the page slugs, '/speakers', '/workshops' etc. and didn't really need public archives anyway
            'public' => true,
            'menu_icon' => 'dashicons-' . $dashicon, // see full list of dashicons here: http://www.kevinleary.net/dashicons-custom-post-type/
            'show_ui' => true, // defaults to true so don't have to include
            'show_in_menu' => true, // defaults to true so don't have to include
            'menu_position' => 20, // set default position in left-hand WP menu
            'rewrite' => array( 'slug' => $slug ),
            'supports' => $supports,
            'show_in_rest' => true,
            'rest_base'          => $slug,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'capability_type'    => 'post',
        )
    );

}

//Loops through and creates the custom taxonomies
function clb_create_taxonomies_tomatillo_design_events_calendar_121($singular, $plural, $slug, $hierarchical = true, $post_types = array('post')) {
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'              => __( $plural ),
    'singular_name'     => __( $singular ),
    'search_items'      => __( 'Search ' . $plural ),
    'all_items'         => __( 'All ' . $plural ),
    'parent_item'       => __( 'Parent ' . $plural ),
    'parent_item_colon' => __( 'Parent ' . $singular . ':' ),
    'edit_item'         => __( 'Edit ' . $plural ),
    'update_item'       => __( 'Update ' . $singular ),
    'add_new_item'      => __( 'Add New ' . $singular ),
    'new_item_name'     => __( 'New ' . $singular ),
    'menu_name'         => __( $plural ),
    'not_found'         => __( 'No ' . $plural . ' found.' ),
  );

  $args = array(
    'hierarchical'      => $hierarchical,
    'labels'            => $labels,
    'show_ui'           => true,
    'show_admin_column' => true,
    'query_var'         => true,
    'rewrite'           => array( 'slug' => $slug ),
    'show_in_rest'          => true,
    'rest_base'             => $slug,
    'rest_controller_class' => 'WP_REST_Terms_Controller',
  );

  register_taxonomy( $slug, $post_types, $args );

}




// Register Custom Fields attached to Events
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_66956ef63fd8c',
	'title' => 'Event Info',
	'fields' => array(
		/**
		 * Row 1: Start/End + All Day toggle
		 * - DT fields show only when All Day is OFF
		 * - Date-only fields show only when All Day is ON
		 */
		array(
			'key' => 'field_66956f15f7c61',
			'label' => 'Event Start Date & Time',
			'name' => 'event_start_date_time',
			'aria-label' => '',
			'type' => 'date_time_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_all_day',
						'operator' => '==',
						'value'    => '0',
					),
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id'    => '',
			),
			'display_format' => 'F j, Y g:i a',
			'return_format'  => 'F j, Y g:i a',
			'first_day'      => 0,
			'allow_in_bindings' => 1,
		),
		array(
			'key' => 'field_66956f2ff7c62',
			'label' => 'Event End Date & Time',
			'name' => 'event_end_date_time',
			'aria-label' => '',
			'type' => 'date_time_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_all_day',
						'operator' => '==',
						'value'    => '0',
					),
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id'    => '',
			),
			'display_format' => 'F j, Y g:i a',
			'return_format'  => 'F j, Y g:i a',
			'first_day'      => 0,
			'allow_in_bindings' => 1,
		),
		array(
			'key' => 'field_yak_event_start_date_only',
			'label' => 'Event Start Date',
			'name'  => 'event_start_date',
			'aria-label' => '',
			'type'  => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_all_day',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id'    => '',
			),
			'display_format' => 'F j, Y',
			'return_format'  => 'F j, Y',
			'first_day'      => 0,
			'allow_in_bindings' => 1,
		),
		array(
			'key' => 'field_yak_event_end_date_only',
			'label' => 'Event End Date',
			'name'  => 'event_end_date',
			'aria-label' => '',
			'type'  => 'date_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_all_day',
						'operator' => '==',
						'value'    => '1',
					),
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id'    => '',
			),
			'display_format' => 'F j, Y',
			'return_format'  => 'F j, Y',
			'first_day'      => 0,
			'allow_in_bindings' => 1,
		),
		array(
			'key' => 'field_yak_event_date_description',
			'label' => 'Date Description for Visitors',
			'name'  => 'event_date_description',
			'aria-label' => '',
			'type'  => 'text',
			'instructions' => 'Describe when this event happens. Examples: "Three days: June 10-12, 2025" or "Saturdays in March (3rd, 10th, 17th, 24th)" or "Every Tuesday in Fall 2025" or "Multiple dates - see schedule below"',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '100',
				'class' => '',
				'id'    => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'placeholder' => 'e.g., Multiple dates throughout June 2025',
			'prepend' => '',
			'append' => '',
			'allow_in_bindings' => 1,
		),
		array(
			'key' => 'field_yak_event_has_sessions',
			'label' => 'Multi-Session Event',
			'name'  => 'event_has_sessions',
			'aria-label' => '',
			'type'  => 'true_false',
			'instructions' => 'For events with multiple time slots (conferences, workshops, etc.). Enable to add individual sessions below.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '100',
				'class' => '',
				'id'    => '',
			),
			'message' => '',
			'default_value' => 0,
			'allow_in_bindings' => 1,
			'ui' => 1,
			'ui_on_text'  => '',
			'ui_off_text' => '',
		),
		array(
			'key' => 'field_yak_event_all_day',
			'label' => 'All Day?',
			'name'  => 'event_all_day',
			'aria-label' => '',
			'type'  => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '33.33',
				'class' => '',
				'id'    => '',
			),
			'message' => '',
			'default_value' => 0,
			'allow_in_bindings' => 1,
			'ui' => 1,
			'ui_on_text'  => '',
			'ui_off_text' => '',
		),
		

		// Existing fields continue
		array(
			'key' => 'field_66ba14f03142f',
			'label' => 'Featured Event?',
			'name' => 'is_featured_event',
			'aria-label' => '',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33.33',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'allow_in_bindings' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'ui' => 1,
		),
		array(
			'key' => 'field_66ad2b50d46d6',
			'label' => 'Gathering Mode',
			'name' => 'event_gathering_mode',
			'aria-label' => '',
			'type' => 'button_group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33.33',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'online' => 'Online',
				'person' => 'In-Person',
			),
			'default_value' => '',
			'return_format' => 'value',
			'allow_null' => 0,
			'allow_in_bindings' => 1,
			'layout' => 'horizontal',
		),
		array(
			'key' => 'field_66956ef6f7c60',
			'label' => 'Event Location & Details',
			'name' => 'event_location',
			'aria-label' => '',
			'type' => 'textarea',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_66ad2b50d46d6',
						'operator' => '==',
						'value' => 'person',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'allow_in_bindings' => 1,
			'rows' => 4,
			'placeholder' => '',
			'new_lines' => 'br',
		),
		array(
			'key' => 'field_66956f47f7c63',
			'label' => 'Event Organizer',
			'name' => 'event_organizer',
			'aria-label' => '',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'allow_in_bindings' => 1,
			'placeholder' => 'Optional. Leave blank unless additional details are needed.',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_66956f53f7c64',
			'label' => 'Event Action Button',
			'name' => 'event_action_button',
			'aria-label' => '',
			'type' => 'group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'sub_fields' => array(
				array(
					'key' => 'field_66956f85f7c65',
					'label' => 'Button Text',
					'name' => 'button_text',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Register Now',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_66956f8cf7c66',
					'label' => 'Button Link (URL)',
					'name' => 'button_link_url',
					'aria-label' => '',
					'type' => 'url',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
				),
			),
		),
		array(
			'key' => 'field_yak_event_sessions',
			'label' => 'Event Sessions / Schedule',
			'name' => 'event_sessions',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => 'Add each session for this multi-session event. Sessions can span multiple days.',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_yak_event_has_sessions',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'layout' => 'block',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Session',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_yak_session_all_day',
					'label' => 'All Day',
					'name' => 'session_all_day',
					'aria-label' => '',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '8',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'allow_in_bindings' => 0,
					'ui' => 1,
					'ui_on_text'  => '',
					'ui_off_text' => '',
					'parent_repeater' => 'field_yak_event_sessions',
				),
				array(
					'key' => 'field_yak_session_start_datetime',
					'label' => 'Session Start Date & Time',
					'name' => 'session_start_datetime',
					'aria-label' => '',
					'type' => 'date_time_picker',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_yak_session_all_day',
								'operator' => '!=',
								'value'    => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'F j, Y g:i a',
					'return_format' => 'F j, Y g:i a',
					'first_day' => 0,
					'allow_in_bindings' => 0,
					'parent_repeater' => 'field_yak_event_sessions',
				),
				array(
					'key' => 'field_yak_session_end_datetime',
					'label' => 'Session End Date & Time',
					'name' => 'session_end_datetime',
					'aria-label' => '',
					'type' => 'date_time_picker',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_yak_session_all_day',
								'operator' => '!=',
								'value'    => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'F j, Y g:i a',
					'return_format' => 'F j, Y g:i a',
					'first_day' => 0,
					'allow_in_bindings' => 0,
					'parent_repeater' => 'field_yak_event_sessions',
				),
				array(
					'key' => 'field_yak_session_start_date',
					'label' => 'Session Start Date',
					'name' => 'session_start_date',
					'aria-label' => '',
					'type' => 'date_picker',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_yak_session_all_day',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'F j, Y',
					'return_format' => 'F j, Y',
					'first_day' => 0,
					'allow_in_bindings' => 0,
					'parent_repeater' => 'field_yak_event_sessions',
				),
				array(
					'key' => 'field_yak_session_end_date',
					'label' => 'Session End Date',
					'name' => 'session_end_date',
					'aria-label' => '',
					'type' => 'date_picker',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_yak_session_all_day',
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
					'wrapper' => array(
						'width' => '25',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'F j, Y',
					'return_format' => 'F j, Y',
					'first_day' => 0,
					'allow_in_bindings' => 0,
					'parent_repeater' => 'field_yak_event_sessions',
				),
				array(
					'key' => 'field_yak_session_description',
					'label' => 'Session Description',
					'name' => 'session_description',
					'aria-label' => '',
					'type' => 'text',
					'instructions' => 'Optional. E.g., "Day 2: Workshops" or "Morning Session"',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '42',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'maxlength' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'allow_in_bindings' => 0,
					'parent_repeater' => 'field_yak_event_sessions',
				),
			),
		),
		array(
			'key' => 'field_6695707249b6a',
			'label' => 'UNIX Timestamp - DO NOT EDIT',
			'name' => 'event_unix_timestamp',
			'aria-label' => '',
			'type' => 'number',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => 'acf-hide',
				'id' => '',
			),
			'default_value' => '',
			'min' => '',
			'max' => '',
			'allow_in_bindings' => 1,
			'placeholder' => '',
			'step' => '',
			'prepend' => '',
			'append' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'events',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
) );
} );




////

/**
 * Hide Unix Timestamp field from non-admin users
 */
add_filter('acf/prepare_field/key=field_6695707249b6a', 'yak_hide_timestamp_from_non_admins');
function yak_hide_timestamp_from_non_admins( $field ) {
	// Only show to administrators
	if ( ! current_user_can('manage_options') ) {
		return false;
	}
	return $field;
}


/**
 * TODO: Complex Logic for Multi-Session Events Sorting
 * 
 * Currently, the unix timestamp is set based on the main event start date.
 * For multi-session events, we need MORE COMPLEX logic:
 * 
 * - The timestamp should be the NEXT UPCOMING SESSION (not the first session)
 * - If all sessions are past, use the last session end datetime
 * - This ensures multi-session events sort correctly in "upcoming" lists
 * 
 * Example:
 *   Event with 3 sessions:
 *   - Session 1: Nov 1 (PAST)
 *   - Session 2: Nov 8 (PAST)
 *   - Session 3: Nov 15 (FUTURE) <- Use this timestamp
 * 
 *   Result: Event appears between Nov 10 and Nov 20 events in the list
 * 
 * This requires:
 * 1. Loop through all sessions
 * 2. Find the next future session from current time
 * 3. Use that session's start datetime as the unix timestamp
 * 4. If no future sessions, use last session end (for archive/past sorting)
 * 
 * TO BE IMPLEMENTED in next phase.
 */

// Update DHM Event unix timestamp on save
add_action( 'acf/save_post', 'my_acf_save_post_update_dhm_event_unix_timestamp' );
function my_acf_save_post_update_dhm_event_unix_timestamp( $post_id ) {
	// Only for real posts (avoid autosaves/revisions/options pages).
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Optional: limit to your CPT.
	$post_type = get_post_type( $post_id );
	if ( $post_type && 'events' !== $post_type ) {
		return;
	}

	// ACF fields
	$all_day               = (bool) get_field( 'event_all_day', $post_id );
	$start_datetime_string = (string) get_field( 'event_start_date_time', $post_id ); // formatted per field
	$start_date_string     = (string) get_field( 'event_start_date', $post_id );      // formatted per field

	// Decide source:
	// - If All Day && date exists -> use date-only at 00:00 local.
	// - Else if datetime exists   -> use datetime.
	// - Else -> 0 (unset).
	$timestamp = 0;

	$tz = wp_timezone(); // honors Settings → General → Timezone

	if ( $all_day && $start_date_string ) {
		// ACF return_format for date is 'F j, Y' in your config.
		$dt = DateTimeImmutable::createFromFormat( 'F j, Y', $start_date_string, $tz );
		if ( $dt instanceof DateTimeImmutable ) {
			$timestamp = $dt->setTime( 0, 0, 0 )->getTimestamp();
		} else {
			// Fallback if format ever changes
			$ts = strtotime( $start_date_string );
			$timestamp = $ts ? (int) $ts : 0;
		}
	} elseif ( $start_datetime_string ) {
		// ACF return_format for datetime is 'F j, Y g:i a' in your config.
		$dt = DateTimeImmutable::createFromFormat( 'F j, Y g:i a', $start_datetime_string, $tz );
		if ( $dt instanceof DateTimeImmutable ) {
			$timestamp = $dt->getTimestamp();
		} else {
			// Fallback if format ever changes
			$ts = strtotime( $start_datetime_string );
			$timestamp = $ts ? (int) $ts : 0;
		}
	}

	// Save to the hidden number field by FIELD KEY (keeps ACF happy).
	// field_6695707249b6a = "UNIX Timestamp - DO NOT EDIT"
	update_field( 'field_6695707249b6a', (int) $timestamp, $post_id );
}



// Load a custom template for the 'events' post type
add_filter('template_include', 'my_events_plugin_custom_template_121');
function my_events_plugin_custom_template_121($template) {
    if (is_singular('events')) {
        // Path to the custom template file in the plugin directory
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-events.php';
        
        // Check if the custom template file exists
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}




// register custom block
add_action( 'init', 'clb_custom_acf_blocks_register_acf_blocks_events_plugin_121' );
function clb_custom_acf_blocks_register_acf_blocks_events_plugin_121() {
    register_block_type( plugin_dir_path( __FILE__ ) . 'blocks/events_list' );
	register_block_type( plugin_dir_path( __FILE__ ) . 'blocks/events_calendar' );
}



// ACF Block Fields
add_action( 'acf/include_fields', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_678fd3d6a45f0',
	'title' => 'BLOCK: Events List',
	'fields' => array(
		array(
			'key' => 'field_678fd451c9bdc',
			'label' => 'Event Query Type',
			'name' => 'event_query_type',
			'aria-label' => '',
			'type' => 'button_group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'upcoming' => 'Upcoming',
				'past' => 'Past',
			),
			'default_value' => 'upcoming',
			'return_format' => 'value',
			'allow_null' => 0,
			'allow_in_bindings' => 0,
			'layout' => 'horizontal',
		),
		array(
			'key' => 'field_678fd499c9bdd',
			'label' => 'Event Query Results - PAST',
			'name' => 'event_query_results_past',
			'aria-label' => '',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_678fd451c9bdc',
						'operator' => '==',
						'value' => 'past',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'all' => 'All Past Events',
				'this_year' => 'This Year Only',
			),
			'default_value' => 'all',
			'return_format' => 'value',
			'allow_null' => 0,
			'other_choice' => 0,
			'allow_in_bindings' => 1,
			'layout' => 'vertical',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_678fd529c9bde',
			'label' => 'Event Query Results - UPCOMING',
			'name' => 'event_query_results_upcoming',
			'aria-label' => '',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_678fd451c9bdc',
						'operator' => '==',
						'value' => 'upcoming',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'all' => 'All Upcoming Events',
				'num' => 'Limit the Number to Show',
			),
			'default_value' => 'all',
			'return_format' => 'value',
			'allow_null' => 0,
			'other_choice' => 0,
			'allow_in_bindings' => 1,
			'layout' => 'vertical',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_678fd84ea64b6',
			'label' => 'Number of Events to Show',
			'name' => 'number_of_events_to_show',
			'aria-label' => '',
			'type' => 'number',
			'instructions' => 'Use -1 to show all',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_678fd529c9bde',
						'operator' => '==',
						'value' => 'num',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'min' => '',
			'max' => '',
			'allow_in_bindings' => 0,
			'placeholder' => '',
			'step' => 1,
			'prepend' => '',
			'append' => 'upcoming events',
		),
		array(
			'key' => 'field_678fda21688a8',
			'label' => 'Event Category',
			'name' => 'event_category',
			'aria-label' => '',
			'type' => 'taxonomy',
			'instructions' => 'Leave blank to show all',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_678fd451c9bdc',
						'operator' => '==',
						'value' => 'upcoming',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'event_categories',
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'field_type' => 'multi_select',
			'allow_null' => 1,
			'allow_in_bindings' => 0,
			'bidirectional' => 0,
			'multiple' => 0,
			'bidirectional_target' => array(
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'block',
				'operator' => '==',
				'value' => 'acf/events-list',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
) );
} );






/**
 * Smart date formatter for events
 * Handles all-day and timed events with intelligent date/time display
 */
function yak_format_event_date_range( $start, $end, $is_all_day = false ) {
    if ( ! $start ) {
        return '';
    }
    
    if ( $is_all_day ) {
        // All-day events: use date picker format (F j, Y)
        if ( $start === $end || ! $end ) {
            return $start;
        }
        return $start . ' – ' . $end;
    }
    
    // Timed events: use datetime picker format (F j, Y g:i a)
    if ( ! $end ) {
        return $start;
    }
    
    // Parse both dates
    $start_obj = DateTime::createFromFormat( 'F j, Y g:i a', $start, wp_timezone() );
    $end_obj = DateTime::createFromFormat( 'F j, Y g:i a', $end, wp_timezone() );
    
    if ( ! $start_obj || ! $end_obj ) {
        // Fallback to original format if parsing fails
        return $start . ' – ' . $end;
    }
    
    // Same day: November 11, 2025 12:00 am – 3:00 am
    if ( $start_obj->format('Y-m-d') === $end_obj->format('Y-m-d') ) {
        return $start_obj->format('F j, Y g:i a') . ' – ' . $end_obj->format('g:i a');
    }
    
    // Different days, same year: November 11, 2025 12:00 am – November 13, 3:00 am
    if ( $start_obj->format('Y') === $end_obj->format('Y') ) {
        return $start_obj->format('F j, Y g:i a') . ' – ' . $end_obj->format('F j, g:i a');
    }
    
    // Different years: show both years fully
    return $start_obj->format('F j, Y g:i a') . ' – ' . $end_obj->format('F j, Y g:i a');
}


// Helper function to format and return a single Event
function clb_get_event( $post_id ) {

    $event_to_return = null;

    $title = get_the_title( $post_id );
    $permalink = get_the_permalink( $post_id );
    $event_metabox = null;
    $dhm_event_categories = null;
    $dhm_programs = null;

    // if has featured image
    $featured_image = get_the_post_thumbnail( $post_id, 'large', array( "class" => "clb-event-featured-img", "alt"=>get_the_title() ));

    if( $featured_image ) { 
        $has_image_class = ' clb-has-featured-image'; 
        $featured_image = '<div class="clb-featured-img-wrapper"><a href="' . $permalink . '">' . $featured_image . '</a></div>';
    } 
    else { $has_image_class = ' clb-missing-featured-image'; }

    $title_to_publish = '<h3 class="single-event-title"><a href="' . $permalink . '">' . $title . '</a></h3>';

    // Handle multi-session events with custom date description
    $has_sessions = get_field('event_has_sessions', $post_id);
    
    if( $has_sessions ) {
        $date_description = get_field('event_date_description', $post_id);
        if( $date_description ) {
            $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Date:</strong> ' . esc_html($date_description) . '</div>';
        }
    } else {
        // Handle both all-day and timed events with smart formatting
        $event_all_day = get_field('event_all_day', $post_id);
        
        if( $event_all_day ) {
            $event_start_date = get_field('event_start_date', $post_id);
            $event_end_date = get_field('event_end_date', $post_id);
            if( $event_start_date ) {
                $formatted_date = yak_format_event_date_range( $event_start_date, $event_end_date, true );
                $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Date:</strong> ' . $formatted_date . '</div>';
            }
        } else {
            $event_start_date_time = get_field('event_start_date_time', $post_id);
            $event_end_date_time = get_field('event_end_date_time', $post_id);
            if( $event_start_date_time ) {
                $formatted_date = yak_format_event_date_range( $event_start_date_time, $event_end_date_time, false );
                $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Date:</strong> ' . $formatted_date . '</div>';
            }
        }
    }

    // event gathering mode: online or in-person
    $gathering_mode = get_field('event_gathering_mode', $post_id);
    $event_location = get_field('event_location', $post_id);
    if( $gathering_mode == 'online' ) {
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Location:</strong> Online Via Zoom</div>';
    } elseif( $event_location) {
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Location:</strong> ' . $event_location . '</div>';
    }

    $event_organizer = get_field('event_organizer', $post_id);
    if( $event_organizer ) {
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Organizer:</strong> ' . $event_organizer . '</div>';
    }

    $custom_taxonomies = [];

    $event_categories = get_the_term_list( $post_id, 'event_categories', '<div class="nugget-desc clb-resource-info-line"><strong>Event Category:</strong> ', ', ', '</div>' );
    if( !is_wp_error( $event_categories ) ) {
        $event_categories = strip_tags( $event_categories, '<div>, <i>, <li>, <ul>, <strong>' );
        $event_metabox .= '<div class="clb-event-custom-taxonomies-wrapper">' . $event_categories . '</div>';
    }

    $event_to_return = '<div class="clb-single-event-wrapper' . $has_image_class . '">' . $featured_image . '<div class="clb-event-body">' . $title_to_publish . $event_metabox . '</div></div>';

    return $event_to_return;

}






add_action( 'wp_enqueue_scripts', 'clb_enqueue_calendar_plugin_js_functionality_121', 99 );
function clb_enqueue_calendar_plugin_js_functionality_121() {

    $dhmEventsArray = array();

    $args = array(
        'numberposts' => -1,
        'post_type'   => 'events',
        'meta_key'          => 'event_unix_timestamp',
        'order'          => 'ASC',
        'orderby'        => 'meta_value',
        'fields' => 'ids'
   );
   
   $event_ids = get_posts( $args );
   
    foreach( $event_ids as $post_id ) {

        // Get event sessions for multi-session events
        $sessions = get_field('event_sessions', $post_id);
        $sessions_array = array();
        
        if( $sessions && is_array($sessions) ) {
            foreach( $sessions as $session ) {
                $sessions_array[] = array(
                    'session_all_day' => $session['session_all_day'] ?? false,
                    'session_start_datetime' => $session['session_start_datetime'] ?? '',
                    'session_end_datetime' => $session['session_end_datetime'] ?? '',
                    'session_start_date' => $session['session_start_date'] ?? '',
                    'session_end_date' => $session['session_end_date'] ?? '',
                    'session_description' => $session['session_description'] ?? '',
                );
            }
        }

        $dhmEventsArray[] = array(
            'id' => $post_id,
            'title' => get_the_title( $post_id ),
            'permalink'   => get_the_permalink( $post_id ),
            'event_has_sessions' => get_field('event_has_sessions', $post_id),
            'event_all_day' => get_field('event_all_day', $post_id),
            'event_start_date_time' => get_field('event_start_date_time', $post_id),
            'event_end_date_time' => get_field('event_end_date_time', $post_id),
            'event_start_date' => get_field('event_start_date', $post_id),
            'event_end_date' => get_field('event_end_date', $post_id),
            'event_gathering_mode' => get_field('event_gathering_mode', $post_id),
            'event_location' => get_field('event_location', $post_id),
            'event_unix_timestamp' => intval(get_field('event_unix_timestamp', $post_id)),
            'event_sessions' => $sessions_array,
       );
       
    }

    // enqueue JS
    wp_enqueue_script( 'clb-events-plugin-global-js', plugin_dir_url( __FILE__ ) . 'js/clb-events-plugin-global-js.js', array( 'jquery' ), '1.0.0', true );
    wp_localize_script( 'clb-events-plugin-global-js', 'dhmEvents', $dhmEventsArray );

    // enqueue calendar view JS
    wp_enqueue_script( 'clb-events-calendar-view-js', plugin_dir_url( __FILE__ ) . 'blocks/events_calendar/js/clb-events-calendar-view.js', array( 'jquery' ), '1.0.0', true );

    // return '<div id="dhm-events-root" class="clb-dhm-events-root"></div>';

}



/**
 * TEMPORARY: Debug/Reporting Page for Events Metadata
 * Can be accessed via shortcode [yak_events_debug] or via admin menu
 */

// Add admin menu page
add_action('admin_menu', 'yak_events_debug_menu');
function yak_events_debug_menu() {
    add_submenu_page(
        'edit.php?post_type=events',
        'Events Debug Report',
        'Debug Report',
        'manage_options',
        'yak-events-debug',
        'yak_events_debug_page'
    );
}

// Admin page callback
function yak_events_debug_page() {
    echo '<div class="wrap">';
    echo '<h1>Events Debug Report</h1>';
    echo '<p><em>This is a temporary debugging page to verify event metadata handling.</em></p>';
    echo yak_events_debug_output();
    echo '</div>';
}

// Shortcode for front-end access
add_shortcode('yak_events_debug', 'yak_events_debug_shortcode');
function yak_events_debug_shortcode($atts) {
    if (!current_user_can('manage_options')) {
        return '<p>You do not have permission to view this page.</p>';
    }
    return yak_events_debug_output();
}

// Core debug output function
function yak_events_debug_output() {
    $args = array(
        'numberposts' => -1,
        'post_type'   => 'events',
        'meta_key'    => 'event_unix_timestamp',
        'order'       => 'ASC',
        'orderby'     => 'meta_value_num',
        'post_status' => 'publish'
    );
    
    $events = get_posts($args);
    
    if (empty($events)) {
        return '<p>No events found.</p>';
    }
    
    ob_start();
    ?>
    <style>
        .yak-debug-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .yak-debug-table th, .yak-debug-table td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: top; }
        .yak-debug-table th { background-color: #0073aa; color: white; font-weight: bold; }
        .yak-debug-table tr:nth-child(even) { background-color: #f9f9f9; }
        .yak-debug-table tr:hover { background-color: #f0f0f0; }
        .yak-debug-meta-list { margin: 0; padding: 0; list-style: none; font-size: 12px; }
        .yak-debug-meta-list li { margin: 4px 0; }
        .yak-debug-meta-list strong { display: inline-block; width: 180px; }
        .yak-debug-true { color: green; font-weight: bold; }
        .yak-debug-false { color: #999; }
        .yak-debug-past { background-color: #ffebee; }
        .yak-debug-future { background-color: #e8f5e9; }
        .yak-debug-featured { border-left: 4px solid #ff9800; }
    </style>
    
    <table class="yak-debug-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Event Title</th>
                <th>Date/Time Info</th>
                <th>Location & Details</th>
                <th>Metadata</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event) : 
            $post_id = $event->ID;
            $now = time();
            
            // Get all fields
            $all_day = get_field('event_all_day', $post_id);
            $start_datetime = get_field('event_start_date_time', $post_id);
            $end_datetime = get_field('event_end_date_time', $post_id);
            $start_date = get_field('event_start_date', $post_id);
            $end_date = get_field('event_end_date', $post_id);
            $unix_timestamp = (int) get_field('event_unix_timestamp', $post_id);
            $is_featured = get_field('is_featured_event', $post_id);
            $gathering_mode = get_field('event_gathering_mode', $post_id);
            $location = get_field('event_location', $post_id);
            $organizer = get_field('event_organizer', $post_id);
            $action_btn = get_field('event_action_button', $post_id);
            
            // Determine if past/future
            $is_past = $unix_timestamp < $now;
            $row_class = $is_past ? 'yak-debug-past' : 'yak-debug-future';
            if ($is_featured) {
                $row_class .= ' yak-debug-featured';
            }
            
            // Get categories
            $categories = get_the_terms($post_id, 'event_categories');
            $cat_names = $categories ? implode(', ', wp_list_pluck($categories, 'name')) : 'None';
            ?>
            <tr class="<?php echo esc_attr($row_class); ?>">
                <td><strong><?php echo $post_id; ?></strong></td>
                <td>
                    <strong><?php echo get_the_title($post_id); ?></strong><br>
                    <small><a href="<?php echo get_permalink($post_id); ?>" target="_blank">View</a> | 
                    <a href="<?php echo get_edit_post_link($post_id); ?>" target="_blank">Edit</a></small>
                </td>
                <td>
                    <ul class="yak-debug-meta-list">
                        <li><strong>All Day:</strong> 
                            <span class="<?php echo $all_day ? 'yak-debug-true' : 'yak-debug-false'; ?>">
                                <?php echo $all_day ? 'YES' : 'NO'; ?>
                            </span>
                        </li>
                        <?php if ($all_day) : ?>
                            <li><strong>Start Date:</strong> <?php echo $start_date ? $start_date : '<em>not set</em>'; ?></li>
                            <li><strong>End Date:</strong> <?php echo $end_date ? $end_date : '<em>not set</em>'; ?></li>
                        <?php else : ?>
                            <li><strong>Start DateTime:</strong> <?php echo $start_datetime ? $start_datetime : '<em>not set</em>'; ?></li>
                            <li><strong>End DateTime:</strong> <?php echo $end_datetime ? $end_datetime : '<em>not set</em>'; ?></li>
                        <?php endif; ?>
                        <li><strong>Unix Timestamp:</strong> <?php echo $unix_timestamp ? $unix_timestamp : '<em>0</em>'; ?></li>
                        <?php if ($unix_timestamp) : ?>
                            <li><strong>Timestamp Date:</strong> <?php echo date('Y-m-d H:i:s', $unix_timestamp); ?></li>
                        <?php endif; ?>
                    </ul>
                </td>
                <td>
                    <ul class="yak-debug-meta-list">
                        <li><strong>Gathering Mode:</strong> <?php echo $gathering_mode ? ucfirst($gathering_mode) : '<em>not set</em>'; ?></li>
                        <li><strong>Location:</strong> <?php echo $location ? nl2br(esc_html($location)) : '<em>not set</em>'; ?></li>
                        <li><strong>Organizer:</strong> <?php echo $organizer ? esc_html($organizer) : '<em>not set</em>'; ?></li>
                        <?php 
                        $sessions = get_field('event_sessions', $post_id);
                        if( $sessions && is_array($sessions) ) : ?>
                            <li><strong>Sessions:</strong> <?php echo count($sessions); ?> session(s)</li>
                        <?php endif; ?>
                    </ul>
                </td>
                <td>
                    <ul class="yak-debug-meta-list">
                        <li><strong>Featured:</strong> 
                            <span class="<?php echo $is_featured ? 'yak-debug-true' : 'yak-debug-false'; ?>">
                                <?php echo $is_featured ? 'YES' : 'NO'; ?>
                            </span>
                        </li>
                        <li><strong>Categories:</strong> <?php echo $cat_names; ?></li>
                        <li><strong>Button Text:</strong> <?php echo $action_btn['button_text'] ?? '<em>not set</em>'; ?></li>
                        <li><strong>Button URL:</strong> 
                            <?php if (!empty($action_btn['button_link_url'])) : ?>
                                <a href="<?php echo esc_url($action_btn['button_link_url']); ?>" target="_blank">Link</a>
                            <?php else : ?>
                                <em>not set</em>
                            <?php endif; ?>
                        </li>
                    </ul>
                </td>
                <td>
                    <strong><?php echo $is_past ? '🔴 PAST' : '🟢 UPCOMING'; ?></strong><br>
                    <?php if ($is_featured) : ?>
                        <span style="color: #ff9800;">⭐ FEATURED</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; padding: 20px; background: #f0f0f0; border-radius: 4px;">
        <h3>Legend:</h3>
        <ul>
            <li><span style="background: #e8f5e9; padding: 4px 8px;">Green background</span> = Upcoming event</li>
            <li><span style="background: #ffebee; padding: 4px 8px;">Red background</span> = Past event</li>
            <li><span style="border-left: 4px solid #ff9800; padding: 4px 8px;">Orange left border</span> = Featured event</li>
            <li><strong>Total Events:</strong> <?php echo count($events); ?></li>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}


/**
 * Admin columns: replace WP "Date" with "Event Start" and make it sortable.
 */
add_filter( 'manage_edit-events_columns', function( $cols ) {
	// Remove the core Date column.
	if ( isset( $cols['date'] ) ) {
		unset( $cols['date'] );
	}

	// Inject our Event Start column near the end.
	$cols['yak_event_start'] = __( 'Event Start', 'yak-events' );
	return $cols;
} );

add_action( 'manage_events_posts_custom_column', function( $column, $post_id ) {
	if ( 'yak_event_start' !== $column ) {
		return;
	}

	// Prefer the precomputed numeric timestamp (fast & consistent).
	$ts = (int) get_field( 'event_unix_timestamp', $post_id );

	// Fallbacks if someone saved before we added the saver.
	if ( ! $ts ) {
		$all_day = (bool) get_field( 'event_all_day', $post_id );
		if ( $all_day ) {
			$start_date = (string) get_field( 'event_start_date', $post_id );
			if ( $start_date ) {
				// Try your configured ACF format first (F j, Y); fall back to strtotime().
				$dt = DateTimeImmutable::createFromFormat( 'F j, Y', $start_date, wp_timezone() );
				$ts = $dt ? $dt->setTime( 0, 0, 0 )->getTimestamp() : (int) strtotime( $start_date );
			}
		} else {
			$start_dt = (string) get_field( 'event_start_date_time', $post_id );
			if ( $start_dt ) {
				$dt = DateTimeImmutable::createFromFormat( 'F j, Y g:i a', $start_dt, wp_timezone() );
				$ts = $dt ? $dt->getTimestamp() : (int) strtotime( $start_dt );
			}
		}
	}

	if ( ! $ts ) {
		echo '<em>' . esc_html__( '—', 'yak-events' ) . '</em>';
		return;
	}

	$all_day = (bool) get_field( 'event_all_day', $post_id );

	// Format for display (local site timezone).
	if ( $all_day ) {
		$label = wp_date( 'M j, Y', $ts, wp_timezone() );
	} else {
		$label = wp_date( 'M j, Y g:i a', $ts, wp_timezone() );
	}

	echo esc_html( $label );

	if ( $all_day ) {
		echo ' <span class="description" style="white-space:nowrap;">' . esc_html__( '(All Day)', 'yak-events' ) . '</span>';
	}
}, 10, 2 );

add_filter( 'manage_edit-events_sortable_columns', function( $cols ) {
	// Map our column key to a virtual "orderby" key.
	$cols['yak_event_start'] = 'yak_event_start';
	return $cols;
} );

/**
 * Apply sorting for our Event Start column and set a sane default order.
 */
add_action( 'pre_get_posts', function( WP_Query $q ) {
	if ( ! is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( 'events' !== $q->get( 'post_type' ) ) {
		return;
	}

	// Default sort: newest events first by our numeric timestamp.
	$orderby = $q->get( 'orderby' );
	if ( empty( $orderby ) ) {
		$q->set( 'meta_key', 'event_unix_timestamp' );
		$q->set( 'orderby', 'meta_value_num' );
		$q->set( 'order', 'DESC' );
		return;
	}

	// Handle clicking our column header.
	if ( 'yak_event_start' === $orderby ) {
		$q->set( 'meta_key', 'event_unix_timestamp' );
		$q->set( 'orderby', 'meta_value_num' );

		// Respect requested 'order' if present; otherwise keep DESC.
		if ( ! $q->get( 'order' ) ) {
			$q->set( 'order', 'DESC' );
		}
	}
} );
