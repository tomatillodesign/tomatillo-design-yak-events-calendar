<?php
/**
 * Events Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 */

$block_to_publish = null;
// print_r($block);
// print_r($block['attributes']);
// print_r($block['data']);

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'clb-events-wrapper';
if ( ! empty( $block['className'] ) ) {
    $class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
    $class_name .= ' align' . $block['align'];
}

// Load values and assign defaults.
$now = strtotime('now');
$event_view = 'list';
$event_query_type = get_field('event_query_type');
$event_query_results_past = get_field('event_query_results_past');
$event_query_results_upcoming = get_field('event_query_results_upcoming');
$number_of_events_to_show = get_field('number_of_events_to_show');

$class_name .= ' clb-event-view-' . $event_view . ' clb-event-query-type-' . $event_query_type;

$event_category_id_array = get_field('event_category');
if( $event_category_id_array ) {
    if (!is_array($event_category_id_array)) { $event_category_id_array = [$event_category_id_array]; }
}

// query vars
$order = 'ASC';
if( $event_query_type == 'past' ) { $order = 'DESC'; }
$num_events = -1;
$event_counter = 1;

// if( $event_query_results_upcoming == 'num' && $number_of_events_to_show ) { $num_events = intval($number_of_events_to_show); }
// print_r($event_query_results_upcoming);
// print_r($num_events);


// if event category selection
if( $event_query_type == 'upcoming' && $event_category_id_array ) {

    $args = array(
        'numberposts' => $num_events,
        'post_type'   => 'events',
        'tax_query' => array(
        array(
            'taxonomy' => 'event_categories',
            'field'    => 'term_id',
            'terms'    => $event_category_id_array
            )
        ),
        'order'          => $order,
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_unix_timestamp',
        'fields' => 'ids'
    );

} 

if( !$event_category_id_array ) {

    $args = array(
        'numberposts' => $num_events,
        'post_type'   => 'events',
        'order'          => $order,
        'orderby'        => 'meta_value',
        'meta_key'       => 'event_unix_timestamp',
        'fields' => 'ids'
    );

}

$event_ids = get_posts( $args );

foreach( $event_ids as $post_id ) {

    //////// CONDITIONALS /////////////////////////////////////////////////////////////////
    // conditional for which events to skip over
    $event_unix_timestamp = get_field('event_unix_timestamp', $post_id);
    if( ($event_query_type == 'upcoming') && ($event_unix_timestamp < $now) ) { continue; }
    if( ($event_query_type == 'past') && ($event_unix_timestamp > $now) ) { continue; }

    // once we have reached the correct number of events, skip the rest
    if( $event_query_results_upcoming != 'all' && $event_query_type != 'past' ) {
        if( $number_of_events_to_show < 0 ) { $number_of_events_to_show = 999; }
        if( $event_counter > $number_of_events_to_show ) { continue; }
    }

    if( $event_query_type == 'past' && $event_query_results_past == 'this_year' ) {
        $start_of_this_year = strtotime('first day of January this year 00:00:00');
        if( $event_unix_timestamp < $start_of_this_year ) { continue; }
    }

    //////// END CONDITIONALS /////////////////////////////////////////////////////////////////

    $block_to_publish .= clb_get_event( $post_id ); // see helper function out at main plugin php
    $event_counter++;

}

$block_to_publish = '<div class="' . $class_name . '">' . $block_to_publish . '</div>';
echo $block_to_publish;
