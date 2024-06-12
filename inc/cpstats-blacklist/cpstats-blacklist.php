<?php
/**
 * CPStats Filter
 * Version:     1.6.2
 *
 */

// Quit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constants.
define( 'CPSTATSBLACKLIST_FILE', __FILE__ );
define( 'CPSTATSBLACKLIST_DIR', dirname( __FILE__ ) );
define( 'CPSTATSBLACKLIST_BASE', plugin_basename( __FILE__ ) );


// System Hooks.
add_action( 'plugins_loaded', array( 'CPStatsBlacklist', 'init' ) );

register_activation_hook( CPSTATSBLACKLIST_FILE, array( 'CPStatsBlacklist_System', 'install' ) );

register_uninstall_hook( CPSTATSBLACKLIST_FILE, array( 'CPStatsBlacklist_System', 'uninstall' ) );

// Upgrade hook.
register_activation_hook( CPSTATSBLACKLIST_FILE, array( 'CPStatsBlacklist_System', 'upgrade' ) );

// Autoload.
spl_autoload_register( 'cpstats_blacklist_autoload' );


/**
 * Autoloader for CPStatsBlacklist classes.
 *
 * @param string $class Name of the class to load.
 *
 * @since 1.0.0
 */
function cpstats_blacklist_autoload( $class ) {
	$plugin_classes = array(
		'CPStatsBlacklist',
		'CPStatsBlacklist_Admin',
		'CPStatsBlacklist_System',
	);

	if ( in_array( $class, $plugin_classes, true ) ) {
		require_once sprintf(
			'%s/inc/class-%s.php',
			CPSTATSBLACKLIST_DIR,
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}
