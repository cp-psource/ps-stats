<?php
/**
 * CPStats Filter: Settings View
 *
 * This file contains the dynamic HTML skeleton for the plugin's settings page.
 *
 * @package    CPStats_Blacklist
 * @subpackage Admin
 * @since      1.0.0
 */

// phpcs:disable ClassicPress.WhiteSpace.PrecisionAlignment.Found

// Quit.
defined( 'ABSPATH' ) || exit;

// Update plugin options.
if ( ! empty( $_POST['cpstatsblacklist'] ) ) {
	// Verify nonce.
	check_admin_referer( 'cpstats-blacklist-settings' );

	// Check user capabilities.
	if ( ! current_user_can( 'manage_options' ) ) {
		die( esc_html__( 'Are you sure you want to do this?', 'cpstats-blacklist' ) );
	}

	if ( ! empty( $_POST['cleanUp'] ) ) {
		// CleanUp DB.
		CPStatsBlacklist_Admin::cleanup_database();
	} else {
		// Extract referer array.
		if ( isset( $_POST['cpstatsblacklist']['referer']['blacklist'] ) ) {
			$referer_str = sanitize_textarea_field( wp_unslash( $_POST['cpstatsblacklist']['referer']['blacklist'] ) );
		}
		if ( empty( trim( $referer_str ) ) ) {
			$referer = array();
		} else {
			$referer = array_filter(
				array_map(
					function ( $a ) {
						return trim( $a );
					},
					explode( "\r\n", $referer_str )
				),
				function ( $a ) {
					return ! empty( $a );
				}
			);
		}

		// Extract target array.
		if ( isset( $_POST['cpstatsblacklist']['target']['blacklist'] ) ) {
			$target_str = sanitize_textarea_field( wp_unslash( $_POST['cpstatsblacklist']['target']['blacklist'] ) );
		}
		if ( empty( trim( $target_str ) ) ) {
			$target = array();
		} else {
			$target = array_filter(
				array_map(
					function ( $a ) {
						return trim( $a );
					},
					explode( "\r\n", str_replace( '\\\\', '\\', $target_str ) )
				),
				function ( $a ) {
					return ! empty( $a );
				}
			);
		}

		// Extract IP array.
		if ( isset( $_POST['cpstatsblacklist']['ip']['blacklist'] ) ) {
			$ip_str = sanitize_textarea_field( wp_unslash( $_POST['cpstatsblacklist']['ip']['blacklist'] ) );
		}
		if ( empty( trim( $ip_str ) ) ) {
			$ip = array();
		} else {
			$ip = array_filter(
				array_map(
					function ( $a ) {
						return trim( $a );
					},
					explode( "\r\n", $ip_str )
				),
				function ( $a ) {
					return ! empty( $a );
				}
			);
		}

		// Extract user agent array.
		if ( isset( $_POST['cpstatsblacklist']['ua']['blacklist'] ) ) {
			$ua_string = sanitize_textarea_field( wp_unslash( $_POST['cpstatsblacklist']['ua']['blacklist'] ) );
		}
		if ( empty( trim( $ua_string ) ) ) {
			$ua = array();
		} else {
			$ua = array_filter(
				array_map(
					function ( $a ) {
						return trim( $a );
					},
					explode( "\r\n", str_replace( '\\\\', '\\', $ua_string ) )
				),
				function ( $a ) {
					return ! empty( $a );
				}
			);
		}

		// Update options (data will be sanitized).
		$cpstatsblacklist_update_result = CPStatsBlacklist_Admin::update_options(
			array(
				'referer' => array(
					'active'    => isset( $_POST['cpstatsblacklist']['referer']['active'] )
						? (int) $_POST['cpstatsblacklist']['referer']['active'] : 0,
					'cron'      => isset( $_POST['cpstatsblacklist']['referer']['cron'] )
						? (int) $_POST['cpstatsblacklist']['referer']['cron'] : 0,
					'regexp'    => isset( $_POST['cpstatsblacklist']['referer']['regexp'] )
						? (int) $_POST['cpstatsblacklist']['referer']['regexp'] : 0,
					'blacklist' => array_flip( $referer ),
				),
				'target'  => array(
					'active'    => isset( $_POST['cpstatsblacklist']['target']['active'] )
						? (int) $_POST['cpstatsblacklist']['target']['active'] : 0,
					'cron'      => isset( $_POST['cpstatsblacklist']['target']['cron'] )
						? (int) $_POST['cpstatsblacklist']['target']['cron'] : 0,
					'regexp'    => isset( $_POST['cpstatsblacklist']['target']['regexp'] )
						? (int) $_POST['cpstatsblacklist']['target']['regexp'] : 0,
					'blacklist' => array_flip( $target ),
				),
				'ip'      => array(
					'active'    => isset( $_POST['cpstatsblacklist']['ip']['active'] )
						? (int) $_POST['cpstatsblacklist']['ip']['active'] : 0,
					'blacklist' => $ip,
				),
				'ua'      => array(
					'active'    => isset( $_POST['cpstatsblacklist']['ua']['active'] )
						? (int) $_POST['cpstatsblacklist']['ua']['active'] : 0,
					'regexp'    => isset( $_POST['cpstatsblacklist']['ua']['regexp'] )
						? (int) $_POST['cpstatsblacklist']['ua']['regexp'] : 0,
					'blacklist' => array_flip( $ua ),
				),
				'version' => CPStatsBlacklist::VERSION_MAIN,
			)
		);

		// Generate messages.
		if ( false !== $cpstatsblacklist_update_result ) {
			$cpstatsblacklist_post_warning = array();
			if ( ! empty( $cpstatsblacklist_update_result['referer']['diff'] ) ) {
				$cpstatsblacklist_post_warning[] = __( 'Some URLs are invalid and have been sanitized.', 'cpstats-blacklist' );
			}
			if ( ! empty( $cpstatsblacklist_update_result['referer']['invalid'] ) ) {
				$cpstatsblacklist_post_warning[] = __( 'Some regular expressions are invalid:', 'cpstats-blacklist' ) . '<br>' . implode( '<br>', $cpstatsblacklist_update_result['referer']['invalid'] );
			}
			if ( ! empty( $cpstatsblacklist_update_result['ip']['diff'] ) ) {
				// translators: List of invalid IP addresses (comma separated).
				$cpstatsblacklist_post_warning[] = sprintf( __( 'Some IPs are invalid: %s', 'cpstats-blacklist' ), implode( ', ', $cpstatsblacklist_update_result['ip']['diff'] ) );
			}
		} else {
			$cpstatsblacklist_post_success = __( 'Settings updated successfully.', 'cpstats-blacklist' );
		}
	}
}

/*
 * Disable some code style rules that are impractical for textarea content:
 *
 * phpcs:disable Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
 * phpcs:disable Squiz.PHP.EmbeddedPhp.ContentAfterEnd
 */
?>

<div class="wrap">
	<h1><?php esc_html_e( 'CPStats Filter', 'cpstats-blacklist' ); ?></h1>
	<?php
	if ( is_plugin_inactive( 'cpstats/cpstats.php' ) ) {
		print '<div class="notice notice-warning"><p>';
		esc_html_e( 'CPStats plugin is not active.', 'cpstats-blacklist' );
		print '</p></div>';
	}
	if ( isset( $cpstatsblacklist_post_warning ) ) {
		foreach ( $cpstatsblacklist_post_warning as $w ) {
			print '<div class="notice notice-warning"><p>' .
				wp_kses( $w, array( 'br' => array() ) ) .
				'</p></div>';
		}
		print '<div class="notice notice-warning"><p>' . esc_html__( 'Settings have not been saved yet.', 'cpstats-blacklist' ) . '</p></div>';
	}
	if ( isset( $cpstatsblacklist_post_success ) ) {
		print '<div class="notice notice-success"><p>' .
			esc_html( $cpstatsblacklist_post_success ) .
			'</p></div>';
	}
	?>
	<form action="" method="post" id="cpstats-blacklist-settings">
		<?php wp_nonce_field( 'cpstats-blacklist-settings' ); ?>

		<h2><?php esc_html_e( 'Referer filter', 'cpstats-blacklist' ); ?></h2>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_active_referer">
						<?php esc_html_e( 'Activate live filter', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="cpstatsblacklist[referer][active]"
						   id="cpstats-blacklist_active_referer"
						   value="1" <?php checked( CPStatsBlacklist::$options['referer']['active'], 1 ); ?>>
					<p class="description">
						<?php esc_html_e( 'Filter at time of tracking, before anything is stored', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_cron_referer">
						<?php esc_html_e( 'CronJob execution', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="cpstatsblacklist[referer][cron]" id="cpstats-blacklist_cron_referer"
						   value="1" <?php checked( CPStatsBlacklist::$options['referer']['cron'], 1 ); ?>>
					<p class="description"><?php esc_html_e( 'Periodically clean up database in background', 'cpstats-blacklist' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_referer_regexp"><?php esc_html_e( 'Matching method', 'cpstats-blacklist' ); ?></label>
				</th>
				<td>
					<select name="cpstatsblacklist[referer][regexp]" id="cpstats-blacklist_referer_regexp">
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_NORMAL ); ?>" <?php selected( CPStatsBlacklist::$options['referer']['regexp'], CPStatsBlacklist::MODE_NORMAL ); ?>>
							<?php esc_html_e( 'Domain', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_KEYWORD ); ?>" <?php selected( CPStatsBlacklist::$options['referer']['regexp'], CPStatsBlacklist::MODE_KEYWORD ); ?>>
							<?php esc_html_e( 'Keyword', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_REGEX ); ?>" <?php selected( CPStatsBlacklist::$options['referer']['regexp'], CPStatsBlacklist::MODE_REGEX ); ?>>
							<?php esc_html_e( 'RegEx case-sensitive', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_REGEX_CI ); ?>" <?php selected( CPStatsBlacklist::$options['referer']['regexp'], CPStatsBlacklist::MODE_REGEX_CI ); ?>>
							<?php esc_html_e( 'RegEx case-insensitive', 'cpstats-blacklist' ); ?>
						</option>
					</select>

					<p class="description">
						<?php esc_html_e( 'Domain', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match given domain including subdomains', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'Keyword', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match every referer that contains one of the keywords', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'RegEx', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match referer by regular expression', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_referer"><?php esc_html_e( 'Referer filter', 'cpstats-blacklist' ); ?></label>
				</th>
				<td>
					<textarea cols="40" rows="5" name="cpstatsblacklist[referer][blacklist]" id="cpstats-blacklist_referer"><?php
					if ( empty( $cpstatsblacklist_update_result['referer'] ) ) {
						print esc_html( implode( "\r\n", array_keys( CPStatsBlacklist::$options['referer']['blacklist'] ) ) );
					} else {
						print esc_html( implode( "\r\n", array_keys( $cpstatsblacklist_update_result['referer']['sanitized'] ) ) );
					}
					?></textarea>
					<p class="description">
						<?php esc_html_e( 'Add one domain (without subdomains) each line, e.g. example.com', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'Target filter', 'cpstats-blacklist' ); ?></h2>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_active_target">
						<?php esc_html_e( 'Activate live filter', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="cpstatsblacklist[target][active]"
						   id="cpstats-blacklist_active_target"
						   value="1" <?php checked( CPStatsBlacklist::$options['target']['active'], 1 ); ?>>
					<p class="description">
						<?php esc_html_e( 'Filter at time of tracking, before anything is stored', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_cron_target">
						<?php esc_html_e( 'CronJob execution', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="cpstatsblacklist[target][cron]" id="cpstats-blacklist_cron_target"
						   value="1" <?php checked( CPStatsBlacklist::$options['target']['cron'], 1 ); ?>>
					<p class="description">
						<?php esc_html_e( 'Clean database periodically in background', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_target_regexp">
						<?php esc_html_e( 'Matching method', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<select name="cpstatsblacklist[target][regexp]" id="cpstats-blacklist_target_regexp">
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_NORMAL ); ?>" <?php selected( CPStatsBlacklist::$options['target']['regexp'], CPStatsBlacklist::MODE_NORMAL ); ?>>
							<?php esc_html_e( 'Exact', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_REGEX ); ?>" <?php selected( CPStatsBlacklist::$options['target']['regexp'], CPStatsBlacklist::MODE_REGEX ); ?>>
							<?php esc_html_e( 'RegEx case-sensitive', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_REGEX_CI ); ?>" <?php selected( CPStatsBlacklist::$options['target']['regexp'], CPStatsBlacklist::MODE_REGEX_CI ); ?>>
							<?php esc_html_e( 'RegEx case-insensitive', 'cpstats-blacklist' ); ?>
						</option>
					</select>

					<p class="description">
						<?php esc_html_e( 'Exact', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match only given targets', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'RegEx', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match target by regular expression', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_target">
						<?php esc_html_e( 'Target filter', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<textarea cols="40" rows="5" name="cpstatsblacklist[target][blacklist]" id="cpstats-blacklist_target"><?php
					if ( empty( $cpstatsblacklist_update_result['target'] ) ) {
						print esc_html( implode( "\r\n", array_keys( CPStatsBlacklist::$options['target']['blacklist'] ) ) );
					} else {
						print esc_html( implode( "\r\n", array_keys( $cpstatsblacklist_update_result['target']['sanitized'] ) ) );
					}
					?></textarea>

					<p class="description">
						<?php esc_html_e( 'Add one target URL each line, e.g.', 'cpstats-blacklist' ); ?> /, /test/page/, /?page_id=123
					</p>
				</td>
			</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'IP filter', 'cpstats-blacklist' ); ?></h2>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_active_ip">
						<?php esc_html_e( 'Activate live filter', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="cpstatsblacklist[ip][active]" id="cpstats-blacklist_active_ip"
						   value="1" <?php checked( CPStatsBlacklist::$options['ip']['active'], 1 ); ?>>
					<p class="description">
						<?php esc_html_e( 'Filter at time of tracking, before anything is stored', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'Cron execution is not possible for IP filter, because IP addresses are not stored.', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_ip"><?php esc_html_e( 'IP filter', 'cpstats-blacklist' ); ?></label>:
				</th>
				<td>
					<textarea cols="40" rows="5" name="cpstatsblacklist[ip][blacklist]" id="cpstats-blacklist_ip"><?php
					if ( empty( $cpstatsblacklist_update_result['ip'] ) ) {
						print esc_html( implode( "\r\n", CPStatsBlacklist::$options['ip']['blacklist'] ) );
					} else {
						print esc_html( implode( "\r\n", $cpstatsblacklist_update_result['ip']['sanitized'] ) );
					}
					?></textarea>

					<p class="description">
						<?php esc_html_e( 'Add one IP address or range per line, e.g.', 'cpstats-blacklist' ); ?>
						127.0.0.1, 192.168.123.0/24, 2001:db8:a0b:12f0::1/64
					</p>
				</td>
			</tr>
			</tbody>
		</table>

		<h2><?php esc_html_e( 'User agent filter', 'cpstats-blacklist' ); ?></h2>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_active_ua">
						<?php esc_html_e( 'Activate live filter', 'cpstats-blacklist' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="cpstatsblacklist[ua][active]" id="cpstats-blacklist_active_ua"
						   value="1" <?php checked( CPStatsBlacklist::$options['ua']['active'], 1 ); ?>>
					<p class="description">
						<?php esc_html_e( 'Filter at time of tracking, before anything is stored', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'Cron execution is not possible for user agent filter, because the user agent is stored.', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_ua_regexp"><?php esc_html_e( 'Matching method', 'cpstats-blacklist' ); ?></label>
				</th>
				<td>
					<select name="cpstatsblacklist[ua][regexp]" id="cpstats-blacklist_ua_regexp">
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_NORMAL ); ?>" <?php selected( CPStatsBlacklist::$options['ua']['regexp'], CPStatsBlacklist::MODE_NORMAL ); ?>>
							<?php esc_html_e( 'Exact', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_KEYWORD ); ?>" <?php selected( CPStatsBlacklist::$options['ua']['regexp'], CPStatsBlacklist::MODE_KEYWORD ); ?>>
							<?php esc_html_e( 'Keyword', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_REGEX ); ?>" <?php selected( CPStatsBlacklist::$options['ua']['regexp'], CPStatsBlacklist::MODE_REGEX ); ?>>
							<?php esc_html_e( 'RegEx case-sensitive', 'cpstats-blacklist' ); ?>
						</option>
						<option value="<?php print esc_attr( CPStatsBlacklist::MODE_REGEX_CI ); ?>" <?php selected( CPStatsBlacklist::$options['ua']['regexp'], CPStatsBlacklist::MODE_REGEX_CI ); ?>>
							<?php esc_html_e( 'RegEx case-insensitive', 'cpstats-blacklist' ); ?>
						</option>
					</select>

					<p class="description">
						<?php esc_html_e( 'Exact', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match only given user agents', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'Keyword', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match every referer that contains one of the keywords', 'cpstats-blacklist' ); ?>
						<br>
						<?php esc_html_e( 'RegEx', 'cpstats-blacklist' ); ?> - <?php esc_html_e( 'Match user agent by regular expression', 'cpstats-blacklist' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cpstats-blacklist_ua"><?php esc_html_e( 'User agent filter', 'cpstats-blacklist' ); ?></label>:
				</th>
				<td>
					<textarea cols="40" rows="5" name="cpstatsblacklist[ua][blacklist]" id="cpstats-blacklist_ua"><?php
					if ( empty( $cpstatsblacklist_update_result['ua'] ) ) {
						print esc_html( implode( "\r\n", array_keys( CPStatsBlacklist::$options['ua']['blacklist'] ) ) );
					} else {
						print esc_html( implode( "\r\n", array_keys( $cpstatsblacklist_update_result['ua']['sanitized'] ) ) );
					}
					?></textarea>

					<p class="description">
						<?php esc_html_e( 'Add one user agent string per line, e.g.', 'cpstats-blacklist' ); ?>
						MyBot/1.23
					</p>
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit">
			<input class="button-primary" type="submit" name="submit" value="<?php esc_html_e( 'Save Changes', 'cpstats-blacklist' ); ?>">
			<hr>
			<input class="button-secondary" type="submit" name="cleanUp"
				   value="<?php esc_html_e( 'CleanUp Database', 'cpstats-blacklist' ); ?>"
				   onclick="return confirm('Do you really want to apply filters to database? This cannot be undone.');">
			<br>
			<p class="description">
				<?php esc_html_e( 'Applies referer and target filter (even if disabled) to data stored in database.', 'cpstats-blacklist' ); ?>
				<em><?php esc_html_e( 'This cannot be undone!', 'cpstats-blacklist' ); ?></em>
			</p>
		</p>
	</form>
</div>
