<?php
/**
 * Theme basic setup
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

add_action( 'after_setup_theme', 'cf_theme_setup' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function cf_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		*/
	load_theme_textdomain( 'freedomtheme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		[
			'primary' => __( 'Primary Menu', 'freedomtheme' ),
			'footer-links' => __( 'Footer menu', 'freedomtheme' ),
		]
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		]
	);

	/*
		* Adding Thumbnail basic support
		*/
	add_theme_support( 'post-thumbnails' );

	/*
		* Adding support for Widget edit icons in customizer
		*/
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
		* Enable support for Post Formats.
		* See http://codex.wordpress.org/Post_Formats
		*/
	add_theme_support(
		'post-formats',
		[
			'aside',
			'image',
			'video',
			'quote',
			'link',
		]
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'cf_custom_background_args',
			[
				'default-color' => 'ffffff',
				'default-image' => '',
			]
		)
	);

	// Register support for Gutenberg wide images in your theme
	add_theme_support( 'align-wide' );

	// Set up the WordPress Theme logo feature.
	add_theme_support( 'custom-logo' );

	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	add_theme_support( 'editor-styles' );

	// Check and setup theme default settings.
	cf_setup_theme_default_settings();

	// Setup colours
	$colours = [
		'coral' => '#FF7A80',
		'blue' => '#8BBBE6',
		'black' => '#000',
		'white' => '#FFF',
	];

	add_theme_support( 'editor-color-palette', cf_theme_var_map( $colours, 'color' ) );

	// Setup gradients
	$gradients = [
		'coral' =>    ['#F47598','#FF9474'],
		'blue' =>    ['#A7D9E7','#638FE1'],
		'green' =>    ['#A9E993','#76BD94'],
	];

	foreach($gradients as $label => $colours) {
			$gradient_map[] = array(
					'name'     => $label,
					'gradient' => 'linear-gradient(175deg,'.$colours[0].' 0%,'.$colours[1].' 100%)',
					'slug'     => strtolower(str_replace(' ', '-', $label)),
			);
	}

	if(! empty($gradient_map) ) {
			add_theme_support( 'editor-gradient-presets', $gradient_map );
	}

	// Setup Sizes
	$sizes = [
		'Small'		=>	12,
		'Regular'	=>	16,
		'Large'		=>	36,
		'Huge'		=>	50
	];

	add_theme_support( 'editor-font-sizes', cf_theme_var_map( $sizes, 'size' ) );

}


add_filter( 'excerpt_more', 'cf_custom_excerpt_more' );

/**
 * Removes the ... from the excerpt read more link
 *
 * @param string $more The excerpt.
 *
 * @return string
 */
function cf_custom_excerpt_more( $more ) {
	if ( ! is_admin() ) {
		$more = '';
	}
	return $more;
}

add_filter( 'wp_trim_excerpt', 'cf_all_excerpts_get_more_link' );

/**
 * Adds a custom read more link to all excerpts, manually or automatically generated
 *
 * @param string $post_excerpt Posts's excerpt.
 *
 * @return string
 */
function cf_all_excerpts_get_more_link( $post_excerpt ) {
	if ( ! is_admin() ) {
		$post_excerpt = $post_excerpt . ' [...]<p><a class="btn btn-secondary freedomtheme-read-more-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __(
			'Read More...',
			'freedomtheme'
		) . '</a></p>';
	}
	return $post_excerpt;
}

/**
* Add custom logo to wordpress login screen
*/
function cf_login_enqueue_scripts() {
	// Change login logo
	if( has_custom_logo()) {
		$styles = [
				'width'						=> '175px',
				'height'					=> '125px',
				'background-size'	=> '100%',
		];

		$css = sprintf(
				'#login h1 a, .login h1 a { background-image: url(%s); %s }',
				wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full', false ),
				implode( ';', array_map( function( $key ) use( $styles ) {
						return "{$key}: {$styles[$key]}";
				}, array_keys( $styles ) ) )
		);
		printf( '<style type="text/css">%s</style>', $css );
	}
}
add_action( 'login_enqueue_scripts', 'cf_login_enqueue_scripts', 20 );

/**
 * Enqueue Google Fonts
 */
function cf_child_load_google_fonts() {

	$fonts = [
		"Hind" => [300, 700]
	];

	$font_string = "";

	foreach( $fonts as $font => $weights ) {
		$font_string .= ! empty( $font_string ) ? "|". urlencode( $font ) : urlencode( $font );
		if( is_array( $weights ) && count( $weights ) > 0 ) $font_string .= ":" . implode( ",", $weights );
	}

	if( ! empty( $font_string ) )
		cf_enqueue_style( 'google-fonts', "//fonts.googleapis.com/css?family={$font_string}&display=swap" );

}

add_action( 'wp_enqueue_scripts', 'cf_child_load_google_fonts' );
add_action( 'admin_enqueue_scripts', 'cf_child_load_google_fonts' );
