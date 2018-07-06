<script>var propertyTypes = [];</script>
<div id="wasiAppSingle" class="listing-detail">
    <?php
    if ( isset($_GET['wasi_debug']) ) {
        echo "<pre>".print_r($single_property, true)."</pre>";
    }
    ?>

    <div class="listing-detail-section" id="listing-detail-section-attributes">

        <?php if ($single_property->galleries) {
            $imgs = $single_property->galleries[0];
            $images_html = '';
            foreach ($imgs as $key => $value) {
                if (is_object($value)) {
                    if(empty($value->description)) { $value->description = 'img'.$key; }
                    // echo "<pre>".print_r($value, true)."</pre>";
                    $images_html.= "<img src='{$value->url}' "
                        ."data-image='{$value->url_big}' "
                        ."alt='{$value->description}' >";
                }
            }
            if(strlen($images_html)>0) {
                echo '<div id="wasi_gallery" style="visibility:hidden;">'.$images_html.'</div>'; ?>
                <script>
                    jQuery(document).ready(function(){
                        var g = jQuery("#wasi_gallery");
                        g.unitegallery();
                        g.css('visibility', 'visible');
                    });
                </script>
                <?php
            }
        }
        ?>
        
        <div class="page-header">
            <h3 class="pull-left">
                <?php
                if($single_property->address) {
                    echo $single_property->address.', ';
                }
                if($single_property->city_label) {
                    echo $single_property->city_label.', ';
                }                
                echo $single_property->region_label;
                ?>
            </h3>
            <div class="pull-right pricing">
                <?php $currency_var = $single_property->iso_currency==='EUR' ? '€' : '$' ?>
                <?php if($single_property->for_sale==='true' && $single_property->sale_price!=='0') : ?>
                    <h3><?php
                    echo __('For Sale', 'wasico');
                    echo ' '.$currency_var.number_format($single_property->sale_price, 0); ?> <span class="currency"><?php 
                    echo $single_property->iso_currency ?></span></h3>
                <?php endif; ?>
                <?php if($single_property->for_rent==='true' && $single_property->rent_price!=='0') : ?>
                    <h3><?php
                    echo __('For Rent', 'wasico');
                    echo ' '.$currency_var.number_format($single_property->rent_price, 0); ?></h3>
                <?php endif; ?>
            </div>
            <div class="clear clearfix"> </div>
        </div>

        <div class="listing-detail-section" id="listing-detail-section-description">
            <?php echo $single_property->observations; ?>
        </div>

        <div class="listing-detail-attributes listing-detail-content-box">
            <ul>
                <li class="listing_property_type">
                    <strong class="key"><?php _e('Property type', 'wasico'); ?></strong>
                    <span class="value"><?php echo get_wasi_property_type($single_property->id_property_type)."</pre>"; ?></span>
                </li>
                <li class="listing_property_reference">
                    <strong class="key"><?php _e('Reference', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->id_property; ?></span>
                </li>
                <li class="listing_property_maintenance_fee">
                    <strong class="key"><?php _e('Maintenance Fee', 'wasico'); ?></strong>
                    <span class="value"><?php echo '$'.number_format($single_property->maintenance_fee, 0) ?></span>
                </li>
                <li class="listing_property_rooms">
                    <strong class="key"><?php _e('Bedrooms', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->bedrooms ?></span>
                </li>
                <li class="listing_property_baths">
                    <strong class="key"><?php _e('Bathrooms', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->bathrooms ?></span>
                </li>
                <li class="listing_property_garages">
                    <strong class="key"><?php _e('Garages', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->garages ?></span>
                </li>
                <li class="listing_property_parking_lots">
                    <strong class="key"><?php _e('Floor', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->floor ?></span>
                </li>
                <li class="listing_property_year_built">
                    <strong class="key"><?php _e('Year built', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->building_date ?></span>
                </li>
                <li class="listing_property_home_area">
                    <strong class="key"><?php _e('Area', 'wasico'); ?></strong>
                    <span class="value">
                        <?php echo $single_property->area.' '.$single_property->unit_area_label ?>
                    </span>
                </li>
                <li class="listing_property_stratum">
                    <strong class="key"><?php _e('Stratum', 'wasico'); ?></strong>
                    <span class="value"><?php echo $single_property->stratum ?></span>
                </li>
            </ul>
        </div>
    </div>

    <?php if($single_property->features->internal ) : ?>
    <div class="listing-detail-section" id="listing-detail-section-property-amenities">
        <h2 class="page-header"><?php _e('Amenities', 'wasico'); ?></h2>
        <div class="listing-detail-property-amenities listing-detail-content-box">
            <ul>
                <?php foreach ($single_property->features->internal as $feat) : ?>
                    <li class="yes">
                        <a><?php echo $feat->nombre; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if($single_property->features->external ) : ?>
    <div class="listing-detail-section" id="listing-detail-section-property-facilities">
        <h2 class="page-header"><?php _e('Public facilities', 'wasico'); ?></h2>
        <div class="listing-detail-property-amenities listing-detail-content-box">
            <ul>
                <?php foreach ($single_property->features->external as $feat) : ?>
                    <li class="yes">
                        <a><?php echo $feat->nombre; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($single_property->video) : ?>
    <div class="listing-detail-section" id="listing-detail-section-video">
        <h2 class="page-header"><?php _e('Video', 'wasico'); ?></h2>
        <div class="video-embed-wrapper listing-detail-content-box">
            <?php
            global $wp_embed;
            $video_w = 500;
            // $video_h = $video_w/1.61; // 1.61 golden ratio
            echo $wp_embed->run_shortcode( '[embed width="'.$video_w.'"]' . $single_property->video . '[/embed]' );
            ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($single_property->map) : ?>
    <div class="listing-detail-section" id="listing-detail-section-location" >
        <h2 class="page-header"><i class="fa fa-fw fa-map-marker"></i> <?php _e('Location', 'wasico'); ?></h2>
        <div class="listing-detail-location-wrapper listing-detail-content-box" >
            <iframe src="https://maps.google.com/maps?q=<?php echo $single_property->map; ?>&z=15&output=embed" width="100%" height="380" frameborder="0" style="border:0"></iframe>
        </div>
    </div>
    <?php endif; ?>
</div>
