<?php
/**
 * Plugin Name:       Tucker Automobile Details
 * Description:       A specialized block for documenting Tucker automobile details with proper nomenclature and descriptive information.
 * Version:           0.1.1
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Matt Ryan
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tucker-automobile-details
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
		// Replace the serialized Tucker block attributes in the post content.
		$updated_content = preg_replace_callback(
			'/<!--\s*wp:telex\/tucker-automobile-details\b(.*?)\/-->/s',
			function ( $matches ) use ( $car_number ) {
				// Pull the block attributes from the serialized comment.
				$attrs = $matches[1];
				// Update the existing carNumber value if present.
				$attrs = preg_replace( '/("carNumber"\s*:\s*")([^"]*)(")/', '$1' . $car_number . '$3', $attrs, 1 );
				// If the attribute is missing, add it so the block still has a value.
				if ( false === strpos( $attrs, 'carNumber' ) ) {
					$attrs = ' {"carNumber":"' . $car_number . '"}' . $attrs;
				}

				return '<!-- wp:telex/tucker-automobile-details' . $attrs . '/-->';
			},
			$content
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
