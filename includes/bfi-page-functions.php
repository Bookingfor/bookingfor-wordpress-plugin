<?php
/**
 * BookingFor  Page Functions
 *
 * Functions related to pages and menus.
 *
 * @author   BookingFor
 * @category Core
 * @package  BookingFor /Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Retrieve page ids - used for myaccount, edit_address, shop, cart, checkout, pay, view_order, terms. returns -1 if no page is found.
 *
 * @param string $page
 * @return int
 */
function bfi_get_page_id( $page ) {
	$pageid = apply_filters( 'bookingfor_get_' . $page . '_page_id', get_option('bookingfor_' . $page . '_page_id' ) );			
	//wpml plugin
	if( isset($pageid) && !is_admin() && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
		global $wp,$sitepress;
		$current_lang = $sitepress->get_current_language();
		$page_lang = $sitepress->get_language_for_element( $pageid, 'post_page');
		if($current_lang!=$page_lang){
			$translPageid = apply_filters( 'translate_object_id', $pageid, 'page', true, $current_lang );
			$pageid = $translPageid ;
		}		
	}
	//polylang plugin 
	if( isset($pageid) && !is_admin() && defined( 'POLYLANG_VERSION' ) ){
		global $wp,$polylang;
		$current_lang = pll_current_language();
		$page_lang = pll_get_post_language( $pageid);
		if($current_lang!=$page_lang){
			$translPageid = pll_get_post( $pageid, $current_lang);
			if(!empty($translPageid)){
				$pageid = $translPageid ;
			}
		}

	}
	return $pageid ? absint( $pageid ) : -1;
}


function bfi_get_template_page_id( $page ) {
	$pageid = apply_filters( 'bookingfor_get_' . $page . '_page_id', get_option('bookingfor_' . $page . '_page_id' ) );
	
	//wpml plugin
	if( isset($pageid) && !is_admin() && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
		global $wp,$sitepress;
		$current_lang = $sitepress->get_current_language();
		$page_lang = $sitepress->get_language_for_element( $pageid, 'post_page');
		if($current_lang!=$page_lang){
			$translPageid = apply_filters( 'translate_object_id', $pageid, 'page', true, $current_lang );
			$pageid = $translPageid ;
		}		
	}
	//polylang plugin 
	if( isset($pageid) && !is_admin() && defined( 'POLYLANG_VERSION' ) ){
		global $wp,$polylang;
		$current_lang = pll_current_language();
		$page_lang = pll_get_post_language( $pageid);
		if($current_lang!=$page_lang){
			$translPageid = pll_get_post( $pageid, $current_lang);
			if(!empty($translPageid)){
				$pageid = $translPageid ;
			}
			$pageid = $translPageid ;
		}

	}
	
	return $pageid ? absint( $pageid ) : -1;
}



/**
 * Retrieve page permalink.
 *
 * @param string $page
 * @return string
 */
function bfi_get_page_permalink( $page ) {
	$page_id   = bfi_get_page_id( $page );
	$permalink = $page_id ? get_permalink( $page_id ) : get_home_url();
	return apply_filters( 'bookingfor_get_' . $page . '_page_permalink', $permalink );
}

function bfi_sanitize_permalink( $value ) {
	global $wpdb;

	$value = $wpdb->strip_invalid_text_for_column( $wpdb->options, 'option_value', $value );

	if ( is_wp_error( $value ) ) {
		$value = '';
	}

	$value = esc_url_raw( $value );
	$value = str_replace( 'http://', '', $value );
	return untrailingslashit( $value );
}

function bfi_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'bfi_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

function bfi_get_current_page() {

	$currpage = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : (get_query_var('currpage')? get_query_var('currpage') : 1));
	if(isset($_REQUEST['paged'])){
		$currpage = absint($_REQUEST['paged']);
	}
	return $currpage ;
}
