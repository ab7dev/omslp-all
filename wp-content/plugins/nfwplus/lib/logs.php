<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa / 2
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) { die( 'Forbidden' ); }

// Tab and div display
if ( empty( $_REQUEST['tab'] ) ) { $_REQUEST['tab'] = 'firewalllog'; }

if ( $_REQUEST['tab'] == 'livelog' ) {
	$firewalllog_tab = ''; $firewalllog_div = ' style="display:none"';
	$livelog_tab = ' nav-tab-active'; $livelog_div = '';
	$centlog_tab = ''; $centlog_div = ' style="display:none"';
} elseif ( $_REQUEST['tab'] == 'centlog' ) {
	$_REQUEST['tab'] = 'centlog';
	$centlog_tab = ' nav-tab-active'; $centlog_div = '';
	$livelog_tab = ''; $livelog_div = ' style="display:none"';
	$firewalllog_tab = ''; $firewalllog_div = ' style="display:none"';
} else {
	$_REQUEST['tab'] = 'firewalllog';
	$firewalllog_tab = ' nav-tab-active'; $firewalllog_div = '';
	$livelog_tab = ''; $livelog_div = ' style="display:none"';
	$centlog_tab = ''; $centlog_div = ' style="display:none"';
}

?>
<div class="wrap">
	<h1><img style="vertical-align:top;width:33px;height:33px;" src="<?php echo plugins_url( '/nfwplus/images/ninjafirewall_32.png' ) ?>">&nbsp;<?php _e('Logs', 'nfwplus') ?></h1>
	<br />
	<h2 class="nav-tab-wrapper wp-clearfix" style="cursor:pointer">
		<a id="tab-firewalllog" class="nav-tab<?php echo $firewalllog_tab ?>" onClick="nfwjs_switch_tabs('firewalllog', 'firewalllog:livelog:centlog')"><?php _e( 'Firewall Log', 'nfwplus' ) ?></a>
		<a id="tab-livelog" class="nav-tab<?php echo $livelog_tab ?>" onClick="nfwjs_switch_tabs('livelog', 'firewalllog:livelog:centlog')"><?php _e( 'Live Log', 'nfwplus' ) ?></a>
		<a id="tab-centlog" class="nav-tab<?php echo $centlog_tab ?>" onClick="nfwjs_switch_tabs('centlog', 'firewalllog:livelog:centlog')"><?php _e( 'Centralized Logging', 'nfwplus' ) ?></a>
		<?php nfw_contextual_help(); ?>
	</h2>
	<br />

	<!-- Firewall Log -->
	<div id="firewalllog-options"<?php echo $firewalllog_div ?>>
		<?php include __DIR__ .'/logs_firewall_log.php'; ?>
	</div>

	<!-- Live Log -->
	<div id="livelog-options"<?php echo $livelog_div ?>>
		<?php include __DIR__ .'/logs_live_log.php'; ?>
	</div>

	<!-- Centralized Logging -->
	<div id="centlog-options"<?php echo $centlog_div ?>>
		<?php include __DIR__ .'/logs_centralized_logging.php'; ?>
	</div>
</div>
<?php

// =====================================================================
// EOF
