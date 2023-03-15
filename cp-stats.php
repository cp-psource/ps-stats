<?php
/**
 * Plugin Name: CP-Stats
 * Description: Compact, easy-to-use and privacy-compliant stats plugin for ClassicPress.
 * Text Domain: cpstats
 * Author:      DerN3rd
 * Author URI:  https://n3rds.work
 * Plugin URI:  https://n3rds.work/
 * License:     GPLv3 or later
 * Version:     1.8.4
 *
 * @package ClassicPress
 */
require 'psource/psource-plugin-update/psource-plugin-updater.php';
use Psource\PluginUpdateChecker\v5\PucFactory;
$MyUpdateChecker = PucFactory::buildUpdateChecker(
	'https://n3rds.work//wp-update-server/?action=get_metadata&slug=cp-stats', 
	__FILE__, 
	'cp-stats' 
);
/* Quit */
defined( 'ABSPATH' ) || exit;


/*  Constants */
define( 'STATIFY_FILE', __FILE__ );
define( 'STATIFY_DIR', dirname( __FILE__ ) );
define( 'STATIFY_BASE', plugin_basename( __FILE__ ) );
define( 'STATIFY_VERSION', '1.8.4' );


/* Hooks */
add_action(
	'plugins_loaded',
	array(
		'CPStats',
		'init',
	)
);
register_activation_hook(
	STATIFY_FILE,
	array(
		'CPStats_Install',
		'init',
	)
);
register_deactivation_hook(
	STATIFY_FILE,
	array(
		'CPStats_Deactivate',
		'init',
	)
);
register_uninstall_hook(
	STATIFY_FILE,
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
			STATIFY_DIR,
			strtolower( str_replace( '_', '-', $class ) )
		);
	}
}
