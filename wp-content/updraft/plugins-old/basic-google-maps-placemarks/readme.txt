=== Basic Google Maps Placemarks ===
Contributors:      iandunn
Donate link:       http://www.doctorswithoutborders.org
Tags:              map, google maps, marker, placemark, geocode, shortcode, marker clustering
Requires at least: 3.1
Tested up to:      4.8
Stable tag:        1.10.7
License:           GPL2

Embeds a Google Map into your site and lets you add map markers with custom icons and information windows.


== Description ==
BGMP creates a [custom post type](http://www.youtube.com/watch?v=FWkLBPpGOmo#!) for placemarks (markers) on a Google Map. The map is embedded into pages or posts using a shortcode, and there are settings to affect how it's displayed. You can create markers that will show up on the map, and set their icons using the Featured Image meta box. When a marker is clicked on, a box will appear and show the marker's title and description.

**Features**

* Each map marker can have a unique custom Installation icon, share a common custom icon, or use the default icon.
* Options to set the map type (street, satellite, etc), center location, size, zoom level, navigation controls, etc.
* Setup unique maps on different pages with their own placemarks, map types, center locations, etc.
* Placemarks can be assigned to categories, and you can control which categories are displayed on a individual map.
* Marker clustering for large numbers of placemarks
* Extra shortcode to output a text-based list of markers for mobile devices, search engines, etc.
* Lots of filters so that developers can customize and extend the plugin.
* Use HTML, images, etc inside the information window.
* Compatible with WordPress MultiSite.
* Internationalized (see [Other Notes](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/other_notes/) for a list of supported languages)

**Live Examples**

* [The Australian Polio Register](http://www.polioaustralia.org.au/?page_id=6098)
* [The North Carolina Fire Station Mapping Project](http://fdmaps.com/forestry-ncfs-and-usfs-combined/)
* [Washington House Churches](http://washingtonhousechurches.net)

**Support**

I'm happy to fix reproducible bugs, but don't have time to help you customize the plugin to fit your needs. There's also plenty of documentation and community support available. Check out the 'How can I get help when I'm having a problem?' question in [the FAQ](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/faq/) for details.


== Installation ==


For help installing this (or any other) WordPress plugin, please read the [Managing Plugins](http://codex.wordpress.org/Managing_Plugins) article on the Codex.


**Basic Usage:**

1. After activating the plugin, go to the 'Basic Google Maps Placemarks' page under the Settings menu. Enter the address that the map should be centered on.
2. Create a page or post where you'd like to embed the map, and type `[bgmp-map]` in the content area.
3. Go to the Placemarks menu and click 'Add New'. Enter the title, address, etc.
4. Click on 'Set Featured Image' to upload the icon.
5. Click on the 'Publish' or 'Update' button to save the placemark.

**Advanced Usage:**

*Multiple maps with different locations, zoom levels, etc:*

1. Just add the extra parameters to the [bgmp-map] shortcode. Here's an example of the different ones you can use:

> [bgmp-map categories="parks,restaurants" width="500" height="500"]

> [bgmp-map placemark="105" center="chicago" zoom="10" type="terrain"]

*Multiple maps with different placemarks:*

1. Go to the Placemarks menu and click on Categories, and add a category for each set of placemarks.
2. Edit your placemarks and click on the category you want to assign them to.
3. Edit the place where the map is embedded and add the category parameter to the shortcode. For example: [bgmp-map categories="restaurants,record-stores"] or [bgmp-map categories="parks"]. Use the category's slug, which is displayed on the Categories page in step 1. Separate each slug with a comma.
4. You can add the [bgmp-map] shortcode to multiple pages, each time using a different set of categories.

*Setting the stacking order of overlapping markers:*

1. Choose which placemark you want to appear on top and edit it.
2. Enter a number in the Stacking Order meta box in the right column that is greater than the other placemark's stacking order.

*Adding a text-based list of placemarks to a page:*

1. Edit the post or page you want the list to appear on.
2. Type `[bgmp-list]` in the context area.
3. Click the 'Publish' or 'Update' button.
4. (optional) You can specifiy a specific category or categories to pull from. e.g., [bgmp-list categories="record-stores,parks"]
5. (optional) You can add "View on Map" links to each item in the list, which will open the corresponding marker's info window. e.g., [bgmp-list viewonmap="true"]

*Using [bgmp-map] in a template file with do_shortcode():*

For efficiency, the plugin only loads the required JavaScript, CSS and markup files on pages where it detects the map shortcode is being called. It's not practical to detect when [do_shortcode()](http://codex.wordpress.org/Function_Reference/do_shortcode) is used in a template, so you need to manually let the plugin know to load the files by adding this code to your theme:

`
function bgmpShortcodeCalled()
{
	global $post;

	$shortcodePageSlugs = array(
		'hello-world',
		'second-page-slug'
	);

	if( $post )
		if( in_array( $post->post_name, $shortcodePageSlugs ) )
			add_filter( 'bgmp_map-shortcode-called', '__return_true' );
}
add_action( 'wp', 'bgmpShortcodeCalled' );
`

Copy and paste that into your theme's *functions.php* file or a [functionality plugin](http://www.doitwithwp.com/create-functions-plugin/), update the function names and filter arguments, and then add the slugs of any pages/posts containing the map to $shortcodePageSlugs.

That won't work for the home page, though. If you want to target the home page, or any other pages with [conditional tags](http://codex.wordpress.org/Conditional_Tags), you can do it like this:

`
function bgmpShortcodeCalled()
{
	global $post;

	if( ( function_exists( 'is_front_page' ) && is_front_page() ) || ( function_exists( 'is_home_page' ) && is_home_page() ) )
		add_filter( 'bgmp_map-shortcode-called', '__return_true' );
}
add_action( 'wp', 'bgmpShortcodeCalled' );
`

Before version 1.9, you needed to use the `bgmp_map-shortcode-arguments` filter to pass shortcode arguments when calling `do_shortcode()` from a template, but that is no longer necessary. You can simply pass the arguments in the `do_shortcode()` call, like this:

`
do_shortcode( '[bgmp-map center="Boston" zoom="5"]' );
`


Check [the FAQ](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/faq/) and [support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) if you have any questions.


== Frequently Asked Questions ==

= How do I use the plugin? =
Read the instructions on [the Installation page](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/installation/). If you still have questions, read this FAQ and look for answers on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks). If you can't find an answer, start a new thread on the forums.

= How can I get help when I'm having a problem? =

> **Don't e-mail me, unless it's a security issue.** I automatically delete any support requests that come in over e-mail. Follow the steps below instead.

1. Read the [the Installation page](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/installation/).
2. Read the answers in this FAQ.
3. Look through [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks), because there's a good chance your problem has already been addressed there.
4. Check the [Other Notes](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/other_notes/) page for known conflicts with other plugins.

If you still need help, then follow these instructions:

1. Disable all other plugins and switch to the default theme, then check if the problem is still happening. If it isn't, then the problem may actually be with your theme or other plugins you have installed.
3. If the problem is still happening, then start a new thread in the forum with a **detailed description** of your problem and **the URL to the page on your site where you placed the map**. Please copy/paste any error messages verbatim. Screenshots can be very helpful, too. And please [be respectful](http://helpfulnerd.com/be-respectful-of-wordpress-plugin-developers/).
5. Check the 'Notify me of follow-up posts via e-mail' box so you won't miss any replies.

I often don't have time to provide support, but if I can't there's still a chance that another user will be able to help you. If not, I'd recommend hiring a developer. See the Customization section on the [Other Notes](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/other_notes/) page for more info on that.


= Does the plugin support [feature]? / How can I get the plugin to do [feature]? =
All of the features that the plugin supports are documented on these pages. If you don't see a feature mentioned, then that means that the plugin doesn't support it. You'll need to write the extra code yourself if you want to add that feature to the plugin, or hire someone to do it for you (see the Customization section on the [Other Notes](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/other_notes/) page). There are filters throughout the core code to support customization. If you need a hook or filter that doesn't currently exist, add a post to [the support forums](http://wordpress.org/support/plugin/basic-google-maps-placemarks) to request it and I'll add it to the next version.

You can also try searching [the support forums](http://wordpress.org/support/plugin/basic-google-maps-placemarks) in case others have already worked out a way to do it.

If you do get it working with your custom code, please share it on [the support forums](http://wordpress.org/support/plugin/basic-google-maps-placemarks) so that others can benefit from your work.


= Why do I get an error saying I need an API key? =
Sometimes Google requires you to obtain an API from them in order to use the Google Maps and Geocoding services on your site. You can go to the Settings screen for instructions on how to get them.

Developers can also use the `bgmp_maps-api-url-parameters` and `bgmp_geocoding-api-url-parameters` filters to programmatically assign keys.


= What does the, "That address couldn't be geocoded, please make sure that it's correct" error mean? =
There are several possible causes for this error, but they generally fall into two different categories.

1. Google Maps didn’t recognize the address you entered.
2. The plugin couldn’t connect to the Google Maps API to geocode the address.

If the error is followed by something similar to the example below, then the problem was with connecting to the Maps API:

`
Geocode response:

stdClass Object
(
	[results] => Array
	(
	)
	[status] => OVER_QUERY_LIMIT
)
`

*If the problem is with the address,* you can try entering it in a different format. For example, instead of “5th Ave and Blanchard St, Seattle”, try “2124 5th Ave, 98121”. You can also try using latitude/longitude coordinates to bypass the geocoding process entirely; see the other FAQ answers for details on that.


*If the problem is with the connection,* some of of the possible reasons for that are:

1. Google Maps places a limit on how many geocoding requests it will serve per day. If you're using shared hosting, there could be other sites on your server or netblock that are also making requests, and you've hit the limit for the day. If this is the problem, you’ll probably need to ask your web host to move you to your own VPS, or just wait until tomorrow and try again.
2. There could be problems with your network or server that are interfering with the connection. If this is the problem, your web host can help you troubleshoot it.
3. Google could be blocking requests from your server’s IP address or netblock due to abuse or violations of their terms of service. The violations could be caused by your site, or another site on your server/netblock. If this is the problem, your web host can help you troubleshoot it. After they remove the problem then it might start working again after a delay (probably 1-7 days), or they may need to contact Google to ask that the server be removed from the blacklist.

You can also try using latitude/longitude coordinates to bypass the geocoding process entirely; see the other FAQ answers for details on that.


= The page says 'Loading map...', but the map never shows up. =
Check to see if there are any Javascript errors by [opening the JavaScript console](http://webmasters.stackexchange.com/q/8525/16266) in your web browser. An error caused by other plugins or your theme can prevent BGMP from working. You'll need to fix the errors, or switch to a different plugin/theme.

Also, make sure your theme is calling *[wp_footer()](http://codex.wordpress.org/Function_Reference/wp_footer)* right before the closing *body* tag in footer.php.


= The map doesn't look right. =
This is probably because some rules from your theme's stylesheet are being applied to the map. Contact your theme developer for advice on how to override the rules.


= Can I use coordinates to set the marker, instead of an address? =
Yes. You can type anything into the Address field that you would type into a standard Google Maps search field, which includes coordinates.

If the plugin recognizes your input as coordinates then it will create the marker at that exact point on the map. If it doesn't, it will attempt to geocode them, which can sometimes result in a different location than you intended. To help the plugin recognize the coordinates, make sure they're in decimal notation (e.g. 48.61322,-123.3465) instead of minutes/seconds notation. The latitude and longitude must be separated by a comma and cannot contain any letters or symbols. If your input has been geocoded, you'll see a note next to the address field that gives the geocoded coordinates, and the plugin will use those to create the marker on the map; if you don't see that note then that means that your input was not geocoded and your exact coordinates will be used to place the marker.

If you're having a hard time getting a set of coordinates to work, try visiting <a href="http://www.itouchmap.com/latlong.html">Latitude and Longitude of a Point</a> and use the coordinates they give you.


= None of the placemarks are showing up on the map =
If your theme is calling `add_theme_support( 'post-thumbnails' )` and passing in a specific list of post types -- rather than enabling support for all post types -- then it should check if some post types are already registered and include those as well. This only applies if it's hooking into `after_theme_setup` with a priority higher than 10. Contact your theme developer and ask them to fix their code.

Also check the [Other Notes](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/other_notes/) page for known conflicts with other plugins.


= Can I change the default icon? =
Yes, if you want to use the same custom icon for all markers by default, instead of having to set it on each individual placemark, you can add this to your theme's functions.php or a [functionality plugin](http://www.doitwithwp.com/create-functions-plugin/):

`
function setBGMPDefaultIcon( $iconURL )
{
	return get_bloginfo( 'stylesheet_directory' ) . '/images/bgmp-default-icon.png';
}
add_filter( 'bgmp_default-icon', 'setBGMPDefaultIcon' );
`

The string you return needs to be the full URL to the new icon.


= How can I set the default icon by category or other condition? =
If you only want to replace the default marker under certain conditions (e.g., when the marker is assigned to a specific category), then you can using something like this:

`
function setBGMPDefaultIconByCategory( $iconURL, $placemarkID )
{
	$placemarkCategories = wp_get_object_terms( $placemarkID, 'bgmp-category' );

	foreach( $placemarkCategories as $pc )
	{
		switch( $pc->slug )
		{
			case 'restaurants':
				$iconURL = get_bloginfo( 'stylesheet_directory' ) . '/images/marker-icons/restaurants.png';
			break;

			case 'book-stores':
				$iconURL = get_bloginfo( 'stylesheet_directory' ) . '/images/marker-icons/book-stores.png';
			break;

			default:
				$iconURL = get_bloginfo( 'stylesheet_directory' ) . '/images/marker-icons/pin.png';
			break;
		}
	}

    return $iconURL;
}
add_filter( 'bgmp_default-icon', 'setBGMPDefaultIconByCategory', 10, 2 );
`

Here's another example to uses the placemark's ID:

`
function setBGMPDefaultIconByID( $iconURL, $placemarkID )
{
	if( $placemarkID == 352 )
		$iconURL = get_bloginfo( 'stylesheet_directory' ) . '/images/bgmp-default-icon.png';

	return $iconURL;
}
add_filter( 'bgmp_default-icon', 'setBGMPDefaultIcon', 10, 2 );
`

The string you return needs to be the full URL to the new icon.


= Can I embed more than one map on the same page? =
No, the Google Maps JavaScript API can only support one map on a page. You can have different maps on separate pages, though. See [the Installation page](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/installation/) for instructions on making different maps have different center locations, display different sets of placemarks, etc.


= How can I override the styles the plugin applies to the map? =
The width/height of the map and marker information windows are always defined in the Settings, but you can override everything else by putting this code in your theme's functions.php file or a [functionality plugin](http://www.doitwithwp.com/create-functions-plugin/):

`
function setBGMPStyle()
{
	wp_deregister_style( 'bgmp_style' );
	wp_register_style(
		'bgmp_style',
		get_bloginfo('template_url') . '/bgmp-style.css'
	);
	wp_enqueue_style( 'bgmp_style' );
}
add_action('init', 'setBGMPStyle');
`

Then create a bgmp-style.css file inside your theme directory or a [child theme](http://codex.wordpress.org/Child_Themes) and put your styles there. If you'd prefer, you could also just make it an empty file and put the styles in your main style.css, but either way you need to register and enqueue a style with the `bgmp_style` handle, because the plugin checks to make sure the CSS and JavaScript files are loaded before embedding the map.


= I get an error when using do_shortcode() to call the map shortcode =
See the instructions on [the Installation page](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/installation/).


= How can I force the info. window width and height to always be the same size? =
Add the following styles to your theme's style.css file or a [child theme](http://codex.wordpress.org/Child_Themes):

`
.bgmp_placemark
{
	width: 450px;
	height: 350px;
}
`


= Can registered users create their own placemarks? =
Yes. The plugin creates a [custom post type](http://codex.wordpress.org/Post_Types), so it has the same [permission structure](http://codex.wordpress.org/Roles_and_Capabilities) as regular posts/pages.


= I upgraded to the latest version and now something's broken =
If you're running a caching plugin like WP Super Cache, make sure you delete the cache contents so that the latest files are loaded, and then refresh your browser.

If you upgraded other plugins at the same time, it's possible that one of them is causing a JavaScript error that breaks the entire page or some other kind of conflict. Check if BGMP works with the default theme and no other plugins activated.

If you're still having problems, create a detailed report on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) (see the 'How can I get help when I'm having a problem?' question above), and then [download an older version](http://wordpress.org/extend/plugins/basic-google-maps-placemarks/developers/) to use until the problem is fixed.

Also, keep in mind that professionals don't just install plugin updates on their live website and then get angry when they inevitably run into a situation where an update crashes the site. The right way to do it is to have [a staging server](http://webdesign.about.com/od/servers/qt/web-servers-and-workflow.htm) where you test all updates and code changes, and then push them to the production server once you're satisfied that everything is working properly. If your website is mission-critical, then this is what you need to be doing. If you're not capable or willing to do it yourself, then you need to hire a developer to manage the process for you. If you don't do those things, then you don't have anyone to blame but yourself when things go wrong. You can subscribe to [the BGMP Testers e-mail list](http://iandunn.us6.list-manage.com/subscribe?u=38510a08f1d822cc1c358e644&id=b183d686c6) to be notified when new release candidates are available for testing.


= Is this plugin secure? =
I've done my best to ensure that it is, but just in case I missed anything [I also offer a security bounty](https://hackerone.com/iandunn-projects/) for any vulnerabilities that can be found and privately disclosed in any of my plugins.


= Are there any hooks I can use to modify or extend the plugin? =
Yes, I've tried to add filters for everything you might reasonably want, just browse the source code to look for them. If you need a filter or action that isn't there, make a request on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) and I'll add it to the next version.


== Other Notes ==

**Localizations**

* Spanish (thanks to Andrew Kurtis from [WebHostingHub](http://www.webhostinghub.com/))
* Chinese (thanks to [yzqiang](http://wordpress.org/support/profile/yzqiang))
* Russian (thanks to [alexgr](http://profiles.wordpress.org/alexgr))
* French (thanks to Romain Fevre)
* German (thanks to Jens)
* Italian (thanks to [Andrea Colombo](http://www.acolombodesign.com/))
* Serbo-Croatian (thanks to Borisa Djuraskovic from [WebHostingHub](http://www.webhostinghub.com/))
* Dutch (thanks to [mardonios](https://profiles.wordpress.org/mardonios/))

If there isn't a translation for your language (or it is incomplete/inaccurate) please consider making one and contributing it to the plugin. You can learn how by reading [Translating WordPress](http://codex.wordpress.org/Translating_WordPress) and [How to Create a .po Language Translation](http://www.wdmac.com/how-to-create-a-po-language-translation). The .pot file you'll need is inside the *languages* directory in the plugin's folder. Once you're done, just start a thread on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) with links to the .po and .mo files, and I'll add them to the next release. You can also subscribe to [the BGMP Translators e-mail list](http://iandunn.us6.list-manage1.com/subscribe?u=38510a08f1d822cc1c358e644&id=b7ff5f7393) to be notified when updated versions have new strings to translate.


**Known conflicts**

* The [Post Types Order](http://wordpress.org/extend/plugins/post-types-order/) plugin can cause <a href="http://wordpress.org/support/topic/plugin-basic-google-maps-placemarks-shortcode-bgmp-list-not-returning-all-placemarks">the wrong placemarks to show up</a> in [bgmp-map] or [bgmp-list] results. Try disabling the *AutoSort* feature.
* The [Better WP Security](http://wordpress.org/extend/plugins/better-wp-security/) plugin may [break the Google Maps API](http://wordpress.org/support/topic/plugin-better-wp-security-google-maps-api) if the "Display random version number" option is enabled.
* The [bgmp-map] and [bgmp-list] shortcodes <a href="http://wordpress.org/support/topic/plugin-basic-google-maps-placemarks-map-showing-all-placemarkers-no-filter">won't work in WP e-Commerce product post types</a>.
* Also make sure that no other Google Maps plugins are activated, and that your theme isn't including the Maps API. You can view the page's source code and search for instances of "maps.google.com/maps/api/js". If there's more than one, then you're probably going to have issues.


**How you can help with the plugin's development**

* The thing I could really use some help with is answering questions on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks). I don't have a lot of time to work on the plugin, so the time I spend answering questions reduces the amount of time I have to add new features. If you're familiar with the plugin and would like to help out, you can click the 'Subscribe to Emails for this Plugin' link to get an e-mail whenever a new post is created.
* Translate the plugin into your language. See the *Localizations* section above for details.
* Volunteer to test new versions before they're officially released. Sign up for [the BGMP Testers e-mail list](http://iandunn.us6.list-manage.com/subscribe?u=38510a08f1d822cc1c358e644&id=b183d686c6) to be notified when new release candidates are available for testing.
* If you find a bug, create a post on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) with as much information as possible. If you're a developer, create a patch and include a link to it in the post.
* Send me feedback on how easy or difficult the plugin is to use, and where you think things could be improved. Add a post to [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) with details.
* Send me feedback on ways the documentation could be more clear or complete. Add a post to [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) with details.
* Review the code for security vulnerabilities and best practices. If you find a security issue, please [contact me](http://iandunn.name/contact) privately so that I can release a fix for it before publicly disclosing it.
* Check the TODO.txt file for features that need to be added and submit a patch.


**Donations**

I do this as a way to give back to the WordPress community, so I don't want to take any donations. If you'd like to give something, though, I'd encourage you to make a donation to [Doctors Without Borders](http://www.doctorswithoutborders.org) or the [WordPress Foundation](http://wordpressfoundation.org).


**Customization**

If you need to customize BGMP and a solution isn't already available in the forums, the best thing to do is to hire a developer. <a href="https://www.meetup.com/topics/wordpress/">Your local WordPress Meetup</a> is a great place to meet one, or you can also check out [jobs.wordpress.net](http://jobs.wordpress.net).

If you make customizations that could be beneficial to other users, please start a thread on [the support forum](http://wordpress.org/support/plugin/basic-google-maps-placemarks) with a description of them and a link to the source code.


== Screenshots ==
1. Custom marker icons on the map and a text list of markers below the map
2. Marker clusterings and custom map icons
3. The Placemarks page, where you can add/edit/delete map markers.
4. A example placemark.
5. The Categories screen.
6. The map settings.


== Changelog ==

= v1.10.7 (8/19/2016) =
* [NEW] Added UI options and filters for entering a Google API keys.

= v1.10.6 (5/16/2016) =
* [FIX] Load local copy of default marker clustering images, because [remote copies no longer work](https://wordpress.org/support/topic/default-style-of-marker-clustering-not-working-anymore).

= v1.10.5 (10/1/2015) =
* [UPDATE] Changed the text-domain from `bgmp` to `basic-google-maps-placemarks`, to [support language packs](https://make.wordpress.org/plugins/2015/09/01/plugin-translations-on-wordpress-org/).

= v1.10.4 (5/1/2015) =
* [NEW] Added Russian translation. props alexgr.
* [NEW] Added Serbo-Croatian translation. props Borisa Djuraskovic.
* [NEW] Added Dutch translation. props mardonios.

= v1.10.3 (4/23/2014) =
* [FIX] Closed [a minor XSS vulnerability in several Settings form fields](https://hackerone.com/reports/9375) in the Administration Panels. props [trizaeron](https://hackerone.com/trizaeron).

= v1.10.2 (1/8/2014) =
* [NEW] Added Spanish translation
* [NEW] Added [bgmp_featured-icon-size filter](http://wordpress.org/support/topic/icon-sizes).

= v1.10.1 (11/25/2013) =
* [FIX] Fixed a bug where [the plugin's JavaScript broke if bgmpData was undefined](http://wordpress.org/support/topic/bgmp-110-rc1-available).
* [NEW] Added Italian translation (thanks to Andrea Colombo).
* [NEW] Added IDs to items in the list shortcode [so they can be targeted with CSS](http://wordpress.org/support/topic/add-id-to-each-bgmp-list-item).
* [NEW] Added checks if meta fields exist before saving.

= v1.10 (3/30/2013) =
* [FIX] Fixed persistent bugs in previous 1.9.x releases.
* [FIX] Fixed bgmp-map shortcode [bug that led to conflicts with Jetpack](http://wordpress.org/support/topic/incompatibility-between-bgmp-193-rc1-and-jetpack-204).
* [NEW] Added viewonmap parameter to [bgmp-list] shortcode.
* [NEW] Added placemark ID parameter to [bgmp-map] shortcode.
* [NEW] Added get-map-placemarks-individual-placemark filter.
* [NEW] Added a filter to allow [changing the language the map is displayed in](http://wordpress.org/support/topic/displaying-the-map-in-difeerent-language).
* [NEW] German translation added (thanks to Jens).
* [NEW] Added do_action() calls to views so they can be more easily extended.
* [NEW] Added a filter to allow [disabling the Street View control](http://wordpress.org/support/topic/hide-street-view-option).
* [UPDATE] Attached bgmp JavaScript object to jQuery object, so it can be accessed from other scripts.
* [UPDATE] Added category data available to JavaScript bgmpData object.

Older entries are in docs/changelog.txt


== Upgrade Notice ==

= 1.10.7 =
BGMP 1.10.7 allows you to enter a Google Maps API key, which is sometimes necessary for the map to work.

= 1.10.6 =
BGMP 1.10.6 fixes a bug where marker-clustering images were missing.

= 1.10.5 =
BGMP 1.10.5 prepares the plugin to support language packs from WordPress.org.

= 1.10.4 =
BGMP 1.10.4 adds new translations for Russian, Dutch and Serbo-Croatian.

= 1.10.3 =
BGMP 1.10.3 includes a fix for a minor security vulnerability that would allow Administrators to inject malicious scripts into form fields. Since the vulnerability requires an Administrator account in order to exploit it, it is unlikely to be a realistic attack vector, but just to be safe I still strongly recommend that all users upgrade to this version.

= 1.10.2 =
BGMP 1.10.2 adds a Spanish translation.

= 1.10.1 =
BGMP 1.10.1 fixes a JavaScript bug and adds an Italian translation.

= 1.10 =
BGMP 1.10 adds some new shortcode options, fixes a few bugs, adds more hooks for developers and adds a German translation.

= 1.9.2 =
BGMP 1.9.2 fixes a bug where a [bgmp-map] shortcode category error would incorrectly be displayed in the Admin Panel.

= 1.9.1 =
BGMP 1.9.1 fixes a bug where [bgmp-map] shortcode categories passed as an array would issue a PHP warning.

= 1.9 =
BGMP 1.9 adds support for clustering large numbers of markers together.

= 1.8 =
BGMP 1.8 is internationalized and includes French and Chinese localizations.

= 1.7 =
BGMP 1.7 adds support for category, map center, zoom level and other parameters in the [bgmp-map] and [bgmp-list] shortcodes.

= 1.6.1 =
BGMP 1.6.1 makes it easier to use coordinates for a placemark location instead of an address.

= 1.6 =
BGMP 1.6 adds options to change the map type and fixes several minor bugs.

= 1.5.1 =
BGMP 1.5.1 increases the WordPress version requirement to 3.1.

= 1.5 =
BGMP 1.5 adds support for categorizing placemarks and creating maps on different pages that display different categories.

= 1.4 =
BGMP 1.4 adds the ability to set a stacking order for placemarks that overlap, and fixes several minor bugs.

= 1.3.2 =
BGMP 1.3.2 sorts the markers in the [bgmp-list] shortcode alphabetically, and prevents the information window scrollbar bug in more cases.

= 1.3.1 =
BGMP 1.3.1 fixes a bug where standard posts and pages would lose the 'Set Featured Image' meta box.

= 1.3 =
BGMP 1.3 loads the map/placemarks faster and contains several bug fixes.

= 1.2.1 =
BGMP 1.2.1 fixes a bug related to the marker's info window width and height.

= 1.2 =
BGMP 1.2 adds support for WordPress MultiSite and fixes several minor bugs.

= 1.1.3 =
BGMP 1.1.3 contains bug fixes, performance improvements and updates for WordPress 3.2 compatibility.

= 1.1.2 =
BGMP 1.1.2 just has some minor changes on the back end and a bug fix, so if you're not having problems then there's really no reason to upgrade, other than getting rid of the annoying upgrade notice.

= 1.1.1 =
BGMP 1.1.1 only loads the JavaScript files when needed, making the rest of the pages load faster, and also fixes a minor bugs related to HTTPS pages.

= 1.1 =
BGMP 1.1 will automatically geocode addresses for you, so you no longer have to manually lookup marker coordinates. After uploading the new files, deactivate and reactivate the plugin to populate the new address field on each Placemark based on the existing coordinates.

= 1.0 =
Initial release.
