<?php
/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/bookingfor/global/sidebar.php.
 *
 * HOWEVER, on occasion BookingFor will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		BookingFor
 * @package 	BookingFor/Templates
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$template = get_option( 'template' );
if($template !='saladmag'){
	//get_sidebar( 'searchavailability' ); 
	get_sidebar( ); 
}

?>
