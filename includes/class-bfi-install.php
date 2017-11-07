<?php
/**
 * Installation related functions and actions.
 *
 * @author   	BookingFor
 * @category 	Admin
 * @package  	BookingFor/Classes
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Install' ) ) :
/**
 * BFI_Install Class.
 */
class BFI_Install {


	/**
	 * Hook in tabs.
	 */
	public static function init() {
//		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
	}

	/**
	 * Install BookingFor.
	 */
	public static function install() {
		global $wpdb;

		self::create_pages();
//		self::create_tables();
		BFI_Post_types::register_post_types();

		do_action( 'bookingfor_installed' );
	}
	/**
	 * Create pages that the plugin relies on, storing page id's in variables.
	 */
	public static function create_pages() {
		$pages = apply_filters( 'bookingfor_create_pages', array(
				'searchavailability' => array(
					'name'    => _x( 'search-availability', 'Page slug', 'bfi' ),
					'title'   => _x( 'Search Availability', 'Page title', 'bfi' ),
					'content' => ''
				)
				,
				'searchonsell' => array(
					'name'    => _x( 'properties-for-sale', 'Page slug', 'bfi' ),
					'title'   => _x( 'Search Properties for sale', 'Page title', 'bfi' ),
					'content' => ''
				)
				,
				'merchantdetails' => array(
					'name'    => _x( 'merchantdetails', 'Page slug', 'bfi' ),
					'title'   => _x( 'Merchant Details', 'Page title', 'bfi' ),
					'content' => ''
				)
				,
				'condominiumdetails' => array(
					'name'    => _x( 'condominiumdetails', 'Page slug', 'bfi' ),
					'title'   => _x( 'Condominium Details', 'Page title', 'bfi' ),
					'content' => ''
				)
				,
				'accommodationdetails' => array(
					'name'    => _x( 'accommodation-details', 'Page slug', 'bfi' ),
					'title'   => _x( 'Accommodation Details', 'Page title', 'bfi' ),
					'content' => ''
				)//,
				,
				'onselldetails' => array(
					'name'    => _x( 'onsell-details', 'Page slug', 'bfi' ),
					'title'   => _x( 'On Sell Details', 'Page title', 'bfi' ),
					'content' => ''
				)//,
				,
				'orderdetails' => array(
					'name'    => _x( 'orders', 'Page slug', 'bfi' ),
					'title'   => _x( 'Order Details', 'Page title', 'bfi' ),
					'content' => ''
				)//,
				,
				'payment' => array(
					'name'    => _x( 'payment', 'Page slug', 'bfi' ),
					'title'   => _x( 'Payment Details', 'Page title', 'bfi' ),
					'content' => ''
				),
				'cartdetails' => array(
					'name'    => _x( 'cartdetails', 'Page slug', 'bfi' ),
					'title'   => _x( 'Cart Details', 'Page title', 'bfi' ),
					'content' => ''
				)
			)
		);

		foreach ( $pages as $key => $page ) {
			self::bfi_create_page( esc_sql( $page['name'] ), 'bookingfor_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? bfi_get_page_id( $page['parent'] ) : '' );
		}
	}

	private static  function bfi_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;
		$option_value     = get_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
				// Valid page is already in place
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		$valid_page_found = apply_filters( 'bookingfor_create_page_id', $valid_page_found, $slug, $page_content );

		if ( $valid_page_found ) {
			if ( $option ) {
				update_option( $option, $valid_page_found );
			}
			return $valid_page_found;
		}

		// Search for a matching valid trashed page
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = array(
				'ID'             => $page_id,
				'post_status'    => 'publish',
			);
			wp_update_post( $page_data );
		} else {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed'
			);
			$page_id = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}

//	private static function create_tables() {
//		global $wpdb;
//		$charset_collate = $wpdb->get_charset_collate();
//		$table_name = $wpdb->prefix . 'bfi_custom_route';
//		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
//			$sql = "CREATE TABLE $table_name (
//			id mediumint(9) NOT NULL AUTO_INCREMENT,
//			title longtext NOT NULL,
//			route longtext NOT NULL,
//			showrating mediumint(9) NOT NULL,
//			showdata mediumint(9) NOT NULL,
//			startswith longtext,
//			categoryid longtext NOT NULL,
//			UNIQUE KEY id (id)
//			) $charset_collate;";
//
//			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//			dbDelta( $sql );
//		}
//	}

	/**
	 * Uninstall tables when MU blog is deleted.
	 * @param  array $tables
	 * @return string[]
	 */
//	public static function wpmu_drop_tables( $tables ) {
//		global $wpdb;
//
//		$tables[] = $wpdb->prefix . 'bfi_custom_route';
//
//		return $tables;
//	}

	/**
	 * Get slug from path
	 * @param  string $key
	 * @return string
	 */
	private static function format_plugin_slug( $key ) {
		$slug = explode( '/', $key );
		$slug = explode( '.', end( $slug ) );
		return $slug[0];
	}
}
endif;
BFI_Install::init();