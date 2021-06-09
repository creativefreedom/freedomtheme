<?php
/**
 * Check and setup theme's default settings
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Store default theme settings in database.
 */
function cf_setup_theme_default_settings() {
	$defaults = cf_get_theme_default_settings();
	$settings = get_theme_mods();
	foreach ( $defaults as $setting_id => $default_value ) {
		// Check if setting is set, if not set it to its default value.
		if ( ! isset( $settings[ $setting_id ] ) ) {
			set_theme_mod( $setting_id, $default_value );
		}
	}
}

/**
 * Retrieve default theme settings.
 *
 * @return array
 */
function cf_get_theme_default_settings() {
	$defaults = [
		'cf_posts_index_style' => 'default',   // Latest blog posts style.
		'cf_sidebar_position'  => 'right',     // Sidebar position.
		'cf_container_type'    => 'container', // Container width.
	];

	/**
	 * Filters the default theme settings.
	 *
	 * @param array $defaults Array of default theme settings.
	 */
	return apply_filters( 'cf_theme_default_settings', $defaults );
}
