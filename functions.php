<?php
/**
 * FreedomTheme functions and definitions
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// FreedomTheme's includes directory.
$cf_inc_dir = get_template_directory() . '/inc';

// Array of files to include.
$cf_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/helpers.php',                         // Add helper functions.
	'/widgets.php',                         // Register widget area.
	'/blocks.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/template-functions.php',              // Template functions.
	'/template-hooks.php',                  // Template hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/cf/cf/issues/567.
	'/editor.php',                          // Load Editor functions.
);

// Include files.
foreach ( $cf_includes as $file ) {
	require_once $cf_inc_dir . $file;
}
