<?php
/**
 * CPStats: CPStats_Deactivate class
 *
 * This file contains the derived class for the plugin's deactivation actions.
 *
 * @package   CPStats
 * @since     1.4.0
 */

// Quit if accessed outside WP context.
defined( 'ABSPATH' ) || exit;

/**
 * CPStats_Deactivate
 *
 * @since 1.4.0
 */
class CPStats_Deactivate {

	/**
	 * Plugin deactivation actions
	 *
	 * @since    1.4.0
	 * @version  1.4.0
	 */
	public static function init() {

		// Delete transients.
		delete_transient( 'cpstats_data' );

		// Delete cron event.
		wp_clear_scheduled_hook( 'cpstats_cleanup' );
	}
}
