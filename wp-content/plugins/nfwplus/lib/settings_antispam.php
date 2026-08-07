<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / s1:h0 / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

$nfw_options = nfw_get_option( 'nfw_options' );

if ( empty( NinjaFirewall_session::read('nfw_goodguy') ) &&
	! empty( $nfw_options['enabled'] ) &&
	! empty( $nfw_options['as_enable'] ) ) {

	// Start a session
	NinjaFirewall_session::start();

	// Comment
	if (! empty($nfw_options['as_comment']) ) {
		add_filter('comment_form_logged_in_after', 'nfw_as_precomment', 1);
		add_filter('comment_form_after_fields', 'nfw_as_precomment', 1);
		add_filter('preprocess_comment', 'nfw_as_postcomment', 1);
	}
	// Registration
	if (! empty($nfw_options['as_register']) ) {
		if ( is_multisite() ) {
			add_action('signup_extra_fields', 'nfw_as_precomment', 1);
			add_action('wpmu_validate_user_signup', 'nfw_as_postcomment', 1);
		} else {
			add_action('register_form','nfw_as_precomment', 1);
			add_filter('registration_errors', 'nfw_as_postcomment', 1);
		}
	}
}

// =====================================================================

function nf_sub_antispam() {

	// Block immediately if user is not allowed :
	nf_not_allowed( 'block', __LINE__ );

	$nfw_options = nfw_get_option( 'nfw_options' );

	echo '
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="'. plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) .'">&nbsp;' . __('Antispam', 'nfwplus') . '</h1>';

	// Saved ?
	if ( isset( $_POST['nfw_options']) ) {
		if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'antispam_save') ) {
			wp_nonce_ays('antispam_save');
		}
		nf_sub_antispam_save();
		$nfw_options = nfw_get_option( 'nfw_options' );
		echo '<div class="updated notice is-dismissible"><p>'. __('Your changes have been saved. If you are using a caching plugin, do not forget to clear its cache.', 'nfwplus') .'</p></div>';

	} else {

		if ( empty($nfw_options['as_enable']) ) {
			$nfw_options['as_enable'] = 0;
		} else {
			$nfw_options['as_enable'] = 1;
		}

		if (! preg_match('/^[123]$/', @$nfw_options['as_level']) ) {
			$nfw_options['as_level'] = 1;
		}

		if (! empty($nfw_options['as_comment']) ) {
			$nfw_options['as_comment'] = 1;
		}
		if (! empty($nfw_options['as_register']) ) {
			$nfw_options['as_register'] = 1;
		}
		if ( empty($nfw_options['as_comment']) && empty($nfw_options['as_register']) ) {
			$nfw_options['as_comment'] = 1;
		}
	}
	nfw_contextual_help();
	?>

<form method="post" name="as_form" onSubmit="return check_asfields();">
	<?php wp_nonce_field('antispam_save', 'nfwnonce', 0); ?>
	<table class="form-table nfw-table">
		<tr style="background-color:#F9F9F9;border: solid 1px #DFDFDF;">
			<th scope="row" class="row-med"><?php _e('Enable antispam protection', 'nfwplus') ?></th>
			<td>
				<?php nfw_toggle_switch( 'green', 'nfw_options[as_enable]', __('Enabled', 'nfwplus'), __('Disabled', 'nfwplus'), 'large', $nfw_options['as_enable'], false, 'onclick="nfwjs_up_down(\'as_table\');"' ) ?>
			</td>
		</tr>
	</table>

	<div id="as_table"<?php echo $nfw_options['as_enable'] == 1 ? '' : ' style="display:none"' ?>>
		<br />
		<table class="form-table nfw-table">
			<tr>
				<th scope="row" class="row-med"><?php _e('Protection level', 'nfwplus') ?></th>
				<td>
					<p><label><input type="radio" name="nfw_options[as_level]" value="1"<?php checked($nfw_options['as_level'], 1) ?>>&nbsp;<?php _e('Low (default)', 'nfwplus') ?></label></p>
					<p><label><input type="radio" name="nfw_options[as_level]" value="2"<?php checked($nfw_options['as_level'], 2) ?>>&nbsp;<?php _e('Medium', 'nfwplus') ?></label></p>
					<p><label><input type="radio" name="nfw_options[as_level]" value="3"<?php checked($nfw_options['as_level'], 3) ?>>&nbsp;<?php _e('High', 'nfwplus') ?></label>
					<p class="description"><?php _e('If you are using a caching plugin, consult the contextual help before enabling the antispam.', 'nfwplus') ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row" class="row-med"><?php _e('Apply protection to', 'nfwplus') ?></th>
				<td>
					<p><label><input type="checkbox" name="nfw_options[as_comment]" value="1"<?php checked($nfw_options['as_comment'], 1) ?>>&nbsp;<?php _e('Comment forms (defaut)', 'nfwplus') ?></label></p>
					<p><label><input type="checkbox" name="nfw_options[as_register]" value="1"<?php checked($nfw_options['as_register'], 1) ?>>&nbsp;<?php _e('User Registration form', 'nfwplus') ?></label></p>
				</td>
			</tr>
		</table>
	</div>
	<br />
	<br />
	<input id="save_login" class="button-primary" type="submit" name="Save" value="<?php _e('Save Antispam options', 'nfwplus') ?>" />
</form>
</div>

<?php
}

// =====================================================================

function nf_sub_antispam_save() {

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Disable or enable the antispam protection ?
	if ( empty( $_POST['nfw_options']['as_enable']) ) {
		$nfw_options['as_enable'] = 0;
	} else {
		$nfw_options['as_enable'] = 1;
	}

	if (! preg_match('/^[123]$/', @$_POST['nfw_options']['as_level']) ) {
		$nfw_options['as_level'] = 1;
	} else {
		$nfw_options['as_level'] = $_POST['nfw_options']['as_level'];
	}

	if (! empty($_POST['nfw_options']['as_comment']) ) {
		$nfw_options['as_comment'] = 1;
	} else {
		$nfw_options['as_comment'] = 0;
	}
	if (! empty($_POST['nfw_options']['as_register']) ) {
		$nfw_options['as_register'] = 1;
	} else {
		$nfw_options['as_register'] = 0;
	}
	if ( empty($nfw_options['as_comment']) && empty($nfw_options['as_register']) ) {
		$nfw_options['as_comment'] = 1;
	}

	$nfw_options['as_salt']    = wp_generate_password();
	$nfw_options['as_field']   = wp_generate_password( mt_rand(5, 12), FALSE);
	$nfw_options['as_field_2'] = wp_generate_password( mt_rand(5, 12), FALSE);

	nfw_update_option( 'nfw_options', $nfw_options );

}
// =====================================================================			// s1:h1

function nfw_as_precomment() {

	// Comment/Signup form pre-processing :

	$nfw_options = nfw_get_option( 'nfw_options' );

	// Level 1 :
	$rand_val = mt_rand(1000, 9999);
	$tmp_input = '<input type=hidden name="' . $nfw_options['as_field'] . '" value="' .
		$rand_val . ':' . sha1( $rand_val . $nfw_options['as_salt'] ) . '">';
	$obfus = '';
	for ( $i = 0; $i < strlen( $tmp_input ); ++$i ) {
		$obfus .= ord( $tmp_input[$i] ). ',';
	}
	echo "\n\t\t" . '<script>document.write(String.fromCharCode('. rtrim($obfus, ',') .'));</script>' .
		'<noscript><strong><font color=red>'. esc_js( __('Please enable JavaScript', 'nfwplus') ) .'</font></strong></noscript>' . "\n\t\t" .
		'<div style="display:none;"><input type="text" name="' . $nfw_options['as_field_2'] . '" value="" /></div>';

	// Level 2 & 3
	if ( $nfw_options['as_level'] > 1 ) {
		NinjaFirewall_session::write( ['nfw_as' => time() ] );
	}
}

// =====================================================================			// s1:h0

function nfw_as_postcomment( $comment ) {

	// Comment/Signup form post-processing :

	$nfw_options = nfw_get_option( 'nfw_options' );

	if ( is_array( $comment ) && isset( $comment['comment_author_email'] ) ) {
		$what = "Comment";
	} else {
		$what = "Registration";
	}

	// Level 1 :
	if (empty( $_POST[$nfw_options['as_field']] ) ) {
		nfw_as_block(1, $what);
	}
	list($rand_val, $rand_hash) = explode( ':', $_POST[$nfw_options['as_field']] . ':' );
	if ( $rand_hash != sha1( $rand_val . $nfw_options['as_salt'] ) ) {
		nfw_as_block(2, $what);
	}
	if ( @$_POST[$nfw_options['as_field_2']] ) {
		nfw_as_block(3, $what);
	}

	// Level 2 :
	if ( $nfw_options['as_level'] > 1 ) {
		if ( $_SERVER['REQUEST_METHOD'] != 'OPTIONS' && empty($_SERVER['HTTP_ACCEPT']) ) {
			nfw_as_block(4, $what);
		}
		if ( empty( NinjaFirewall_session::read('nfw_as') ) ) {
			nfw_as_block(5, $what);
		}
	}

	// Level 3 :
	if ( $nfw_options['as_level'] == 3 ) {
		if ( time() - NinjaFirewall_session::read('nfw_as') < 10 ) {
			nfw_as_block(6, $what);
		}
		if ( empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
			nfw_as_block(7, $what);
		}
	}

	NinjaFirewall_session::delete('nfw_as');

	return $comment;
}

// =====================================================================

function nfw_as_block( $id, $what ) {

	global $nfw_options;

	NinjaFirewall_log::write(
		"$what spam",
		"#$id",
		NFWLOG_MEDIUM, 0, $nfw_options, NFW_LOG_DIR .'/nfwlog'
	);

	// Don't block if we are running in Debugging Mode
	if ( empty( $nfw_options['debug'] ) ) {
		NinjaFirewall_session::destroy();
		wp_die( esc_html__('Sorry, this looks like spam to me.', 'nfwplus') );
	}

}
// =====================================================================
// EOF
