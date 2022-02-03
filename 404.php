<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper has-pale-blue-background-color" id="wrapper-404">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row mb-5">

			<main class="site-main py-5 col-10 mx-auto" id="main">
				<h1>404</h1>
				<h4>Oops! That page can’t be found.</h4>
				<p>It looks like nothing was found at this location.</p>
				<div class="wp-block-buttons mb-5">
					<div class="wp-block-button"><a href="/" class="wp-block-button__link">Back to the home page</a></div>
				</div>
			</main><!-- #main -->

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php get_footer(); ?>
