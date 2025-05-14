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

    $event_start_date_time = get_field('event_start_date_time', $post_id);
    $event_end_date_time = get_field('event_end_date_time', $post_id);

    $is_featured_event = get_field('is_featured_event', $post_id);
    if( $is_featured_event ) { $custom_classes .= ' event-is-featured'; }

    if( $event_start_date_time && $event_end_date_time ) { 
        $event_metabox .= '<div class="clb-event-info-item-wrapper"><strong>Date:</strong> ' . $event_start_date_time . ' – ' . $event_end_date_time . '</div>';
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
    if( strtotime($event_end_date_time) < strtotime('now') ) {
        $event_warning = '<div class="clb-event-now-over-warning">This event is now over</div>';
    }

    if( $event_metabox ) {
        echo $event_warning . '<div class="clb-event-metabox-wrapper' . $custom_classes . '">' . $event_metabox . '</div>';
    }
    

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
    $event_end_date_time = get_field('event_end_date_time');
    if( strtotime($event_end_date_time) < strtotime('now') ) {
        $event_action_btn = '<button href="#" class="button" disabled>' . $button_text . '</button>';
    }

    echo $event_action_btn;

}



genesis();