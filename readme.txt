=== WP Shortcode Control ===
Author URI: https://rarus.io
Plugin URI: https://rarus.io/wp-shortcode-control
Contributors: rarusteam
Donate link: https://rarus.io/donate/
Tags: shortcode, control, do_shortcode_tag, pre_do_shortcode_tag, wp shortcode control, manage shortcode, shortcodes
Requires at least: 4.7
Tested up to: 5.2.4
Stable Tag: 1.0.2
License: GNU Version 2 or Any Later Version

The easiest way to manage and manipulate your shortcodes.

== Description ==

WP Shortcode Control allows you to manage and manipulate the output of your shortcodes. You can easily replace the whole content, disable it completely, change just partial stuff, add custom and/or default shortcode attributes and much, much more.
Our plugin works with every kind of shortcodes. 

= Features =

* Activate/Deactivate shortcodes
* Filter every single shortcode content
* Add custom capabilities to every shortcode
* Add custom and/or default attributes to shortcodes
* Import/Expport for Shortcodes
* Completely performance optimized plugin structure
* Fully translateable and compatible with WPML
* Optimized debugging system
* Developer optimized code

= For devs =

We offer a super extendable development structure for our WP Shortcode Control plugin to optimize the code for your needs.

== Installation ==

1. Activate the plugin
2. Go to WP Shortcode Control under the "tool" menu item and start right away
3. We offer you there a tour of the plugin to make your start as smooth as possible.

== Frequently Asked Questions ==

= Is there a documentation available? =

We are currently creating a documentation for WP Shortcode Control. For more informations, you can visit [https://rarus.io/wp-shortcode-control](https://rarus.io/wp-shortcode-control?utm_source=docs-wpsc&utm_medium=installation_tab&utm_content=documentation&utm_campaign=readme)

= What should I do if a shortcode is not in the list? =

To solve this problem, we have two possibilities: If you know on which site the shortcode gets called, you can use our crawl function to find it on the page (available for frontend and backend). The second way would be to add the shortcode manually by using our add shortcode button. More informations on that are in our documentation.

== Screenshots ==

1. The WP Shortcode Control panel.
2. The settings tab of WP Shortcode Control

== Changelog ==

= 1.0.2: August 10, 2018 =
* Fix: Fix issue with wrong focus of integer select field in offset feature
* Tweak: Optimize PHPDocs


= 1.0.1: August 10, 2018 =
* Feature: - You can now add custom shortcode attributes as default or new values to your shortcodes
* Tweak: Moved the WP Shortcode Control Admin Menu Item under Tools -> WP Shortcode Control
* Tweak: Optimized PHPDocs
* Fix: Fixed issue with not working post include/exclude function
* Fix: Fixed case sensitive order of shortcodes
* Dev: New hook (filter) "wpscont/shortcodes/features/list/filter_attributes" available to filter the list of custom applied attributes.
* Dev: New hook (filter) "wpscont/shortcodes/features/filter_attributes" available to filter custom shortcode attributes.

= 1.0.0: July 27, 2018 =
* Birthday of WP Shortcode Control
