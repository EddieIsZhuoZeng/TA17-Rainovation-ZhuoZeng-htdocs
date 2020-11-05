=== Clone ===
Contributors: wpacademy, migrate, nick843
Donate link: https://sellcodes.com/q1OGuSox
Tags: migrate, clone, backup, migration, backups, copy, restore, recover, restoration, duplicate
Author URI: http://wpacademy.com
Requires PHP: 5.5
Requires at least: 3.3
Tested up to: 5.5
Stable tag: 2.2.10
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html


100% FREE clone and migration 

== Description ==

= WP Clone is back! =

Or, to be precise: we **want** it to come back!

After 3 years of neglection we believe it's time for a revival. New contributors got added to the plugin, and with it comes new motivation to make it the best backup solution on the market! 

By that, we mean: 

- Finally make it work in all cases
- Add more features, but **keep the simplicity**
- Get user trust back by delivering a top service!

But to achieve that… 

= We need your help! =

Good development takes time & money, and we need your support. 

We decided to [crowdfund it](https://sellcodes.com/q1OGuSox), meaning: 

- **Contributions of 5-10 USD**: Feel great by giving a sincere "Thank you" and helping us to develop the plugin further. If the plugin made your life easier in the past, it's a great opportunity to show some appreciation :)
- **Contributions of 15 USD**: As in 1.), plus you'll get a free premium license (for the premium plugin which we will create*). A contribution of 30 USD gets you 2 licenses. 
- **Contributions of 50 USD** (or more): As in 1), plus you'll get an unlimited websites premium license. This a fantastic, one-time deal. The plugin will provide many more features - such as backup scheduling, backup to external servers etc. - while still being super-easy to use!

All licenses are lifetime licenses and valid on both commercial and non-commercial websites. 

*We will create the premium plugin only if we reach our (quite modest) crowdfunding goal of 3,000 USD. If we don't hit it, you'll get your contributions refunded (except the 5 and 10 USD amounts - those are basically for the past :). 

Please help us out, and see the status quo of our crowdfunding effort, on the [WP clone crowdfunding page](https://sellcodes.com/q1OGuSox)!

= Where do we stand today? =

But first, let’s back up ;) - where does the (free) plugin stand now?

WP Clone is still the easiest, fastest, cheapest and most secure way to backup, migrate or clone a WordPress site to another domain or hosting server. 

You can also use it to backup, migrate or clone your site to/from local server hosting, to create backup of your site for development or testing purposes, and to install pre-configured backups of WordPress.

WP Clone is a superior to other backup & migrate plugins for the following reasons:

* It does not require FTP access to backup files you migrate or clone, neither the source or destination site; just install a new WordPress on the destination site, install our backup plugin, and follow the prompts to migrate or clone your site.
* It does not backup or restore the WordPress system files (it just creates user content and database backups); reducing upload time for migration and improving security of your site
* It fetches the site backup via your host's direct http connection, which saves you from having to upload large backup files, making it easier to migrate. 

= What are today's limitations? =

Today: 

- 90% of cases: Backups & migrations work flawlessly (we fixed some key bugs in the most recent version)
- 9% of cases: Backups or migrations fail due to your hoster's configurations (most likely limits in up- and downloads) which is typically the case when you backup or migrate very large sites. However, there's a workaround: simply do a “Database Only” backup (use “Advanced Settings”), transfer the wp-content directory over with FTP, and then restore new site. Then backup and migration also works. 
- 1% of cases: Your site/hosting is abnormal (pardon our French) and backup or migration doesn't work. However: that's what we'll now be working on, so that eventually backups and migrations will work in all cases. 

The 1% case means: 

- Basic rule: DO NOT use it as your only backup solution! Only use it for migrations (so that if something fails, you still have the files on your old site as backup). 
- If you want to use it as backup, test it by restoring the backup file on a new site. If that works fine you should be safe.
- In any case, we cannot take any responsibility if backup or migration fails. 

Note: 
* There is never an issue in damaging the source installation (i.e. on the site where you create the backup). So backup sites at your pleasure. If your backup succeeds then chances are good that the migration (i.e. restore on another site) will also succeed. But don't take any chances.
* If backup or migration (restore) fails, just try it again. Often it works on second attempt.

= Other tips how to backup and migrate =

* NEVER overwrite an installation for which you do not have an alternate backup source (e.g. a cPanel backup). Normally you would restore the backup onto a fresh WP installation on another host or on a subdomain. If the restore fails your destination site might become unusable, so be prepared to enter cPanel and then destroy / recreate the new installation if necessary.
* DO NOT use our backup plugin on WP Engine or any hosting system with proprietary operating system. Instead, use their built-in backup tools.
* Large sites (>2GB) might take as long as an hour to backup and migrate. Sites of 250 MB or less should take no more than a minute or two to backup, depending on your server.
* We recommend you deactivate and delete page caching, security and maybe redirection plugins before you migrate, and re-install them on the new site, if necessary. In general, delete all unnecessary plugins and data from your site before you backup. You can also use the "Exclude directories" option if you have large media files, which you can then copy back to the new site with FTP. 
* How to copy from local server to your hosted website: Create a backup of the local site in the usual way, then save the backup file (right-click > Save) to your local disk. Upload this file to the root directory of your destination website and then use this url in the “Restore” dialog of the new site: http://yourdomain.com/<name of the backup file.zip>.

= Help Video =


(Old video, but may still be helpful; we’ll replace it with an updated one soon)

[youtube http://www.youtube.com/watch?v=xN5Ffhyn4Ao]

= Credits =
WP Backup uses functions from the "Safe Search and Replace on Database with Serialized Data" script first written by David Coveney of Interconnect IT Ltd (UK) http://www.davidcoveney.com or http://www.interconnectit.com and released under the WTFPL http://sam.zoy.org/wtfpl/. Partial script with full changelog is placed inside 'lib/files' directory.

Thank you for reading this far. Please don’t forget to [contribute to create a kick-ass plugin](https://sellcodes.com/q1OGuSox). Thank you!
== Installation ==

1. Navigate to Plugins > Add New
2. Search for "WP Clone by WP Academy"
3. Install and activate the backup and migration plugin
4. Follow remaining instructions in the help video

== Frequently Asked Questions ==
Backup and migration FAQ are under construction

== Changelog ==

= 2.2.10 =
* Updated links

= 2.2.9 =
* Updated feedback version
* Updated migration texts

= 2.2.8 =
* Updated migration texts

= 2.2.7 =
* Updated backup plugin pic
* Updated backup texts

= 2.2.6 =
* Integrated feedback system

= 2.2.5 =
* Major bug that % got hashed fixed
* Other basic bug fixes (e.g. backups and migrations didn’t work on new domains)
* Texts in plugin updated, broken links removed

= 2.2.4 =
* Updated: `Tested up to` tag for backup and migration

= 2.2.3 =
* Added: PHP7 support of backup and migration
* Added: a multisite check during restore (not required at time of backup)
* Fixed: failed backups due to unreadable files

= 2.2.1 =
* Fixed: Backup names will use the time zone selected in general settings
* Added: basic backup and migration logs
* Added: An option to exclude files in backup and migration based on size (files larger than 25MB will be excluded by default for backups and migration)
* Added: An option to ignore the wordpress table prefix during backup and migration
* Added: An option to check the mysql connection during migration (restore)
* Changed: Files are no longer copied to a temporary location during backup
* Changed: siteurl option is updated during the database import

= 2.2 =
* Fixed: Missing backups that some users encountered after upgrading to 2.1.9
* Added: An option to refresh the backup list
* Added: An option to remove the database entry and delete all the backup files
* Added: A section that shows the uncompressed database size and the uncompressed size and the number of files that will be archived during a full backup
* Added: Notes in the advanced settings section regarding the Maximum memory limit and the Script backup execution time fields.
* Added: The report returned from the search and replace process into the restore successful page
* Changed: Moved the backup list location from the custom table to the wp_options table. (previous backups will be imported and the custom table will be removed on existing installations)
* Changed: Only the tables with the wordpress table prefix will be altered during a restore
* Changed: Only the tables with the wordpress table prefix will be saved during a backup
* Changed: Backup deletion is now handled using AJAX

= 2.1.6 =
* Added: An option to exclude specific directories during backup
* Added: An option to only backup the database
* Changed: File operations during backup are now handled directly instead of using the WP filesystem abstraction class

= 2.1.4 =
* Fixed: When javascript is disabled,submit button shows "Create Backup" but the plugin attempts to do a restore
* Changed: The temporary directory location during the backup restore process from '/wp-content/' to '/wp-content/wpclone-temp/'

= 2.1.3 =
* Added: An option to backup the database using WordPress' WPDB class
* Removed: The need to keep the original backup names intact
* Changed: The backup name structure
* Changed: Backup file downloads are now handled using WP core functions

= 2.0.2 =
* Initial release


== Screenshots ==
1. Configuration Page

== Upgrade Notice ==

= 2.2.10 =
* Please upgrade!