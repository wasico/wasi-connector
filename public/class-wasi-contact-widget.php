<?php
/**
 * The contact widget.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/public
 */

/**
 * The contact widget.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/public
 */
class Wasi_Contact_Widget extends WP_Widget {
	/**
	 * The Wasi API client.
	 *
	 * @var Wasi_Api_Client
	 */
	private $api;

	/**
	 * The parent class.
	 *
	 * @var Wasi_Connector_Public
	 */
	private $parent;

	/**
	 * The constructor.
	 *
	 * @param Wasi_Connector_Public $parent The parent class.
	 * @param Wasi_Api_Client       $api The Wasi API client.
	 */
	public function __construct( $parent, $api ) {
		$this->api    = $api;
		$this->parent = $parent;

		$widget_opts = array(
			'classname'   => 'wasi-contact-widget',
			'description' => 'Wasi contact form widget',
		);
		parent::__construct( 'wasi_contact_widget', __( 'Wasi Contact Form', 'wasico' ), $widget_opts );

		// Register Ajax Actions on this widget to manage all Contact Form logic from this class.
		add_action( 'wp_ajax_wasi_contact', array( $this, 'ajax_contact_form' ) );
		add_action( 'wp_ajax_nopriv_wasi_contact', array( $this, 'ajax_contact_form' ) );
	}

	/**
	 * Post callback for the contact form.
	 *
	 * @return mixed
	 */
	public function ajax_contact_form() {
		header( 'Content-Type: application/json' );

		if ( ! isset( $_POST['data'] ) ) {
			echo wp_json_encode( 'Invalid contact params!' );
			wp_die();
			return false;
		}

		$res     = '';
		$data    = $_POST['data'];
		$contact = $this->api->get_user( $data['id_user_property'] );
		$relate  = $this->relate_client_to_property( $data, $data['id_property'] );
		if ( property_exists( $contact, 'email' ) ) {
			// send email here...
			$url_property  = home_url( '/' );
			$url_property .= get_post( $this->parent->get_wasi_data()['properties_page'] )->post_name;
			$url_property .= '/' . $data['id_property'];

			$to      = $contact->email;
			$subject = 'Usuario interesado en la propiedad ' . $url_property;
			$message = "Hola, \nUn usuario está interesa en la propiedad $data[id_property]\n\n"
				. "Datos del usuario:\n"
				. "- Nombre: $data[name]\n"
				. "- Correo: $data[email]\n"
				. "- Teléfono: $data[phone]\n"
				. "- Mensaje: $data[message]\n\n\n"
				. "---\n"
				. 'Email enviado desde ' . home_url( '/' ) . "\n";

			$r = wp_mail( $to, $subject, $message );
			if ( $r ) {
				$res = array(
					'send'    => 'ok',
					'message' => __( 'Thank you for your message! We will contact you shortly.', 'wasico' ),
				);
			} else {
				$res = array(
					'send'    => 'no',
					'message' => 'Error: Email not send!',
				);
			}
		} else {
			$res = array(
				'send'    => 'no',
				'message' => $contact->message,
			);
		}
		$res['relate'] = $relate;

		echo wp_json_encode( $res );
		wp_die();
	}

	/**
	 * Render the widget form.
	 *
	 * @param array $args The widget arguments.
	 * @param mixed $instance The widget instance.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$id = $this->parent->get_single_id_property();
		if ( $id > 0 ) {
			echo $args['before_widget'];
			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			$this->api_data = get_option( 'wasi_api_data' );

			$wasi_countries = $this->api->get_countries();
			include 'views/contact-form.php';

			echo $args['after_widget'];
		}
	}

	/**
	 * The widget form.
	 *
	 * @param mixed $instance The widget instance.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		// Outputs the options form on admin.
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'wasico' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
			$form_class = ! empty( $instance['formClass'] ) ? $instance['formClass'] : 'row';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'formClass' ) ); ?>"><?php esc_attr_e( 'Form Class:', 'wasico' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'formClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'formClass' ) ); ?>" type="text" value="<?php echo esc_attr( $form_class ); ?>">
		</p>
		<?php
			$submit_class = ! empty( $instance['submitClass'] ) ? $instance['submitClass'] : 'btn btn-primary';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'submitClass' ) ); ?>"><?php esc_attr_e( 'Submit Button Class:', 'wasico' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'submitClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'submitClass' ) ); ?>" type="text" value="<?php echo esc_attr( $submit_class ); ?>">
		</p>
		<?php

	}

	/**
	 * Update the widget.
	 *
	 * @param mixed $new_instance The new widget instance.
	 * @param mixed $old_instance The old widget instance.
	 *
	 * @return mixed
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
		$instance['formClass']   = ( ! empty( $new_instance['formClass'] ) ) ? $new_instance['formClass'] : '';
		$instance['submitClass'] = ( ! empty( $new_instance['submitClass'] ) ) ? $new_instance['submitClass'] : '';

		return $instance;
	}

	/**
	 * Relate a client to a property.
	 *
	 * @param array          $client The client.
	 * @param integer|string $id_property The property ID.
	 *
	 * @return array|object
	 */
	private function relate_client_to_property( $client, $id_property ) {
		$name = $client['name'];
		if ( strpos( $name, ' ' ) > 0 ) {
			$names                = explode( ' ', $name );
			$client['first_name'] = $names[0];
			$client['last_name']  = $names[1];
		} else {
			$client['first_name'] = $name;
			$client['last_name']  = '';
		}

		$client['query']            = $client['message'];
		$client['id_user']          = intval( $client['id_user_property'] );
		$client['send_information'] = false;
		if ( isset( $client['id_country'] ) ) {
			$client['id_country'] = intval( $client['id_country'] );
		}
		if ( isset( $client['id_region'] ) ) {
			$client['id_region'] = intval( $client['id_region'] );
		}
		if ( isset( $client['id_city'] ) ) {
			$client['id_city'] = intval( $client['id_city'] );
		}

		// @link http://api.wasi.co/docs/en/guide/fields/client-types.html
		$client['id_client_type'] = 7;

		unset( $client['name'], $client['message'], $client['id_property'], $client['id_user_property'] );

		$rel      = array();
		$response = $this->api->add_customer( $client );
		if ( $response && ! is_wp_error( $response ) ) {
			$id_client = $response->id_client;
			if ( $id_client ) {
				$rel = $this->api->add_client_to_property( $id_property, $id_client );
			} else {
				$rel = $response;
			}
		}
		return $rel;
	}
}
