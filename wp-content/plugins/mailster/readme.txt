=== Mailster - Email Newsletter Plugin for WordPress ===

Contributors: everpress  
Tags: email newsletter, newsletter, newsletter signup, email signup, email marketing  
Requires at least: 6.2  
Tested up to: 7.0  
Stable tag: 4.1.17  
License: GPLv2 or later  
Requires PHP: 7.4

== Description ==

= Mailster is an easy to use Email Newsletter Plugin for WordPress. Create, send and track your Newsletter Campaigns without hassle =

#### Send Your Latest Posts, Products, Events or any other post type

Mailster lets you send all your posts, products, events or other custom post types. Every type can also be used for email automation.

#### Welcome Your New Subscribers

Set up your welcome series and leave the rest to Mailster.

You sit back and focus on your business while Mailster makes sure your new subscribers feel welcome.

#### Free and Premium Templates

Mailster offers you a choice of more than 400 free and premium designs.

Create stunning and engaging campaigns in minutes!

#### GDPR Compliance

When it comes to GDPR compliance, Mailster is your newsletter marketing tool of choice: Mailster fully complies with GDPR requirements.

All your data is stored in your own database and is not transferred or shared with third parties.

#### Grow Without Limits

With Mailster there’s no limit in subscribers. For a one-time fee you can have as many subscribers as you want.

Go ahead and grow as much as you like!

- Unlimited Subscribers
- Unlimited Forms
- Unlimited Lists

#### Send With Any Email Provider

Mailster works with any email provider, no matter if you want to use a professional provider or your own server.

#### RSS to Email

Your subscribers will want to know if there’s new content on a third-party site they follow.

With our RSS-to-email feature, new posts are automatically imported into an email and sent to your subscribers. Just set up your automation campaign and you’re ready to go.

#### Time Zone Based Sending

You have subscribers all over the world? With Mailster you send your email campaigns based on the time zone of your subscribers.

This way you ensure that your readers receive your emails at the exact times when they are most likely to open them.

#### Know Your Subscribers

Analyze your subscribers´ data and target your marketing strategy according to their needs.
Mailster collects and uses your subscribers’ data while staying completely within GDPR requirements.

#### Merge Tags

Merge tags are the key to marketing personalization: They are placeholder tags that get replaced with content tailored to your subscribers.

Our merge tags are customizable, extendable and easy to remember.

#### Create Better Segmentations

Increase your open rates with targeted campaigns and segmentation. Mailster provides many metrics to choose from so only the right audience gets the right email.

#### Great Campaign Insights

Track and analyse your campaigns and subscribers: Benefit from real time insights into your campaigns.

Find out about your subscribers’ click rates and open rates, their location, and other activities relevant for your online marketing strategy.

#### Test Your Email Quality Before Sending

With Mailster you can give your email campaign a thorough pre-check before sending it out.
Mailster gives you feedback on what you should improve.

Fix these issues, send and relax – your campaign will be good.

#### Automation

Send automatic welcome emails, birthday emails, drip campaigns, follow-ups, auto-responders and more.

Just sit back and let Mailster take care of your email marketing.

#### You Own Your Data

All your subscribers’ data is stored in your own database.
No third party has access to that data.

#### Dozens of Integrations With Plugins

We’ve partnered with many popular developers so you can continue using your favorite plugins along with Mailster.

#### Built in Capability Management

Assign specific rights to the people in your team. This makes it easy to keep full control over who does what.

Let your team support you!

#### Custom Template Language

With Mailster’s simple and straightforward template markup language, coding your own template or adopting existing ones is easy.

#### Developer Friendly

Mailster provides plenty hooks and filters you can use to increase its functionality and options.

Just go ahead and adapt Mailster to your unique needs.

#### Features

- Capture subscribers on your website or blog.
- Send your latest posts/products/custom post types.
- Welcome Emails.
- Birthday Campaigns.
- Follow up/Drip Campaigns.
- 400 free and premium templates.
- RSS email campaigns.
- Built in email campaign precheck.
- Integration with your favorite plugins.
- Import your existing data with ease.
- Available in over 15 languages.
- GDPR Compliance.
- Send with any email provider.
- Time zone based delivery.
- Merge Tags.
- Advanced Segmentation.
- Newsletter Campaign Insights.
- Auto Responders and email marketing automation.
- Full Data Control.
- Reports.
- Public archive of your latest newsletters.
- Capability management.
- Developer friendly.

== Templates ==

= Free and Premium Templates =

Mailster supports over 400 templates from various authors. Please visit our website to browse them all.

**[Templates →](https://mailster.co/templates/)**

== Add Ons ==

= Add Ons =

Extend the functionality of Mailster or your site with these add ons.

**[Add ons →](https://mailster.co/addons/)**

== Support ==

= We’re Here to Help =

= Knowledge Base =

Find help on our knowledge base. With over 300 articles, tips and troubleshoot guides you can quickly find answers to the most common problems.

**[Visit Knowledge Base →](https://kb.mailster.co/)**

= Members Area =

We provide all of our official support via the member area. Please login or register if you have not done so yet for access so that you can post your question there.

**[Account →](https://mailster.co/account)**

= Hire an expert =

Get professional development help from our expert support partner _Codeable_ for consultations, customisations and small to medium sized projects.

**[Get a Quote →](https://codeable.io/?ref=zATDD)**

== Changelog ==

= Changelog =

= 4.1.17 =

- This update brings stability and improvements for WordPress 7.0.
- improved: block forms now support block gaps.
- improved: block form message now visible in the editor.
- improved: block form message now supports borders and shadows.
- improved: buttons on campaign edit screen for WordPress 7.0.
- improved: visual fixes for WordPress 7.0.
- improved: Quickly copy Block Markup for block forms (next to short codes).
- improved: sanitize URLs using esc_url() for improved security.
- updated: included form patterns for WordPress 7.0.
- updated: validate form ID by ensuring it is numeric before processing in frontpage class
- updated: replace ButtonGroup with Flex component in FormSelector and EmailSelector for improved layout consistency
- updated: included form patterns for WordPress 7.0.
- removed: clean lists note on import screen.
- removed: deprecated `utf8_*` methods.
- fixed: missing conditions error on manage subscribers screen.
- fixed: missing colors on forms if block messages have a non-default color.
- fixed: form section in form blocks now displays all available forms when there are more than 10.
- updated: dependencies

= 4.1.16 =

- removed: eliminate remove_template method from MailsterAjax class to streamline AJAX functionality
- fixed: broken blocks on automation by adding `sprintf` function to i18n imports for better string formatting in multiple files
- improved: ensure only the latest export file is downloadable by validating the filename against stored option
- improved: modify MIME type for zip uploads and enable type testing in MailsterAjax class

= 4.1.15 =

- fixed: correct logic in URL validation check
- fixed: check if campaign is in a valid status before checking the content
- refactored: streamline form retrieval and fallback logic in MailsterNotification class for improved readability and maintainability
- updated: bump dependencies and lock files to latest versions for improved compatibility and performance
- updated: enhance URL validation logic in MailsterFrontpage class to include campaign status checks and improve error handling
- updated: add __next40pxDefaultSize prop to various components for consistent styling across form elements

= 4.1.14 =

- fixed: synchronize max days for warmup settings between queue and delivery files
- refactored: sanitize URLs in subscribers table class for improved security
- refactored: update content filters and actions for better form rendering and modularity in block forms. Popups are now rendered inside the_content filter, which improves compatibility with more themes and core blocks, used inside the form.
- updated: bump dependencies and lock files to latest versions for improved compatibility and performance
- updated: correct typo in welcome message on campaigns dashboard

= 4.1.13 =

- added: option to continue workflow on specific date delays if the date is in the past
- fixed: css: issues with some themes on the automation editor
- updated: dependencies and lock files for improved compatibility and performance

= 4.1.12 =

- fixed: cast smtp_secure option to string for proper type handling

= 4.1.11 =

- added: implement installation checks and enhance default settings handling
- added: link to documentation for capabilities settings page
- added: new capability to delete campaigns with appropriate roles and help text
- added: new helpdesk links to the settings page
- refactored: improve notification template fallback and error handling
- refactored: improve SMTP configuration type casting
- refactored: improve wp_mail filter argument handling
- fixed: correct parent block references for condition blocks
- updated: enhance import functionality and UI interactions
- packages updates

= 4.1.10 =

- fixed: improve capabilities and permissions system
- updated Dependencies and Package Versions
- chore: update commit message guidelines
- updated: improve form block error handling
- updated: refactor homepage block edit and inspector controls

= 4.1.9 =

- fixed: missing capability to delete campaigns in some case
- fixed: missing key in array function
- added: new capabilities `delete_private_newsletters`, `delete_published_newsletters`
- improved: alignment of elements in the automation inspector
- improved: use `ToggleGroupControl` on update custom field action for checkboxes
- package upgrades

= 4.1.8 =

- fixed: bug when subscribe on multiple lists. 'wpdb->delete' may return 0 so do better checks
- removed redundant shortcode
- updated packages

= 4.1.7 =

- fixed: issue where activating caused a fatal error if `wp_mail` is used by another plugin
- fixed: typo
- updated: Freemius SDK to 2.10.1

= 4.1.6 =

- fixed: campaigns cache were not cleared on status change
- fixed: condition using "have" and "doesn't have" tags returned wrong results
- fixed: force boolean on some expressions
- added: '__nextHasNoMarginBottom' to components to prevent deprecated messages in WP 6.7
- removed: option to upgrade license from notification
- updated: dependencies for composer and npm
- Less use of constant MAILSTER_BUILT

= 4.1.5 =

- improved: admin notices improvements
- improved: show "0" values on subscriber details page instead of nothing
- improved: convert license screen
- improved: option to quickly remove filter from subscriber overview
- added: `{second}` tag to display the current second
- fixed: minor CSS issues
- fixed: PHP Notice while Autoresponder converting
- fixed: Trigger selector break blocks if connection is to slow
- fixed: cache wasn't cleared for block forms in some cases
- fixed: conditions with quotes are rendered incorrectly
- fixed: form patterns were missing on saved block forms
- fixed: race condition when a workflow is triggered on manual user update
- fixed: searchmark causes error if applied on non strings
- removed: legacy usage tracking
- rewritten notice methods
- updated: Freemius SDK to 2.9.0

= 4.1.4 =

- fixed: profile showed unsubscribe button
- fixed: workflow can be triggered multiple times in some instances
- improved: checks if Mailster methods are called before Mailster is initialized
- improved: Support for WooCommerce Add-on

= 4.1.3 =

- fixed: automation schedule generated invalid query if empty arguments were passed
- fixed: missing translations
- fixed: respect `WPLANG` property when fetching translations
- fixed: typo on dashboard
- improved: allow updating from the dashboard if translations have been loaded already
- improved: hook based triggers now support multiple or all subscribers

= 4.1.2 =

- fixed: PHP warning in automations class
- fixed: missing ID when block forms are used on the frontend
- fixed: missing campaign on email workflow
- fixed: some strings weren't translate-able improved: translations
- improved: form patterns are now translate-able
- added: link to Translator Program on dashboard

= 4.1.1 =

- fixed: PHP Error with PHP version 7.4
- fixed: Jumper Step caused broken block in some cases
- fixed: Step Appender not aligned correctly

= 4.1.0 =

- new: Jumper Step: Jump to a specific step in the workflow.
- new: Notifications Step: Send a notification to an email address.
- new: Allow Prefills forms with URL parameters.
- new: Queued subscribers on workflows can now get inspected and removed/forwarded/finished.
- new: Option to skip steps in workflows.
- new: Option to allow pending Subscribers entering the workflow.
- new: Option to activate/deactivate and duplicate the workflow from the overview page.
- new: Capabilities to manage workflows and block forms.
- new: Shortcode of forms is now quickly copyable from the forms overview page.
- added: option to selected autocomplete type for input elements on forms.
- improved: initiation Mailster. Now the main class is loaded when all plugins are available.
- improved: Newsletter Homepage in the Site editor is not broken anymore.
- improved: frontend script for forms is executed with domReady.
- improved: BlockRecovery now uses less resources.
- improved: refetching data is now less resource intensive.
- improved: missing step appender in WP 6.6 is now back (and better).
- improved: new icons for some steps to make them more distinguishable.
- improved: time related input fields got a "now" button to quickly jump to the current time.
- improved: more option buttons in editor toolbar for better accessibility.
- improved: workflows can now run max 1024 steps in a single process to prevent infinite loops.
- change: Canvas toolbar is now located in the canvas.
- change: `wp_mail` setup: changed the way functions are loaded

= 4.0.11 =

- fixed: PHP error "attempt to assign a property to an array"
- fixed: styling issues on Automations with WP 6.6
- fixed: missing Canvas tools on WP 6.6
- fixed: workflow steps with delay didn't calculated the timezone correctly.

= 4.0.10 =

- fixed: escaping on web version
- fixed: subject placeholder tag in title was not replaced on the webversion
- improved: css improvements
- update plugin api info

= 4.0.9 =

- fixed: line height on form selector input field
- fixed: PHP Warning: preg_replace(): Compilation failed with certain shortcodes.
- fixed: PHP deprecated notice
- fixed: admin header was visible on the newsletter homepage
- fixed: deeplink of steps works again
- fixed: dynamic properties are deprecated
- fixed: fatal error on tags triggers if no tag has been set
- fixed: logos were not applied to some templates
- fixed: translation wasn't loaded before the setup wizard
- improved: bounce performances

= 4.0.8 =

- fixed: linked images in templates weren't mapped correctly.
- fixed: missing array index in options.php
- fixed: using "given-name" for autocomplete value instead of "name" on the first name field for block forms
- improved: legacy forms inclusion
- improved: activation experience for Envato users
- improved: bounce handler to not timeout during processing of large quantities of bounce messages
- removed: beta notice
- removed: health check notice.
- reverted: forcing unsubscribe link on the bottom of the mail if not present in the email.
- fully tested with WordPress 6.5

= 4.0.7 =

- fixed: action links on campaign overview page.
- fixed: linked images in templates weren't mapped correctly.
- fixed: missing array index in options.php
- improved: legacy forms inclusion
- improved: bounce handler to not timeout during processing of large quantities of bounce messages
- removed: deprecated cron trigger

= 4.0.6 =

- fixed: Custom fields with underline were not working
- fixed: check for array on bulk actions on campaigns
- fixed: embeded elements were broken caused by block forms
- fixed: wrong link in the block form overview
- improved: added option to define 'fields' in search query on the subscribers overview to search only within certain fields
- improved: deleted subscribers no longer count to totals on overview page
- improved: loading of lists page with large subscriber base.

= 4.0.5 =

- fixed: SQL error on form preview page
- improved: loading time on subscribers overview

= 4.0.4 =

- fixed: journey were triggered more than once in some circumstances
- fixed: usage of `wp_posts` instead of `{$wpdb->posts}` on some queries
- improved: fast triggers in automations
- improved: cleanup on date related workflows

= 4.0.3 =

- added: upgrade notice
- fixed: small bug fixes and CSS improvements

= 4.0.2 =

- fixed: custom field creation during import if no custom fields are present
- fixed: render error if first step in workflow is removed
- improved: handling different translations for different users
- updated: included templates

= 4.0.1 =

- fixed: some phpcbf issues
- fixed: Newsletter homepage creation causes broken blocks
- fixed: checkout links fixed: typo
- fixed: some automations got stuck on the delay step under certain conditions

= 4.0.0 =

- **Email Health Check**: Check your delivery method to comply with upcoming changes to Gmail and Yahoo.
- **Block Forms**: Create forms and popups with the built-in editor.
- **Automations**: Bring automations to the next stage with customer journeys.
- **One Click Unsubscribe**: Now enabled by default for Gmail and Yahoo, complying with RFC 8058.
- **Save Custom Modules**: Allows for saving and reusing custom modules within the platform.
- **Native Support**:
- For DIVI.
- Improved native support forms for Elementor, including a change in the slug of the Elementor module to prevent conflicts.
- **General**:
- Welcome Wizard.
- Automation triggers now run on the same process.
- List view labels are more descriptive.
- Newsletter homepage block.
- Standardized rendering of campaigns.
- Performance improvements on querying action data from campaigns and on data in the wp_options table.
- Rendered of admin screens.
- Subscribers detail page.
- Allow any type of hook in hook triggers.
- **Forms and Popups**:
- Block form preview.
- Animations on forms are now reduced if clients use "prefer-reduced-motion".
- Honeypot mechanism to prevent false positives on heavily cached sites and improved to prevent false positives more broadly.
- **Compatibility and Standards**:
- PHP 8.2 compatibility.
- Updated WP Coding Standards to 3.0.
- Fully tested for PHP 8.1 and partially tested with PHP 8.2.
- **UI/UX**:
- Improved preview of forms from the block form overview.
- Improved rewrite rules for newsletter homepage.
- Improved placeholder image tags algorithm.
- Improved beacon message loader.
- CSS improvements on the form editor.
- General style updates.
- **Technical and Structural**:
- Page link triggers are now stored differently.
- No longer use trigger post meta value for automation triggers.
- Moved form padding to style section.
- Hide "Show form" option if used in content.
- **Deprecated**:
- Legacy forms.
- **SDKs and Libraries**:
- Freemius SDK updated to 2.6.2.
- **Geo API**: Updated to use preferred single mmdb file instead of multiple data files.
- **Admin Screens**: Improved rendering.
- **General Fixes**:
- Deprecated notice on subscribers detail page for PHP 8.2+.
- Warning on activation if update state is not clear.
- Warnings when trying to resize SVG images in email.
- Custom fields were not saved in some cases.
- Warning on PHP 8.2 if subscriber count is 0.
- CodeMirror editor not responding.
- `mailster_ip2Country` didn't return country codes.
- Forms popup appear in wrong places.
- Conditions were sometimes wrongly not fulfilled.
- Automations don't get triggered due to a wrong db column.
- Inline style attribute got removed in some edge cases.
- Scroll percentage trigger was not working.
- Align property hasn't been stored if forms are used out of context.
- Prevent step ID be the same if multiple blocks are duplicated.
- **Premium Templates**: Now available to certain plans.
- **Specific Date and Anniversary Triggers**: Can now have an offset.
- **Support and Cleanup**:
- Improved log cleanup algorithm.
- New filter 'mailster_cron_simple_output' to change the output of the cron page.
- Run KSES filter on form output via shortcode.
- Smaller fixes and improvements, cleanup.
- Support block form preview in the site editor.
- **Miscellaneous**:
- Option to convert certain autoresponders to workflows.
- Legacy forms menu entry is hidden by default.

**For further details please visit our change log page.**

**[Mailster Homepage →](https://mailster.co/changelog/)**
