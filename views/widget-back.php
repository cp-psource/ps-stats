<?php
/**
 * CP-Stats: Widget Backend View
 *
 * This file contains the viewmodel for the plugin's widget backend.
 *
 * @package   CP-Stats
 */

// Quit if accessed outside WP context.
class_exists( 'CPStats' ) || exit; ?>

<?php if ( current_user_can( 'manage_options' ) ) : ?>
<p class="meta-links settings-link">
	<a href="<?php echo esc_attr( add_query_arg( array( 'page' => 'cpstats-settings' ), admin_url( '/options-general.php' ) ) ); ?>"
		title="<?php esc_attr_e( 'Open full settings page', 'cpstats' ); ?>">
		<span class="dashicons dashicons-admin-generic"></span>
		<?php esc_html_e( 'All Settings', 'cpstats' ); ?></a>
</p>

<br>
<?php endif; ?>

<h3><?php esc_html_e( 'Widget Settings', 'cpstats' ); ?></h3>
<div>
	<label>
		<input name="cpstats[days_show]" type="number" min="1"
			   value="<?php echo esc_attr( CPStats::$_options['days_show'] ); ?>">
		<?php esc_html_e( 'days', 'cpstats' ); ?> -
		<?php esc_html_e( 'Period of data display in Dashboard', 'cpstats' ); ?>
	</label>
	<label>
		<input name="cpstats[limit]" type="number" min="1" max="100"
			   value="<?php echo esc_attr( CPStats::$_options['limit'] ); ?>">
		<?php esc_html_e( 'Number of entries in top lists', 'cpstats' ); ?>
	</label>
	<label>
		<input type="checkbox" name="cpstats[today]" value="1" <?php checked( CPStats::$_options['today'], 1 ); ?>>
		<?php esc_html_e( 'Entries in top lists only for today', 'cpstats' ); ?>
	</label>
	<label>
		<input type="checkbox" name="cpstats[show_totals]" value="1" <?php checked( CPStats::$_options['show_totals'], 1 ); ?>>
		<?php esc_html_e( 'Show totals', 'cpstats' ); ?>
	</label>
</div>
<?php wp_nonce_field( 'cpstats-dashboard' ); ?>

<p class="meta-links">
	<a href="<?php echo esc_url( __( 'https://github.com/cp-psource/cp-stats/wiki', 'cpstats' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Dokumentation', 'cpstats' ); ?></a>
	&bull; <a href="<?php echo esc_url( __( 'https://github.com/cp-psource/cp-stats/discussions', 'cpstats' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support', 'cpstats' ); ?></a>
</p>
