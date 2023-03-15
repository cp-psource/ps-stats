<?php
/**
 * Plugin Name: CPStats – Extended Evaluation
 * Plugin URI: https://patrick-robrecht.de/wordpress/
 * Description: Extended evaluation for the compact, easy-to-use and privacy-compliant CPStats plugin.
 * Version: 2.6.3
 * Author: Patrick Robrecht
 * Author URI: https://patrick-robrecht.de/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: extended-evaluation-for-cpstats
 *
 * @package extended-evaluation-for-cpstats
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'EEFSTATFIFY_VERSION', '2.6.3' );

// Includes.
require_once 'inc/queries.php';
require_once 'inc/formatting.php';

/**
 * Requires CPStats to be installed and activated during installation.
 */
function eefcpstats_activate() {
	if ( ! eefcpstats_is_cpstats_active() ) {
		deactivate_plugins( __FILE__ );
		wp_die(
			esc_html(
				__(
					'CPStats – Extended Evaluation requires the plugin CPStats which has to be installed and activated! Please install and activate CPStats before activating this plugin!',
					'extended-evaluation-for-cpstats'
				)
			),
			esc_html(
				__(
					'Activation Error: CPStats – Extended Evaluation requires CPStats!',
					'extended-evaluation-for-cpstats'
				)
			),
			array(
				'response'  => 200,
				'back_link' => true,
			)
		);
	}
}

// Hook to run during plugin activation.
register_activation_hook( __FILE__, 'eefcpstats_activate' );

/**
 * Delete database entries created by the plugin on uninstall.
 */
function eefcpstats_uninstall() {
	// Nothing to do.
}

// Hook to run during plugin uninstall.
register_uninstall_hook( __FILE__, 'eefcpstats_uninstall' );

/**
 * Check for CPStats plugin being installed.
 * If it isn't, display message and deactivate this plugin.
 */
function eefcpstats_check_status() {
	if ( ! eefcpstats_is_cpstats_active() ) {
		// Display warning in the admin area.
		echo '<div class="error"><p>'
			. esc_html(
				__(
					'CPStats – Extended Evaluation requires the plugin CPStats which has to be installed and activated! Please install and activate CPStats before activating this plugin!',
					'extended-evaluation-for-cpstats'
				)
			)
			 . '</p></div>';
		// Deactivate this plugin.
		deactivate_plugins( __FILE__ );
	}
}

// Add status check to the admin notices.
add_action( 'admin_notices', 'eefcpstats_check_status' );

/**
 * Test whether CPStats is active.
 *
 * @return boolean true if and only if CPStats is installed and active.
 */
function eefcpstats_is_cpstats_active() {
	return is_plugin_active( 'cpstats/cpstats.php' );
}

/**
 * Load text domain for translation.
 */
function eefcpstats_load_plugin_textdomain() {
	load_plugin_textdomain( 'extended-evaluation-for-cpstats' );
}

// Add text domain during initialization.
add_action( 'init', 'eefcpstats_load_plugin_textdomain' );

/**
 * Register and load the style sheets and JavaScript libraries.
 */
function eefcpstats_register_and_load_assets() {
	if ( eefcpstats_current_user_can_see_evaluation() ) {
		eefcpstats_enqueue_style(
			'chartist',
			'/lib/chartist.min.css'
		);
		eefcpstats_enqueue_style(
			'chartist-plugin-tooltip',
			'lib/chartist-plugin-tooltip.min.css'
		);
		eefcpstats_enqueue_style(
			'eefcpstats',
			'/css/style.min.css'
		);

		eefcpstats_enqueue_script(
			'chartist',
			'/lib/chartist.min.js'
		);
		eefcpstats_enqueue_script(
			'chartist-plugin-tooltip',
			'lib/chartist-plugin-tooltip.min.js'
		);
		eefcpstats_enqueue_script(
			'eefcpstats_functions',
			'/js/functions.min.js',
			[ 'chartist', 'chartist-plugin-tooltip', 'jquery' ]
		);

		wp_localize_script(
			'eefcpstats_functions',
			'eefcpstats_translations',
			array(
				'view'  => strip_tags( esc_html__( 'View', 'extended-evaluation-for-cpstats' ) ),
				'views' => strip_tags( esc_html__( 'Views', 'extended-evaluation-for-cpstats' ) ),
			)
		);
	}
}

/**
 * Loads the CSS file.
 *
 * @param string $style_name the name of the style.
 * @param string $style_path the plugin-relative path of the CSS file.
 */
function eefcpstats_enqueue_style( $style_name, $style_path ) {
	wp_enqueue_style(
		$style_name,
		plugins_url(
			$style_path,
			__FILE__
		),
		[],
		EEFSTATFIFY_VERSION
	);
}

/**
 * Loads the JavaScript file.
 *
 * @param string $script_name the name of the script.
 * @param string $script_path the plugin-relative path of the JavaScript.
 * @param array  $dependencies the dependencies.
 */
function eefcpstats_enqueue_script( $script_name, $script_path, $dependencies = [] ) {
	wp_enqueue_script(
		$script_name,
		plugins_url(
			$script_path,
			__FILE__
		),
		$dependencies,
		EEFSTATFIFY_VERSION
	);
}

/**
 * Create an item and submenu items in the ClassicPress admin menu.
 */
function eefcpstats_add_menu() {
	$page_hook_suffixes   = [];
	$page_hook_suffixes[] = add_menu_page(
		__( 'CPStats – Extended Evaluation', 'extended-evaluation-for-cpstats' ), // page title.
		'CPStats', // title in the menu.
		'see_cpstats_evaluation',
		'extended_evaluation_for_cpstats_dashboard',
		'eefcpstats_show_dashboard',
		'dashicons-chart-bar',
		50
	);
	$page_hook_suffixes[] = add_submenu_page(
		'extended_evaluation_for_cpstats_dashboard',
		__( 'Content', 'extended-evaluation-for-cpstats' )
		. ' &mdash; ' . __( 'CPStats – Extended Evaluation', 'extended-evaluation-for-cpstats' ),
		__( 'Content', 'extended-evaluation-for-cpstats' ),
		'see_cpstats_evaluation',
		'extended_evaluation_for_cpstats_content',
		'eefcpstats_show_content'
	);
	$page_hook_suffixes[] = add_submenu_page(
		'extended_evaluation_for_cpstats_dashboard',
		__( 'Referrers', 'extended-evaluation-for-cpstats' )
		. ' &mdash; ' . __( 'CPStats – Extended Evaluation', 'extended-evaluation-for-cpstats' ),
		__( 'Referrers', 'extended-evaluation-for-cpstats' ),
		'see_cpstats_evaluation',
		'extended_evaluation_for_cpstats_referrer',
		'eefcpstats_show_referrer'
	);

	// Load CSS and JavaScript on plugin pages.
	foreach ( $page_hook_suffixes as $page_hook_suffix ) {
		add_action( "admin_print_styles-{$page_hook_suffix}", 'eefcpstats_register_and_load_assets' );
	}
}

// Register the menu building function.
add_action( 'admin_menu', 'eefcpstats_add_menu' );

/**
 * Adds a custom capability for users who see the evaluation pages.
 */
function eefcpstats_add_capability() {
	// Only administrators can see the evaluation by default.
	$role = get_role( 'administrator' );
	if ( $role ) {
		$role->add_cap( 'see_cpstats_evaluation' );
	}

	// Remove old role CPStats Analyst.
	$role = get_role( 'cpstats_evaluator' );
	if ( $role ) {
		remove_role( 'cpstats_evaluator' );
	}
}

// Adds the capability.
add_action( 'admin_init', 'eefcpstats_add_capability' );

/**
 * Checks whether the current user is in the admin area and has the capability to see the evaluation.
 *
 * @return true if and only if the current user is allowed to see plugin pages
 */
function eefcpstats_current_user_can_see_evaluation() {
	return is_admin() && current_user_can( 'see_cpstats_evaluation' );
}

/**
 * Show the dashboard page.
 */
function eefcpstats_show_dashboard() {
	eefcpstats_load_view( __DIR__ . '/views/dashboard.php' );
}

/**
 * Show the most popular content statistics page.
 */
function eefcpstats_show_content() {
	eefcpstats_load_view( __DIR__ . '/views/content.php' );
}

/**
 * Show the referrer statistics page.
 */
function eefcpstats_show_referrer() {
	eefcpstats_load_view( __DIR__ . '/views/referrers.php' );
}

/**
 * Loads the view template.
 *
 * @param string $view_path the path to the view template.
 */
function eefcpstats_load_view( $view_path ) {
	if ( eefcpstats_current_user_can_see_evaluation() ) {
		load_template(
			wp_normalize_path( $view_path )
		);
	}
}
