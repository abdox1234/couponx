<?php

/* -----------------------------------------------------------------------

	Plugin Name: MyThemeShop Adcode Widget
	Description: A widget for showing ad codes in the sidebar
	Version: 1.0
------------------------------------------------------------------------*/

if ( ! class_exists( 'MTS_Widget_Adcode' ) ) {
    class MTS_Widget_Adcode extends WP_Widget {

        public function __construct() {
            parent::__construct(
                'mts_widget_adcode',
                sprintf( __( '%sAdcode', 'coupon' ), MTS_THEME_WHITE_LABEL ? '' : 'MTS ' ),
                array(
                    'description' => __( 'A widget for showing ad codes in the sidebar', 'coupon' ),
                ),
                array(
                    'width'  => 400,
                    'height' => 350,
                )
            );
        }

        public function form( $instance ) {
            $instance = wp_parse_args( $instance, array( 'content' => '' ) );
        ?>
            <p>
            <textarea cols="52" rows="10" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>"><?php echo esc_textarea( $instance['content'] ); ?></textarea>
            </p>
        <?php
        }
    
        public function widget( $args, $instance ) {
            echo $args['before_widget'];
        ?>
            <div class="adcode-widget"><?php echo $instance['content']; ?></div>
        <?php
            echo $args['after_widget'];
        }
    }
}


function mts_register_adcode_widget() {
	register_widget( 'MTS_Widget_Adcode' );
}
add_action( 'widgets_init', 'mts_register_adcode_widget' );
