<?php
/**
 * CPStats: CPStats_Settings class
 *
 * This file contains the plugin's settings capabilities.
 *
 * @package   CPStats
 * @since     1.7
 */

// Quit if accessed directly..
defined( 'ABSPATH' ) || exit;

/**
 * Class CPStats_Settings
 *
 * @since 1.7
 */
class CPStats_Settings {

	/**
	 * Registers all options using the WP Settings API.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting( 'cpstats', 'cpstats', array( __CLASS__, 'sanitize_options' ) );

		// Global settings.
		add_settings_section(
			'cpstats-global',
			__( 'Global settings', 'cpstats' ),
			null,
			'cpstats'
		);
		add_settings_field(
			'cpstats-days',
			__( 'Period of data saving', 'cpstats' ),
			array( __CLASS__, 'options_days' ),
			'cpstats',
			'cpstats-global',
			array( 'label_for' => 'cpstats-days' )
		);
		add_settings_field(
			'cpstats-snippet',
			__( 'Tracking method', 'cpstats' ),
			array( __CLASS__, 'options_snippet' ),
			'cpstats',
			'cpstats-global',
			array( 'label_for' => 'cpstats-snippet' )
		);

		// Dashboard widget settings.
		add_settings_section(
			'cpstats-dashboard',
			__( 'Dashboard Widget', 'cpstats' ),
			array( __CLASS__, 'header_dashboard' ),
			'cpstats'
		);
		add_settings_field(
			'cpstats-days_show',
			__( 'Period of data display in Dashboard', 'cpstats' ),
			array( __CLASS__, 'options_days_show' ),
			'cpstats',
			'cpstats-dashboard',
			array( 'label_for' => 'cpstats-days-show' )
		);
		add_settings_field(
			'cpstats-limit',
			__( 'Number of entries in top lists', 'cpstats' ),
			array( __CLASS__, 'options_limit' ),
			'cpstats',
			'cpstats-dashboard',
			array( 'label_for' => 'cpstats-limit' )
		);
		add_settings_field(
			'cpstats-today',
			__( 'Top lists only for today', 'cpstats' ),
			array( __CLASS__, 'options_today' ),
			'cpstats',
			'cpstats-dashboard',
			array( 'label_for' => 'cpstats-today' )
		);
		add_settings_field(
			'cpstats-show-totals',
			__( 'Show totals', 'cpstats' ),
			array( __CLASS__, 'options_show_totals' ),
			'cpstats',
			'cpstats-dashboard',
			array( 'label_for' => 'cpstats-show-totals' )
		);

		// Exclusion settings.
		add_settings_section(
			'cpstats-skip',
			__( 'Skip tracking for ...', 'cpstats' ),
			array( __CLASS__, 'header_skip' ),
			'cpstats'
		);
		add_settings_field(
			'cpstats-skip-referrer',
			__( 'Disallowed referrers', 'cpstats' ),
			array( __CLASS__, 'options_skip_blacklist' ),
			'cpstats',
			'cpstats-skip',
			array( 'label_for' => 'cpstats-skip-referrer' )
		);
		add_settings_field(
			'cpstats-skip-logged_in',
			__( 'Logged in users', 'cpstats' ),
			array( __CLASS__, 'options_skip_logged_in' ),
			'cpstats',
			'cpstats-skip',
			array( 'label_for' => 'cpstats-skip-logged_in' )
		);
	}

	/**
	 * Option for data collection period.
	 *
	 * @return void
	 */
	public static function options_days() {
		?>
		<input id="cpstats-days" name="cpstats[days]" type="number" min="1" value="<?php echo esc_attr( CPStats::$_options['days'] ); ?>">
		<?php esc_html_e( 'days', 'cpstats' ); ?>
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: 14)
		<?php
	}

	/**
	 * Option for tracking via JS.
	 *
	 * @return void
	 */
	public static function options_snippet() {
		?>
		<p>
			<?php self::show_snippet_option( CPStats::TRACKING_METHOD_DEFAULT, __( 'Default tracking', 'cpstats' ) ); ?>
			<br>
			<?php self::show_snippet_option( CPStats::TRACKING_METHOD_JAVASCRIPT_WITH_NONCE_CHECK, __( 'JavaScript based tracking with nonce check', 'cpstats' ) ); ?>
			<br>
			<?php self::show_snippet_option( CPStats::TRACKING_METHOD_JAVASCRIPT_WITHOUT_NONCE_CHECK, __( 'JavaScript based tracking without nonce check', 'cpstats' ) ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'JavaScript based tracking is strongly recommended if caching or AMP is in use.', 'cpstats' ); ?>
			<?php esc_html_e( 'Disable the nonce check if the caching time is longer than the nonce time or you miss views due to 403 Forbidden errors.', 'cpstats' ); ?>
		</p>
		<?php
	}

	/**
	 * Outputs the input radio for an option.
	 *
	 * @param int    $value the value for the input radio.
	 * @param string $label the label.
	 */
	private static function show_snippet_option( $value, $label ) {
		?>
			<label>
				<input name="cpstats[snippet]" type="radio" value="<?php echo esc_html( $value ); ?>" <?php checked( CPStats::$_options['snippet'], $value ); ?>>
				<?php echo esc_html( $label ); ?>
			</label>
		<?php
	}

	/**
	 * Section header for "Dashboard Widget" section.
	 *
	 * @return void
	 */
	public static function header_dashboard() {
		?>
		<p>
			<?php esc_html_e( 'The following options affect the admin dashboard widget.', 'cpstats' ); ?>
		</p>
		<?php
	}

	/**
	 * Option for data display period.
	 *
	 * @return void
	 */
	public static function options_days_show() {
		?>
		<input id="cpstats-days-show" name="cpstats[days_show]" type="number" min="1" value="<?php echo esc_attr( CPStats::$_options['days_show'] ); ?>">
		<?php esc_html_e( 'days', 'cpstats' ); ?>
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: 14)
		<?php
	}

	/**
	 * Option for number of entries in top lists.
	 *
	 * @return void
	 */
	public static function options_limit() {
		?>
		<input id="cpstats-limit" name="cpstats[limit]" type="number" min="1" max="100" value="<?php echo esc_attr( CPStats::$_options['limit'] ); ?>">
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: 3)
		<?php
	}

	/**
	 * Option for number of entries in top lists.
	 *
	 * @return void
	 */
	public static function options_today() {
		?>
		<input  id="cpstats-today" type="checkbox" name="cpstats[today]" value="1" <?php checked( CPStats::$_options['today'], 1 ); ?>>
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: <?php esc_html_e( 'No', 'cpstats' ); ?>)
		<?php
	}

	/**
	 * Option for showing visit totals.
	 *
	 * @return void
	 */
	public static function options_show_totals() {
		?>
		<input  id="cpstats-show-totals" type="checkbox" name="cpstats[show_totals]" value="1" <?php checked( CPStats::$_options['show_totals'], 1 ); ?>>
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: <?php esc_html_e( 'No', 'cpstats' ); ?>)
		<?php
	}

	/**
	 * Section header for "Skip tracking for..." section.
	 *
	 * @return void
	 */
	public static function header_skip() {
		?>
		<p>
			<?php echo wp_kses( __( 'The following options define cases in which a view will <strong>not</strong> be tracked.', 'cpstats' ), array( 'strong' => array() ) ); ?>
		</p>
		<?php
	}

	/**
	 * Option to skip tracking for disallowed referrers.
	 *
	 * @return void
	 */
	public static function options_skip_blacklist() {
		?>
		<input id="cpstats-skip-referrer" type="checkbox" name="cpstats[blacklist]" value="1"<?php checked( CPStats::$_options['blacklist'] ); ?>>
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: <?php esc_html_e( 'No', 'cpstats' ); ?>)
		<p class="description"><?php esc_html_e( 'Enabling this option excludes any views with referrers listed in the list of disallowed comment keys.', 'cpstats' ); ?></p>
		<?php
	}

	/**
	 * Option to skip tracking for logged in uses.
	 *
	 * @return void
	 */
	public static function options_skip_logged_in() {
		?>
		<input id="cpstats-skip-logged_in" type="checkbox" name="cpstats[skip][logged_in]" value="1"<?php checked( CPStats::$_options['skip']['logged_in'] ); ?>>
		(<?php esc_html_e( 'Default', 'cpstats' ); ?>: <?php esc_html_e( 'Yes', 'cpstats' ); ?>)
		<p class="description"><?php esc_html_e( 'Enabling this option excludes any views of logged-in users from tracking.', 'cpstats' ); ?></p>
		<?php
	}

	/**
	 * Action to be triggered after CPStats options have been saved.
	 * Delete transient data to refresh the dashboard widget and flushes Cachify cache, if the plugin is available and
	 * JS settings have changed.
	 *
	 * @since 1.7.1
	 *
	 * @param array $old_value The old options value.
	 * @param array $value     The updated options value.
	 *
	 * @return void
	 */
	public static function action_update_options( $old_value, $value ) {
		// Delete transient.
		delete_transient( 'cpstats_data' );

		// Clear Cachify cache, if JS settings have changed.
		if ( $old_value['snippet'] !== $value['snippet'] && has_action( 'cachify_flush_cache' ) ) {
			do_action( 'cachify_flush_cache' );
		}
	}

	/**
	 * Validate and sanitize submitted options.
	 *
	 * @param array $options Original options.
	 *
	 * @return array Validated and sanitized options.
	 */
	public static function sanitize_options( $options ) {

		// Sanitize numeric values.
		$res = array();
		foreach ( array( 'days', 'days_show', 'limit' ) as $o ) {
			$res[ $o ] = CPStats::$_options[ $o ];
			if ( isset( $options[ $o ] ) && (int) $options[ $o ] > 0 ) {
				$res[ $o ] = (int) $options[ $o ];
			}
		}
		if ( $res['limit'] > 100 ) {
			$res['limit'] = 100;
		}

		if ( isset( $options['snippet'] ) ) {
			$method = (int) $options['snippet'];
			if ( in_array(
				$method,
				array(
					CPStats::TRACKING_METHOD_DEFAULT,
					CPStats::TRACKING_METHOD_JAVASCRIPT_WITH_NONCE_CHECK,
					CPStats::TRACKING_METHOD_JAVASCRIPT_WITHOUT_NONCE_CHECK,
				),
				true
			) ) {
				$res['snippet'] = $method;
			}
		}

		// Get checkbox values.
		foreach ( array( 'today', 'blacklist', 'show_totals' ) as $o ) {
			$res[ $o ] = isset( $options[ $o ] ) && 1 === (int) $options[ $o ] ? 1 : 0;
		}
		$res['skip']['logged_in'] = isset( $options['skip']['logged_in'] ) && 1 === (int) $options['skip']['logged_in'] ? 1 : 0;

		return $res;
	}

	/**
	 * Creates a menu entry in the settings menu.
	 *
	 * @return void
	 */
	public static function add_admin_menu() {
		add_options_page(
			__( 'CP-Stats', 'cpstats' ),
			__( 'CP-Stats', 'cpstats' ),
			'manage_options',
			'cpstats-settings',
			array( __CLASS__, 'create_settings_page' )
		);
	}

	/**
	 * Creates the settings pages.
	 *
	 * @return void
	 */
	public static function create_settings_page() {
		?>

		<div class="wrap">
			<h1><?php esc_html_e( 'CPStats Settings', 'cpstats' ); ?></h1>

			<form id="cpstats-settings" method="post" action="options.php">
				<?php
				settings_fields( 'cpstats' );
				do_settings_sections( 'cpstats' );
				submit_button();
				?>
				<p class="alignright">
					<a href="<?php echo esc_url( __( 'https://wordpress.org/plugins/cpstats/', 'cpstats' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Documentation', 'cpstats' ); ?></a>
					&bull; <a href="https://n3rds.work/spendenaktionen/unterstuetze-unsere-psource-free-werke/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Donate', 'cpstats' ); ?></a>
					&bull; <a href="<?php echo esc_url( __( 'https://wordpress.org/support/plugin/cpstats', 'cpstats' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support', 'cpstats' ); ?></a>
				</p>

			</form>
		</div>

		<?php
	}

}
