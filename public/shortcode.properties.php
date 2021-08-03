<?php
/**
 * The properties shortcode. [wasi-properties]
 *
 * @package    Wasi_Connector
 */

/**
 * The properties shortcode render.
 *
 * @param Wasi_Connector_Public $parent The parent class.
 * @param array                 $atts The shortcode attributes.
 *
 * @return string
 */
function render_wasi_properties( $parent, $atts ) {
	$atts = shortcode_atts(
		array(
			'order'         => 'desc',
			'order_by'      => 'id_property',
			'featured'      => 'false',
			'limit'         => '-1',
			'layout'        => 'list',
			'btn-class'     => 'btn btn-primary',
			'tags-bg-color' => '',
		),
		$atts,
		'wasi-properties'
	);

	if ( ! $atts ) {
		$atts = array();
	}

	$atts['propertyTypes'] = $parent->get_api_client()->get_property_types();
	$atts['propertyPath']  = $parent->get_single_path();

	ob_start();

	echo '<script>';
	echo 'var propertyTypes = ' . wp_json_encode( $atts['propertyTypes'] ) . ';' . PHP_EOL;
	echo 'var wasi_order = "' . esc_js( $atts['order'] ) . '";';
	echo 'var wasi_order_by = "' . esc_js( $atts['order_by'] ) . '";';
	if ( 'false' !== $atts['featured'] ) {
		echo 'var wasi_featured = true;';
	}
	if ( '-1' !== $atts['limit'] ) {
		echo 'var wasi_limit = ' . esc_js( $atts['limit'] ) . ';';
	}
	echo '</script>';

	switch ( $atts['layout'] ) {
		case 'grid':
			require_once 'views/properties.grid.php';
			break;

		case 'list':
		default:
			require_once 'views/properties.php';
			break;
	}

	$out = ob_get_clean();
	return $out;
}
