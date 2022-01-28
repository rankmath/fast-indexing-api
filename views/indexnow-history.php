<?php
/**
 * IndexNow History page contents.
 *
 * @package Instant Indexing
 */

?>
<div class="wrap rank-math-wrap">
	<h1><?php esc_attr_e( 'IndexNow History', 'fast-indexing-api' ); ?></h1>
	<p class="description"><?php esc_attr_e( 'Here you can see a list of the most recent IndexNow URL submissions, and their status.', 'fast-indexing-api' ); ?></p>

	<?php

	$history_content  = '';
	$history_content .= '<a href="#" id="indexnow_clear_history" class="button alignright hidden">' . esc_html__( 'Clear History', 'fast-indexing-api' ) . '</a>';
	$history_content .= '<div class="history-filter-links hidden" id="indexnow_history_filters"><a href="#" data-filter="all" class="current">' . esc_html__( 'All', 'fast-indexing-api' ) . '</a> | <a href="#" data-filter="manual">' . esc_html__( 'Manual', 'fast-indexing-api' ) . '</a> | <a href="#" data-filter="auto">' . esc_html__( 'Auto', 'fast-indexing-api' ) . '</a></div>';
	$history_content .= '<div class="clear"></div>';
	$history_content .= '<table class="wp-list-table widefat striped" id="indexnow_history"><thead><tr><th class="col-date">' . esc_html__( 'Time', 'fast-indexing-api' ) . '</th><th class="col-url">' . esc_html__( 'URL', 'fast-indexing-api' ) . '</th><th class="col-status">' . esc_html__( 'Response', 'fast-indexing-api' ) . '</th></tr></thead><tbody>';

	$result = get_option( 'rank_math_indexnow_log', array() );
	foreach ( $result as $key => $value ) {
		$result[ $key ]['timeFormatted'] = wp_date( 'Y-m-d H:i:s', $value['time'] );
		// Translators: placeholder is human-readable time, e.g. "1 hour".
		$result[ $key ]['timeHumanReadable'] = sprintf( __( '%s ago', 'fast-indexing-api' ), human_time_diff( $value['time'] ) );

		if ( 'manual' === $filter && empty( $result[ $key ]['manual_submission'] ) ) {
			unset( $result[ $key ] );
		} elseif ( 'auto' === $filter && ! empty( $result[ $key ]['manual_submission'] ) ) {
			unset( $result[ $key ] );
		}
	}
	$result = array_values( array_reverse( $result ) );
	if ( ! empty( $result ) ) {
		foreach ( $result as $value ) {
			$history_content .= '<tr class="' . ( ! empty( $value['manual_submission'] ) ? 'manual' : 'auto' ) . '"><td class="col-date">' . $value['timeFormatted'] . '<br /><span class="time-human-readable">' . $value['timeHumanReadable'] . '</span></td><td class="col-url">' . $value['url'] . '</td><td class="col-status">' . $value['status'] . '</td></tr>';
		}
	} else {
		$history_content .= '<tr><td colspan="3">' . esc_html__( 'No submissions yet.', 'fast-indexing-api' ) . '</td></tr>';
	}

	$history_content .= '</tbody></table>';

	echo wp_kses_post( $history_content );

	// Print a clear history button.
	$nonce = wp_create_nonce( 'giapi-clear-history' );
	echo '<p><a href="' . add_query_arg( array( 'clear_indexnow_history' => '1', '_wpnonce' => wp_create_nonce( 'giapi_clear_history' ) ) ) . '" id="indexnow_clear_history" class="button alignright">' . esc_html__( 'Clear History', 'fast-indexing-api' ) . '</a></p>';

	?>

</div>
