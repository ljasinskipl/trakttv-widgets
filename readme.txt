=== TraktTV Wordpress Widget ===
Contributors: ljasinskipl
Donate link: http://www.ljasinski.pl/donate
Tags: widget, trakttv, tv shows, movies
Requires at least: 2.0.2
Tested up to: 3.7.1
Stable tag: 1.4.1
Author URI: http://www.ljasinski.pl
Plugin URI: http://www.ljasinski.pl/en/category/wordpress-en/plugins/trakttv-wordpress-widget-en/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show what you watch to your visitors. Widget, that shows your last watched movies or TV show episodes from trakt.tv

== Description ==

This widget displays your last watched episodes/movies from trakt.tv. Requires trakt.tv account and api key (free to obtain). In future there will be much more features like caching, ratings, more widgets etc.


== Installation ==

Just unzip to '/wp-content/plugins/' and activate

== Frequently Asked Questions ==

= I've found a bug =

Great. Let me know :) Leave me a comment on http://www.ljasinski.pl/en/category/wordpress-en/plugins/trakttv-wordpress-widget-en/ (fastest way) or use support forum

= I have two widgets and they both show the same results =

That's a bug.

== Screenshots ==

1. Example view of widget

2. Widget configuration

== Changelog ==

= 1.4.1 = 
* Hide debug info again

= 1.4 =
* More code rewrites
* BUGFIX for actions: collection and rating
* Code documentation

= 1.3.1 =

* Hide debug info from plain sight

= 1.3 =

* Caching now works with multiple widgets
* Found bugs - can't display info for actions other than seen and scrobble - will be fixed as soon as possible

= 1.2 = 

* Caching results from API - huge performance boost
* Because of simple caching, only one copy of widget will work. Please hold with update until the new release, if you plan to use more than one widget on your blog.
* Some plugin filesystem cleaning and other technical stuff you shouldn't notice
* Simple style override - just create file named user-trakttv.css in your theme directory

= 1.1a =

* Fixed episode info for actions other than "seen"

= 1.1 = 

Added few options:
* select max last views
* select types (movies, shows, episodes)
* select actions (watching, scrobble, rated, etc.)

Some code cleanup

= 1.0 =

* First stable version. Hardcoded 10 last views. Only one widget type.
