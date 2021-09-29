<?php
/**
 * Declaring widgets
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

add_action( 'widgets_init', 'cf_widgets_init' );

/**
 * Initializes themes widgets.
 */
function cf_widgets_init() {

	register_sidebar(
		[
			'name'          => __( 'Header', 'freedomtheme' ),
			'id'            => 'headerfull',
			'description'   => __( 'Full sized header widget', 'freedomtheme' ),
			'before_widget' => '<div id="%1$s" class="sidebar-widgets header-widget %2$s dynamic-classes">',
			'after_widget'  => '</div><!-- .footer-widget -->',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]
	);
	register_sidebar(
		[
			'name'          => __( 'Footer', 'freedomtheme' ),
			'id'            => 'footerfull',
			'description'   => __( 'Full sized footer widget', 'freedomtheme' ),
			'before_widget' => '<div id="%1$s" class="sidebar-widgets footer-widget %2$s dynamic-classes">',
			'after_widget'  => '</div><!-- .footer-widget -->',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		]
	);
	
}
