<?php
/**
 * bookingfor Template
 *
 * Functions for the templating system.
 *
 * @author   BookingFor
 * @category Core
 * @package  bookingfor/Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page works.
 */
//function bfi_template_redirect() {
//	global $wp_query, $wp;
//
//	// When default permalinks are enabled, redirect shop page to post type archive url
//	if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && $_GET['page_id'] == bfi_get_page_id( 'shop' ) ) {
//		wp_safe_redirect( get_post_type_archive_link('product') );
//		exit;
//	}
//
//	// When on the checkout with an empty cart, redirect to cart page
//	elseif ( is_page( bfi_get_page_id( 'checkout' ) ) && BFI()->cart->is_empty() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ) {
//		bfi_add_notice( __( 'Checkout is not available whilst your cart is empty.', 'bfi' ) );
//		wp_redirect( bfi_get_page_permalink( 'cart' ) );
//		exit;
//	}
//
//	// Logout
//	elseif ( isset( $wp->query_vars['customer-logout'] ) ) {
//		wp_redirect( str_replace( '&amp;', '&', wp_logout_url( bfi_get_page_permalink( 'myaccount' ) ) ) );
//		exit;
//	}
//
//	// Redirect to the product page if we have a single product
//	elseif ( is_search() && is_post_type_archive( 'product' ) && apply_filters( 'bookingfor_redirect_single_search_result', true ) && 1 === $wp_query->found_posts ) {
//		$product = bfi_get_product( $wp_query->post );
//
//		if ( $product && $product->is_visible() ) {
//			wp_safe_redirect( get_permalink( $product->id ), 302 );
//			exit;
//		}
//	}
//
//	// Ensure payment gateways are loaded early
//	elseif ( is_add_payment_method_page() ) {
//
//		BFI()->payment_gateways();
//
//	}
//
//	// Checkout pages handling
//	elseif ( is_checkout() ) {
//		// Buffer the checkout page
//		ob_start();
//
//		// Ensure gateways and shipping methods are loaded early
//		BFI()->payment_gateways();
//		BFI()->shipping();
//	}
//}
//add_action( 'template_redirect', 'bfi_template_redirect' );

/**
 * When loading sensitive checkout or account pages, send a HTTP header to limit rendering of pages to same origin iframes for security reasons.
 *
 * Can be disabled with: remove_action( 'template_redirect', 'bfi_send_frame_options_header' );
 *
 * @since  2.3.10
 */
//function bfi_send_frame_options_header() {
//	if ( is_checkout() || is_account_page() ) {
//		send_frame_options_header();
//	}
//}
//add_action( 'template_redirect', 'bfi_send_frame_options_header' );

/**
 * No index our endpoints.
 * Prevent indexing pages like order-received.
 *
 * @since 2.5.3
 */
//function bfi_prevent_endpoint_indexing() {
//	if ( is_bfi_endpoint_url() || isset( $_GET['download_file'] ) ) {
//		@header( 'X-Robots-Tag: noindex' );
//	}
//}
//add_action( 'template_redirect', 'bfi_prevent_endpoint_indexing' );





	/**
	 * Output generator tag to aid debugging.
	 *
	 * @access public
	 */
	function bfi_generator_tag( $gen, $type ) {
		switch ( $type ) {
			case 'html':
				$gen .= "\n" . '<meta name="generator" content="bookingfor ' . esc_attr( BFI_VERSION ) . '">';
				break;
			case 'xhtml':
				$gen .= "\n" . '<meta name="generator" content="bookingfor ' . esc_attr( BFI_VERSION ) . '" />';
				break;
		}
		return $gen;
	}

	function bfi_add_meta_keywords($keywords) {
		echo '<meta name="keywords" content="', esc_attr( strip_tags( stripslashes( $keywords ) ) ), '"/>', "\n";
	}

	function bfi_add_meta_description($description) {
		echo '<meta name="description" content="', esc_attr( strip_tags( stripslashes( $description) ) ), '"/>', "\n";
	}

	function bfi_add_meta_robots() {
		echo '<meta name="robots" content="index,follow"/>', "\n";
	}
	function bfi_generator_recaptcha_foot( ) {
		if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY)){
		?><script src="https://www.google.com/recaptcha/api.js?onload=BFIInitReCaptcha2&render=explicit" async defer></script><?php
		}
	}
	function bfi_google_analytics( ) {
		
		if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT)) {
			$gaOption="'auto'";
			if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
				$gaOption="{'cookieDomain': 'none'}";
			}		
			
			?>
			<!-- Google Analytics -->
			<script type="text/javascript">
			<!--
				var bookingfor_gacreated = true;
				var bookingfor_eeccreated = false;
				var bookingfor_gapageviewsent = 0;
				if(!window.ga) {
					(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,"script","https://www.google-analytics.com/analytics.js","ga");
					ga('create', '<?php echo COM_BOOKINGFORCONNECTOR_GAACCOUNT ?>', <?php echo $gaOption ?>); 
				}		
			//-->
			</script><?php
		}
	}

	function bfi_google_analytics_EEc($listName ) {

		if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			?>
			<!-- Google Analytics -->
			<script type="text/javascript">
			<!--
							ga("require", "ec");
							bookingfor_eeccreated = true;
							
							function initAnalyticsBFEvents() {
								jQuery("body").on("click", "#grid-view, #list-view", function(e) {
									if(e.originalEvent) {
										callAnalyticsEEc("", "", (jQuery(this).attr("id") == "grid-view" ? "GridView" : "ListView"), null, "changeView", "View&Sort");
									}
								});
								jQuery("body").on("click", ".bfi-sort-item", function(e){
									if(e.originalEvent) {
										var listname = "OrderBy";
										var sortType = "";
										switch(jQuery(this).attr("rel").split("|")[0].toLowerCase()) {
											case "reviewvalue":
												sortType = "GuestRating";
												break;
											case "stay":
											case "price":
												sortType = "Price";
												break;
											case "offer":
												sortType = "Offer";
												break;
											case "addedon":
												sortType = "AddedDate";
												break;
											case "name":
												sortType = "Name";
												break;
										}
										if(!jQuery.trim(sortType).length) { return; }
										listname += sortType;
										callAnalyticsEEc("", "", listname, null, "changeSort", "View&Sort");
									}
								});
								jQuery("body").on("mouseup", ".eectrack", function(e) {
									var currList = jQuery(this).attr("data-list") || null;

									if( e.which <= 2 ) {
										callAnalyticsEEc("addProduct", [{
											id: jQuery(this).attr("data-id") + " - " + jQuery(this).attr("data-type"),
											name: jQuery(this).attr("data-itemname"), 
											category: jQuery(this).attr("data-category"),
											brand: jQuery(this).attr("data-brand"), 
											//variant: jQuery(this).attr("data-type"),
											position: parseInt(jQuery(this).attr("data-index")), 
										}], "viewDetail", currList, jQuery(this).attr("data-id"), jQuery(this).attr("data-type"));
									}
								});
							}
							
							function callAnalyticsEEc(type, items, actiontype, list, actiondetail, itemtype) {
								list = list && jQuery.trim(list).length ? list : "<?php echo $listName ?>";
								switch(type) {
									case "addProduct":
										if(!items.length) { return; }
										jQuery.each(items, function(i, itm) {
											ga("ec:addProduct", itm);
										});
										break;
									case "addImpression":
										if(!items.length) { return; }
										jQuery.each(items, function(i, itm) {
											itm.list = list;
											ga("ec:addImpression", itm);
										});
										break;
								}
								
								switch(actiontype.toLowerCase()) {
									case "click":
										ga("ec:setAction", "click", {"list": list});
										ga("send", "event", "Bookingfor", "click", list);
										break;
									case "item":
										ga("ec:setAction", "detail");
										ga("send","pageview");
										bookingfor_gapageviewsent++;
										break;
									case "checkout":
									case "checkout_option":
										ga("ec:setAction", actiontype, actiondetail);
										ga("send","pageview");
										bookingfor_gapageviewsent++;
										break;
									case "addtocart":
										ga("set", "&cu", "<?php echo BFCHelper::$currencyCode[bfi_get_defaultCurrency()] ?>");
										ga("ec:setAction", "add", actiondetail);
										ga("send", "event", "Bookingfor - " + itemtype, "click", "addToCart");
										bookingfor_gapageviewsent++;
										break;
									case "removefromcart":
										ga("set", "&cu", "<?php echo BFCHelper::$currencyCode[bfi_get_defaultCurrency()] ?>");
										ga("ec:setAction", "remove", actiondetail);
										ga("send", "event", "Bookingfor - " + itemtype, "click", "addToCart");
										bookingfor_gapageviewsent++;
										break;
									case "purchase":
										ga("set", "&cu", "<?php echo BFCHelper::$currencyCode[bfi_get_defaultCurrency()] ?>");
										ga("ec:setAction", "purchase", actiondetail);
										ga("send","pageview");
										bookingfor_gapageviewsent++;
									case "list":
										ga("send","pageview");
										bookingfor_gapageviewsent++;
										break;
									default:
										ga("ec:setAction", "click", {"list": list});
										ga("send", "event", "Bookingfor - " + itemtype, actiontype, actiondetail);
										break;
								}
							}
							
							jQuery(function(){
								initAnalyticsBFEvents();
							});
			//-->
			</script><?php
		}
	}

/**
 * Add body classes for WC pages.
 *
 * @param  array $classes
 * @return array
 */
function bfi_body_class( $classes ) {
	$classes = (array) $classes;

//	if ( is_bookingfor() ) {
//		$classes[] = 'bookingfor';
//		$classes[] = 'bookingfor-page';
//	}
//
//	elseif ( is_checkout() ) {
//		$classes[] = 'bookingfor-checkout';
//		$classes[] = 'bookingfor-page';
//	}
//
//	elseif ( is_cart() ) {
//		$classes[] = 'bookingfor-cart';
//		$classes[] = 'bookingfor-page';
//	}
//
//	elseif ( is_account_page() ) {
//		$classes[] = 'bookingfor-account';
//		$classes[] = 'bookingfor-page';
//	}
//
//	if ( is_store_notice_showing() ) {
//		$classes[] = 'bookingfor-demo-store';
//	}
//
//	foreach ( BFI()->query->query_vars as $key => $value ) {
//		if ( is_bfi_endpoint_url( $key ) ) {
//			$classes[] = 'bookingfor-' . sanitize_html_class( $key );
//		}
//	}

	return array_unique( $classes );
}


/** Template pages ********************************************************/


/** Global ****************************************************************/

if ( ! function_exists( 'bookingfor_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function bookingfor_output_content_wrapper() {
		bfi_get_template( 'global/wrapper-start.php' );
	}
}
if ( ! function_exists( 'bookingfor_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function bookingfor_output_content_wrapper_end() {
		bfi_get_template( 'global/wrapper-end.php' );
	}
}
if ( ! function_exists( 'bfi_get_template' ) ) {

	function bfi_get_template($file) {
			$template       = locate_template($file );

//			if ( ! $template || BFI_TEMPLATE_DEBUG_MODE ) {
			if ( ! $template ) {
				$template = BFI()->plugin_path() . '/templates/' . $file;
			}
	include( $template );
	}
}



if ( ! function_exists( 'bookingfor_get_sidebar' ) ) {

	/**
	 * Get the shop sidebar template.
	 *
	 */
	function bookingfor_get_sidebar() {		
		bfi_get_template( 'global/sidebar.php' );
	}
}

if ( ! function_exists( 'bookingfor_demo_store' ) ) {

	/**
	 * Adds a demo store banner to the site if enabled.
	 *
	 */
	function bookingfor_demo_store() {
		if ( ! is_store_notice_showing() ) {
			return;
		}

		$notice = get_option( 'bookingfor_demo_store_notice' );

		if ( empty( $notice ) ) {
			$notice = __( 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'bfi' );
		}

		echo apply_filters( 'bookingfor_demo_store', '<p class="demo_store">' . wp_kses_post( $notice ) . '</p>'  );
	}
}

/** Loop ******************************************************************/

if ( ! function_exists( 'bookingfor_page_title' ) ) {

	/**
	 * bookingfor_page_title function.
	 *
	 * @param  bool $echo
	 * @return string
	 */
	function bookingfor_page_title( $echo = true ) {

		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'bfi' ), get_search_query() );

			if ( get_query_var( 'paged' ) )
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'bfi' ), get_query_var( 'paged' ) );

		} else {

			$searchavailability_page_id = bfi_get_page_id( 'searchavailability' );
			$page_title   = get_the_title( $searchavailability_page_id );

		}

		$page_title = apply_filters( 'bookingfor_page_title', $page_title );

		if ( $echo )
	    	echo $page_title;
	    else
	    	return $page_title;
	}
}

