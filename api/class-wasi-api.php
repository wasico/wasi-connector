<?php

/**
 * The WASI API Client
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/api
 */
class WasiAPICient {

    private $API_URL = 'https://api.wasi.co/v1';
    private $api_data = null;
    private $lang_context = 'wasico';

    public function __construct() {
        $this->api_data = get_option( 'wasi_api_data' );

        // expose public ajax endpoints:
        $ajaxWasi = array($this, 'ajaxAPI');
        add_action( 'wp_ajax_wasi_api', $ajaxWasi );
        add_action( 'wp_ajax_nopriv_wasi_api', $ajaxWasi );
    }


    public function ajaxAPI() {
        if( !isset($_POST['endpoint']) ) {
            echo "Invalid endpoint params!";
            wp_die();
        }
        $filters = isset($_POST['api_data']) ? $_POST['api_data'] : array();
        $p = $this->callAPI($_POST['endpoint'], $filters);
        echo json_encode($p);
        wp_die();
    }


    /**
     *      PROPERTIES
     ************************/

    public function getProperty($id) {
        $url = '/property/get/'.$id;
        return $this->callAPI($url);
    }

    public function getPropertyStatus() {
        return array(
            'for_rent' => __('For Rent', 'wasico'),
            'for_sale' => __('For Sale', 'wasico'),
            'for_transfer' => __('For Transfer', 'wasico')
        );
    }


    public function getRentTypes() {
        return array(
            1 => __('Daily', 'wasico'),
            2 => __('Weekly', 'wasico'),
            3 => __('Biweekly', 'wasico'),
            4 => __('Monthly', 'wasico')
        );
    }

    public function getPropertyType($id_type) {
        $types = $this->getPropertyTypes();
        foreach ($types as $key => $type) {
            if($type->id_property_type==$id_type) {
                return $type->name;
            }
        }
        return __('Property Type not found!', 'wasico');
    }

    public function getPropertyTypes() {
        $url = '/property-type/all';
        return $this->callAPI($url);
    }

    public function getPropertyAreas() {
        $url = '/property/area-range';
        return $this->callAPI($url);
    }

    public function getPriceRanges() {
        $url = '/property/price-range';
        return $this->callAPI($url);
    }

    public function getContactOwner($id_property) {
        $url = '/property/owner/'.$id_property;
        return $this->callAPI($url);
    }

    // This user is the Agent owner of a property
    public function getUserProperty($id_user) {
        $url = '/user/get/'.$id_user;
        return $this->callAPI($url);
    }


    /**
     *      LOCATIONS
     ***********************/

    // Countries
    public function getCountries() {
        $url = '/location/all-countries';
        return $this->callAPI($url);
    }

    // Regions / States
    public function getRegions($id_country) {
        $url = '/location/regions-from-country/'.$id_country;
        return $this->callAPI($url);
    }

    // Cities
    public function getCities($id_region) {
        $url = '/location/cities-from-region/'.$id_region;
        return $this->callAPI($url);
    }

    // Zones
    public function getZones($id_city) {
        $url = '/location/zones-from-city/'.$id_city;
        return $this->callAPI($url); // array(), 'GET', true
    }


    /**
     *       CLIENTS
     ***********************/

    public function addClient($client) {
        $url = '/client/add';
        $c = $this->callAPI($url, $client);
        if (is_wp_error($c)) {
            $url = '/client/search';
            $query = array('query' => $client['email']);
            $cli = $this->callAPI($url, $query);
            foreach ($cli as $key => $value) {
                if($key==='0') {
                    return $value;
                }
            }
            return new WP_Error('API', __('Client not found', 'wasico'));
        } else {
            return $c;
        }
    }

    // http://api.wasi.co/guide/es/client/client_property.html
    // http://api.wasi.co/guide/es/client/client_type.html
    public function addClientToProperty($id_property, $id_client, $id_client_type=7) {
        $url = '/client/add-property/'.$id_property;
        $params = array(
            'id_client' => $id_client,
            'id_client_type' => $id_client_type
        );
        return $this->callAPI($url, $params);
    }


    /**
     * HTTP API Call
     *
     * Remote HTTP Call with cache usage through wp transients.
     **/
    private function callAPI($url=false, $params=array(), $method='GET', $force_save_trans=false) {
        if ($url) {

            $createTrans = false;
            $url_trans_name = 'wasi'.str_replace('/', '-', $url);
            
            // by default enable trans for requests without params or only with pagination (take param)
            if(empty($params) || (count($params)===1 && isset($params['take']))) {

                $url_trans_name .= isset($params['take']) ? '_'.$params['take'] : '';
                $trans = get_transient( $url_trans_name );
                if ( $trans ) {
                    // echo("<pre>Transient: ".print_r($url_name, true)."</pre>");
                    if(isset($_GET['wasi-reset-api']))
                        delete_transient($url_trans_name);

                    return $trans;
                }
                
                // if the transient is not available, we need to create it!
                $createTrans = true;
            }


            //open connection
            $ch = curl_init();

            // Add WASI API Key and Token:
            $params = array_merge($params, $this->api_data);
            unset($params['properties_page']);
            unset($params['property_single_page']);
            unset($params['wasi_max_per_page']);
            unset($params['wasi_bootstrap']);

            // $ip = $_SERVER['REMOTE_ADDR'];
            
            $query_params = http_build_query($params);

            if($method==='GET') {
                $url = sprintf("%s?%s", $url, $query_params);
            }

            $url = $this->API_URL . $url;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
            if($method==='POST') {
                curl_setopt($ch, CURLOPT_POST, count($params));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_params);
            }

            //execute post
            $result = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($result) {
                $res = json_decode($result);
                if(property_exists($res, 'status') && $res->status==='error') {
                    return new WP_Error('API', $res->message);
                }

                // If everything is ok:
                if ($force_save_trans===true || ($createTrans===true && $res)) {

                    unset($res->status);

                    $expiration = 604800; // 3600*24*7; // WEEK_IN_SECONDS
                    set_transient($url_trans_name, $res, $expiration);
                }
                return $res;
            } else {
                return new WP_Error( 'broke', $err );
            }
        }
        return false;
    }
}