<?php

// shortocode: [wasi-properties]
function renderWasiProperties($parent, $atts) {
    $atts = shortcode_atts(
        array(
            'order' => 'desc',
            'order_by' => 'id_property',
            'featured' => 'false',
            'limit' => '-1',
            'layout' => 'list',
            'btn-class' => 'btn btn-primary',
            'tags-bg-color' => ''
        ), $atts, 'wasi-properties' );

    if(!$atts) { $atts = []; }
        
    $atts['propertyTypes'] = $parent->getAPIClient()->getPropertyTypes();
    $atts['propertyPage'] = get_post($parent->getWasiData()['property_single_page'])->post_name;

    ob_start();

    echo '<script>';
    echo 'var propertyTypes = '.json_encode($atts['propertyTypes']).";\n";
    echo 'var wasi_order = "'.$atts['order'].'";';
    echo 'var wasi_order_by = "'.$atts['order_by'].'";';
    if($atts['featured']!=='false') {
        echo 'var wasi_featured = true;';
    }
    if($atts['limit']!=='-1') {
        echo 'var wasi_limit = '.$atts['limit'].';';
    }
    echo '</script>';

    switch($atts['layout']) {
        case 'grid':
            require_once('views/properties.grid.php');
            break;

        case 'list':
        default:
            require_once('views/properties.php');
            break;
    }
    

    $out = ob_get_clean();
    return $out;
}
