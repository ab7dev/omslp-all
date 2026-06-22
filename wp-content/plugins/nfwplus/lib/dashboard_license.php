<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

$nfw_options = nfw_get_option( 'nfw_options' );

if (! empty($_POST['nfw_what']) ) {
	if ( empty($_POST['nfwnonce_license']) || ! wp_verify_nonce($_POST['nfwnonce_license'], 'license_save') ) {
		wp_nonce_ays('license_save');
	}
	if ( is_multisite() ) {
		$nfw_site_url = rtrim( strtolower( network_site_url('','http')), '/' );
	} else {
		$nfw_site_url = rtrim( strtolower(site_url('','http')), '/' );
	}

	global $wp_version;
	$opt_update = 0;
	if ( $_POST['nfw_what'] == 'check' ) {
		if ( empty( $nfw_options['lic'] ) ) {
			echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('Error: you do not have any license.', 'nfwplus') . '</p></div>';
		} else {
			$nfw_options['lic'] = trim( $nfw_options['lic'] );
			$request_string = array(
				'body' 	=> array(
					'action' => 'checklicense',
					'host'	=> strtolower( $_SERVER['HTTP_HOST'] ),
					'name'	=> strtolower( $_SERVER['SERVER_NAME'] ),
					'lic' 	=> $nfw_options['lic'],
					'ver'		=> NFW_ENGINE_VERSION
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . $nfw_site_url
			);

			$res = wp_remote_post('https://api.nintechnet.com/ninjafirewall/wpplus-update', $request_string);

			if (! is_wp_error($res) ) {
				if ( $res['response']['code'] == 200 ) {
					$nfw_res = unserialize( $res['body'] );
					if (! empty($nfw_res['exp']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $nfw_res['exp']) ) {
						$nfw_options['lic_exp'] = $nfw_res['exp'];
						$opt_update = 1;
						// Don't display any 'success' message
						// if the license has expired
						if ( $nfw_options['lic_exp'] >= date('Y-m-d', strtotime("-1 day")) ){
							echo '<div class="nfw-notice nfw-notice-green"><p>'. esc_html__('You have a valid license.', 'nfwplus') . '</p></div>';
						}
					} elseif (! empty($nfw_res['ret']) ) {
						if ( $nfw_res['ret'] > 9 && $nfw_res['ret'] < 20 ) {
							echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('Your license is not valid', 'nfwplus') . ' (#'. htmlspecialchars( $nfw_res['ret'] ) .')</p></div>';
							$nfw_options['lic_exp'] = 0;
							$opt_update = 1;
						} else {
							echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An unknown error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . '.</p></div>';
						}
					} else {
						echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . ' (#1).</p></div>';
					}
				} else {
					echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . ' (#2).</p></div>';
				}
			} else {
				echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . ' (#3).</p></div>';
			}
		}

	} elseif ( $_POST['nfw_what'] == 'renew' ) {
		// Ensure there is a license
		if ( empty( $_POST['new_lic'] ) ) {
			echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('Enter a valid license to save!', 'nfwplus') . '</p></div>';
		} else {
			// Use stripslashes() to prevent WordPress from escaping the variable
			$_POST['new_lic'] = stripslashes( $_POST['new_lic'] );
			if (! empty($nfw_options['lic']) && $nfw_options['lic'] == $_POST['new_lic'] ) {
				echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('This is already your current license!', 'nfwplus') . '</p></div>';
			} else {
				$_POST['new_lic'] = trim( $_POST['new_lic'] );
				// Let's check it :
				$request_string = array(
					'body' 	=> array(
						'action' => 'checklicense',
						'host'	=> strtolower( $_SERVER['HTTP_HOST'] ),
						'name'	=> strtolower( $_SERVER['SERVER_NAME'] ),
						'lic' 	=>  $_POST['new_lic'],
						'ver'		=> NFW_ENGINE_VERSION
					),
					'user-agent' => 'WordPress/' . $wp_version . '; ' . $nfw_site_url
				);

				$res = wp_remote_post('https://api.nintechnet.com/ninjafirewall/wpplus-update', $request_string);

				if (! is_wp_error($res) ) {
					if ( $res['response']['code'] == 200 ) {
						$nfw_res = unserialize( $res['body'] );
						if (! empty($nfw_res['exp']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $nfw_res['exp']) ) {
							// Save it :
							$nfw_options['lic_exp'] = $nfw_res['exp'];
							$nfw_options['lic'] = $_POST['new_lic'];
							$opt_update = 1;
							echo '<div class="nfw-notice nfw-notice-green"><p>'. esc_html__('Your new license has been accepted and saved.', 'nfwplus') . '</p></div>';
						} elseif (! empty($nfw_res['ret']) ) {
							if ( $nfw_res['ret'] > 9 && $nfw_res['ret'] < 20 ) {
								echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('This license is not valid', 'nfwplus') . ' (#'. htmlspecialchars( $nfw_res['ret'] ) .')</p></div>';
							} else {
								echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An unknown error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . '.</p></div>';
							}
						} else {
							echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . ' (#10).</p></div>';
						}
					} else {
						echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . ' (#20).</p></div>';
					}
				} else {
					echo '<div class="nfw-notice nfw-notice-red"><p>'. esc_html__('An error occured while connecting to NinjaFirewall servers. Please try again in a few minutes', 'nfwplus') . ' (#30).</p></div>';
				}
			}
		}
	}
	// Update options if needed
	if ( $opt_update ) {
		nfw_update_option( 'nfw_options', $nfw_options);
	}
}

if (empty($nfw_options['lic']) ) {
	$lic = '';
} else {
	$lic = $nfw_options['lic'];
}

$lic_exp_warn = $renew = 0;
if (! empty($nfw_options['lic_exp']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $nfw_options['lic_exp']) ) {
	$lic_exp = $nfw_options['lic_exp'];
	if ( $lic_exp < date('Y-m-d', strtotime("-1 day")) ){
		$lic_exp_warn = -1;
	} elseif ( $lic_exp < date('Y-m-d', strtotime("+30 day")) ){
		$lic_exp_warn = 30;
	}
} else {
	$lic_exp = '';
}
?>
<h3><?php esc_html_e('WP+ Edition License', 'nfwplus') ?></h3>
<?php
echo '<form method="post" name="lic_check">';
	wp_nonce_field('license_save', 'nfwnonce_license', 0);
	echo '
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">'. esc_html__('License Number', 'nfwplus') . '</th>
			<td>';
if (! $lic ) {
	$renew = 1;
	echo '<span class="dashicons dashicons-dismiss nfw-danger"></span> '. esc_html__('No license found', 'nfwplus') . '</td>';
} else {
	echo '<input type="text" name="lic_check" value="'. htmlspecialchars( $lic ) .'" class="large-text" readonly />
			<p><input class="button-secondary" type="submit" name="Check" value="'. esc_html__('Click to check your license validity', 'nfwplus') . '" /></p>
			</td>';
			}
echo '</tr>';

if ( $lic ) {
	echo '
		<tr>
			<th scope="row" class="row-med">'. esc_html__('Expiration date', 'nfwplus') . '</th>
			<td>';
	if (! $lic_exp ) {
		$renew = 1;
		echo '<span class="dashicons dashicons-dismiss nfw-danger"></span>';
		$lic_exp = ''. esc_html__('Unknown expiration date', 'nfwplus') . '<br /><span class="description">'. esc_html__('Use the "Check License Validity" button to attempt to fix this error.', 'nfwplus') . '</span>';
	} elseif ( $lic_exp_warn > 0 ) {
		$renew = 1;
		echo '<span class="dashicons dashicons-warning nfw-warning"></span>';
		$lic_exp .= '&nbsp;&nbsp;<span class="description"><font color="red">'. esc_html__('Your license will expire soon!', 'nfwplus') . '</font></span>';
	} elseif ( $lic_exp_warn < 0 ) {
		$renew = 1;
		echo '<span class="dashicons dashicons-dismiss nfw-danger"></span>';
		$lic_exp = '<span class="description"><font color="red">'. esc_html__('Your license has expired.', 'nfwplus') . '</font></span>';
	} else {
		$renew = 0;
		echo '<span class="dashicons dashicons-yes-alt nfw-success"></span>';
	}

	echo ' '. $lic_exp .'</td>
		</tr>';
}
echo '
	</table>
	<input type="hidden" name="nfw_what" value="check" />
	<input type="hidden" name="tab" value="license" />
	</form>';

if ( $renew ) {
	echo '
	<br />
	<form method="post">';
	wp_nonce_field('license_save', 'nfwnonce_license', 0);
	echo '
	<h3>'. esc_html__('License renewal', 'nfwplus') . '</h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">&nbsp;</th>
			<td><a href="https://nintechnet.com/ninjafirewall/wp-edition/" target="_blank">'. esc_html__('Click here to get a license!', 'nfwplus') . '</a></td>
		</tr>
		<tr>
			<th scope="row" class="row-med">'. esc_html__('Enter your new license and click on the save button', 'nfwplus') . '</th>
			<td>
				<input type="text" autocomplete="off" value="" maxlength="500" class="large-text" name="new_lic">
			<p><input class="button-secondary" type="submit" name="Save" value="'. esc_html__('Save New License', 'nfwplus') . '" /></p>
			</td>
		</tr>
	</table>
	<input type="hidden" name="nfw_what" value="renew" />
	<input type="hidden" name="tab" value="license" />
	</form>';
}

// =====================================================================
// EOF
