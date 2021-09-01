<?php
/**
 * FreedomTheme enqueue scripts
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'classes/swiper.php';

function cf_theme_scripts() {
	cf_enqueue_style( 'cf-theme-styles', get_template_directory_uri() . '/css/theme.min.css' );

	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'swiper', 'https://unpkg.com/swiper/swiper-bundle.min.js' );
	cf_enqueue_script( 'cf-theme-scripts', get_template_directory_uri() . '/js/theme.min.js', ['swiper'], true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'cf_theme_scripts' );

// editor scripts
function cf_frontend_editor() {
	cf_enqueue_style( 'cf-blocks-css', get_stylesheet_directory_uri() . '/js/blocks.css' );
	cf_enqueue_script( 'cf-blocks-js', get_stylesheet_directory_uri() .  '/js/blocks.js', ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'] );
}
add_action( 'enqueue_block_assets', 'cf_frontend_editor' );

function cf_context() {

	// Dynamic styles
	do_action( 'cf_render_dynamic_styles' );


	$swiper_block = 'cf/swiper';
	$post 				= get_post();
	$blocks 			= parse_blocks( $post->post_content );

	if ( ! is_array( $blocks ) || empty( $blocks ) ) {
		return;
	}

	$swiper_blocks = [];

	// Check for swiper block
	if( has_block( $swiper_block ) ) {

		foreach( $blocks as $block ) {
			$x = cf_find_block( $block, $swiper_block );
			if( $x ) $swiper_blocks = array_merge($swiper_blocks, $x);
		}

	}

	// Check for reusable swiper blocks
	foreach ( $blocks as $block ) {

		$reusable_block_id = cf_find_reusable_block( $block );

		if( $reusable_block_id ) {

			$reusable_block_post 	= get_post( $reusable_block_id );
			$reusable_blocks 			= parse_blocks( $reusable_block_post->post_content );

			foreach( $reusable_blocks as $block ) {
				$x = cf_find_block( $block, $swiper_block );
				if( $x ) $swiper_blocks = array_merge($swiper_blocks, $x);
			}

		}
	}

	$template_blocks = apply_filters( 'cf_template_blocks', [] );

	foreach ( $template_blocks as $template_id ) {

		$template = get_post( $template_id );
		$blocks 	= parse_blocks( $template->post_content );

		if( has_block( $swiper_block, $template ) ) {

			foreach( $blocks as $block ) {
				$x = cf_find_block( $block, $swiper_block );
				if( $x ) $swiper_blocks = array_merge($swiper_blocks, $x);
			}

		}


	}

	// If there are no swiper blocks return
	if( empty( $swiper_blocks ) ) return;

	/**
	 * Namespace
	 *
	 * @var string
	 */
	$ns 						= 'CF';
	$object 				= 'window.' . $ns;
	$context 				= $object . '.context';
	$check_pattern 	= 'if(!%1$s) %1$s = {};';

	$script 				= sprintf( $check_pattern, $object );
	$script 				.= sprintf( $check_pattern, $context );

	foreach( $swiper_blocks as $block ) {
		$swiper_options = CF_Swiper::get_swiper_options( $block );
		$container_id 	= $swiper_options['container_id'];
		$item_pattern 	= $context . '["%s"] = %s;';

		$script 				.= sprintf( $item_pattern, $container_id, json_encode( $swiper_options ) );
	}

	$script = sprintf( '<script>%s</script>', $script );

	print $script;
}
add_action( 'wp_head', 'cf_context' );
