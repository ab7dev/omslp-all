=== NinjaFirewall (WP+ Edition) ===
Contributors: nintechnet
Tags: security, firewall, malware, virus, protection
Requires at least: 4.9
Tested up to: 7.0
Stable tag: 4.8.5
Requires PHP: 7.1
License: Modification and distribution of this software require express written permission from the author ~ (c) NinTechNet Limited

A true Web Application Firewall to protect and secure WordPress.

== Description ==

= A true Web Application Firewall =

NinjaFirewall (WP+ Edition) is a true Web Application Firewall. Although it can be installed and configured just like a plugin, it is a stand-alone firewall that stands in front of WordPress.

It allows any blog administrator to benefit from very advanced and powerful security features that usually aren't available at the WordPress level, but only in security applications such as the Apache [ModSecurity](http://www.modsecurity.org/ "") module or the PHP [Suhosin](http://suhosin.org/ "") extension.

> NinjaFirewall requires at least PHP 7.1, MySQLi extension and is only compatible with Unix-like OS (Linux, BSD). It is **not compatible with Microsoft Windows**.

NinjaFirewall can hook, scan, sanitise or reject any HTTP/HTTPS request sent to a PHP script before it reaches WordPress or any of its plugins. All scripts located inside the blog installation directories and sub-directories will be protected, including those that aren't part of the WordPress package. Even encoded PHP scripts, hackers shell scripts and backdoors will be filtered by NinjaFirewall.

= Powerful filtering engine =

NinjaFirewall includes **the most powerful filtering engine available in a WordPress plugin**. Its most important feature is its ability to normalize and transform data from incoming HTTP requests which allows it to detect Web Application Firewall evasion techniques and obfuscation tactics used by hackers, as well as to support and decode a large set of encodings. See our blog for a full description: [An introduction to NinjaFirewall filtering engine](https://blog.nintechnet.com/introduction-to-ninjafirewall-filtering-engine/ "").

= Features =

* Full standalone web application firewall. Works before WordPress is loaded.
* Powerful filtering engine.
* Supports a large set of encodings.
* Detects and blocks obfuscated code and evasion techniques used to bypass Web Application Firewalls.
* Protects against remote file inclusion, local file inclusion, cross-site scripting, code execution, SQL injections, brute-force scanners, shell scripts, backdoors etc.
* Scans and sanitises GET/POST requests, HTTP/HTTPS traffic, cookies, server variables (`HTTP_USER_AGENT`, `HTTP_REFERER`, `PHP_SELF`, `PATH_TRANSLATED`, `PATH_INFO`) and raw POST data.
* Sanitises variables names and values.
* Privilege escalation protection.
* Advanced filtering options to block ASCII control characters, NULL bytes and PHP built-in wrappers.
* Decodes and scans Base64-encoded POST requests to detect backdoors and code injection attempts.
* Hooks and secures HTTP reponse headers to prevent XSS, phishing and clickjacking attempts (`X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Strict-Transport-Security`, `Content-Security-Policy`).
* Hooks and modifies cookies to set the `HttpOnly` flag.
* Blocks username enumeration scanning attempts through the author archives and the login page.
* Blocks/allows uploads, sanitises uploaded file names.
* Blocks suspicious bots and scanners.
* Hides PHP error and notice messages.
* Blocks direct access to PHP scripts located inside specific directories (e.g., `/wp-content/uploads/`).
* Protects WordPress XML-RPC API.
* Whitelist option for WordPress administrator(s), localhost and private IP address spaces.
* Configurable HTTP return code and message.
* Rules editor to enable/disable built-in security rules.
* Activity log and statistics.
* Debugging mode.
* And many more...

= Fastest and most efficient brute-force attack protection for WordPress =

By processing incoming HTTP requests before your blog and any of its plugins, NinjaFirewall is the only plugin for WordPress able to protect it against very large brute-force attacks, including distributed attacks coming from several thousands of different IPs.

See our benchmarks and stress-tests: [Brute-force attack detection plugins comparison](https://blog.nintechnet.com/wordpress-brute-force-attack-detection-plugins-comparison-2015/ "")

The protection applies to the `wp-login.php` script but can be extended to the `xmlrpc.php` one. The incident can also be written to the server `AUTH` log, which can be useful to the system administrator for monitoring purposes or banning IPs at the server level (e.g., Fail2ban).

= Real-time detection =

**File Guard** real-time detection is a totally unique feature provided by NinjaFirewall: it can detect, in real-time, any access to a PHP file that was recently modified or created, and alert you about this. If a hacker uploaded a shell script to your site (or injected a backdoor into an already existing file) and tried to directly access that file using his browser or a script, NinjaFirewall would hook the HTTP request and immediately detect that the file was recently modified or created. It would send you an alert with all details (script name, IP, request, date and time).

= File integrity monitoring  =

**File Check** lets you perform file integrity monitoring by scanning your website hourly, twicedaily or daily. Any modification made to a file will be detected: file content, file permissions, file ownership, timestamp as well as file creation and deletion.

= Watch your website traffic in real time =

**Live Log** lets you watch your website traffic in real time. It displays connections in a format similar to the one used by the `tail -f` Unix command. Because it communicates directly with the firewall, i.e., without loading WordPress, **Live Log** is fast, lightweight and it will not affect your server load, even if you set its refresh rate to the lowest value.

= Event Notifications =

NinjaFirewall can alert you by email on specific events triggered within your blog. Some of those alerts are enabled by default and it is highly recommended to keep them enabled. It is not unusual for a hacker, after breaking into your WordPress admin console, to install or just to upload a backdoored plugin or theme in order to take full control of your website. NinjaFirewall can also [attach a PHP backtrace](https://blog.nintechnet.com/ninjafirewall-wp-edition-adds-php-backtrace-to-email-notifications/ "NinjaFirewall adds PHP backtrace to email notifications") to important notifications.

Monitored events:

* Administrator login.
* Modification of any administrator account in the database.
* Plugins upload, installation, (de)activation, update, deletion.
* Themes upload, installation, activation, deletion.
* WordPress update.
* Pending security update in your plugins and themes.

= Stay protected against the latest WordPress security vulnerabilities =

To get the most efficient protection, NinjaFirewall can automatically update its security rules daily, twice daily or even hourly. Each time a new vulnerability is found in WordPress or one of its plugins/themes, a new set of security rules will be made available to protect your blog immediately.

= Shared Memory use =

Although NinjaFirewall is already much faster than other WordPress plugins, the WP+ Edition brings its performance to a whole new level by using Unix shared memory in order to speed things up even more. This allows easier and faster inter-process communication between the firewall and the plugin part of NinjaFirewall and, because its data and configuration are stored in shared memory segments, the firewall does not need to connect to the database any longer. This dramatically increases the processing speed (there is nothing faster than RAM), prevents blocking I/O and MySQL slow queries, specially on a very busy server like a multi-site network for instance. On average, the firewall processing speed will increase from 25% to 30%. Resource-intensive options like NinjaFirewall's Web Filter can get a 50% speed boost.

= Access Control =

Access Control is a powerful set of directives that can be used to allow or restrict access to your blog, depending on the User Role, IP, Geolocation, Requested URL, User-agent and visitors behavior (Rate Limiting). Those directives will be processed before the Firewall Policies and NinjaFirewall's built-in security rules. You can enable/disable firewall logging (Log event checkbox) for each access control directive separately.

= Web Filter =

If NinjaFirewall can hook and scan incoming requests, the WP+ Edition can also hook the response body (i.e., the output of the HTML page right before it is sent to your visitors browser) and search it for some specific keywords. Such a filter can be useful to detect hacking or malware patterns injected into your HTML page (text strings, spam links, malicious JavaScript code), hackers shell script, redirections and even errors (PHP/MySQL errors). Some suggested keywords as well as a default list are included.
In the case of a positive detection, NinjaFirewall will not block the response body but will send you an alert by email. It can even attach the whole HTML source of the page for your review.

= Centralized Logging =

NinjaFirewall (WP+ Edition) lets you remotely access the firewall log of all your NinjaFirewall protected websites from one single installation, using the [Centralized Logging](https://blog.nintechnet.com/centralized-logging-with-ninjafirewall/ "") feature. You do not need any longer to log in to individual servers to analyse your log data. There is no limit to the number of websites you can connect to, and they can be running any edition of NinjaFirewall: WP, WP+, Pro or Pro+.

= Antispam =

NinjaFirewall (WP+ Edition) can protect your blog comment and registration forms against spam. The protection is totally transparent to your visitors and does not require any interaction: no CAPTCHA, no math puzzles or trivia questions. Extremely easy to activate, but powerful enough to make spam bots life as miserable as possible.

= IPv6 compatibility =

IPv6 compatibility is a mandatory feature for a security plugin: if it supports only IPv4, **hackers can easily bypass the plugin by using an IPv6**. NinjaFirewall natively supports IPv4 and IPv6 protocols, for both public and private addresses.

= Multi-site support =

NinjaFirewall is multi-site compatible. It will protect all sites from your network and its configuration interface will be accessible only to the Super Admin from the network main site.

= Possibility to prepend your own PHP code to the firewall =

You can prepend your own PHP code to the firewall with the help of an [optional user configuration file](https://blog.nintechnet.com/ninjafirewall-wp-edition-the-htninja-configuration-file/). It will be processed **before WordPress and all its plugins are loaded**. This is a very powerful feature, and there is almost no limit to what you can do: add your own security rules, manipulate HTTP requests, variables etc.

= Low Footprint Firewall =

NinjaFirewall is very fast, optimised, compact, and requires very low system resource.
See for yourself: download and install [Query Monitor](https://wordpress.org/plugins/query-monitor/ "") and [Xdebug Profiler](https://xdebug.org/ "") and compare NinjaFirewall performances with other security plugins.

= Non-Intrusive User Interface =

NinjaFirewall looks and feels like a built-in WordPress feature. It does not contain intrusive banners, warnings or flashy colors. It uses the WordPress simple and clean interface and is also smartphone-friendly.

= Contextual Help =

Each NinjaFirewall menu page has a contextual help screen with useful information about how to use and configure it.
If you need help, click on the *Help* menu tab located in the upper right corner of each page in your admin panel.

= Strong Privacy =

Unlike a Cloud Web Application Firewall, or Cloud WAF, NinjaFirewall works and filters the traffic on your own server and infrastructure. That means that your sensitive data (contact form messages, customers credit card number, login credentials etc) remains on your server and is not routed through a third-party company's servers, which could pose unnecessary risks (e.g., decryption of your HTTPS traffic in order to inspect it, employees accessing your data or logs in plain text, theft of private information, man-in-the-middle attack etc).

= Supported Languages =

* English
* French

= Requirements =

* WordPress 4.9+
* Admin/Superadmin with `manage_options` + `unfiltered_html capabilities`.
* MySQLi extension
* Apache / Nginx / LiteSpeed / Openlitespeed
* Unix-like OS (Linux, BSD) only. NinjaFirewall is **NOT** compatible with Microsoft Windows.

NinjaFirewall includes GeoLite data created by MaxMind, available from http://www.maxmind.com/

== Installation ==

1. Upload `nfwplus` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Plugin settings are located in 'NinjaFirewall+' menu.

= 4.8.5 =

* Behind-the-scenes update: All the code dealing with exporting, importing and restoring the configuration was rewritten.
* WP+ Edition (Premium): You can import/export the configuration from WP-CLI. See `$ wp ninjafirewall help` for usage.

= 4.8.4 =

* Behind-the-scenes update: All the code dealing with the firewall log and IP address checks has been rewritten, bugs were resolved and several small fixes were added to make NinjaFirewall better.
* WP+ Edition (Premium): Updated IP location databases.

= 4.8.3 =

* When running in "Full WAF mode", NinjaFirewall will automatically detect if the SQL database is listening on IPv6.
* WP+ Edition (Premium): Updated IP location databases.
* WP+ Edition (Premium): updated the whitelist of webhook IP addresses of payment processors.
* Updated Chartjs library.
* Small fixes and adjustments.

= 4.8.2 =

* Fixed a "No such file or directory" PHP error due to a wrong path to the session folder (props @gosuccess).


= 4.8.1 =

* All active installations will now use NinjaFirewall sessions instead of PHP's.
* Added compatibility with PHP 8.5, which will be released in a few weeks.

= 4.8 =

* All new installations will now use NinjaFirewall sessions instead of PHP's. Current installations will automatically migrate at a later time (next release or so).
* Whitelisted reCAPTCHA response to prevent it from being blocked by the firewall.
* WP+ Edition (Premium): Updated the IP location databases.
* WP+ Edition (Premium): Added the PROPFIND method to the "Access Control > HTTP Methods" section. By default, it is not enabled.
* Updated Charts.js library.
* Added the "autofocus" attribute to the login protection form.
* Fixed a potential database issue: since PHP 8.1 MySQLi extension throws an Exception on errors (props @m2e47).
* Small fixes and adjustments.

= 4.7.5 =

* Several small fixes and adjustments under the hood.
* WP+ Edition (Premium): The firewall log can now be sorted in ascending (oldest entries first) or descending (newest entries first) order. See "NinjaFirewall > Logs > Log Options > Sorting".
* WP+ Edition (Premium): Added Square and Airwallex webhook IP addresses to the "Access Control > IP address > External Services" section.
* WP+ Edition (Premium): When saving an IP address to the whitelist or blacklist in the "IP Access Control" settings page, NinjaFirewall will reject non-conform CIDR values and display a warning.
* WP+ Edition (Premium): Updated geolocation database.

= 4.7.4 =

* Fixed a bug where, in some cases, NinjaFirewall's email notifications were not sent to all recipients but only to the first one in the list.
* Updated Charts.js.
* WP+ Edition (Premium): Updated geolocation databases.
* Small fixes and adjustments.

== Changelog ==

= 4.7.3 =

* Fixed the "[NinjaFirewall]" subject tag line that was missing in all email notifications.

= 4.7.2 =

* The email notification system was fully rewritten. You can now customize the subject and body of each email sent by NinjaFirewall. See our blog for more info about that: https://nin.link/nfmail
* Fixed a PHP "Uncaught Error: Undefined constant NF_PG_SIGNATURE" error.
* Fixed a PHP "ctype_digit(): Argument of type int will be interpreted as string in the future" notice.
* Fixed a PHP "Undefined array key REMOTE_ADDR" warning that could be returned by some command line cron jobs.
* Fixed a critical error with the saved "Custom HTTP headers" field on servers that supports HTTP/3.
* WP+ Edition (Premium): Added a check to the firewall so that if the plugin configuration is corrupted, the file size check will be skipped to prevent blocking uploads.
* Fixed some typos.
* Updated Charts.js.
* WP+ Edition (Premium): Updated GeoIP databases.
* Many small fixes and adjustments.

= 4.7 =

* This new version introduces NinjaFirewall sessions, an alternative to PHP sessions. They are an hybrid of PHP sessions and object caching, without session blocking. If you want to switch between PHP sessions and NinjaFirewall sessions, go to "NinjaFirewall > Dashboard" and follow the instructions.
* Fixed a "Undefined constant NFW_RULES" fatal error when migrating NinjaFirewall to another host.
* We have a new API (updates, security rules etc): api.nintechnet.com. Make sure to whitelist this subdomain if you are filtering outgoing connections.
* Updated Charts.js.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.6.1 =

* WP+ Edition (Premium): You can now enter your license key from WP CLI. Type "wp ninjafirewall license" and enter your license at the prompt.
* Fixed an issue with bulk user deletion: when multiple users were deleted at once, only the first one was written to the firewall log.
* Fixed an issue with the login protection: after disabling it and logging out, NinjaFirewall was still displaying a notice on the login page.
* Fixed a potential PHP fatal error: Attempt to modify property "no_update" on bool.
* Replaced all calls to the PHP glob() function with DirectoryIterator() to make file search compatible with remote files.
* Fixed an issue where some scheduled tasks were executed too often on multisite installations.
* WP+ Edition (Premium): Updated GeoIP databases.
* Updated Charts.js.
* Many additional small fixes and adjustments.

= 4.5.11 =

* Updated Charts.js.
* WP+ Edition (Premium): updated PayPal IPN and Automattic IP addresses.
* WP+ Edition (Premium): Updated GeoIP databases.
* Small fixes and adjustments.

= 4.5.10 =

* Added compatibility with blogs that don't have a database prefix.
* In the "Custom HTTP headers" section, NinjaFirewall will automatically convert header names to lowercase.
* Fixed a potential "Timezone ID is invalid" PHP notice when viewing the log.
* Updated Charts.js library.
* WP+ Edition (Premium): Updated GeoIP databases.
* Small fixes and adjustments.

= 4.5.9 =

* Added a new policy to protect against user accounts deletion. It can be found in the "Firewall Policies > WordPress > Permissions" section.
* Fixed an issue with the firewall log where the time and date could be using the wrong timezone.
* Fixed a PHP deprecated notice in the sodium_crypto_generichash function.
* WP+ Edition (Premium): Fixed a bug in the firewall where some uploaded images could be wrongly blocked.
* Updated Charts.js library.
* Small fixes and adjustments.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.5.8 =

* Added a "Line wrapping" checkbox in the "Live Log" page: it can be used to wrap or unwrap the lines in the textarea field.
* Updated Charts.js library.
* Small fixes and adjustments.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.5.7 =

* You can now select to block access to the REST API only if the user is not authenticated. See "Firewall Policies > WordPress REST API > Allow logged-in users to access the API".
* Fixed an accessibility issue with the toggle switches used in NinjaFirewall's settings. They were not compatible with screen readers.
* Added a new constant that can be used to change the frequency used by the firewall to monitor the database: `NFW_DBCHECK_INTERVAL`. It can be added to the wp-config.php or .htninja script. For instance, a 300-second interval: `define('NFW_DBCHECK_INTERVAL', 300);`. The lowest possible value, which is also the default, is 60 seconds.
* Small fixes and adjustments.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.5.6 =

* WP+ Edition (Premium): Updated GeoIP databases.
* Updated Charts.js library.
* Small fixes and adjustments.

= 4.5.5 =

* NinjaFirewall will always rely on the timezone that was set by WordPress and PHP, and will no longer attempt to set it.
* Updated Charts.js library.
* Small fixes and adjustments.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.5.4 =

* Fixed a potential "syntax error" on sites running PHP <=7.2.
* Fixed a bug where quotes in "Custom HTTP headers" values were escaped with slashes.
* Updated Charts.js library.
* WP+ Edition (Premium): Updated GeoIP databases.
* Small fixes and adjustments.

= 4.5.2 =

* Fixed several deprecated messages on websites running PHP 8.1.
* Updated Charts.js library.
* Small fixes and adjustments.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.5.1 =

* Fixed a PHP "Cannot use object of type WP_Error as array" error.
* Activating/deactivating NinjaFirewall from WP CLI doesn't require the `--user` parameter anymore.
* On websites running PHP 7.3 or above, NinjaFirewall will use the hrtime() function instead of microtime() for its metrics, because it is more reliable as it is not based on the internal system clock.
* WP+ Edition (Premium): Fixed a bug with right-to-left (RTL) WordPress sites where the checkboxes below the log were all messed up.
* The detection of base64-encoded injection has been slightly tweaked to lower the risk of false positives.
* WP+ Edition (Premium): The Bot Access Control input now accepts the following 6 additional characters: `( ) , ; ' "`.
* The "Monthly Statistics" graph and tooltip colours were improved.
* Updated Charts.js library.
* Small fixes and adjustments.
* WP+ Edition (Premium): Updated GeoIP databases.

= 4.5 =

* Added the possibility to enter custom HTTP response headers. See "Firewall Policies > Advanced Policies > HTTP response headers > Custom HTTP headers".
* Added the possibility to view the server's HTTP response headers. Click on the "Firewall Policies > Advanced Policies > HTTP response headers > HTTP headers test" button.
* Added a warning if WordPress is running inside a Docker image and the user wants to upgrade NinjaFirewall to Full WAF mode.
* Fixed a PHP "Undefined array key pluginzip" warning when reinstalling a plugin from a ZIP archive.
* WP+ Edition (Premium): The Access Control URI whitelist and blacklist now support permalinks.
* Fixed an issue where the daily report could be sent multiple times on some multisite installations.
* Fixed deprecated readonly() function message on WordPress 5.9.
* Fixed an issue where the firewall would wrongly send a WordPress update notification.
* WP+ Edition (Premium): Updated Stripes webhook notifications IP addresses in the Access Control section.
* Updated Charts.js library.
* WP+ Edition (Premium): Updated GeoIP databases.
* Many small fixes and adjustments.
