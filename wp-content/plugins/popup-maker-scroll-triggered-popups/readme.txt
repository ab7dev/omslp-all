=== Popup Maker - Scroll Triggered Popups ===
Contributors: danieliser, wppopupmaker
Author URI: https://wppopupmaker.com/
Plugin URI: https://wppopupmaker.com/extensions/scroll-triggered-popups/
Tags: 
Requires at least: 3.6
Tested up to: 4.9.5
Stable tag: 1.3.2

Use this extension and market your valuable content as users scroll down your pages - great for increasing revenue and funneling users throughout your site.

== Description ==

Use this extension and market your valuable content as users scroll down your pages - great for increasing revenue and funneling users throughout your site.

== Changelog ==

= v1.3.2 - 08/24/2018 =
* Fix: Corrected activation routine issues that could prevent version number storage.

= v1.3.1 - 06/15/2018 =
* Tweak: Made changes to respect new setting to disable asset caching.

= v1.3.0 - 05/01/2018 =
* Feature: Added ability to change what part of the trigger element will be visible when the popup triggers.
* Improvement: Relabeled and reworked existing settings to be more intuitive.
* Improvement: Updated for full Popup Maker v1.7 support.
  * Leveraged the new AssetCache reducing the need to load an extra JS file for this extension.
  * Autoloader
  * Upgrade routines.
* Tweak: Replaced the scroll_trigger shortcode with a namespaced version [pum_scroll_trigger]
* Tweak: Removed Scroll Trigger shortcode button from popup post type.

= v1.2.3 - 08/10/2017 =
* Refactored for better trigger position accuracy.
* Refactroed to allow for creating something similar to a floating nav bar that opens and closes continuously.

= v1.2.2 - 04/23/2017 =
* Improvement: Added check for advanced conditions when triggering a popup.

= v1.2.1 - 04/30/2016 =
* Bug: Fixed incorrect JS template tags.

= v1.2 - 03/22/2016 =
* Feature: Added (v1.4) trigger **Scroll**.
* Improvement: Migrated code to new PUM boilerplate v2.
* Developer: Added automated build routines to eliminate build time errors making it to releases.

= v1.1.2 =
* Feature: Trigger Point setting. Allows you to set whether the trigger fires at the top or bottom of the screen or a ratio based position for %.
* Improvement: Improved the scroll trigger accuracy, especially for % based distances. You can now set it to 100% which will trigger just as the user scrolls to the very bottom.
* Improvement: Minor performance improvements in the JavaScript.
* Fix: Bugged % based scroll when position set above a certain level.

= v1.1.1 =
* Fixed bug where cookie was set and popup still opened.
* Set scripts to only load when necessary.
* Added scroll-triggered class to enabled popups.

= v1.1.0 =
* Rewritten to use the PM Boilerplate.
* Added POT file for translations.
* Added option to close popup when user scrolls back up
* Added new trigger "Element" allowing user to specify an element by CSS / jQuery Selector which when scrolled on screen will trigger the popup.

= v1.0.1 =
* Version Change for Launch

= v1.0 =
* Initial Release