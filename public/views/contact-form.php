<?php
/**
 * Render the contact form.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 */

?>
<div id="wasiContactApp" class="wasi_contact">
	<form action="#" method="POST" role="form" class="<?php echo esc_attr( $instance['formClass'] ); ?>"
		v-on:submit.prevent="wasiContactOwner">

		<div class="form-group">
			<label for="contact-name">* <?php esc_html_e( 'Your name', 'wasico' ); ?>:</label>
			<input id="contact-name" name="contact-name" type="text" class="form-control inp-text" v-model="contact.name" required>
		</div>

		<div class="form-group">
			<label for="contact-email">* <?php esc_html_e( 'Your email', 'wasico' ); ?>:</label>
			<input id="contact-email" name="contact-email" type="text" class="form-control inp-text" v-model="contact.email" required>
		</div>

		<div class="form-group">
			<label for="contact-phone"><?php esc_html_e( 'Your phone', 'wasico' ); ?>:</label>
			<input id="contact-phone" name="contact-phone" type="text" class="form-control inp-text" v-model="contact.phone">
		</div>


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
				v-model="contact.id_city">
				<option v-for="city in location.cities" v-bind:value="city.id_city" v-html="city.name"></option>
			</select>
		</div>


		<div class="form-group">
			<label for="contact-message">* <?php esc_html_e( 'Message', 'wasico' ); ?>:</label>
			<textarea id="contact-message" name="contact-message" required
				class="form-control" rows="3" v-model="contact.message"
				placeholder="<?php esc_html_e( 'I\'m interested on this property.', 'wasico' ); ?>"></textarea>
		</div>

		<div class="form-group col-xs-12">
			<button id="wasi-contact-btn" class="<?php echo esc_attr( $instance['submitClass'] ); ?>"
				data-cleaned-text="!!" data-loading-text="Sending..."><?php esc_html_e( 'Send' ); ?></button>
			<span id="contact-ajax-send" class="hidden">
				<img src="data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==" alt="Send">
			</span>
		</div>
		<div class="alert alert-success" role="alert" id="wasiResponseContact" style="display: none"></div>

	</form>
</div>
