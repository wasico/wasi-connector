<?php
/**
 * The search widget.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/public
 */

/**
 * The search widget.
 *
 * @link       wasi.co
 * @since      1.0.0
 *
 * @package    Wasi_Connector
 * @subpackage Wasi_Connector/public
 */
class Wasi_Search_Widget extends WP_Widget {
	/**
	 * The API client.
	 *
	 * @var Wasi_Api_Client
	 */
	private $api;

	/**
	 * The search widget constructor.
	 *
	 * @param Wasi_Api_Client $api The Wasi API client.
	 */
	public function __construct( $api ) {
		$this->api = $api;

		$widget_opts = array(
			'classname'   => 'search-widget',
			'description' => 'Wasi properties search widget',
		);
		parent::__construct( 'wasi_search_widget', __( 'Wasi Search', 'wasico' ), $widget_opts );
	}

	public function widget( $args, $instance ) {
		$property_status = $this->api->get_property_status();
		$property_types  = $this->api->get_property_types();

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$api_data        = get_option( 'wasi_api_data' );
		$properties_slug = get_post($api_data['properties_page'])->post_name;
		$wasi_countries  = $this->api->get_countries();
		include 'views/search.php';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		// outputs the options form on admin
		$title = !empty( $instance['title'] ) ? $instance['title'] : '';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'wasico' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php


		$formClass = !empty( $instance['formClass'] ) ? $instance['formClass'] : 'row';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'formClass' ) ); ?>"><?php esc_attr_e( 'Form Class:', 'wasico' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'formClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'formClass' ) ); ?>" type="text" value="<?php echo esc_attr( $formClass ); ?>">
		</p>
		<?php


		$submitClass = !empty( $instance['submitClass'] ) ? $instance['submitClass'] : 'btn btn-primary';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'submitClass' ) ); ?>"><?php esc_attr_e( 'Submit Button Class:', 'wasico' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'submitClass' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'submitClass' ) ); ?>" type="text" value="<?php echo esc_attr( $submitClass ); ?>">
		</p>
		<?php

	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( !empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
		$instance['formClass'] = ( !empty( $new_instance['formClass'] ) ) ? $new_instance['formClass'] : '';
		$instance['submitClass'] = ( !empty( $new_instance['submitClass'] ) ) ? $new_instance['submitClass'] : '';

		return $instance;
	}
}
