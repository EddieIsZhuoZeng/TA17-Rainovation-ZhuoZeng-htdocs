=== Lana Downloads Manager ===
Contributors: lanacodes
Tags: download, download manager, file manager, download counter
Requires at least: 4.0
Tested up to: 5.4
Stable tag: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Downloadable files management system

== Description ==

Lana Downloads Manager is a downloadable files management system.

Manageable local files (WordPress uploaded) and remote files.

= Includes: =
* Counter system
* Log system

= Lana Codes =
[Lana Downloads Manager](http://lana.codes/lana-product/lana-downloads-manager/)

== Installation ==

= Requires =
* WordPress at least 4.0
* PHP at least 5.3

= Instalation steps =

1. Upload the plugin files to the `/wp-content/plugins/lana-downloads-manager` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

= How to use it =
* in `Downloads > Add New`, add a new download file and the system creates the shortcode is what you can use.
* in `Appearance > Widgets`, add the 'Lana - Download' widget to the sidebar, for example, add it to the Primary Sidebar.
* in `Posts > Edit`, you can manually add the download button to the selected post, add the `[lana_download]` shortcode to the post content, for example, add `[lana_download id="1"]` shortcode to the content.
* in `Pages > Edit`, you can manually add the download button to the selected page, add the `[lana_download]` shortcode to the page content, for example, add `[lana_download id="1"]` shortcode to the content.

== Frequently Asked Questions ==

Do you have questions or issues with Lana Downloads Manager?
Use these support channels appropriately.

= Lana Codes =
[Support](http://lana.codes/contact/)

= WordPress Forum =
[Support Forum](http://wordpress.org/support/plugin/lana-downloads-manager)

== Screenshots ==

1. screenshot-1.jpg

== Changelog ==

= 1.4.0 =
* change shortcode
* change download handler

= 1.3.0 =
* change shortcode functions
* reformat code
* bugfix widgets

= 1.2.2 =
* bugfix filters
* bugfix file update post meta

= 1.2.1 =
* add filter to post type and taxonomy args

= 1.2.0 =
* add counter disable or enable for settings

= 1.1.9 =
* bugfix settings save
* add settings errors
* add get user ip and user agent functions
* change logs list

= 1.1.8 =
* add lana_downloads_manager_before_file_download action hook

= 1.1.7 =
* add counter attr to shortcode
* add post_type to text shortcode attr
* add text attr to tinymce shortcode
* change assets structure

= 1.1.6 =
* add the missing files

= 1.1.5 =
* reformat code
* add constants
* add per page in list table
* change post type name
* change taxonomy name
* change wp role add cap function
* change ajax get lana download list function
* bugfix order query by count
* bugfix filter query by download category

= 1.1.4 =
* add text domain to plugin header

= 1.1.3 =
* Bugfix global post declaration

= 1.1.2 =
* Bugfix rewrite

= 1.1.1 =
* Tested in WordPress 4.8 (compatible)
* Change website to lana.codes

= 1.1.0 =
* Added post type endpoint
* Added endpoint validate
* Bugfix endpoint
* Bugfix post type and taxonomy

= 1.0.9 =
* Added public option to lana download post type

= 1.0.8 =
* Added text atts to lana download shortcode

= 1.0.7 =
* Added Download Category
* Added Download shortcode in WordPress editor

= 1.0.6 =
* Bugfix escape

= 1.0.5 =
* change lana download custom post type capabilities

= 1.0.4 =
* Bugfix jquery.js include

= 1.0.3 =
* PHP compatibility changes

= 1.0.2 =
* Bugfix add log function

= 1.0.1 =
* Bugfix .htaccess in /uploads/lana-downloads folder
* Bugfix download handler function

= 1.0.0 =
* Added Lana Downloads Manager
* Added Log system

== Upgrade Notice ==

= 1.4.0 =
This version update shortcode and download handler. Upgrade recommended.

= 1.3.0 =
This version fixes widget and update shortcode. Upgrade recommended.

= 1.2.2 =
This version fixes filters and update post meta. Upgrade recommended.

= 1.2.1 =
This version added filter to post type and taxonomy args. Upgrade recommended.

= 1.2.0 =
This version added counter disable or enable for settings. Upgrade recommended.

= 1.1.9 =
This version fixes settings save. Upgrade recommended.

= 1.1.8 =
This version added before download action hook. Upgrade recommended.

= 1.1.7 =
This version updated shortcode. Upgrade recommended.

= 1.1.6 =
This version added required missing files. Upgrade recommended.

= 1.1.5 =
This version changed post type and taxonomy name and bugfix queries in list. Upgrade recommended.

= 1.1.4 =
This version added text domain to the plugin header. Upgrade recommended.

= 1.1.3 =
This version fixes global post declaration bug. Update recommended.

= 1.1.2 =
This version fixes rewrite bug. Update recommended.

= 1.1.1 =
Nothing has changed in this version. Tested in WordPress 4.8 and compatible.

= 1.1.0 =
This version add post type endpoint and fixes endpoint bug. Update recommended.

= 1.0.9 =
This version add public option to lana download post type.

= 1.0.8 =
This version add text atts to lana download shortcode.

= 1.0.7 =
This version add download category and download shortcode. Upgrade recommended.

= 1.0.6 =
This version fixes escape bug. Upgrade recommended.

= 1.0.5 =
This version fixes lana download custom post type capabilities. Upgrade recommended.

= 1.0.4 =
This version fixes jQuery include bug. Upgrade recommended.

= 1.0.3 =
This version fixes PHP compatibility problem (Prior to PHP 5.5, empty() only supports variables; anything else will result in a parse error).

= 1.0.2 =
This version fixes add log to database bug. Upgrade recommended.

= 1.0.1 =
This version fixes download handler query_vars and .htaccess bug. Upgrade recommended.