<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
$merchant_id = get_query_var( 'merchant_id', 0 );
?>
<?php
	$layout = get_query_var( 'bfi_layout', '' );

	if(!isset($_GET['task']) &&  ($layout !=_x('contactspopup', 'Page slug', 'bfi' ) &&  ($layout != _x('mapspopup', 'Page slug', 'bfi' )) &&  ($layout != _x('thankspopup', 'Page slug', 'bfi' )) )) {

	get_header( 'merchantdetails' );
?>
 <?php
		/**
		 * bookingfor_before_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked bookingfor_breadcrumb - 20
		 */
		do_action( 'bookingfor_before_main_content' );
		$model = new BookingForConnectorModelMerchantDetails;
		$merchant = $model->getItem($merchant_id);	 
	?>
	
<?php
	$model->setMerchantId($merchant_id);
	$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);

	$listName = "";
	$checkAnalytics = false;
	$itemType = 0;
	$totalItems = array();
	$type = "";
	$sendAnalytics = true;
	$layoutcriteo = "";
	
	$cartType = 1; //$merchant->CartType;

	switch ( $layout) {
		case _x( 'resources', 'Page slug', 'bfi' ):
			$resources = $model->getItems('',0, $merchant_id);
			$total = $model->getTotal();
			include(BFI()->plugin_path().'/templates/merchantdetails/resources.php'); // merchant template
			
			$listName = "Resources List";
			$type = "Resource";
			$itemType = 1;
			if ($resources  != null){
				foreach ($resources as $key => $value) {
					$obj = new stdClass;
					$obj->Id = $value->ResourceId;
					$obj->Name = $value->Name;
					$totalItems[] = $obj;
				}
			}

		
		break;
		case _x( 'onsellunits', 'Page slug', 'bfi' ):
			$resources = $model->getItems('onsellunits',0, $merchant_id);
			$total = $model->getTotal("onsellunits");
			include(BFI()->plugin_path().'/templates/merchantdetails/onsellunits.php'); // merchant template

			$listName = "Sales Resources Merchant List";
			$type = "Sales Resource";
			$itemType = 1;
			if ($resources  != null){
				foreach ($resources as $key => $value) {
					$obj = new stdClass;
					$obj->Id = $value->ResourceId;
					$obj->Name = $value->Name;
					$totalItems[] = $obj;
				}
			}

		break;
		case _x('offers', 'Page slug', 'bfi' ):
			$offers = $model->getItems('offers',0, $merchant_id);
			$total = $model->getTotal('offers');
			include(BFI()->plugin_path().'/templates/merchantdetails/offers.php'); // merchant template

			$listName = "Offers List";
			$type = "Offer";
			$itemType = 1;
			if ($offers  != null){
				foreach ($offers as $key => $value) {
					$obj = new stdClass;
					$obj->Id = $value->VariationPlanId;
					$obj->Name = $value->Name;
					$totalItems[] = $obj;
				}
			}

		break;
		case _x('offer', 'Page slug', 'bfi' ):
			$offerId = get_query_var( 'bfi_id', 0 );
			if(!empty($offerId)){
				$offer = $model->getMerchantOfferFromService($offerId);
				include(BFI()->plugin_path().'/templates/merchantdetails/offer-details.php'); // merchant template

				$listName = "Offers Page";
				$type = "Offer";
				$itemType = 0;
				$obj = new stdClass;
				$obj->Id = $offer->VariationPlanId;
				$obj->Name = $offer->Name;
				$totalItems[] = $obj;

			}
		break;
		case _x('thanks', 'Page slug', 'bfi' ):
			include(BFI()->plugin_path().'/templates/merchantdetails/thanks.php'); // merchant template

			$layoutcriteo = "thanks";
			$itemType = 2;

		break;
		case _x('errors', 'Page slug', 'bfi' ):
			include(BFI()->plugin_path().'/templates/merchantdetails/errors.php'); // merchant template
			$sendAnalytics = false;
		break;
		case _x('packages', 'Page slug', 'bfi' ):
			$packages = $model->getItems('packages',0, $merchant_id);
			$total = $model->getTotal('packages');
			include(BFI()->plugin_path().'/templates/merchantdetails/packages.php'); // merchant template
			
			$listName = "Packages List";
			$type = "Package";
			$itemType = 1;
			if ($packages  != null){
				foreach ($packages as $key => $value) {
					$obj = new stdClass;
					$obj->Id = $value->PackageId;
					$obj->Name = $value->Name;
					$totalItems[] = $obj;
				}
			}
		break;
		case _x('package', 'Page slug', 'bfi' ):
			$packageId = get_query_var( 'bfi_id', 0 );
			if(!empty($packageId)){
				$offer = $model->getMerchantPackageFromService($packageId);
				include(BFI()->plugin_path().'/templates/merchantdetails/package-details.php'); // merchant template
				
				$listName = "Packages Page";
				$type = "Package";
				$itemType = 0;
				$obj = new stdClass;
				$obj->Id = $offer->PackageId;
				$obj->Name = $offer->Name;
				$totalItems[] = $obj;
			}


		break;
		case _x('reviews', 'Page slug', 'bfi' ):
			if(isset($_POST) && !empty($_POST)) {
				$_SESSION['ratings']['filters']['typologyid'] = $_POST['filters']['typologyid'];
			}
			$ratings = $model->getItems('ratings',0, $merchant_id);
			$total = $model->getTotal('ratings');
			$summaryRatings = $model->getMerchantRatingAverageFromService($merchant_id);
			include(BFI()->plugin_path().'/templates/merchantdetails/reviews.php'); // merchant template
			$sendAnalytics = false;
		break;
		case _x('review', 'Page slug', 'bfi' ):
			include(BFI()->plugin_path().'/templates/merchantdetails/review.php'); // merchant template
			$sendAnalytics = false;
		break;
		case _x('redirect', 'Page slug', 'bfi' ):
			include(BFI()->plugin_path().'/templates/merchantdetails/redirect.php'); // merchant template
			$sendAnalytics = false;
		break;		
		default:
			include(BFI()->plugin_path().'/templates/merchantdetails/merchantdetails.php'); // merchant template

			$listName = "Merchants Page";
			$type = "Merchant";
			$itemType = 0;
			$obj = new stdClass;
			$obj->Id = $merchant->MerchantId;
			$obj->Name = $merchant->Name;
			$totalItems[] = $obj;
			$layoutcriteo = "default";

	}

		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED && ($layoutcriteo == "thanks" || $layoutcriteo == "default")) {
			$merchants = array();
			$merchants[] = $merchant->MerchantId;
			if($layoutcriteo == "thanks") {								
				$orderid = isset($_REQUEST['orderid'])?$_REQUEST['orderid']:0;
				$criteoConfig = BFCHelper::getCriteoConfiguration(4, $merchants, $orderid);	
			} else if ($layout == "") {
				$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
			}
			if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
//				$document->addScript('//static.criteo.net/js/ld/ld.js');
				echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>';


				if($layoutcriteo == "thanks") {
//					$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
//					window.criteo_q.push( 
//						{ event: "setAccount", account: ' . $criteoConfig->campaignid . '}, 
//						{ event: "setSiteType", type: "d" }, 
//						{ event: "setEmail", email: "" }, 
//						{ event: "trackTransaction", id: "' . $criteoConfig->transactionid . '",  item: [' . json_encode($criteoConfig->orderdetails) . '] }
//					);');
					echo '<script type="text/javascript"><!--
					';
					echo ('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: ' . $criteoConfig->campaignid . '}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "trackTransaction", id: "' . $criteoConfig->transactionid . '",  item: [' . json_encode($criteoConfig->orderdetails) . '] }
					);');
					echo "//--></script>";
				} else if ($layoutcriteo == "default") {
//					$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
//					window.criteo_q.push( 
//						{ event: "setAccount", account: ' . $criteoConfig->campaignid . '}, 
//						{ event: "setSiteType", type: "d" }, 
//						{ event: "setEmail", email: "" }, 
//						{ event: "viewItem", item: "' . $criteoConfig->merchants[0] . '" }
//					);');
					echo '<script type="text/javascript"><!--
					';
					echo ('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: ' . $criteoConfig->campaignid . '}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "viewItem", item: "' . $criteoConfig->merchants[0] . '" }
					);');
					echo "//--></script>";

				}
			}
		}

		add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
		do_action('bfi_head', $listName);
		if($sendAnalytics && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$item = $merchant;
				switch($itemType) {
					case 0:
						$value = $totalItems[0];
						$obj = new stdClass;
						$obj->id = "" . $value->Id . " - " . $type;
						$obj->name = $value->Name;
						$obj->category = $item->MainCategoryName;
						$obj->brand = $item->Name;
						$obj->variant = 'NS';
						
						echo '<script type="text/javascript"><!--
						';
						echo ('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
						echo "//--></script>";
						
						break;
					case 1:
						$allobjects = array();
						foreach ($totalItems as $key => $value) {
							$obj = new stdClass;
							$obj->id = "" . $value->Id . " - " . $type;
							$obj->name = $value->Name;
							$obj->category = $item->MainCategoryName;
							$obj->brand = $item->Name;
							$obj->position = $key;
							$allobjects[] = $obj;
						}
						echo '<script type="text/javascript"><!--
						';
						echo ('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');
						echo "//--></script>";
						break;
					case 2:
						$orderid = 	isset($_REQUEST['orderid'])?$_REQUEST['orderid']:0;;
						$act = 	BFCHelper::getVar('act');
						if(!empty($orderid) && $act!="Contact" ){
							$order = BFCHelper::getSingleOrderFromService($orderid);
							$purchaseObject = new stdClass;
							$purchaseObject->id = "" . $order->OrderId;
							$purchaseObject->affiliation = "" . $order->Label;
							$purchaseObject->revenue = $order->TotalAmount;
							$purchaseObject->tax = 0.00;
							
							$allobjects = array();
							$allservices = array();
							$svcTotal = 0;
							
							if(!empty($order->NotesData) && !empty(bfi_simpledom_load_string($order->NotesData)->xpath("//price"))) {
								$allservices = array_values(array_filter(bfi_simpledom_load_string($order->NotesData)->xpath("//price"), function($prc) {
									return (string)$prc->tag == "extrarequested";
								}));
								
								
								if(!empty($allservices )){
									foreach($allservices as $svc) {
										$svcObj = new stdClass;
										$svcObj->id = "" . (int)$svc->priceId . " - Service";
										$svcObj->name = (string)$svc->name;
										$svcObj->category = "Services";
										$svcObj->brand = $item->Name;
										$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
										$svcObj->price = round((float)$svc->discountedamount / (int)$svc->quantity, 2);
										$svcObj->quantity = (int)$svc->quantity;
										$allobjects[] = $svcObj;
										$svcTotal += (float)$svc->discountedamount;
									}
								}
							
								$mainObj = new stdClass;
								$mainObj->id = "" . $order->RequestedItemId . " - Resource";
								$mainObj->name = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
								$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
								$mainObj->category = $item->MainCategoryName;
								$mainObj->brand = $item->Name;
								$mainObj->price = $order->TotalAmount - $svcTotal;
								$mainObj->quantity = 1;
								
								$allobjects[] = $mainObj;
								
								echo '<script type="text/javascript"><!--
								';
								echo ('callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {"step": 3,});
									   callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');
								echo "//--></script>";
			
							}
						}

						break;
				}
		}
	
	if($cartType ==0 ){
		wp_enqueue_script('bf_cart_type', BFI()->plugin_url() . '/assets/js/bf_cart_type_0.js',array(),BFI_VERSION);
	}else{
		wp_enqueue_script('bf_cart_type', BFI()->plugin_url() . '/assets/js/bf_cart_type_1.js',array(),BFI_VERSION);
	}

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
<?php get_footer( 'merchantdetails' ); ?>

<?php
  
  }
  else {
    $task = BFCHelper::getVar('task','');
	
	$model = new BookingForConnectorModelMerchantDetails;
	$merchant = $model->getItem($merchant_id);	 
	$currencyclass = bfi_get_currentCurrency();
	$resourceId = 0;
	

	if($task == 'getMerchantResources') {
		if(!empty(BFCHelper::getVar('refreshcalc',''))){
			bfi_setSessionFromSubmittedData();
		}
		$output = '';
		$merchants = array();
		$merchants[] = $merchant->MerchantId;
		$criteoConfig = null;
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED ){
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
		}
//		include(BFI()->plugin_path().'/templates/merchantdetails/search.php'); //merchant temp 
		include(BFI()->plugin_path().'/templates/search_details.php'); //merchant temp 
		die($output);
	} 
	//------------------------------
	if(empty($task)){
			switch ( $layout) {
				case _x('contactspopup', 'Page slug', 'bfi' ):
					
					$orderType = "a";
					$task = "sendContact";
					$popupview = true;
					$layout ='';
					$currentView='merchant';
//					$merchant = $model->getItem($merchant_id);	
					$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
					$url_merchant_page = get_permalink( $merchantdetails_page->ID );
					$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
					$uriMerchant = $routeMerchant;

			$resource_id = get_query_var( 'bfi_id', 0 );
			$resourceType = get_query_var( 'bfi_name', 0 );
if($resourceType == _x( 'accommodation-details', 'Page slug', 'bfi' )){
	$model = new BookingForConnectorModelResource;
	$resource = $model->getItem($resource_id);
	$currentView = 'resource';
	$orderType = "c";
	$task = "sendInforequest";
}
if($resourceType == _x( 'properties-for-sale', 'Page slug', 'bfi' )){
	$model = new BookingForConnectorModelOnSellUnit;
	$resource = $model->getItem($resource_id);
	$currentView = 'onsellunit';
	$orderType = "b";
	$task = "sendOnSellrequest";
}


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
					include(BFI()->plugin_path().'/templates/merchant-sidebar-contact.php'); // merchant template
				die($output);
				break;
				case _x('mapspopup', 'Page slug', 'bfi' ):
//					$merchant = $model->getItem($merchant_id);	
					include(BFI()->plugin_path().'/templates/merchantdetails/mapspopup.php'); // merchant template
					die();
				break;
				case _x('thankspopup', 'Page slug', 'bfi' ):
				$output = '';
					include(BFI()->plugin_path().'/templates/merchantdetails/thanks.php'); // merchant template
				die($output);
				break;

			}

	}

}
?>
