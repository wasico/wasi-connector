<?php
class Search_Widget extends WP_Widget {

    private $lang_context;
    private $api;

    public function __construct($lang_context,  $api) {
        $this->lang_context = $lang_context;
        $this->api = $api;

        $widget_opts = array(
            'classname' => 'search-widget',
            'description' => 'Wasi properties search widget'
        );
        parent::__construct('wasi_search_widget', __('Wasi Search', $this->lang_context), $widget_opts);
    }

    public function widget($args, $instance) {
        

        $propertyStatus = $this->api->getPropertyStatus();
        $propertyTypes = $this->api->getPropertyTypes();
        // $propertyAreas = $this->api->getPropertyAreas();
        // $propertyPrices = $this->api->getPriceRanges();

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        $this->api_data = get_option( 'wasi_api_data' );
        $properties_slug = get_post($this->api_data['properties_page'])->post_name;
        include('views/search.php');
        echo $args['after_widget'];
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