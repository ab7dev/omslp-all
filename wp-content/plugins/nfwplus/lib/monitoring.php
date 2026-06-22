<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// File Check scheduled scan?
if (defined('NFSCANDO') ) {
	include __DIR__ .'/monitoring_file_check.php';
	return;
}

// Tab and div display
if ( empty( $_REQUEST['tab'] ) ) { $_REQUEST['tab'] = 'filecheck'; }

if ( $_REQUEST['tab'] == 'filecheck' ) {
	$fileguard_tab = ''; $fileguard_div = ' style="display:none"';
	$filecheck_tab = ' nav-tab-active'; $filecheck_div = '';
	$webfilter_tab = ''; $webfilter_div = ' style="display:none"';
} elseif ( $_REQUEST['tab'] == 'webfilter' ) {
	$_REQUEST['tab'] = 'webfilter';
	$webfilter_tab = ' nav-tab-active'; $webfilter_div = '';
	$filecheck_tab = ''; $filecheck_div = ' style="display:none"';
	$fileguard_tab = ''; $fileguard_div = ' style="display:none"';
} else {
	$_REQUEST['tab'] = 'fileguard';
	$fileguard_tab = ' nav-tab-active'; $fileguard_div = '';
	$filecheck_tab = ''; $filecheck_div = ' style="display:none"';
	$webfilter_tab = ''; $webfilter_div = ' style="display:none"';
}

?>
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="<?php echo plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) ?>">&nbsp;<?php _e('Monitoring', 'nfwplus') ?></h1>
	<br />
	<h2 class="nav-tab-wrapper wp-clearfix" style="cursor:pointer">
		<a id="tab-filecheck" class="nav-tab<?php echo $filecheck_tab ?>" onClick="nfwjs_switch_tabs('filecheck', 'fileguard:filecheck:webfilter')"><?php _e( 'File Check', 'nfwplus' ) ?></a>
		<a id="tab-fileguard" class="nav-tab<?php echo $fileguard_tab ?>" onClick="nfwjs_switch_tabs('fileguard', 'fileguard:filecheck:webfilter')"><?php _e( 'File Guard', 'nfwplus' ) ?></a>
		<a id="tab-webfilter" class="nav-tab<?php echo $webfilter_tab ?>" onClick="nfwjs_switch_tabs('webfilter', 'fileguard:filecheck:webfilter')"><?php _e( 'Web Filter', 'nfwplus' ) ?></a>
		<?php nfw_contextual_help(); ?>
	</h2>
	<br />

	<!-- File Guard -->
	<div id="fileguard-options"<?php echo $fileguard_div ?>>
		<?php include __DIR__ .'/monitoring_file_guard.php'; ?>
	</div>

	<!-- File Check -->
	<div id="filecheck-options"<?php echo $filecheck_div ?>>
		<?php include __DIR__ .'/monitoring_file_check.php'; ?>
	</div>

	<!-- Web Filter -->
	<div id="webfilter-options"<?php echo $webfilter_div ?>>
		<?php include __DIR__ .'/monitoring_web_filter.php'; ?>
	</div>

</div>
<?php

// =====================================================================
// EOF
