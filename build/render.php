<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

$car_number = isset( $attributes['carNumber'] ) ? esc_html( $attributes['carNumber'] ) : '';
$original_paint_code = isset( $attributes['originalPaintCode'] ) ? esc_html( $attributes['originalPaintCode'] ) : '';
$original_paint_color = isset( $attributes['originalPaintColor'] ) ? esc_html( $attributes['originalPaintColor'] ) : '';
$current_paint_code = isset( $attributes['currentPaintCode'] ) ? esc_html( $attributes['currentPaintCode'] ) : '';
$current_paint_color = isset( $attributes['currentPaintColor'] ) ? esc_html( $attributes['currentPaintColor'] ) : '';
$original_interior_color = isset( $attributes['originalInteriorColor'] ) ? esc_html( $attributes['originalInteriorColor'] ) : '';
$current_interior_color = isset( $attributes['currentInteriorColor'] ) ? esc_html( $attributes['currentInteriorColor'] ) : '';
$body_number = isset( $attributes['bodyNumber'] ) ? esc_html( $attributes['bodyNumber'] ) : '';
$original_engine_number = isset( $attributes['originalEngineNumber'] ) ? esc_html( $attributes['originalEngineNumber'] ) : '';
$current_engine_number = isset( $attributes['currentEngineNumber'] ) ? esc_html( $attributes['currentEngineNumber'] ) : '';
$transmission_type = isset( $attributes['transmissionType'] ) ? esc_html( $attributes['transmissionType'] ) : '';
$ownership = isset( $attributes['ownership'] ) ? esc_html( $attributes['ownership'] ) : '';
$location = isset( $attributes['location'] ) ? esc_html( $attributes['location'] ) : '';
$on_display = isset( $attributes['onDisplay'] ) && $attributes['onDisplay'];
$image_id = isset( $attributes['imageId'] ) ? intval( $attributes['imageId'] ) : 0;
$image_url = isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '';
$image_alt = isset( $attributes['imageAlt'] ) ? esc_attr( $attributes['imageAlt'] ) : '';
$car_notes = isset( $attributes['carNotes'] ) ? esc_attr( $attributes['carNotes'] ) : '';

$original_paint_combined = trim( $original_paint_code . ' ' . $original_paint_color );
$current_paint_combined = trim( $current_paint_code . ' ' . $current_paint_color );
?>

<div <?php echo get_block_wrapper_attributes(); ?>>
	<div class="tucker-automobile-details">
		<div class="tucker-automobile-details__content">
			<div class="tucker-automobile-details__left">
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Car Number:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $car_number ? $car_number : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Original Paint Code/Color:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $original_paint_combined ? $original_paint_combined : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Current Paint Code/Color:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $current_paint_combined ? $current_paint_combined : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Original Interior Color:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $original_interior_color ? $original_interior_color : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Current Interior Color:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $current_interior_color ? $current_interior_color : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Body Number:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $body_number ? $body_number : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Original Engine Number:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $original_engine_number ? $original_engine_number : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Current Engine Number:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $current_engine_number ? $current_engine_number : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Transmission Type:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $transmission_type ? $transmission_type : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Ownership:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $ownership ? $ownership : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Location:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $location ? $location : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'On Display:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $on_display ? esc_html__( 'Yes', 'tucker-automobile-details' ) : esc_html__( 'No', 'tucker-automobile-details' ); ?></span>
				</div>
				<?php if ( $car_notes ) : ?>
				<div class="tucker-detail-row">
					<span class="tucker-detail-label"><?php esc_html_e( 'Car Notes:', 'tucker-automobile-details' ); ?></span>
					<span class="tucker-detail-value"><?php echo $car_notes ? $car_notes : esc_html__( 'Not set', 'tucker-automobile-details' ); ?></span>
				</div>
				<?php endif; ?>
			</div>
			<div class="tucker-automobile-details__right">
				<?php if ( $image_url ) : ?>
					<img src="<?php echo $image_url; ?>" alt="<?php echo $image_alt ? $image_alt : esc_attr__( 'Tucker automobile', 'tucker-automobile-details' ); ?>" class="tucker-image" />
				<?php else : ?>
					<div class="tucker-image-placeholder">
						<p><?php esc_html_e( 'No image uploaded', 'tucker-automobile-details' ); ?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
