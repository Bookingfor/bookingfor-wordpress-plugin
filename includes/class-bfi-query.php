<?php
/**
 * Contains the query functions for BookingFor which alter the front-end post queries and loops
 *
 * @class 		BFI_Query
 * @version             2.0.5
 * @package		BookingFor/
 * @category	        Class
 * @author 		BookingFor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Query' ) ) :

/**
 * BFI_Query Class.
 */
class BFI_Query {

	/** @public array Query vars to add to wp */
	public $query_vars = array();

	/**
	 * Stores chosen attributes
	 * @var array
	 */
	private static $_chosen_attributes;

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
	if ( ! is_admin() ) {
		add_filter( 'query_vars', array( $this, 'add_query_vars'), 0 );
		}
		$this->init_query_vars();
	}

	/**
	 * Get any errors from querystring.
	 */
	public function get_errors() {
//		if ( ! empty( $_GET['bfi_error'] ) && ( $error = sanitize_text_field( $_GET['bfi_error'] ) ) && ! bfi_has_notice( $error, 'error' ) ) {
//			bfi_add_notice( $error, 'error' );
//		}
	}

	/**
	 * Init query vars by loading options.
	 */
	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = array(
			// Checkout actions.
			'merchantdetails'          => get_option( 'merchantdetails', 'merchantdetails' ),
			'condominiumdetails'          => get_option( 'condominiumdetails', 'condominiumdetails' ),
			'_api_controller'          => get_option( 'api_controller', 'api_controller' ),
			'resourcedetails'          => get_option( 'resourcedetails', 'resourcedetails' ),
			'orderdetails'          => get_option( 'orderdetails', 'orderdetails' ),
			'payment'          => get_option( 'payment', 'payment' ),
			'onselldetails'          => get_option( 'onselldetails', 'onselldetails' ),
			'cartdetails'          => get_option( 'cartdetails', 'cartdetails' )
		);
	}

	/**
	 * Get page title for an endpoint.
	 * @param  string
	 * @return string
	 */
	public function get_endpoint_title( $endpoint ) {
		global $wp;

		switch ( $endpoint ) {
			case 'merchantdetails' :
				$title = __( 'Merchant details', 'bfi' );
			break;
			case 'condominiumdetails' :
				$title = __( 'Condominium details', 'bfi' );
			break;
			case 'resourcedetails' :
				$title = __( 'Accomodation details', 'bfi' );
			break;
			case 'orderdetails' :
				$title = __( 'Order details', 'bfi' );
			break;
			case 'payment' :
				$title = __( 'Order payment', 'bfi' );
			break;
			case 'onselldetails' :
				$title = __( 'On Sell details', 'bfi' );
			break;
			case 'cartdetails' :
				$title = __( 'Cart details', 'bfi' );
			break;
			
			default :
				$title = apply_filters( 'bookingfor_endpoint_' . $endpoint . '_title', '' );
			break;
		}

		return $title;
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function add_endpoints() {
		foreach ( $this->query_vars as $key => $var ) {
			add_rewrite_endpoint( $var, EP_PERMALINK | EP_PAGES, false );
	
			if ( 'merchantdetails' == $key ) {

				add_rewrite_tag( '%merchant_id%',   '([^&]+)' );
				add_rewrite_tag( '%merchant_name%', '([^&]+)' );
				add_rewrite_tag( '%bfi_layout%', '([^&]+)' );
				add_rewrite_tag( '%bfi_id%', '([^&]+)' );
				add_rewrite_tag( '%bfi_name%', '([^&]+)' );
				add_rewrite_tag( '%currpage%', '([^&]+)' );

				$page_id   = bfi_get_page_id( 'merchantdetails' ); 
				
				
				$page_slug = get_post( $page_id )->post_name;

				// e.g. /page-slug/my-endpoint/123/some-string/
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&merchant_id=$matches[1]&merchant_name=$matches[2]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)/page/([^/]*)?$",
					'index.php?page_id=' . $page_id . '&merchant_id=$matches[1]&merchant_name=$matches[2]&bfi_layout=$matches[3]&currpage=$matches[4]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)?$",
					'index.php?page_id=' . $page_id . '&merchant_id=$matches[1]&merchant_name=$matches[2]&bfi_layout=$matches[3]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&merchant_id=$matches[1]&merchant_name=$matches[2]&bfi_layout=$matches[3]&bfi_id=$matches[4]&bfi_name=$matches[5]',
					'top'
				);


			}
			
			if ( 'condominiumdetails' == $key ) {

				add_rewrite_tag( '%resource_id%',   '([^&]+)' );
				add_rewrite_tag( '%resource_name%', '([^&]+)' );
				add_rewrite_tag( '%bfi_layout%', '([^&]+)' );
				add_rewrite_tag( '%bfi_id%', '([^&]+)' );
				add_rewrite_tag( '%bfi_name%', '([^&]+)' );

				$page_id   = bfi_get_page_id( 'condominiumdetails' ); 
				$page_slug = get_post( $page_id )->post_name;
				// e.g. /page-slug/my-endpoint/123/some-string/

				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]&bfi_layout=$matches[3]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]&bfi_layout=$matches[3]&bfi_id=$matches[4]&bfi_name=$matches[5]',
					'top'
				);

			}
			if ( 'onselldetails' == $key ) {

				add_rewrite_tag( '%resource_id%',   '([^&]+)' );
				add_rewrite_tag( '%resource_name%', '([^&]+)' );
				add_rewrite_tag( '%bfi_layout%', '([^&]+)' );
				add_rewrite_tag( '%bfi_id%', '([^&]+)' );
				add_rewrite_tag( '%bfi_name%', '([^&]+)' );

				$page_id   = bfi_get_page_id( 'onselldetails' ); 
				$page_slug = get_post( $page_id )->post_name;

				// e.g. /page-slug/my-endpoint/123/some-string/
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]&bfi_layout=$matches[3]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]&bfi_layout=$matches[3]&bfi_id=$matches[4]&bfi_name=$matches[5]',
					'top'
				);



			}
			if ( 'resourcedetails' == $key ) {

				add_rewrite_tag( '%resource_id%',   '([^&]+)' );
				add_rewrite_tag( '%resource_name%', '([^&]+)' );
				add_rewrite_tag( '%bfi_layout%', '([^&]+)' );
				add_rewrite_tag( '%bfi_id%', '([^&]+)' );
				add_rewrite_tag( '%bfi_name%', '([^&]+)' );

				$page_id   = bfi_get_page_id( 'accommodationdetails' ); 
				$page_slug = get_post( $page_id )->post_name;
				// e.g. /page-slug/my-endpoint/123/some-string/

				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]&bfi_layout=$matches[3]',
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)-([^/]*)(?:/)([^/]*)/([^/-]*)-([^/]*)?$",
					'index.php?page_id=' . $page_id . '&resource_id=$matches[1]&resource_name=$matches[2]&bfi_layout=$matches[3]&bfi_id=$matches[4]&bfi_name=$matches[5]',
					'top'
				);

			}
			
			if ( 'orderdetails' == $key ) {

				add_rewrite_tag( '%checkmode%',   '([^&]+)' );

				$page_id   = bfi_get_page_id( 'orderdetails' ); 
				$page_slug = get_post( $page_id )->post_name;

				// e.g. /page-slug/my-endpoint/123/some-string/
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/?$",
					'index.php?page_id=' . $page_id ,
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)?$",
					'index.php?page_id=' . $page_id . '&checkmode=$matches[1]',
					'top'
				);

			}
			if ( 'payment' == $key ) {

				add_rewrite_tag( '%orderid%',   '([^&]+)' );

				$page_id   = bfi_get_page_id( 'payment' ); 
				$page_slug = get_post( $page_id )->post_name;

				// e.g. /page-slug/my-endpoint/123/some-string/
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/?$",
					'index.php?page_id=' . $page_id ,
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)?$",
					'index.php?page_id=' . $page_id . '&orderid=$matches[1]',
					'top'
				);

			}
			if ( 'cartdetails' == $key ) {

				$page_id   = bfi_get_page_id( 'cartdetails' ); 
				$page_slug = get_post( $page_id )->post_name;

				// e.g. /page-slug/my-endpoint/123/some-string/
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/?$",
					'index.php?page_id=' . $page_id ,
					'top'
				);
				add_rewrite_rule(
					"^(?:[a-z]{2}/|){$page_slug}/([^/-]*)?$",
					'index.php?page_id=' . $page_id . '&bfi_layout=$matches[1]',
					'top'
				);

			}
			
			if ( '_api_controller' == $key ) {

					$regex = '^(?:[a-z]{2}/|)bfi-api/v1/([^/]*)?';
					$location = 'index.php?_api_controller=$matches[1]';
					$priority = 'top';
					
					add_rewrite_rule( $regex, $location, $priority );
			
			
			}
		}
		flush_rewrite_rules();
	}

	/**
	 * Add query vars.
	 *
	 * @access public
	 * @param array $vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->query_vars as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return $this->query_vars;
	}

	/**
	 * Get query current active query var.
	 *
	 * @return string
	 */
	public function get_current_endpoint() {
		global $wp;
		foreach ( $this->get_query_vars() as $key => $value ) {
			if ( isset( $wp->query_vars[ $key ] ) ) {
				return $key;
			}
		}
		return '';
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 */
	public function parse_request() {
		global $wp;

		// Map query vars to their keys, or get them if endpoints are not supported
		foreach ( $this->query_vars as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = $_GET[ $var ];
			}

			elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
	}

	/**
	 * Hook into pre_get_posts to do the main product query.
	 *
	 * @param mixed $q query object
	 */
	public function pre_get_posts( $q ) {
		// We only want to affect the main query
		if ( ! $q->is_main_query() ) {
			return;
		}

		// Fix for verbose page rules
		if ( $GLOBALS['wp_rewrite']->use_verbose_page_rules && isset( $q->queried_object->ID ) && $q->queried_object->ID === bfi_get_page_id( 'merchantdetails' ) ) {
//			$q->set( 'post_type', 'product' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );

			// Fix conditional Functions
			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;
		}

		// Fix for endpoints on the homepage
		if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) !== absint( $q->get( 'page_id' ) ) ) {
			$_query = wp_parse_args( $q->query );
			if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->query_vars ) ) ) {
				$q->is_page     = true;
				$q->is_home     = false;
				$q->is_singular = true;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				add_filter( 'redirect_canonical', '__return_false' );
			}
		}

		// When orderby is set, WordPress shows posts. Get around that here.
		if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) === bfi_get_page_id( 'merchantdetails' ) ) {
			$_query = wp_parse_args( $q->query );
			if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
				$q->is_page = true;
				$q->is_home = false;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
//				$q->set( 'post_type', 'product' );
			}
		}

		// Special check for shops with the product archive on front
		if ( $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === bfi_get_page_id( 'merchantdetails' ) ) {

			// This is a front-page shop
//			$q->set( 'post_type', 'product' );
			$q->set( 'page_id', '' );

			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page shop later on
			define( 'SHOP_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$shop_page 	= get_post( bfi_get_page_id( 'merchantdetails' ) );

			$wp_post_types['product']->ID 			= $shop_page->ID;
			$wp_post_types['product']->post_title 	= $shop_page->post_title;
			$wp_post_types['product']->post_name 	= $shop_page->post_name;
			$wp_post_types['product']->post_type    = $shop_page->post_type;
			$wp_post_types['product']->ancestors    = get_ancestors( $shop_page->ID, $shop_page->post_type );

			// Fix conditional Functions like is_front_page
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = true;

			// Remove post type archive name from front page title tag
			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

//			// Fix WP SEO
//			if ( class_exists( 'WPSEO_Meta' ) ) {
//				add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
//				add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
//			}

		// Only apply to product categories, the product post archive, the shop page, product tags, and product attribute taxonomies
//		} elseif ( ! $q->is_post_type_archive( 'product' ) && ! $q->is_tax( get_object_taxonomies( 'product' ) ) ) {
//			return;
		}

		$this->product_query( $q );

		if ( is_search() ) {
			add_filter( 'posts_where', array( $this, 'search_post_excerpt' ) );
			add_filter( 'wp', array( $this, 'remove_posts_where' ) );
		}

		// And remove the pre_get_posts hook
		$this->remove_product_query();
	}

	/**
	 * Search post excerpt.
	 *
	 * @access public
	 * @param string $where (default: '')
	 * @return string (modified where clause)
	 */
	public function search_post_excerpt( $where = '' ) {
		global $wp_the_query;

		// If this is not a WC Query, do not modify the query
		if ( empty( $wp_the_query->query_vars['bfi_query'] ) || empty( $wp_the_query->query_vars['s'] ) )
			return $where;

		$where = preg_replace(
			"/post_title\s+LIKE\s*(\'\%[^\%]+\%\')/",
			"post_title LIKE $1) OR (post_excerpt LIKE $1", $where );

		return $where;
	}

	/**
	 * WP SEO meta description.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @access public
	 * @return string
	 */
	public function wpseo_metadesc() {
		return WPSEO_Meta::get_value( 'metadesc', bfi_get_page_id( 'merchantdetails' ) );
	}

	/**
	 * WP SEO meta key.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @access public
	 * @return string
	 */
	public function wpseo_metakey() {
		return WPSEO_Meta::get_value( 'metakey', bfi_get_page_id( 'merchantdetails' ) );
	}

	/**
	 * Query the products, applying sorting/ordering etc. This applies to the main wordpress loop.
	 *
	 * @param mixed $q
	 */
	public function product_query( $q ) {
		// Ordering query vars
//		$ordering  = $this->get_catalog_ordering_args();
//		$q->set( 'orderby', $ordering['orderby'] );
//		$q->set( 'order', $ordering['order'] );
//		if ( isset( $ordering['meta_key'] ) ) {
//			$q->set( 'meta_key', $ordering['meta_key'] );
//		}

		// Query vars that affect posts shown
//		$q->set( 'meta_query', $this->get_meta_query( $q->get( 'meta_query' ) ) );
//		$q->set( 'tax_query', $this->get_tax_query( $q->get( 'tax_query' ) ) );
		$q->set( 'posts_per_page', $q->get( 'posts_per_page' ) ? $q->get( 'posts_per_page' ) : apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) ) );
		$q->set( 'bfi_query', 'product_query' );
		$q->set( 'post__in', array_unique( apply_filters( 'loop_shop_post_in', array() ) ) );

		do_action( 'bookingfor_product_query', $q, $this );
	}


	/**
	 * Remove the query.
	 */
	public function remove_product_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Remove ordering queries.
	 */
	public function remove_ordering_args() {
		remove_filter( 'posts_clauses', array( $this, 'order_by_popularity_post_clauses' ) );
		remove_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
	}

	/**
	 * Remove the posts_where filter.
	 */
	public function remove_posts_where() {
		remove_filter( 'posts_where', array( $this, 'search_post_excerpt' ) );
	}

}
endif;