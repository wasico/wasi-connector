<?php
/**
 * Render the search widget.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 */

?>
<div id="wasiSearchApp" class="wasi_search">
	<form
		action="/<?php echo esc_html( $properties_slug ); ?>"
		method="GET"
		role="form"
		class="<?php echo esc_html( $instance['formClass'] ); ?>"
		v-on:submit.prevent="wasiSearchProperties"
	>
		<div class="form-group">
			<label for="keyword-match"><?php esc_html_e( 'Keyword:', 'wasico' ); ?></label>
			<input id="keyword-match" placeholder="<?php esc_html_e( 'Keyword', 'wasico' ); ?>" name="match" type="text" class="form-control inp-text" v-model="filters.match">
		</div>

		<div class="form-group">
			<label for=""><?php esc_html_e( 'Looking for:', 'wasico' ); ?></label>
			<select class="selectpicker" name="for_type" id="for_type" v-model="filters.for_type">
				<option value="0"><?php esc_html_e( 'All', 'wasico' ); ?></option>
				<?php foreach ( $property_status as $key => $item ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>">' . <?php echo esc_html( $item ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="id_property_type"><?php esc_html_e( 'Type:', 'wasico' ); ?></label>
			<select class="selectpicker" name="id_property_type" id="id_property_type" v-model="filters.id_property_type">
				<option value="0"><?php esc_html_e( 'All', 'wasico' ); ?></option>
				<?php foreach ( $property_types as $item ) : ?>
					<option value="<?php echo esc_attr( $item->id_property_type ); ?>"><?php esc_html( $item->nombre ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="min_bedrooms"><?php esc_html_e( 'Bedrooms:', 'wasico' ); ?></label>
			<select class="selectpicker" name="min_bedrooms" id="min_bedrooms" v-model="filters.min_bedrooms">
				<option value="0"><?php esc_html_e( 'All', 'wasico' ); ?></option>
				<?php for ( $i = 1; $i <= 10; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
				<?php endfor; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="bathrooms"><?php esc_html_e( 'Bathrooms:', 'wasico' ); ?></label>
			<select class="selectpicker" name="bathrooms" id="bathrooms" v-model="filters.bathrooms">
				<option value="0"><?php esc_html_e( 'All', 'wasico' ); ?></option>
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
				<?php endfor; ?>
			</select>
		</div>

		<!-- LOCALE FILTERS BY: Country/Region/City/Zone -->
		<div class="form-group">
			<label for="contact-country"><?php esc_html_e( 'Your country', 'wasico' ); ?>:</label>
			<select class="selectpicker" name="contact-country" id="contact-country" 
				v-model="contact.id_country" v-on:change="changeLocationCountry">
				<option value="0"><?php esc_html_e( 'Select country', 'wasico' ); ?></option>
				<?php foreach ( $wasi_countries as $country ) : ?>
					<option value="<?php echo esc_attr( $country->id_country ); ?>"><?php echo esc_html( $country->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="contact-region"><?php esc_html_e( 'Region', 'wasico' ); ?>:</label>
			<select class="selectpicker" name="contact-region" id="contact-region" 
				v-model="contact.id_region" v-on:change="changeLocationRegion">
				<option v-for="region in location.regions" v-bind:value="region.id_region" v-html="region.name"></option>
			</select>
		</div>
		<div class="form-group">
			<label for="contact-city"><?php esc_html_e( 'City', 'wasico' ); ?>:</label>
			<select class="selectpicker" name="contact-city" id="contact-city" 
				v-model="contact.id_city" v-on:change="changeLocationCity">
				<option v-for="city in location.cities" v-bind:value="city.id_city" v-html="city.name"></option>
			</select>
		</div>
		<div class="form-group">
			<label for="contact-zone"><?php esc_html_e( 'Zone', 'wasico' ); ?>:</label>
			<select class="selectpicker" name="contact-zone" id="contact-zone" 
				v-model="contact.id_zone">
				<option v-for="zone in location.zones" v-bind:value="zone.id_zone" v-html="zone.name"></option>
			</select>
		</div>

		<div class="form-group">
			<label><?php esc_html_e( 'Prices range:', 'wasico' ); ?></label>
			<div class="<?php echo esc_attr( $instance['formClass'] ); ?>" style="padding: 0px;">
				<div class="col-xs-6 span6" style="padding: 1px;">
					<input
						id="min_price"
						placeholder="<?php esc_attr_e( 'Price min', 'wasico' ); ?>"
						name="min_price"
						type="number"
						class="form-control inp-text"
						v-model="filters.min_price"
					>
				</div>
				<div class="col-xs-6 span6" style="padding: 1px;">
					<input
						id="max_price"
						placeholder="<?php esc_attr_e( 'Price max', 'wasico' ); ?>"
						name="max_price"
						type="number"
						class="form-control inp-text"
						v-model="filters.max_price"
					>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label><?php esc_html_e( 'Area range:', 'wasico' ); ?></label>
			<div class="<?php echo esc_attr( $instance['formClass'] ); ?>" style="padding: 0px;">
				<div class="col-xs-6 span6" style="padding: 1px;">
					<input
						id="min_area"
						placeholder="<?php esc_attr_e( 'Area min', 'wasico' ); ?>"
						name="min_area"
						type="number"
						class="form-control inp-text"
						v-model="filters.min_area"
					>
				</div>
				<div class="col-xs-6 span6" style="padding: 1px;">
					<input
						id="max_area"
						placeholder="<?php esc_attr_e( 'Area max', 'wasico' ); ?>"
						name="max_area"
						type="number"
						class="form-control inp-text"
						v-model="filters.max_area"
					>
				</div>
			</div>
		</div>

		<div class="form-group col-xs-12">
			<button
				id="search-btn"
				class="<?php echo esc_attr( $instance['submitClass'] ); ?>"
				data-cleaned-text="!!"
				data-loading-text="<?php esc_html_e( 'Searching', 'wasico' ); ?>..."
			><?php esc_html_e( 'Search' ); ?></button>
		</div>
	</form>
</div>
