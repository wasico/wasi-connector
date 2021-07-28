<?php
/**
 * The HTTP client for connect to the Wasi API.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/api
 */

/**
 * The WASI API Client
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/api
 */
class Wasi_Api_Client {
	/**
	 * The Wasi API base URL.
	 *
	 * @var string
	 */
	private $api_host = 'https://api.wasi.co/v1';

	/**
	 * The Wasi API data.
	 *
	 * @var array|null
	 */
	private $api_data = null;

	/**
	 * The instance of this class.
	 */
	public function __construct() {
		$this->api_data = get_option( 'wasi_api_data' );

		// Expose public ajax endpoints.
		$ajax_wasi = array( $this, 'ajax_api' );
		add_action( 'wp_ajax_wasi_api', $ajax_wasi );
		add_action( 'wp_ajax_nopriv_wasi_api', $ajax_wasi );
	}

	/**
	 * Ajax endpoint for API calls.
	 *
	 * @return void
	 */
	public function ajax_api() {
		if ( ! isset( $_POST['endpoint'] ) ) {
			echo 'Invalid endpoint params!';
			wp_die();
		}
		$filters = isset( $_POST['api_data'] ) ? $_POST['api_data'] : array();
		if ( isset( $filters['for_type'] ) ) {
			$filters[ $filters['for_type'] ] = 'true';
			unset( $filters['for_type'] );
		}
		// Clean unused filters...
		foreach ( $filters as $key => $value ) {
			if ( '0' === $value || empty( $value ) ) {
				unset( $filters[ $key ] );
			}
		}

		$p = $this->call_api( $_POST['endpoint'], $filters );
		echo wp_json_encode( $p );
		wp_die();
	}

	/**
	 * +----------------------
	 * | PROPERTIES
	 * +----------------------
	 */

	/**
	 * Fetch the specified property.
	 *
	 * @param string|integer $id The ID of the property.
	 *
	 * @return array|WP_Error
	 */
	public function get_property( $id ) {
		return $this->call_api( "/property/get/$id" );
	}

	/**
	 * Get the property states.
	 *
	 * @return array
	 */
	public function get_property_status() {
		return array(
			'for_rent'     => __( 'For Rent', 'wasico' ),
			'for_sale'     => __( 'For Sale', 'wasico' ),
			'for_transfer' => __( 'For Transfer', 'wasico' ),
		);
	}

	/**
	 * Get the rent types.
	 *
	 * @return array
	 */
	public function get_rent_types() {
		return array(
			1 => __( 'Daily', 'wasico' ),
			2 => __( 'Weekly', 'wasico' ),
			3 => __( 'Biweekly', 'wasico' ),
			4 => __( 'Monthly', 'wasico' ),
		);
	}

	/**
	 * Fetch the name of the specified property type ID.
	 *
	 * @param integer|string $id_type The ID of the property type.
	 *
	 * @return string The property type name.
	 */
	public function get_property_type( $id_type ) {
		$types = $this->get_property_types();
		foreach ( $types as $type ) {
			if ( (int) $type->id_property_type === (int) $id_type ) {
				return $type->name;
			}
		}
		return __( 'Property Type not found!', 'wasico' );
	}

	/**
	 * Get the property types.
	 *
	 * @return array|WP_Error
	 *
	 * @link http://api.wasi.co/docs/en/guide/properties.html#property-types
	 */
	public function get_property_types() {
		return $this->call_api( '/property-type/all' );
	}

	/**
	 * Get the property areas.
	 *
	 * @return array|WP_Error
	 *
	 * @link http://api.wasi.co/docs/en/guide/properties.html#areas-range
	 */
	public function get_property_areas() {
		return $this->call_api( '/property/area-range' );
	}

	/**
	 * Get the price ranges.
	 *
	 * @return array|WP_Error
	 *
	 * @link http://api.wasi.co/docs/en/guide/properties.html#prices-range
	 */
	public function get_price_ranges() {
		return $this->call_api( '/property/price-range' );
	}

	/**
	 * Get the property owners.
	 *
	 * @param string|integer $id_property The ID of the property.
	 *
	 * @return array|WP_Error
	 *
	 * @link http://api.wasi.co/docs/en/guide/properties.html#owners
	 */
	public function get_owners( $id_property ) {
		return $this->call_api( "/property/owner/$id_property" );
	}

	/**
	 * +----------------------
	 * | USERS
	 * +----------------------
	 */

	/**
	 * Get the specified user.
	 *
	 * @param integer|string $id_user The ID of the user.
	 *
	 * @return array|WP_Error The user data.
	 *
	 * @link http://api.wasi.co/docs/en/guide/users.html#get-a-user
	 */
	public function get_user( $id_user ) {
		return $this->call_api( "/user/get/$id_user" );
	}

	/**
	 * +----------------------
	 * | LOCATIONS
	 * +----------------------
	 */

	/**
	 * Get the countries.
	 *
	 * @return array|WP_Error
	 */
	public function get_countries() {
		return $this->call_api( '/location/all-countries' );
	}

	/**
	 * Get the regions of the country.
	 *
	 * @param integer|string $id_country The ID of the country.
	 *
	 * @return array|WP_Error The regions.
	 */
	public function get_regions( $id_country ) {
		return $this->call_api( "/location/regions-from-country/$id_country" );
	}

	/**
	 * Get the cities of the region.
	 *
	 * @param integer|string $id_region The ID of the region.
	 *
	 * @return array|WP_Error The cities.
	 */
	public function get_cities( $id_region ) {
		return $this->call_api( "/location/cities-from-region/$id_region" );
	}

	/**
	 * Get the zones of the city.
	 *
	 * @param integer|string $id_city The ID of the city.
	 *
	 * @return array|WP_Error The zones.
	 */
	public function get_zones( $id_city ) {
		return $this->call_api( "/location/zones-from-city/$id_city" );
	}

	/**
	 * +----------------------
	 * | CUSTOMERS
	 * +----------------------
	 */

	/**
	 * Add a new customer.
	 *
	 * @param array $customer The customer data.
	 *
	 * @return array|WP_Error
	 */
	public function add_customer( $customer ) {
		$c = $this->call_api( '/customer/add', $customer );
		if ( is_wp_error( $c ) ) {
			$query = array( 'query' => $customer['email'] );
			$cli   = $this->call_api( '/client/search', $query );
			foreach ( $cli as $key => $value ) {
				if ( '0' === $key ) {
					return $value;
				}
			}
			return new WP_Error( 'API', __( 'Client not found', 'wasico' ) );
		} else {
			return $c;
		}
	}

	/**
	 * Relate a customer to a property.
	 *
	 * @param integer|string $id_property The ID of the property.
	 * @param integer|string $id_client The ID of the client.
	 * @param integer|string $id_client_type The ID of the client type.
	 *
	 * @return array|WP_Error The status of the operation.
	 *
	 * @link http://api.wasi.co/docs/en/guide/clients.html#properties
	 * @link http://api.wasi.co/docs/en/guide/clients.html#client-types
	 */
	public function add_client_to_property( $id_property, $id_client, $id_client_type = 7 ) {
		$params = array(
			'id_client'      => $id_client,
			'id_client_type' => $id_client_type,
		);
		return $this->call_api( "/client/add-property/$id_property", $params );
	}

	/**
	 * HTTP API Call. Also Remote HTTP Call with cache usage through wp transients.
	 *
	 * @param boolean|string $url The URL to call.
	 * @param array          $params The params to send.
	 * @param string         $method The method to use.
	 * @param boolean        $force_save_trans If must force the trasient saving.
	 *
	 * @return boolean|array|object
	 */
	private function call_api( $url = false, $params = array(), $method = 'GET', $force_save_trans = false ) {
		if ( $url ) {
			$create_trans   = false;
			$url_trans_name = 'wasi' . str_replace( '/', '-', $url );

			// Cache duration from options of default 7 days...
			$cache_days = isset( $this->api_data['cache_duration'] ) ? $this->api_data['cache_duration'] : 7;
			if ( $cache_days <= 0 ) {
				$cache_days = 1;
			}
			// By default enable trans for requests without params or only with pagination (take param).
			if ( empty( $params ) || ( 1 === count( $params ) && isset( $params['take'] ) ) ) {
				$url_trans_name .= isset( $params['take'] ) ? '_' . $params['take'] : '';
				$trans           = get_transient( $url_trans_name );
				if ( $trans ) {
					if ( isset( $_GET['wasi-reset-api'] ) ) {
						delete_transient( $url_trans_name );
					}
					return $trans;
				}
				// If the transient is not available, we need to create it!
				$create_trans = true;
			}

			// Add WASI API Key and Token...
			$params = array_merge( $params, $this->api_data, array( 'format' => 'json' ) );
			unset( $params['properties_page'], $params['property_single_path'], $params['wasi_max_per_page'] );
			unset( $params['wasi_bootstrap'], $params['cache_duration'] );

			$response = wp_remote_request(
				$this->api_host . $url,
				array(
					'method'  => $method,
					'body'    => $params,
					'timeout' => 15,
					'headers' => array(
						'Content-Type'      => 'application/json',
						'Accept'            => 'application/json',
						'X-REAL-HTTP-CODES' => '1',
					),
				)
			);
			if ( is_wp_error( $response ) ) {
				return $response;
			}
			$result = wp_remote_retrieve_body( $response );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
			$result = json_decode( $result );

			// Support when API return errors with 200 status code.
			if ( property_exists( $result, 'status' ) && 'error' === $result->status ) {
				return new WP_Error( 'API', $result->message );
			}

			// If everything is ok...
			if ( $force_save_trans || $create_trans && $result ) {
				unset( $result->status );

				$expiration = 3600 * 24 * $cache_days; // 3600*24*7; // WEEK_IN_SECONDS
				set_transient( $url_trans_name, $result, $expiration );
			}
			return $result;
		}
		return false;
	}
}
