<?php
/*
Plugin Name: Tomatillo Design ~ Yak Events Calendar
Description: Custom Events Calendar for WordPress with CPT + Block. Requires ACF installed and activated.
Author: Chris Liu-Beers, Tomatillo Design
Author URI: http://www.tomatillodesign.com
Version: 1.3
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
 * MULTI-SESSION EVENT SORTING - IMPLEMENTED ✓
 * 
 * The unix timestamp for events is now dynamically calculated:
 * 
 * For MULTI-SESSION events:
 * - If any session is upcoming: Uses the NEXT upcoming session's start time
 * - If all sessions are past: Uses the LAST session's end time
 * 
 * For SINGLE events:
 * - Uses the main event start date/time
 * 
 * Example:
 *   Event with 3 sessions:
 *   - Session 1: Nov 1 (PAST)
 *   - Session 2: Nov 8 (PAST)
 *   - Session 3: Nov 15 (FUTURE) <- Uses this timestamp
 * 
 *   Result: Event appears between Nov 10 and Nov 20 events in the list
 * 
 * Implementation:
 * - Transient-based lazy update system (recalculates every X hours)
 * - Async background processing (doesn't block page loads)
 * - Multiple trigger points (front-end, admin, heartbeat API)
 * - Settings page for configuration and debugging
 * 
 * See Settings & Debug page under Events menu for monitoring.
 */

// Update event unix timestamp on save
add_action( 'acf/save_post', 'my_acf_save_post_update_dhm_event_unix_timestamp', 20 ); // Priority 20 to run after ACF saves fields
function my_acf_save_post_update_dhm_event_unix_timestamp( $post_id ) {
	// Only for real posts (avoid autosaves/revisions/options pages).
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Only for events CPT
	$post_type = get_post_type( $post_id );
	if ( $post_type && 'events' !== $post_type ) {
		return;
	}

	// Calculate and update timestamp
	$timestamp = yak_calculate_event_unix_timestamp( $post_id );
	
	// Save to the hidden number field by FIELD KEY
	update_field( 'field_6695707249b6a', (int) $timestamp, $post_id );
	
	// Clear the transient to force recalculation on next load
	delete_transient( 'yak_events_last_recalc' );
	
	// Log the save
	yak_log_event( 'Event saved and timestamp updated', array(
		'post_id' => $post_id,
		'timestamp' => $timestamp,
		'human_time' => $timestamp ? date( 'F j, Y g:i a', $timestamp ) : 'No timestamp',
	));
}

/**
 * Calculate the appropriate unix timestamp for an event
 * 
 * Logic:
 * - For multi-session events: Use next upcoming session start OR last session end (if all past)
 * - For single events: Use main event start date/time
 * 
 * @param int $post_id Event post ID
 * @return int Unix timestamp
 */
function yak_calculate_event_unix_timestamp( $post_id ) {
	$tz = wp_timezone();
	$now = current_time( 'timestamp' );
	
	// Check if this is a multi-session event
	$has_sessions = (bool) get_field( 'event_has_sessions', $post_id );
	
	if ( $has_sessions ) {
		$sessions = get_field( 'event_sessions', $post_id );
		
		if ( $sessions && is_array( $sessions ) ) {
			$session_timestamps = array();
			
			foreach ( $sessions as $session ) {
				$session_all_day = isset( $session['session_all_day'] ) ? (bool) $session['session_all_day'] : false;
				
				if ( $session_all_day ) {
					// Use date fields
					$start_date_string = isset( $session['session_start_date'] ) ? (string) $session['session_start_date'] : '';
					$end_date_string = isset( $session['session_end_date'] ) ? (string) $session['session_end_date'] : '';
					
					if ( $start_date_string ) {
						$dt = DateTimeImmutable::createFromFormat( 'F j, Y', $start_date_string, $tz );
						if ( $dt ) {
							$session_timestamps[] = array(
								'start' => $dt->setTime( 0, 0, 0 )->getTimestamp(),
								'end' => $end_date_string ? yak_parse_date_string( $end_date_string, true, $tz ) : $dt->setTime( 23, 59, 59 )->getTimestamp(),
							);
						}
					}
				} else {
					// Use datetime fields
					$start_datetime_string = isset( $session['session_start_datetime'] ) ? (string) $session['session_start_datetime'] : '';
					$end_datetime_string = isset( $session['session_end_datetime'] ) ? (string) $session['session_end_datetime'] : '';
					
					if ( $start_datetime_string ) {
						$start_ts = yak_parse_datetime_string( $start_datetime_string, $tz );
						$end_ts = $end_datetime_string ? yak_parse_datetime_string( $end_datetime_string, $tz ) : $start_ts;
						
						if ( $start_ts ) {
							$session_timestamps[] = array(
								'start' => $start_ts,
								'end' => $end_ts,
							);
						}
					}
				}
			}
			
			if ( ! empty( $session_timestamps ) ) {
				// Sort by start time
				usort( $session_timestamps, function( $a, $b ) {
					return $a['start'] - $b['start'];
				});
				
				// Find next upcoming session
				foreach ( $session_timestamps as $session ) {
					if ( $session['start'] >= $now ) {
						// Found an upcoming session, use its start time
						return $session['start'];
					}
				}
				
				// All sessions are past, use the last session's end time
				$last_session = end( $session_timestamps );
				return $last_session['end'];
			}
		}
	}
	
	// Single event (not multi-session) - use main event fields
	$all_day = (bool) get_field( 'event_all_day', $post_id );
	$start_datetime_string = (string) get_field( 'event_start_date_time', $post_id );
	$start_date_string = (string) get_field( 'event_start_date', $post_id );
	
	$timestamp = 0;
	
	if ( $all_day && $start_date_string ) {
		$dt = DateTimeImmutable::createFromFormat( 'F j, Y', $start_date_string, $tz );
		if ( $dt instanceof DateTimeImmutable ) {
			$timestamp = $dt->setTime( 0, 0, 0 )->getTimestamp();
		} else {
			$ts = strtotime( $start_date_string );
			$timestamp = $ts ? (int) $ts : 0;
		}
	} elseif ( $start_datetime_string ) {
		$dt = DateTimeImmutable::createFromFormat( 'F j, Y g:i a', $start_datetime_string, $tz );
		if ( $dt instanceof DateTimeImmutable ) {
			$timestamp = $dt->getTimestamp();
		} else {
			$ts = strtotime( $start_datetime_string );
			$timestamp = $ts ? (int) $ts : 0;
		}
	}
	
	return $timestamp;
}

/**
 * Helper: Parse datetime string to unix timestamp
 * Properly handles WordPress timezone
 */
function yak_parse_datetime_string( $datetime_string, $tz ) {
	// Create datetime object in the site's timezone
	$dt = DateTimeImmutable::createFromFormat( 'F j, Y g:i a', $datetime_string, $tz );
	if ( $dt instanceof DateTimeImmutable ) {
		// getTimestamp() returns UTC timestamp, which is what we want
		// The timezone was already accounted for in createFromFormat
		return $dt->getTimestamp();
	}
	
	// Fallback: use strtotime
	$ts = strtotime( $datetime_string );
	if ( $ts ) {
		return (int) $ts;
	}
	
	return 0;
}

/**
 * Helper: Parse date string to unix timestamp
 * Properly handles WordPress timezone
 */
function yak_parse_date_string( $date_string, $end_of_day = false, $tz = null ) {
	if ( ! $tz ) {
		$tz = wp_timezone();
	}
	
	// Create date object in the site's timezone
	$dt = DateTimeImmutable::createFromFormat( 'F j, Y', $date_string, $tz );
	if ( $dt instanceof DateTimeImmutable ) {
		// Set time to start or end of day in the site's timezone
		$dt = $end_of_day ? $dt->setTime( 23, 59, 59 ) : $dt->setTime( 0, 0, 0 );
		return $dt->getTimestamp();
	}
	
	// Fallback: use strtotime
	$ts = strtotime( $date_string );
	if ( $ts ) {
		if ( $end_of_day ) {
			// Add 23 hours, 59 minutes, 59 seconds
			$ts += ( 24 * HOUR_IN_SECONDS ) - 1;
		}
		return (int) $ts;
	}
	
	return 0;
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
        
        // Parse dates for smarter formatting
        $start_obj = DateTime::createFromFormat( 'F j, Y', $start, wp_timezone() );
        $end_obj = DateTime::createFromFormat( 'F j, Y', $end, wp_timezone() );
        
        if ( ! $start_obj || ! $end_obj ) {
            // Fallback to original format if parsing fails
            return $start . ' – ' . $end;
        }
        
        // Same month and year: November 12-13, 2025
        if ( $start_obj->format('Y-m') === $end_obj->format('Y-m') ) {
            return $start_obj->format('F j') . '-' . $end_obj->format('j, Y');
        }
        
        // Different months, same year: November 12 – December 2, 2025
        if ( $start_obj->format('Y') === $end_obj->format('Y') ) {
            return $start_obj->format('F j') . ' – ' . $end_obj->format('F j, Y');
        }
        
        // Different years: December 30, 2024 – January 2, 2025
        return $start_obj->format('F j, Y') . ' – ' . $end_obj->format('F j, Y');
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
    wp_enqueue_script( 'clb-events-plugin-global-js', plugin_dir_url( __FILE__ ) . 'js/clb-events-plugin-global-js.js', array( 'jquery' ), '1.3.4', true );
    wp_localize_script( 'clb-events-plugin-global-js', 'dhmEvents', $dhmEventsArray );

    // enqueue calendar view JS
    wp_enqueue_script( 'clb-events-calendar-view-js', plugin_dir_url( __FILE__ ) . 'blocks/events_calendar/js/clb-events-calendar-view.js', array( 'jquery' ), '1.3.4', true );

    // return '<div id="dhm-events-root" class="clb-dhm-events-root"></div>';

}



/**
 * TEMPORARY: Debug/Reporting Page for Events Metadata
 * Can be accessed via shortcode [yak_events_debug] or via admin menu
 */

// Shortcode for front-end access (restricted to administrators)
add_shortcode('yak_events_debug', 'yak_events_debug_shortcode');
function yak_events_debug_shortcode($atts) {
    if (!current_user_can('administrator')) {
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
                            <li><strong>Timestamp Date (WP TZ):</strong> <?php echo wp_date('Y-m-d H:i:s', $unix_timestamp); ?></li>
                            <li><strong>Timestamp Date (UTC):</strong> <?php echo gmdate('Y-m-d H:i:s', $unix_timestamp); ?></li>
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


/* ==================================================================
 * TRANSIENT-BASED LAZY UPDATE SYSTEM FOR MULTI-SESSION EVENTS
 * ================================================================== */

/**
 * Get the configured cache interval (in seconds)
 * Default: 2 hours
 */
function yak_get_cache_interval() {
	$hours = get_option( 'yak_events_cache_hours', 2 );
	return absint( $hours ) * HOUR_IN_SECONDS;
}

/**
 * Check if timestamps need recalculation and trigger if needed
 * This is called from multiple trigger points
 */
function yak_maybe_trigger_timestamp_recalc() {
	// Check if transient exists
	$last_recalc = get_transient( 'yak_events_last_recalc' );
	
	if ( false === $last_recalc ) {
		// Transient expired or doesn't exist, trigger recalculation
		
		// Set transient immediately to prevent multiple simultaneous triggers
		$interval = yak_get_cache_interval();
		set_transient( 'yak_events_last_recalc', current_time( 'timestamp' ), $interval );
		
		// Log the trigger
		yak_log_event( 'Timestamp recalculation triggered', array(
			'interval_hours' => $interval / HOUR_IN_SECONDS,
			'next_recalc' => date( 'F j, Y g:i a', current_time( 'timestamp' ) + $interval ),
		));
		
		// Trigger async background process
		yak_trigger_async_recalc();
	}
}

/**
 * Trigger async background recalculation via admin-ajax
 */
function yak_trigger_async_recalc() {
	// Create a unique secret key for this specific recalculation
	$secret = wp_generate_password( 32, false );
	set_transient( 'yak_recalc_secret', $secret, 60 ); // Valid for 60 seconds
	
	// Trigger non-blocking background request
	wp_remote_post( admin_url( 'admin-ajax.php' ), array(
		'blocking' => false, // Don't wait for response
		'timeout' => 0.01,   // Return immediately
		'body' => array(
			'action' => 'yak_recalc_event_timestamps',
			'secret' => $secret,
		),
	));
}

/**
 * AJAX handler for background timestamp recalculation
 * This runs asynchronously and doesn't block page loads
 * Uses secret key verification (not nonce) because it's non-blocking
 */
add_action( 'wp_ajax_yak_recalc_event_timestamps', 'yak_recalc_timestamps_background' );
add_action( 'wp_ajax_nopriv_yak_recalc_event_timestamps', 'yak_recalc_timestamps_background' );

function yak_recalc_timestamps_background() {
	// Verify secret key (transient-based for non-blocking requests)
	$submitted_secret = isset( $_POST['secret'] ) ? $_POST['secret'] : '';
	$stored_secret = get_transient( 'yak_recalc_secret' );
	
	if ( ! $submitted_secret || $submitted_secret !== $stored_secret ) {
		yak_log_event( 'Background recalc failed: Invalid or expired secret key', array(
			'has_submitted' => ! empty( $submitted_secret ),
			'has_stored' => ! empty( $stored_secret ),
		) );
		wp_die();
	}
	
	// Delete the secret so it can't be reused
	delete_transient( 'yak_recalc_secret' );
	
	// Increase time limit for large sites
	set_time_limit( 120 );
	
	$start_time = microtime( true );
	
	// Get all events (prioritize multi-session events)
	$args = array(
		'numberposts' => -1,
		'post_type'   => 'events',
		'post_status' => 'publish',
		'fields'      => 'ids',
	);
	
	$event_ids = get_posts( $args );
	$updated_count = 0;
	$multi_session_count = 0;
	
	foreach ( $event_ids as $event_id ) {
		// Calculate new timestamp
		$new_timestamp = yak_calculate_event_unix_timestamp( $event_id );
		$old_timestamp = (int) get_field( 'event_unix_timestamp', $event_id );
		
		// Only update if changed
		if ( $new_timestamp !== $old_timestamp ) {
			update_field( 'field_6695707249b6a', (int) $new_timestamp, $event_id );
			$updated_count++;
			
			// Track multi-session events
			if ( get_field( 'event_has_sessions', $event_id ) ) {
				$multi_session_count++;
			}
		}
	}
	
	$end_time = microtime( true );
	$duration = round( $end_time - $start_time, 2 );
	
	// Log completion
	yak_log_event( 'Background timestamp recalculation completed', array(
		'total_events' => count( $event_ids ),
		'updated_events' => $updated_count,
		'multi_session_events' => $multi_session_count,
		'duration_seconds' => $duration,
	));
	
	wp_die(); // Important for AJAX
}


/* ==================================================================
 * TRIGGER POINTS
 * ================================================================== */

/**
 * Trigger #1: Front-end events block load
 */
add_action( 'wp_enqueue_scripts', 'yak_trigger_on_frontend_load', 999 );
function yak_trigger_on_frontend_load() {
	// Only trigger if on a page that likely has events
	if ( is_singular() || is_archive() || is_home() || is_front_page() ) {
		yak_maybe_trigger_timestamp_recalc();
	}
}

/**
 * Trigger #2: Admin events list view
 */
add_action( 'load-edit.php', 'yak_trigger_on_admin_list' );
function yak_trigger_on_admin_list() {
	global $typenow;
	
	if ( 'events' === $typenow ) {
		yak_maybe_trigger_timestamp_recalc();
	}
}

/**
 * Trigger #3: WordPress Heartbeat API (admin only)
 */
add_filter( 'heartbeat_received', 'yak_trigger_on_heartbeat', 10, 2 );
function yak_trigger_on_heartbeat( $response, $data ) {
	// Only in admin area
	if ( ! is_admin() ) {
		return $response;
	}
	
	// Only if on events screen
	$screen = get_current_screen();
	if ( $screen && ( 'events' === $screen->post_type || 'edit-events' === $screen->id ) ) {
		yak_maybe_trigger_timestamp_recalc();
	}
	
	return $response;
}


/* ==================================================================
 * LOGGING SYSTEM
 * ================================================================== */

/**
 * Log an event with context data
 */
function yak_log_event( $message, $context = array() ) {
	$max_logs = 100; // Keep last 100 log entries
	
	$logs = get_option( 'yak_events_log', array() );
	
	$log_entry = array(
		'timestamp' => current_time( 'timestamp' ),
		'datetime' => current_time( 'mysql' ),
		'message' => $message,
		'context' => $context,
	);
	
	// Add to beginning of array
	array_unshift( $logs, $log_entry );
	
	// Keep only last N entries
	$logs = array_slice( $logs, 0, $max_logs );
	
	update_option( 'yak_events_log', $logs, false ); // No autoload
}

/**
 * Clear the event log
 */
function yak_clear_event_log() {
	delete_option( 'yak_events_log' );
	yak_log_event( 'Event log cleared by admin' );
}


/* ==================================================================
 * SETTINGS PAGE
 * ================================================================== */

/**
 * Add settings page to WordPress admin (Administrator only)
 */
add_action( 'admin_menu', 'yak_add_settings_page' );
function yak_add_settings_page() {
	// Only show to administrators
	if ( ! current_user_can( 'administrator' ) ) {
		return;
	}
	
	add_submenu_page(
		'edit.php?post_type=events',           // Parent slug
		'Events Calendar Settings',             // Page title
		'Settings & Debug',                     // Menu title
		'administrator',                        // Capability (administrator only)
		'yak-events-settings',                  // Menu slug
		'yak_render_settings_page'              // Callback
	);
}

/**
 * Render the settings page (Administrator only)
 */
function yak_render_settings_page() {
	// Double-check administrator capability
	if ( ! current_user_can( 'administrator' ) ) {
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}
	
	// Handle form submissions
	if ( isset( $_POST['yak_clear_log'] ) && check_admin_referer( 'yak_clear_log' ) ) {
		yak_clear_event_log();
		echo '<div class="notice notice-success"><p>Event log cleared!</p></div>';
	}
	
	if ( isset( $_POST['yak_force_recalc'] ) && check_admin_referer( 'yak_force_recalc' ) ) {
		delete_transient( 'yak_events_last_recalc' );
		yak_trigger_async_recalc();
		echo '<div class="notice notice-success"><p>Timestamp recalculation triggered! Check the log below for results.</p></div>';
	}
	
	if ( isset( $_POST['yak_save_settings'] ) && check_admin_referer( 'yak_save_settings' ) ) {
		$cache_hours = absint( $_POST['yak_cache_hours'] );
		if ( $cache_hours < 1 ) {
			$cache_hours = 1;
		}
		if ( $cache_hours > 24 ) {
			$cache_hours = 24;
		}
		update_option( 'yak_events_cache_hours', $cache_hours );
		echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
	}
	
	// Get current settings
	$cache_hours = get_option( 'yak_events_cache_hours', 2 );
	$last_recalc = get_transient( 'yak_events_last_recalc' );
	$logs = get_option( 'yak_events_log', array() );
	
	?>
	<div class="wrap">
		<h1>🎯 Events Calendar Settings & Debug</h1>
		<p style="background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin-bottom: 20px;">
			<strong>⚠️ Administrator Only:</strong> This page is only visible to WordPress Administrators. 
			Other user roles (Editors, Authors, etc.) cannot access this page.
		</p>
		
		<!-- Settings Section -->
		<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
			<h2>⚙️ Cache Settings</h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'yak_save_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="yak_cache_hours">Timestamp Recalculation Interval</label>
						</th>
						<td>
							<input type="number" name="yak_cache_hours" id="yak_cache_hours" 
							       value="<?php echo esc_attr( $cache_hours ); ?>" 
							       min="1" max="24" step="1" style="width: 80px;">
							<span class="description">hours (between 1 and 24)</span>
							<p class="description">
								How often to recalculate event timestamps for multi-session events. 
								Lower values = more accurate but more server load. 
								Recommended: 2-3 hours.
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Last Recalculation</th>
						<td>
							<?php if ( $last_recalc ) : ?>
								<strong><?php echo wp_date( 'F j, Y g:i a', $last_recalc ); ?></strong>
								<p class="description">
									Next recalculation will trigger after: 
									<strong><?php echo wp_date( 'F j, Y g:i a', $last_recalc + yak_get_cache_interval() ); ?></strong>
								</p>
							<?php else : ?>
								<em>Never (will trigger on next page load)</em>
							<?php endif; ?>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="yak_save_settings" class="button button-primary" value="Save Settings">
				</p>
			</form>
			
			<hr>
			
			<h3>Manual Triggers</h3>
			<form method="post" action="" style="display: inline;">
				<?php wp_nonce_field( 'yak_force_recalc' ); ?>
				<input type="submit" name="yak_force_recalc" class="button button-secondary" 
				       value="🔄 Force Recalculation Now" 
				       onclick="return confirm('This will recalculate timestamps for all events in the background. Continue?');">
			</form>
			<p class="description">Force an immediate timestamp recalculation for all events (runs in background).</p>
		</div>
		
		<!-- Event Timestamps Debug -->
		<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
			<h2>📊 Event Timestamps</h2>
			<p>Current timestamp for each event (what WordPress uses for sorting):</p>
			<?php yak_render_event_timestamps_table(); ?>
		</div>
		
		<!-- Activity Log -->
		<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
			<h2>📋 Activity Log</h2>
			<p>Showing all stored log entries (system keeps last 100).</p>
			<form method="post" action="" style="margin-bottom: 15px;">
				<?php wp_nonce_field( 'yak_clear_log' ); ?>
				<input type="submit" name="yak_clear_log" class="button button-secondary" value="Clear Log">
			</form>
			
			<?php if ( empty( $logs ) ) : ?>
				<p><em>No log entries yet.</em></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th width="180">Timestamp</th>
							<th>Event</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $logs as $log ) : ?>
							<tr>
								<td><code><?php echo esc_html( $log['datetime'] ); ?></code></td>
								<td><strong><?php echo esc_html( $log['message'] ); ?></strong></td>
								<td>
									<?php if ( ! empty( $log['context'] ) ) : ?>
										<details>
											<summary style="cursor: pointer;">View details</summary>
											<pre style="margin-top: 10px; padding: 10px; background: #f5f5f5; overflow-x: auto;"><?php 
												echo esc_html( print_r( $log['context'], true ) ); 
											?></pre>
										</details>
									<?php else : ?>
										<em>No additional details</em>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p style="margin-top: 15px;"><em>Showing all <?php echo count( $logs ); ?> log entries.</em></p>
			<?php endif; ?>
		</div>
		
		<!-- Full Event Metadata Report -->
		<div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
			<h2>📝 Complete Event Metadata Report</h2>
			<p>Comprehensive view of all event data including date/time info, location, metadata, and sessions.</p>
			<?php echo yak_events_debug_output(); ?>
		</div>
	</div>
	<?php
}

/**
 * Render event timestamps debug table
 */
function yak_render_event_timestamps_table() {
	$args = array(
		'numberposts' => -1,
		'post_type'   => 'events',
		'post_status' => 'publish',
		'meta_key'    => 'event_unix_timestamp',
		'orderby'     => 'meta_value_num',
		'order'       => 'ASC',
	);
	
	$events = get_posts( $args );
	$now = current_time( 'timestamp' );
	
	if ( empty( $events ) ) {
		echo '<p><em>No events found.</em></p>';
		return;
	}
	
	?>
	<table class="widefat striped">
		<thead>
			<tr>
				<th>Event Title</th>
				<th>Type</th>
				<th>Unix Timestamp</th>
				<th>Human Readable Time</th>
				<th>Status</th>
				<th>Session Info</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $events as $event ) : 
				$timestamp = (int) get_field( 'event_unix_timestamp', $event->ID );
				$has_sessions = (bool) get_field( 'event_has_sessions', $event->ID );
				$is_past = $timestamp && $timestamp < $now;
				$sessions = $has_sessions ? get_field( 'event_sessions', $event->ID ) : array();
				$bg_color = $is_past ? '#ffebee' : '#e8f5e9';
			?>
				<tr style="background-color: <?php echo $bg_color; ?>;">
					<td>
						<strong><?php echo esc_html( $event->post_title ); ?></strong><br>
						<small><a href="<?php echo get_edit_post_link( $event->ID ); ?>" target="_blank">Edit</a></small>
					</td>
					<td>
						<?php if ( $has_sessions ) : ?>
							<span style="background: #2196f3; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px;">
								MULTI-SESSION
							</span>
						<?php else : ?>
							<span style="background: #9e9e9e; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px;">
								SINGLE
							</span>
						<?php endif; ?>
					</td>
					<td><code><?php echo $timestamp ? $timestamp : 'Not set'; ?></code></td>
					<td>
						<?php if ( $timestamp ) : ?>
							<strong><?php echo wp_date( 'F j, Y', $timestamp ); ?></strong><br>
							<span style="color: #666;"><?php echo wp_date( 'g:i a', $timestamp ); ?></span><br>
							<small style="color: #999;">UTC: <?php echo gmdate( 'Y-m-d H:i', $timestamp ); ?></small>
						<?php else : ?>
							<em>No timestamp</em>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( $is_past ) : ?>
							<span style="color: #d32f2f;">🔴 PAST</span>
						<?php else : ?>
							<span style="color: #388e3c;">🟢 UPCOMING</span>
						<?php endif; ?>
					</td>
					<td>
						<?php if ( $has_sessions && ! empty( $sessions ) ) : ?>
							<details>
								<summary style="cursor: pointer;">
									<?php echo count( $sessions ); ?> session(s)
								</summary>
								<ul style="margin-top: 8px; font-size: 12px;">
									<?php foreach ( $sessions as $i => $session ) : 
										$session_all_day = isset( $session['session_all_day'] ) ? (bool) $session['session_all_day'] : false;
										
										if ( $session_all_day ) {
											$start_str = isset( $session['session_start_date'] ) ? $session['session_start_date'] : '';
											$end_str = isset( $session['session_end_date'] ) ? $session['session_end_date'] : '';
										} else {
											$start_str = isset( $session['session_start_datetime'] ) ? $session['session_start_datetime'] : '';
											$end_str = isset( $session['session_end_datetime'] ) ? $session['session_end_datetime'] : '';
										}
										
										$desc = isset( $session['session_description'] ) ? $session['session_description'] : '';
									?>
										<li>
											<strong>Session <?php echo $i + 1; ?>:</strong> 
											<?php echo esc_html( $start_str ); ?>
											<?php if ( $end_str && $end_str !== $start_str ) : ?>
												→ <?php echo esc_html( $end_str ); ?>
											<?php endif; ?>
											<?php if ( $session_all_day ) : ?>
												<em>(all day)</em>
											<?php endif; ?>
											<?php if ( $desc ) : ?>
												<br><em><?php echo esc_html( wp_trim_words( $desc, 10 ) ); ?></em>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</details>
						<?php else : ?>
							<em>—</em>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}
