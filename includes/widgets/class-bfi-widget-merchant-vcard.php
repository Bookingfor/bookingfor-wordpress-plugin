<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Search Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     2.0.5
 * @extends  WP_Widget
 */
if ( ! class_exists( 'BFI_Widget_Merchant_Vcard' ) ) :
class BFI_Widget_Merchant_Vcard extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-widget_merchant_vcard';
		$this->widget_description = __( 'A details merchant box.', 'bfi' );
		$this->widget_id          = 'bookingfor_merchant_vcard';
		$this->widget_name        = __( 'BookingFor details merchant box', 'bfi' );
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
		bfi_get_template("widgets/merchant-vcard.php",$args);	

//		include(BFI()->plugin_path() .'/templates/widgets/merchant-vcard.php');
//		$this->widget_end( $args );
	}
}
endif;