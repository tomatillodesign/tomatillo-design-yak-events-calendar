<?php
/*
Plugin Name: Tomatillo Design ~ Yak Events Calendar
Description: Custom Events Calendar for WordPress with CPT + Block. Requires ACF installed and activated.
Author: Chris Liu-Beers, Tomatillo Design
Author URI: http://www.tomatillodesign.com
Version: 1.0
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
		array(
			'key' => 'field_66956f15f7c61',
			'label' => 'Event Start Date & Time',
			'name' => 'event_start_date_time',
			'aria-label' => '',
			'type' => 'date_time_picker',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '25',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'F j, Y g:i a',
			'return_format' => 'F j, Y g:i a',
			'first_day' => 0,
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
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '25',
				'class' => '',
				'id' => '',
			),
			'display_format' => 'F j, Y g:i a',
			'return_format' => 'F j, Y g:i a',
			'first_day' => 0,
			'allow_in_bindings' => 1,
		),
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
				'width' => '25',
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
				'width' => '25',
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

// Update DHM Event unix timestamp on save
add_action('acf/save_post', 'my_acf_save_post_update_dhm_event_unix_timestamp');
function my_acf_save_post_update_dhm_event_unix_timestamp( $post_id ) {

    // Get newly saved values.
    $event_start_date_time = get_field( 'event_start_date_time', $post_id );

    // Save a basic text value.
    $field_key = "field_6695707249b6a";
    $value = strtotime($event_start_date_time);
    $value = intval($value);
    update_field( $field_key, $value, $post_id );

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

    $event_start_date_time = get_field('event_start_date_time', $post_id);
    $event_end_date_time = get_field('event_end_date_time', $post_id);

    if( $event_start_date_time && $event_end_date_time ) { 
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Date:</strong> ' . $event_start_date_time . ' – ' . $event_end_date_time . '</div>';
    }

    // event gathering mode: online or in-person
    $gathering_mode = get_field('gathering_mode', $post_id);
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

        $dhmEventsArray[] = array(
            'id' => $post_id,
            'title' => get_the_title( $post_id ),
            'permalink'   => get_the_permalink( $post_id ),
            'event_start_date_time' => get_field('event_start_date_time', $post_id),
            'event_end_date_time' => get_field('event_end_date_time', $post_id),
            'event_time_zone' => get_field('event_time_zone', $post_id),
            'gathering_mode' => get_field('gathering_mode', $post_id),
            'event_location' => get_field('event_location', $post_id),
            'clb_dhm_event_unix_timestamp' => intval(get_field('clb_dhm_event_unix_timestamp', $post_id)),
       );
       
    }

    // enqueue JS
    wp_enqueue_script( 'clb-events-plugin-global-js', plugin_dir_url( __FILE__ ) . 'js/clb-events-plugin-global-js.js', array( 'jquery' ), '1.0.0', true );
    wp_localize_script( 'clb-events-plugin-global-js', 'dhmEvents', $dhmEventsArray );

    // return '<div id="dhm-events-root" class="clb-dhm-events-root"></div>';

}
