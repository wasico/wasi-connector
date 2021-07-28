<?php

// shortocode: [wasi-properties]
function renderWasiSearch( $parent, $instance ) {

	if ( isset( $instance['formclass'] ) ) {
		$instance['formClass'] = $instance['formclass'];
	}
	if ( isset( $instance['submitclass'] ) ) {
		$instance['submitClass'] = $instance['submitclass'];
	}

	$instance = shortcode_atts(
		array(
			'formClass'   => 'row',
			'submitClass' => 'btn btn-primary',
		),
		$instance,
		'wasi-search'
	);

	if ( ! $instance ) {
		$instance = array();
	}
	$property_status = $parent->get_api_client()->get_property_status();
	$property_types  = $parent->get_api_client()->get_property_types();
	$wasi_countries  = $parent->get_api_client()->get_countries();

	ob_start();

	require_once 'views/search.php';

	$out = ob_get_clean();
	return $out;
}
