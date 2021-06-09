<?php
/**
 * FreedomTheme modify editor
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Registers an editor stylesheet for the theme.
 */
function cf_wpdocs_theme_add_editor_styles() {
	add_editor_style( 'css/custom-editor-style.min.css' );
}

add_action( 'admin_init', 'cf_wpdocs_theme_add_editor_styles' );

/**
 * Reveals TinyMCE's hidden Style dropdown.
 *
 * @param array $buttons Array of Tiny MCE's button ids.
 * @return array
 */
function cf_tiny_mce_style_formats( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}

add_filter( 'mce_buttons_2', 'cf_tiny_mce_style_formats' );

/**
 * Adds style options to TinyMCE's Style dropdown.
 *
 * @param array $settings TinyMCE settings array.
 * @return array
 */
function cf_tiny_mce_before_init( $settings ) {

	$style_formats = [
		[
			'title'    => 'Lead Paragraph',
			'selector' => 'p',
			'classes'  => 'lead',
			'wrapper'  => true,
		],
		[
			'title'  => 'Small',
			'inline' => 'small',
		],
		[
			'title'   => 'Blockquote',
			'block'   => 'blockquote',
			'classes' => 'blockquote',
			'wrapper' => true,
		],
		[
			'title'   => 'Blockquote Footer',
			'block'   => 'footer',
			'classes' => 'blockquote-footer',
			'wrapper' => true,
		],
		[
			'title'  => 'Cite',
			'inline' => 'cite',
		],
	];

	if ( isset( $settings['style_formats'] ) ) {
		$orig_style_formats = json_decode( $settings['style_formats'], true );
		$style_formats      = array_merge( $orig_style_formats, $style_formats );
	}

	$settings['style_formats'] = wp_json_encode( $style_formats );
	return $settings;
}

add_filter( 'tiny_mce_before_init', 'cf_tiny_mce_before_init' );
