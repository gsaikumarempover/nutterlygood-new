<?php

if ( ! function_exists( 'greenpath_core_get_instagram_feed_list' ) ) {
	/**
	 * Function that return formatted list of instagram feeds
	 *
	 * @return array
	 */
	function greenpath_core_get_instagram_feed_list() {
		global $wpdb;

		$options = array();

		$feed_table = $wpdb->prefix . 'sbi_feeds';
		$feed_rows  = $wpdb->get_results( "SELECT * FROM {$feed_table}", ARRAY_A );

		foreach ( $feed_rows as $feed_row ) {
			$options[$feed_row['id']] = $feed_row['feed_name'];
		}

		return $options;
	}
}
