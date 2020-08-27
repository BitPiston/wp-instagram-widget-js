# WP Instagram Widget JS

## About

WP Instagram Widget JS is a client side drop-in replacement for Scott Evans's WP Instagram Widget plugin. It does not require you to provide your login details or sign in via oAuth nor does it use the soon to be dead Instagram API.

The widget is built to mirror the original as closely as possible which was built with the following philosophy:

* Use sensible and simple markup
* Provide no styles/css - it is up to you to style the widget to your theme and taste
* Cache where possible - filters are provided to adjust cache timings
* Require little setup - avoid oAuth for example

## Installation

To install this plugin:

* Upload the `wp-instagram-widget-js` folder to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress
* That's it!

Alternatively you can install the plugin using composer:

```
composer require bitpiston/wp-instagram-widget-js
```

Visit [WordPress.org for a comprehensive guide](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation) on in how to install WordPress plugins.

## Hooks & Filters

The following filters from version 2.0 and earlier are supported:

The first allows you adjust that cache time for retrieving the images from Instagram:

```
add_filter('null_instagram_cache_time', 'my_cache_time');

function my_cache_time() {
	return HOUR_IN_SECONDS;
}
```

The second allows you to filter video results from the widget:

```
add_filter('wpiw_images_only', '__return_true');
```

The rest allow you to add custom classes to the [ul] list container, each list item, link or image:

```
add_filter( 'wpiw_list_class', 'my_instagram_class' );

add_filter( 'wpiw_item_class', 'my_instagram_class' );
add_filter( 'wpiw_a_class', 'my_instagram_class' );
add_filter( 'wpiw_img_class', 'my_instagram_class' );
add_filter( 'wpiw_linka_class', 'my_instagram_class' );

function my_instagram_class( $classes ) {
	$classes = "instagram-image";
	return $classes;
}
```

In version 1.3 you also have two new hooks for adding custom output before and after the widget:

```
wpiw_before_widget
wpiw_after_widget
```

The following filters are no longer supported as it is not possible to provide a drop-in compatible equivelent:

In version 1.4 and above you can also customise the image loop completely by creating a `parts/wp-instagram-widget.php` file in your theme.

In version 1.9.6 you can now use a filter to change the location of your custom template part: `wpiw_template_part`.

## Frequently Asked Questions

...

## Changelog

#### 3.0
* Rewritten to scrape client side
