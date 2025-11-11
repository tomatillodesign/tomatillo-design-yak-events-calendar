<?php


// Remove typical Genesis stuff
//* Remove the entry meta in the entry header (requires HTML5 theme support)
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );


// show event meta info below title
add_action('genesis_entry_header', 'clb_show_event_info', 12);
function clb_show_event_info() {

    $post_id = get_the_ID();
    $event_metabox = null;
    $custom_classes = null;

    $is_featured_event = get_field('is_featured_event', $post_id);
    if( $is_featured_event ) { $custom_classes .= ' event-is-featured'; }

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
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Location:</strong> Online</div>';
    } elseif( $event_location) {
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Location:</strong> ' . $event_location . '</div>';
    }

    $custom_taxonomies = [];

    $event_categories = get_the_term_list( $post_id, 'event_categories', '<div class="nugget-desc clb-resource-info-line"><strong>Event Category:</strong> ', ', ', '</div>' );
    if( !is_wp_error( $event_categories ) ) {
        $event_categories = strip_tags( $event_categories, '<div>, <i>, <li>, <ul>, <strong>' );
        $event_metabox .= '<div class="clb-event-custom-taxonomies-wrapper">' . $event_categories . '</div>';
    }

    // is this event past?
    $event_warning = null;
    $end_timestamp = get_field('event_unix_timestamp', $post_id);
    // For events with end dates, we should check the end date instead
    if( $event_all_day ) {
        $event_end_date = get_field('event_end_date', $post_id);
        if( $event_end_date ) {
            $end_timestamp = strtotime($event_end_date . ' 23:59:59');
        }
    } else {
        $event_end_date_time = get_field('event_end_date_time', $post_id);
        if( $event_end_date_time ) {
            $end_timestamp = strtotime($event_end_date_time);
        }
    }
    if( $end_timestamp && $end_timestamp < strtotime('now') ) {
        $event_warning = '<div class="clb-event-now-over-warning">This event is now over</div>';
    }

    if( $event_metabox ) {
        echo $event_warning . '<div class="clb-event-metabox-wrapper' . $custom_classes . '">' . $event_metabox . '</div>';
    }
    
    // Display event sessions if they exist
    $event_sessions = get_field('event_sessions', $post_id);
    if( $event_sessions && is_array($event_sessions) ) {
        echo '<div class="clb-event-sessions-wrapper">';
        echo '<h3 class="clb-event-sessions-title">Event Schedule</h3>';
        echo '<div class="clb-event-sessions-list">';
        
        foreach( $event_sessions as $session ) {
            $session_all_day = $session['session_all_day'] ?? false;
            $session_desc = $session['session_description'] ?? '';
            
            // Handle all-day vs timed sessions
            if( $session_all_day ) {
                $session_start = $session['session_start_date'] ?? '';
                $session_end = $session['session_end_date'] ?? '';
                if( $session_start ) {
                    $formatted_session = yak_format_event_date_range( $session_start, $session_end, true );
                    
                    echo '<div class="clb-single-session">';
                    echo '<div class="clb-session-datetime">' . esc_html($formatted_session) . '</div>';
                    if( $session_desc ) {
                        echo '<div class="clb-session-description">' . esc_html($session_desc) . '</div>';
                    }
                    echo '</div>';
                }
            } else {
                $session_start = $session['session_start_datetime'] ?? '';
                $session_end = $session['session_end_datetime'] ?? '';
                if( $session_start && $session_end ) {
                    $formatted_session = yak_format_event_date_range( $session_start, $session_end, false );
                    
                    echo '<div class="clb-single-session">';
                    echo '<div class="clb-session-datetime">' . esc_html($formatted_session) . '</div>';
                    if( $session_desc ) {
                        echo '<div class="clb-session-description">' . esc_html($session_desc) . '</div>';
                    }
                    echo '</div>';
                }
            }
        }
        
        echo '</div>';
        echo '</div>';
    }

}



add_action('genesis_entry_content', 'clb_inject_events_featured_image_floater', 8);
function clb_inject_events_featured_image_floater() {

    $post_id = get_the_ID();
    $featured_image = get_the_post_thumbnail($post_id, 'full');

    if( !$featured_image ) { return; }

    echo '<div class="clb-yak-events-featured-img-wrapper">' . $featured_image . '</div>';

}



// action button at bottom of entry content
// single resource metabox
add_action('genesis_entry_content', 'clb_publish_action_btn', 12);
function clb_publish_action_btn() {

    $event_action_btn = null;
    $event_action_button_array = get_field('event_action_button');
    $button_text = $event_action_button_array['button_text'];
    $button_link_url = $event_action_button_array['button_link_url'];

    if( $button_text && $button_link_url ) {
        $event_action_btn = '<div class="clb-event-action-btn-wrapper clb-entry-content-event-action-btn"><a href="' . $button_link_url . '" class="button full">' . $button_text . '</a></div>';
    }

    // check if event is in the past ////////
    $post_id = get_the_ID();
    $event_all_day = get_field('event_all_day', $post_id);
    $end_timestamp = 0;
    
    if( $event_all_day ) {
        $event_end_date = get_field('event_end_date', $post_id);
        if( $event_end_date ) {
            $end_timestamp = strtotime($event_end_date . ' 23:59:59');
        }
    } else {
        $event_end_date_time = get_field('event_end_date_time', $post_id);
        if( $event_end_date_time ) {
            $end_timestamp = strtotime($event_end_date_time);
        }
    }
    
    if( $end_timestamp && $end_timestamp < strtotime('now') ) {
        $event_action_btn = '<button href="#" class="button" disabled>' . $button_text . '</button>';
    }

    echo $event_action_btn;

}



genesis();