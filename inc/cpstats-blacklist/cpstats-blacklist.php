<?php
/**
 * CPStats Filter
 *
 * @package     PluginPackage
 * @author      Stefan Kalscheuer <stefan@stklcode.de>
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: CPStats Filter
 * Plugin URI:  https://wordpress.org/plugins/cpstats-blacklist/
 * Description: Extension for the CPStats plugin to add customizable filters. (formerly "CPStats Blacklist)
 * Version:     1.6.2
 * Author:      Stefan Kalscheuer (@stklcode)
 * Author URI:  https://www.stklcode.de
 * Text Domain: cpstats-blacklist
 * License:     GPLv2 or later
 *
 * CPStats Filter is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * CPStats Filter is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CPStats Filter. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// Quit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constants.
define( 'CPSTATSBLACKLIST_FILE', __FILE__ );
define( 'CPSTATSBLACKLIST_DIR', dirname( __FILE__ ) );
define( 'CPSTATSBLACKLIST_BASE', plugin_basename( __FILE__ ) );

// Check for compatibility.
if ( cpstats_blacklist_compatibility_check() ) {
	// System Hooks.
	add_action( 'plugins_loaded', array( 'CPStatsBlacklist', 'init' ) );

	register_activation_hook( CPSTATSBLACKLIST_FILE, array( 'CPStatsBlacklist_System', 'install' ) );

	register_uninstall_hook( CPSTATSBLACKLIST_FILE, array( 'CPStatsBlacklist_System', 'uninstall' ) );

	// Upgrade hook.
	register_activation_hook( CPSTATSBLACKLIST_FILE, array( 'CPStatsBlacklist_System', 'upgrade' ) );

	// Autoload.
	spl_autoload_register( 'cpstats_blacklist_autoload' );
} else {
	// Disable plugin, if active.
	add_action( 'admin_init', 'cpstats_blacklist_disable' );
}

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

/**
 * Check for compatibility with PHP and WP version.
 *
 * @since 1.5.0
 *
 * @return boolean Whether minimum WP and PHP versions are met.
 */
function cpstats_blacklist_compatibility_check() {
	return version_compare( $GLOBALS['wp_version'], '4.7', '>=' ) &&
		version_compare( phpversion(), '5.5', '>=' );
}

/**
 * Disable plugin if active and incompatible.
 *
 * @since 1.5.0
 *
 * @return void
 */
function cpstats_blacklist_disable() {
	if ( is_plugin_active( CPSTATSBLACKLIST_BASE ) ) {
		deactivate_plugins( CPSTATSBLACKLIST_BASE );
		add_action( 'admin_notices', 'cpstats_blacklist_disabled_notice' );
		// phpcs:disable ClassicPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		// phpcs:enable
	}
}

/**
 * Admin notification for unmet requirements.
 *
 * @since 1.5.0
 *
 * @return void
 */
function cpstats_blacklist_disabled_notice() {
	echo '<div class="notice notice-error is-dismissible"><p><strong>';
	printf(
		/* translators: minimum version numbers for ClassicPress and PHP inserted at placeholders */
		esc_html__( 'CPStats Filter requires at least ClassicPress %1$s and PHP %2$s.', 'cpstats-blacklist' ),
		'4.7',
		'5.5'
	);
	echo '<br>';
	printf(
		/* translators: current version numbers for ClassicPress and PHP inserted at placeholders */
		esc_html__( 'Your site is running ClassicPress %1$s on PHP %2$s, thus the plugin has been disabled.', 'cpstats-blacklist' ),
		esc_html( $GLOBALS['wp_version'] ),
		esc_html( phpversion() )
	);
	echo '</strong></p></div>';
}
