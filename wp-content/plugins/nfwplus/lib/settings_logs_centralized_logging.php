<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Block immediately if user is not allowed
nf_not_allowed( 'block', __LINE__ );

$nfw_options = nfw_get_option( 'nfw_options' );

if (! empty( $_POST['nfw_save_centlog']) ) {
	if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'cent_logs') ) {
		wp_nonce_ays('cent_logs');
	}
	nf_sub_centlog_save( $nfw_options );
	echo '<div class="updated notice is-dismissible"><p>' . __('Your changes have been saved.', 'nfwplus') . '</p></div>';
	$nfw_options = nfw_get_option( 'nfw_options' );
}

if ( empty($nfw_options['clogs_enable']) ) {
	$nfw_options['clogs_enable'] = 0;
} else {
	$nfw_options['clogs_enable'] = 1;
}

if ( empty( $nfw_options['clogs_ip'] ) || (! filter_var( $nfw_options['clogs_ip'], FILTER_VALIDATE_IP ) && $nfw_options['clogs_ip'] != '*' ) ) {
	$nfw_options['clogs_ip'] = $_SERVER['SERVER_ADDR'];
}

// Verify secret key syntax:
if (! empty( $nfw_options['clogs_seckey'] ) ) {
	$nfw_options['clogs_seckey'] = base64_decode( $nfw_options['clogs_seckey'] );
	if ( ! preg_match( '/^[\x20-\x7e]{30,100}$/', $nfw_options['clogs_seckey'] ) ) {
		$nfw_options['clogs_seckey'] = generate_clogs_seckey();
		$error_msg = __('Warning: Your previous secret key was either corrupted or missing. A new one, as well as a new public key, were created.', 'nfwplus');
	}
} else {
	$nfw_options['clogs_seckey'] = generate_clogs_seckey();
}

// List of URLs
$urls = '';
if (! empty( $nfw_options['clogs_urls'] ) ) {
	$tmp = unserialize( $nfw_options['clogs_urls'] );
	if ( $tmp ) {
		foreach ($tmp as $url) {
			$urls .= htmlspecialchars( $url ) . "\n";
		}
	}
}
if ( empty( $urls ) && $nfw_options['clogs_enable'] == 1 ) {
	$error_msg = __('Please enter the remote websites URL.', 'nfwplus');
}

if (! empty( $error_msg ) ) {
	echo '<div class="error notice is-dismissible"><p>' . $error_msg . '</p></div>';
}

?>
<form method="post" name="ctform" onSubmit="return nfwjs_centlog_check()">
	<?php wp_nonce_field('cent_logs', 'nfwnonce', 0); ?>

	<table class="form-table nfw-table">
		<tr style="background-color:#F9F9F9;border: solid 1px #DFDFDF;">
			<th scope="row" class="row-med"><?php _e('Enable Centralized Logging', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'green', 'nfw_options[clogs_enable]', __('Enabled', 'nfwplus'), __('Disabled', 'nfwplus'), 'large', $nfw_options['clogs_enable'], false, 'onclick="nfwjs_up_down(\'clogs_table\');"' ) ?>
			</td>
		</tr>
	</table>

	<br />

	<div id="clogs_table"<?php echo $nfw_options['clogs_enable'] == 1 ? '' : ' style="display:none"' ?>>
		<table class="form-table nfw-table">
			<tr>
				<th scope="row" class="row-med"><?php _e('Secret key', 'nfwplus') ?></th>
				<td>
					<input class="large-text" type="text" id="clogs-seckey" maxlength="100" name="nfw_options[clogs_seckey]" value="<?php echo htmlentities( $nfw_options['clogs_seckey'] ) ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" oninput="nfwjs_clear_pubkey();" />
					<p class="description"><?php _e('From 30 to 100 ASCII printable characters.', 'nfwplus') ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row" class="row-med"><?php _e('This server\'s IP address', 'nfwplus'); echo ' ('. htmlspecialchars( $_SERVER['SERVER_ADDR'] ) . ')' ?></th>
				<td>
					<input type="text" id="clogs-ip" name="nfw_options[clogs_ip]" value="<?php echo htmlspecialchars( $nfw_options['clogs_ip'] ) ?>" placeholder="<?php _e('e.g.,', 'nfwplus') ?> 1.2.3.4" oninput="nfwjs_clear_pubkey();" />
					<p class="description"><?php _e('Only this IP address (IPv4 or IPv6) will be allowed to connect to the remote websites. If you don\'t want to restrict the access by IP, enter the <code>*</code> character instead.', 'nfwplus') ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row" class="row-med"><?php _e('Public key', 'nfwplus') ?></th>
				<td id="pubkey">
					<input type="text" class="large-text" value="<?php echo sha1( $nfw_options['clogs_seckey'] )  .':'. htmlspecialchars( $nfw_options['clogs_ip'] ) ?>" readonly />
					<p class="description"><?php printf( __('Add this key to the remote websites. <a href="%s">Consult our blog</a> for more info.', 'nfwplus'), 'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/' ) ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row" class="row-med"><?php _e('Remote websites URL', 'nfwplus') ?></th>
				<td>
					<textarea class="large-text code" id="clogs-urls" name="nfw_options[clogs_urls]" rows="10" placeholder="http://example.org/index.php" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?php echo $urls ?></textarea>
					<p class="description"><?php _e('Enter one URL per line, including the protocol (<code>http://</code> or <code>https://</code>). Only ASCII URLs are accepted.', 'nfwplus') ?></p>
				</td>
			</tr>

		</table>
	</div>

	<br />

	<input type="hidden" name="nfw_save_centlog" value="save_options" />
	<input class="button-primary" name="clsaveopt" value="<?php _e('Save Options', 'nfwplus') ?>" type="submit" />
	<input type="hidden" name="tab" value="centlog" />
</form>
<?php

// =====================================================================

function nf_sub_centlog_save( $nfw_options ) {

	if ( empty( $_POST['nfw_options']['clogs_enable'] ) ) {
		$nfw_options['clogs_enable'] = 0;

	} else {
		$nfw_options['clogs_enable'] = 1;
		if ( empty( $_POST['nfw_options']['clogs_seckey'] ) ||
			! preg_match( '/^[\x20-\x7e]{30,100}$/', $_POST['nfw_options']['clogs_seckey'] ) ) {
			$nfw_options['clogs_seckey'] = '';

		} else {
			// Prevent WP from adding slashes
			$nfw_options['clogs_seckey'] = base64_encode( stripslashes( $_POST['nfw_options']['clogs_seckey'] ) );
		}

		if ( empty( $_POST['nfw_options']['clogs_ip'] ) ||
			(! filter_var( $_POST['nfw_options']['clogs_ip'], FILTER_VALIDATE_IP ) &&
			$_POST['nfw_options']['clogs_ip'] != '*' ) ) {
			$nfw_options['clogs_ip'] = $_SERVER['SERVER_ADDR'];
		} else {
			$nfw_options['clogs_ip'] = $_POST['nfw_options']['clogs_ip'];
		}

		if (! empty( $_POST['nfw_options']['clogs_urls'] ) ) {
			$res = explode( "\n", $_POST['nfw_options']['clogs_urls'] );
			$urls = array_values( array_filter( array_map( 'trim', $res ) ) );
			$res = array();
			foreach ( $urls as $url ) {
				if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
					$res[] = $url;
				}
			}
			if ( $res ) {
				sort( $res );
				$nfw_options['clogs_urls'] = serialize( $res );
			}
		}
	}

	nfw_update_option( 'nfw_options', $nfw_options);

}

// =====================================================================

function generate_clogs_seckey() {

	$key = '';
	for ( $i = 0; $i < 40; ++$i ) {
		$key .= chr( mt_rand( 33, 126 ) );
	}
	return $key;

}

// =====================================================================
// EOF
