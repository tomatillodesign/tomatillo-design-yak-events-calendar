<?php
/**
 * Events Calendar Monthly View Block Template.
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


$admin_cells = null;
if ( is_admin() && wp_doing_ajax() ) {
	// Get current month details
	$now = new DateTime();
	$current_month = $now->format('F Y');
	$first_day = new DateTime($now->format('Y-m-01'));
	$last_day = new DateTime($now->format('Y-m-t'));
	$first_day_of_week = (int)$first_day->format('w'); // 0 = Sunday
	$days_in_month = (int)$first_day->format('t');
	
	// Get events for this month
	$args = array(
		'numberposts' => -1,
		'post_type'   => 'events',
		'post_status' => 'publish',
		'meta_key'    => 'event_unix_timestamp',
		'order'       => 'ASC',
		'orderby'     => 'meta_value_num',
	);
	$events = get_posts($args);
	
	// Organize events by day
	$events_by_day = array();
	foreach ($events as $event) {
		$has_sessions = get_field('event_has_sessions', $event->ID);
		
		if ($has_sessions) {
			// Multi-session event - get all sessions
			$sessions = get_field('event_sessions', $event->ID);
			if ($sessions && is_array($sessions)) {
				foreach ($sessions as $session) {
					$session_all_day = isset($session['session_all_day']) ? $session['session_all_day'] : false;
					$session_start = $session_all_day ? 
						(isset($session['session_start_date']) ? $session['session_start_date'] : '') : 
						(isset($session['session_start_datetime']) ? $session['session_start_datetime'] : '');
					
					if ($session_start) {
						$session_date = new DateTime($session_start);
						// Only include if in current month
						if ($session_date >= $first_day && $session_date <= $last_day) {
							$day_num = (int)$session_date->format('j');
							if (!isset($events_by_day[$day_num])) {
								$events_by_day[$day_num] = array();
							}
							
							// Format time for display
							$time_display = '';
							if (!$session_all_day) {
								$time_display = $session_date->format('g:i a');
							}
							
							$events_by_day[$day_num][] = array(
								'title' => get_the_title($event->ID),
								'url' => get_permalink($event->ID),
								'time' => $time_display
							);
						}
					}
				}
			}
		} else {
			// Single event (no sessions)
			$event_all_day = get_field('event_all_day', $event->ID);
			$event_start = $event_all_day ? get_field('event_start_date', $event->ID) : get_field('event_start_date_time', $event->ID);
			
			if ($event_start) {
				$event_date = new DateTime($event_start);
				// Only include if in current month
				if ($event_date >= $first_day && $event_date <= $last_day) {
					$day_num = (int)$event_date->format('j');
					if (!isset($events_by_day[$day_num])) {
						$events_by_day[$day_num] = array();
					}
					
					// Format time for display
					$time_display = '';
					if (!$event_all_day) {
						$time_display = $event_date->format('g:i a');
					}
					
					$events_by_day[$day_num][] = array(
						'title' => get_the_title($event->ID),
						'url' => get_permalink($event->ID),
						'time' => $time_display
					);
				}
			}
		}
	}
	
	// Build calendar
	$admin_cells .= '<div class="yak-calendar-admin-preview">';
	$admin_cells .= '<div class="yak-calendar-month-title">' . $current_month . '</div>';
	$admin_cells .= '<div class="yak-calendar-grid">';
	
	// Day headers
	$days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	foreach ($days as $day) {
		$admin_cells .= '<div class="yak-calendar-day-header">' . $day . '</div>';
	}
	
	// Calendar cells
	$day_counter = 1;
	for ($row = 0; $row < 5; $row++) {
		for ($col = 0; $col < 7; $col++) {
			$cell_index = $row * 7 + $col;
			
			if (($row === 0 && $col < $first_day_of_week) || $day_counter > $days_in_month) {
				// Empty cell
				$admin_cells .= '<div class="yak-calendar-cell yak-calendar-empty"></div>';
			} else {
				// Day cell
				$admin_cells .= '<div class="yak-calendar-cell">';
				$admin_cells .= '<span class="yak-calendar-date">' . $day_counter . '</span>';
				
				// Add events for this day
				if (isset($events_by_day[$day_counter])) {
					foreach ($events_by_day[$day_counter] as $evt) {
						$event_text = esc_html($evt['title']);
						if (!empty($evt['time'])) {
							$event_text = '<span class="yak-calendar-event-time">' . esc_html($evt['time']) . '</span> ' . $event_text;
						}
						$admin_cells .= '<div class="yak-calendar-event">' . $event_text . '</div>';
					}
				}
				
				$admin_cells .= '</div>';
				$day_counter++;
			}
		}
	}
	
	$admin_cells .= '</div>';
	$admin_cells .= '<div class="yak-calendar-notice">Admin preview - fully interactive on front-end</div>';
	$admin_cells .= '</div>';
}


$block_to_publish = '<div class="' . $class_name . '">' . $block_to_publish . '<div id="clb-events-calendar-view-root">' . $admin_cells . '</div></div>';
echo $block_to_publish;



