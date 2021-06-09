<?php
/**
 * FreedomTheme enqueue scripts
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function cf_theme_scripts() {
	cf_enqueue_style( 'cf-theme-styles', get_template_directory_uri() . '/css/theme.min.css' );

	wp_enqueue_script( 'jquery' );

	cf_enqueue_script( 'cf-theme-scripts', get_template_directory_uri() . '/js/theme.min.js', [], true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'cf_theme_scripts' );

// editor scripts
function cf_block_editor( $hook ) {
	cf_enqueue_script( 'cf-block-variants-js', get_stylesheet_directory_uri() .  '/inc/assets/cf-block-variants.js' );
}

add_action('enqueue_block_editor_assets', 'cf_block_editor');

// frontend and editor scripts
function cf_frontend_editor( $hook ) {
	cf_enqueue_style( 'cf-block-variants-css', get_stylesheet_directory_uri() . '/inc/assets/cf-block-variants.css' );
}

add_action('enqueue_block_assets', 'cf_frontend_editor');
