<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Server-side rendering of the `cf/latest-content` block.
 *
 * @package WordPress
 */
require_once get_template_directory() . '/inc/blocks/latest-content.php';

function cf_bootstrap_child_register_acf_block_types() {

  acf_register_block_type( [
    'name'              => 'post-grid',
    'title'             => __('Post Grid'),
    'description'       => __('Grid of posts.'),
    'render_template'   => 'block-templates/post-grid.php',
    'category'          => 'widgets',
    'icon'              => 'grid-view',
    'keywords'          => ['posts', 'grid', 'card'],
  ] );

}

if( function_exists( 'acf_register_block_type' ) ) {
  add_action( 'acf/init', 'cf_bootstrap_child_register_acf_block_types' );
}
