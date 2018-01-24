<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingFor Cart Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     2.0.5
 * @extends  WP_Widget
 */
if ( ! class_exists( 'BFI_Widget_Currency_Switcher' ) ) :
class BFI_Widget_Currency_Switcher extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-currency-switcher';
		$this->widget_description = __("Display the user's currency switcher in the sidebar.", 'bfi' );
		$this->widget_id          = 'bookingfor_widget_changecurrency';
		$this->widget_name        = __( 'BookingFor currency switcher', 'bfi' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'bfi' )
			)
		);
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

//		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		// these are the widget options
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : "";
		$title = apply_filters('widget_title', $title );
//		$this->widget_start( $args, $instance );
		$args["title"] =  $title;
		$args["instance"] =  $instance;
		bfi_get_template("widgets/currency-switcher.php",$args);	
//		include(BFI()->plugin_path() .'/templates/widgets/currency-switcher.php');
//		$this->widget_end( $args );
	}
}
endif;