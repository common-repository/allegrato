=== Allegrato ===
Contributors: ssmentek
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=C4XJ6C9VZX9YA
Tags: allegro, auctions
Requires at least: 3.0.1
Tested up to: 3.0.1
Stable tag: trunk

Allegrato takes information from auctions and insertions systems and displays it on widget at frontend.

== Description ==

plugin home page: http://blog.smentek.eu/allegrato-wordpress-plugin-for-auction-and-publication-systems/

Plugin needs PHP 5.3.x or higher.

Current version supports only one widget `My allegro auctions` that displays user auctions from allegro system.
Allegrato supports all foreign allegros 'like' foreign systems that works with allegro webapi:

*allegro.by
*aukro.bg
*aukro.cz
*teszvesz.hu
*allegro.kz
*tizo.ro
*molotok.ru
*aukro.sk
*aukro.ua
*allegro.rs
*aukro.ua

== Installation ==

1. (recommended) Install `WP File Cache` plugin, or any other cache plugin that can cache widgets.
2. Upload `allegrato` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Test and save access data for remote system through the 'settings' menu.
5. Activate and set widget.

== Frequently Asked Questions ==

= Do I need any cache for a plugin =

No, but some caching is highly recommended. Plugin dosn't have internal cache and without it
will be pulling data from remote systems on every requests. It means that without some additional 
cache layer, site will work slow and will generate unnecessary trafic to remote system.  

= What cache plugin is best to use with Allegrato plugin? =

I recommend `WP File Cache` plugin. It is simplest solution. `WP File Cache` stores data pulled from remote system into files.
Allegrato is able to use data from those files to display it on widgets so there is no need to pull data from remote system on every request. 
Any other cache plugin that is able to cache widgets output would be good to.

== Screenshots ==

1. My allegro auctions widget.

== Changelog ==

= 0.0.2 =
* Fixes html code on widget
* Error reporting temporary force on 0. 

= 0.0.1 =
* 'My allegro auctions' widget for allegro 'like' systems.
