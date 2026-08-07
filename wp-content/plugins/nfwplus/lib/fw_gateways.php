<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+
*/

if (! isset( $nfw_['nfw_options']['enabled']) && ! defined( 'NFW_STATUS' ) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}
// =====================================================================

$gateways_ip = [
	'paypal_ipn' => [
		'64.4.240.0/21',
		'64.4.248.0/22',
		'66.211.168.0/22',
		'91.243.72.0/23',
		'173.0.80.0/20',
		'185.177.52.0/22',
		'192.160.215.0/24',
		'198.54.216.0/23'
	],
	'stripe_webhook' => [
		'3.18.12.63',
		'3.130.192.231',
		'13.235.14.237',
		'13.235.122.149',
		'18.211.135.69',
		'35.154.171.200',
		'52.15.183.38',
		'54.88.130.119',
		'54.88.130.237',
		'54.187.174.169',
		'54.187.205.235',
		'54.187.216.72',
		'35.157.207.129',
		'3.69.109.8',
		'3.120.168.93'
	],
	'jetpack_ipv4' => [
		'122.248.245.244/32',
		'54.217.201.243/32',
		'54.232.116.4/32',
		'192.0.64.0/18',
		'195.234.108.0/22'
	],
	'square' => [
		'54.245.1.154',
		'34.202.99.168',
		// Sandbox
		'54.212.177.79',
		'107.20.218.8'
	],
	'airwallex' => [
		'34.11.6.7',
		'34.21.104.184',
		'34.21.185.174',
		'34.85.218.163',
		'34.86.42.60',
		'34.87.64.173',
		'34.91.47.254',
		'34.91.75.229',
		'34.92.128.176',
		'34.124.183.136',
		'34.126.131.52',
		'34.142.140.136',
		'34.143.140.106',
		'34.145.133.253',
		'34.158.57.211',
		'34.177.101.131',
		'34.182.195.122',
		'35.185.179.53',
		'35.186.166.138',
		'35.186.175.150',
		'35.220.213.251',
		'35.230.185.215',
		'35.236.197.105',
		'35.240.218.67',
		'136.107.34.62',
		// Sandbox
		'34.87.139.23',
		'34.92.48.104',
		'34.92.144.250',
		'34.92.15.70',
		'34.142.189.194',
		'35.187.239.216',
		'35.197.128.86',
		'35.240.211.132',
		'136.110.35.143'
	]
];
$gateways_info = [
	'paypal_ipn' => [
		'name' => 'PayPal IPN (notify.paypal.com)',
		'url' => 'https://www.paypal.com/au/smarthelp/article/what-are-the-ip-addresses-for-live-paypal-servers-ts1056'
	],
	'stripe_webhook' => [
		'name' => 'Stripe (webhook)',
		'url' => 'https://stripe.com/docs/ips'
	],
	'jetpack_ipv4' => [
		'name' => 'Automattic (JetPack, VaultPress etc)',
		'url' => 'https://jetpack.com/support/how-to-add-jetpack-ips-allowlist/',
		'url' => 'https://jetpack.com/ips-v4.txt',
		'url' => 'https://help.vaultpress.com/connection-issues/'
	],
	'square' => [
		'name' => 'Square (webhook)',
		'url' => 'https://developer.squareup.com/docs/webhooks/overview#static-ip-address'
	],
	'airwallex' => [
		'name' => 'Airwallex (webhook)',
		'url' => 'https://www.airwallex.com/docs/developer-tools__listen-for-webhook-events'
	]
];

// =====================================================================
// EOF
