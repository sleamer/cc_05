=== WP Spry Menu ===
Contributors: takien
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=BL7ERUY46HPL8&lc=ID&item_name=WP%20Spry%20Menu%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: menu, css menu, dropdown menu, category menu, category exclude, horizontal menu, vertical menu
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 1.5.2

Create Spry Drop Down Menu for WordPress category.

== Description ==

WP Spry Menu is plugin that automatically creating Spry Drop Down Menu for WordPress category. What is Spry? Spry is JavaScript dropdown menu library from www.adobe.com. The Spry library is FREE and redistributable.

Feature:

* For new feature, see 'Changelog'.
* No template arguments are needed to configure menu behavior, all settings are place on the Admin.
* Live preview as soon as you change the settings. No need to refresh your homepage to see changes.
* Configurable direction, your menu should appears horizontal, vertical drop left, or vertical drop right.
* Changeable Home text link, you can leave it blank for no home link.
* Depth setting, how many child you want to display.
* Exclue setting, you can select which category should not appears on the menu.
* Order setting, order by name or ID.
* Hide/Hide emtpy category. By default Wordpress won't display an empty category, now you can configure.
* Child of setting, only display menu from selected parent category.


== Installation ==

The installation is very easy.

1. Upload `wp-spry-menu` folder to the `/wp-content/plugins/` directory. Make sure wp-spry-menu contains all included files.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Place `<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu();?>` in your templates or use shortcode `[wp_spry_menu /]` in your post/page or Text Widget.
4. Cofigure from WP Spry Menu options in your Wordpress Admin.

== Frequently Asked Questions ==

= no =


== Screenshots ==

1. Setting page with softbule theme selected.


== Changelog ==

= 1.5.2 =
* Fixed bug, admin CSS loaded on front page.
* Fixed typo in readme.txt

= 1.5.1 =
* Fix function error on PHP <  5.3.0

= 1.5 =
* Code rewritten, because I know the old code is very funny. :D
* Now use single option, wp_spry_menu_settings ( don't worry, old setting will be imported when you upgrade to this version).
* Add shortcode [wp_spry_menu] for easy installation in post/page or Text Widget
* Removed support for IE browser.
* Removed Style Editor.
* Plugin Settings now moved under Appearance on the WordPress dashboard.

= 1.0.3 =
* Add Theme chooser, while Style Editor can be enable/disable.
* Add Order By options: count, slug, term_group
* Add Category Title.
* Fixed bug, white border in IE.

= 1.0.2 =
* Fixed bug, wrong path.
* Only add_filter admin_head if you are in option page.
* Add Simple Style Editor with Color Picker.


= 1.0.1 =
* Add Child of as dropdown select.
* Live preview from plugin setting page.
* Code improvement

= 1.0.0 =

* Add admin settings
* No need to add template argument, all go to setting area.
* Add Vertical Drop Left and Vertical Drop Right

= 0.0.1 =

* Only display Horizontal style
* first release 

