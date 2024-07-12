<?php
/**
 * Plugin Name: Featured Image Lightbox Block
 * Description: Example of the block bindings API that creates a new "Image Block" that pulls the featured image from the post and gives it a lightbox effect.
 * Version: 1.0.0
 * Author: Brian Coords
 * Author URI: https://briancoords.com
 * Requires at least: 6.6
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Register the custom block bindings source.
 *
 * @return void
 */
function bc_register_custom_block_bindings() {
	register_block_bindings_source(
		'bc/bindings',
		array(
			'label'              => __( 'Custom Bindings', 'bc' ),
			'get_value_callback' => 'bc_get_custom_source_value',
		)
	);
}
add_action( 'init', 'bc_register_custom_block_bindings' );



/**
 * Get the value for the custom block binding.
 *
 * @param array  $source_args    Array of source arguments.
 * @param object $block_instance The block instance.
 *
 * @return mixed|null
 */
function bc_get_custom_source_value( array $source_args, $block_instance ) {

	if ( ! isset( $block_instance->context ) || 'featured_image_url' !== $source_args['key'] ) {
		return null;
	}

	$post_id = absint( $block_instance->context['postId'] );

	if ( ! $post_id ) {
		return null;
	}

	$featured_image_id = get_post_thumbnail_id( $post_id );

	if ( ! $featured_image_id ) {
		return null;
	}

	$featured_image = wp_get_attachment_image_src( $featured_image_id, 'full' );

	if ( ! $featured_image ) {
		return null;
	}

	return $featured_image[0];
}


/**
 * Register a block variation for the core/image block.
 *
 * @param array  $variations Array of block variations.
 * @param object $block_type The block type.
 *
 * @return array
 */
function bc_register_block_variations( $variations, $block_type ) {
	if ( 'core/image' === $block_type->name ) {
		$variations[] = array(
			'name'       => 'bc_feat_image',
			'title'      => __( 'Featured Image with Lightbox', 'bc' ),
			'attributes' => array(
				'lightbox' => array(
					'enabled' => true,
				),
				'metadata' => array(
					'bindings' => array(
						'url' => array(
							'source' => 'bc/bindings',
							'args'   => array(
								'key' => 'featured_image_url',
							),
						),
					),
				),
			),
		);
	}
	return $variations;
}
add_filter( 'get_block_type_variations', 'bc_register_block_variations', 10, 2 );
