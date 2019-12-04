=== Slide Posts with Ajax Pagination ===
Contributors: Andrey Raychev
Donate link: http://bettermonday.org
Tags: AJAX, ajax pagination, ajax load posts, category, navigation, pagination, paging, post, posts, gallery, shortcode, thumbnails
Tested up to: 5.3
Requires at least: 4.1 or higher
Stable tag: 1.0.0
License: GPL2


Adds posts from custom category with ajax pagination

== Description ==

With this plugin one can easily convert a wordpress posts category into a slideshow of images or just slide through the pages of a category without reloading the pages.
This can allow you to integrate a small gallery and show posts in a grid layout with their featured images. You can show the plugin inside a particular page or post without writing a single line of code. Simply specify the desired category and number of post shown per page from the settings page of the plugin under "Settings" > "SlidePosts". If the plugin is used as a gallery each post of the category should have a featured image.


== Installation ==

Upload the files of the plugin inside a slideposts-ajax-pagination folder to the /wp-content/plugins/ directory.
Activate the plugin through the 'Plugins' menu in WordPress.
------
You will find 'SlidePosts' submenu in your WordPress > Settings admin panel.
In the plugin settings page you should specify the category slug name and the number of post shown per page. Each post in the specified category with "published" status will be included. 
The posts can be shown as a grid of thumbnails  - showing the featured images and titles of the post on hover state.
Keep in mind that when SlidePosts is in gallery layout the thumbnails of the featured images is used - so it is recommended to set the minimum thumbnail size to be bigger - 300-400px is large enough. The Thumbnail size can be set from Media Settings in "Settings" > "Media".
------

To insert the SlidePosts content into your post or page, copy the shortcode [postslist] and paste it into the post/page content.
To embed the plugin into template file you will need to pass the shortcode into do_shortcode() function and display its output like this: <?php echo do_shortcode('[postslist]'); ?>


The shortcode will load posts from custom category specified in the plugin settings page.

== Upgrade Notice ==

= 1.0 =
First stable release

== Screenshots ==

1. Admin screen.

== Changelog ==

= 1.0.0 =
Adds posts from custom category with ajax pagination