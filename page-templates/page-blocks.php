<?php
/**
 * Template Name: Blocks layout (no header)
 *
 * Template for displaying a page without the default header (custom header required).
 *
 * @package FreedomTheme
 */


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'cf_container_type' );

?>

<div id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">

			<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

				<div class="entry-content">

					<?php the_content(); ?>

				</div><!-- .entry-content -->

			</article><!-- #post-## -->


			</main><!-- #main -->

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
