<?php
/**
 * PSStats: PSStats_Dashboard class
 *
 * This file contains the derived class for the plugin's dashboard features.
 *
 * @package   PSStats
 * @since     1.1
 */

// Quit if accessed outside WP context.
defined( 'ABSPATH' ) || exit;

/**
 * PSStats_Dashboard
 *
 * @since 1.1
 */
class PSStats_Dashboard extends PSStats {

	/**
	 * Plugin version.
	 *
	 * @since  1.4.0
	 * @var    string
	 */
	protected static $_plugin_version;

	/**
	 * Dashboard widget initialize
	 *
	 * @since   0.1.0
	 * @version 2016-12-21
	 *
	 * @wp-hook boolean  psstats__user_can_see_stats
	 * @see     https://wordpress.org/plugins/psstats/
	 */
	public static function init() {

		// Filter user_can_see_stats.
		if ( ! apply_filters( 'psstats__user_can_see_stats', current_user_can( 'edit_dashboard' ) ) ) {
			return;
		}

		// Check if user can edit the widget.
		$can_edit = apply_filters( 'psstats__user_can_see_stats', current_user_can( 'edit_dashboard' ) );

		// Load textdomain.
		load_plugin_textdomain(
			'psstats',
			false,
			wp_normalize_path( sprintf( '%s/lang', CPSTATS_DIR ) )
		);

		// Plugin version.
		self::_get_version();

		// Add dashboard widget.
		wp_add_dashboard_widget(
			'psstats_dashboard',
			'PS-Stats',
			array( __CLASS__, 'print_frontview' ),
			$can_edit ? array( __CLASS__, 'print_backview' ) : null
		);

		// Init CSS.
		add_action( 'admin_print_styles', array( __CLASS__, 'add_style' ) );

		// Init JS.
		add_action( 'admin_print_scripts', array( __CLASS__, 'add_js' ) );
	}

	/**
	 * Print CSS
	 *
	 * @since   0.1.0
	 * @version 1.4.0
	 */
	public static function add_style() {

		// Register CSS.
		wp_register_style(
			'chartist_css',
			plugins_url( '/css/chartist.min.css', CPSTATS_FILE ),
			array(),
			self::$_plugin_version
		);
		wp_register_style(
			'chartist_tooltip_css',
			plugins_url( '/css/chartist-plugin-tooltip.min.css', CPSTATS_FILE ),
			array(),
			self::$_plugin_version
		);
		wp_register_style(
			'psstats',
			plugins_url( '/css/dashboard.min.css', CPSTATS_FILE ),
			array(),
			self::$_plugin_version
		);

		// Load CSS.
		wp_enqueue_style( 'chartist_css' );
		wp_enqueue_style( 'chartist_tooltip_css' );
		wp_enqueue_style( 'psstats' );
	}

	/**
	 * Print JavaScript
	 *
	 * @since    0.1.0
	 * @version  1.4.0
	 */
	public static function add_js() {

		// Register JS.
		wp_register_script(
			'chartist_js',
			plugins_url( 'js/chartist.min.js', CPSTATS_FILE ),
			array(),
			self::$_plugin_version,
			true
		);
		wp_register_script(
			'chartist_tooltip_js',
			plugins_url( 'js/chartist-plugin-tooltip.min.js', CPSTATS_FILE ),
			array( 'chartist_js' ),
			self::$_plugin_version,
			true
		);
		wp_register_script(
			'psstats_chart_js',
			plugins_url(
				'js/dashboard.min.js',
				CPSTATS_FILE
			),
			array( 'jquery', 'chartist_tooltip_js' ),
			self::$_plugin_version,
			true
		);

		// Localize strings.
		wp_localize_script(
			'psstats_chart_js',
			'psstats_translations',
			array(
				'pageview'  => strip_tags( esc_html__( 'Pageview', 'psstats' ) ),
				'pageviews' => strip_tags( esc_html__( 'Pageviews', 'psstats' ) ),
			)
		);
	}


	/**
	 * Print widget frontview.
	 *
	 * @since    0.1.0
	 * @version  1.4.0
	 */
	public static function print_frontview() {

		// Load JS.
		wp_enqueue_script( 'chartist_js' );
		wp_enqueue_script( 'psstats_chart_js' );

		// Load template.
		load_template(
			wp_normalize_path( sprintf( '%s/views/widget-front.php', CPSTATS_DIR ) )
		);
	}


	/**
	 * Print widget backview
	 *
	 * @since    0.4.0
	 * @version  1.4.0
	 */
	public static function print_backview() {

		// Capability check.
		if ( ! current_user_can( 'edit_dashboard' ) ) {
			return;
		}

		// Update plugin options.
		if ( ! empty( $_POST['psstats'] ) ) {
			check_admin_referer( 'psstats-dashboard' );

			self::_save_widget_options();
		}

		// Load view.
		load_template(
			wp_normalize_path( sprintf( '%s/views/widget-back.php', CPSTATS_DIR ) )
		);
	}


	/**
	 * Save dashboard widget options.
	 *
	 * @since    1.4.0
	 * @sicnce   1.7.0 Renamed to _save_widget_options()
	 *
	 * @return void
	 */
	private static function _save_widget_options() {
		// Check the nonce field from the dashboard form.
		if ( ! check_admin_referer( 'psstats-dashboard' ) ) {
			return;
		}

		// We only do a partial update, so initialize with current values.
		$options = PSStats::$_options;

		// Parse numeric values.
		foreach ( array( 'days', 'days_show', 'limit' ) as $option_name ) {
			$options[ $option_name ] = PSStats::$_options[ $option_name ];
			if ( isset( $_POST['psstats'][ $option_name ] ) && (int) $_POST['psstats'][ $option_name ] > 0 ) {
				$options[ $option_name ] = (int) $_POST['psstats'][ $option_name ];
			}
		}
		if ( $options['limit'] > 100 ) {
			$options['limit'] = 100;
		}

		// Parse "today" checkbox.
		if ( isset( $_POST['psstats']['today'] ) && 1 === (int) $_POST['psstats']['today'] ) {
			$options['today'] = 1;
		} else {
			$options['today'] = 0;
		}

		// Parse "show totals" checkbox.
		if ( isset( $_POST['psstats']['show_totals'] ) && 1 === (int) $_POST['psstats']['show_totals'] ) {
			$options['show_totals'] = 1;
		} else {
			$options['show_totals'] = 0;
		}

		// Update values.
		update_option( 'psstats', $options );
	}


	/**
	 * Set plugin version from plugin meta data
	 *
	 * @since    1.4.0
	 * @version  1.4.0
	 */
	private static function _get_version() {

		// Get plugin meta.
		$meta = get_plugin_data( CPSTATS_FILE );

		self::$_plugin_version = $meta['Version'];
	}


	/**
	 * Get stats from cache
	 *
	 * @since   0.1.0
	 * @version 1.4.0
	 *
	 * @param bool $force_refresh If true, the data is recalculated, otherwise the cached value is used if available.
	 *
	 * @return  array  $data  stats data from cache or database
	 */
	public static function get_stats( $force_refresh = false ) {

		// Get from cache if enabled.
		if ( ! $force_refresh ) {
			$data_from_cache = get_transient( 'psstats_data' );
			if ( $data_from_cache ) {
				return $data_from_cache;
			}
		}

		// Get from DB.
		$data = self::_select_data();

		// Prepare data.
		if ( ! empty( $data['visits'] ) ) {
			$data['visits'] = array_reverse( $data['visits'] );
		} else {
			$data = null;
		}

		// Make cache.
		set_transient(
			'psstats_data',
			$data,
			MINUTE_IN_SECONDS * 15
		);

		return $data;
	}


	/**
	 * Get stats from DB
	 *
	 * @since    0.1.0
	 * @version  1.4.0
	 *
	 * @return  array  DB results
	 */
	private static function _select_data() {

		// Global.
		global $wpdb;

		// Init values.
		$days_show   = (int) self::$_options['days_show'];
		$limit       = (int) self::$_options['limit'];
		$today       = (int) self::$_options['today'];
		$show_totals = (int) self::$_options['show_totals'];

		$current_date = current_time( 'Y-m-d' );

		$data = array(
			'visits'   => $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `created` as `date`, COUNT(`created`) as `count` FROM `$wpdb->psstats` GROUP BY `created` ORDER BY `created` DESC LIMIT %d",
					$days_show
				),
				ARRAY_A
			),
		);

		if ( $today ) {
			$data['target'] = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT COUNT(`target`) as `count`, `target` as `url` FROM `$wpdb->psstats` WHERE created = %s GROUP BY `target` ORDER BY `count` DESC LIMIT %d",
					$current_date,
					$limit
				),
				ARRAY_A
			);
			$data['referrer'] = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT COUNT(`referrer`) as `count`, `referrer` as `url`, SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(LEADING 'www.' FROM(TRIM(LEADING 'https://' FROM TRIM(LEADING 'http://' FROM TRIM(`referrer`))))), '/', 1), ':', 1) as `host` FROM `$wpdb->psstats` WHERE `referrer` != '' AND created = %s GROUP BY `host` ORDER BY `count` DESC LIMIT %d",
					$current_date,
					$limit
				),
				ARRAY_A
			);
		} else {
			$data['target'] = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT COUNT(`target`) as `count`, `target` as `url` FROM `$wpdb->psstats` WHERE created > DATE_SUB(%s, INTERVAL %d DAY) GROUP BY `target` ORDER BY `count` DESC LIMIT %d",
					$current_date,
					$days_show,
					$limit
				),
				ARRAY_A
			);
			$data['referrer'] = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT COUNT(`referrer`) as `count`, `referrer` as `url`, SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(LEADING 'www.' FROM(TRIM(LEADING 'https://' FROM TRIM(LEADING 'http://' FROM TRIM(`referrer`))))), '/', 1), ':', 1) as `host` FROM `$wpdb->psstats` WHERE `referrer` != '' AND created > DATE_SUB(%s, INTERVAL %d DAY) GROUP BY `host` ORDER BY `count` DESC LIMIT %d",
					$current_date,
					$days_show,
					$limit
				),
				ARRAY_A
			);
		}

		if ( $show_totals ) {
			$data['visit_totals'] = array(
				'today'           => $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(`created`) FROM `$wpdb->psstats` WHERE created = %s",
						$current_date
					)
				),
				'since_beginning' => $wpdb->get_row(
					"SELECT COUNT(`created`) AS `count`, MIN(`created`) AS `date` FROM `$wpdb->psstats`",
					ARRAY_A
				),
			);
		}

		return $data;
	}
}
