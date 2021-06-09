<?php
/**
 * Comment layout
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add Bootstrap classes to WP's comment form default fields.
 *
 * @param array $fields {
 *     Default comment fields.
 *
 *     @type string $author  Comment author field HTML.
 *     @type string $email   Comment author email field HTML.
 *     @type string $url     Comment author URL field HTML.
 *     @type string $cookies Comment cookie opt-in field HTML.
 * }
 *
 * @return array
 */
function cf_bootstrap_comment_form_fields( $fields ) {

	$replace = [
		'<p class="' => '<div class="form-group ',
		'<input'     => '<input class="form-control" ',
		'</p>'       => '</div>',
	];

	if ( isset( $fields['author'] ) ) {
		$fields['author'] = strtr( $fields['author'], $replace );
	}
	if ( isset( $fields['email'] ) ) {
		$fields['email'] = strtr( $fields['email'], $replace );
	}
	if ( isset( $fields['url'] ) ) {
		$fields['url'] = strtr( $fields['url'], $replace );
	}

	$replace = [
		'<p class="' => '<div class="form-group form-check ',
		'<input'     => '<input class="form-check-input" ',
		'<label'     => '<label class="form-check-label" ',
		'</p>'       => '</div>',
	];

	if ( isset( $fields['cookies'] ) ) {
		$fields['cookies'] = strtr( $fields['cookies'], $replace );
	}

	return $fields;
}
// Add Bootstrap classes to comment form fields.
add_filter( 'comment_form_default_fields', 'cf_bootstrap_comment_form_fields' );

// Add Bootstrap classes to comment form submit button and comment field.
add_filter( 'comment_form_defaults', 'cf_bootstrap_comment_form' );

/**
 * Adds Bootstrap classes to comment form submit button and comment field.
 *
 * @param string[] $args Comment form arguments and fields.
 *
 * @return string[]
 */
function cf_bootstrap_comment_form( $args ) {
	$replace = [
		'<p class="' => '<div class="form-group ',
		'<textarea'  => '<textarea class="form-control" ',
		'</p>'       => '</div>',
	];

	if ( isset( $args['comment_field'] ) )
		$args['comment_field'] = strtr( $args['comment_field'], $replace );


	if ( isset( $args['class_submit'] ) )
		$args['class_submit'] = 'btn btn-secondary';

	return $args;
}


/**
 * Displays a note that comments are closed if comments are closed and there are comments.
 */
function cf_comment_form_comments_closed() {
	if ( get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'freedomtheme' ); ?></p>
		<?php
	}
}

// Add note if comments are closed.
add_action( 'comment_form_comments_closed', 'cf_comment_form_comments_closed' );
