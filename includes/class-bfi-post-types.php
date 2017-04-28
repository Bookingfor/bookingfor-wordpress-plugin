<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     BFI_Post_types
 * @version     2.0.5
 * @package   BookingFor/Classes/Products
 * @category  Class
 * @author    BookingFor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Post_types' ) ) :

/**
 * BFI_Post_types Class.
 */
class BFI_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( __CLASS__, 'rest_api_allowed_post_types' ) );
	}

	/**
	 * Register core taxonomies.
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'merchantlist_type' ) ) {
			return;
		}

		do_action( 'bookingfor_register_taxonomy' );

		$permalinks = get_option( 'bookingfor_permalinks' );

		register_taxonomy( 'merchantlist_type',
			apply_filters( 'bookingfor_taxonomy_objects_merchantlist_type', array( 'merchantlist' ) ),
			apply_filters( 'bookingfor_taxonomy_args_merchantlist_type', array(
				'hierarchical'      => false,
				'show_ui'           => false,
				'show_in_nav_menus' => false,
				'query_var'         => is_admin(),
				'rewrite'           => false,
				'public'            => false
			) )
		);

		do_action( 'bookingfor_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {
		if ( post_type_exists('merchantlist') ) {
			return;
		}

		do_action( 'bookingfor_register_post_type' );

		$permalinks        = get_option( 'bookingfor_permalinks' );
		$merchantlist_permalink = empty( $permalinks['merchantlist_base'] ) ? _x( 'merchantlist', 'slug', 'bfi' ) : $permalinks['merchantlist_base'];

		register_post_type( 'merchantlist',
			apply_filters( 'bookingfor_register_post_type_merchantlist',
				array(
					'labels'              => array(
							'name'                  => __( 'Merchant list pages', 'bfi' ),
							'singular_name'         => __( 'Merchant list page', 'bfi' ),
							'menu_name'             => _x( 'Merchant list', 'Admin menu name', 'bfi' ),
							'add_new'               => __( 'Add Merchant list page', 'bfi' ),
							'add_new_item'          => __( 'Add New Merchant list page', 'bfi' ),
							'edit'                  => __( 'Edit', 'bfi' ),
							'edit_item'             => __( 'Edit Merchant list page', 'bfi' ),
							'new_item'              => __( 'New Merchant list page', 'bfi' ),
							'view'                  => __( 'View Merchant list page', 'bfi' ),
							'view_item'             => __( 'View Merchant list page', 'bfi' ),
							'search_items'          => __( 'Search Merchant list page', 'bfi' ),
							'not_found'             => __( 'No Merchant list page found', 'bfi' ),
							'not_found_in_trash'    => __( 'No Merchant list page found in trash', 'bfi' ),
							'parent'                => __( 'Parent Merchant list page', 'bfi' ),
//							'featured_image'        => __( 'Merchant list page Image', 'bfi' ),
//							'set_featured_image'    => __( 'Set Merchant list page image', 'bfi' ),
//							'remove_featured_image' => __( 'Remove Merchant list page image', 'bfi' ),
//							'use_featured_image'    => __( 'Use as Merchant list page image', 'bfi' ),
							'insert_into_item'      => __( 'Insert into Merchant list page', 'bfi' ),
							'uploaded_to_this_item' => __( 'Uploaded to this Merchant list page', 'bfi' ),
							'filter_items_list'     => __( 'Filter Merchant list pages', 'bfi' ),
							'items_list_navigation' => __( 'Merchant list pages navigation', 'bfi' ),
							'items_list'            => __( 'Merchant list pages', 'bfi' ),
						),
					'description'         => __( 'This is where you can add new Merchant list page.', 'bfi' ),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'       => 'bfi-settings',
					'capability_type'     => 'page',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'             => $merchantlist_permalink ? array( 'slug' => untrailingslashit( $merchantlist_permalink ), 'with_front' => false, 'feeds' => true ) : false,
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'menu_position'	=>	99,
					'has_archive'         => false, //( $shop_page_id = bfi_get_page_id( 'shop' ) ) && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : 'shop',
					'show_in_nav_menus'   => true
//					,'register_meta_box_cb' => 'BFI_Admin_Meta_Boxes::add_merchantlist_meta_boxes'

				)
			)
		);

	}

	/**
	 * Register our custom post statuses, used for order status.
	 */
	public static function register_post_status() {

	}

	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'merchantlist' );
		}
	}

	/**
	 * Added merchantlist for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'merchantlist';

		return $post_types;
	}
}
endif;

BFI_Post_types::init();