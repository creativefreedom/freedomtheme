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
?>

<div id="page-wrapper">

	<div class="container" id="content" tabindex="-1">

		<div class="row">

			<main class="site-main" id="main">

				<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

					<div class="entry-content">

						<?php the_content(); ?>

					</div><!-- .entry-content -->

				</article><!-- #post-## -->

			</main><!-- #main -->

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #page-wrapper -->

<?php
get_footer();
