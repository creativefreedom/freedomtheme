<?php
/**
 * FreedomTheme helper functions
 *
 * @package FreedomTheme
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function cf_is_external_url( $url, $home_url ) {
  $delimiter = '~';
  $pattern_home_url = preg_quote( $home_url, $delimiter );
  $pattern = $delimiter . '^' . $pattern_home_url . $delimiter . 'i';
  return ! preg_match( $pattern, $url );
}

function cf_generate_file_version( $src ) {
  // Normalize both URLs in order to avoid problems with http, https
  // and protocol-less cases.
  $src = preg_replace( '~^https?:~i', '', $src );
  $home_url = preg_replace( '~^https?:~i', '', WP_CONTENT_URL );
  $version = false;

  if ( ! cf_is_external_url( $src, $home_url ) ) {
      // Generate the absolute path to the file.
      $file_path = preg_replace(
          '~[' . preg_quote( '/\\', '~' ) . ']+~',
          DIRECTORY_SEPARATOR,
          str_replace(
              [$home_url, '/'],
              [WP_CONTENT_DIR, DIRECTORY_SEPARATOR],
              $src
          )
      );

      if ( file_exists( $file_path ) ) {
          // Use the last modified time of the file as a version.
          $version = filemtime( $file_path );
      }
  }

  return $version;
}

function cf_enqueue_style( $handle, $src, $dependencies = [], $media = 'all' ) {
  wp_enqueue_style( $handle, $src, $dependencies, cf_generate_file_version( $src ), $media );
}

function cf_enqueue_script( $handle, $src, $dependencies = [], $in_footer = false ) {
  wp_enqueue_script( $handle, $src, $dependencies, cf_generate_file_version( $src ), $in_footer );
}


function cf_theme_var_map( array $arr, string $arg ): array {

	$map = [];

	foreach( $arr as $key => $value ) {
		$entry = [];
		$entry['name'] = __( $key, 'freedomtheme' ),
		$entry['slug'] = strtolower( str_replace( ' ', '-', $key ) );
		$entry[$arg] 	 = $value;
		$map[] = $entry;
	}

	return $map;
}
