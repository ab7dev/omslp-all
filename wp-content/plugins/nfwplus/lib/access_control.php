<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Block immediately if user is not allowed
nf_not_allowed( 'block', __LINE__ );

$nfw_options = nfw_get_option( 'nfw_options' );

define('NFW_ISO_CSV', __DIR__ . '/share/iso3166.csv');

// Tab and div display:
if ( empty( $_REQUEST['tab'] ) ) { $_REQUEST['tab'] = 'general'; }

if ( $_REQUEST['tab'] == 'geolocation' ) {
	$geolocation_tab = ' nav-tab-active'; $geolocation_div = '';

	$general_tab = ''; $general_div = ' style="display:none"';
	$ip_tab = ''; $ip_div = ' style="display:none"';
	$url_tab = ''; $url_div = ' style="display:none"';
	$bot_tab = ''; $bot_div = ' style="display:none"';
	$input_tab = ''; $input_div = ' style="display:none"';

} elseif ( $_REQUEST['tab'] == 'ip' ) {
	$ip_tab = ' nav-tab-active'; $ip_div = '';

	$general_tab = ''; $general_div = ' style="display:none"';
	$geolocation_tab = ''; $geolocation_div = ' style="display:none"';
	$url_tab = ''; $url_div = ' style="display:none"';
	$bot_tab = ''; $bot_div = ' style="display:none"';
	$input_tab = ''; $input_div = ' style="display:none"';

} elseif ( $_REQUEST['tab'] == 'url' ) {
	$url_tab = ' nav-tab-active'; $url_div = '';

	$general_tab = ''; $general_div = ' style="display:none"';
	$ip_tab = ''; $ip_div = ' style="display:none"';
	$geolocation_tab = ''; $geolocation_div = ' style="display:none"';
	$bot_tab = ''; $bot_div = ' style="display:none"';
	$input_tab = ''; $input_div = ' style="display:none"';

} elseif ( $_REQUEST['tab'] == 'bot' ) {
	$bot_tab = ' nav-tab-active'; $bot_div = '';

	$general_tab = ''; $general_div = ' style="display:none"';
	$geolocation_tab = ''; $geolocation_div = ' style="display:none"';
	$ip_tab = ''; $ip_div = ' style="display:none"';
	$url_tab = ''; $url_div = ' style="display:none"';
	$input_tab = ''; $input_div = ' style="display:none"';

} elseif ( $_REQUEST['tab'] == 'input' ) {
	$input_tab = ' nav-tab-active'; $input_div = '';

	$general_tab = ''; $general_div = ' style="display:none"';
	$geolocation_tab = ''; $geolocation_div = ' style="display:none"';
	$ip_tab = ''; $ip_div = ' style="display:none"';
	$url_tab = ''; $url_div = ' style="display:none"';
	$bot_tab = ''; $bot_div = ' style="display:none"';

} else {
	$_REQUEST['tab'] = 'general';
	$general_tab = ' nav-tab-active'; $general_div = '';

	$geolocation_tab = ''; $geolocation_div = ' style="display:none"';
	$ip_tab = ''; $ip_div = ' style="display:none"';
	$url_tab = ''; $url_div = ' style="display:none"';
	$bot_tab = ''; $bot_div = ' style="display:none"';
	$input_tab = ''; $input_div = ' style="display:none"';
}

?>
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="<?php echo plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) ?>">&nbsp;<?php esc_html_e('Access Control', 'nfwplus') ?></h1>
<?php

// Saved options ?
if ( isset( $_POST['nfw_options'] ) ) {
	if ( empty( $_POST['nfwnonce'] ) || ! wp_verify_nonce( $_POST['nfwnonce'], 'ac_save') ) {
		wp_nonce_ays('ac_save');
	}
	if (! empty( $_POST['save_acd'] ) ) {
		$ac_error = nf_sub_access_save();
		if ( $ac_error ) {
			echo '<div class="notice-warning notice is-dismissible"><p>'.
				esc_html__('Some of your changes have been saved, but not all because of syntax errors.', 'nfwplus') .
			'</p></div>';
		} else {
			echo '<div class="notice-success notice is-dismissible"><p>'.
				esc_html__('Your changes have been saved.', 'nfwplus') .
			'</p></div>';
		}
	} elseif (! empty( $_POST['rest_acd'] ) ) {
		nf_sub_access_default();
		echo '<div class="updated notice is-dismissible"><p>'. esc_html__('Default values were restored.', 'nfwplus') . '</p></div>';
	} else {
		echo '<div class="error notice is-dismissible"><p>'. esc_html__('No action taken.', 'nfwplus') . '</p></div>';
	}
	$nfw_options = nfw_get_option( 'nfw_options' );
}

if ( empty( $nfw_options['ac_roles'] ) ) {
	$nfw_options['ac_roles'] = '|administrator|'; // Keep the trailing vertical bar
}

if ( defined('NFW_WPWAF') ) {
	?>
	<div style="background:#fff;border-left:4px solid #fff;-webkit-box-shadow:0 1px 1px 0 rgba(0,0,0,.1);box-shadow:0 1px 1px 0 rgba(0,0,0,.1);margin:5px 0 15px;padding:1px 12px;border-left-color:orange;">
		<p><?php printf( esc_html__('You are running NinjaFirewall in WordPress WAF mode. All URL-based features such as Geolocation and URL Access Control will be limited to WordPress files only (e.g., index.php, wp-login.php, xmlrpc.php, admin-ajax.php, wp-load.php etc). If you want them to apply to any PHP script, please %sgo to the Dashboard page%s and enable NinjaFirewall\'s Full WAF mode.', 'nfwplus'), '<a href="?page=NinjaFirewall">', '</a>') ?></p>
	</div>
	<?php
}

?>
<br />
<h2 class="nav-tab-wrapper wp-clearfix" style="cursor:pointer">
	<a id="tab-general" class="nav-tab<?php echo $general_tab ?>" onClick="nfwjs_switch_tabs('general', 'general:geolocation:ip:url:bot:input')"><?php esc_html_e( 'General', 'nfwplus' ) ?></a>
	<a id="tab-geolocation" class="nav-tab<?php echo $geolocation_tab ?>" onClick="nfwjs_switch_tabs('geolocation', 'general:geolocation:ip:url:bot:input')"><?php esc_html_e( 'Geolocation', 'nfwplus' ) ?></a>
	<a id="tab-ip" class="nav-tab<?php echo $ip_tab ?>" onClick="nfwjs_switch_tabs('ip', 'general:geolocation:ip:url:bot:input')"><?php esc_html_e( 'IP address', 'nfwplus' ) ?></a>
	<a id="tab-url" class="nav-tab<?php echo $url_tab ?>" onClick="nfwjs_switch_tabs('url', 'general:geolocation:ip:url:bot:input')"><?php esc_html_e( 'URL address', 'nfwplus' ) ?></a>
	<a id="tab-bot" class="nav-tab<?php echo $bot_tab ?>" onClick="nfwjs_switch_tabs('bot', 'general:geolocation:ip:url:bot:input')"><?php esc_html_e( 'Bot', 'nfwplus' ) ?></a>
	<a id="tab-input" class="nav-tab<?php echo $input_tab ?>" onClick="nfwjs_switch_tabs('input', 'general:geolocation:ip:url:bot:input')"><?php esc_html_e( 'User Input', 'nfwplus' ) ?></a>
	<?php nfw_contextual_help(); ?>
</h2>
<br />

<form name="ac_form" method="post" action="?page=nfsubaccess" onSubmit="return check_ac_fields();">
	<?php wp_nonce_field('ac_save', 'nfwnonce', 0); ?>

	<!-- General Access Control -->

	<div id="general-options"<?php echo $general_div ?>>

	<h3><?php esc_html_e('Role-based Access Control', 'nfwplus') ?></h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Do not block the following users', 'nfwplus') ?></th>
			<td>
			<?php
			// Fetch all roles:
			global $wpdb;
			$user_roles = get_option("{$wpdb->base_prefix}user_roles");

			foreach ( $user_roles as $role => $cap ) {
				if ( strpos( $nfw_options['ac_roles'], "|{$role}|" ) !== false ) {
					$nfw_options['ac_roles_form'][$role] = 1;
				} else {
					$nfw_options['ac_roles_form'][$role] = 0;
				}
				$role = htmlspecialchars( $role );
				$cap['name'] = htmlspecialchars( $cap['name'] );
				?>
				<p><label><input type="checkbox" name="nfw_options[ac_roles_form][<?php echo $role ?>]" value="1"<?php checked($nfw_options['ac_roles_form'][$role], 1) ?>>&nbsp;<?php esc_html_e( $cap['name'] ); echo " (<code>{$role}</code>)"; ?></label></p>
				<?php
			}
			?>
			<p class="description"><?php esc_html_e('Users must log out and log in back again to apply changes', 'nfwplus') ?></p>
			</td>
		</tr>
	</table>

	<a name="sourceip"></a>
	<br />
	<br />

	<?php
	if ( empty( $nfw_options['ac_ip'] ) ) {
		$nfw_options['ac_ip'] = 1;
		$nfw_options['ac_ip_2'] = '';
	}
	if ( empty( $nfw_options['ac_ip_2'] ) ) {
		$nfw_options['ac_ip_2'] = '';
	}
	// Make sure the selected source IP is valid or display an error:
	$warn_msg = '';
	if ( $nfw_options['ac_ip'] == 3 && ! empty( $nfw_options['ac_ip_2'] ) && empty( $_SERVER[$nfw_options['ac_ip_2']] ) ) {
		$warn_msg = htmlspecialchars( $nfw_options['ac_ip_2'] );
	} elseif ( $nfw_options['ac_ip'] == 2 && empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$warn_msg = 'HTTP_X_FORWARDED_FOR';
	}
	?>
	<h3><?php esc_html_e('Source IP', 'nfwplus') ?></h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Retrieve visitors IP address from', 'nfwplus') ?></th>
			<td>
				<p><label><input type="radio" name="nfw_options[ac_ip]" value="1"<?php checked($nfw_options['ac_ip'], 1) ?> onclick="ac_radio_toggle(0,'ac_ip_2');" />&nbsp;<code>REMOTE_ADDR</code> (<?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']) ?>)</label></p>
				<?php
				if (! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				?>
					<p><label><input type="radio" name="nfw_options[ac_ip]" value="2"<?php checked( $nfw_options['ac_ip'], 2 ) ?> onclick="ac_radio_toggle(0,'ac_ip_2');" />&nbsp;HTTP_X_FORWARDED_FOR<?php echo ' ('. htmlspecialchars( $_SERVER['HTTP_X_FORWARDED_FOR'] ) .')';	?></label></p>
				<?php
				}
				?>
				<p><label><input type="radio" name="nfw_options[ac_ip]" value="3"<?php checked( $nfw_options['ac_ip'], 3 ) ?> onclick="ac_radio_toggle(1,'ac_ip_2');" />&nbsp;<?php esc_html_e('Other', 'nfwplus') ?></label>&nbsp;<input class="small-text code" type="text" style="width:250px;" id="ac_ip_2" name="nfw_options[ac_ip_2]" value="<?php echo htmlspecialchars($nfw_options['ac_ip_2']) ?>" placeholder="<?php esc_html_e('e.g.,', 'nfwplus') ?> HTTP_X_FORWARDED_FOR"<?php
				if ( empty( $nfw_options['ac_ip_2'] ) ) {
					echo " disabled";
				}
				?> /></p><?php
				if ( $warn_msg ) {
					echo '<p class="description" style="color:red">&nbsp;'.sprintf( esc_html__('Your server does not seem to support the %s variable.', 'nfwplus'), $warn_msg ) .'</p>';
				}
				?>
			</td>
		</tr>
		<?php
		if ( empty( $nfw_options['allow_local_ip'] ) ) {
			$allow_local_ip = 0;
		} else {
			$allow_local_ip = 1;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Scan traffic coming from localhost and private IP address spaces', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[allow_local_ip]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['allow_local_ip'] ) ?>
			</td>
		</tr>
	</table>

	<br />
	<br />

	<?php
	if ( empty($nfw_options['ac_method']) ) {
		$nfw_options['ac_method'] = 'GETPOSTHEADPUTDELETEPATCH';
	}
	?>
	<h3><?php esc_html_e('HTTP Methods', 'nfwplus') ?></h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('All Access Control directives should apply to the folowing HTTP methods', 'nfwplus') ?></th>
			<td style="width:100px">
				<p><label><input type="checkbox" name="nfw_options[ac_method_0]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'GET' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>GET</code></label></p>
				<p><label><input type="checkbox" name="nfw_options[ac_method_1]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'POST' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>POST</code></label></p>
				<p><label><input type="checkbox" name="nfw_options[ac_method_2]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'HEAD' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>HEAD</code></label></p>
			</td>
			<td>
				<p><label><input type="checkbox" name="nfw_options[ac_method_3]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'PUT' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>PUT</code></label></p>
				<p><label><input type="checkbox" name="nfw_options[ac_method_4]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'DELETE' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>DELETE</code></label></p>
				<p><label><input type="checkbox" name="nfw_options[ac_method_5]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'PATCH' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>PATCH</code></label></p>
				<p><label><input type="checkbox" name="nfw_options[ac_method_6]" value="1"<?php if ( strpos( $nfw_options['ac_method'], 'PROPFIND' ) !== FALSE) { echo ' checked'; } ?>>&nbsp;<code>PROPFIND</code></label></p>
			</td>
		</tr>
	</table>

	</div>

	<!-- GeoIP Access Control -->

	<div id="geolocation-options"<?php echo $geolocation_div ?>>

	<?php
	if ( empty($nfw_options['ac_geoip']) ) {
		$nfw_options['ac_geoip'] = 0;
	}
	?>
	<h3><?php esc_html_e('Geolocation Access Control', 'nfwplus') ?></h3>
	<table class="form-table">
		<tr>
			<td>
				<table class="form-table nfw-table">
					<tr style="background-color:#F9F9F9;border:solid 1px #DFDFDF;">
						<th scope="row" class="row-med">&nbsp;<?php esc_html_e('Enable Geolocation', 'nfwplus') ?></th>
						<td>
							<?php nfw_toggle_switch( 'green', 'nfw_options[ac_geoip]', esc_html__('Enabled', 'nfwplus'), esc_html__('Disabled', 'nfwplus'), 'large', $nfw_options['ac_geoip'], false, 'onclick="nfwjs_up_down(\'geotable\');"' ) ?>
						</td>
					</tr>
				</table>

				<div id="geotable"<?php if ( empty( $nfw_options['ac_geoip'] ) ) {echo ' style="display:none;"';} ?>>
				<br />
				<br />

				<?php
				if ( empty( $nfw_options['ac_geoip_db2'] ) ) {
					$nfw_options['ac_geoip_db2'] = '';
				}
				$no_db = $no_var = '';
				if ( empty( $nfw_options['ac_geoip_db'] ) ) {
					$nfw_options['ac_geoip_db'] = 1;
				}
				$no_var = '';
				if ( $nfw_options['ac_geoip_db'] == 2 ) {
					if (! empty( $nfw_options['ac_geoip_db2'] ) && ( empty( $_SERVER[$nfw_options['ac_geoip_db2']] ) ||
						! preg_match( '/^[A-Z0-9]{2}$/', $_SERVER[$nfw_options['ac_geoip_db2']] ) ) ) {
						// Variable is undefined:
						$no_var = '<p class="description" style="color:red">&nbsp;'.
							sprintf(
								_('Your server does not seem to support the %s variable.'),
								htmlspecialchars( $nfw_options['ac_geoip_db2'] )
							) .
							'</p>';
						$nfw_options['ac_geoip_db'] = 0;
					} elseif ( empty( $nfw_options['ac_geoip_db2'] ) ) {
						$nfw_options['ac_geoip_db2'] = '';
						$nfw_options['ac_geoip_db'] = 1;
					}
				}
				?>
				<table class="form-table nfw-table">
					<tr valign="top">
						<th scope="row" class="row-med"><?php esc_html_e('Retrieve the ISO 3166 code from', 'nfwplus') ?></th>
						<td>
							<p><label><input type="radio" name="nfw_options[ac_geoip_db]" value="1"<?php checked( $nfw_options['ac_geoip_db'], 1 ) ?> onclick="ac_radio_toggle(0,'ac_geoip_db2');" />&nbsp;NinjaFirewall</label></p>
							<p><label><input type="radio" name="nfw_options[ac_geoip_db]" value="2"<?php checked( $nfw_options['ac_geoip_db'], 2 ) ?> onclick="ac_radio_toggle(1,'ac_geoip_db2');" />&nbsp;<?php esc_html_e('PHP Variable', 'nfwplus') ?></label>
							<input class="small-text code" style="width:250px;" type="text" id="ac_geoip_db2" name="nfw_options[ac_geoip_db2]" value="<?php echo htmlspecialchars($nfw_options['ac_geoip_db2']) ?>" placeholder="<?php esc_html_e('e.g.,', 'nfwplus') ?> GEOIP_COUNTRY_CODE"<?php
							if ( empty( $nfw_options['ac_geoip_db2'] ) ) {
								echo " disabled";
							}
							?> /></p>
							<?php echo $no_var ?>
						</td>
					</tr>

					<tr>
						<th scope="row" class="row-med"><?php esc_html_e('Block the following ISO 3166 codes', 'nfwplus') ?></th>
						<td id="td-countries">
						<?php
							if ( empty( $nfw_options['ac_geoip_cn'] ) ) {
								$nfw_options['ac_geoip_cn'] = '';
							}
							$count = 0;
							$buffer = '';
							$blocked_cn = explode( '|', $nfw_options['ac_geoip_cn'] );
							$csv_array = file( NFW_ISO_CSV, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
							$row = 0;
							foreach ( $csv_array as $line ) {
								$checked = '';
								if ( preg_match( '/^(\w\w),"(.+?)"$/', $line, $match ) ) {
									if ( in_array( $match[1], $blocked_cn ) ) {
										$checked = ' checked';
										++$count;
									} else {
										if ( $match[1] == 'A1' || $match[1] == 'A2' || $match[1] == 'O1' ) {
											continue;
										}
									}
									++$row;
									if ( $row % 2 == 0 ) {
										$r_color = 'f-white';
									} else {
										$r_color = 'f-grey';
									}
									// "country-class" is only used by the JS check_ac_fields function
									$buffer .= '<tr class="'. $r_color .'"><td class="country-list"><label class="geo"><input type="checkbox" class="country-class" onClick="nfw_update_counter(this)" name="country['. htmlspecialchars( $match[1] ) .']"'. $checked .' /> '. htmlspecialchars( $match[1] ) .' - '. htmlspecialchars( $match[2] ) .'</label></td></tr>';
								}
							}
							?>
							<?php
								printf( esc_html__('Total blocked items: %s', 'nfwplus'), '<font id="total-items">'. (int) $count .'</font>' );
							?>
							<div class="f-sub">
								<table class="form-table">
									<?php echo $buffer; ?>
								</table>
							</div>
							<p class="description2"><a id="check-code" href="javascript:" onclick="nfw_check_countries(1)">Check all</a> - <a id="uncheck-code" href="javascript:" onclick="nfw_check_countries(0)">Uncheck all</a></p>
						</td>
					</tr>

					<?php
					$list = '';
					if ( empty( $nfw_options['ac_geo_url'] ) ) {
						$nfw_options['ac_geo_url'] = '';
					} else {
						$urls =  explode('|',  preg_replace( '/\\\([`.\\\+*?\[^\]$(){}=!<>|:-])/', '$1', $nfw_options['ac_geo_url'] ));
						sort( $urls );
						foreach ($urls as $url) {
							if (! empty( $url ) ) {
								$list .= htmlspecialchars( trim( $url ) ) ."\n";
							}
						}
					}
					?>
					<tr>
						<th scope="row" class="row-med">
							<?php esc_html_e('Geolocation should apply to the whole site or to specific URLs only?', 'nfwplus') ?>
							<br />
							<br />
							<font class="description2">
								<span><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-geo-url');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
								<div id="view-geo-url" style="display:none">
									<ul class="view">
										<li><?php printf( esc_html__('Full or partial case-sensitive URL (e.g., %s).', 'nfwplus'), '<code>/wp-login.php</code>, <code>/foo/bar/</code>' ) ?></li>
										<li><?php esc_html_e('One item per line.', 'nfwplus' ) ?></li>
									</ul>
								</div>
							</font>
						</th>
						<td>
							<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ac_geo_url]" class="large-text code" rows="10" placeholder="<?php esc_html_e('Leave this field empty if you want geolocation to apply to all your PHP scripts.', 'nfwplus') ?>"><?php echo $list ?></textarea>
						</td>
					</tr>
					<?php
					if ( empty($nfw_options['ac_geoip_ninja']) ) {
						$nfw_options['ac_geoip_ninja'] = 0;
					} else {
						$nfw_options['ac_geoip_ninja'] = 1;
					}
					?>
					<tr>
						<th scope="row" class="row-med"><?php esc_html_e('Add NINJA_COUNTRY_CODE to PHP headers?', 'nfwplus') ?></th>
						<td>
							<?php nfw_toggle_switch( 'info', 'nfw_options[ac_geoip_ninja]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_geoip_ninja'] ) ?>
						</td>
					</tr>
					<?php
					if (empty($nfw_options['ac_geoip_log']) ) {
						$nfw_options['ac_geoip_log'] = 0;
					} else {
						$nfw_options['ac_geoip_log'] = 1;
					}
					?>
					<tr>
						<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
						<td id="td-countries">
							<?php nfw_toggle_switch( 'info', 'nfw_options[ac_geoip_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_geoip_log'] ) ?>
						</td>
					</tr>
				</table>

				</div> <!-- geotable -->
			</td>
		</tr>
	</table>

	</div>

	<!-- IP Access Control -->

	<div id="ip-options"<?php echo $ip_div ?>>

	<a name="ipaccess"></a>

	<h3><?php esc_html_e('IP Access Control', 'nfwplus') ?></h3>

	<?php
	$ip_list = array();
	$asn_list = array();
	if (! empty( $nfw_options['ac_allow_ip'] ) ) {
		$ip_list = unserialize( $nfw_options['ac_allow_ip'] );
		ksort( $ip_list );
	}
	if (! empty( $nfw_options['ac_allow_asn'] ) ) {
		$asn_list = unserialize( $nfw_options['ac_allow_asn'] );
		ksort( $asn_list );
	}
	?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Allow the following IP, CIDR or AS number', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-ip-allow');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-ip-allow" style="display:none">
						<ul class="view">
							<li><?php printf( esc_html__('IPv4 address: %s', 'nfwplus') , '<code>66.155.10.20</code>' ) ?></li>
							<li><?php printf( esc_html__('IPv4 CIDR: %s', 'nfwplus') , '<code>66.155.0.0/17</code>' ) ?></li>
							<li><?php printf( esc_html__('IPv6 address: %s', 'nfwplus') , '<code>2001:db8:85a3::8a2e</code>' ) ?></li>
							<li><?php printf( esc_html__('IPv6 CIDR: %s', 'nfwplus') , '<code>2c0f:f248::/32</code>' ) ?></li>
							<li><?php printf( esc_html__('Autonomous System number: %s', 'nfwplus') , '<code>AS15169</code>' ) ?></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php esc_html_e('Whitelist:', 'nfwplus') ?><br />
				<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ip_allowed]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php
				foreach( $asn_list as $asn ) {
					echo htmlspecialchars( $asn ) ."\n";
				}
				foreach( $ip_list as $ip ) {
					echo htmlspecialchars( $ip ) ."\n";
				}
				?></textarea>
			</td>
		</tr>

		<?php
		// External services
		$nf_gateways = array();
		if (! empty( $nfw_options['nf_gateways'] ) ) {
			$nf_gateways = unserialize( $nfw_options['nf_gateways'] );
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('External Services', 'nfwplus') ?></th>
			<td>
				<p><?php esc_html_e('Whitelist IP addresses from the following services:', 'nfwplus' ) ?></p>
				<?php
				include __DIR__ .'/fw_gateways.php';
				foreach( $gateways_ip as $gateway => $ips ) {
				?>
					<br /><label><input type="checkbox" name="nf_gateways[<?php echo htmlspecialchars( $gateway ) ?>]"<?php
					if (! empty( $nf_gateways[$gateway] ) ) {
							echo ' checked';
						}
					?> /> <?php echo htmlspecialchars( $gateways_info[$gateway]['name'] ) ?></label> <font class="description2">
					<span><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('<?php echo htmlspecialchars( $gateway ) ?>');">[+] <?php esc_html_e('View IP addresses', 'nfwplus') ?></a></span>
					<div id="<?php echo htmlspecialchars( $gateway ) ?>" style="display:none">
						<ul class="view">
						<?php
						foreach ( $ips as $ip ) {
							echo '<li><code>'. htmlspecialchars( $ip ) .'</code></li>';
						}
						?>
						</ul>
					</div>
				</font>
				<br />
				<?php
				}
				?>
			</td>
		</tr>

		<?php
		if ( empty( $nfw_options['ac_allow_ip_log'] ) ) {
			$nfw_options['ac_allow_ip_log'] = 0;
		} else {
			$nfw_options['ac_allow_ip_log'] = 1;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_allow_ip_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_allow_ip_log'] ) ?>
			</td>
		</tr>
	</table>

	<br />
	<br />

	<?php
	$ip_list = array();
	$asn_list = array();
	if (! empty( $nfw_options['ac_block_ip'] ) ) {
		$ip_list = unserialize( $nfw_options['ac_block_ip'] );
		ksort( $ip_list );
	}
	if (! empty( $nfw_options['ac_block_asn'] ) ) {
		$asn_list = unserialize( $nfw_options['ac_block_asn'] );
		ksort( $asn_list );
	}
	?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Block the following IP, CIDR or AS number', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-ip-block');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-ip-block" style="display:none">
						<ul class="view">
							<li><?php printf( esc_html__('IPv4 address: %s', 'nfwplus') , '<code>66.155.10.20</code>' ) ?></li>
							<li><?php printf( esc_html__('IPv4 CIDR: %s', 'nfwplus') , '<code>66.155.0.0/17</code>' ) ?></li>
							<li><?php printf( esc_html__('IPv6 address: %s', 'nfwplus') , '<code>2001:db8:85a3::8a2e</code>' ) ?></li>
							<li><?php printf( esc_html__('IPv6 CIDR: %s', 'nfwplus') , '<code>2c0f:f248::/32</code>' ) ?></li>
							<li><?php printf( esc_html__('Autonomous System number: %s', 'nfwplus') , '<code>AS15169</code>' ) ?></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php esc_html_e('Blacklist:', 'nfwplus') ?><br />
				<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ip_blocked]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php
				foreach( $asn_list as $asn ) {
					echo htmlspecialchars( $asn ) ."\n";
				}
				foreach( $ip_list as $ip ) {
					echo htmlspecialchars( $ip ) ."\n";
				}
				?></textarea>
			</td>
		</tr>
		<?php
		if ( empty( $nfw_options['ac_block_ip_log'] ) ) {
			$nfw_options['ac_block_ip_log'] = 0;
		} else {
			$nfw_options['ac_block_ip_log'] = 1;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_block_ip_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_block_ip_log'] ) ?>
			</td>
		</tr>
	</table>

	<br />
	<br />

	<?php
	$style = '';
	if ( empty( $nfw_options['ac_rl_on'] ) ) {
		$nfw_options['ac_rl_on'] = 0;
		$style = ' style="display:none"';
	} else {
		$nfw_options['ac_rl_on'] = 1;
	}

	if ( empty($nfw_options['ac_rl_conn']) || ! preg_match('/^[1-9][0-9]{0,2}$/', $nfw_options['ac_rl_conn']) ) {
		$nfw_options['ac_rl_conn'] = 10;
		$nfw_options['ac_rl_intv'] = 5;
	}
	if ( empty($nfw_options['ac_rl_time']) || ! preg_match('/^\d{1,3}$/', $nfw_options['ac_rl_time']) ) {
		$nfw_options['ac_rl_time'] = 30;
	}
	if ( empty($nfw_options['ac_rl_intv']) || ! preg_match('/^(5|1[05]|30)$/', $nfw_options['ac_rl_intv']) ) {
		$nfw_options['ac_rl_conn'] = 10;
		$nfw_options['ac_rl_intv'] = 5;
	}
	?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Rate Limiting', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_rl_on]', esc_html__('Enabled', 'nfwplus'), esc_html__('Disabled', 'nfwplus'), 'large', $nfw_options['ac_rl_on'], false, 'onclick="nfwjs_toggle_ratelimit()"' ) ?>
				<div id="rate-limit"<?php echo $style?>>
				<br />
				<br />
				<?php
				printf(
					esc_html__('Block for %s seconds any IP address with more than %s connections within a %s interval.', 'nfwplus'),
					'<input class="small-text" type="number" name="nfw_options[ac_rl_time]" value="'. $nfw_options['ac_rl_time'] .'"  id="acrltime" size="2" max="999" maxlength="3" />',
					'<input class="small-text" type="number" id="acrlconn" name="nfw_options[ac_rl_conn]" value="'. $nfw_options['ac_rl_conn'] .'" size="2" max="999" maxlength="3" />',
					'<select name="nfw_options[ac_rl_intv]" ><option value="5" '. selected($nfw_options['ac_rl_intv'], 5, 0) .'>'. esc_html__('5-second', 'nfwplus') .'</option><option value="10" '. selected($nfw_options['ac_rl_intv'], 10, 0) .'>'. esc_html__('10-second', 'nfwplus') .'</option><option value="15" '. selected($nfw_options['ac_rl_intv'], 15, 0) .'>'. esc_html__('15-second', 'nfwplus') .'</option><option value="30" '. selected($nfw_options['ac_rl_intv'], 30, 0) .'>'. esc_html__('30-second', 'nfwplus') .'</option></select>'
					);
				?>
				</div>
			</td>
		</tr>
		<?php
		if (empty($nfw_options['ac_rl_log']) ) {
			$nfw_options['ac_rl_log'] = 0;
		} else {
			$nfw_options['ac_rl_log'] = 1;
		}
		?>
		<tr id="rate-limit-log"<?php echo $style?>>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_rl_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_rl_log'] ) ?>
			</td>
		</tr>
	</table>

	</div>


	<!-- URL Access Control -->

	<div id="url-options"<?php echo $url_div ?>>

	<a name="urlaccess"></a>

	<h3><?php esc_html_e('URL Access Control', 'nfwplus') ?></h3>

	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Allow access to the following URL', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-allow-url');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-allow-url" style="display:none">
						<ul class="view">
							<li><?php printf( esc_html__('Full or partial case-sensitive relative URLs (e.g., %s).', 'nfwplus'), '<code>/script.php</code>, <code>/foo/bar/</code>' ) ?></li>
							<li><?php esc_html_e('One item per line.', 'nfwplus' ) ?></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php esc_html_e('Whitelist:', 'nfwplus') ?><br />
				<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ac_wl_url]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php
				$urls = array();
				if (! empty( $nfw_options['ac_wl_url'] ) ) {
					$urls =  explode('|',  preg_replace( '/\\\([`.\\\+*?\[^\]$(){}=!<>|:-])/', '$1', $nfw_options['ac_wl_url'] ));
					sort( $urls );
					foreach ($urls as $url) {
						if ( $url ) {
							echo htmlspecialchars( $url ) ."\n";
						}
					}
				}
				?></textarea>
			</td>
		</tr>
		<?php
		if (! isset( $nfw_options['ac_wl_url_log'] ) ) {
			$nfw_options['ac_wl_url_log'] = 0;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_wl_url_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_wl_url_log'] ) ?>
			</td>
		</tr>
	</table>

	<br />
	<br />

	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Block access to the following URL', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-block-url');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-block-url" style="display:none">
						<ul class="view">
							<li><?php printf( esc_html__('Full or partial case-sensitive relative URLs (e.g., %s).', 'nfwplus'), '<code>/script.php</code>, <code>/wp-admin/</code>' ) ?></li>
							<li><?php esc_html_e('One item per line.', 'nfwplus' ) ?></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php esc_html_e('Blacklist:', 'nfwplus') ?><br />
				<textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ac_bl_url]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php
				$urls = array();
				if (! empty( $nfw_options['ac_bl_url'] ) ) {
					$urls =  explode('|',  preg_replace( '/\\\([`.\\\+*?\[^\]$(){}=!<>|:-])/', '$1', $nfw_options['ac_bl_url'] ));
					sort( $urls );
					foreach ($urls as $url) {
						if ( $url ) {
							echo htmlspecialchars( $url ) ."\n";
						}
					}
				}
				?></textarea>
			</td>
		</tr>
		<?php
		if (empty($nfw_options['ac_bl_url_log']) ) {
			$nfw_options['ac_bl_url_log'] = 0;
		} else {
			$nfw_options['ac_bl_url_log'] = 1;
		}

		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_bl_url_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_bl_url_log'] ) ?>
			</td>
		</tr>
	</table>

	</div>


	<!-- Bot Access Control -->

	<div id="bot-options"<?php echo $bot_div ?>>

	<h3><?php esc_html_e('Bot Access Control', 'nfwplus') ?></h3>

	<?php
	$default_list = NFW_BOT_LIST;
	$bot_list = '';
	if (! empty( $nfw_options['ac_bl_bot'] ) ) {
		$bot_list = stripslashes( str_replace( '|', "\n", $nfw_options['ac_bl_bot'] ) );
	}
	?>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Reject the following bots', 'nfwplus') ?> (<code>HTTP_USER_AGENT</code>)
				<br />
				<br />
				<font class="description2">
					<span id="view-bot-span"><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-bot');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-bot" style="display:none">
						<ul class="view">
							<li><?php esc_html_e('A full or partial case-insensitive string.', 'nfwplus') ?></li>
							<li><?php printf( esc_html__('Allowed characters are: %s and %s.', 'nfwplus'), '<code>a-zA-Z</code> <code>0-9</code> <code>.</code> <code>-</code> <code>_</code> <code>:</code> <code>/</code> <code>(</code> <code>)</code> <code>"</code> <code>\'</code> <code>,</code> <code>;</code>' , '<code>space</code>') ?></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php esc_html_e('Blocked bots:', 'nfwplus') ?><br />
				<textarea id="bot-blocked" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[bot_blocked]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php echo htmlspecialchars( $bot_list ); ?></textarea>

				<p class="description2"><a id="check-code" href="javascript:" onclick="nfwjs_restore_bots('<?php echo htmlspecialchars( $default_list ) ?>', '<?php esc_html_e('The default list of bots will be restored. Continue?', 'nfwplus') ?>')"><?php esc_html_e('Restore default bots list', 'nfwplus') ?></a></p>
			</td>
		</tr>
		<?php
		if (empty($nfw_options['ac_bl_bot_log']) ) {
			$nfw_options['ac_bl_bot_log'] = 0;
		} else {
			$nfw_options['ac_bl_bot_log'] = 1;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_bl_bot_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_bl_bot_log'] ) ?>
			</td>
		</tr>
	</table>

	</div>

	<!-- User Input Access Control -->

	<div id="input-options"<?php echo $input_div ?>>

	<a name="inputaccess"></a>
	<h3><?php esc_html_e('User Input Access Control', 'nfwplus') ?></h3>

	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Do not filter the following user input', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span id="view-bot-span"><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-allow-input');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-allow-input" style="display:none">
						<br />
						<?php esc_html_e('A GET, POST or COOKIE global variable, followed by a colon (:) and the case-sensitive input:', 'nfwplus') ?>
						<ul class="view">
							<li><code>GET:foo</code></li>
							<li><code>POST:foo</code></li>
							<li><code>COOKIE:foo</code></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php
				$list = '';
				if (! empty( $nfw_options['ac_wl_input'] ) ) {
					$input = unserialize( $nfw_options['ac_wl_input'] );
					foreach( $input as $global => $x ) {
						foreach( $input[$global] as $var => $y ) {
							if ( $input ) {
								$list .= htmlspecialchars( "$global:$var" ) ."\n";
							}
						}
					}
				}
				?>
				<?php esc_html_e('Unfiltered input:', 'nfwplus' ) ?><br />
				<textarea id="bot-blocked" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ac_wl_input]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php echo $list ?></textarea>
			</td>
		</tr>
		<?php
		if ( empty( $nfw_options['ac_wl_input_log'] ) ) {
			$nfw_options['ac_wl_input_log'] = 0;
		} else {
			$nfw_options['ac_wl_input_log'] = 1;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'warning', 'nfw_options[ac_wl_input_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_wl_input_log'], false, 'onClick="return alert_input_log(this)"' ) ?>
			</td>
		</tr>
	</table>

	<br />
	<br />

	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">
				<?php esc_html_e('Block the request if the following input is present', 'nfwplus') ?>
				<br />
				<br />
				<font class="description2">
					<span id="view-bot-span"><a href="javascript:" style="text-decoration:none;" onClick="nfwjs_view_format('view-block-input');">[+] <?php esc_html_e('View allowed syntax', 'nfwplus') ?></a></span>
					<div id="view-block-input" style="display:none">
						<br />
						<?php esc_html_e('A GET, POST or COOKIE global variable, followed by a colon (:) and the case-sensitive input:', 'nfwplus') ?>
						<ul class="view">
							<li><code>GET:foo</code></li>
							<li><code>POST:foo</code></li>
							<li><code>COOKIE:foo</code></li>
						</ul>
					</div>
				</font>
			</th>
			<td>
				<?php
				$list = '';
				if (! empty( $nfw_options['ac_bl_input'] ) ) {
					$input = unserialize( $nfw_options['ac_bl_input'] );
					foreach( $input as $global => $x ) {
						foreach( $input[$global] as $var => $y ) {
							if ( $input ) {
								$list .= htmlspecialchars( "$global:$var" ) ."\n";
							}
						}
					}
				}
				?>
				<?php esc_html_e('Blocked input:', 'nfwplus' ) ?><br />
				<textarea id="bot-blocked" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="nfw_options[ac_bl_input]" class="large-text code" rows="15" placeholder="<?php esc_html_e('Enter one item per line.', 'nfwplus') ?>"><?php echo $list ?></textarea>
			</td>
		</tr>
		<?php
		if (empty($nfw_options['ac_bl_input_log']) ) {
			$nfw_options['ac_bl_input_log'] = 0;
		} else {
			$nfw_options['ac_bl_input_log'] = 1;
		}
		?>
		<tr>
			<th scope="row" class="row-med"><?php esc_html_e('Write event to the firewall log', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'info', 'nfw_options[ac_bl_input_log]', esc_html__('Yes', 'nfwplus'), esc_html__('No', 'nfwplus'), 'small', $nfw_options['ac_bl_input_log'] ) ?>
			</td>
		</tr>
	</table>

	</div>

	<br />
	<br />
	<input type="hidden" name="tab" id="tab-selected" value="<?php echo htmlspecialchars( $_REQUEST['tab']  ) ?>" />
	<input type="submit" class="button-primary" name="save_acd" value="<?php esc_html_e('Save Access Control directives', 'nfwplus') ?>" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input class="button-secondary" type="submit" name="rest_acd" value="<?php esc_html_e('Restore Default Values', 'nfwplus') ?>" onclick="return nfwjs_restore_default();" />
</form>

</div>
<?php

// =====================================================================

function nf_sub_access_save(){

	// Save Access Control options

	global $nfw_options;

	$rejected_items = 0;

	// Role-based access control
	$nfw_options['ac_roles'] = '|';
	if (! empty($_POST['nfw_options']['ac_roles_form'] ) ) {
		foreach( $_POST['nfw_options']['ac_roles_form'] as $role => $v ) {
			$nfw_options['ac_roles'] .= $role .'|'; // Keep the trailing vertical bar
		}
	}

	// Enable/Disable good guy's flag
	if ( nfw_is_goodguy( null ) ) {
		$_SESSION['nfw_goodguy'] = true;
	} else {
		if ( isset( $_SESSION['nfw_goodguy'] ) ) {
			unset( $_SESSION['nfw_goodguy'] );
		}
	}

	// Source IP
	if ( (empty( $_POST['nfw_options']['ac_ip']) || ! preg_match('/^[123]$/', $_POST['nfw_options']['ac_ip']) ) ||
		( $_POST['nfw_options']['ac_ip'] == 3 && empty($_POST['nfw_options']['ac_ip_2']) ) ) {
		$nfw_options['ac_ip'] = 1;
		$nfw_options['ac_ip_2'] = 0;
	} else {
		$nfw_options['ac_ip'] = $_POST['nfw_options']['ac_ip'];
		if (! empty($_POST['nfw_options']['ac_ip_2']) ) {
			$nfw_options['ac_ip_2'] = trim( $_POST['nfw_options']['ac_ip_2'] );
		} else {
			$nfw_options['ac_ip_2'] = 0;
		}
	}
	// Scan server/local IP
	if ( empty( $_POST['nfw_options']['allow_local_ip']) ) {
		$nfw_options['allow_local_ip'] = 0;
	} else {
		$nfw_options['allow_local_ip'] = 1;
	}

	// HTTP Methods:
	if ( empty($_POST['nfw_options']['ac_method_0']) &&
		empty($_POST['nfw_options']['ac_method_1']) &&
		empty($_POST['nfw_options']['ac_method_2']) &&
		empty($_POST['nfw_options']['ac_method_3']) &&
		empty($_POST['nfw_options']['ac_method_4']) &&
		empty($_POST['nfw_options']['ac_method_5']) &&
		empty($_POST['nfw_options']['ac_method_6']) ) {
		$nfw_options['ac_method'] = 'GETPOSTHEADPUTDELETEPATCH';
	} else {
		$nfw_options['ac_method'] = '';
		if (! empty($_POST['nfw_options']['ac_method_0']) ) {
			$nfw_options['ac_method'] .= 'GET';
		}
		if (! empty($_POST['nfw_options']['ac_method_1']) ) {
			$nfw_options['ac_method'] .= 'POST';
		}
		if (! empty($_POST['nfw_options']['ac_method_2']) ) {
			$nfw_options['ac_method'] .= 'HEAD';
		}
		if (! empty($_POST['nfw_options']['ac_method_3']) ) {
			$nfw_options['ac_method'] .= 'PUT';
		}
		if (! empty($_POST['nfw_options']['ac_method_4']) ) {
			$nfw_options['ac_method'] .= 'DELETE';
		}
		if (! empty($_POST['nfw_options']['ac_method_5']) ) {
			$nfw_options['ac_method'] .= 'PATCH';
		}
		if (! empty($_POST['nfw_options']['ac_method_6']) ) {
			$nfw_options['ac_method'] .= 'PROPFIND';
		}
	}

	// Geolocation:
	// Enable?
	if ( empty( $_POST['nfw_options']['ac_geoip']) ) {
		// Default: no
		$nfw_options['ac_geoip'] = 0;
	} else {
		$nfw_options['ac_geoip'] = 1;
	}
	// DB to use:
	if (! @preg_match( '/^[12]$/', $_POST['nfw_options']['ac_geoip_db']) ) {
		$nfw_options['ac_geoip_db'] = 0;
		$nfw_options['ac_geoip_db2'] = '';
	} else {
		$nfw_options['ac_geoip_db'] = $_POST['nfw_options']['ac_geoip_db'];
		if ( empty($_POST['nfw_options']['ac_geoip_db2']) ) {
			$nfw_options['ac_geoip_db2'] = '';
		} else {
			$nfw_options['ac_geoip_db2'] = trim( $_POST['nfw_options']['ac_geoip_db2'] );
		}
	}
	// Countries to block
	$nfw_options['ac_geoip_cn'] = '';
	if (! empty( $_POST['country'] ) && is_array( $_POST['country'] ) ) {
		foreach( $_POST['country'] as $code => $null ) {
			$nfw_options['ac_geoip_cn'] .= $code . '|';
		}
	}
	$nfw_options['ac_geoip_cn'] = rtrim( $nfw_options['ac_geoip_cn'], '|' );
	// Geolocation (URLs)
	$tmp_geourl = '';
	if (! empty( $_POST['nfw_options']['ac_geo_url'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ac_geo_url'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $url ) {
			$url = trim( $url );
			if ( empty( $url ) ) { continue; }
			$tmp_geourl .= preg_quote( trim ( $url ), '`') . '|';
		}
	}
	if (! empty( $tmp_geourl ) ) {
		$nfw_options['ac_geo_url'] = rtrim( $tmp_geourl, '|' );
	} else {
		$nfw_options['ac_geo_url'] = 0;
	}
	// NINJA_COUNTRY_CODE:
	if ( empty( $_POST['nfw_options']['ac_geoip_ninja'] ) ) {
		// Default: no
		$nfw_options['ac_geoip_ninja'] = 0;
	} else {
		$nfw_options['ac_geoip_ninja'] = 1;
	}
	if ( isset( $_POST['nfw_options']['ac_geoip_log']) ) {
		$nfw_options['ac_geoip_log'] = 1;
	} else {
		$nfw_options['ac_geoip_log'] = 0;
	}


	/**
	 * Allow IP/CIDR/ASN addresses.
	 */
	$ip_list		= [];
	$asn_list	= [];
	if (! empty( $_POST['nfw_options']['ip_allowed'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ip_allowed'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $item ) {
			$item = trim( $item );
			if ( empty( $item ) ) {
				continue;
			}
			/**
			 * IP/CIDR.
			 */
			if ( preg_match('/^([a-f0-9.:]{3,45})(?:\/\d{1,2})?$/i', $item, $match ) ) {
				/**
				 * Verify the syntax or ignore it.
				 */
				if ( filter_var( $match[1], FILTER_VALIDATE_IP ) )  {
					$ip_list[] = strtolower( $item );
				} else {
					$rejected_items = 1;
				}
				continue;
			}
			/**
			 * ASN.
			 */
			if ( preg_match('/^ASN?\d+$/i', $item ) ) {
				$item = str_ireplace('ASN', 'AS', $item );
				$asn_list[] = strtoupper( $item );
				continue;
			}
			/**
			 * None of the above, reject it.
			 */
			$rejected_items = 1;
		}
	}
	if (! empty( $ip_list ) ) {
		$nfw_options['ac_allow_ip'] = serialize( $ip_list );
	} else {
		$nfw_options['ac_allow_ip'] = 0;
	}
	if (! empty( $asn_list ) ) {
		$nfw_options['ac_allow_asn'] = serialize( $asn_list );
	} else {
		$nfw_options['ac_allow_asn'] = 0;
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_allow_ip_log']) ) {
		$nfw_options['ac_allow_ip_log'] = 1;
	} else {
		$nfw_options['ac_allow_ip_log'] = 0;
	}

	// Check if we need to whitelist external services:
	$nfw_options['nf_gateways'] = '';
	if (! empty( $_POST['nf_gateways'] ) ) {
		$gtw = array();
		include __DIR__ .'/fw_gateways.php';
		foreach( $_POST['nf_gateways'] as $gateway => $on ) {
			if (! empty( $gateways_ip[ $gateway ] ) ) {
				$gtw[$gateway] = 1;
			}
		}
		$nfw_options['nf_gateways'] = serialize( $gtw );
	}

	/**
	 * Block IP/CIDR/ASN addresses.
	 */
	$ip_list		= [];
	$asn_list	= [];
	if (! empty( $_POST['nfw_options']['ip_blocked'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ip_blocked'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $item ) {
			$item = trim( $item );
			if ( empty( $item ) ) {
				continue;
			}
			/**
			 * IP/CIDR.
			 */
			if ( preg_match('/^([a-f0-9.:]{3,45})(?:\/\d{1,2})?$/i', $item, $match ) ) {
				/**
				 * Verify the syntax or ignore it.
				 */
				if ( filter_var( $match[1], FILTER_VALIDATE_IP ) )  {
					$ip_list[] = strtolower( $item );
				} else {
					$rejected_items = 1;
				}
				continue;
			}
			/**
			 * ASN.
			 */
			if ( preg_match('/^ASN?\d+$/i', $item ) ) {
				$item = str_ireplace('ASN', 'AS', $item );
				$asn_list[] = strtoupper( $item );
				continue;
			}
			/**
			 * None of the above, reject it.
			 */
			$rejected_items = 1;
		}
	}
	if (! empty( $ip_list ) ) {
		$nfw_options['ac_block_ip'] = serialize( $ip_list );
	} else {
		$nfw_options['ac_block_ip'] = 0;
	}
	if (! empty( $asn_list ) ) {
		$nfw_options['ac_block_asn'] = serialize( $asn_list );
	} else {
		$nfw_options['ac_block_asn'] = 0;
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_block_ip_log']) ) {
		$nfw_options['ac_block_ip_log'] = 1;
	} else {
		$nfw_options['ac_block_ip_log'] = 0;
	}


	// Rate Limiting
	if (empty($_POST['nfw_options']['ac_rl_on']) ) {
		$nfw_options['ac_rl_on'] = 0;
	} else {
		$nfw_options['ac_rl_on'] = 1;
	}
	if ( empty($_POST['nfw_options']['ac_rl_time']) || ! preg_match('/^\d{1,3}$/', $_POST['nfw_options']['ac_rl_time']) ) {
		$nfw_options['ac_rl_time'] = 30;
	} else {
		$nfw_options['ac_rl_time'] = $_POST['nfw_options']['ac_rl_time'];
	}
	if ( empty($_POST['nfw_options']['ac_rl_conn']) || ! preg_match('/^[1-9][0-9]{0,2}$/', $_POST['nfw_options']['ac_rl_conn']) ) {
		$nfw_options['ac_rl_conn'] = 10;
		$nfw_options['ac_rl_intv'] = 5;
		$nfw_options['ac_rl_time'] = 30;
	} else {
		$nfw_options['ac_rl_conn'] = $_POST['nfw_options']['ac_rl_conn'];
	}
	if ( empty($_POST['nfw_options']['ac_rl_intv']) || ! preg_match('/^(5|1[05]|30)$/', $_POST['nfw_options']['ac_rl_intv']) ) {
		$nfw_options['ac_rl_conn'] = 10;
		$nfw_options['ac_rl_intv'] = 5;
		$nfw_options['ac_rl_time'] = 30;
	} else {
		$nfw_options['ac_rl_intv'] = $_POST['nfw_options']['ac_rl_intv'];
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_rl_log']) ) {
		$nfw_options['ac_rl_log'] = 1;
	} else {
		if (empty($nfw_options['ac_rl_on']) ) {
			$nfw_options['ac_rl_log'] = 1;
		} else {
			$nfw_options['ac_rl_log'] = 0;
		}
	}



	// Allowed URLs
	$url_list = '';
	if (! empty( $_POST['nfw_options']['ac_wl_url'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ac_wl_url'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $url ) {
			$url = trim( $url );
			if ( empty( $url ) ) { continue; }
			$url_list .= preg_quote( $url, '`') . '|';
		}
		$nfw_options['ac_wl_url'] = rtrim( $url_list, '|' );
	} else {
		$nfw_options['ac_wl_url'] = 0;
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_wl_url_log']) ) {
		$nfw_options['ac_wl_url_log'] = 1;
	} else {
		$nfw_options['ac_wl_url_log'] = 0;
	}


	// Block URLs
	$url_list = '';
	if (! empty( $_POST['nfw_options']['ac_bl_url'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ac_bl_url'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $url ) {
			$url = trim( $url );
			if ( empty( $url ) ) { continue; }
			$url_list .= preg_quote( $url, '`') . '|';
		}
		$nfw_options['ac_bl_url'] = rtrim( $url_list, '|' );
	} else {
		$nfw_options['ac_bl_url'] = 0;
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_bl_url_log']) ) {
		$nfw_options['ac_bl_url_log'] = 1;
	} else {
		$nfw_options['ac_bl_url_log'] = 0;
	}


	// Block user-agents/bots
	$blocked_bot = '';
	if (! empty( $_POST['nfw_options']['bot_blocked'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['bot_blocked'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $item ) {
			$item	= preg_replace( '`[^-:/_.0-9a-zA-Z,;"\'() ]`', '', stripslashes( $item ) );
			if (! empty( $item ) ) {
				$blocked_bot .= preg_quote( trim ( strtolower( $item ) ) ) .'|';
			}
		}
		$blocked_bot = rtrim( $blocked_bot, '|' );
	}
	if ( empty( $blocked_bot ) ) {
		$nfw_options['ac_bl_bot'] = 0;
	} else {
		$nfw_options['ac_bl_bot'] = $blocked_bot;
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_bl_bot_log']) ) {
		$nfw_options['ac_bl_bot_log'] = 1;
	} else {
		$nfw_options['ac_bl_bot_log'] = 0;
	}


	// Unfiltered input
	$input_list = array();
	$nfw_options['ac_wl_input'] = '';
	if (! empty( $_POST['nfw_options']['ac_wl_input'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ac_wl_input'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $item ) {
			if ( empty( $item ) || strpos( $item, ':' ) === false ) { continue; }
			list( $var, $val ) = explode( ':', $item );
			$var = strtoupper( $var );
			$val = trim( $val );
			if ( preg_match( '/^(GET|POST|COOKIE)$/', $var ) ) {
				$input_list[$var][$val] = 1;
			}
		}
		if (! empty( $input_list ) ) {
			$nfw_options['ac_wl_input'] = serialize( $input_list );
		}
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_wl_input_log']) ) {
		$nfw_options['ac_wl_input_log'] = 1;
	} else {
		$nfw_options['ac_wl_input_log'] = 0;
	}


	// Blocked input
	$input_list = array();
	$nfw_options['ac_bl_input'] = '';
	if (! empty( $_POST['nfw_options']['ac_bl_input'] ) ) {
		$items = explode( "\r\n", $_POST['nfw_options']['ac_bl_input'] );
		$items = array_unique( $items );
		sort( $items );
		foreach( $items as $item ) {
			if ( empty( $item ) || strpos( $item, ':' ) === false ) { continue; }
			list( $var, $val ) = explode( ':', $item );
			$var = strtoupper( $var );
			$val = trim( $val );
			if ( preg_match( '/^(GET|POST|COOKIE)$/', $var ) ) {
				$input_list[$var][$val] = 1;
			}
		}
		if (! empty( $input_list ) ) {
			$nfw_options['ac_bl_input'] = serialize( $input_list );
		}
	}
	// Log event
	if ( isset( $_POST['nfw_options']['ac_bl_input_log']) ) {
		$nfw_options['ac_bl_input_log'] = 1;
	} else {
		$nfw_options['ac_bl_input_log'] = 0;
	}


	// Update
	nfw_update_option( 'nfw_options', $nfw_options );

	/**
	 * Inform the user if some items were rejected (invalid syntax etc).
	 */
	return $rejected_items;

}
// =====================================================================

function nf_sub_access_default() {

	// Restore Access Control default values

	global $nfw_options;

	$_SESSION['nfw_goodguy'] = true;
	$nfw_options['ac_roles'] = '|administrator|'; // Keep the trailing vertical bar

	$nfw_options['ac_ip'] = 1;
	$nfw_options['ac_ip_2'] = 0;
	$nfw_options['allow_local_ip'] = 1;  // 1 == no !
	$nfw_options['ac_method'] = 'GETPOSTHEADPUTDELETEPATCH';
	$nfw_options['ac_geoip'] = 0;
	$nfw_options['ac_geoip_db'] = 1;
	$nfw_options['ac_geoip_db2'] = '';
	$nfw_options['ac_geoip_cn'] = '';
	$nfw_options['ac_geo_url'] = '';
	$nfw_options['ac_geoip_ninja'] = 0;

	$nfw_options['ac_allow_ip'] = 0;
	$nfw_options['nf_gateways'] = '';
	$nfw_options['ac_block_ip'] = 0;
	$nfw_options['ac_rl_on'] = 0;
	$nfw_options['ac_rl_time'] = 30;
	$nfw_options['ac_rl_conn'] = 10;
	$nfw_options['ac_rl_intv'] = 5;

	$nfw_options['ac_bl_url'] = 0;
	$nfw_options['ac_wl_url'] = 0;
	$nfw_options['ac_allow_asn'] = 0;
	$nfw_options['ac_block_asn'] = 0;

	$nfw_options['ac_bl_bot'] = NFW_BOT_LIST;

	$nfw_options['ac_bl_input'] = '';
	$nfw_options['ac_wl_input'] = '';

	$nfw_options['ac_geoip_log'] = 1;
	$nfw_options['ac_allow_ip_log'] = 0;
	$nfw_options['ac_block_ip_log'] = 1;
	$nfw_options['ac_rl_log'] = 1;
	$nfw_options['ac_wl_url_log'] = 0;
	$nfw_options['ac_bl_url_log'] = 1;
	$nfw_options['ac_bl_bot_log'] = 1;
	$nfw_options['ac_wl_input_log'] = 0;
	$nfw_options['ac_bl_input_log'] = 1;

	// Update
	nfw_update_option( 'nfw_options', $nfw_options );

}

// =====================================================================
// EOF
