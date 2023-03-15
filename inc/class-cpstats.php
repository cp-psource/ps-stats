<?php
/**
 * CPStats: CPStats class
 *
 * This file contains the plugin's base class.
 *
 * @package   CPStats
 * @since     0.1.0
 */

// Quit if accessed outside WP context.
defined( 'ABSPATH' ) || exit;

/**
 * CPStats.
 *
 * @since 0.1.0
 */
class CPStats {
	const TRACKING_METHOD_DEFAULT = 0;
	const TRACKING_METHOD_JAVASCRIPT_WITH_NONCE_CHECK = 1;
	const TRACKING_METHOD_JAVASCRIPT_WITHOUT_NONCE_CHECK = 2;

	/**
	 * Plugin options.
	 *
	 * @since  1.4.0
	 * @var    array $_options
	 */
	public static $_options;

	/**
	 * Plugin initialization.
	 *
	 * @since    1.7 Replaces previously used instance() and __construct().
	 */
	public static function init() {
		// Nothing to do on autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Table init.
		CPStats_Table::init();

		// Plugin options.
		self::$_options = wp_parse_args(
			get_option( 'cpstats' ),
			array(
				'days'        => 14,
				'days_show'   => 14,
				'limit'       => 3,
				'today'       => 0,
				'snippet'     => 0,
				'blacklist'   => 0,
				'show_totals' => 0,
				'skip'        => array(
					'logged_in' => 1,
				),
			)
		);

		// Cron.
		add_action( 'cpstats_cleanup', array( 'CPStats_Cron', 'cleanup_data' ) );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			add_action( 'wp_ajax_nopriv_cpstats_track', array( 'CPStats_Frontend', 'track_visit_ajax' ) );
			add_action( 'wp_ajax_cpstats_track', array( 'CPStats_Frontend', 'track_visit_ajax' ) );
		} elseif ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {  // XMLRPC.
			add_filter( 'xmlrpc_methods', array( 'CPStats_XMLRPC', 'xmlrpc_methods' ) );
		} elseif ( is_admin() ) {   // Backend.
			add_action( 'wpmu_new_blog', array( 'CPStats_Install', 'init_site' ) );
			add_action( 'delete_blog', array( 'CPStats_Uninstall', 'init_site' ) );
			add_action( 'wp_dashboard_setup', array( 'CPStats_Dashboard', 'init' ) );
			add_filter( 'plugin_row_meta', array( 'CPStats_Backend', 'add_meta_link' ), 10, 2 );
			add_filter( 'plugin_action_links_' . CPSTATS_BASE, array( 'CPStats_Backend', 'add_action_link' ) );
			add_action( 'admin_init', array( 'CPStats_Settings', 'register_settings' ) );
			add_action( 'admin_menu', array( 'CPStats_Settings', 'add_admin_menu' ) );
			add_action( 'update_option_cpstats', array( 'CPStats_Settings', 'action_update_options' ), 10, 2 );
		} else {    // Frontend.
			add_action( 'template_redirect', array( 'CPStats_Frontend', 'track_visit' ) );
			add_filter( 'query_vars', array( 'CPStats_Frontend', 'query_vars' ) );
			add_action( 'wp_footer', array( 'CPStats_Frontend', 'wp_footer' ) );
			if ( function_exists( 'amp_is_request' ) || function_exists( 'is_amp_endpoint' ) ) {
				// Automattic AMP plugin present.
				add_filter( 'amp_analytics_entries', array( 'CPStats_Frontend', 'amp_analytics_entries' ) );
				add_filter( 'amp_post_template_analytics', array( 'CPStats_Frontend', 'amp_post_template_analytics' ) );
			}
		}
	}

	/**
	 * Get a readable date from YYYY-MM-DD database date.
	 *
	 * This function is designed as a wrapper around date_i18n() or wp_date(), if the latter is available (#166).
	 *
	 * @param string $date Raw date in "YYYY-MM-DD" format.
	 *
	 * @return string Parsed date in WP default format.
	 *
	 * @since 1.7.3
	 */
	public static function parse_date( $date ) {
		if ( function_exists( 'wp_date' ) ) { // Exists since WP 5.3.
			return wp_date( get_option( 'date_format' ), strtotime( $date ) );
		}

		return date_i18n( get_option( 'date_format' ), strtotime( $date ) );
	}

	/**
	 * Check JavaScript tracking.
	 *
	 * @return bool true if and only if one of the JavaScript tracking options is enabled.
	 */
	public static function is_javascript_tracking_enabled() {
		return in_array(
			self::$_options['snippet'],
			array(
				self::TRACKING_METHOD_JAVASCRIPT_WITH_NONCE_CHECK,
				self::TRACKING_METHOD_JAVASCRIPT_WITHOUT_NONCE_CHECK,
			),
			true
		);
	}
}
