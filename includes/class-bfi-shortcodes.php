<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'bfi_Shortcodes' ) ) :
/**
 * bfi_Shortcodes class
 *
 * @class       bfi_Shortcodes
 * @version     2.0.5
 * @package     Bookingfor/Classes
 * @category    Class
 * @author      Bookingfor
 */
class bfi_Shortcodes {

	/**
	 * $shortcode_tag 
	 * holds the name of the shortcode tag
	 * @var string
	 */
	public $shortcode_tag = 'bfi_panel';

	
	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'bookingfor_search'           => __CLASS__ . '::bfi_shortcode_search',
			'bookingfor_merchants'           => __CLASS__ . '::bfi_shortcode_merchants',
			'bookingfor_merchantscarousel'           => __CLASS__ . '::bfi_shortcode_merchantscarousel',
			'bookingfor_resources'           => __CLASS__ . '::bfi_shortcode_resources',
			'bookingfor_onsells'           => __CLASS__ . '::bfi_shortcode_onsells',
			'bookingfor_tag'           => __CLASS__ . '::bfi_shortcode_tag',
			'bookingfor_currencyswitcher'           => __CLASS__ . '::bfi_shortcode_currencyswitcher',
//			'buildings'                    => __CLASS__ . '::buildings',
//			'real_estates'               => __CLASS__ . '::realestates',
//			'tag'            => __CLASS__ . '::tag',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}
		
	/**
	 * __construct 
	 * class constructor will set the needed filter and action hooks
	 * 
	 * @param array $args 
	 */
	function __construct($args = array()){
//		if ( is_admin() ){
//			add_action( 'admin_head', array( $this, 'admin_head') );
//			add_action( 'admin_enqueue_scripts', array($this , 'admin_enqueue_scripts' ) );
//		}

	}

	/**
	 * admin_head
	 * calls your functions into the correct filters
	 * @return void
	 */
	function admin_head() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}
		
		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this ,'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array($this, 'mce_buttons' ) );
		}
	}


	/**
	 * mce_external_plugins 
	 * Adds our tinymce plugin
	 * @param  array $plugin_array 
	 * @return array
	 */
	function mce_external_plugins( $plugin_array ) {
		$plugin_array[$this->shortcode_tag] =  BFI()->plugin_url() . '/assets/js/mce-button.js';
		return $plugin_array;
	}

	/**
	 * mce_buttons 
	 * Adds our tinymce button
	 * @param  array $buttons 
	 * @return array
	 */
	function mce_buttons( $buttons ) {
		array_push( $buttons, $this->shortcode_tag );
		return $buttons;
	}

	/**
	 * admin_enqueue_scripts 
	 * Used to enqueue custom styles
	 * @return void
	 */
	function admin_enqueue_scripts(){
		 wp_enqueue_style('bfi_panel_shortcode', BFI()->plugin_url() . '/assets/css/mce-button.css' );
	}





	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'bookingfor',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * bfi_shortcode_search form shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_search( $atts ) {
		ob_start();
		bfi_get_template("widgets/booking-search.php",array("instance" =>$atts));	
//		include(BFI()->plugin_path().'/templates/widgets/booking-search.php');
		$return = ob_get_contents();
		ob_end_clean();
		return $return ;
	}

	public static function bfi_shortcode_currencyswitcher( $atts ) {
		ob_start();
		bfi_get_template("widgets/currency-switcher.php",array("instance" =>$atts));	
//		include(BFI()->plugin_path().'/templates/widgets/currency-switcher.php');
		$return = ob_get_contents();
		ob_end_clean();
		return $return ;
	}


	
	public static function bfi_shortcode_merchantscarousel( $atts ) {
		$atts = shortcode_atts( array(
			'tags'  => '',
			'itemspage'    =>4,
			'maxitems' => 10,  // Slugs
			'descmaxchars' => 300,  // Slugs
		), $atts );
		
		
		if ( ! $atts['tags'] ) {
			return '';
		}

		$tags =[];
		if(!empty($atts['tags'])){
			$tags =explode(",",$atts['tags']);
		}

		 $instance['tags'] = $tags;
		 $instance['itemspage'] = $atts['itemspage'];
		 $instance['maxitems'] = $atts['maxitems'];
		 $instance['descmaxchars'] = $atts['descmaxchars'];

		ob_start();
		bfi_get_template("widgets/merchants.php",array("instance" =>$instance,"tags" =>$tags));	
//		include(BFI()->plugin_path().'/templates/widgets/merchants.php');
		$return = ob_get_contents();
		ob_end_clean();
		return $return ;
	}


	/**
	 * Merchants page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_merchants( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'rating' => '',  // Slugs
			'cityids' => '',  // Slugs
		), $atts );

		if ( ! $atts['category'] ) {
			return '';
		}

		$merchantCategories =[];
		if(!empty($atts['category'])){
			$merchantCategories =explode(",",$atts['category']);
		}
		$rating = !empty($atts['rating'])?$atts['rating']:'';
		$cityids = [];
		if(!empty($atts['cityids'])){
			$cityids =explode(",",$atts['cityids']);
		}

		$model = new BookingForConnectorModelMerchants;
		$model->populateState();	
		$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
//
		$filter_order = $model->getOrdering();
		$filter_order_Dir = $model->getDirection();

		$currParam = $model->getParam();
		$currParam['categoryId'] = $merchantCategories;
		$currParam['rating'] = $rating;
		$currParam['cityids'] = $cityids;
		$model->setParam($currParam);

			
		$total = $model->getTotal();
		$items = $model->getItems();
		
		$merchants = is_array($items) ? $items : array();
		ob_start();
		$listNameAnalytics =4;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";
		add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
		do_action('bfi_head', $listName);

//		include(BFI()->plugin_path().'/templates/merchantslist/merchantslist.php');
		$paramRef = array(
			"merchants"=>$merchants,
			"total"=>$total,
			"items"=>$items,
			"currParam"=>$currParam,
			"filter_order"=>$filter_order,
			"filter_order_Dir"=>$filter_order_Dir
			);
		bfi_get_template("merchantslist/merchantslist.php",$paramRef);	
		
		
//		echo "<pre>total: ";
//		echo ($total);
//		echo "</pre>";
		
		$return = ob_get_contents();

		
		ob_end_clean();
		return $return ;
//		return self::shortcode_wrapper( array( 'WC_Shortcode_Checkout', 'output' ), $atts );
	}

	/**
	 * Resources page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_resources( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'title',
			'order'    => 'desc',
			'categories' => '',  // Slugs
			'condominiumid' => 0,  // Slugs
		), $atts );

//		if ( ! $atts['category'] ) {
//			return '';
//		}
		
		$resourcesmodel = new BookingForConnectorModelResources();
		$resourcesmodel->populateState();	
		$resourcesmodel->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
//
		$filter_order = $resourcesmodel->getOrdering();
		$filter_order_Dir = $resourcesmodel->getDirection();
		
		$currParam = $resourcesmodel->getParam();
		$categories = !empty($atts['categories'])?$atts['categories']:'';
		$currParam['categories'] = $categories;
		$condominiumid = !empty($atts['condominiumid'])?$atts['condominiumid']:0;
		$currParam['parentProductId'] = $condominiumid;
		$resourcesmodel->setParam($currParam);

		$total = $resourcesmodel->getTotal();
		$items = $resourcesmodel->getItems();
		
		$merchants = is_array($items) ? $items : array();
		$resources = $resourcesmodel->getItems();
		$results = is_array($items) ? $items : array();

		ob_start();
		$paramRef = array(
			"merchants"=>$merchants,
			"resources"=>$resources,
			"results"=>$results,
			"total"=>$total,
			"items"=>$items,
			"currParam"=>$currParam,
			"filter_order"=>$filter_order,
			"filter_order_Dir"=>$filter_order_Dir,
			);
		bfi_get_template("resources.php",$paramRef);	
//		include(BFI()->plugin_path().'/templates/resources.php');
		$return = ob_get_contents();
		ob_end_clean();
		return $return ;
//		return self::shortcode_wrapper( array( 'WC_Shortcode_Checkout', 'output' ), $atts );
	}

	/**
	 * Resources on sells page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_onsells( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'AddedOn',
			'order'    => 'desc',
			'category' => '',  // Slugs
		), $atts );

//		if ( ! $atts['category'] ) {
//			return '';
//		}
		
		$resourcesmodel = new BookingForConnectorModelOnSellUnits();
		$resourcesmodel->populateState();	
		$resourcesmodel->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
//
		
		 $resourcesmodel->setOrdering($atts['orderby']);
		 $resourcesmodel->setDirection($atts['order']);

		$filter_order = $resourcesmodel->getOrdering();
		$filter_order_Dir = $resourcesmodel->getDirection();

		$items = $resourcesmodel->getItems();
		
		$resources = is_array($items) ? $items : array();
		$total = $resourcesmodel->getTotal();

		ob_start();
		include(BFI()->plugin_path().'/templates/onsellunits.php');
		$return = ob_get_contents();
		ob_end_clean();
		return $return ;
//		return self::shortcode_wrapper( array( 'WC_Shortcode_Checkout', 'output' ), $atts );
	}

	/**
	 * Tag page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function bfi_shortcode_tag( $atts ) {

		$atts = shortcode_atts( array(
			'per_page' => COM_BOOKINGFORCONNECTOR_ITEMPERPAGE,
			'orderby'  => 'Order',
			'order'    => 'asc',
			'tagid' => '',  // Slugs
			'category' => '',  // Slugs
			'grouped' => '0',  // Slugs
		), $atts );

		if ( ! $atts['tagid'] ) {
			return '';
		}
		
		$resourcesmodel = new BookingForConnectorModelTags();
		$resourcesmodel->populateState();	
		$resourcesmodel->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
		
		
		$currParam = $resourcesmodel->getParam();
		$currParam['tagId'] = $atts['tagid'];
		$currParam['category'] = $atts['category'];
		$currParam['show_grouped'] = $atts['grouped'];
		$resourcesmodel->setParam($currParam);

//
//		$filter_order = $resourcesmodel->getOrdering();
//		$filter_order_Dir = $resourcesmodel->getDirection();
		
		 $resourcesmodel->setOrdering($atts['orderby']);
		 $resourcesmodel->setDirection($atts['order']);
		 $item = $resourcesmodel->getItem();
//		$items = $resourcesmodel->getItems();

		$category = 0;
		$showGrouped = 0;
		$list = "";
		$listNameAnalytics = 0;
		$totalItems = array();
		$sendData = true;

		if(!empty($item)) {
		ob_start();
			$category = $item->SelectionCategory;			
			if ($category  == 1) {
				$listNameAnalytics = 1;
				$items = $resourcesmodel->getItemsMerchants();
				$total = $resourcesmodel->getTotalMerchants();
				$filter_order = $resourcesmodel->getOrdering();
				$filter_order_Dir = $resourcesmodel->getDirection();

				$merchants = is_array($items) ? $items : array();

				$paramRef = array(
					"merchants"=>$merchants,
					"total"=>$total,
					"items"=>$items,
					"listNameAnalytics"=>$listNameAnalytics,
					"filter_order"=>$filter_order,
					"filter_order_Dir"=>$filter_order_Dir
					);
				bfi_get_template("merchantslist/merchantslist.php",$paramRef);	
//				include(BFI()->plugin_path().'/templates/merchantslist/merchantslist.php');
			}
			if ($category == 2) {
				$listNameAnalytics = 7;
				$items = $resourcesmodel->getItemsOnSellUnit();
				$total = $resourcesmodel->getTotalResources();
				$filter_order = $resourcesmodel->getOrdering();
				$filter_order_Dir = $resourcesmodel->getDirection();
				$resources = is_array($items) ? $items : array();
				$paramRef = array(
					"resources"=>$resources,
					"total"=>$total,
					"items"=>$items,
					"listNameAnalytics"=>$listNameAnalytics,
					"filter_order"=>$filter_order,
					"filter_order_Dir"=>$filter_order_Dir
					);
				bfi_get_template("onsellunits.php",$paramRef);	
//				include(BFI()->plugin_path().'/templates/onsellunits.php');
			}
			if ($category == 4) {
				$listNameAnalytics = 5;
				$items = $resourcesmodel->getItemsResources();
				$total = $resourcesmodel->getTotalResources();
				if (!empty($items)) {
					foreach($items as $mrckey => $mrcValue) {
						$obj = new stdClass();
						$obj->Id = $mrcValue->ResourceId . " - Resource";
						$obj->MerchantId = $mrcValue->MerchantId;
						$obj->MrcCategoryName = $mrcValue->DefaultLangMrcCategoryName;
						$obj->Name = $mrcValue->ResName;
						$obj->MrcName = $mrcValue->MrcName;
						$obj->Position = $mrckey;
						$totalItems[] = $obj;
					}
					if  ($currParam['show_grouped'] == true) {
						$merchants = is_array($items) ? $items : array();
						$paramRef = array(
							"merchants"=>$merchants,
							"total"=>$total,
							"items"=>$items,
							"listNameAnalytics"=>$listNameAnalytics,
//							"filter_order"=>$filter_order,
//							"filter_order_Dir"=>$filter_order_Dir
							);
						bfi_get_template("resources_grouped.php",$paramRef);	
//						include(BFI()->plugin_path().'/templates/resources_grouped.php');
					}else{
						$resources = is_array($items) ? $items : array();
						$paramRef = array(
							"resources"=>$resources,
							"total"=>$total,
							"items"=>$items,
							"listNameAnalytics"=>$listNameAnalytics,
//							"filter_order"=>$filter_order,
//							"filter_order_Dir"=>$filter_order_Dir
							);
						bfi_get_template("resources.php",$paramRef);	
//						include(BFI()->plugin_path().'/templates/resources.php');
					}
				}
			}
			$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];
			if(count($totalItems) > 0 && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			
			add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
			do_action('bfi_head', $listName);

				$allobjects = array();
				$initobjects = array();
				foreach ($totalItems as $key => $value) {
					$obj = new stdClass;
					$obj->id = "" . $value->Id;
					if(isset($value->GroupId) && !empty($value->GroupId)) {
						$obj->groupid = $value->GroupId;
					}
					$obj->name = $value->Name;
					$obj->category = $value->MrcCategoryName;
					$obj->brand = $value->MrcName;
					$obj->position = $value->Position;
					if(!isset($value->ExcludeInitial) || !$value->ExcludeInitial) {
						$initobjects[] = $obj;
					} else {
						///$obj->merchantid = $value->MerchantId;
						//$allobjects[] = $obj;
					}
				}
				echo '<script type="text/javascript"><!--
				';
				echo ('var currentResources = ' .json_encode($allobjects) . ';
				var initResources = ' .json_encode($initobjects) . ';
				' . ($sendData ? 'callAnalyticsEEc("addImpression", initResources, "list");' : ''));
				echo "//--></script>";

			}
			$return = ob_get_contents();
			ob_end_clean();
		}
		return $return ;
//		return self::shortcode_wrapper( array( 'WC_Shortcode_Checkout', 'output' ), $atts );
	}



}
endif;