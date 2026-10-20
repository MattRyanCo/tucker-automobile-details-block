<?php
/**
 * Plugin Name:       Tucker Automobile Details
 * Description:       A specialized block for documenting Tucker automobile details with proper nomenclature and descriptive information.
 * Version:           0.2.1
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Matt Ryan
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tucker-automobile-details
 * GitHub Plugin URI: https://github.com/mattryanco/tucker-automobile-details-block
 * Primary Branch: main
 *
 * @package TuckerAutomobileDetails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function tucker_automobile_details_block_init() {
	register_block_type( __DIR__ . '/build/' );
}
add_action( 'init', 'tucker_automobile_details_block_init' );

/**
 * Find the telex/tucker-automobile-details block anywhere in the block tree,
 * set its carNumber attribute, and return the updated blocks array.
 * Returns the original array unchanged if the block is not found.
 *
 * @param array[]  $blocks     Flat array of parsed block arrays to search.
 * @param string   $car_number Car number value to write into the block.
 * @return array[] The blocks array, updated if the Tucker block was found.
 */
function tucker_set_car_number_in_blocks( array $blocks, string $car_number ): array {
	foreach ( $blocks as $index => $block ) {
		// Found the Tucker block — update the attribute and stop.
		if ( 'telex/tucker-automobile-details' === $block['blockName'] ) {
			$attrs                  = is_array( $block['attrs'] ) ? $block['attrs'] : array();
			$attrs['carNumber']     = $car_number;
			$blocks[ $index ]['attrs'] = $attrs;
			return $blocks;
		}

		// Not found at this level — recurse into innerBlocks if present.
		if ( ! empty( $block['innerBlocks'] ) ) {
			$updated_inner = tucker_set_car_number_in_blocks( $block['innerBlocks'], $car_number );
			if ( $updated_inner !== $block['innerBlocks'] ) {
				$blocks[ $index ]['innerBlocks'] = $updated_inner;
				return $blocks;
			}
		}
	}

	return $blocks;
}

/**
 * Sync the Tucker car number block attribute, gallery shortcode category,
 * and the post slug from the car post title when it follows the Tucker nnnn format.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @param bool    $update  Whether this is an existing post being updated.
 */
function tucker_sync_car_details_from_title( $post_id, $post, $update ) {
	// Only run this logic for car posts.
	if ( 'car' !== $post->post_type ) {
		return;
	}

	// Skip auto-saves so we do not create a loop or update the post unexpectedly.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Ignore revisions and autosaves, which are not the main published post.
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Allow normal edits and WP-CLI verification, but still protect the hook.
	if ( ! current_user_can( 'edit_post', $post_id ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return;
	}

	// Only act when the title follows the expected format: "Tucker nnnn".
	$title = trim( $post->post_title );
	if ( ! preg_match( '/^Tucker\s+([A-Za-z0-9._-]+)$/i', $title, $matches ) ) {
		return;
	}

	// Pull out the car number from the title and prepare the new slug.
	$car_number = $matches[1];
	$content    = $post->post_content;
	$updated_content = $content;
	$updated_post_name = sanitize_title( $title );
	$updated = false;

	// Update the block content and any gallery shortcode category placeholder.
	if ( ! empty( $content ) ) {
		// Walk the block tree and update the Tucker block's carNumber attribute.
		$updated_content = serialize_blocks(
			tucker_set_car_number_in_blocks( parse_blocks( $content ), $car_number )
		);

		// Replace any gallery shortcode placeholder category with the car number.
		$updated_content = preg_replace_callback(
			'/attachment_category=(?:(["\'])0000\1)/i',
			function ( $matches ) use ( $car_number ) {
				return 'attachment_category=' . $matches[1] . $car_number . $matches[1];
			},
			$updated_content
		);

		// If the content changed, mark the post as needing an update.
		if ( $updated_content !== $content ) {
			$updated = true;
		}
	}

	// The slug should match the title, so update it if needed.
	if ( $post->post_name !== $updated_post_name ) {
		$updated = true;
	}

	// Do nothing if nothing changed.
	if ( ! $updated ) {
		return;
	}

	// Prevent this hook from calling itself again while we update the post.
	remove_action( 'save_post_car', 'tucker_sync_car_details_from_title', 10, 3 );

	// Build the post array for a safe update.
	$postarr = array(
		'ID'           => $post_id,
		'post_name'    => $updated_post_name,
		'post_content' => $updated_content,
	);

	// Save the post so the slug and content changes are persisted.
	wp_update_post( $postarr );

	// Reattach the hook after the update finishes.
	add_action( 'save_post_car', 'tucker_sync_car_details_from_title', 10, 3 );
}
add_action( 'save_post_car', 'tucker_sync_car_details_from_title', 10, 3 );



add_action( 'transition_post_status', 'tucker_sync_car_featured_image_from_block_on_publish', 20, 3 );

/**
 * On first publish, sync the Tucker block image to the car CPT:
 * - sets the image as the featured image
 * - sets the attachment parent to this car post
 *
 * Image resolution order:
 * 1) Use block attribute imageId (if valid attachment)
 * 2) Resolve imageUrl to existing attachment
 * 3) Sideload imageUrl into Media Library and attach to car post
 *
 * @param string  $new_status New post status after transition.
 * @param string  $old_status Previous post status before transition.
 * @param WP_Post $post       Post object being transitioned.
 * @return void
 */
function tucker_sync_car_featured_image_from_block_on_publish( $new_status, $old_status, $post ) {
	// Only run when transitioning into publish for the first time.
	// if ( 'publish' !== $new_status || 'publish' === $old_status ) {
	// 	return;
	// }

	// Guard against invalid hook payload.
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// Limit behavior to car CPT.
	if ( 'car' !== $post->post_type ) {
		return;
	}

	// Ignore revisions and autosaves.
	// if ( wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
	// 	return;
	// }
	// Ignore autosaves.
	if ( wp_is_post_autosave( $post->ID ) ) {
		return;
	}

	// Find the Tucker block attributes in post content.
	$attrs = tucker_find_tucker_block_attrs_in_content( $post->post_content );
	if ( empty( $attrs ) ) {
		return;
	}

	// Normalize block image attributes.
	$image_id  = isset( $attrs['imageId'] ) ? absint( $attrs['imageId'] ) : 0;
	$image_url = isset( $attrs['imageUrl'] ) ? esc_url_raw( $attrs['imageUrl'] ) : '';

	$attachment_id = 0;

	// Preferred source: explicit imageId from block attributes.
	if ( $image_id > 0 && 'attachment' === get_post_type( $image_id ) ) {
		$attachment_id = $image_id;
	}

	// Fallback: map imageUrl to an existing Media Library attachment.
	if ( ! $attachment_id && ! empty( $image_url ) ) {
		$resolved = attachment_url_to_postid( $image_url );
		if ( $resolved && 'attachment' === get_post_type( $resolved ) ) {
			$attachment_id = (int) $resolved;
		}
	}

	// Final fallback: sideload remote/local URL and create attachment.
	if ( ! $attachment_id && ! empty( $image_url ) ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$sideloaded_id = media_sideload_image( $image_url, $post->ID, null, 'id' );
		if ( ! is_wp_error( $sideloaded_id ) ) {
			$attachment_id = (int) $sideloaded_id;
		}
	}

	// Exit if no valid attachment could be found or created.
	if ( ! $attachment_id ) {
		return;
	}

	// Ensure this car post is the attachment parent.
	if ( (int) get_post_field( 'post_parent', $attachment_id ) !== (int) $post->ID ) {
		wp_update_post(
			array(
				'ID'          => $attachment_id,
				'post_parent' => $post->ID,
			)
		);
	}

	// Set attachment as featured image for the car post.
	set_post_thumbnail( $post->ID, $attachment_id );
}

/**
 * Parse post content and return attributes from the first
 * telex/tucker-automobile-details block found.
 *
 * @param string $content Raw post_content string.
 * @return array Block attributes, or empty array if not found.
 */
function tucker_find_tucker_block_attrs_in_content( $content ) {
	// Fast path: skip parsing if content has no Gutenberg blocks.
	if ( ! has_blocks( $content ) ) {
		return array();
	}

	$blocks = parse_blocks( $content );

	return tucker_find_tucker_block_attrs_recursive( $blocks );
}

/**
 * Recursively walk parsed blocks to find the first Tucker block and return attrs.
 *
 * @param array[] $blocks Parsed block tree.
 * @return array Matching block attributes, or empty array when absent.
 */
function tucker_find_tucker_block_attrs_recursive( array $blocks ) {
	foreach ( $blocks as $block ) {
		// Return immediately when target block is found.
		if ( ! empty( $block['blockName'] ) && 'telex/tucker-automobile-details' === $block['blockName'] ) {
			return isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		}

		// Search nested blocks (columns, groups, patterns, etc.).
		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$found = tucker_find_tucker_block_attrs_recursive( $block['innerBlocks'] );
			if ( ! empty( $found ) ) {
				return $found;
			}
		}
	}

	// No Tucker block in this branch.
	return array();
}