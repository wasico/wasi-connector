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

	private $plugin_name;
	private $version;
	private $context = 'wasico'; // used for i18n strings

	// used on settings page and register options
	private $settings_slug = 'api-wasi';
	private $options = 'api-wasi';

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('admin_menu', array($this,'register_settings_page'));
		add_action('admin_init', array($this,'register_api_settings'));


		// https://hugh.blog/2012/07/27/wordpress-add-plugin-settings-link-to-plugins-page/
		$name = $plugin_name.'/wasico.php';
        // die("<pre>".print_r('plugin_action_links_'.$name, true)."</pre>");
		add_filter('plugin_action_links_'.$name, array($this, 'plugin_add_settings_link') );


        // Ajax function to clear transients cache
        $ajaxWasi = array($this, 'clearTransientsCache');
        add_action( 'wp_ajax_wasi_clear_cache', $ajaxWasi );
        add_action( 'wp_ajax_nopriv_wasi_clear_cache', $ajaxWasi );
	}


    public function clearTransientsCache() {
        header('Content-Type: application/json');

        global $wpdb;
        $sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
            FROM  $wpdb->options
            WHERE `option_name` LIKE '%transient_wasi%'
            ORDER BY `option_name`";

        $results = $wpdb->get_results( $sql );
        foreach ( $results as $trans ) {
            $name = str_replace('_transient_', '', $trans->name);
            delete_transient($name);
        }

        echo json_encode(array(
            'cleared' => 'OK',
            'total' => count($results)
        ));
        // delete_transient();

        // echo json_encode('OK');
        wp_die();
    }

	public function plugin_add_settings_link($links) {
		$url = 'options-general.php?page='.$this->settings_slug;
		$links[] = '<a href="'. esc_url( get_admin_url(null, $url) ) .'">'.__('Settings').'</a>';

		return $links;
	}


	public function register_settings_page() {
		// add_submenu_page('options-general.php',...)
		$this->options = get_option( 'wasi_api_data' );
		add_options_page('API wasi.co', 'API wasi.co', 'manage_options', $this->settings_slug, array($this, 'include_admin_template'));
	}

	public function include_admin_template() {
		include_once('views/settings_page.php');
	}

	public function register_api_settings() {
		$wasi_option = 'wasi_api_data';
		$this->options = get_option( $wasi_option );
		// echo "<pre>Opts:".print_r($this->options, true)."</pre>";

		$args = array( $this, 'sanitize' );
		register_setting( 'wasi_api_group', $wasi_option, $args);

		add_settings_section('wasi_settings_section',
			__('API Data', $this->context),
			array($this, 'render_api_settings_info'),
			$this->settings_slug
		);

		add_settings_field(
            'id_company', // ID
            __('ID Company', $this->context), // Title 
            array( $this, 'render_input_id_company' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_section' // Section           
        );
        
        add_settings_field(
            'wasi_token', // ID
            __('Wasi Token', $this->context), // Title 
            array( $this, 'render_input_wasi_token' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_section' // Section           
        );


        add_settings_section('wasi_settings_wp',
            __('WP Settings', $this->context),
            array($this, 'render_wp_settings_info'),
            $this->settings_slug
        );

        add_settings_field(
            'wasi_max_per_page', // ID
            __('Properties per page', $this->context), // Title 
            array( $this, 'render_properties_per_page' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_wp' // Section           
        );

        add_settings_field(
            'wp_page', // ID
            __('Properties Page', $this->context), // Title 
            array( $this, 'render_select_page' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_wp' // Section           
        );

        add_settings_field(
            'wp_single_page', // ID
            __('Single Property Page', 'wasico'), // Title 
            array( $this, 'render_select_single_page' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_wp' // Section           
        );

        add_settings_field(
            'wasi_use_bootstrap', // ID
            __('UI Library', 'wasico'), // Title 
            array( $this, 'render_checkbox_bootstrap' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_wp' // Section
        );


        add_settings_field(
            'wasi_clear_transients', // ID
            __('Clear Cache', 'wasico'), // Title 
            array( $this, 'render_clear_cache' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_wp' // Section
        );
        add_settings_field(
            'wasi_cache_duration', // ID
            __('Cache Duration', $this->context), // Title 
            array( $this, 'render_cache_duration' ), // Callback
            $this->settings_slug, // Page
            'wasi_settings_wp' // Section           
        );
	}

    public function render_wp_settings_info() {
        echo __('Required settings to ensure the correct working of the plugin', 'wasico');
    }

	public function render_api_settings_info() {
		echo sprintf(__('Connect with your Wasi account by using your API access data (you can learn how to use it here: %s)', 'wasico'), '<a href="https://api.wasi.co/" target="_blank">https://api.wasi.co/</a>');
	}

	public function render_input_id_company($field) {
		printf(
            '<input class="wasi_input" type="text" id="id_company" name="wasi_api_data[id_company]" value="%s" />',
            isset( $this->options['id_company'] ) ? esc_attr( $this->options['id_company']) : ''
        );
	}

	public function render_input_wasi_token($field) {
		printf(
            '<input class="wasi_input" type="text" id="wasi_token" name="wasi_api_data[wasi_token]" value="%s" />',
            isset( $this->options['wasi_token'] ) ? esc_attr( $this->options['wasi_token']) : ''
        );
	}


    public function render_properties_per_page($field) {
        printf(
            '<input class="wasi_input" type="number" id="wasi_max_per_page" name="wasi_api_data[wasi_max_per_page]" value="%s" />',
            isset( $this->options['wasi_max_per_page'] ) ? esc_attr( $this->options['wasi_max_per_page']) : '10'
        );
    }

    // https://codex.wordpress.org/Function_Reference/wp_dropdown_pages
    public function render_select_page($field) {
        $args = array(
            'name' => 'wasi_api_data[properties_page]',
            'id' => 'properties_page',
            'show_option_none' => __('Please select page', $this->context).' '
        );

        if( isset($this->options['properties_page']) ) {
            $args['selected'] = $this->options['properties_page'];
        }
        wp_dropdown_pages($args);
        echo '<br /> <small>';
        echo __('Ensure to put the shortcode [wasi-properties] in the content of that page', 'wasico');
        echo '</small>';
    }

    public function render_select_single_page($field) {
        $args = array(
            'name' => 'wasi_api_data[property_single_page]',
            'id' => 'property_single_page',
            'show_option_none' => __('Please select page', $this->context).' '
        );

        if( isset($this->options['property_single_page']) ) {
            $args['selected'] = $this->options['property_single_page'];
        }
        wp_dropdown_pages($args);
        echo '<br /> <small>';
        echo __('The content of this page will change dinamically according to each property', 'wasico');
        echo '</small>';
    }


    public function render_checkbox_bootstrap($field) {
        $checked = (isset($this->options['wasi_bootstrap']) && $this->options['wasi_bootstrap']==='true') ? 'checked' : '';
        echo '<label><input class="wasi_input" type="checkbox" id="wasi_bootstrap" name="wasi_api_data[wasi_bootstrap]" value="load" '.$checked.' />'.__('Load Bootstrap 3', 'wasico').'</label>';
        echo '<br /> <small>';
        echo __('If you enabled this checkbox, the UI Library Bootstrap 3 will be loaded.<br />Use only if your theme is not using bootstrap.', 'wasico');
        echo '</small>';
    }


    public function render_clear_cache($field) {
        echo '<button id="wasi-clear-cache">'.__('Clear plugin cache', 'wasico').'</button>';
        echo '<span id="clear-cache-results"></span>';
        echo '<br/><small>';
        echo __('Clear internal plugin cache if your Wasi properties have been updated', 'wasico');
        echo '</small>';
    }
    public function render_cache_duration($field) {
        printf(
            '<input class="wasi_input" type="text" id="cache_duration" name="wasi_api_data[cache_duration]" value="%s" />',
            isset( $this->options['cache_duration'] ) ? esc_attr( $this->options['cache_duration']) : '7'
        );
        echo '<br /> <small>';
        echo __('Cache duration in days', 'wasico');
        echo '</small>';
    }


	/**
     * Sanitize each setting field as needed
     */
    public function sanitize( $input ) {

        $new_input = array();
        if( isset( $input['id_company'] ) )
            $new_input['id_company'] = absint( $input['id_company'] );

        if( isset( $input['wasi_token'] ) )
            $new_input['wasi_token'] = sanitize_text_field( $input['wasi_token'] );

        if( isset( $input['properties_page'] ) )
            $new_input['properties_page'] = $input['properties_page'];

        if( isset( $input['property_single_page'] ) )
            $new_input['property_single_page'] = $input['property_single_page'];


        if( isset( $input['wasi_max_per_page'] ) )
            $new_input['wasi_max_per_page'] = sanitize_text_field( $input['wasi_max_per_page'] );

        if( isset( $input['wasi_bootstrap'] ) )
            $new_input['wasi_bootstrap'] = 'true';
        else {
            $new_input['wasi_bootstrap'] = 'false';
        }

        if( isset( $input['cache_duration'] ) ) {
            $new_input['cache_duration'] = sanitize_text_field( $input['cache_duration'] );
            if(!is_numeric($new_input['cache_duration'])) {
                $new_input['cache_duration'] = 7;
            }

            if($new_input['cache_duration']<1 || $new_input['cache_duration']>365) {
                $new_input['cache_duration'] = 7;
            }
        }

        return $new_input;
    }


	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wasi-connector-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wasi-connector-admin.js', array( 'jquery' ), $this->version, false );
	}

}
