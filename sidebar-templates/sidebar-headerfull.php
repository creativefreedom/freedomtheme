<?php
/**
 * Sidebar setup for footer full
 *
 * @package FreedomTheme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$container = get_theme_mod( 'cf_container_type' );

?>

<?php if ( is_active_sidebar( 'headerfull' ) ) : ?>

	<!-- ******************* The Footer Full-width Widget Area ******************* -->

	<div class="bg-secondary" id="wrapper-header-full">

		<div class="<?php echo esc_attr( $container ); ?>" id="header-full-content" tabindex="-1">

			<?php dynamic_sidebar( 'headerfull' ); ?>

		</div>

	</div><!-- #wrapper-footer-full -->

	<?php
endif;
