<?php

// shortocode: [wasi-properties]
function renderWasiSearch($parent, $instance) {

    if(isset($instance['formclass'])) {
        $instance['formClass'] = $instance['formclass'];
    }
    if(isset($instance['submitclass'])) {
        $instance['submitClass'] = $instance['submitclass'];
    }

    $instance = shortcode_atts(
        array(
            'formClass' => 'row',
            'submitClass' => 'btn btn-primary'
        ), $instance, 'wasi-search' );

    if(!$instance) { $instance = []; }
        
    $propertyStatus = $parent->getAPIClient()->getPropertyStatus();
    $propertyTypes = $parent->getAPIClient()->getPropertyTypes();
    // $atts['propertyTypes'] = $parent->getAPIClient()->getPropertyTypes();
    // $atts['propertyPage'] = get_post($parent->getWasiData()['property_single_page'])->post_name;

    ob_start();

    require_once('views/search.php');

    $out = ob_get_clean();
    return $out;
}
