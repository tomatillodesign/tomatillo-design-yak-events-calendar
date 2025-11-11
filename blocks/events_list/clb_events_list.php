<?php
/**
 * Events Block Template (with Featured Events section).
 *
 * Adds a "Featured Events" section at the top of the list.
 * - Featured flag: event_is_featured | event_featured | is_featured | featured (truthy).
 * - Featured events are rendered first, do NOT count against normal list limit, and are not duplicated below.
 * - Injects per-card classes: "clb-event--featured clb-event--id-{ID}" into the root element of each featured card.
 */

$block_to_publish = null;

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'clb-events-wrapper';
if ( ! empty( $block['className'] ) ) {
	$class_name .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$class_name .= ' align' . $block['align'];
}

// Load values and assign defaults.
$now                         = strtotime( 'now' );
$event_view                  = 'list';
$event_query_type            = get_field( 'event_query_type' );
$event_query_results_past    = get_field( 'event_query_results_past' );
$event_query_results_upcoming= get_field( 'event_query_results_upcoming' );
$number_of_events_to_show    = get_field( 'number_of_events_to_show' );

$class_name .= ' clb-event-view-' . $event_view . ' clb-event-query-type-' . $event_query_type;

$event_category_id_array = get_field( 'event_category' );
if ( $event_category_id_array && ! is_array( $event_category_id_array ) ) {
	$event_category_id_array = [ $event_category_id_array ];
}

// Query vars
$order        = ( $event_query_type === 'past' ) ? 'DESC' : 'ASC';
$num_events   = -1; // fetch all; we’ll handle limits manually
$regular_count= 1;

// Build base query args
if ( $event_query_type === 'upcoming' && $event_category_id_array ) {
	$args = [
		'numberposts' => $num_events,
		'post_type'   => 'events',
		'tax_query'   => [
			[
				'taxonomy' => 'event_categories',
				'field'    => 'term_id',
				'terms'    => $event_category_id_array,
			],
		],
		'order'       => $order,
		'orderby'     => 'meta_value',
		'meta_key'    => 'event_unix_timestamp',
		'fields'      => 'ids',
	];
} else {
	$args = [
		'numberposts' => $num_events,
		'post_type'   => 'events',
		'order'       => $order,
		'orderby'     => 'meta_value',
		'meta_key'    => 'event_unix_timestamp',
		'fields'      => 'ids',
	];
}

$event_ids = get_posts( $args );

// --- helper: inject classes into the root element of card markup (no dependency on clb_get_event) ---
if ( ! function_exists( 'clb_inject_root_classes' ) ) {
	/**
	 * Safely inject additional classes into the FIRST root element of an HTML fragment.
	 * - If a class attribute exists on the first tag, append.
	 * - Else, add a class attribute.
	 */
	function clb_inject_root_classes( $html, $classes ) {
		if ( ! $html ) { return $html; }
		$classes = trim( preg_replace( '/\s+/', ' ', $classes ) );
		// Append to existing class=""
		$updated = preg_replace(
			'/^<([a-zA-Z0-9\-]+)(\s[^>]*\sclass=("|\'))([^"\']*)(\3)([^>]*)>/',
			'<$1$2class=$3$4 ' . $classes . '$5$6>',
			$html,
			1,
			$did_append
		);
		if ( $did_append ) {
			return $updated;
		}
		// No class attr found on root; add one
		$updated = preg_replace(
			'/^<([a-zA-Z0-9\-]+)(\s*)([^>]*)>/',
			'<$1 class="' . $classes . '"$2$3>',
			$html,
			1,
			$did_add
		);
		return $did_add ? $updated : $html;
	}
}

// Buckets
$featured_html = '';
$regular_html  = '';

// Iterate in query order and bucket events
foreach ( $event_ids as $post_id ) {

	// --------- CONDITIONALS ----------
	$event_unix_timestamp = (int) get_field( 'event_unix_timestamp', $post_id );
	$has_sessions = get_field( 'event_has_sessions', $post_id );
	
	// For multi-session events, check if ANY session is upcoming/past
	if ( $has_sessions ) {
		$sessions = get_field( 'event_sessions', $post_id );
		$has_future_session = false;
		$has_past_session = false;
		
		if ( $sessions && is_array( $sessions ) ) {
			foreach ( $sessions as $session ) {
				$session_all_day = $session['session_all_day'] ?? false;
				
				// Get appropriate date field based on all-day status
				if ( $session_all_day ) {
					$session_start = $session['session_start_date'] ?? '';
				} else {
					$session_start = $session['session_start_datetime'] ?? '';
				}
				
				if ( $session_start ) {
					$session_timestamp = strtotime( $session_start );
					if ( $session_timestamp >= $now ) {
						$has_future_session = true;
					} else {
						$has_past_session = true;
					}
				}
			}
		}
		
		// Filter based on query type
		if ( $event_query_type === 'upcoming' && ! $has_future_session ) { continue; }
		if ( $event_query_type === 'past' && ! $has_past_session ) { continue; }
		
		// Past-year limiter for multi-session events
		if ( $event_query_type === 'past' && $event_query_results_past === 'this_year' ) {
			$start_of_this_year = strtotime( 'first day of January this year 00:00:00' );
			$has_session_this_year = false;
			foreach ( $sessions as $session ) {
				$session_all_day = $session['session_all_day'] ?? false;
				
				// Get appropriate date field based on all-day status
				if ( $session_all_day ) {
					$session_start = $session['session_start_date'] ?? '';
				} else {
					$session_start = $session['session_start_datetime'] ?? '';
				}
				
				if ( $session_start ) {
					$session_timestamp = strtotime( $session_start );
					if ( $session_timestamp >= $start_of_this_year ) {
						$has_session_this_year = true;
						break;
					}
				}
			}
			if ( ! $has_session_this_year ) { continue; }
		}
	} else {
		// Standard event filtering
		// upcoming vs past filters
		if ( ( $event_query_type === 'upcoming' ) && ( $event_unix_timestamp < $now ) ) { continue; }
		if ( ( $event_query_type === 'past' )     && ( $event_unix_timestamp > $now ) ) { continue; }

		// past-year limiter
		if ( $event_query_type === 'past' && $event_query_results_past === 'this_year' ) {
			$start_of_this_year = strtotime( 'first day of January this year 00:00:00' );
			if ( $event_unix_timestamp < $start_of_this_year ) { continue; }
		}
	}
	// --------- END CONDITIONALS ----------

	// Determine featured (support a few common meta keys)
	$is_featured = false;
	foreach ( [ 'is_featured_event' ] as $mk ) {
		$val = get_field( $mk, $post_id );
		if ( $val ) { $is_featured = true; break; }
	}

	if ( $is_featured ) {
		// Render normally, then inject featured classes
		$card = clb_get_event( $post_id ); // existing helper
		$card = clb_inject_root_classes( $card, 'clb-single-event-wrapper clb-event--featured clb-event--id-' . (int) $post_id );
		$featured_html .= $card;
		continue; // don’t let featured bleed into regular list
	}

	// REGULAR list respecting upcoming count limits (featured never counts against limit)
	if ( $event_query_results_upcoming !== 'all' && $event_query_type !== 'past' ) {
		if ( $number_of_events_to_show < 0 ) { $number_of_events_to_show = 999; }
		if ( $regular_count > (int) $number_of_events_to_show ) { continue; }
	}

	$regular_html .= clb_get_event( $post_id );
	$regular_count++;
}

// Build final output
ob_start();
?>
<div class="<?php echo esc_attr( $class_name ); ?>">
	<?php if ( $featured_html ) : ?>
		<section class="clb-events-featured" role="region" aria-labelledby="clb-events-featured-title">
			<h3 id="clb-events-featured-title" class="yak-section-heading">Featured Events</h3>
			<div class="clb-events-featured__grid">
				<?php echo $featured_html; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="clb-events-list" role="region" aria-labelledby="clb-events-list-title">
        <?php if ( $featured_html ) : ?>
            <h3 id="clb-events-list-title" class="yak-section-heading">All Events</h3>
        <?php else : ?>
            <h3 id="clb-events-list-title" class="screen-reader-text">All Events</h3>
        <?php endif; ?>
		<div class="clb-events-list__grid">
			<?php echo $regular_html; ?>
		</div>
	</section>
</div>
<?php
echo ob_get_clean();
