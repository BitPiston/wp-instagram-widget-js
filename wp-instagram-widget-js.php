<?php
/*
Plugin Name: WP Instagram Widget JS
Plugin URI: https://github.com/bitpiston/wp-instagram-widget-js
Description: A client side drop-in replacement for Scott Evans's WP Instagram Widget plugin.
Version: 3.0
Author: BitPiston Studios
Author URI: https://bitpiston.com
Text Domain: wp-iwjs
Domain Path: /lang/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: bitpiston/wp-instagram-widget-js

Copyright © 2020 BitPiston Studios
Copyright © 2013 Scott Evans

This program is free software; you can redistribute it and/or modify it under the terms of 
the GNU General Public License as published by the Free Software Foundation; either version 2 
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See 
the GNU General Public License for more details.

*/

function wpiwjs_init() {

	// define some constants.
	define( 'WP_IWJS_JS_URL', plugins_url( '/js', __FILE__ ) );
	define( 'WP_IWJS_PATH', dirname( __FILE__ ) );
	define( 'WP_IWJS_BASE', plugin_basename( __FILE__ ) );
	define( 'WP_IWJS_FILE', __FILE__ );

	// load language files.
	load_plugin_textdomain( 'wp-iwjs', false, dirname( WP_IWJS_BASE ) . '/lang/' );
}
add_action( 'init', 'wpiwjs_init' );

function wpiwjs_widget() {
	register_widget( 'null_instagram_widget' );
}
add_action( 'widgets_init', 'wpiwjs_widget' );

function wpiwjs_loaded() {

	// deactivate original plugin if loaded
	if ( class_exists( 'null_instagram_widget' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		deactivate_plugins( 'wp-instagram-widget/wp-instagram-widget.php', true );
	}
}
add_action( 'plugins_loaded', 'wpiwjs_loaded' );

Class null_instagram_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'null-instagram-feed',
			__( 'Instagram', 'wp-iwjs' ),
			array(
				'classname' => 'null-instagram-feed',
				'description' => esc_html__( 'Displays your latest Instagram photos', 'wp-iwjs' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	function widget( $args, $instance ) {
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$options = [
            'container' => empty( $instance['container'] ) ? '#' . $this->id . '.null-instagram-feed' : $instance['container'],
			'username' => empty( $instance['username'] ) ? '' : $instance['username'],
			'limit' => empty( $instance['number'] ) ? 9 : $instance['number'],
			'size' => empty( $instance['size'] ) ? 'large' : $instance['size'],
			'target' => empty( $instance['target'] ) ? '_self' : $instance['target'],
			'link' => empty( $instance['link'] ) ? '' : $instance['link'],
			'images_only' => apply_filters( 'wpiw_images_only', false ),
			'cache' => apply_filters( 'null_instagram_cache_time', 3600 ),
			'classes' => [
				'list' => [
					'ul' => apply_filters( 'wpiw_list_class', 'instagram-pics instagram-size-' . $size ),
					'li' => apply_filters( 'wpiw_item_class', '' ),
					'a' => apply_filters( 'wpiw_a_class', '' ),
					'img' => apply_filters( 'wpiw_img_class', '' )
				],
				'link' => [
					'p' => apply_filters( 'wpiw_link_class', 'clear' ),
					'a' => apply_filters( 'wpiw_linka_class', '' )
				]
			]
		];

		echo $args['before_widget'];

		if ( ! empty( $title ) ) { echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title']; };

		do_action( 'wpiw_before_widget', $instance );

		wp_enqueue_script( 'wp-iwjs', WP_IWJS_JS_URL . '/wp-instagram-widget.js', ['jquery'], '3.0' );
   		wp_add_inline_script( 'wp-iwjs', '(function($){ $(function() { $.wpInstagramWidget(' . wp_json_encode( $options ) . '); }); })(jQuery);' );

   		echo '<ul class="' . esc_attr( $options['classes']['list']['ul'] ) . '"></ul>';

		do_action( 'wpiw_after_widget', $instance );

		echo $args['after_widget'];
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => __( 'Instagram', 'wp-iwjs' ),
			'username' => '',
            'container' => '#' . $this->id . '.null-instagram-feed',
			'size' => 'large',
			'link' => __( 'Follow Me!', 'wp-iwjs' ),
			'number' => 9,
			'target' => '_self',
		) );
		$title = $instance['title'];
		$username = $instance['username'];
        $container = $instance['container'];
		$number = absint( $instance['number'] );
		$size = $instance['size'];
		$target = $instance['target'];
		$link = $instance['link'];
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'wp-iwjs' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e( '@username or #tag', 'wp-iwjs' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" /></label></p>
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'container' ) ); ?>"><?php esc_html_e( 'HTML Container class', 'wp-instagram-widget-js' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'container' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'container' ) ); ?>" type="text" value="<?php echo esc_attr( $container ); ?>" /></label></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of photos', 'wp-iwjs' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" /></label></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e( 'Photo size', 'wp-iwjs' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" class="widefat">
				<option value="thumbnail" <?php selected( 'thumbnail', $size ); ?>><?php esc_html_e( 'Thumbnail', 'wp-iwjs' ); ?></option>
				<option value="small" <?php selected( 'small', $size ); ?>><?php esc_html_e( 'Small', 'wp-iwjs' ); ?></option>
				<option value="large" <?php selected( 'large', $size ); ?>><?php esc_html_e( 'Large', 'wp-iwjs' ); ?></option>
				<option value="original" <?php selected( 'original', $size ); ?>><?php esc_html_e( 'Original', 'wp-iwjs' ); ?></option>
			</select>
		</p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Open links in', 'wp-iwjs' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" class="widefat">
				<option value="_self" <?php selected( '_self', $target ); ?>><?php esc_html_e( 'Current window (_self)', 'wp-iwjs' ); ?></option>
				<option value="_blank" <?php selected( '_blank', $target ); ?>><?php esc_html_e( 'New window (_blank)', 'wp-iwjs' ); ?></option>
			</select>
		</p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link text', 'wp-iwjs' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" /></label></p>
		<?php

	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = trim( strip_tags( $new_instance['username'] ) );
        $instance['container'] = trim( strip_tags( $new_instance['container'] ) );
		$instance['number'] = ! absint( $new_instance['number'] ) ? 9 : $new_instance['number'];
		$instance['size'] = ( ( 'thumbnail' === $new_instance['size'] || 'large' === $new_instance['size'] || 'small' === $new_instance['size'] || 'original' === $new_instance['size'] ) ? $new_instance['size'] : 'large' );
		$instance['target'] = ( ( '_self' === $new_instance['target'] || '_blank' === $new_instance['target'] ) ? $new_instance['target'] : '_self' );
		$instance['link'] = strip_tags( $new_instance['link'] );

		return $instance;
	}
}
