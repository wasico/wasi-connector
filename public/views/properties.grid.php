<?php
/**
 * The properties view in grid format.
 *
 * @package    Wasi_Connector
 */

?>
<div class="listing-box-archive type-box items-per-row-3">
<div id="wasiApp" class="listings-row">
	<div v-show="app_ready===true && loading===false && properties.length<=0" class="listings-not-found text-center" v-cloak>
		<h3><?php esc_html_e( 'Properties not found!', 'wasico' ); ?></h3>
		<span><?php esc_html_e( 'Try again with different filters.', 'wasico' ); ?></span>
	</div>
	<div v-show="app_ready===true && loading===true" class="listings-not-found text-center" v-cloak>
		<h3><?php esc_html_e( 'Searching properties...', 'wasico' ); ?></h3>
	</div>

	<article class="listing-container" v-for="prop in properties" v-cloak>
		<div class="listing-column"> <!-- class featured in case of define any way to detect it -->
			<a v-bind:href="'<?php echo esc_url_raw( home_url( '/' ) . $atts['propertyPath'] ); ?>/'+prop.id_property" 
				class="listing-column-image" v-bind:style="prop.thumbnail">
			</a>
			<div class="listing-column-label-top listing-column-label-top-left"
				style="<?php echo esc_attr( ! empty( $atts['tags-bg-color'] ) ? 'background-color:' . $atts['tags-bg-color'] : '' ); ?>">{{ get_property_type(prop.id_property_type) }}</div>
			<div class="listing-column-label-special"
				style="<?php echo esc_attr( ! empty( $atts['tags-bg-color'] ) ? 'background-color:' . $atts['tags-bg-color'] : '' ); ?>">
				<span v-if="prop.for_sale=='true' && prop.sale_price>0">{{ prop.sale_price | formatNumber }} {{ prop.iso_currency }}</span>
				<span v-if="prop.for_rent=='true' && prop.rent_price>0">{{ prop.rent_price | formatNumber }} {{ prop.iso_currency }}</span>
				<span v-if="prop.sale_price==0 && prop.rent_price==0">0</span>
			</div>

			<div class="listing-column-title">
				<h3><a v-bind:href="'<?php echo esc_url_raw( home_url( '/' ) . $atts['propertyPath'] ); ?>/'+prop.id_property" >{{ prop.title }}</a></h3>
			</div>

			<div class="listing-column-content">
				<ul>
					<li><address v-html="prop.city_label+', '+prop.region_label"></address></li>
					<li><?php esc_html_e( 'Rooms', 'wasico' ); ?>: {{ prop.bedrooms }}</li>
					<li><?php esc_html_e( 'Bathrooms', 'wasico' ); ?>: {{ prop.bathrooms }}</li>
				</ul>
				<a v-bind:href="'<?php echo esc_url_raw( home_url( '/' ) . $atts['propertyPath'] ); ?>/'+prop.id_property" 
					class="<?php echo esc_attr( $atts['btn-class'] ); ?>"><?php esc_html_e( 'More info', 'wasico' ); ?></a>
			</div>
		</div>
	</article>

	<!-- pagination: -->
	<nav aria-label="Page navigation" class="nav-container pagination hidden" 
		v-show="total_pages>0 && total_properties>properties_per_page" v-cloak>
		<ul class="pagination pagination-inner">
			<li>
				<a href="#" aria-label="Previous" v-on:click="previousPage()">
					<span aria-hidden="true">&laquo;</span>
				</a>
			</li>
			<li v-for="page in total_pages" v-bind:class="activePageClass(page)">
				<a href="#" v-on:click.stop="paginate(page)">{{ page }}</a>
			</li>
			<li>
				<a href="#" aria-label="Next" v-on:click="nextPage()">
					<span aria-hidden="true">&raquo;</span>
				</a>
			</li>
		</ul>
	</nav>
</div>
</div>
