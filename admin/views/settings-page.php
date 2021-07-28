<?php
/**
 * The settings page.
 *
 * @package    Wasi_Connector
 */

?>

<div class="wrap wasi-settings">
	<h1><?php esc_html_e( 'Wasi.co', 'wasico' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'wasi_api_group' ); ?>

		<?php do_settings_sections( 'api-wasi' ); ?>

		<?php submit_button(); ?>

	</form>
</div>
