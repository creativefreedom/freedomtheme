<?php

// Create id attribute allowing for custom "anchor" value.
$id = 'wp-block-post-grid-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'wp-block-post-grid';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}


$theme  = get_field('style');
$type   = get_field('post_type') ?: 'post';
$date   = get_field('hide_date');
$limit  = get_field('limit') ?: -1;
$offset  = get_field('offset') ?: 0;


// post query
$tax_query = [];

// Categories
if( $categories = get_field('filter_categories') ) {
    $tax_query[] = [
        'taxonomy'  => 'category',
        'field'     => 'term_id',
        'terms'     => $categories,
	];
}

// Tags
if( $tags = get_field('filter_tags') ) {
    $tax_query[] = [
        'taxonomy'  => 'post_tag',
        'field'     => 'term_id',
        'terms'     => $tags,
	];
}

$args = [
    'posts_per_page'	=> $limit,
    'offset'	=> $offset,
    'post_type' 		=> $type,
    'tax_query' 		=> $tax_query
];

if( $type == 'page' ) {
    
    $args['post__in'] = get_field('pages') ?: [0];
}

$posts = new WP_Query( $args );


$taxonomies = [
    'categories' 	=> $categories,
    'tags' 			=> $tags,
];

?>

<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    
    <?php if ( $posts->have_posts() ): ?>
        
        <div class="row">
            
            <?php while ( $posts->have_posts() ) : $posts->the_post(); $post_id = get_the_ID(); ?>
                
                <div class="post-card-wrapper col-12 <?php print get_field('col_layout') ?: 'col-md-6' ; ?> mb-3">

                    <article class="card">
                     <?php the_post_thumbnail( 'large', ['class'=>'card-img-top','alt'=>''] ); ?>
                        <div class="card-body">
                            <h4><a <?php if(!is_admin()): ?>href="<?php the_permalink(); ?>"<?php endif; ?> class="stretched-link"><?php the_title(); ?></a></h4>
                            <?php if(!get_field('hide_date') && 'post' == $type): ?>
                                <span class="post-card-date"><?php print get_the_date(); ?></span>
                            <?php endif; ?>
                            <?php the_excerpt($post_id);?>
                        </div>
                        <div class="post-card-feature">
                            
                        </div>
                    </article> 

                </div>
                
            <?php endwhile; ?>

        </div>
    
    <?php else: ?>
        <div class="alert alert-secondary" role="alert">There are currently no <?php print $type."s"; ?> to display.</div>        
    <?php endif; ?>

</div>