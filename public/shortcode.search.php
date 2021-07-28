<?php
/**
 * The properties shortcode. [wasi-search]
 *
 * @package    Wasi_Connector
 */

/**
 * The search shortcode render.
 *
 * @param Wasi_Connector_Public $parent The parent class.
 * @param array                 $atts The shortcode attributes.
 *
 * @return string
 */
function render_wasi_search( $parent, $atts ) {
	if ( isset( $atts['formclass'] ) ) {
		$atts['formClass'] = $atts['formclass'];
	}
	if ( isset( $atts['submitclass'] ) ) {
		$atts['submitClass'] = $atts['submitclass'];
	}

	$atts = shortcode_atts(
		array(
			'formClass'   => 'row',
			'submitClass' => 'btn btn-primary',
		),
		$atts,
		'wasi-search'
	);

	if ( ! $atts ) {
		$atts = array();
	}
	$property_status = $parent->get_api_client()->get_property_status();
	$property_types  = $parent->get_api_client()->get_property_types();
	$wasi_countries  = $parent->get_api_client()->get_countries();

	ob_start();

	require_once 'views/search.php';

	$out = ob_get_clean();
	return $out;
}
