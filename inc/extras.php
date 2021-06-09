<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function cf_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'cf_body_classes' );



/**
 * Replaces logo CSS class.
 *
 * @param string $html Markup.
 *
 * @return string
 */
function cf_change_logo_class( $html ) {

	$html = str_replace( 'class="custom-logo"', 'class="img-fluid"', $html );
	$html = str_replace( 'class="custom-logo-link"', 'class="navbar-brand custom-logo-link"', $html );
	$html = str_replace( 'alt=""', 'title="Home" alt="logo"', $html );

	return $html;
}
// Filter custom logo with correct classes.
add_filter( 'get_custom_logo', 'cf_change_logo_class' );

/**
 * Display navigation to next/previous post when applicable.
 */
function cf_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="container navigation post-navigation">
		<h2 class="sr-only"><?php esc_html_e( 'Post navigation', 'freedomtheme' ); ?></h2>
		<div class="row nav-links justify-content-between">
			<?php
			if ( get_previous_post_link() ) {
				previous_post_link( '<span class="nav-previous">%link</span>', _x( '<i class="fa fa-angle-left"></i>&nbsp;%title', 'Previous post link', 'freedomtheme' ) );
			}
			if ( get_next_post_link() ) {
				next_post_link( '<span class="nav-next">%link</span>', _x( '%title&nbsp;<i class="fa fa-angle-right"></i>', 'Next post link', 'freedomtheme' ) );
			}
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 * Add mobile-web-app meta.
 */
function cf_mobile_web_app_meta() {
	print '<meta name="mobile-web-app-capable" content="yes">' . "\n";
	print '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
	print '<meta name="apple-mobile-web-app-title" content="' . esc_attr( get_bloginfo( 'name' ) ) . ' - ' . esc_attr( get_bloginfo( 'description' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'cf_mobile_web_app_meta' );

/**
 * Adds schema markup to the body element.
 *
 * @param array $atts An associative array of attributes.
 * @return array
 */
function cf_default_body_attributes( $atts ) {
	$atts['itemscope'] = '';
	$atts['itemtype']  = 'http://schema.org/WebSite';
	return $atts;
}
add_filter( 'cf_body_attributes', 'cf_default_body_attributes' );



/**
 * Escapes the description for an author or post type archive.
 *
 * @param string $description Archive description.
 * @return string Maybe escaped $description.
 */
function cf_escape_the_archive_description( $description ) {
	if ( is_author() || is_post_type_archive() ) {
		return wp_kses_post( $description );
	}

	/*
		* All other descriptions are retrieved via term_description() which returns
		* a sanitized description.
		*/
	return $description;
}

add_filter( 'get_the_archive_description', 'cf_escape_the_archive_description' );

/**
 * Sanitizes data for allowed HTML tags for post title.
 *
 * @param string $data Post title to filter.
 * @return string Filtered post title with allowed HTML tags and attributes intact.
 */
function cf_kses_title( $data ) {
	// Tags not supported in HTML5 are not allowed.
	$allowed_tags = [
		'abbr'             => [],
		'aria-describedby' => true,
		'aria-details'     => true,
		'aria-label'       => true,
		'aria-labelledby'  => true,
		'aria-hidden'      => true,
		'b'                => [],
		'bdo'              => [
			'dir' => true,
		],
		'blockquote'       => [
			'cite'     => true,
			'lang'     => true,
			'xml:lang' => true,
		],
		'cite'             => [
			'dir'  => true,
			'lang' => true,
		],
		'dfn'              => [],
		'em'               => [],
		'i'                => [
			'aria-describedby' => true,
			'aria-details'     => true,
			'aria-label'       => true,
			'aria-labelledby'  => true,
			'aria-hidden'      => true,
			'class'            => true,
		],
		'code'             => [],
		'del'              => [
			'datetime' => true,
		],
		'ins'              => [
			'datetime' => true,
			'cite'     => true,
		],
		'kbd'              => [],
		'mark'             => [],
		'pre'              => [
			'width' => true,
		],
		'q'                => [
			'cite' => true,
		],
		's'                => [],
		'samp'             => [],
		'span'             => [
			'dir'      => true,
			'align'    => true,
			'lang'     => true,
			'xml:lang' => true,
		],
		'small'            => [],
		'strong'           => [],
		'sub'              => [],
		'sup'              => [],
		'u'                => [],
		'var'              => [],
	];
	$allowed_tags = apply_filters( 'cf_kses_title', $allowed_tags );

	return wp_kses( $data, $allowed_tags );
}


// Escapes all occurances of 'the_title()' and 'get_the_title()'.
add_filter( 'the_title', 'cf_kses_title' );

// Escapes all occurances of 'the_archive_title' and 'get_the_archive_title()'.
add_filter( 'get_the_archive_title', 'cf_kses_title' );
