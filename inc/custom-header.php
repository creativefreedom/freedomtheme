<?php
/**
 * Custom header setup
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'cf_custom_header_setup' );

if ( ! function_exists( 'cf_custom_header_setup' ) ) {
	/**
	 * Set up custom header feature.
	 */
	function cf_custom_header_setup() {

		/**
		 * Filter FreedomTheme custom-header support arguments.
		 *
		 * @param array $args {
		 *     An array of custom-header support arguments.
		 *
		 *     @type string $default-image          Default image of the header.
		 *     @type string $default_text_color     Default color of the header text.
		 *     @type int    $width                  Width in pixels of the custom header image. Default 954.
		 *     @type int    $height                 Height in pixels of the custom header image. Default 1300.
		 *     @type string $wp-head-callback       Callback function used to styles the header image and text
		 *                                          displayed on the blog.
		 *     @type string $flex-height            Flex support for height of header.
		 * }
		 */
		add_theme_support(
			'custom-header',
			apply_filters(
				'cf_custom_header_args',
				[
					'default-image' => get_parent_theme_file_uri( '/img/header.jpg' ),
					'width'         => 2000,
					'height'        => 1200,
					'flex-height'   => true,
				]
			)
		);

		register_default_headers(
			[
				'default-image' => [
					'url'           => '%s/img/header.jpg',
					'thumbnail_url' => '%s/img/header.jpg',
					'description'   => __( 'Default Header Image', 'freedomtheme' ),
				],
			]
		);
	}
}
