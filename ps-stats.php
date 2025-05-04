<?php
/**
 * Plugin Name: PS Stats
 * Description: Kompaktes, benutzerfreundliches und datenschutzkonformes Statistik-Plugin für WordPress.
 * Text Domain: psstats
 * Author:      DerN3rd
 * Author URI:  https://n3rds.work
 * Plugin URI:  https://cp-psource.github.io/ps-stats/
 * License:     GPLv3 or later
 * Version:     1.8.5
 * Domain Path: languages
 *
 * @package WordPress
 */

/**
 * @@@@@@@@@@@@@@@@@ PS UPDATER 1.3 @@@@@@@@@@@
 **/
require 'psource/psource-plugin-update/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
 
$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/cp-psource/ps-stats',
	__FILE__,
	'ps-stats'
);
 
//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

/**
 * @@@@@@@@@@@@@@@@@ ENDE PS UPDATER 1.3 @@@@@@@@@@@
 **/

/* Quit */
defined( 'ABSPATH' ) || exit;


/*  Constants */
define( 'CPSTATS_FILE', __FILE__ );
define( 'CPSTATS_DIR', dirname( __FILE__ ) );
define( 'CPSTATS_BASE', plugin_basename( __FILE__ ) );
define( 'CPSTATS_VERSION', '1.8.5' );


/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'PSStats',
		'init',
	)
);
register_activation_hook(
	CPSTATS_FILE,
	array(
		'PSStats_Install',
		'init',
	)
);
register_deactivation_hook(
	CPSTATS_FILE,
	array(
		'PSStats_Deactivate',
		'init',
	)
);
register_uninstall_hook(
	CPSTATS_FILE,
	array(
		'PSStats_Uninstall',
		'init',
	)
);


/* Autoload */
spl_autoload_register( 'psstats_autoload' );

/**
 * Include classes via autoload.
 *
 * @param string $class Name of an class-file name, without file extension.
 */
function psstats_autoload( $class ) {

	$plugin_classes = array(
		'PSStats',
		'PSStats_Backend',
		'PSStats_Frontend',
		'PSStats_Dashboard',
		'PSStats_Install',
		'PSStats_Uninstall',
		'PSStats_Deactivate',
		'PSStats_Settings',
		'PSStats_Table',
		'PSStats_XMLRPC',
		'PSStats_Cron',
	);

	if ( in_array( $class, $plugin_classes, true ) ) {
		require_once sprintf(
			'%s/inc/class-%s.php',
			CPSTATS_DIR,
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}

// Load psstats-widget plugin
function load_psstats_widget() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/psstats-widget/psstats-widget.php' );
}
add_action( 'plugins_loaded', 'load_psstats_widget' );

// Load psstats-blacklist plugin
function load_psstats_blacklist() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/psstats-blacklist/psstats-blacklist.php' );
}
add_action( 'plugins_loaded', 'load_psstats_blacklist' );

// Load psstats-extended-evaluation plugin
function load_extended_evaluation_for_psstats() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/extended-evaluation-for-psstats/extended-evaluation-for-psstats.php' );
}

function load_psstats_textdomain() {
    load_plugin_textdomain( 'psstats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'load_psstats_textdomain' );

add_action( 'plugins_loaded', 'load_extended_evaluation_for_psstats' );
