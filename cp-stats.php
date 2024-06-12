<?php
/**
 * Plugin Name: CP-Stats
 * Description: Kompaktes, benutzerfreundliches und datenschutzkonformes Statistik-Plugin für ClassicPress.
 * Text Domain: cpstats
 * Author:      DerN3rd
 * Author URI:  https://n3rds.work
 * Plugin URI:  https://n3rds.work/
 * License:     GPLv3 or later
 * Version:     1.8.4
 * Domain Path: languages
 *
 * @package ClassicPress
 */

/**
 * @@@@@@@@@@@@@@@@@ PS UPDATER 1.3 @@@@@@@@@@@
 **/
require 'psource/psource-plugin-update/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
 
$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/cp-psource/cp-stats',
	__FILE__,
	'cp-stats'
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
define( 'CPSTATS_VERSION', '1.8.4' );


/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'CPStats',
		'init',
	)
);
register_activation_hook(
	CPSTATS_FILE,
	array(
		'CPStats_Install',
		'init',
	)
);
register_deactivation_hook(
	CPSTATS_FILE,
	array(
		'CPStats_Deactivate',
		'init',
	)
);
register_uninstall_hook(
	CPSTATS_FILE,
	array(
		'CPStats_Uninstall',
		'init',
	)
);


/* Autoload */
spl_autoload_register( 'cpstats_autoload' );

/**
 * Include classes via autoload.
 *
 * @param string $class Name of an class-file name, without file extension.
 */
function cpstats_autoload( $class ) {

	$plugin_classes = array(
		'CPStats',
		'CPStats_Backend',
		'CPStats_Frontend',
		'CPStats_Dashboard',
		'CPStats_Install',
		'CPStats_Uninstall',
		'CPStats_Deactivate',
		'CPStats_Settings',
		'CPStats_Table',
		'CPStats_XMLRPC',
		'CPStats_Cron',
	);

	if ( in_array( $class, $plugin_classes, true ) ) {
		require_once sprintf(
			'%s/inc/class-%s.php',
			CPSTATS_DIR,
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}

// Load cpstats-widget plugin
function load_cpstats_widget() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/cpstats-widget/cpstats-widget.php' );
}
add_action( 'plugins_loaded', 'load_cpstats_widget' );

// Load cpstats-blacklist plugin
function load_cpstats_blacklist() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/cpstats-blacklist/cpstats-blacklist.php' );
}
add_action( 'plugins_loaded', 'load_cpstats_blacklist' );

// Load cpstats-extended-evaluation plugin
function load_extended_evaluation_for_cpstats() {
	require_once( plugin_dir_path( __FILE__ ) . 'inc/extended-evaluation-for-cpstats/extended-evaluation-for-cpstats.php' );
}

function load_cpstats_textdomain() {
    load_plugin_textdomain( 'cpstats', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'load_cpstats_textdomain' );

add_action( 'plugins_loaded', 'load_extended_evaluation_for_cpstats' );
