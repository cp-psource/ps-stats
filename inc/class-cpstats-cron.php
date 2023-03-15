<?php
/**
 * CPStats: CPStats_Cron class
 *
 * This file contains the derived class for the plugin's cron features.
 *
 * @package   CPStats
 * @since     1.4.0
 */

// Quit if accessed outside WP context.
defined( 'ABSPATH' ) || exit;

/**
 * CPStats_Cron
 *
 * @since 1.4.0
 */
class CPStats_Cron extends CPStats {

	/**
	 * Cleanup obsolete DB values
	 *
	 * @since    0.3.0
	 * @version  1.4.0
	 */
	public static function cleanup_data() {

		// Global.
		global $wpdb;

		// Remove items.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$wpdb->cpstats` WHERE created <= SUBDATE(%s, %d)",
				current_time( 'Y-m-d' ),
				(int) self::$_options['days']
			)
		);

		// Optimize DB.
		$wpdb->query(
			"OPTIMIZE TABLE `$wpdb->cpstats`"
		);
	}
}
