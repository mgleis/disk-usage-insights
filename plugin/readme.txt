=== Disk Usage Insights ===
Contributors: mgleis
Tags: disk usage, file size, large files, large folders
Requires at least: 5.0
Tested up to: 6.7.1
Stable Tag: 1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Find large files and folders in no time! Hunt down the TOP 10 files and folders with the most disk usage.

== Description ==

Find large files and large folders in no time. This plugin scans your whole WordPress installation, counts all files and folders, sums up the sizes and outputs useful statistics to find unwanted large objects in your system.

== Demonstration ==

Use this link to get a live preview of this plugin in the WordPress Playground:

[Open Demo](https://playground.wordpress.net/?php-extension-bundle=light&plugin=disk-usage-insights&url=%2Fwp-admin%2Ftools.php%3Fpage%3Ddisk-usage-insights)

== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder.
1. Go to the **Plugins** page and activate the plugin.

== Frequently Asked Questions ==

= How do I use this plugin? =

You have two options:
* Open the Tools section in the Admin Area and select "Disk Usage Insights".
* Click the pie chart in the admin bar.

= How to uninstall the plugin? =

Simply deactivate and delete the plugin.

= Is Multisite supported? =

Yes, but only for users with the "manage_sites" privilege, which Super Admins have by default.

== Screenshots ==
1. Largest Files and Largest Folders
1. Largest Folders, Folders with most files, Largest Plugins, Largest Themes

== Changelog ==

= 1.2 =
* Support for really big and slow installations (complete code rewrite, usage of sqlite as a database and job queues on top of it)

= 1.1 =
* Support for Multisite installations (user must have privilege "manage_sites" which Super Admins have by default)
* Disk Usage in percent is now visualized with a bar in the background of the first column

= 1.0 =
* Plugin released.
