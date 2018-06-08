<?php
class Wasi_Contact_Widget extends WP_Widget {

    private $lang_context = 'wasico';
    private $api;
    private $parent;

    public function __construct($parent,  $api) {
        $this->api = $api;
        $this->parent = $parent;

        $widget_opts = array(
            'classname' => 'wasi-contact-widget',
            'description' => 'Wasi contact form widget'
        );
        parent::__construct('wasi_contact_widget', __('Wasi Contact Form', $this->lang_context), $widget_opts);


        // Register Ajax Actions on this widget to manage all COntact Form logic from this class
        $ajaxWasi = array($this, 'ajaxContactForm');
        add_action( 'wp_ajax_wasi_contact', $ajaxWasi );
        add_action( 'wp_ajax_nopriv_wasi_contact', $ajaxWasi );
    }

    private function relateClientToProperty($client, $id_property) {
        
        $name = $client['name'];
        if(strpos($name, ' ')>0) {
            $names = explode(' ', $name);
            $client['first_name'] = $names[0];
            $client['last_name'] = $names[1];
        } else {
            $client['first_name'] = $name;
            $client['last_name'] = '';
        }

        $client['query'] = $client['message'];
        $client['id_user'] = intval($client['id_user_property']);
        $client['send_information'] = false;
        if(isset($client['id_country'])) {
            $client['id_country'] = intval($client['id_country']);
        }
        if(isset($client['id_region'])) {
            $client['id_region'] = intval($client['id_region']);
        }
        if(isset($client['id_city'])) {
            $client['id_city'] = intval($client['id_city']);
        }

        // http://api.wasi.co/guide/es/client/client_type.html
        $client['id_client_type'] = 7;

        unset($client['name']);
        unset($client['message']);
        unset($client['id_property']);
        unset($client['id_user_property']);

        $rel = array();
        $response = $this->api->addClient($client);
        if ($response && !is_wp_error($response)) {
            $id_client = $response->id_client;
            if ($id_client) {
                $rel = $this->api->addClientToProperty($id_property, $id_client);
            } else {
                $rel = $response;
            }
        }

        // return array('params' => $client, 'addClient' => $response, 'rel'=>$rel);
        return $rel;
    }


    public function ajaxContactForm() {
        header('Content-Type: application/json');

        if( !isset($_POST['data']) ) {
            echo json_encode("Invalid contact params!");
            wp_die();
            return false;
        }

        $res = '';
        $data = $_POST['data'];
        $contact = $this->api->getUserProperty($data['id_user_property']);
        $relate = $this->relateClientToProperty($data, $data['id_property']);
        if (property_exists($contact, 'email')) {
            // send email here...
            $url_property = home_url( '/' ); //.'property/'.$data['id_property'];
            $url_property.= get_post($this->parent->getWasiData()['properties_page'])->post_name;
            $url_property.= '/'.$data['id_property'];

            $to = $contact->email;
            $subject = 'Usuario interesado en la propiedad '.$url_property;

            $message = "Hola, \nUn usuario está interesa en la propiedad ".$data['id_property']."\n\n"
                      ."Datos del usuario:\n"
                      ."- Nombre: ".$data['name']."\n"
                      ."- Correo: ".$data['email']."\n"
                      ."- Teléfono: ".$data['phone']."\n"
                      ."- Mensaje: ".$data['message']."\n\n\n"
                      ."---\n"
                      ."Email enviado desde " . home_url( '/' ) . "\n";

            $r = wp_mail( $to, $subject, $message);
            if ($r) {
                $res = array(
                    'send' => 'ok',
                    'message' => __('Thank you for your message! We will contact you shortly.', 'wasico')
                );
            } else {
                $res = array(
                    'send' => 'no',
                    'message' => 'Error: Email not send!'
                );
            }
        } else {
            $res = array(
                'send' => 'no',
                'message' => $contact->message
            );
        }
        $res['relate'] = $relate;

        echo json_encode($res);
        wp_die();
    }


    public function widget($args, $instance) {

        $id = $this->parent->getSingleIdProperty();
        if($id>0) {
            echo $args['before_widget'];
            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }
            $this->api_data = get_option( 'wasi_api_data' );

            $wasiCountries = $this->api->getCountries();
            // $properties_slug = get_post($this->api_data['properties_page'])->post_name;
            include('views/contact-form.php');

            echo $args['after_widget'];
        }

    }

    public function form( $instance ) {
        // outputs the options form on admin
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', $this->lang_context ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php


        $formClass = !empty( $instance['formClass'] ) ? $instance['formClass'] : 'row';
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'formClass' ) ); ?>"><?php esc_attr_e( 'Form Class:', $this->lang_context ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'formClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'formClass' ) ); ?>" type="text" value="<?php echo esc_attr( $formClass ); ?>">
        </p>
        <?php


        $submitClass = !empty( $instance['submitClass'] ) ? $instance['submitClass'] : 'btn btn-primary';
        ?>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'submitClass' ) ); ?>"><?php esc_attr_e( 'Submit Button Class:', $this->lang_context ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'submitClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'submitClass' ) ); ?>" type="text" value="<?php echo esc_attr( $submitClass ); ?>">
        </p>
        <?php

    }


    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
        $instance['formClass'] = ( !empty( $new_instance['formClass'] ) ) ? sanitize_title( $new_instance['formClass'] ) : '';
        $instance['submitClass'] = ( !empty( $new_instance['submitClass'] ) ) ? sanitize_title( $new_instance['submitClass'] ) : '';

        return $instance;
    }
}