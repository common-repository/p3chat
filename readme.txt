=== P3chat ===
Contributors: sergey.s.betke@novgaro.ru
Donate link: http://sergey-s-betke.blogs.novgaro.ru/category/it/web/wordpress/p3chat
Tags: chat, XMPP, Jabber, MSN, MSNP, ICQ
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: trunk

This plugin provides support for p3chat.com online chat service on Your wordpress website.

== Description ==

* Author: [Sergey S. Betke](http://sergey-s-betke.blogs.novgaro.ru/about)
* Project URI: <http://sergey-s-betke.blogs.novgaro.ru/category/it/web/wordpress/p3chat>

This plugin provides support for [online chat p3chat service](http://p3chat.com) (online chat, offline messages)
on Your wordpress website.

== Installation ==

1. Read [my post about p3chat installation](http://sergey-s-betke.blogs.novgaro.ru/p3chat-plugin).
1. Upload `p3chat.php` to the `/wp-content/plugins/p3chat` directory.
1. Register on [p3chat service](http://p3chat.com/signup).
1. Obtain Your UID from HTML code, provided by p3chat.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to P3 Chat options, set Your UID code and save settings.
1. Insert [ p3chat-button ] in page or post for static chat activation button.

== Frequently Asked Questions ==

= What is it p3chat? =

P3chat - chat service for Your site. Please read about at [p3chat website](http://p3chat.com/features).

== Screenshots ==

1. Site main page with online chat button.
1. Online chat AJAX window.
1. Offline message AJAX window (when operator offline).
1. Static chat activation button (by [ p3chat-button ]).

== Changelog ==

= 1.2.1 =
* minor enhancements: now html is XHTML
* correct p3chat script registration - through wp_register_script and wp_enqueue_script

= 1.2 =
* error resolved: change FQDN dev.p3chat.com to p3chat.com in the script url

= 1.1 =
* shortcode [ p3chat-button ] for static chat launcher button

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.0 =
This version is first release of this plugin.

== ToDo ==
The next version or later:

1. images for buttons
1. auto registration at p3chat.com (by open-id) 
