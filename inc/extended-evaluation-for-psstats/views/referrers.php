<?php
/**
 * The referrers page.
 *
 * @package extended-evaluation-for-psstats
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Get the selected post if one is set, otherwise: all posts.
if ( isset( $_POST['post'] ) && check_admin_referer( 'referrers' ) ) {
	$selected_post = sanitize_text_field( wp_unslash( $_POST['post'] ) );
} else {
	$selected_post = '';
}

// Reset variables and get post parameters for the dates if submitted.
$valid_start = false;
$valid_end = false;
$message = '';
$start = '';
$end = '';

// Check for at least one date set and valid wp_nonce.
if ( isset( $_POST['start'], $_POST['end'] ) && check_admin_referer( 'referrers' ) ) {
	$start = sanitize_text_field( wp_unslash( $_POST['start'] ) );
	$end = sanitize_text_field( wp_unslash( $_POST['end'] ) );
	if ( '' !== $start || '' !== $end ) {
		$valid_start = eefpsstats_is_valid_date_string( $start );
		$valid_end = eefpsstats_is_valid_date_string( $end );
		if ( ! $valid_start || ! $valid_end ) {
			// Error message if at least one date is not valid.
			$message = __( 'No valid date period set. Please enter a valid start and a valid end date!', 'psstats' );
		}
	}
}

$referrers = eefpsstats_get_views_for_all_referrers( $selected_post, $start, $end );
$referrers_for_diagram = array_slice( $referrers, 0, 24, true );

$filename = eefpsstats_get_filename(
	__( 'Referrers', 'psstats' )
	. eefpsstats_get_date_period_string( $start, $end, $valid_start && $valid_end )
	. '-' . eefpsstats_get_post_title_from_url( $selected_post )
);
?>
<div class="wrap eefpsstats">
	<h1><?php esc_html_e( 'PSStats – Extended Evaluation', 'psstats' ); ?>
			&rsaquo; <?php esc_html_e( 'Referrers from other websites', 'psstats' ); ?></h1>
	<?php if ( '' !== $message ) { ?>
	<div class="notice notice-error">
		<p><?php echo esc_html( $message ); ?></p>
	</div>
	<?php } ?>
	<form method="post">
		<?php wp_nonce_field( 'referrers' ); ?>
		<?php eefpsstats_echo_date_selection( $valid_start, $start, $valid_end, $end ); ?>
		<fieldset>
			<legend><?php esc_html_e( 'Per default the views of all posts are shown. To restrict the evaluation to one post/page, enter their path or name.', 'psstats' ); ?></legend>
			<?php eefpsstats_echo_post_selection( $selected_post ); ?>
			<button type="submit" class="button-secondary"><?php esc_html_e( 'Select post/page', 'psstats' ); ?></button>
		</fieldset>
	</form>
<?php if ( count( $referrers ) === 0 ) { ?>
	<p><?php esc_html_e( 'No data available.', 'psstats' ); ?></p>
<?php } else { ?>
	<section>
		<?php
		$legend = [];
		foreach ( $referrers_for_diagram as $referrer ) {
			$legend[] = $referrer['host'];
		}

		eefpsstats_echo_chart_container(
			'chart-referrers',
			__( 'Referrers from other websites', 'psstats' ),
			eefpsstats_get_post_title_from_url( $selected_post ) . eefpsstats_get_date_period_string( $start, $end, $valid_start && $valid_end, true ),
			$legend
		);
		?>
		<script type="text/javascript">
			eefpsstatsColumnChart(
				'#chart-referrers',
				[
					<?php
					foreach ( $referrers_for_diagram as $referrer ) {
						echo "['" . esc_js( $referrer['host'] ) . "'," . esc_js( $referrer['count'] ) . '],';
					}
					?>
				]
			);
		</script>
	</section>	
	<section>
		<h3><?php esc_html_e( 'Referrers from other websites', 'psstats' ); ?>
			<?php echo esc_html( eefpsstats_get_date_period_string( $start, $end, $valid_start && $valid_end, true ) ); ?>
			<?php
			echo esc_html( eefpsstats_get_post_type_name_and_title_from_url( $selected_post ) );
			eefpsstats_echo_export_button( $filename );
			?>
		</h3>
		<table id="table-data" class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Referring Domain', 'psstats' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Views', 'psstats' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Proportion', 'psstats' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total = 0;
				foreach ( $referrers as $referrer ) {
					$total += $referrer['count'];
				}
				foreach ( $referrers as $referrer ) {
					?>
				<tr>
					<td><a href="<?php echo esc_url( $referrer['url'] ); ?>" target="_blank"><?php echo esc_html( $referrer['host'] ); ?></a></td>
					<td class="right"><?php eefpsstats_echo_number( $referrer['count'] ); ?></td>
					<td class="right"><?php eefpsstats_echo_percentage( $referrer['count'] / $total ); ?></td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td><?php esc_html_e( 'Sum', 'psstats' ); ?></td>
					<td class="right"><?php eefpsstats_echo_number( $total ); ?></td>
					<td class="right"><?php eefpsstats_echo_percentage( 1 ); ?></td>
				</tr>
			</tfoot>
		</table>
	</section>
	<?php } ?>
</div>
