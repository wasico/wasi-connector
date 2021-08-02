<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/public
 */
class Wasi_Connector_Public {
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
	 * The Wasi API client.
	 *
	 * @var Wasi_Api_Client
	 */
	private $api;

	/**
	 * The API configuration data.
	 *
	 * @var array
	 */
	private $api_data;

	/**
	 * Used to temporal store of single property and avoid double API call.
	 *
	 * @var object|null
	 */
	private $single_property = null;

	/**
	 * The Wasi_Connector_Public constructor.
	 *
	 * @param string $plugin_name The plugin name.
	 * @param string $version    The plugin version.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->api_data    = get_option( 'wasi_api_data' );

		$this->declare_shortcodes();
		$this->declare_widgets();
		$this->include_templates();
	}

	/**
	 * Declare shortcodes.
	 *
	 * @return void
	 */
	private function declare_shortcodes() {
		add_shortcode( 'wasi-properties', array( $this, 'wasi_properties_shortcode' ) );
		add_shortcode( 'wasi-search', array( $this, 'init_search_shortcode' ) );
	}

	/**
	 * Declare widgets.
	 *
	 * @return void
	 */
	private function declare_widgets() {
		add_action( 'widgets_init', array( $this, 'init_wasi_widgets' ) );
	}

	/**
	 * Include templates.
	 *
	 * @return void
	 */
	private function include_templates() {
		add_filter( 'template_include', array( $this, 'wasi_single_page' ), 99 );
	}

	/**
	 * Create the single property template.
	 *
	 * @param string $template The template name to load.
	 *
	 * @return string The template name.
	 */
	public function wasi_single_page( $template ) {
		global $post;
		if ( $this->is_single_property_page() ) {
			$id_property = $this->get_single_id_property();
			if ( is_numeric( $id_property ) && 0 !== $id_property ) {
				$this->single_property = $this->api->get_property( $id_property );
				global $post;
				if ( is_wp_error( $this->single_property ) ) {
					$post->post_title   = 'WASI API Error!';
					$post->post_content = $this->single_property->get_error_message();
					return $template;
				}
				$post->post_title   = $this->single_property->title;
				$post->post_content = ''; // removed post content to load it from Wasi property on the_content.
				$post->post_date    = $this->single_property->created_at;

				add_filter( 'the_content', array( $this, 'wasi_single_property' ), 1 );

				// Add script gallery only on this page.
				$unite_js_url  = plugin_dir_url( __FILE__ ) . 'gallery/js/unitegallery.js';
				$unite_css_url = plugin_dir_url( __FILE__ ) . 'gallery/css/unite-gallery.css';
				wp_enqueue_script( 'unitejs', $unite_js_url, array( 'jquery' ), '1.7.28', true );
				wp_enqueue_style( 'unitecss', $unite_css_url, array(), '1.7.28', 'all' );

				$unite_js_url  = plugin_dir_url( __FILE__ ) . 'gallery/themes/default/ug-theme-default.js';
				$unite_css_url = plugin_dir_url( __FILE__ ) . 'gallery/themes/default/ug-theme-default.css';
				wp_enqueue_script( 'unitejs-theme', $unite_js_url, array( 'unitejs' ), '1.7.28', true );
				wp_enqueue_style( 'unitecss-theme', $unite_css_url, array( 'unitecss' ), '1.7.28', 'all' );
				return get_template_directory() . '/page.php';
			}
		}
		return $template;
	}

	/**
	 * Return the API configuration data.
	 *
	 * @return array
	 */
	public function get_wasi_data() {
		return $this->api_data;
	}

	/**
	 * Get the Wasi API client.
	 *
	 * @return Wasi_Api_Client
	 */
	public function get_api_client() {
		return $this->api;
	}

	/**
	 * Verify if the current page is the single property page.
	 *
	 * @return boolean
	 */
	private function is_single_property_page() {
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		return strpos( $uri, $this->get_single_path() ) !== false;
	}

	/**
	 * Set the Wasi API client.
	 *
	 * @param Wasi_Api_Client $api The Wasi API client.
	 *
	 * @return void
	 */
	public function set_api_client( $api ) {
		$this->api = $api;
	}

	/**
	 * Render the properties list in the properties shortcode.
	 *
	 * @param array $atts The user defined attributes.
	 *
	 * @return string The properties list HTML.
	 */
	public function wasi_properties_shortcode( $atts ) {
		require_once 'shortcode.properties.php';
		return render_wasi_properties( $this, $atts );
	}

	/**
	 * Get the single property ID from the current URI.
	 *
	 * @return integer The property ID from the URI.
	 */
	public function get_single_id_property() {
		$id_property = get_query_var( $this->get_single_path() );
		if ( is_numeric( $id_property ) ) {
			return (int) $id_property;
		}
		return 0;
	}

	/**
	 * Get the configured single property path.
	 *
	 * @return string The single property path.
	 */
	public function get_single_path() {
		$data = $this->get_wasi_data();
		if ( empty( $data ) || empty( $data['property_single_path'] ) ) {
			return '';
		}
		return $data['property_single_path'];
	}

	/**
	 * Render a single property
	 **/
	public function wasi_single_property() {
		$atts = array();

		ob_start();
		$id_property = $this->get_single_id_property();
		if ( $id_property > 0 ) {
			if ( null === $this->single_property ) {
				// this should never be executed... is only in a remote case!
				$this->single_property = $this->api->get_property( $id_property );
			}
			// This variables will be used in the template.
			$single_property = $this->single_property;
			$wasi_lang       = 'wasico';
			$property_types  = $this->api->get_property_types();

			// Allow theme override single-property.
			$overridden_template = locate_template( 'single-property-wasi.php' );
			if ( $overridden_template ) {
				include $overridden_template;
			} else {
				include dirname( __FILE__ ) . '/views/single-property.php';
			}
		} else {
			esc_html_e( 'Property not found!', 'wasico' );
		}

		$out = ob_get_clean();
		return $out;
	}

	/**
	 * Register the plugin widgets.
	 *
	 * @return void
	 */
	public function init_wasi_widgets() {
		include 'class-wasi-search-widget.php';
		$search_widget = new Wasi_Search_Widget( $this->api );
		register_widget( $search_widget );

		include 'widget.contact.php';
		$contact_widget = new Wasi_Contact_Widget( $this, $this->api );
		register_widget( $contact_widget );
	}

	/**
	 * Register the plugin search shortcodes.
	 *
	 * @param array $atts The user defined attributes.
	 *
	 * @return string The search HTML
	 */
	public function init_search_shortcode( $atts ) {
		include 'shortcode.search.php';
		return render_wasi_search( $this, $atts );
	}

	/**
	 * Queue public stylesheets.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/wasi-connector-public.css',
			array(),
			$this->version,
			'all'
		);
		$use_bootstrap = isset( $this->api_data['wasi_bootstrap'] ) ? $this->api_data['wasi_bootstrap'] : '';
		if ( 'true' === $use_bootstrap ) {
			$bootstrap_css = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
			wp_enqueue_style( 'bootstrap', $bootstrap_css, array(), '3.3.7', 'all' );
		}
	}

	/**
	 * Queue public javascripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$vue     = 'vue.min.js';
		$vue_url = plugin_dir_url( __FILE__ ) . 'js/libs/' . $vue;
		wp_enqueue_script( 'vuejs', $vue_url, array( 'jquery' ), '2.5.6', true );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wasi-connector-public.js', array( 'vuejs' ), $this->version, true );

		$use_bootstrap = isset( $this->api_data['wasi_bootstrap'] ) ? $this->api_data['wasi_bootstrap'] : '';
		if ( 'true' === $use_bootstrap ) {
			$bootstrap_js = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js';
			wp_enqueue_script( 'bootstrap', $bootstrap_js, array( 'jquery' ), '3.3.7', true );
		}

		// Add data before JS plugin.
		add_action( 'wp_footer', array( $this, 'create_public_js_vars' ), 1 );
	}

	/**
	 * Create public JS Variables to pass to external script.
	 *
	 * @return void
	 */
	public function create_public_js_vars() {
		$is_properties_page = $this->is_properties_page();
		$vars               = 'var ajax_url="' . admin_url( 'admin-ajax.php' ) . '";';
		$vars              .= 'var is_properties_page=' . $is_properties_page . ';';

		$vars .= 'var wasi_properties_page="' . get_post( $this->api_data['properties_page'] )->post_name . '";';

		if ( ! isset( $this->api_data['wasi_max_per_page'] ) ) {
			$this->api_data['wasi_max_per_page'] = 10; // Default option according to the API.
		}
		$vars .= 'var properties_per_page=' . $this->api_data['wasi_max_per_page'] . ';';

		if ( $this->is_single_property_page() ) {
			$id_property = $this->get_single_id_property();
			if ( $id_property > 0 ) {
				if ( null === $this->single_property ) {
					// this should never be executed... is only in a remote case!
					$this->single_property = $this->api->get_property( $id_property );
				}
				$vars .= 'var id_property = ' . $id_property . ';';
				$vars .= 'var id_user_property = ' . $this->single_property->id_user . ';';
			}
		}

		$vars .= 'var default_region_name="' . __( 'Select region', 'wasico' ) . '";';
		$vars .= 'var default_city_name="' . __( 'Select city', 'wasico' ) . '";';
		$vars .= 'var default_zone_name="' . __( 'Select zone', 'wasico' ) . '";';

		echo '<script>' . $vars . '</script>';
	}

	/**
	 * Verify if the current page is the properties page.
	 *
	 * @return boolean
	 */
	private function is_properties_page() {
		$page = $this->api_data['properties_page'];
		global $post;
		if ( $post && $post->ID ) {
			return $post->ID === $page ? 'true' : 'false';
		}
		return 'false';
	}
}
