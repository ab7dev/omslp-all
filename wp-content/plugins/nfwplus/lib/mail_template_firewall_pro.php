<?php
/*
 +=====================================================================+
 |    _   _ _        _       _____ _                        _ _        |
 |   | \ | (_)_ __  (_) __ _|  ___(_)_ __ _____      ____ _| | |       |
 |   |  \| | | '_ \ | |/ _` | |_  | | '__/ _ \ \ /\ / / _` | | |       |
 |   | |\  | | | | || | (_| |  _| | | | |  __/\ V  V / (_| | | |       |
 |   |_| \_|_|_| |_|/ |\__,_|_|   |_|_|  \___| \_/\_/ \__,_|_|_|       |
 |                |__/                                                 |
 |  (c) NinTechNet Limited ~ https://nintechnet.com/                   |
 +=====================================================================+
*/

if (! class_exists('NinjaFirewall_mail') ) {
	return;
}

/***********************************************************************
 * IMPORTANT: Those strings are loaded before WordPress loads.
 * As such, internationalization IS NOT possible.
 */

/***********************************************************************
 * Subject line tag for all email notification.
 * Ex: "Subject: [NinjaFirewall] My email subject"
 */
$template['subject_line_tag'] = '[NinjaFirewall]';

/**
 * Email signature.
 */
$template['signature'] = 'NinjaFirewall (WP+ Edition) - https://nintechnet.com/';


/***********************************************************************
 * Webfilter.
 * NinjaFirewall > Monitoring > Webfilter.
 */
$template['webfilter']['subject'] = 'Alert: Web Filter detection';
$template['webfilter']['content'] =
'The following event was detected by NinjaFirewall Web Filter:

SERVER_NAME: %1$s
SCRIPT_FILENAME: %2$s
REQUEST_URI: %3$s
USER IP: %4$s
Date: %5$s
Keyword(s) detected in HTML page:
%6$s';


/***********************************************************************
 * File Guard.
 * NinjaFirewall > Monitoring > File Guard.
 */
$template['fileguard']['subject'] = 'Alert: File Guard detection';
$template['fileguard']['content'] =
'Someone accessed a script that was modified or created less than %1$s hour(s) ago:

SERVER_NAME: %2$s
USER IP: %3$s
SCRIPT_FILENAME: %4$s
REQUEST_URI: %5$s
Last changed on: %6$s';


// -------- DO NOT EDIT BELOW ------------------------------------------

return $template;
