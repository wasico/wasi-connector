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
class Wasi_Connector_Public {

	private $plugin_name;
	private $version;
	private $lang_context = 'wasico'; // used for i18n strings

	private $api; // Wasi API Client
	private $api_data;

	// used to temporal store of single property and avoid double API call
	private $single_property = null;

	public function __construct( $plugin_name, $version,  $lang_context = 'wasico' ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->lang_context = $lang_context;
		$this->api_data = get_option( 'wasi_api_data' );


		// Declaration of shortcodes and widgets
		add_shortcode( 'wasi-properties', array($this, 'wasiPropertiesShortcode') );
		add_shortcode( 'wasi-single-property', array($this, 'wasiSingePropertyShortcode') );
		add_shortcode( 'wasi-search', array($this, 'initSearchShortcode') );

		add_action( 'widgets_init', array($this, 'initWasiWidgets'));

		add_filter( 'template_include', array($this, 'wasi_single_page'), 99 );
	}

	public function wasi_single_page($template) {
		$property_page = $this->api_data['property_single_page'];
		$wp_wasi_post = get_post($property_page);
		global $post;
		if( is_object($wp_wasi_post) && is_object($post) && $wp_wasi_post->ID === $post->ID) {
			// $new_template = locate_template( array( 'portfolio-page-template.php' ) );
			$new_template = plugin_dir_path( __FILE__ ) . 'views/single-property.php';
			// return $new_template;

			$fullpath = explode("/", $_SERVER['REQUEST_URI']);
			$id_property = $fullpath[ count($fullpath)-2 ];
			if (is_numeric($id_property)) {
				$this->single_property = $this->api->getProperty($id_property);
				if (is_wp_error( $this->single_property ) ) {
					$post->post_title = "WASI API Error!";
					$post->post_content = $this->single_property->get_error_message();
					
					return $template;
				}

				global $post;
				// die("<pre>".print_r($this->single_property, true)."</pre>");
				$post->post_title = $this->single_property->title;
				$post->post_content = ''; // removed post content to load it from Wasi property on the_content
				$post->post_date = $this->single_property->created_at;
				add_filter('the_content', array($this, 'wasiSingleProperty'));

				// add script gallery only on this page
				$unite_js_url = plugin_dir_url( __FILE__ ) . 'gallery/js/unitegallery.js';
				$unite_css_url = plugin_dir_url( __FILE__ ) . 'gallery/css/unite-gallery.css';
				wp_enqueue_script( 'unitejs', $unite_js_url, array( 'jquery' ), '1.7.28', true );
				wp_enqueue_style( 'unitecss', $unite_css_url, array(), '1.7.28', 'all' );

				$unite_js_url = plugin_dir_url( __FILE__ ) . 'gallery/themes/default/ug-theme-default.js';
				$unite_css_url = plugin_dir_url( __FILE__ ) . 'gallery/themes/default/ug-theme-default.css';
				wp_enqueue_script( 'unitejs-theme', $unite_js_url, array('unitejs'), '1.7.28', true );
				wp_enqueue_style( 'unitecss-theme', $unite_css_url, array('unitecss'), '1.7.28', 'all' );
			}
		}

		return $template;
	}

	// return the wp admin options
	public function getWasiData() {
		return $this->api_data;
	}

	public function getAPIClient() {
		return $this->api;
	}

	public function setAPIClient($api) {
		$this->api = $api;
	}

	/**  Render Properties LIST **/
	public function wasiPropertiesShortcode( $atts ) {
		require_once("shortcode.properties.php");

		return renderWasiProperties( $this, $atts );
	}

	public function getSingleIdProperty() {
		$fullpath = explode("/", $_SERVER['REQUEST_URI']);
		$id_property = $fullpath[ count($fullpath)-2 ];

		if (is_numeric($id_property)) {
			return $id_property;
		} else {
			return 0;
		}
	}


	/**  Render a SINGLE Property **/
	public function wasiSingleProperty( ) {
		// if(!$atts) { $atts = []; }
		$atts = [];

		$id_property = $this->getSingleIdProperty();
		if ($id_property>0) {

			if($this->single_property===null) {
				// this should never be executed... is only in a remote case!
				$this->single_property = $this->api->getProperty($id_property);
			}
			
			// set this var to be used in the template:
			$single_property = $this->single_property;
			$wasi_lang = $this->lang_context;
			$property_types = $this->api->getPropertyTypes();

			// allow theme override single-property
			if ( $overridden_template = locate_template( 'single-property.php' ) ) {
			   // load_template( $overridden_template );
				require_once($overridden_template);
			} else {
			   // load_template( dirname( __FILE__ ) . '/views/single-property.php' );
				require_once(dirname( __FILE__ ) . '/views/single-property.php');
			}
		} else {
			echo __('Property not found!', $this->lang_context);
		}
	}

	/**  Render SEARCH Widget **/
	public function initWasiWidgets() {
		require_once("widget.search.php");
		$searchWidget = new Search_Widget($this->lang_context, $this->api);
		register_widget( $searchWidget );


		require_once("widget.contact.php");
		$contactWidget = new Wasi_Contact_Widget($this, $this->api);
		register_widget( $contactWidget );
	}

	public function initSearchShortcode( $atts ) {
		require_once("shortcode.search.php");

		return renderWasiSearch( $this, $atts );
	}


	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wasi-connector-public.css', array(), $this->version, 'all' );

		$use_bootstrap = isset($this->api_data['wasi_bootstrap']) ? $this->api_data['wasi_bootstrap'] : '';
		if($use_bootstrap==='true') {
			$bootstrap_css = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css';
			wp_enqueue_style( 'bootstrap', $bootstrap_css, array(), null, 'all' );
		}
	}

	public function enqueue_scripts() {
		$vue = 'vue.min.js';
		if(strpos($_SERVER['HTTP_HOST'], 'local')>=0) {
			$vue = 'vue.js';
		}
		$vue_url = plugin_dir_url( __FILE__ ) . 'js/libs/'.$vue;
		wp_enqueue_script( 'vuejs', $vue_url, array( 'jquery' ), '2.5.6', true );

		
		// wp_enqueue_script( 'moonjs', 'https://unpkg.com/moonjs', array( 'jquery' ), '0.11.0', true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wasi-connector-public.js', array( 'vuejs' ), $this->version, true );

		$use_bootstrap = isset($this->api_data['wasi_bootstrap']) ? $this->api_data['wasi_bootstrap'] : '';
		if($use_bootstrap==='true') {
			$bootstrap_js = 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js';
			wp_enqueue_script( 'bootstrap', $bootstrap_js, array( 'jquery' ), null, true );
		}

		// add data before JS plugin
		// $js_vars = ;
		add_action( 'wp_footer', array($this, 'createPublicJSvars'));
		// wp_add_inline_script($this->plugin_name, $js_vars, 'before');
	}

	// Create public JS Variables to pass to external script
	public function createPublicJSvars () {
		$is_properties_page = $this->isPropertiesPage();
		$vars = 'var ajax_url="'.admin_url( 'admin-ajax.php' ).'";';
		$vars.= 'var is_properties_page='.$is_properties_page.';';

		$vars.= 'var wasi_properties_page="'.get_post($this->api_data['properties_page'])->post_name.'";';

		if(!isset($this->api_data['wasi_max_per_page'])) {
			$this->api_data['wasi_max_per_page'] = 10; // default option according to the API
		}
		$vars.= 'var properties_per_page='.$this->api_data['wasi_max_per_page'].';';

		if ( is_page() ) {
			$id_property = $this->getSingleIdProperty();
			if ($id_property>0) {
				if($this->single_property===null) {
					// this should never be executed... is only in a remote case!
					$this->single_property = $this->api->getProperty($id_property);
				}
				$vars .= 'var id_property = '.$id_property.';';
				$vars .= 'var id_user_property = '.$this->single_property->id_user.';';
			}
		}

		// return $vars;
		echo '<script>'.$vars.'</script>';
	}

	private function isPropertiesPage() {
		$page = $this->api_data['properties_page'];

		global $post;
		if($post && $post->ID) {
			return ($post->ID==$page) ? 'true' : 'false';
		}
		return 'false';
	}

}
