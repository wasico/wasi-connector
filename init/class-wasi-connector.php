<?php
/**
 * The core plugin class.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package Wasi_Connector
 * @subpackage Wasi_Connector/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/includes
 * @author     WasiCo, Inc <soporte@wasi.co>
 */
class Wasi_Connector {


	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Language Context
	 *
	 * @var string
	 */
	protected $lang_context;

	/**
	 * API Connector
	 *
	 * @var Wasi_Api_Client
	 */
	private $api;

	/**
	 * The admin-specific class instance.
	 *
	 * @var Wasi_Connector_Admin
	 */
	private $plugin_admin;

	/**
	 * The public-facing class instance.
	 *
	 * @var Wasi_Connector_Public
	 */
	public $plugin_public;

	/**
	 * The instance of this class.
	 */
	public function __construct() {
		if ( defined( 'WASICO_VERSION' ) ) {
			$this->version = WASICO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name  = 'wasi-connector';
		$this->lang_context = 'wasico';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();

		if ( is_admin() ) {
			$this->define_admin_hooks();
		}
	}

	/**
	 * Load common, admin and public dependencies for this plugin.
	 */
	private function load_dependencies() {
		// API Connector.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/class-wasi-api-client.php';
		$this->api = new Wasi_Api_Client( $this->lang_context );

		// Admin Settings.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wasi-connector-admin.php';

		// Includes widgets, shortcodes, and public templates.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wasi-connector-public.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$rel_path = dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/';
		load_plugin_textdomain( 'wasico', false, $rel_path );
	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$this->plugin_admin = new Wasi_Connector_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', array( $this->plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this->plugin_admin, 'enqueue_scripts' ) );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * like shortcodes, widgets, templates, public assets, etc...
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->plugin_public = new Wasi_Connector_Public(
			$this->get_plugin_name(),
			$this->get_version()
		);

		$this->plugin_public->set_api_client( $this->api );

		add_action( 'wp_enqueue_scripts', array( $this->plugin_public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this->plugin_public, 'enqueue_scripts' ) );

		$this->add_rewrite_rule();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Add rewrite rule for the plugin.
	 *
	 * @return void
	 */
	private function add_rewrite_rule() {
		$path = $this->plugin_public->get_single_path();
		if ( empty( $path ) ) {
			return;
		}
		add_action(
			'init',
			function () use ( $path ) {
				add_rewrite_rule( "$path/([a-z0-9-]+)[/]?$", "index.php?$path=\$matches[1]", 'top' );
			}
		);

		add_filter(
			'query_vars',
			function( $query_vars ) use ( $path ) {
				$query_vars[] = $path;
				return $query_vars;
			}
		);
	}
}
