=== Pixpie - Intelligent Image Compression ===
Contributors: pixpie
Donate link: https://www.pixpie.co
Tags: pixpie, optimize, compress, shrink, resize, faster, fit, scale, improve, images, jpeg, jpg, png, lossy, minify, smush, save, bandwidth, website, speed, performance, panda, wordpress app, optimize image, resize image, crop image, lossless
Requires at least: 4.4
Tested up to: 4.7
Stable tag: 1.1.3
License: LGPLv2.1 or later
License URI: https://www.gnu.org/licenses/lgpl-2.1.html

== Description ==

Make your website faster by optimizing your new and existing WordPress image uploads through Pixpie API.
This plugin automatically optimizes all your new pictures. Supported image formats are JPEG, PNG. Maximum file size is 25 MB.

For more details, including detailed documentation and plans and pricing, please visit pixpie.co.

== Features ==

- Plugin optimizes images uploaded to the Media Library on-the-fly. All generated thumbnails are optimized too.
- Plugin performs "intelligent lossy" compression, so resulting image is visually identical to the original. (SSIM index of resulting image is 0.95 or higher)
- Fast bulk optimization of your existing media library.
- You can use your Pixpie API key and secret on as many sites/blogs as you like. No per-site license.
- Convert CMYK to RGB to save storage space and maximize compatibility.
- Optimize and resize uploads with the WordPress mobile app.
- Statistics page with your total savings.
- The plugin keeps originals by default, so it's easy to roll back the changes.
- The plugin does not require any root or command-line access.
- WooCommerce compatible.
- WP Retina 2x compatible.
- 25 MB file size limit.
- No need to compile and install any binaries.

== Getting started ==

Install this plugin and obtain your free API key following [Pixpie WordPress plugin guide](https://pixpie.atlassian.net/wiki/display/DOC/Wordpress+plugin). You can optimize approximately 100 images on Free plan each month (for regular WordPress installation).
The exact result depends on the number of thumbnail sizes that are in use in your WordPress installation. For a small additional fee, you can optimize more images per month.

== How does it work? ==

Each time you upload an image to your WordPress site, it is sent to the Pixpie Cloud. Cloud analyzes you picture using artificial intelligence algorithms and applies best possible optimization.
The result is saved in your WordPress Media Library. The original image can be automatically backed up or replaced by optimized image. Average JPEG file is compressed by 40%, PNG file by 50%, without visible loss in quality.
Your website will load faster, have a higher ranking in search engines, save bandwidth, and storage space!

== Optimizing all your images ==

You can compress all your JPEG and PNG images at once. To do that, go to WP Pixpie Plugin >  Convert All Images.

== Multisite support ==

You can use one API key on as many of your sites as you want. Tarif plans are based on actions with images.

== Contact us ==

Got questions or feedback? Let us know! Contact us at support@pixpie.co or find us on [Facebook](http://facebook.com/PixpieCo).

== Screenshots ==

1. Dashboard: See optimization metrics
2. Plugin Settings: Setup Pixpie API Key & Secret
3. See All Images: Compare original and optimized images
4. Convert All images: bulk optimization of all site images
5. Roll Back Screen: Revert unwanted changes
6. Log Screen: See Plugin Execution Log

== Contributors ==

Want to contribute? Check out the [Pixpie WordPress plugin on GitHub](https://github.com/PixpieCo/wordpress-plugin).

== Installation ==

= From your WordPress dashboard =

1. Visit *Plugins > Add New*.
2. Search for 'pixpie' and press the 'Install Now' button for the plugin named 'Pixpie – Intelligent Image Compression' by 'Pixpie'.
3. Activate the plugin from your Plugins page.
4. Go to the *WP Pixpie Plugin > Settings* page and register a new Pixpie account.
5. Or enter the Bundle ID and Secret key if you already have an account (https://pixpie.atlassian.net/wiki/display/DOC/WordPress+plugin#WordPressplugin-activation).
6. Go to *WP Pixpie Plugin > Convert All Images* and convert all your images!

= From WordPress.org =

1. Download the plugin named 'Pixpie – Intelligent Image Compression' by 'Pixpie'.
2. Upload the `wp-pixpie-plugin` directory to your `/wp-content/plugins/` directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate the plugin from your Plugins page.
4. Go to the *WP Pixpie Plugin > Settings* page and register a new Pixpie account.
5. Or enter the Bundle ID and Secret key if you already have an account (https://pixpie.atlassian.net/wiki/display/DOC/WordPress+plugin#WordPressplugin-activation).
6. Go to *WP Pixpie Plugin > Convert All Images* and convert all your images!

== Frequently Asked Questions ==

Please read up-to-date [FAQ](https://blog.pixpie.co/faq/) on [pixpie.co](https://www.pixpie.co)

== Upgrade Notice ==
Coming soon...

== Changelog ==
= 1.1.3 =
* Fixing issue with update.

= 1.1.2 =
* Fixed UX issues on Settings page.

= 1.1.1 =
* Updated registration form on Settings page, improved UX.
* Added more statistic, sending to Pixpie, including attempts.

= 1.1.0 =
* Added registration form through Settings page.

= 1.0.0 =
* Initial release