=== Plugin Name ===
Contributors: tox82
Donate link: http://cookie-bar.eu
Tags: cookie, law, cookielaw, cookiebar
Requires at least: 3.0.1
Tested up to: 4.5.2
Stable tag: 1.5.15
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


With EU cookie law, site owners need to make the use of cookies very obvious to visitors. cookieBAR does just that.


== Description ==

There is a lot of mystery and fuss surrounding the new EU cookie legislation, but it's essentially really simple. Cookies are files used to track site activity and most websites use them. Site owners need to make the use of cookies very obvious to visitors.

Cookie bar makes it simple and clear to visitors that cookies are in use and tells them how to adjust browser settings if they are concerned.

cookieBAR is a drop-in and forget, pure vanilla javascript plugin, with no jQuery nor any other dependency needed. It shows up when needed and stay silent when not: if a website has a cookie or some localStorage data set then the bar is shown, otherwhise nothing happens.

Once user clicks Allow Cookies cookieBAR will set a cookie for that domain with a name cookiebar that will expire in 30 days. What this means is that the plugin will only show up once month (this duration is configurable).

If a user decides to click Disallow Cookies, cookieBAR will simply remove all cookies and localStorage data (and will show up again the first time a cookie is detected).

cookieBAR is currently translated in:

* Catalan
* Danish
* Dutch
* English
* French
* German
* Hungarian
* Italian
* Spanish
* Portuguese
* Romanian

== Installation ==

1. Upload `cookiebar-wp.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use the configuration panel to set your desired configurations

== Frequently Asked Questions ==

= Which languages are supported? =
cookieBAR is currently translated in:

* Catalan
* Danish
* Dutch
* English
* French
* German
* Hungarian
* Italian
* Spanish
* Portuguese
* Romanian

= I don't like the standard black bar, how can I change its appearance? =
You can go to `Appearance - cookieBAR` and pick another theme.

= How do I configure cookieBAR's behavior? =
You can go to `Appearance - cookieBAR` and have a look to the configuration options.

== Upgrade Notice ==

.

== Screenshots ==

1. The cookieBAR
2. Details panel

== Changelog ==

= 1.5.15 =
* Added Danish translation
* New: Added Alternative Black theme
* Bugfix: Fixed Romanian translation

= 1.5.14 =
* Switched from freegeoip.net to freegeoip.io

= 1.5.13 =
* Added Romanian translation

= 1.5.12 =
* New: Added "Accept cookies by scrolling window" option.
* Bugfix: "Always show cookie bar" option was not correctly working.
* Bugfix: Minified CSS files where present, but not in use.
* Misc: The startup process code was a bit messy, I did my best to clean it a bit, it should be easier to review and edit, if needed.

= 1.5.11 =
* New: All links in the cookiebar's language files are now set with rel="nofollow"
* New: Fix for body margins

= 1.5.10 =
* Added Portuguese translation

= 1.5.9 =
* Added Catalan and Spanish translation

= 1.5.8 =
* Added the Dutch translation

= 1.5.7 =
* Switched back from telize to freegeoip.

= 1.5.6 =
* Improved the French translation

= 1.5.5 =
* Removed the 'http' prefix for telize api

= 1.5.4 =
* Switched to Telize for ip geolocation

= 1.5.3 =
* New: Added minified CSS and JS
* Bugfix: cookieLawStates should include GB not UK
* Bugfix: When hiding the cookieBar, margins are not correctly reset

= 1.5.2 =
* New: Added Hungarian translation
* New: Add support for German
* Bugfix: Fix prompt for no consent
* Bugfix: Minor fixes and improvements

= 1.5.1 =
* New: Geolocation - show the bar only in countries affected by the cookie law
* New: Specify which kind of cookies are in use (technical, third party, tracking cookies)
* New: The link to the complete privacy page should be in the main banner
* Bugfix: cookieBAR's cookie is now set at domain level

= 1.4.0 =
* Some new configuration options: always show and remember choice duration

= 1.3.0 =
* Added themes functionality and two more bundled themes

= 1.2 =
* Fixed a css bug that made the bar unreadable in some websites, some minor fixes

= 1.1.2 =
* Fixed a bug with Internet Explorer and custom privacy page

= 1.1 =
* Set your own privacy page, or use the default popup

= 1.0 =
* First working version
