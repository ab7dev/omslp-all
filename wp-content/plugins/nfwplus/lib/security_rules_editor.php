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

$nfw_rules = nfw_get_option( 'nfw_rules' );
$is_update = 0;

if ( isset($_POST['sel_e_r']) ) {
	if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'editor_save') ) {
		wp_nonce_ays('editor_save');
	}
	if ( $_POST['sel_e_r'] < 1 ) {
		echo '<div class="error notice is-dismissible"><p>' . __('Error: you did not select a rule to disable.', 'nfwplus') .'</p></div>';
	} else if ( ( $_POST['sel_e_r'] == 2 ) || ( $_POST['sel_e_r'] > 499 ) && ( $_POST['sel_e_r'] < 600 ) ) {
		echo '<div class="error notice is-dismissible"><p>' . __('Error: to change this rule, use the "Firewall Policies" menu.', 'nfwplus') .'</p></div>';
	} else if (! isset( $nfw_rules[$_POST['sel_e_r']] ) ) {
		echo '<div class="error notice is-dismissible"><p>' . __('Error: this rule does not exist.', 'nfwplus') .'</p></div>';
	} elseif ($_POST['sel_e_r'] != 999) {
		$nfw_rules[$_POST['sel_e_r']]['ena'] = 0;
		$is_update = 1;
		echo '<div class="updated notice is-dismissible"><p>' . sprintf( __('Rule ID %s has been disabled.', 'nfwplus'), htmlentities($_POST['sel_e_r']) ) .'</p></div>';
	}
} else if ( isset($_POST['sel_d_r']) ) {
	if ( empty($_POST['nfwnonce']) || ! wp_verify_nonce($_POST['nfwnonce'], 'editor_save') ) {
		wp_nonce_ays('editor_save');
	}
	if ( $_POST['sel_d_r'] < 1 ) {
		echo '<div class="error notice is-dismissible"><p>' . __('Error: you did not select a rule to enable.', 'nfwplus') .'</p></div>';
	} else if ( ( $_POST['sel_d_r'] == 2 ) || ( $_POST['sel_d_r'] > 499 ) && ( $_POST['sel_d_r'] < 600 ) ) {
		echo '<div class="error notice is-dismissible"><p>' . __('Error: to change this rule, use the "Firewall Policies" menu.', 'nfwplus') .'</p></div>';
	} else if (! isset( $nfw_rules[$_POST['sel_d_r']] ) ) {
		echo '<div class="error notice is-dismissible"><p>' . __('Error: this rule does not exist.', 'nfwplus') .'</p></div>';
	} elseif ($_POST['sel_d_r'] != 999) {
		$nfw_rules[$_POST['sel_d_r']]['ena'] = 1;
		$is_update = 1;
		echo '<div class="updated notice is-dismissible"><p>' . sprintf( __('Rule ID %s has been enabled.', 'nfwplus'), htmlentities($_POST['sel_d_r']) ) .'</p></div>';
	}
}
if ( $is_update ) {
	nfw_update_option( 'nfw_rules', $nfw_rules);
}

$disabled_rules = $enabled_rules = array();

if ( empty( $nfw_rules ) ) {
	echo '<div class="error notice is-dismissible"><p>' . __('Error: no rules found.', 'nfwplus') .'</p></div></div>';
	return;
}

foreach ( $nfw_rules as $rule_key => $rule_value ) {
	if ( $rule_key == 999 ) { continue; }
	// Ingore firewall policies:
	if ( $rule_key == 2 || $rule_key > 499 && $rule_key < 600 ) {
		continue;
	}
	if (! empty( $nfw_rules[$rule_key]['ena'] ) ) {
		$enabled_rules[] =  $rule_key;
	} else {
		$disabled_rules[] = $rule_key;
	}
}

$nonce = wp_nonce_field('editor_save', 'nfwnonce', 0, 0);

echo '<h3>' . __('NinjaFirewall built-in security rules', 'nfwplus') .'</h3>
	<table class="form-table nfw-table">
		<tr>
			<th scope="row" class="row-med">' . __('Select the rule you want to disable or enable', 'nfwplus') .'</th>
			<td>
			<form method="post">'. $nonce . '
			<select name="sel_e_r" style="font-family:Consolas,Monaco,monospace;">
				<option value="0">' . __('Total rules enabled', 'nfwplus') .' : ' . count( $enabled_rules ) . '</option>';
sort( $enabled_rules );
$count = 0;
$desr = '';
foreach ( $enabled_rules as $key ) {
	if ( $key < 100 ) {
		$desc = ' ' . __('Remote/local file inclusion', 'nfwplus');
	} elseif ( $key < 150 ) {
		$desc = ' ' . __('Cross-site scripting', 'nfwplus');
	} elseif ( $key < 200 ) {
		$desc = ' ' . __('Code injection', 'nfwplus');
	} elseif (  $key > 249 && $key < 300 ) {
		$desc = ' ' . __('SQL injection', 'nfwplus');
	} elseif ( $key < 350 ) {
		$desc = ' ' . __('Various vulnerability', 'nfwplus');
	} elseif ( $key < 400 ) {
		$desc = ' ' . __('Backdoor/shell', 'nfwplus');
	} elseif ( $key > 999 && $key < 1300 ) {
		$desc = ' ' . __('Application specific', 'nfwplus');
	} elseif ( $key > 1349 ) {
		$desc = ' ' . __('WordPress vulnerability', 'nfwplus');
	}
	echo '<option value="' . htmlspecialchars($key) . '">' . __('Rule ID', 'nfwplus') .' : ' . htmlspecialchars($key) . $desc . '</option>';
	++$count;
}
echo '</select>&nbsp;&nbsp;<input class="button-secondary" type="submit" name="disable" value="' . __('Disable it', 'nfwplus') .'"' . disabled( $count, 0) .'>
			<input type="hidden" name="tab" value="editor" />
		</form>
		<br />
		<form method="post">'. $nonce . '
		<select name="sel_d_r" style="font-family:Consolas,Monaco,monospace;">
		<option value="0">' . __('Total rules disabled', 'nfwplus') .' : ' . count( $disabled_rules ) . '</option>';
sort( $disabled_rules );
$count = 0;
foreach ( $disabled_rules as $key ) {
	if ( $key < 100 ) {
		$desc = ' ' . __('Remote/local file inclusion', 'nfwplus');
	} elseif ( $key < 150 ) {
		$desc = ' ' . __('Cross-site scripting', 'nfwplus');
	} elseif ( $key < 200 ) {
		$desc = ' ' . __('Code injection', 'nfwplus');
	} elseif (  $key > 249 && $key < 300 ) {
		$desc = ' ' . __('SQL injection', 'nfwplus');
	} elseif ( $key < 350 ) {
		$desc = ' ' . __('Various vulnerability', 'nfwplus');
	} elseif ( $key < 400 ) {
		$desc = ' ' . __('Backdoor/shell', 'nfwplus');
	} elseif ( $key > 999 && $key < 1300 ) {
		$desc = ' ' . __('Application specific', 'nfwplus');
	} elseif ( $key > 1349 ) {
		$desc = ' ' . __('WordPress vulnerability', 'nfwplus');
	}
	echo '<option value="' . htmlspecialchars($key) . '">' . __('Rule ID', 'nfwplus') .' #' . htmlspecialchars($key) . $desc . '</option>';
	++$count;
}

echo '</select>&nbsp;&nbsp;<input class="button-secondary" type="submit" name="disable" value="' . __('Enable it', 'nfwplus') .'"' . disabled( $count, 0) .'>
				<input type="hidden" name="tab" value="editor" />
			</form>
			</td>
		</tr>
	</table>
';
// =====================================================================
// EOF
