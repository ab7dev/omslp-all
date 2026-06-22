<?php
/*
 +=====================================================================+
 | NinjaFirewall (WP+ Edition)                                         |
 |                                                                     |
 | (c) NinTechNet - https://nintechnet.com/                            |
 +=====================================================================+ i18n+ / sa
*/

if (! defined( 'NFW_ENGINE_VERSION' ) ) {
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	exit;
}

// contextual help - choose Help on the top right
// of the admin panel to preview this.

/* ================================================================== */ // i18n+

function help_nfsubmain() {

	// Overview menu help :
	get_current_screen()->add_help_tab( array(
		'id'        => 'main01',
		'title'     => __('Firewall Dashboard', 'nfwplus'),
		'content'   => '<br />' . __('This is NinjaFirewall Dashboard page; it shows information about the firewall status. We recommend you keep an eye on it because, in case of problems, all possible errors and warnings will be displayed here.', 'nfwplus') . '<br />&nbsp;'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'help01',
		'title'     => __('Monthly Statistics', 'nfwplus'),
		'content'   => '<br />'.
			__('Statistics are taken from the current log. It is rotated on the first day of each month.', 'nfwplus') .
			'<br />'. sprintf( __('You can view the log by clicking on the <a href="%s">Firewall Log</a> menu.', 'nfwplus'), '?page=nfsublog') .

			'<p>'. __('Benchmarks show the time NinjaFirewall took, in seconds, to process each request it has blocked.', 'nfwplus') .'</p>'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'log01',
		'title'     => __('License', 'nfwplus'),
		'content'   => '<br />'.
		__('Your license is valid until the indicated expiration date. If you don\'t renew it after this date, NinjaFirewall will keep working and protecting your website as usual, but updates/upgrades will stop.', 'nfwplus').
		'<br />'.
		sprintf( __('You can renew your license from <a href="%s">NinTechNet.com</a> website.', 'nfwplus'), 'https://secure.nintechnet.com/login/?nf')
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'about',
		'title'     => __('About...', 'nfwplus'),
		'content'   => '<br />'.

			__('Everything you ever wanted to know about NinjaFirewall.', 'nfwplus')
	) );

}

/* ================================================================== */ // i18n+

function help_nfsubopt() {

	// Firewall options menu help :

	get_current_screen()->add_help_tab( array(
		'id'        => 'opt01',
		'title'     =>  __('Firewall protection', 'nfwplus'),
		'content'   => '<br />' .
			sprintf( __('This option allows you to disable NinjaFirewall. It has basically the same effect as deactivating it from the <a href="%s">Plugins</a> menu page.', 'nfwplus'), admin_url() . 'plugins.php') .
			'<br />'.
			__('Your site will remain unprotected until you enable it again.', 'nfwplus')
	) );
	get_current_screen()->add_help_tab( array(
		'id'        => 'opt02',
		'title'     => __('Use shared memory', 'nfwplus'),
		'content'   => '<br />' .
		__('This feature allows NinjaFirewall to use Unix shared memory segments in order to speed up all operations. The firewall will no longer need to connect to the database and, instead, will retrieve its options and configuration directly from memory (RAM). On a very busy server (e.g., multisite network etc), this feature can dramatically increase the processing speed from 25% to 30%, prevent blocking I/O and slow queries.', 'nfwplus') . '
		<p><span class="dashicons dashicons-warning nfw-warning"></span>'.
		__('This option requires that your PHP version was compiled with the <code>--enable-shmop</code> parameter, otherwise, if it is not compatible with your server/hosting environment, it will be disabled.', 'nfwplus') . '</p>
		<p><span class="dashicons dashicons-warning nfw-warning"></span>' .
		sprintf( __('If you are using <a href="%s">GB2312</a> character set (A.K.A <code>GBK</code> - simplified Chinese characters) for your database, we recommend to disable that option otherwise NinjaFirewall will not have access to the database and it may not be able to properly sanitise multi-byte characters used by that charset.', 'nfwplus'), 'http://en.wikipedia.org/wiki/GBK') . '</p>'
	) );
	get_current_screen()->add_help_tab( array(
		'id'        => 'optlanguage',
		'title'     => __('Language', 'nfwplus'),
		'content'   => '<br />' .
		__('When this option is enabled, NinjaFirewall will download, if available, the corresponding language files from the WordPress repo. Then, every hour, it will check if there\'s a new version and will download it.', 'nfwplus') .'<br />' .
		__('Note that this does not apply to <code>en_US</code> and <code>fr_FR</code> locales because they are already included with NinjaFirewall.', 'nfwplus')
	) );
	get_current_screen()->add_help_tab( array(
		'id'        => 'opt03',
		'title'     => __('Debugging mode', 'nfwplus'),
		'content'   => '<br />' .
			sprintf( __('In Debugging mode, NinjaFirewall will not block or sanitise suspicious requests but will only log them. The <a href="%s">Firewall Log</a> will display <code>DEBUG_ON</code> in the LEVEL column.', 'nfwplus'), '?page=nfsublog') .
			'<p>' . __('We recommend to run it in Debugging Mode for at least 24 hours after installing it on a new site and then to keep an eye on the firewall log during that time. If you notice a false positive in the log, you can simply use NinjaFirewall\'s Rules Editor to disable the security rule that was wrongly triggered.', 'nfwplus') . '</p>'
	) );
	get_current_screen()->add_help_tab( array(
		'id'        => 'optipanon',
		'title'     => __('IP anonymization', 'nfwplus'),
		'content'   => '<p>'. __('This option will anonymize IP addresses in the firewall log by removing their last 3 characters.', 'nfwplus') .' '. __('It does not apply to private IP addresses and the Login Protection feature.', 'nfwplus') .'</p>'.
		'<p>'. __('Note that it will affect only IP addresses written to the firewall log after enabling this option.', 'nfwplus') .' '.	__('Also, if you are redirecting events to the syslog server (NinjaFirewall <font color="#21759B">WP+</font> Edition), IP addresses will be anonymized too.', 'nfwplus') .'</p>'
	) );
	get_current_screen()->add_help_tab( array(
		'id'        => 'opt04',
		'title'     =>  __('Blocked user message', 'nfwplus'),
		'content'   => '<br />' .
			__('Lets you customize the HTTP error code returned by NinjaFirewall when blocking a dangerous request and the message to display to the user.' , 'nfwplus') . ' ' .
			__('You can use any HTML tags and 3 built-in variables:' , 'nfwplus') .
			'<ul><li><code>%%REM_ADDRESS%%</code> : '. __('the blocked user IP.' , 'nfwplus') . '</li>
			<li><code>%%NUM_INCIDENT%%</code> : '. __('the unique incident number as it will appear in the firewall log "INCIDENT" column.' , 'nfwplus') . '</li>
			<li><code>%%NINJA_LOGO%%</code> : '. __('NinjaFirewall logo.' , 'nfwplus') . '</li></ul>'
	) );
	list ( $major_current ) = explode( '.', NFW_ENGINE_VERSION );
	get_current_screen()->add_help_tab( array(
		'id'        => 'opt05',
		'title'     =>  __('Export/import configuration', 'nfwplus'),
		'content'   => '<br />' .
			sprintf( __('This options lets you export you current configuration or import it from another NinjaFirewall (WP+ Edition) installation. The imported file must match the major version of your current version (%s) otherwise it will be rejected. Note that importing will override all firewall rules, options and configuration, except your current license.', 'nfwplus'), (int) $major_current .'.x' ) .
			'<p><span class="dashicons dashicons-warning nfw-warning"></span>' .
			__('"File Check" configuration will not be exported/imported.', 'nfwplus') . '</p>'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'opt06',
		'title'     =>  __('Configuration backup', 'nfwplus'),
		'content'   => '<br />' .
		__('NinjaFirewall will automatically backup its configuration (options, policies and rules) everyday for the last 5 days. If you want to restore its configuration to an earlier date, select the corresponding file in the list.', 'nfwplus')

	) );
}
/* ================================================================== */ // i18n+

function help_nfsubpolicies() {

	// Firewall policies menu help :

	if (! function_exists( 'get_home_path' ) ) {
		include_once ABSPATH .'wp-admin/includes/file.php';
 	}
 	$NFW_ABSPATH = get_home_path();

	// Show this text only if we are running in "Full WAF" mode:
	if ( defined('NFW_WPWAF') ) {
		$res= '';
	} else {
		$res = sprintf( __('Keep in mind, however, that the Firewall Policies apply to any PHP scripts located inside the %s directory and its sub-directories, and not only to your WordPress index page.', 'nfwplus'), '<code>' . $NFW_ABSPATH . '</code>');
	}

	get_current_screen()->add_help_tab( array(
		'id'        => 'policies01',
		'title'     => __('Policies overview', 'nfwplus'),
		'content'   => '<br />' .
			sprintf( __('Because NinjaFirewall sits in front of WordPress, it can hook, scan and sanitise all PHP requests, HTTP variables, headers and IPs before they reach your blog: <code><a href="%s">$_GET</a></code>, <code><a href="%s">$_POST</a></code>, <code><a href="%s">$_COOKIE</a></code>, <code><a href="%s">$_REQUEST</a></code>, <code><a href="%s">$_FILES</a></code>, <code><a href="%s">$_SERVER</a></code> in HTTP and/or HTTPS mode.', 'nfwplus'), 'http://www.php.net/manual/en/reserved.variables.get.php', 'http://www.php.net/manual/en/reserved.variables.post.php', 'http://www.php.net/manual/en/reserved.variables.cookies.php', 'http://www.php.net/manual/en/reserved.variables.request.php', 'http://www.php.net/manual/en/reserved.variables.files.php', 'http://php.net/manual/en/reserved.variables.server.php') .
			'<br />' .
			__('Use the options below to enable, disable or to tweak these rules according to your needs.', 'nfwplus') .
			'<br />' .
			$res .
			'<br />'
	) );
	get_current_screen()->add_help_tab( array(
		'id'        => 'policies02',
		'title'     =>  __('Scan and Sanitise', 'nfwplus'),
		'content'   => '<br />'.
		__('You can choose to scan and reject dangerous content but also to sanitise requests and variables. Those two actions are different and can be combined together for better security.', 'nfwplus') .
		'<ul><li>'. __('Scan: If anything suspicious is detected, NinjaFirewall will block the request and return an HTTP error code and message (defined in the "Firewall Options" page). The user request will fail and the connection will be closed immediately.', 'nfwplus') .'</li>
		<li>'. sprintf( __('Sanitise: This option will not block but sanitise the user request by escaping characters that can be used to exploit vulnerabilities (%s) and replacing <code>&lt;</code> and <code>&gt;</code> with their corresponding HTML entities (<code>&amp;lt;</code>, <code>&amp;gt;</code>). If it is a variable, i.e. <code>?name=value</code>, both its name and value will be sanitised.', 'nfwplus'), '<code>\'</code>, <code>"</code>, <code>\\</code>, <code>\n</code>, <code>\r</code>, <code>`</code>, <code>\x1a</code>, <code>\x00</code>, <code>*</code>, <code>?</code>') .'
		<br />' .
		__('This action will be performed when the filtering process is over, right before NinjaFirewall forwards the request to your PHP script.', 'nfwplus') . '
		<br />
		<br />
		<span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'. __('If you enabled <code>POST</code> requests sanitising, articles and messages posted by your visitors could be corrupted with excessive backslashes or substitution characters.', 'nfwplus'). '</li></ul>'
	) );

	get_current_screen()->add_help_tab( array(
		'id'			=> 'policies03',
		'title'		=> __('Basic Policies', 'nfwplus'),
		'content'	=> '
		<div style="height:400px;">

		<h3>HTTP / HTTPS</h3>'.
		__('Whether to filter HTTP and/or HTTPS traffic', 'nfwplus').


		'<h3>' . __('Uploads', 'nfwplus'). '</h3>

		<p><strong>' . __('File Uploads', 'nfwplus'). '</strong><br />' . __('You can allow/disallow uploads, or allow uploads but block scripts (PHP, CGI, Ruby, Python, bash/shell, JavaScript), C/C++ source code, binaries (MZ/PE/NE and ELF formats), system files (.htaccess, .htpasswd and PHP INI) and SVG files containing Javascript/XML events.', 'nfwplus'). '</p>

		<p><strong>' . __('Sanitise filenames', 'nfwplus'). '</strong><br />' . __('Any character that is not a letter <code>a-zA-Z</code>, a digit <code>0-9</code>, a dot <code>.</code>, a hyphen <code>-</code> or an underscore <code>_</code> will be removed from the filename and replaced with the substitution character.', 'nfwplus'). '</p>

		<p><strong>' . __('Maximum allowed file size', 'nfwplus'). '</strong><br />' . __('If you allow uploads, you can select the maximum size of an uploaded file. Any file bigger than this value will be rejected. Note that if your PHP configuration uses the  <code>upload_max_filesize</code> directive, it will be used before NinjaFirewall.', 'nfwplus'). '</p>

		<h3>WordPress</h3>

		<p><strong>'. __('Block direct access to any PHP file located in one of these directories', 'nfwplus') .'</strong><br />' .
		__('Whether to block direct access to PHP files located in specific WordPress directories.', 'nfwplus').

		'<p><strong>' . __('Block attempts to modify important WordPress settings', 'nfwplus'). '</strong><br />' . __('Enabling this policy will block any attempt (e.g., exploiting a vulnerability, using a backdoor etc) to modify some important WordPress settings. This policy will also send you an alert by email with all details regarding the issue. It is enabled by default.', 'nfwplus') . '</p>

		<p><strong>' . __('Block user accounts creation', 'nfwplus'). '</strong><br />' . __('Enabling this policy will block any attempt (e.g., exploiting a vulnerability, using a backdoor etc) to create a user account. If you allow user registration, you should not enable it.', 'nfwplus'). '</p>

		<p><strong>' . __('Block user accounts deletion', 'nfwplus'). '</strong><br />' . __('Enabling this policy will block any attempt (e.g., exploiting a vulnerability, using a backdoor etc) to delete a user account.', 'nfwplus'). '</p>

		<p><strong>' . __('Block attempts to gain administrative privileges', 'nfwplus'). '</strong><br />' . __('This policy will block vulnerabilities that could be leveraged by attackers to gain administrative privileges.', 'nfwplus') .'</p>
		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . __("If you have a multisite installation, this option will apply to the main site only. If you want it to apply to all subsites in your network, check the 'Apply to all subsites in the network' option.", 'nfwplus'). '</p>

		<p><strong>' . __('Block attempts to publish, edit or delete a published post by users who do not have the right capabilities', 'nfwplus'). '</strong><br />' . __('This policy will block vulnerabilities that could be leveraged by attackers to create, edit or delete posts. Note that it applies to <code>post</code> and <code>page</code> post types only (not custom ones).', 'nfwplus'). '</p>

		<p><strong>' . __('WordPress AJAX', 'nfwplus'). '</strong><br />' . sprintf( __('Many vulnerabilities in plugins are exploited via the admin-ajax.php script. This policy will try to detect and immediately block bots and malicious scanners trying to access it. The server IP address (%s) and private IP addresses will not be blocked.', 'nfwplus'), NFW_REMOTE_ADDR ). '</p>

		<p><strong>' . __('Protect against username enumeration', 'nfwplus'). '</strong><br />' . __('It is possible to enumerate usernames either through the WordPress author archives, author sitemap, the REST API or the login page. Although this is not a vulnerability but a WordPress feature, some hackers use it to retrieve usernames in order to launch more accurate brute-force attacks. If it is a failed login attempt, NinjaFirewall will sanitise the error message returned by WordPress. If it is an author archives scan, it will invalidate it and redirect the user to the blog index page. Regarding the WP REST API, it will block the request immediately.', 'nfwplus'). '</p>

		<p><strong>' . __('WordPress REST API', 'nfwplus'). '</strong><br />' . __('It allows you to access your WordPress site\'s data through an easy-to-use HTTP REST API. Since WordPress 4.7, it is enabled by default. NinjaFirewall allows you to block any access to that API if you do not intend to use it.', 'nfwplus'). '</p>

		<p><strong>' . __('WordPress XML-RPC API', 'nfwplus'). '</strong><br />' . __('XML-RPC is a remote procedure call (RPC) protocol which uses XML to encode its calls and HTTP as a transport mechanism. WordPress has an XMLRPC API that can be accessed through the <code>xmlrpc.php</code> file. Since WordPress version 3.5, it is always activated and cannot be turned off. NinjaFirewall allows you to immediately block any access to that file, or only to block an access using the <code>system.multicall</code> method often used in brute-force amplification attacks or to block Pingbacks.', 'nfwplus'). '</p>

		<p><strong>' . __('Disable Application Passwords', 'nfwplus'). '</strong><br />' . __('This option will disabled the Application Passwords feature introduced in WordPress 5.6.', 'nfwplus'). '</p>

		<p><strong>' . __('Block <code>POST</code> requests in the themes folder <code>/wp-content/themes</code>', 'nfwplus'). '</strong><br />' . __('This option can be useful to block hackers from installing backdoor in the PHP theme files. However, because some custom themes may include an HTML form (contact, search form etc), this option is not enabled by default.', 'nfwplus'). '</p>

		<p><strong>' . __('Force HTTPS for admin and logins <code>FORCE_SSL_ADMIN</code>', 'nfwplus'). '</strong><br />' . __('Enable this option when you want to secure logins and the admin area so that both passwords and cookies are never sent in the clear. Ensure that you can access your admin console from HTTPS before enabling this option, otherwise you will lock yourself out of your site!', 'nfwplus'). '</p>

		<p><strong>' . __('Disable the plugin and theme editor <code>DISALLOW_FILE_EDIT</code>', 'nfwplus'). '</strong><br />' . __('Disabling the plugin and theme editor provides an additional layer of security if a hacker gains access to a well-privileged user account.', 'nfwplus'). '</p>

		<p><strong>' . __('Disable plugin and theme update/installation <code>DISALLOW_FILE_MODS</code>', 'nfwplus'). '</strong><br />' . __('This option will block users being able to use the plugin and theme installation/update functionality from the WordPress admin area. Setting this constant also disables the Plugin and Theme editor.', 'nfwplus'). '</p>

		<p><strong>' . __('Disable the fatal error handler <code>WP_DISABLE_FATAL_ERROR_HANDLER</code>', 'nfwplus'). '</strong><br />' . __('This option will disable the WSOD protection introduced in WordPress 5.1.', 'nfwplus'). '</p>

		</div>'
	) );


	get_current_screen()->add_help_tab( array(
		'id'			=> 'policies04',
		'title'		=> __('Intermediate Policies', 'nfwplus'),
		'content'	=> '
		<div style="height:400px;">


		<h3>' . __('HTTP GET variable', 'nfwplus'). '</h3>' .
		__('Whether to scan and/or sanitise the <code>GET</code> variable.', 'nfwplus').


		'<h3>' . __('HTTP POST variable', 'nfwplus'). '</h3>' .
		__('Whether to scan and/or sanitise the <code>POST</code> variable.', 'nfwplus').
		'<p><strong>'. __('Decode Base64-encoded <code>POST</code> variable:', 'nfwplus'). '</strong><br />' . __('NinjaFirewall will decode and scan base64 encoded values in order to detect obfuscated malicious code. This option is only available for the <code>POST</code> variable.', 'nfwplus'). '</p>


		<h3>' . __('HTTP REQUEST variable', 'nfwplus'). '</h3>'.
		__('Whether to sanitise the <code>REQUEST</code> variable.', 'nfwplus').

		'<h3>' . __('Cookies', 'nfwplus'). '</h3>'.
		__('Whether to scan and/or sanitise cookies.', 'nfwplus').


		'<h3>' . __('HTTP_USER_AGENT server variable', 'nfwplus'). '</h3>'.
		__('Whether to scan and/or sanitise <code>HTTP_USER_AGENT</code> requests.', 'nfwplus').


		'<h3>' . __('HTTP_REFERER server variable', 'nfwplus'). '</h3>'.
		__('Whether to scan and/or sanitise <code>HTTP_REFERER</code> requests.', 'nfwplus').
		'<p><strong>' . __('Block POST requests that do not have an <code>HTTP_REFERER</code> header', 'nfwplus'). '</strong><br />' . __('This option will block any <code>POST</code> request that does not have a Referrer header (<code>HTTP_REFERER</code> variable). If you need external applications to post to your scripts (e.g. Paypal IPN, WordPress WP-Cron...), you are advised to keep this option disabled otherwise they will likely be blocked. Note that <code>POST</code> requests are not required to have a Referrer header and, for that reason, this option is disabled by default.', 'nfwplus'). '</p>

		</div>'
	) );

	get_current_screen()->add_help_tab( array(
		'id'			=> 'policies05',
		'title'		=> __('Advanced Policies', 'nfwplus'),
		'content'	=> '
		<div style="height:400px;">

		<h3>' . __('HTTP response headers', 'nfwplus'). '</h3>

		' . __('In addition to filtering incoming requests, NinjaFirewall can also hook the HTTP response in order to alter its headers. Those modifications can help to mitigate threats such as XSS, phishing and clickjacking attacks.', 'nfwplus'). '

		<p><strong>' . __('Set <code>X-Content-Type-Options</code> to protect against MIME type confusion attacks', 'nfwplus'). '</strong><br />' . __('This header will send the nosniff value to instruct the browser to disable content or MIME sniffing and to use the content-type returned by the server. Some browsers try to guess (sniff) and override the content-type by looking at the content itself which, in some cases, could lead to security issues such as MIME Confusion Attacks.', 'nfwplus'). '</p>

		<p><strong>' . __('Set <code>X-Frame-Options</code> to protect against clickjacking attempts', 'nfwplus'). '</strong><br />' . __('This header indicates a policy whether a browser must not allow to render a page in a &lt;frame&gt; or &lt;iframe&gt;. Hosts can declare this policy in the header of their HTTP responses to prevent clickjacking attacks, by ensuring that their content is not embedded into other pages or frames. NinjaFirewall accepts two different values:', 'nfwplus'). '</p>
		<ul>
			<li><code>SAMEORIGIN</code>: ' . __('A browser receiving content with this header must not display this content in any frame from a page of different origin than the content itself.', 'nfwplus'). '</li>
			<li><code>DENY</code>: ' . __('A browser receiving content with this header must not display this content in any frame.', 'nfwplus'). '</li>
		</ul>
		<p>'. __('NinjaFirewall does not support the <code>ALLOW-FROM</code> value.', 'nfwplus'). '</p>
		<p>'. __('Since v3.1.3, WordPress sets this value to <code>SAMEORIGIN</code> for the administrator and the login page only.', 'nfwplus'). '</p>

		<p><strong>' . __('Set <code>X-XSS-Protection</code>', 'nfwplus'). '</strong><br />' . __('This header allows browsers to identify and block XSS attacks by preventing malicious scripts from executing. It is enabled by default on all compatible browsers.', 'nfwplus'). '</p>'.
		'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . __('This header is deprecated and most browsers phased out support for it. Consider using Content-Security-Policy instead.', 'nfwplus'). '</p>'.

		'<p><strong>' . __('Force <code>SameSite</code> flag on all cookies to mitigate CSRF attacks', 'nfwplus'). '</strong><br />' . __('Adding this flag to cookies helps to mitigate the risk of CSRF (cross-site request forgery) attacks because cookies can only be sent in requests originating from the same origin as the target domain.', 'nfwplus'). '</p>'.

		'<p><strong>' . __('Force <code>HttpOnly</code> flag on all cookies to mitigate XSS attacks', 'nfwplus'). '</strong><br />' . __('Adding this flag to cookies helps to mitigate the risk of cross-site scripting by preventing them from being accessed through client-side scripts. NinjaFirewall can hook all cookies sent by your blog, its plugins or any other PHP script, add the <code>HttpOnly</code> flag if it is missing, and re-inject those cookies back into your server HTTP response headers right before they are sent to your visitors. Note that WordPress sets that flag on the logged in user cookies only.', 'nfwplus'). '</p>
		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . __('If your PHP scripts send cookies that need to be accessed from JavaScript, you should keep that option disabled.', 'nfwplus'). '</p>

		<p><strong>' . __('Set <code>Strict-Transport-Security</code> (HSTS) to enforce secure connections to the server', 'nfwplus'). '</strong><br />' . __('This policy enforces secure HTTPS connections to the server. Web browsers will not allow the user to access the web application over insecure HTTP protocol. It helps to defend against cookie hijacking and Man-in-the-middle attacks. Most recent browsers support HSTS headers.', 'nfwplus'). '</p>

		<p><strong>' . __('Set <code>Content-Security-Policy</code>', 'nfwplus'). '</strong><br />' . __('This policy helps to mitigate threats such as XSS, phishing and clickjacking attacks. It covers JavaScript, CSS, HTML frames, web workers, fonts, images, objects (Java, ActiveX, audio and video files), and other HTML5 features.', 'nfwplus'). ' ' . __('NinjaFirewall lets you configure the CSP policy separately for the frontend (blog, website) and the backend (WordPress admin dashboard).', 'nfwplus') . '</p>

		<p><strong>' . __('Set <code>Referrer-Policy</code>', 'nfwplus'). '</strong><br />' . __('This HTTP header governs which referrer information, sent in the Referer header, should be included with requests made.', 'nfwplus') . '</p>

		<h3>PHP</h3>

		<p><strong>' . __('Block PHP built-in wrappers', 'nfwplus'). '</strong><br />' . __('PHP has several wrappers for use with the filesystem functions. It is possible for an attacker to use them to bypass firewalls and various IDS to exploit remote and local file inclusions. This option lets you block any script attempting to pass a <code>expect://</code>, <code>file://</code>, <code>phar://</code>, <code>php://</code>, <code>zip://</code> or <code>data://</code> stream inside a <code>GET</code> or <code>POST</code> request, cookies, user agent and referrer variables.', 'nfwplus'). '</p>

		<p><strong>' . sprintf( __('Block serialized PHP objects', 'nfwplus'). '</strong><br />' . __('Object Serialization is a PHP feature used by many applications to generate a storable representation of a value. However, some insecure PHP applications and plugins can turn that feature into a critical vulnerability called <a href="%s">PHP Object Injection</a>. This option can block serialized PHP objects found inside a <code>GET</code> or <code>POST</code> request, cookies, user agent and referrer variables.', 'nfwplus'), 'https://www.owasp.org/index.php/PHP_Object_Injection'). '</p>

		<p><strong>' . sprintf( __('Block attempts to override PHP Superglobals', 'nfwplus'). '</strong><br />' . __('This policy will block attempts to override superglobals (%s). A plugin or a theme could make an unsafe use of some PHP functions that could potentially override superglobals. Enabling this option will not block the request but unset the dangerous value and write the event ot the firewall log.', 'nfwplus'), '<code>_GET</code>, <code>_POST</code>, <code>_COOKIE</code>, <code>_SESSION</code>, <code>_SERVER</code>, <code>_FILES</code>, <code>_ENV</code>, <code>_REQUEST</code> and <code>GLOBALS</code>'). '</p>

		<p><strong>' . __('Hide PHP notice and error messages', 'nfwplus'). '</strong><br />' . __('This option lets you hide errors returned by your scripts. Such errors can leak sensitive informations which can be exploited by hackers.', 'nfwplus'). '</p>

		<p><strong>' . __('Sanitise <code>PHP_SELF</code>, <code>PATH_TRANSLATED</code>, <code>PATH_INFO</code>', 'nfwplus'). '</strong><br />' . __('This option can sanitise any dangerous characters found in those 3 server variables to prevent various XSS and database injection attempts.', 'nfwplus'). '</p>


		<h3>' . __('Various', 'nfwplus'). '</h3>

		<p><strong>' . sprintf( __('Block the <code>DOCUMENT_ROOT</code> server variable (%s) in HTTP requests', 'nfwplus'), '<code>' . $_SERVER['DOCUMENT_ROOT'] . '</code>'). '</strong><br />' . __('This option will block scripts attempting to pass the <code>DOCUMENT_ROOT</code> server variable in a <code>GET</code> or <code>POST</code> request. Hackers use shell scripts that often need to pass this value, but most legitimate programs do not.', 'nfwplus'). '</p>

		<p><strong>' . __('Block ASCII character 0x00 (NULL byte)', 'nfwplus'). '</strong><br />' . __('This option will reject any <code>GET</code> or <code>POST</code> request, <code>COOKIE</code>, <code>HTTP_USER_AGENT</code>, <code>REQUEST_URI</code>, <code>PHP_SELF</code>, <code>PATH_INFO</code>, <code>HTTP_REFERER</code> variables containing the ASCII character 0x00 (NULL byte). Such a character is dangerous and should always be rejected.', 'nfwplus'). '</p>

		<p><strong>' . __('Block ASCII control characters 1 to 8 and 14 to 31', 'nfwplus'). '</strong><br />' . __('This option will reject any <code>GET</code> or <code>POST</code> request, <code>HTTP_USER_AGENT</code>, <code>HTTP_REFERER</code> variables containing ASCII characters from 1 to 8 and 14 to 31.', 'nfwplus'). '</p>

		<p><strong>' . __('Block localhost IP in <code>GET/POST</code> requests', 'nfwplus'). '</strong><br />' . __('This option will block any <code>GET</code> or <code>POST</code> request containing the localhost IP (127.0.0.1). It can be useful to block SQL dumpers and various hacker\'s shell scripts.', 'nfwplus'). '</p>

		<p><strong>' . __('Block HTTP requests with an IP in the <code>HTTP_HOST</code> header', 'nfwplus'). '</strong><br />' . sprintf( __('This option will reject any request using an IP instead of a domain name in the <code>Host</code> header of the HTTP request. Unless you need to connect to your site using its IP address, (e.g. %s), enabling this option will block a lot of hackers scanners because such applications scan IPs rather than domain names.', 'nfwplus'), 'https://1.2.3.4/index.php'). '</p>
		<br />&nbsp;

		</div>'
	) );

}
/* ================================================================== */ // i18n+

function help_nfsubaccesscontrol() {

	// Access Control :

	get_current_screen()->add_help_tab( array(
		'id'        => 'ac00',
		'title'     => __('Access Control', 'nfwplus'),
		'content'   => '<br />
		<div style="height:400px;">' .
		 __('Access Control is a powerful set of directives that can be used to allow or restrict access to your website based on many criteria.', 'nfwplus').
		' ' .
		 __('To make better use of them, it is important to understand NinjaFirewall\'s directives processing order.', 'nfwplus').
		'<br />' .
		 __('Because NinjaFirewall is a PHP firewall, its Access Control options apply to PHP scripts, not to static elements (e.g., images, JS, CSS etc). Depending on your configuration, they can also apply to HTML pages.', 'nfwplus').
		'<br />
		<br />
		'. __('Processing order:', 'nfwplus') .'<br />
		<p><strong>' .__('Incoming HTTP request', 'nfwplus') .'</strong></p>
		<ol>
			<li>' . sprintf( __('%s file', 'nfwplus'), '<code><a href="https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/">.htninja</a></code>') .'.</li>
			<li>' .__('Login Protection.', 'nfwplus') .'</li>
			<li>' .__('Access Control (except User Input Access Control):', 'nfwplus') .'</li>
			<ol>
				<li>' .__('Role-based Access Control.', 'nfwplus') .'</li>
				<li>' .__('Allowed IPs.', 'nfwplus') .'</li>
				<li>' .__('Blocked IPs.', 'nfwplus') .'</li>
				<li>' .__('Allowed URLs.', 'nfwplus') .'</li>
				<li>' .__('Blocked URLs.', 'nfwplus') .'</li>
				<li>' .__('Bot Access Control.', 'nfwplus') .'</li>
				<li>' .__('Geolocation.', 'nfwplus') .'</li>
				<li>' .__('Rate Limiting.', 'nfwplus') .'</li>
			</ol>
			<li>' .__('File Guard.', 'nfwplus') .'</li>
			<li>' .__('NinjaFirewall built-in rules and policies + User Input Access Control.', 'nfwplus') .'</li>
		</ol>

		<p><strong>' .__('Response body', 'nfwplus') .'</strong></p>
		<ol>
			<li>' .__('HTTP response headers (Firewall Policies).', 'nfwplus') .'</li>
			<li>' .__('Web Filter.', 'nfwplus') .'</li>
		</ol>
		<br />&nbsp;
		</div>'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'ac01',
		'title'     => __('Role-based Access Control', 'nfwplus'),
		'content'   => '<br />' .
		sprintf( __('By default, any logged in WordPress administrator will not be blocked by NinjaFirewall. This applies to all "Access Control" listed below as well as the <strong>Antispam</strong>, the <strong>Web Filter</strong> and the <strong>Firewall Policies</strong>, except <code>FORCE_SSL_ADMIN</code>, <code>DISALLOW_FILE_EDIT</code>, <code>DISALLOW_FILE_MODS</code> options and the <a href="%s">Login Protection</a> which, if enabled, are always enforced.', 'nfwplus'), '?page=nfsubloginprot').
		'<br />' .
		__('You can also add other users to the whitelist, depending on their role.', 'nfwplus')
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'ac02',
		'title'     => __('Source IP', 'nfwplus'),
		'content'   => '
		<p><strong>'. __('Retrieve visitors IP address from', 'nfwplus') .'</strong><br />'. sprintf( __('this option should be used if you are behind a reverse proxy, a load balancer or using a CDN, in order to tell NinjaFirewall which IP it should use. By default, it will rely on <code>REMOTE_ADDR</code>. If you want it to use <code>HTTP_X_FORWARDED_FOR</code> or any other similar variable, it is <a href="%s">absolutely necessary to ensure that it is reliable</a> (i.e., setup by your own load balancer/reverse proxy) because it can be easily spoofed. If that variable includes more than one IP, only the left-most (the original client) will be checked. If it does not include any IP, NinjaFirewall will fall back to <code>REMOTE_ADDR</code>.', 'nfwplus'), 'https://blog.nintechnet.com/many-popular-wordpress-security-plugins-vulnerable-to-ip-spoofing/') .'</p>

		<p><strong>'. __('Scan traffic coming from localhost and private IP address spaces', 'nfwplus') .'</strong><br />'. __('this option will allow the firewall to scan traffic from all non-routable private IPs (IPv4 and IPv6) as well as the localhost IP. We recommend to keep it enabled if you have a private network (2 or more servers interconnected).', 'nfwplus') .'</p>
		'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'ac03',
		'title'     => __('HTTP Methods', 'nfwplus'),
		'content'   => '<p>'. __('This option lets you select the HTTP method(s). All Access Control directives (Geolocation, IPs, bots and URLs) will only apply to the selected methods.', 'nfwplus') .'</p>
		<p>'. __('It does not apply to the "Firewall Policies" options, which use their own ones.', 'nfwplus') .'</p>
		'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'ac04',
		'title'     => __('Geolocation Access Control', 'nfwplus'),
		'content'   => '<br />
		<div style="height:400px;">' .
		__('You can filter and block traffic coming from specific countries/territories.', 'nfwplus') .
		'<br />
		<p><strong>'. __('Retrieve the ISO 3166 code from', 'nfwplus') .'</strong><br />'. __('This is the two-letter code that is used to define a country/territory (e.g., US, UK, FR, DE etc), based on the visitors IP. NinjaFirewall can either retrieve it from its database, or from a predefined PHP variable added by your HTTP server (e.g., <code>GEOIP_COUNTRY_CODE</code>).', 'nfwplus') .'</p>

		<p><strong>'. __('Block the following ISO 3166 codes', 'nfwplus') .'</strong><br />'. __('You can add/remove any country/territory from the two listboxes.', 'nfwplus') .'</p>

		<p><strong>'. __('Geolocation should apply to the whole site or to specific URLs only?', 'nfwplus') .'</strong><br />'. __('Whether geolocation should apply to the whole site or to specific URLs only (e.g., /wp-login.php, /xmlrpc.php etc). Leave all fields empty if you want it to apply to the whole site.', 'nfwplus') .'</p>

		<p><strong>'. __('Add <code>NINJA_COUNTRY_CODE</code> to PHP headers?', 'nfwplus') .'</strong><br />'. __('After retrieving the two-letter country/territory code, NinjaFirewall can add it to the PHP headers in the <code>$_SERVER["NINJA_COUNTRY_CODE"]</code> variable. If you have a theme or a plugin that needs to know your visitors location, simply use that variable.', 'nfwplus') .'</p>

		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'. __('If NinjaFirewall cannot find the two-letter ISO 3166 code, it will replace it with 2 hyphens (<code>--</code>).', 'nfwplus') .'</p>

		'. __('PHP code example to use in your theme or plugin to geolocate your visitors:', 'nfwplus') .
		'<br />
		<center>
		<textarea class="large-text code" style="height:100px;" wrap="off">if (! empty($_SERVER[\'NINJA_COUNTRY_CODE\']) &&
     $_SERVER[\'NINJA_COUNTRY_CODE\'] != \'--\' ) {
	echo \'Your ISO 3166 code is: \' . $_SERVER[\'NINJA_COUNTRY_CODE\'];
}</textarea>
		</center>
		<br />
		<div align="right" style="font-size:11px;color:#999999;">'. __('NinjaFirewall includes GeoLite data created by MaxMind, available from https://www.maxmind.com', 'nfwplus') .'</div>
		</div>
		'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'ac05',
		'title'     => __('IP / URL / Bot Access Control', 'nfwplus'),
		'content'   => '
		<div style="height:400px;">
		<p><strong>'. __('IP Access Control', 'nfwplus') .'</strong><br />'. __('You can permanently allow/block an IP, a whole range of IP addresses or AS numbers (Autonomous System number). IPv4 and IPv6 are fully supported by NinjaFirewall.', 'nfwplus') . '</p>
		<ul>
		<li>'. __('Full IP:', 'nfwplus') .' <code>1.2.3.123</code></li>
		<li>'. sprintf( __('IP ranges using CIDR notation: %s or %s.', 'nfwplus'), '<code>66.155.0.0/17</code>', '<code>2c0f:f248::/32</code>' ). '</li>
		<li>'. __('Autonomous System number:', 'nfwplus') .' <code>AS12345</code></li>
		</ul>

		<p><strong>'. __('Rate Limiting', 'nfwplus') .'</strong><br />'. __('This option allows you to slow down aggressive bots, crawlers, web scrapers or even small HTTP attacks. Any IP reaching the defined threshold will be banned from 1 to 999 seconds. Note that the purpose of this feature is not to permanently block an IP but rather to temporarily prevent it from accessing the site and abusing your system resources. If you want to permanently block an IP, use the blacklist instead. Also, do not rely on this option to block brute force attacks on the login page, use the more suitable "Login Protection" for that purpose. By default, Rate Limiting is turned off.', 'nfwplus') .'</p>

		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'. __('IPs temporarily banned by the Rate Limiting option can be unblocked immediately by clicking either the "Save Access Control Directives" or "Restore Default Values" buttons at the bottom of this page.', 'nfwplus') .'</p>

		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;
		'. __('Because NinjaFirewall can handle a lot of HTTP requests per second and block IPs even before your blog is loaded, we strongly recommend that you disable the rate limiting/throttling option of any other WordPress plugin that you may have installed and only use NinjaFirewall\'s one instead. It will drastically speed up your site and reduce the server load on a busy site or during an attack.', 'nfwplus') .'</p>
		<br />

		<p><strong>'. __('URL Access Control', 'nfwplus') .'</strong><br />'. __('You can permanently allow/block any access to one or more PHP scripts based on their path, relative to the web root (<code>SCRIPT_NAME</code>). You can enter either a full or partial path (case-sensitive).', 'nfwplus') .'</p>
		<ul>
		<li>'. __('<code>/foo/bar.php</code> will block any access to the <code>bar.php</code> script located inside a <code>/foo/</code> directory', 'nfwplus') .' (<code>' . site_url() . '/foo/bar.php</code>, <code>' . site_url() . '/another/directory/foo/bar.php</code> '. __('etc', 'nfwplus') .').</li>
		<li>'. __('<code>/foo/</code> will block access to all PHP scripts located inside a <code>/foo/</code> directory and its sub-directories.', 'nfwplus') .'</li>
		</ul>
		'. __('Note that the "Firewall Policies" page already includes restrictions to some WordPress directories.', 'nfwplus') .'
		<br />
		<p><strong>'. __('Bot Access Control', 'nfwplus') .'</strong><br />'. __('You can block bots, scanners and various crawlers based on the <code>HTTP_USER_AGENT</code> variable. You can enter either a full or partial name (case-insensitive).', 'nfwplus') .'</p>
		<br />&nbsp;
		</div>'
	) );


	get_current_screen()->add_help_tab( array(
		'id'        => 'acinput',
		'title'     => __('User Input Access Control', 'nfwplus'),
		'content'   => '
		<div style="height:400px;">
		<br />'. __('You can select to ignore or block some specific user input. It applies to the <code>GET</code>, <code>POST</code> and <code>COOKIE</code> global variables, for instance <code>$_GET["foo"]</code> or <code>$_POST["bar"]</code>:', 'nfwplus') .
		'<br />
		<ul>
			<li>'.
				__('When an input is added to the "Unfiltered input" list, it will not be filtered or sanitised. All other input present in the request will be filtered.', 'nfwplus' ) .'
			</li>
			<li>'.
				__('When an input is added to the "Blocked input", NinjaFirewall will block the request and close the connection if that input is found in the request.', 'nfwplus' ) .'
			</li>
		</ul>
		<br />&nbsp;
		</div>'
	) );


	get_current_screen()->add_help_tab( array(
		'id'        => 'ac06',
		'title'     => __('Log Event', 'nfwplus'),
		'content'   => '<br />'.
		__('You can enable/disable firewall logging for each access control directive separately.', 'nfwplus')
	) );

}
/* ================================================================== */ // i18n+

function help_nfsubfileguard() {

	// File check menu help :
	get_current_screen()->add_help_tab( array(
		'id'        => 'filecheck01',
		'title'     => __('File Check', 'nfwplus'),
		'content'   => '<p>'. __('File Check lets you perform file integrity monitoring upon request or on a specific interval.', 'nfwplus') .
			'<br />' .
			__('You need to create a snapshot of all your files and then, at a later time, you can scan your system to compare it with the previous snapshot. Any modification will be immediately detected: file content, file permissions, file ownership, timestamp as well as file creation and deletion.', 'nfwplus') .'</p>' .
			'<ul>'.
			'<li>'. sprintf( __('Create a snapshot of all files stored in that directory: by default, the directory is set to WordPress <code>ABSPATH</code> (%s)', 'nfwplus'), '<code>' . ABSPATH . '</code>') .'</li>'.
			'<li>'.  __('Exclude the following files/folders: you can enter a directory or a file name (e.g., <code>/foo/bar/</code>), or a part of it (e.g., <code>foo</code>). Or you can exclude a file extension (e.g., <code>.css</code>).', 'nfwplus') .
			'<br />' .
			__('Multiple values must be comma-separated (e.g., <code>/foo/bar/,.css,.png</code>).', 'nfwplus') .'</li>' .
			'<li>'.  __('Do not follow symbolic links: by default, NinjaFirewall will not follow symbolic links.', 'nfwplus') .'</li>'.
			'</ul>'.

		'<p><strong>'. __('Scheduled scans', 'nfwplus') .'</strong><p>'.
		 '<p>'. __('NinjaFirewall can scan your system on a specific interval (hourly, twicedaily or daily).', 'nfwplus').
			'<br />'.
			__('It can either send you a scan report only if changes are detected, or always send you one after each scan.', 'nfwplus').
			'<br />'.
			__('Reports will be sent to the contact email address defined in the "Event Notifications" menu.', 'nfwplus'). '</p>'.

			'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'. sprintf( __('Scheduled scans rely on <a href="%s">WordPress pseudo cron</a> which works only if your site gets sufficient traffic.', 'nfwplus'), 'http://codex.wordpress.org/Category:WP-Cron_Functions') . '</p>'
	) );

	// File Guard :
	get_current_screen()->add_help_tab( array(
		'id'        => 'fileguard01',
		'title'     => __('File Guard', 'nfwplus'),
		'content'   => '<br/>' .
			__('File Guard can detect, in real-time, any access to a PHP file that was recently modified or created, and alert you about this.', 'nfwplus') .
			'<br />' .
			__('If a hacker uploaded a shell script to your site (or injected a backdoor into an already existing file) and tried to directly access that file using his browser or a script, NinjaFirewall would hook the HTTP request and immediately detect that the file was recently modified/created. It would send you a detailed alert (script name, IP, request, date and time). Alerts will be sent to the contact email address defined in the "Event Notifications" menu.', 'nfwplus') .
			'<p>' . __('If you do not want to monitor a folder, you can exclude its full path or a part of it (e.g., <code>/var/www/public_html/cache/</code> or <code>/cache/</code> etc). NinjaFirewall will compare this value to the <code>$_SERVER["SCRIPT_FILENAME"]</code> server variable and, if it matches, will ignore it.', 'nfwplus') . '</p>' .
			__('Multiple values must be comma-separated (e.g., <code>/foo/bar/,/cache/</code>).', 'nfwplus') .
			'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . __('File Guard real-time detection is a totally unique feature, because NinjaFirewall is the only plugin for WordPress that can hook HTTP requests sent to any PHP script, even if that script is not part of the WordPress package (third-party software, shell script, backdoor etc).', 'nfwplus') . '</p>'
	) );

	// Web Filter:
	get_current_screen()->add_help_tab( array(
		'id'        => 'webfilter01',
		'title'     => __('Web Filter', 'nfwplus'),
		'content'   => '<br />' .
			__('If NinjaFirewall can hook and scan incoming requests, it can also hook the response body (i.e., the output of the HTML page right before it is sent to your visitors browser) and search it for some specific keywords. Such a filter can be useful to detect hacking or malware patterns injected into your HTML code (text strings, spam links, malicious JavaScript code), hackers shell script, redirections and even errors (PHP/MySQL errors).', 'nfwplus'). '
		<p>' . __('In the case of a positive detection, NinjaFirewall will not block the response body but will send you an alert by email.', 'nfwplus'). '</p>

		<p><strong>' . __('Search HTML page for the following keywords', 'nfwplus'). '</strong><br />' . __('You can enter any keyword from 4 to 150 characters and select whether the search will be case sensitive or not.', 'nfwplus'). '</p>

		<p><strong>' . __('Email Alerts', 'nfwplus'). '</strong><br />' . __('You can use the notification throttling option to limit the frequency of alerts sent to you (and written to the firewall log) and select whether you want NinjaFirewall to send you the whole HTML source of the page where the keyword was found. Alerts will be sent to the contact email address defined in the "Event Notifications" menu.', 'nfwplus'). '</p>

		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . __('Response body filtering can be resource-intensive. Try to limit the number of keywords to what you really need (less than 10) and, if possible, prefer case sensitive to case insensitive filtering.', 'nfwplus'). '</p>'
	) );


}
/* ================================================================== */ // i18n+

function help_nfsubnetwork() {	// i18n

	// Network (multisite version only) :
	get_current_screen()->add_help_tab( array(
		'id'        => 'network01',
		'title'     => __('Network', 'nfwplus'),
		'content'   => '<br />' .
			__('Even if NinjaFirewall administration menu is only available to the Super Admin (from the main site), you can still display its status to all sites in the network by adding a small NinjaFirewall icon to their WordPress ToolBar. It will be visible only to the administrators of those sites.', 'nfwplus') .
			'<br />' .
			__('It is recommended to enable this feature as it is the only way to know whether the sites in your network are protected and if NinjaFirewall installation was successful.', 'nfwplus') .
			'<br />'.
			__('Note that when it is disabled, the icon still remains visible to you, the Super Admin.', 'nfwplus')
	) );
}
/* ================================================================== */ // i18n+

function help_nfsubevent() {

	// Event Notifications menu help :

	get_current_screen()->add_help_tab( array(
		'id'        => 'log01',
		'title'     => __('Event Notifications', 'nfwplus'),
		'content'   => '<br />' . __('NinjaFirewall can alert you by email on specific events triggered within your blog. They include installations, updates, activations etc, as well as users login and modification of any administrator account in the database. Some of those alerts are enabled by default and it is highly recommended to keep them enabled. It is not unusual for a hacker, after breaking into your WordPress admin console, to install or just to upload a backdoored plugin or theme in order to take full control of your website.', 'nfwplus')
	) );
}
/* ================================================================== */ // i18n+

function help_nfsublogin() {

	// Login protection menu help :

	get_current_screen()->add_help_tab( array(
		'id'        => 'login01',
		'title'     => __('Login Protection', 'nfwplus'),
		'content'   => '
		<div style="height:400px;">

		<p>' . __('By processing incoming HTTP requests before your blog and any of its plugins, NinjaFirewall is the only plugin for WordPress able to protect it against very large brute-force attacks, including distributed attacks coming from several thousands of different IPs.', 'nfwplus') .

		'<p>' . __('You can choose two different types of protection: a password or a captcha. You can enable the protection only if an attack is detected or to keep it always activated.', 'nfwplus') . '</p>

		<strong>' . __('Yes, if under attack:', 'nfwplus') . '</strong>
		<br />' .
		__('The protection will be triggered when too many login attempts are detected, regardless of the offending IP. It blocks the attack instantly and prevents it from reaching WordPress, but still allows you to access your administration console using either the predefined username/password combination or the captcha code.', 'nfwplus') . '
		<br />
		<strong>' . __('Always ON:', 'nfwplus') . '</strong>
		<br />'.
		__('NinjaFirewall will always enforce the HTTP authentication or captcha implementation each time you access the login page.', 'nfwplus') . '
		<br />
		<br />
		<strong>' . __('Type of protection:', 'nfwplus') . '</strong>
		<p>' . __('<b>Password:</b> It password-protects the login page. NinjaFirewall uses its own very fast authentication scheme and it is compatible with any HTTP server (Apache, Nginx, Lighttpd etc).', 'nfwplus') . '</p>
		<p>' . __('<b>Captcha:</b> It will display a 5-character captcha code.', 'nfwplus') . '</p>
		<p><b>' . __('Bot protection:', 'nfwplus') . '</b>
		<br />' . __('NinjaFirewall will attempt to block bots and scripts immediately, i.e., even before they start a brute-force attack.', 'nfwplus') . '</p>

		<br />&nbsp;
		</div>'
	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'login02',
		'title'     => __('AUTH log', 'nfwplus'),
		'content'   => '
		<div style="height:400px;">
		<p>' . __('NinjaFirewall can write to the server Authentication log when the brute-force protection is triggered. This can be useful to the system administrator for monitoring purposes or banning IPs at the server level.', 'nfwplus') . '
		<br />' .
		__('If you have a shared hosting account, keep this option disabled as you do not have any access to the server\'s logs.', 'nfwplus') .
		'<br />' .
		__('On Debian-based systems, the log is located in <code>/var/log/auth.log</code>, and on Red Hat-based systems in <code>/var/log/secure</code>. The logline uses the following format:', 'nfwplus') .
		'<p><code>ninjafirewall[<font color="red">AA</font>]: Possible brute-force attack from <font color="red">BB</font> on <font color="red">CC</font> (<font color="red">DD</font>). Blocking access for <font color="red">EE</font>mn.</code><p>
		<ul>
			<li>' . __('AA: the process ID (PID).', 'nfwplus') . '</li>
			<li>' . __('BB: the user IPv4 or IPv6 address.', 'nfwplus') . '</li>
			<li>' . __('CC: the blog (sub-)domain name.', 'nfwplus') . '</li>
			<li>' . __('DD: the target: it can be either <code>wp-login.php</code> or <code>XML-RPC API</code>.', 'nfwplus') . '</li>
			<li>' . __('EE: the time, in minutes, the protection will remain active.', 'nfwplus') . '</li>
		</ul>'.
		__('Sample loglines:', 'nfwplus') .
		'<br />
		<textarea class="large-text code" style="height:80px;" wrap="off">Aug 31 01:40:35 www ninjafirewall[6191]: Possible brute-force attack from 172.16.0.1 on mysite.com (wp-login.php). Blocking access for 5mn.'. "\n" . 'Aug 31 01:45:28 www ninjafirewall[6192]: Possible brute-force attack from fe80::6e88:14ff:fe3e:86f0 on blog.domain.com (XML-RPC API). Blocking access for 25mn.</textarea>
		<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . sprintf( __('Be careful if you are behind a load balancer, reverse-proxy or CDN because the Login Protection feature will always record the <code>REMOTE_ADDR</code> IP. If you have an application parsing the AUTH log in order to ban IPs (e.g. Fail2ban), you <strong>must</strong> setup your HTTP server to forward the correct IP (or use the <code><a href="%s">.htninja</a></code> file), otherwise you will likely block legitimate users.', 'nfwplus'), 'https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/') . '</p>
		</div>'
	) );
}
/* ================================================================== */ // i18n+

function help_nfsubantispam() {

	// Login protection menu help:

	get_current_screen()->add_help_tab( array(
		'id'        => 'antispam01',
		'title'     => __('Antispam', 'nfwplus'),
		'content'   => '

		<p>'. __('NinjaFirewall can protect your blog against spam without user interaction (e.g., CAPTCHA, math puzzles etc). The protection is totally transparent to your visitors. The antispam feature works only with WordPress built-in comment and registration forms. If you are using third-party plugins to generate your forms, they will not be protected against spam.', 'nfwplus') .'</p>

		<p><strong>'. __('Protection level:', 'nfwplus') .'</strong>
			<br />'.
			__('Select the level of protection. In most cases, <strong>Low</strong> should be enough.', 'nfwplus') .
		'</p>

		<p><strong>'. __('Apply protection to:', 'nfwplus') .'</strong>
			<br />'.
			__('Whether to protect comment and/or registration forms.', 'nfwplus') .
		'</p>

		<p>
			<strong>'. __('If you are using a caching plugin, ensure you follow these steps:', 'nfwplus') .'</strong>
		</p>
		<ol>
			<li>'. __('Set the Protection Level to "Low" only. Do not use another value, otherwise the antispam could behave erratically after a while.', 'nfwplus') .'</li>
			<li>'. __('Flush/clear your cache immediately after enabling or disabling the antispam.', 'nfwplus') .'</li>
		</ol>'
	) );
}
/* ================================================================== */ // i18n+

function help_nfsublog() {

	// Firewall log menu help :

	get_current_screen()->add_help_tab( array(
		'id'        => 'log01',
		'title'     => __('Firewall Log', 'nfwplus'),
		'content'   => '
		<div style="height:400px;">
			<br />'.
			__('The firewall log displays blocked and sanitised requests as well as some useful information. It has 6 columns:', 'nfwplus') . '
			<ul><li>' . __('DATE : date and time of the incident.', 'nfwplus') . '</li>
			<li>' . __('INCIDENT : unique incident number/ID as it was displayed to the blocked user.', 'nfwplus') . '</li>
			<li>' . __('LEVEL : level of severity (<code>CRITICAL</code>, <code>HIGH</code> or <code>MEDIUM</code>), information (<code>INFO</code>, <code>UPLOAD</code>) and debugging mode (<code>DEBUG_ON</code>).', 'nfwplus') . '</li>
			<li>' . __('RULE : reference of the NinjaFirewall built-in security rule that triggered the action. A hyphen (<code>-</code>) instead of a number means it was a rule from the "Firewall Policies" or "Access Control" pages.', 'nfwplus') . '</li>
			<li>' . __('IP : the user IPv4 or IPv6 address.', 'nfwplus') . '</li>
			<li>' . __('REQUEST : the HTTP request including offending variables and values as well as the reason the action was logged.', 'nfwplus') . '</li>
			</ul>
			<p>' . __('The log can also be exported as a TSV (tab-separated values) text file.', 'nfwplus') . '</p>'.


		'<p><strong>'. __('Enable firewall log', 'nfwplus') .'</strong></p>'.
			__('You can disable/enable the firewall log from this page.', 'nfwplus') .
		'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'. __('Brute-force attacks will still be written to the firewall log, even if you disable it.', 'nfwplus') .'</p>'.


		'<p><strong>'. __('Auto-rotate log', 'nfwplus') .'</strong></p>'.
			__('NinjaFirewall will rotate its log automatically on the very first day of each month. If your site is very busy, you may want to allow it to rotate the log when it reaches a certain size (MB) as well.', 'nfwplus').
			'<p>' .__('By default, if will rotate the log each month or earlier, if it reaches 2 megabytes.', 'nfwplus') . '</p>
			<p>' .__('Rotated logs, if any, can be selected and viewed from the dropdown menu.', 'nfwplus'). '</p>'.


		'<p><strong>'. __('Auto-delete log', 'nfwplus') .'</strong></p>'.
			__('This options lets you configure NinjaFirewall to delete its old logs automatically. By default, logs are never deleted, <b>even when uninstall NinjaFirewall</b>. Leave this value to <code>0</code> if you don\'t want to delete old logs.', 'nfwplus').

		'<p><strong>'. __('Sorting', 'nfwplus') .'</strong></p>'.
			__('This option lets you sort the firewall log.', 'nfwplus').

		'<p><strong>'. __('Centralized Logging', 'nfwplus') .'</strong></p>'.
			'<p>'. __('Centralized Logging lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation. You do not need any longer to log in to individual servers to analyse your log data.', 'nfwplus') .	' ' . sprintf( __('<a href="%s">Consult our blog</a> for more info about it.', 'nfwplus'), 'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/' ) . '</p>' .
			'<ul><li>' .	 __('Enter your public key (optional): This is the public key that was created from your main server.', 'nfwplus') . '</li>
			</ul>' .

			'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'.
			__('Centralized Logging will keep working even if NinjaFirewall is disabled. Delete your public key below if you want to disable it.', 'nfwplus') .'</p>'.


			'<p><strong>'. __('Syslog', 'nfwplus') .'</strong></p>'.
			'<p>'. __("In addition to the firewall log, events can also be redirected to the syslog server (<code>LOG_USER</code> facility).", 'nfwplus') .	' ' .
			__("If you have a shared hosting account, keep this option disabled as you do not have any access to the server logs.", 'nfwplus') .
			'</p>' .
			__('The logline uses the following format:', 'nfwplus') .
			'<p><code>ninjafirewall[<font color="red">AA</font>]: <font color="red">BB</font>: #<font color="red">CCCCCCC</font>: <i>Some event</i> from <font color="red">DD</font> on <font color="red">EE</font></code><p>
			<ul>
				<li>' . __('AA: the process ID (PID).', 'nfwplus') . '</li>
				<li>' . __('BB: the level of severity as it appears in the firewall log. It can be <code>CRITICAL</CODE>, <CODE>HIGH</CODE>, <CODE>MEDIUM</CODE>, <CODE>INFO</CODE>, <CODE>UPLOAD</CODE> or <CODE>DEBUG_ON</CODE>.', 'nfwplus') . '</li>
				<li>' . __('CCCCCCC: the 7-digit incident ID.', 'nfwplus') . '</li>
				<li>' . __('DD: the user IPv4 or IPv6 address.', 'nfwplus') . '</li>
				<li>' . __('EE: the blog (sub-)domain name.', 'nfwplus') . '</li>
			</ul>'.
			__('Sample loglines:', 'nfwplus') .
			'<br />
			<textarea class="large-text code" style="height:80px;" wrap="off">Oct  2 14:57:46 www ninjafirewall[11798]: INFO: #2654956: Logged in user from 12.24.56.78 on mysite.com'. "\n" . 'Oct  2 14:58:05 www ninjafirewall[23121]: HIGH: #7296291: Cross-site scripting from fe80::6e88:14ff:fe3e:86f0 on blog.domain.com</textarea>' .

			'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'.
			sprintf( __('This logging option does not apply to the brute-force protection which can be set up separately to write events to the server authentication log. See the <a href="%s">Login Protection</a> page.', 'nfwplus'), '?page=nfsubloginprot') .
			'</p><br />'.

			'</div>'

	) );


	get_current_screen()->add_help_tab( array(
		'id'        => 'log02',
		'title'     => __('Live Log', 'nfwplus'),
		'content'   =>
		'<div style="height:400px;">'.
			'<p>' .	__('Live Log lets you watch your blog traffic in real time, just like the Unix <code>tail -f</code> command. Note that requests sent to static elements like JS/CSS files and images are not managed by NinjaFirewall.', 'nfwplus') .'</p>

			<p>' . __('You can enable/disable the monitoring process, change the refresh rate, clear the screen, enable automatic vertical scrolling, change the log format, select which traffic you want to view (HTTP/HTTPS) and the timezone.', 'nfwplus') .' '. __('You can also apply filters to include or exclude files and folders (REQUEST_URI).', 'nfwplus') .
			'</p>

			<p>' . __('Live Log does not make use of any WordPress core file (e.g., <code>admin-ajax.php</code>). It communicates directly with the firewall without loading WordPress bootstrap. Consequently, it is fast, lightweight and it should not affect your server load, even if you set its refresh rate to the lowest value.', 'nfwplus') .	'</p>

			<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;' . __('If you are using the optional <code>.htninja</code> configuration file to whitelist your IP, the Live Log feature will not work.', 'nfwplus') . '
		</p>'.


		'<p><strong>'. __('Log Format', 'nfwplus') .'</strong></p>'.
		 __('You can easily customize the log format. Possible values are:', 'nfwplus') .
			'<ul><li>'. __('<code>%time</code>: the server date, time and timezone.', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%name</code>: authenticated user (HTTP basic auth), if any.', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%client</code>: the client REMOTE_ADDR. If you are behind a load balancer or CDN, this will be its IP.', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%method</code>: HTTP method (e.g., GET, POST).', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%uri</code>: the URI which was given in order to access the page (REQUEST_URI).', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%referrer</code>: the referrer (HTTP_REFERER), if any.', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%ua</code>: the user-agent (HTTP_USER_AGENT), if any.', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%forward</code>: HTTP_X_FORWARDED_FOR, if any. If you are behind a load balancer or CDN, this will likely be the visitor true IP.', 'nfwplus') . '</li>' .
			'<li>'. __('<code>%host</code>: the requested host (HTTP_HOST), if any.', 'nfwplus') . '</li>' .
			'</ul>'.
			__('Additionally, you can include any of the following characters: <code>"</code>, <code>%</code>, <code>[</code>, <code>]</code>, <code>space</code> and lowercase letters <code>a-z</code>.', 'nfwplus').

			'<br />&nbsp;</div>'

	) );

	get_current_screen()->add_help_tab( array(
		'id'        => 'centlog01',
		'title'     => __('Centralized Logging', 'nfwplus'),
		'content'   => '<br />'.
			__('Centralized Logging lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation. You do not need any longer to log in to individual servers to analyse your log data.', 'nfwplus') .
			' ' .
			__('There is no limit to the number of websites you can connect to, and they can be running any edition of NinjaFirewall: WP, <font color="#21759B">WP+</font>, Pro or <font color="red">Pro+</font>.', 'nfwplus') .
			'<ul>' .
			'<li>' . __('Secret key: The secret key will be used to generate your public key. Enter at least 30 ASCII characters, or use the one randomly created by NinjaFirewall.', 'nfwplus') . '</li>' .
			'<li>' . __('This server IP address: As an additional protection layer, you can restrict access to the remote website(s) to the main server IP only. You can use IPv4 or IPv6. If you do not want any IP restriction, enter the <code>*</code> character instead.', 'nfwplus') . '</li>' .
			'<li>' . sprintf( __('Public key: This is the public key that you will need to upload to each remote website (<a href="%s">consult our blog</a> for more info about it).', 'nfwplus'), 'https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/' ) . '</li>' .
			'<li>' . __('Remote websites URL: Enter the full URL of your NinjaFirewall protected website(s) that you want to remotely access from the main server.', 'nfwplus') . '</li>
			</ul>' .

			'<p><span class="dashicons dashicons-warning nfw-warning"></span>&nbsp;'.
			__('Centralized Logging will keep working even if NinjaFirewall is disabled. Use the menu below if you want to disable it.', 'nfwplus') .
			'</p>'

	) );


	// GDPR compliance tab:
	get_current_screen()->add_help_tab( array(
		'id'        => 'log07',
		'title'     => __('GDPR Compliance', 'nfwplus'),
		'content'   =>
			'<p>'.  __('Your website can run NinjaFirewall and be compliant with the General Data Protection Regulation (GDPR). For more info, please visit our blog:', 'nfwplus') .' <a href="https://blog.nintechnet.com/ninjafirewall-general-data-protection-regulation-compliance/">https://blog.nintechnet.com/ninjafirewall-general-data-protection-regulation-compliance/</a>'.
			'</p>'
	) );
}
/* ================================================================== */ // i18n+

function help_nfsubupdates() {

	// Firewall Updates menu help :

	get_current_screen()->add_help_tab( array(
		'id'        => 'updates01',
		'title'     => __('Rules Updates', 'nfwplus'),
		'content'   => '<p>'.
		__('To get the most efficient protection, you can ask NinjaFirewall to automatically update its security rules.', 'nfwplus') .
		'<br />' .
		__('Each time a new vulnerability is found in WordPress or one of its plugins/themes, a new set of security rules will be made available to protect against such vulnerability if needed.', 'nfwplus') .
		'<br />' .
		__('Only security rules will be downloaded. If a new version of NinjaFirewall (including new files, options and features) was available, it would have to be updated from the dashboard plugins menu as usual.', 'nfwplus') .
		'</p><p>' .
		__('We recommend to enable this feature, as it is the <strong>best way to keep your WordPress secure</strong> against new vulnerabilities.', 'nfwplus') . '</p>'
	) );


	get_current_screen()->add_help_tab( array(
		'id'        => 'editor01',
		'title'     => __('Rules Editor', 'nfwplus'),
		'content'   => '<br />' .
			__('Besides the "Firewall Policies", NinjaFirewall includes also a large set of built-in rules used to protect your blog against the most common vulnerabilities and hacking attempts. They are always enabled and you cannot edit them, but if you notice that your visitors are wrongly blocked by some of those rules, you can use the Rules Editor below to disable them individually:', 'nfwplus') . '
			<ul>
			<li>'. __('Check your firewall log and find the rule ID you want to disable (it is displayed in the <code>RULE</code> column).', 'nfwplus') . '</li>
			<li>'. __('Select its ID from the enabled rules list below and click the "Disable it" button.', 'nfwplus') . '</li>
			</ul>'.
			__('Note: if the <code>RULE</code> column from your log shows a hyphen <code>-</code> instead of a number, that means that the rule can be changed in the "Firewall Policies" page.', 'nfwplus')
	) );
}

/* ================================================================== */ // -
// EOF
?>
