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
