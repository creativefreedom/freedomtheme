<?php
/**
 * Server-side rendering of the `cf/latest-content` block.
 *
 * @package WordPress
 */

/**
 * The excerpt length set by the Latest Content block
 * set at render time and used by the block itself.
 *
 * @var int
 */
global $block_cf_latest_content_excerpt_length;
$block_cf_latest_content_excerpt_length = 0;

/**
 * Callback for the excerpt_length filter used by
 * the Latest Content block at render time.
 *
 * @return int Returns the global $block_cf_latest_content_excerpt_length variable
 *             to allow the excerpt_length filter respect the Latest Block setting.
 */
function block_cf_latest_content_get_excerpt_length() {
	global $block_cf_latest_content_excerpt_length;
	return $block_cf_latest_content_excerpt_length;
}

/**
 * Renders the `cf/latest-content` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with latest posts added.
 */
function render_block_cf_latest_content( $attributes ) {
	global $post, $block_cf_latest_content_excerpt_length;

	$args = [
		'posts_per_page'   => $attributes['postsToShow'],
		'post_status'      => 'publish',
		'order'            => $attributes['order'],
		'orderby'          => $attributes['orderBy'],
		'offset'					 =>	$attributes['offsetPosts'],
		'suppress_filters' => false,
	];

	$block_cf_latest_content_excerpt_length = $attributes['excerptLength'];
	add_filter( 'excerpt_length', 'block_cf_latest_content_get_excerpt_length', 20 );

	if ( isset( $attributes['categories'] ) ) {
		$args['category__in'] = array_column( $attributes['categories'], 'id' );
	}

	if ( ! empty( $attributes['postsToInclude'] ) ) {
		$args['include'] = array_column( $attributes['postsToInclude'], 'id' );
	}

	$recent_posts = get_posts( $args );

	$list_items_markup = '';

	foreach ( $recent_posts as $post ) {
		$post_link = esc_url( get_permalink( $post ) );

		$list_items_markup .= '<li>';

		if ( $attributes['displayFeaturedImage'] && has_post_thumbnail( $post ) ) {
			$image_style = '';
			if ( isset( $attributes['featuredImageSizeWidth'] ) ) {
				$image_style .= sprintf( 'max-width:%spx;', $attributes['featuredImageSizeWidth'] );
			}
			if ( isset( $attributes['featuredImageSizeHeight'] ) ) {
				$image_style .= sprintf( 'max-height:%spx;', $attributes['featuredImageSizeHeight'] );
			}

			$image_classes = 'wp-block-cf-latest-content__featured-image';
			if ( isset( $attributes['featuredImageAlign'] ) ) {
				$image_classes .= ' align' . $attributes['featuredImageAlign'];
			}

			$featured_image = get_the_post_thumbnail(
				$post,
				$attributes['featuredImageSizeSlug'],
				['style' => $image_style]
			);
			if ( $attributes['addLinkToFeaturedImage'] ) {
				$featured_image = sprintf(
					'<a href="%1$s">%2$s</a>',
					$post_link,
					$featured_image
				);
			}
			$list_items_markup .= sprintf(
				'<div class="%1$s">%2$s</div>',
				$image_classes,
				$featured_image
			);
		}

		$list_items_markup .= '<div class="wp-block-cf-latest-content__content">';

		if( isset( $attributes['displayPostCategory'] ) && $attributes['displayPostCategory'] ) {

			$categories = wp_get_post_categories( $post->ID );
			$tags = '';

			if( ! empty( $categories ) ) {
				foreach( $categories as $category_id ) {
					$category 	= get_term( $category_id );
					$colour 		= get_term_meta( $category->term_id, 'category-colour', true );
					if(
						$category->name !== 'Uncategorised' &&
						$category->name !== 'Uncategorized'
					) {
						$tags .= sprintf(
							'<a href="%s" class="tag cat-item-%s">%s</a>',
							$post_link,
							$category_id,
							$category->name
						);
					}
				}
			}

			if( ! empty( $tags ) ) {
				$list_items_markup .= sprintf(
					'<div class="wp-block-cf-latest-content__post-categories mb-1">%s</div>',
					$tags
				);
			}
		}

		$title = get_the_title( $post );
		if ( ! $title ) {
			$title = __( '(no title)' );
		}
		$list_items_markup .= sprintf(
			'<h3><a href="%1$s">%2$s</a></h3>',
			$post_link,
			$title
		);

		if ( isset( $attributes['displayPostContent'] ) && $attributes['displayPostContent']
			&& isset( $attributes['displayPostContentRadio'] ) && 'excerpt' === $attributes['displayPostContentRadio'] ) {

			$trimmed_excerpt = get_the_excerpt( $post );

			if ( post_password_required( $post ) ) {
				$trimmed_excerpt = __( 'This content is password protected.' );
			}

			$list_items_markup .= sprintf(
				'<div class="wp-block-cf-latest-content__post-excerpt">%1$s</div>',
				$trimmed_excerpt
			);
		}

		if ( isset( $attributes['displayPostContent'] ) && $attributes['displayPostContent']
			&& isset( $attributes['displayPostContentRadio'] ) && 'full_post' === $attributes['displayPostContentRadio'] ) {

			$post_content = wp_kses_post( html_entity_decode( $post->post_content, ENT_QUOTES, get_option( 'blog_charset' ) ) );

			if ( post_password_required( $post ) ) {
				$post_content = __( 'This content is password protected.' );
			}

			$list_items_markup .= sprintf(
				'<div class="wp-block-cf-latest-content__post-full-content">%1$s</div>',
				$post_content
			);
		}

		// Post date
		$list_items_markup .= sprintf(
			'<div class="wp-block-cf-latest-content__post-date text-muted small mt-auto">%s</div>',
			get_the_date()
		);


		$list_items_markup .= "</div></li>\n";
	}

	remove_filter( 'excerpt_length', 'block_cf_latest_content_get_excerpt_length', 20 );
	// var_dump($attributes['columns']);
	$class = sprintf( 'wp-block-cf-latest-content__list columns-%s', $attributes['columns'] );

	$wrapper_attributes = get_block_wrapper_attributes( ['class' => $class] );

	return sprintf(
		'<ul %1$s>%2$s</ul>',
		$wrapper_attributes,
		$list_items_markup
	);
}

/**
 * Registers the `cf/latest-content` block on server.
 */
function register_block_cf_latest_content() {
	register_block_type_from_metadata(
		get_template_directory() . '/src/js/admin/blocks/LatestContent',
		[
			'render_callback' => 'render_block_cf_latest_content',
		]
	);
}
add_action( 'init', 'register_block_cf_latest_content' );

/**
 * Handles outdated versions of the `cf/latest-content` block by converting
 * attribute `categories` from a numeric string to an array with key `id`.
 *
 * This is done to accommodate the changes introduced in #20781 that sought to
 * add support for multiple categories to the block. However, given that this
 * block is dynamic, the usual provisions for block migration are insufficient,
 * as they only act when a block is loaded in the editor.
 *
 * TODO: Remove when and if the bottom client-side deprecation for this block
 * is removed.
 *
 * @param array $block A single parsed block object.
 *
 * @return array The migrated block object.
 */
function block_cf_latest_content_migrate_categories( $block ) {
	if (
		'cf/latest-content' === $block['blockName'] &&
		! empty( $block['attrs']['categories'] ) &&
		is_string( $block['attrs']['categories'] )
	) {
		$block['attrs']['categories'] = [
			['id' => absint( $block['attrs']['categories'] )],
		];
	}

	return $block;
}
add_filter( 'render_block_data', 'block_cf_latest_content_migrate_categories' );
