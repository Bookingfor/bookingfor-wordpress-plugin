<?php
/**
 * BookingFor Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @author		BookingFor
 * @category	Core
 * @package		BookingFor/Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
include_once( 'widgets/class-bfi-widget-search-filters.php' );
include_once( 'widgets/class-bfi-widget-merchant-vcard.php' );
include_once( 'widgets/class-bfi-widget-merchant.php' );
include_once( 'widgets/class-bfi-widget-booking-search.php' );
include_once( 'widgets/class-bfi-widget-booking-currency-switcher.php' );
include_once( 'widgets/class-bfi-widget-booking-cart.php' );
include_once( 'widgets/class-bfi-widget-booking-login.php' );

/**
 * Register Widgets.
 *
 * @since 2.3.0
 */
function bfi_register_widgets() {
	register_widget( 'BFI_Widget_Booking_search' );
	register_widget( 'BFI_Widget_Search_Filters' );
	register_widget( 'BFI_Widget_Merchant_Vcard' );
	register_widget( 'BFI_Widget_Merchants' );
	register_widget( 'BFI_Widget_Currency_Switcher' );
	register_widget( 'BFI_Widget_Cart' );
	register_widget( 'BFI_Widget_Login' );
}
add_action( 'widgets_init', 'bfi_register_widgets' );