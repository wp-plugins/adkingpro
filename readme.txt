=== Ad King Pro ===
Contributors: ashdurham
Donate link: http://durham.net.au/donate/
Tags: advertising, ads, ad, adverts, advert, advertisements, advertisement, advertise, stats, stat, statistics, statistic, promotions, promotion, banners, banner, tracking, track, detailed, adkingpro, ad king pro, page, post, reporting, reports, report, csv, pdf, revenue, charge, money, theme, themes, flash, adsense, text, resize
Requires at least: 3.0.1
Tested up to: 3.5.2
Stable tag: 1.7.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ad King Pro allows you to easily manage, track and report on your on-site advertising. Upload, link, go.

== Description ==

--

Stay up-to-date with the latest by following [@kingproplugins on Twitter](http://twitter.com/kingproplugins) or [KingProPlugins on Facebook](http://facebook.com/kingproplugins)

--

Ad King Pro allows you to easily manage, track and report on your on-site advertising. Upload your banner/flash banner/adsense code/text, add the link its to go to then 
your ready to go. Set it to start and end at a certain time if needed. Ad King Pro can be placed into any page or post by using the shortcode. It can also be placed directly into 
theme files if need be. Create types and assign multiple banners to randomly show one on every page refresh, define a category to display in
a specific spot or even define a particular ad to display.

All clicks on your Ad King Pro banners are logged by IP address for a set time, modifiable within the settings page, 
to give a true reading of the click rate you are receiving.

Reports of clicks per banner are given on the dashboard, and in detail on the Ad King Pro dashboard page. Stats are given daily, weekly, monthly and all time. The day the week
starts for you is also modifiable within the settings section. Giving your advertisers a detailed view of the clicks they received on your site 
is important, so with Ad King Pro, you can export a banners stats in either CSV or PDF format.

Assign adverts an impression and click price. The details section and PDF will then calculate and display how much you have earnt from 
your advertising. Revenue made from advertising is what it is there for, give detailed reports of your advertisers banners with an outline of how 
much you are owed.

Choose how your PDF reports look by choosing a PDF theme from the settings. The amount of themes are a bit light on at the moment, but please
feel free to contact me if you would like to implement your own custom branded design. Visit [my website](http://durham.net.au/wordpress/plugins/ad-king-pro/) to
make a request for development (and design if required).

--

If you have any suggestions or would like to see a feature in the plugin, please let me know in the support forum.

Any issues you are having, I'd also love to know, so again, please let me know using the support forum.

--

Check out my newly released plugin, [Invoice King Pro](http://wordpress.org/plugins/invoice-king-pro/)


== Installation ==

1. Download and unzip the zip file onto your computer
2. Upload the 'adkingpro' folder into the `/wp-content/plugins/` directory (alternatively, install the plugin from the plugin directory within the admin)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Create your first advert within the 'Advert' section of the admin (Make sure you assign it to a type)
5. Within the WYSIWYG editor, place the short code '[adkingpro]' or within the code, &lt;?php if (function_exists('adkingpro_func')) echo do_shortcode('[adkingpro']); ?&gt;

== Frequently Asked Questions ==

= After activating this plugin, my site has broken! Why? =

Nine times out of ten it will be due to your own scripts being added above the standard area where all the plugins are included. If you move your javascript files below the function, "wp_head()" in the "header.php" file of your theme, it should fix your problem.

= I want to track clicks on a banner that scrolls to or opens a flyout div on my site. Is it possible? =

Yes. Enter a '#' in as the URL for the banner when setting it up. At output, the banner is given a number of classes to allow for styling, one being "banner{banner_id}", where you would replace the "{banner_id}" for the number in the required adverts class. Use this in a jquery click event and prevent the default action of the click to make it do the action you require

= I have created an Advert and added the shortcode onto my page but nothing shows up. Why? =

Be sure that you have assigned your advert to an "Advert Type". One called sidebar is automatically created for you when you install the plugin. It is this type that is pulled automatically in the default shortcode. Also in some cases you don't need to wrap the type in quotes (eg [adkingpro type=sidebar]).

= I get an error saying the PDF can't be saved due to write permissions on the server. What do I do? =

The plugin needs your permission to save the PDFs you generate to the output folder in the plugins folder. To do this, you are required to update the outputs permissions to be writable. Please see [the wordpress help page](http://codex.wordpress.org/Changing_File_Permissions) to carry this out

== Screenshots ==

1. Wordpress Dashboard with AdKingPro activated
2. Advert section of wordpress admin
3. Adding/Editing an Advert and its options
4. AdKingPro stat section
5. AdKingPro settings and FAQ/Help

== Changelog ==

= 1.7.1 =
* Removal of left over echo in output

= 1.7 =
* Added option to set a size to an advert type, generating that image size from the images you upload.
* Added support for lack of 'post-thumbnail' support
* Added 'text' media type

= 1.6.1 =
* Issue with CSV and PDF generation fixed

= 1.6 =
* Addition of expiry date/time functionality

= 1.5.1 =
* Adding missing functionality for flash upload button

= 1.5 =
* Added support for flash and Google AdSense banners

= 1.4.2 =
* Update compatible with 3.5.2

= 1.4.1 =
* Fix to install error

= 1.4 =
* Widget option added

= 1.3 =
* Update to how admin scripts are included

= 1.2 =
* Addition of revenue allocation and calculation
* Addition of PDF themes
* Fix to week starts dropdown

= 1.1 =
* Addition of impressions
* Addition of impression settings
* Update to settings page
* Update to PDF output - display of banner refined

= 1.0 =
* Initial

== Upgrade Notice ==

= 1.7.1 =
* Removal of left over echo in output

= 1.7 =
* Added option to set a size to an advert type, generating that image size from the images you upload.
* Added support for lack of 'post-thumbnail' support
* Added 'text' media type

= 1.6.1 =
* Issue with CSV and PDF generation fixed

= 1.6 =
* Now set expiry dates/times on your adverts

= 1.5.1 =
* Adding missing functionality for flash upload button

= 1.5 =
* Added support for flash and Google AdSense banners

= 1.4.2 =
* Update compatible with 3.5.2

= 1.4.1 =
* Fix to install error

= 1.4 =
* Widget option added

= 1.3 =
* Update to how admin scripts are included to work better with other plugins

= 1.2 =
* Themes now available for PDF reporting
* Now track how much your advertising is making you by entering revenue pricing for impressions and clicks

= 1.1 =
* Added tracking impressions and settings
* Banner rendering in PDF improved

= 1.0 =
* Gotta start somewhere