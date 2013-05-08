=== Ad King Pro ===
Contributors: ashdurham
Donate link: http://durham.net.au/
Tags: advertising, ads, ad, adverts, advert, advertisements, advertisement, advertise, stats, stat, statistics, statistic, promotions, promotion, banners, banner, tracking, track, detailed, adkingpro, ad king pro, page, post, reporting, reports, report, csv, pdf
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 1.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ad King Pro allows you to easily manage, track and report on your on-site advertising. Upload, link, go.

== Description ==

Ad King Pro allows you to easily manage, track and report on your on-site advertising. Upload your banner, add the link its to go to then 
your ready to go. Ad King Pro can be placed into any page or post by using the shortcode. It can also be placed directly into 
theme files if need be. Create types and assign multiple banners to randomly show one on every page refresh, define a category to display in
a specific spot or even define a particular ad to display.

All clicks on your Ad King Pro banners are logged by IP address for a set time, modifiable within the settings page, 
to give a true reading of the click rate you are receiving.

Reports of clicks per banner are given on the dashboard, and in detail on the Ad King Pro dashboard page. Stats are given daily, weekly, monthly and all time. The day the week
starts for you is also modifiable within the settings section. Giving your advertisers a detailed view of the clicks they received on your site 
is important, so with Ad King Pro, you can export a banners stats in either CSV or PDF format.

--

If you have any suggestions or would like to see a feature in the plugin, please let me know in the support forum.

Any issues you are having, I'd also love to know, so again, please let me know using the support forum.

--


== Installation ==

1. Download and unzip the zip file onto your computer
2. Upload the 'adkingpro' folder into the `/wp-content/plugins/` directory (alternatively, install the plugin from the plugin directory within the admin)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Create your first advert within the 'Advert' section of the admin
5. Within the WYSIWYG editor, place the short code '[adkingpro]' or within the code, &lt;?php do_shortcode('[adkingpro']); ?&gt;

== Frequently Asked Questions ==

= After activating this plugin, my site has broken! Why? =

Nine times out of ten it will be due to your own scripts being added above the standard area where all the plugins are included. If you move your javascript files below the function, "wp_head()" in the "header.php" file of your theme, it should fix your problem.

= I want to track clicks on a banner that scrolls to or opens a flyout div on my site. Is it possible? =

Yes. Enter a '#' in as the URL for the banner when setting it up. At output, the banner is given a number of classes to allow for styling, one being "banner{banner_id}". Use this in a jquery click event and prevent the default action of the click to make it do the action you require

== Screenshots ==

1. Wordpress Dashboard with AdKingPro activated
2. Advert section of wordpress admin
3. AdKingPro stat section
4. AdKingPro settings and FAQ/Help

== Changelog ==

= 1.0 =
* Gotta start somewhere

== Upgrade Notice ==

= 1.0 =
* Initial