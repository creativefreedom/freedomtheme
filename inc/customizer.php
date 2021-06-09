<?php
/**
 * FreedomTheme Theme Customizer
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function cf_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'cf_customize_register' );

/**
 * Register individual settings through customizer's API.
 *
 * @param WP_Customize_Manager $wp_customize Customizer reference.
 */
function cf_theme_customize_register( $wp_customize ) {

	// Theme layout settings.
	$wp_customize->add_section(
		'cf_theme_layout_options',
		[
			'title'       => __( 'Theme Layout Settings', 'freedomtheme' ),
			'capability'  => 'edit_theme_options',
			'description' => __( 'Container width and sidebar defaults', 'freedomtheme' ),
			'priority'    => apply_filters( 'cf_theme_layout_options_priority', 160 ),
		]
	);

	/**
	 * Select sanitization function
	 *
	 * @param string               $input   Slug to sanitize.
	 * @param WP_Customize_Setting $setting Setting instance.
	 * @return string Sanitized slug if it is a valid choice; otherwise, the setting default.
	 */
	function cf_theme_slug_sanitize_select( $input, $setting ) {

		// Ensure input is a slug (lowercase alphanumeric characters, dashes and underscores are allowed only).
		$input = sanitize_key( $input );

		// Get the list of possible select options.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

	}

	$wp_customize->add_setting(
		'cf_container_type',
		[
			'default'           => 'container',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'cf_theme_slug_sanitize_select',
			'capability'        => 'edit_theme_options',
		]
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'cf_container_type',
			[
				'label'       => __( 'Container Width', 'freedomtheme' ),
				'description' => __( 'Choose between Bootstrap\'s container and container-fluid', 'freedomtheme' ),
				'section'     => 'cf_theme_layout_options',
				'settings'    => 'cf_container_type',
				'type'        => 'select',
				'choices'     => [
					'container'       => __( 'Fixed width container', 'freedomtheme' ),
					'container-fluid' => __( 'Full width container', 'freedomtheme' ),
				],
				'priority'    => apply_filters( 'cf_container_type_priority', 10 ),
			]
		)
	);

	$wp_customize->add_setting(
		'cf_sidebar_position',
		[
			'default'           => 'right',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'sanitize_text_field',
			'capability'        => 'edit_theme_options',
		]
	);

	$wp_customize->add_control(
		new WP_Customize_Control(
			$wp_customize,
			'cf_sidebar_position',
			[
				'label'             => __( 'Sidebar Positioning', 'freedomtheme' ),
				'description'       => __(
					'Set sidebar\'s default position. Can either be: right, left, both or none. Note: this can be overridden on individual pages.',
					'freedomtheme'
				),
				'section'           => 'cf_theme_layout_options',
				'settings'          => 'cf_sidebar_position',
				'type'              => 'select',
				'sanitize_callback' => 'cf_theme_slug_sanitize_select',
				'choices'           => [
					'right' => __( 'Right sidebar', 'freedomtheme' ),
					'left'  => __( 'Left sidebar', 'freedomtheme' ),
					'both'  => __( 'Left & Right sidebars', 'freedomtheme' ),
					'none'  => __( 'No sidebar', 'freedomtheme' ),
				],
				'priority'          => apply_filters( 'cf_sidebar_position_priority', 20 ),
			]
		)
	);
}

add_action( 'customize_register', 'cf_theme_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function cf_customize_preview_js() {
	cf_enqueue_script( 'cf_customizer', get_template_directory_uri() . '/js/customizer.js', ['customize-preview'], true );
}
add_action( 'customize_preview_init', 'cf_customize_preview_js' );
