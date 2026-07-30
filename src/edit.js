/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';

/**
 * WordPress dependencies
 */
import { PanelBody, TextControl, ToggleControl, Button } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		carNumber,
		originalPaintCode,
		originalPaintColor,
		currentPaintCode,
		currentPaintColor,
		originalInteriorColor,
		currentInteriorColor,
		bodyNumber,
		originalEngineNumber,
		currentEngineNumber,
		transmissionType,
		ownership,
		location,
		onDisplay,
		imageId,
		imageUrl,
		imageAlt,
		carNotes
	} = attributes;

	const onSelectImage = ( media ) => {
		setAttributes( {
			imageId: media.id,
			imageUrl: media.url,
			imageAlt: media.alt
		} );
	};

	const onRemoveImage = () => {
		setAttributes( {
			imageId: 0,
			imageUrl: '',
			imageAlt: ''
		} );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Vehicle Identification', 'tucker-automobile-details' ) }>
					<TextControl
						label={ __( 'Car Number', 'tucker-automobile-details' ) }
						help={ __( 'Tucker series number (e.g., 1001, 1048)', 'tucker-automobile-details' ) }
						value={ carNumber }
						onChange={ ( value ) => setAttributes( { carNumber: value } ) }
					/>
					<TextControl
						label={ __( 'Body Number', 'tucker-automobile-details' ) }
						value={ bodyNumber }
						onChange={ ( value ) => setAttributes( { bodyNumber: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Paint Information', 'tucker-automobile-details' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Original Paint Code', 'tucker-automobile-details' ) }
						value={ originalPaintCode }
						onChange={ ( value ) => setAttributes( { originalPaintCode: value } ) }
					/>
					<TextControl
						label={ __( 'Original Paint Color', 'tucker-automobile-details' ) }
						value={ originalPaintColor }
						onChange={ ( value ) => setAttributes( { originalPaintColor: value } ) }
					/>
					<TextControl
						label={ __( 'Current Paint Code', 'tucker-automobile-details' ) }
						value={ currentPaintCode }
						onChange={ ( value ) => setAttributes( { currentPaintCode: value } ) }
					/>
					<TextControl
						label={ __( 'Current Paint Color', 'tucker-automobile-details' ) }
						value={ currentPaintColor }
						onChange={ ( value ) => setAttributes( { currentPaintColor: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Interior Information', 'tucker-automobile-details' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Original Interior Color', 'tucker-automobile-details' ) }
						value={ originalInteriorColor }
						onChange={ ( value ) => setAttributes( { originalInteriorColor: value } ) }
					/>
					<TextControl
						label={ __( 'Current Interior Color', 'tucker-automobile-details' ) }
						value={ currentInteriorColor }
						onChange={ ( value ) => setAttributes( { currentInteriorColor: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Mechanical Information', 'tucker-automobile-details' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Original Engine Number', 'tucker-automobile-details' ) }
						value={ originalEngineNumber }
						onChange={ ( value ) => setAttributes( { originalEngineNumber: value } ) }
					/>
					<TextControl
						label={ __( 'Current Engine Number', 'tucker-automobile-details' ) }
						value={ currentEngineNumber }
						onChange={ ( value ) => setAttributes( { currentEngineNumber: value } ) }
					/>
					<TextControl
						label={ __( 'Transmission Type', 'tucker-automobile-details' ) }
						value={ transmissionType }
						onChange={ ( value ) => setAttributes( { transmissionType: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Ownership & Location', 'tucker-automobile-details' ) } initialOpen={ false }>
					<TextControl
						label={ __( 'Ownership', 'tucker-automobile-details' ) }
						value={ ownership }
						onChange={ ( value ) => setAttributes( { ownership: value } ) }
					/>
					<TextControl
						label={ __( 'Location', 'tucker-automobile-details' ) }
						value={ location }
						onChange={ ( value ) => setAttributes( { location: value } ) }
					/>
					<ToggleControl
						label={ __( 'On Display', 'tucker-automobile-details' ) }
						checked={ onDisplay }
						onChange={ ( value ) => setAttributes( { onDisplay: value } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Additional Notes', 'tucker-automobile-details' ) } initialOpen={ false }>
					<p className="components-base-control__label">{ __( 'Notes', 'tucker-automobile-details' ) }</p>
					<RichText
						tagName="div"
						className="car-notes"
						value={ carNotes }
						onChange={ ( value ) => setAttributes( { carNotes: value } ) }
						allowedFormats={ [ 'core/bold', 'core/italic', 'core/link', 'core/strikethrough' ] }
						placeholder={ __( 'Add notes with links or styled text.', 'tucker-automobile-details' ) }
					/>
				</PanelBody>


				<PanelBody title={ __( 'Vehicle Image', 'tucker-automobile-details' ) } initialOpen={ true }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes={ ['image'] }
							value={ imageId }
							render={ ( { open } ) => (
								<>
									{ imageUrl ? (
										<>
											<img
												src={ imageUrl }
												alt={ imageAlt || __( 'Tucker automobile', 'tucker-automobile-details' ) }
												style={ { maxWidth: '100%', height: 'auto', marginBottom: '10px' } }
											/>
											<Button
												onClick={ open }
												variant="secondary"
												style={ { marginRight: '10px' } }
											>
												{ __( 'Replace Image', 'tucker-automobile-details' ) }
											</Button>
											<Button
												onClick={ onRemoveImage }
												variant="tertiary"
												isDestructive
											>
												{ __( 'Remove Image', 'tucker-automobile-details' ) }
											</Button>
										</>
									) : (
										<Button onClick={ open } variant="secondary">
											{ __( 'Select Image', 'tucker-automobile-details' ) }
										</Button>
									) }
								</>
							) }
						/>
					</MediaUploadCheck>
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<div className="tucker-automobile-details">
					<div className="tucker-automobile-details__content">
						<div className="tucker-automobile-details__left">
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Car Number:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ carNumber || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Original Paint Code/Color:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">
									{ originalPaintCode || originalPaintColor ? 
										`${originalPaintCode || ''} ${originalPaintColor || ''}`.trim() : 
										__( 'Not set', 'tucker-automobile-details' ) 
									}
								</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Current Paint Code/Color:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">
									{ currentPaintCode || currentPaintColor ? 
										`${currentPaintCode || ''} ${currentPaintColor || ''}`.trim() : 
										__( 'Not set', 'tucker-automobile-details' ) 
									}
								</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Original Interior Color:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ originalInteriorColor || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Current Interior Color:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ currentInteriorColor || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Body Number:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ bodyNumber || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Original Engine Number:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ originalEngineNumber || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Current Engine Number:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ currentEngineNumber || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Transmission Type:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ transmissionType || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Ownership:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ ownership || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'Location:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ location || __( 'Not set', 'tucker-automobile-details' ) }</span>
							</div>
							<div className="tucker-detail-row">
								<span className="tucker-detail-label">{ __( 'On Display:', 'tucker-automobile-details' ) }</span>
								<span className="tucker-detail-value">{ onDisplay ? __( 'Yes', 'tucker-automobile-details' ) : __( 'No', 'tucker-automobile-details' ) }</span>
							</div>
							{ carNotes ? (
								<div className="tucker-detail-row">
									<span className="tucker-detail-label">{ __( 'Notes:', 'tucker-automobile-details' ) }</span>
									<div className="tucker-detail-value">
										<RichText.Content tagName="div" className="car-notes" value={ carNotes } />
									</div>
								</div>
							) : null }
						</div>
						<div className="tucker-automobile-details__right">
							{ imageUrl ? (
								<img
									src={ imageUrl }
									alt={ imageAlt || __( 'Tucker automobile', 'tucker-automobile-details' ) }
									className="tucker-image"
								/>
							) : (
								<div className="tucker-image-placeholder">
									<p>{ __( 'No image selected', 'tucker-automobile-details' ) }</p>
									<p style={ { fontSize: '0.9em', marginTop: '8px' } }>{ __( 'Use the sidebar to select an image', 'tucker-automobile-details' ) }</p>
								</div>
							) }
						</div>
					</div>
				</div>
			</div>
		</>
	);
}