<?php
/**
 * Render a single property.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 */

?>
<h1><?php echo esc_html( $single_property->title ); ?></h1>

<script>
	var propertyTypes = [];
</script>
<div id="wasiAppSingle" class="listing-detail">
	<div class="listing-detail-section" id="listing-detail-section-attributes">
		<?php if ( $single_property->galleries ) : ?>
			<div id="wasi_gallery" style="visibility: hidden;">
				<?php foreach ( $single_property->galleries[0] as $key => $value ) : ?>
					<?php if ( is_object( $value ) ) : ?>
						<?php $has_pictures = true; ?>
						<img
							src="<?php echo esc_attr( $value->url ); ?>"
							data-image="<?php echo esc_attr( $value->url_big ); ?>"
							alt="<?php echo esc_attr( $value->description ); ?>"
						>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php if ( $has_pictures ) : ?>
				<script>
					jQuery(document).ready(function(){
						var $gallery = jQuery("#wasi_gallery");
						$gallery.find('br').remove();
						$gallery.unitegallery();
						$gallery.css('visibility', 'visible');
					});
				</script>
			<?php endif; ?>
		<?php endif; ?>

		<div class="page-header">
			<h3 class="pull-left">
				<?php
				if ( $single_property->address ) {
					echo esc_html( $single_property->address . ', ' );
				}
				if ( $single_property->city_label ) {
					echo esc_html( $single_property->city_label . ', ' );
				}
				echo esc_html( $single_property->region_label );
				?>
			</h3>
			<div class="pull-right pricing">
				<?php $currency_var = 'EUR' === $single_property->iso_currency ? '€' : '$'; ?>
				<?php if ( 'true' === $single_property->for_sale && '0' !== $single_property->sale_price ) : ?>
					<h3>
						<?php esc_html_e( 'For Sale', 'wasico' ); ?>
						<?php echo esc_html( $currency_var . number_format( $single_property->sale_price, 0 ) ); ?>
						<span class="currency"><?php echo esc_html( $single_property->iso_currency ); ?></span>
					</h3>
				<?php endif; ?>
				<?php if ( 'true' === $single_property->for_rent && '0' !== $single_property->rent_price ) : ?>
					<h3>
						<?php esc_html_e( 'For Rent', 'wasico' ); ?>
						<?php echo esc_html( $currency_var . number_format( $single_property->rent_price, 0 ) ); ?>
					</h3>
				<?php endif; ?>
			</div>
			<div class="clear clearfix"> </div>
		</div>

		<div class="listing-detail-section" id="listing-detail-section-description">
			<?php echo wp_kses_post( $single_property->observations ); ?>
		</div>

		<div class="listing-detail-attributes listing-detail-content-box">
			<ul>
				<li class="listing_property_type">
					<strong class="key"><?php esc_html_e( 'Property type', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( get_wasi_property_type( $single_property->id_property_type ) ); ?></span>
				</li>
				<li class="listing_property_reference">
					<strong class="key"><?php esc_html_e( 'Reference', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->id_property ); ?></span>
				</li>
				<li class="listing_property_maintenance_fee">
					<strong class="key"><?php esc_html_e( 'Maintenance Fee', 'wasico' ); ?></strong>
					<span class="value">$<?php echo esc_html( number_format( $single_property->maintenance_fee, 0 ) ); ?></span>
				</li>
				<li class="listing_property_rooms">
					<strong class="key"><?php esc_html_e( 'Bedrooms', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->bedrooms ); ?></span>
				</li>
				<li class="listing_property_baths">
					<strong class="key"><?php esc_html_e( 'Bathrooms', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->bathrooms ); ?></span>
				</li>
				<li class="listing_property_garages">
					<strong class="key"><?php esc_html_e( 'Garages', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->garages ); ?></span>
				</li>
				<li class="listing_property_parking_lots">
					<strong class="key"><?php esc_html_e( 'Floor', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->floor ); ?></span>
				</li>
				<li class="listing_property_year_built">
					<strong class="key"><?php esc_html_e( 'Year built', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->building_date ); ?></span>
				</li>
				<li class="listing_property_home_area">
					<strong class="key"><?php esc_html_e( 'Area', 'wasico' ); ?></strong>
					<span class="value">
						<?php echo esc_html( "$single_property->area $single_property->unit_area_label" ); ?>
					</span>
				</li>
				<li class="listing_property_stratum">
					<strong class="key"><?php esc_html_e( 'Stratum', 'wasico' ); ?></strong>
					<span class="value"><?php echo esc_html( $single_property->stratum ); ?></span>
				</li>
			</ul>
		</div>
	</div>

	<?php if ( $single_property->features->internal ) : ?>
		<div class="listing-detail-section" id="listing-detail-section-property-amenities">
			<h2 class="page-header"><?php esc_html_e( 'Amenities', 'wasico' ); ?></h2>
			<div class="listing-detail-property-amenities listing-detail-content-box">
				<ul>
					<?php foreach ( $single_property->features->internal as $feat ) : ?>
						<li class="yes">
							<a><?php echo esc_html( $feat->nombre ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $single_property->features->external ) : ?>
		<div class="listing-detail-section" id="listing-detail-section-property-facilities">
			<h2 class="page-header"><?php esc_html_e( 'Public facilities', 'wasico' ); ?></h2>
			<div class="listing-detail-property-amenities listing-detail-content-box">
				<ul>
					<?php foreach ( $single_property->features->external as $feat ) : ?>
						<li class="yes">
							<a><?php echo esc_html( $feat->nombre ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $single_property->video ) : ?>
		<div class="listing-detail-section" id="listing-detail-section-video">
			<h2 class="page-header"><?php esc_html_e( 'Video', 'wasico' ); ?></h2>
			<div class="video-embed-wrapper listing-detail-content-box">
				<?php
					global $wp_embed;
					echo wp_kses_post( $wp_embed->run_shortcode( '[embed width="500"]' . $single_property->video . '[/embed]' ) );
				?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $single_property->map ) : ?>
		<div class="listing-detail-section" id="listing-detail-section-location" >
			<h2 class="page-header"><i class="fa fa-fw fa-map-marker"></i> <?php esc_html_e( 'Location', 'wasico' ); ?></h2>
			<div class="listing-detail-location-wrapper listing-detail-content-box" >
				<iframe src="https://maps.google.com/maps?q=<?php echo esc_html( $single_property->map ); ?>&z=15&output=embed" width="100%" height="380" frameborder="0" style="border:0"></iframe>
			</div>
		</div>
	<?php endif; ?>
</div>
