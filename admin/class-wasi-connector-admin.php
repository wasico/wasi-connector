<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/admin
 * @author     WasiCo, Inc <soporte@wasi.co>
 */
class Wasi_Connector_Admin {
	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The settings slug used on settings page and register options.
	 *
	 * @var string
	 */
	private $settings_slug = 'api-wasi';

	/**
	 * The user specific settings.
	 *
	 * @var string
	 */
	private $options = 'api-wasi';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_api_settings' ) );

		$this->add_settings_link_in_plugins_page();

		// Ajax function to clear transients cache.
		add_action( 'wp_ajax_wasi_clear_cache', array( $this, 'clear_transients_cache' ) );
		add_action( 'wp_ajax_nopriv_wasi_clear_cache', array( $this, 'clear_transients_cache' ) );
	}

	/**
	 * Add the settings link to the plugin list.
	 *
	 * @return void
	 *
	 * @link https://hugh.blog/2012/07/27/wordpress-add-plugin-settings-link-to-plugins-page
	 */
	private function add_settings_link_in_plugins_page() {
		add_filter( "plugin_action_links_$this->plugin_name/wasico.php", array( $this, 'plugin_add_settings_link' ) );
	}

	/**
	 * The clear trasients.
	 *
	 * @return void
	 */
	public function clear_transients_cache() {
		header( 'Content-Type: application/json' );

		global $wpdb;
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT `option_name` AS `name`, `option_value` AS `value`
				FROM  $wpdb->options
				WHERE `option_name` LIKE %s
				ORDER BY `option_name`",
				'%transient_wasi%'
			)
		);
		foreach ( $results as $trans ) {
			$name = str_replace( '_transient_', '', $trans->name );
			delete_transient( $name );
		}

		echo wp_json_encode(
			array(
				'cleared' => 'OK',
				'total'   => count( $results ),
			)
		);
		wp_die();
	}

	/**
	 * Add the setting link.
	 *
	 * @param array $links The links.
	 *
	 * @return array
	 */
	public function plugin_add_settings_link( $links ) {
		$url     = 'options-general.php?page=' . $this->settings_slug;
		$links[] = '<a href="' . esc_url( get_admin_url( null, $url ) ) . '">' . __( 'Settings' ) . '</a>';
		return $links;
	}

	/**
	 * Register the settings page in the admin menu.
	 *
	 * @return void
	 */
	public function register_settings_page() {
		$this->options = get_option( 'wasi_api_data' );
		add_options_page(
			'API wasi.co',
			'API wasi.co',
			'manage_options',
			$this->settings_slug,
			array( $this, 'include_admin_template' )
		);
	}

	/**
	 * Include the admin template.
	 *
	 * @return void
	 */
	public function include_admin_template() {
		include_once 'views/settings-page.php';
	}

	/**
	 * Register the API settings.
	 *
	 * @return void
	 */
	public function register_api_settings() {
		$wasi_option   = 'wasi_api_data';
		$this->options = get_option( $wasi_option );
		$args          = array( $this, 'sanitize' );
		register_setting( 'wasi_api_group', $wasi_option, $args );

		add_settings_section(
			'wasi_settings_section',
			__( 'API Data', 'wasico' ),
			array( $this, 'render_api_settings_info' ),
			$this->settings_slug
		);

		add_settings_field(
			'id_company', // ID.
			__( 'ID Company', 'wasico' ), // Title.
			array( $this, 'render_input_id_company' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_section' // Section.
		);

		add_settings_field(
			'wasi_token', // ID.
			__( 'Wasi Token', 'wasico' ), // Title.
			array( $this, 'render_input_wasi_token' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_section' // Section.
		);

		add_settings_section(
			'wasi_settings_wp',
			__( 'WP Settings', 'wasico' ),
			array( $this, 'render_wp_settings_info' ),
			$this->settings_slug
		);

		add_settings_field(
			'wasi_max_per_page', // ID.
			__( 'Properties per page', 'wasico' ), // Title.
			array( $this, 'render_properties_per_page' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_wp' // Section.
		);

		add_settings_field(
			'wp_page', // ID.
			__( 'Properties Page', 'wasico' ), // Title.
			array( $this, 'render_select_page' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_wp' // Section.
		);

		add_settings_field(
			'wp_single_slug', // ID.
			__( 'Single Property Path', 'wasico' ), // Title.
			array( $this, 'render_input_single_path' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_wp' // Section.
		);

		add_settings_field(
			'wasi_use_bootstrap', // ID.
			__( 'UI Library', 'wasico' ), // Title.
			array( $this, 'render_checkbox_bootstrap' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_wp' // Section.
		);

		add_settings_field(
			'wasi_clear_transients', // ID.
			__( 'Clear Cache', 'wasico' ), // Title.
			array( $this, 'render_clear_cache' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_wp' // Section.
		);
		add_settings_field(
			'wasi_cache_duration', // ID.
			__( 'Cache Duration', 'wasico' ), // Title.
			array( $this, 'render_cache_duration' ), // Callback.
			$this->settings_slug, // Page.
			'wasi_settings_wp' // Section.
		);
	}

	/**
	 * Render the API settings info.
	 *
	 * @return void
	 */
	public function render_wp_settings_info() {
		echo esc_html_e( 'Required settings to ensure the correct working of the plugin', 'wasico' );
	}

	/**
	 * Render the API settings info.
	 *
	 * @return void
	 */
	public function render_api_settings_info() {
		echo sprintf(
			/* translators: %s: The link to the API documentation */
			esc_html__( 'Connect with your Wasi account by using your API access data (you can learn how to use it here: %s)', 'wasico' ),
			'<a href="https://api.wasi.co/" target="_blank">https://api.wasi.co/</a>'
		);
	}

	/**
	 * Render the input field for the ID company.
	 *
	 * @return void
	 */
	public function render_input_id_company() {
		printf(
			'<input class="wasi_input" type="text" id="id_company" name="wasi_api_data[id_company]" value="%s" />',
			isset( $this->options['id_company'] ) ? esc_attr( $this->options['id_company'] ) : ''
		);
	}

	/**
	 * Render the input field for the Wasi Token.
	 *
	 * @return void
	 */
	public function render_input_wasi_token() {
		printf(
			'<input class="wasi_input" type="text" id="wasi_token" name="wasi_api_data[wasi_token]" value="%s" />',
			isset( $this->options['wasi_token'] ) ? esc_attr( $this->options['wasi_token'] ) : ''
		);
	}

	/**
	 * Render the input field for the per page value.
	 *
	 * @return void
	 */
	public function render_properties_per_page() {
		printf(
			'<input class="wasi_input" type="number" id="wasi_max_per_page" name="wasi_api_data[wasi_max_per_page]" value="%s" />',
			isset( $this->options['wasi_max_per_page'] ) ? esc_attr( $this->options['wasi_max_per_page'] ) : '10'
		);
	}

	/**
	 * Render the select field for the page.
	 *
	 * @return void
	 *
	 * @link https://codex.wordpress.org/Function_Reference/wp_dropdown_pages
	 */
	public function render_select_page() {
		wp_dropdown_pages(
			array(
				'name'             => esc_html( 'wasi_api_data[properties_page]' ),
				'id'               => esc_html( 'properties_page' ),
				'show_option_none' => esc_html__( 'Please select page', 'wasico' ),
				'selected'         => esc_html( isset( $this->options['properties_page'] ) ? $this->options['properties_page'] : 0 ),
			)
		);
		echo '<br /> <small>';
		esc_html_e( 'Ensure to put the shortcode [wasi-properties] in the content of that page', 'wasico' );
		echo '</small>';
	}

	/**
	 * Render the select field for the single page.
	 *
	 * @return void
	 */
	public function render_input_single_path() {
		printf(
			'<input class="wasi_input" type="text" id="property_single_path" name="wasi_api_data[property_single_path]" value="%s" />',
			isset( $this->options['property_single_path'] ) ? esc_attr( $this->options['property_single_path'] ) : ''
		);
	}

	/**
	 * Render the input field for check if must be use bootstrap.
	 *
	 * @return void
	 */
	public function render_checkbox_bootstrap() {
		$checked = ( isset( $this->options['wasi_bootstrap'] ) && 'true' === $this->options['wasi_bootstrap'] ) ? 'checked' : '';
		echo '<label><input class="wasi_input" type="checkbox" id="wasi_bootstrap" name="wasi_api_data[wasi_bootstrap]" value="load" ' . esc_html( $checked ) . ' />' . esc_html__( 'Load Bootstrap 3', 'wasico' ) . '</label>';
		echo '<br /> <small>';
		esc_html_e( 'If you enabled this checkbox, the UI Library Bootstrap 3 will be loaded.<br />Use only if your theme is not using bootstrap.', 'wasico' );
		echo '</small>';
	}

	/**
	 * Render the input field for the clear cache button.
	 *
	 * @return void
	 */
	public function render_clear_cache() {
		echo '<button id="wasi-clear-cache">' . esc_html__( 'Clear plugin cache', 'wasico' ) . '</button>';
		echo '<span id="clear-cache-results"></span>';
		echo '<br/><small>';
		echo esc_html__( 'Clear internal plugin cache if your Wasi properties have been updated', 'wasico' );
		echo '</small>';
	}

	/**
	 * Render the input field for the cache duration.
	 *
	 * @return void
	 */
	public function render_cache_duration() {
		printf(
			'<input class="wasi_input" type="text" id="cache_duration" name="wasi_api_data[cache_duration]" value="%s" />',
			isset( $this->options['cache_duration'] ) ? esc_attr( $this->options['cache_duration'] ) : '7'
		);
		echo '<br /> <small>';
		echo esc_html__( 'Cache duration in days', 'wasico' );
		echo '</small>';
	}


	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input The submitted input.
	 *
	 * @return array The sanitized input.
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['id_company'] ) ) {
			$new_input['id_company'] = absint( $input['id_company'] );
		}
		if ( isset( $input['wasi_token'] ) ) {
			$new_input['wasi_token'] = sanitize_text_field( $input['wasi_token'] );
		}
		if ( isset( $input['properties_page'] ) ) {
			$new_input['properties_page'] = $input['properties_page'];
		}
		if ( isset( $input['property_single_path'] ) ) {
			$new_input['property_single_path'] = $input['property_single_path'];
		}

		if ( isset( $input['wasi_max_per_page'] ) ) {
			$new_input['wasi_max_per_page'] = sanitize_text_field( $input['wasi_max_per_page'] );
		}
		if ( isset( $input['wasi_bootstrap'] ) ) {
			$new_input['wasi_bootstrap'] = 'true';
		} else {
			$new_input['wasi_bootstrap'] = 'false';
		}

		if ( isset( $input['cache_duration'] ) ) {
			$new_input['cache_duration'] = sanitize_text_field( $input['cache_duration'] );
			if ( ! is_numeric( $new_input['cache_duration'] ) ) {
				$new_input['cache_duration'] = 7;
			}

			if ( $new_input['cache_duration'] < 1 || $new_input['cache_duration'] > 365 ) {
				$new_input['cache_duration'] = 7;
			}
		}

		return $new_input;
	}

	/**
	 * Queue admin styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/wasi-connector-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Queue admin scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/wasi-connector-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

}
