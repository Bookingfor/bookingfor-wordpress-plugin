<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
$resource_id = get_query_var( 'resource_id', 0 );
$language = $GLOBALS['bfi_lang'];
?>
<?php
	$layout = get_query_var( 'bfi_layout', '' );
	$model = new BookingForConnectorModelCondominiums;
	$resource = $model->getCondominiumFromService($resource_id,$language);	 
	$merchant = $resource->Merchant;
	$currencyclass = bfi_get_currentCurrency();
	$merchants = array();
	$merchants[] = $resource->MerchantId;

	if(empty($resource)){
	  global $wp_query;
	  $wp_query->set_404();
	  status_header( 404 );
	  get_template_part( 404 );
	  exit();		
	}
	
	if(!isset($_GET['task']) && ($layout !=_x('inforequestpopup', 'Page slug', 'bfi' )) && ($layout !=_x('mapspopup', 'Page slug', 'bfi' ))  ) {

	get_header('condominiumdetails' );
?>
 <?php
		/**
		 * bookingfor_before_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked bookingfor_breadcrumb - 20
		 */
				
		do_action( 'bookingfor_before_main_content' );
		if(isset($_REQUEST['newsearch'])){
			bfi_setSessionFromSubmittedData();
		}
		if(isset($_REQUEST['state'])){
			$_SESSION['search.params']['state'] = $_REQUEST['state'];

		}
	?>
	
<?php
//	$layout = get_query_var( 'bfi_layout', '' );
//	$model->setResourceId($resource_id);
//	$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);

	$sendAnalytics =false;
	$criteoConfig = null;


	switch ( $layout) {
		default:
			if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
				$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
				if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
					echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>';
					echo '<script type="text/javascript"><!--
					';
					echo ('window.criteo_q = window.criteo_q || [];');
					echo "//--></script>";
					if($resource->IsCatalog) {
						echo '<script type="text/javascript"><!--
						';
						echo ('
						window.criteo_q.push( 
							{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
							{ event: "setSiteType", type: "d" }, 
							{ event: "setEmail", email: "" }, 
							{ event: "viewItem", item: "'. $criteoConfig->merchants[0] .'" }
						);');
						echo "//--></script>";
					}
				}
			}
						
			
			include(BFI()->plugin_path().'/templates/condominiumdetails/resourcedetails.php'); // merchant template
			$sendAnalytics = $resource->IsCatalog;
	}

		add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
		do_action('bfi_head', "Merchant Resources Search List");
		if($sendAnalytics && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
				$obj = new stdClass;
				$obj->id = "" . $resource->ResourceId . " - Resource";
				$obj->name = $resource->Name;
				$obj->category = $resource->MerchantCategoryName;
				$obj->brand = $resource->MerchantName;
				$obj->variant = 'CATALOG';
				echo '<script type="text/javascript"><!--
				';
				echo ('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
				echo "//--></script>";
		}
	

	wp_enqueue_script('bf_cart_type', BFI()->plugin_url() . '/assets/js/bf_cart_type_1.js',array(),BFI_VERSION);
	wp_enqueue_script('bf_appTimePeriod', BFI()->plugin_url() . '/assets/js/bf_appTimePeriod.js',array(),BFI_VERSION);
	wp_enqueue_script('bf_appTimeSlot', BFI()->plugin_url() . '/assets/js/bf_appTimeSlot.js',array(),BFI_VERSION);


?>
	<?php
		/**
		 * bookingfor_after_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'bookingfor_after_main_content' );
	?>	
	<?php
		/**
		 * bookingfor_sidebar hook.
		 *
		 * @hooked bookingfor_get_sidebar - 10
		 */
//		do_action( 'bookingfor_sidebar' );
	?>
<?php get_footer( 'condominiumdetails' ); ?>

<?php
  
  }
  else {
    $task = BFCHelper::getVar('task','');

//	$model = new BookingForConnectorModelResource;
//	$model->setResourceId($resource_id);
//	$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
//	$resource = $model->getItem($resource_id);	 

//	$model = new BookingForConnectorModelCondominiums;
//	$resource = $model->getCondominiumFromService($resource_id,$language);	 
//	$currencyclass = bfi_get_currentCurrency();
	
//	$model = new BookingForConnectorModelMerchantDetails;
//	$merchant = $model->getItem($merchant_id);	 
//
	if($task == 'getMerchantResources') {
		if(!empty(BFCHelper::getVar('refreshcalc',''))){
			bfi_setSessionFromSubmittedData();
		}
		$criteoConfig = null;
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
		}
	
		
		$_SESSION['search.params']['resourceId'] = $resource_id;
		$output = '';
		$resourceId = 0;
		$condominiumId = $resource->condominiumId;

		include(BFI()->plugin_path().'/templates/search_details.php'); //merchant temp 
		die($output);
	}   
	//------------------------------
	if(empty($task)){
			switch ( $layout) {
				case _x('inforequestpopup', 'Page slug', 'bfi' ):
					
					$merchant_id = $resource->MerchantId;
					$model = new BookingForConnectorModelMerchantDetails;
					$merchant = $model->getItem($merchant_id);
					$currentView = 'resource';
					$orderType = "c";
					$task = "sendInforequest";
					$popupview = true;

					$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
					$url_merchant_page = get_permalink( $merchantdetails_page->ID );
					$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
					$uriMerchant = $routeMerchant;

					$routeThanks = $uriMerchant .'/'._x('thankspopup', 'Page slug', 'bfi' );
					$routeThanksKo = $uriMerchant .'/'._x('errors', 'Page slug', 'bfi' );
					$checkoutspan = '+1 day';
					$checkin = new DateTime();
					$checkout = new DateTime();
					$paxes = 2;
					$pars = BFCHelper::getSearchParamsSession();
					if (!empty($pars)){

						$checkin = isset($pars['checkin']) ? $pars['checkin'] : new DateTime();
						$checkout = isset($pars['checkout']) ? $pars['checkout'] : new DateTime();

						if (!empty($pars['paxes'])) {
							$paxes = $pars['paxes'];
						}
						if (!empty($pars['merchantCategoryId'])) {
							$merchantCategoryId = $pars['merchantCategoryId'];
						}
						if (!empty($pars['paxages'])) {
							$paxages = $pars['paxages'];
						}
						if ($pars['checkout'] == null){
							$checkout->modify($checkoutspan); 
						}
					}
					$checkinId = uniqid('checkin');
					$checkoutId = uniqid('checkout');

				$output = '';
					$model = new BookingForConnectorModelResource;
					$resource = $model->getItem($resource_id);	 
					include(BFI()->plugin_path().'/templates/merchant-sidebar-contact.php'); // merchant template
				die($output);
				break;
				case _x('mapspopup', 'Page slug', 'bfi' ):
					include(BFI()->plugin_path().'/templates/resourcedetails/mapspopup.php'); // merchant template
				die();
				break;
			}

	}

}
?>
