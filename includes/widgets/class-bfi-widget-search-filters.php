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
if ( ! class_exists( 'BFI_Widget_Search_Filters' ) ) :
class BFI_Widget_Search_Filters extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-widget_search_filters';
		$this->widget_description = __( 'A Filter box.', 'bfi' );
		$this->widget_id          = 'bookingfor_search_filters';
		$this->widget_name        = __( 'BookingFor Search Filters', 'bfi' );
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
//		$this->widget_start( $args, $instance );

		wp_enqueue_script('jquery-ui-core');
//		wp_enqueue_script('jquery-ui-slider');
		extract( $args );
		// these are the widget options
		
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : "";
		$title = apply_filters('widget_title', $title );
		$args["title"] =  $title;
		$args["instance"] =  $instance;
		bfi_get_template("widgets/search-filter.php",$args);	
//		include(BFI()->plugin_path() .'/templates/widgets/search-filter.php');

//		$this->widget_end( $args );
	}
}
endif;