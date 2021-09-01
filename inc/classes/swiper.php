<?php
defined( 'ABSPATH' ) || die;

class CF_Swiper {
  static function get_swiper_options( $block )
  {
    $block_id 		= cf_get_block_id( $block );
		$attributes 	= $block['attrs'];
    $options 			= ['container_id' => $block_id];

    if( isset( $attributes['slidesPerView'] ) )
      $options['slidesPerView'] = $attributes['slidesPerView'];

    if( isset( $attributes['pagination'] ) && $attributes['pagination'] === true ) {
      $options['pagination'] = [
        'el' 				=> sprintf( "#%s .swiper-pagination", $block_id ),
				'clickable' => true
      ];
    }

    if( isset( $attributes['navigation'] ) && $attributes['navigation'] === true ) {
      $options['navigation'] = [
        'nextEl' => sprintf( "#%s .swiper-button-next", $block_id ),
        'prevEl' => sprintf( "#%s .swiper-button-prev", $block_id ),
      ];
    }

    if( isset( $attributes['scrollbar'] ) && $attributes['scrollbar'] === true ) {
      $options['scrollbar'] = [
        'el'        => sprintf( "#%s .swiper-scrollbar", $block_id ),
        'draggable' => true,
      ];
    }

    if( ! isset( $attributes['autoplay'] ) || $attributes['autoplay'] === true ) {
      $options['autoplay'] = true;
    }

    if( ! isset( $attributes['loop'] ) || $attributes['loop'] === true ) {
      $options['loop'] = true;
    }

    if( isset( $attributes['speed'] ) ) {
      $options['speed'] = $attributes['speed'];
    }

    if( isset( $attributes['spaceBetween'] ) ) {
      $options['spaceBetween'] = $attributes['spaceBetween'];
    }


    return $options;
  }
}
