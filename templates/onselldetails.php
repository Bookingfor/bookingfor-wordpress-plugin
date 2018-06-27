<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
	$resource_id = get_query_var( 'resource_id', 0 );
	$language = $GLOBALS['bfi_lang'];
	$layout = get_query_var( 'bfi_layout', '' );
	$sitename = sanitize_text_field( get_bloginfo( 'name' ) );


	if(!isset($_GET['task']) && ($layout !=_x('inforequestpopup', 'Page slug', 'bfi' )) && ($layout !=_x('mapspopup', 'Page slug', 'bfi' ))  ) {

	get_header( 'onselldetails' );
?>
 <?php
		/**
		 * bookingfor_before_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked bookingfor_breadcrumb - 20
		 */
		do_action( 'bookingfor_before_main_content' );
		$model = new BookingForConnectorModelOnSellUnit;
		$resource = $model->getItem($resource_id);	 
	?>
	
<?php
//	$layout = get_query_var( 'bfi_layout', '' );
	$model->setResourceId($resource_id);
	
	$listName = "";
	$sendAnalytics = true;

	$paramRef = array(
		"merchant"=>$merchant,
		"resource"=>$resource,
		"resource_id"=>$resource_id,
		);

	switch ( $layout) {
//		case 'form' :
//			include(BFI()->plugin_path().'/templates/onselldetails/form.php'); // merchant template
//		break;
//		case _x('review', 'Page slug', 'bfi' ):
//			include(BFI()->plugin_path().'/templates/onselldetails/review.php'); // merchant template
//		break;
//		case 'resources' :
//			$resources = $model->getItems('',0, $merchant_id);
//			$total = $model->getTotal();
//			include(BFI()->plugin_path().'/templates/merchantdetails/resources.php'); // merchant template
//		break;
//		case 'offers' :
//			$offers = $model->getItems('offers',0, $merchant_id);
//			$total = $model->getTotal('offers');
//			include(BFI()->plugin_path().'/templates/merchantdetails/offers.php'); // merchant template
//		break;
//		case 'offer' :
//			$offerId = get_query_var( 'bfi_id', 0 );
//			if(!empty($offerId)){
//				$offer = $model->getMerchantOfferFromService($offerId);
//				include(BFI()->plugin_path().'/templates/merchantdetails/offer-details.php'); // merchant template
//			}
//		break;
//		case 'thanks' :
//			include(BFI()->plugin_path().'/templates/merchantdetails/thanks.php'); // merchant template
//		break;
//		case 'errors' :
//			include(BFI()->plugin_path().'/templates/merchantdetails/errors.php'); // merchant template
//		break;
//		case 'packages' :
//			$packages = $model->getItems('packages',0, $merchant_id);
//			$total = $model->getTotal('packages');
//			include(BFI()->plugin_path().'/templates/merchantdetails/packages.php'); // merchant template
//		break;
//		case 'package' :
//			$packageId = get_query_var( 'bfi_id', 0 );
//			if(!empty($packageId)){
//				$offer = $model->getMerchantPackageFromService($packageId);
//				include(BFI()->plugin_path().'/templates/merchantdetails/package-details.php'); // merchant template
//			}
//		break;
//		case 'reviews' :
//			if(isset($_POST) && !empty($_POST)) {
//				$_SESSION['ratings']['filters']['typologyid'] = $_POST['filters']['typologyid'];
//			}
//			$ratings = $model->getItems('ratings',0, $merchant_id);
//			$total = $model->getTotal('ratings');
//			$summaryRatings = $model->getMerchantRatingAverageFromService($merchant_id);
//			include(BFI()->plugin_path().'/templates/merchantdetails/reviews.php'); // merchant template
//		break;
//		case 'review' :
//			include(BFI()->plugin_path().'/templates/merchantdetails/review.php'); // merchant template
//		break;
		case _x('mapspopup', 'Page slug', 'bfi' ):
			bfi_get_template("onselldetails/mapspopup.php",$paramRef);	
//			include(BFI()->plugin_path().'/templates/onselldetails/mapspopup.php'); // merchant template
			die();
		break;
		
		default:
			$listName = "Sales Resources Page";
//			include(BFI()->plugin_path().'/templates/onselldetails/resourcedetails.php'); // merchant template
			$paramRef = array(
				"merchant"=>$merchant,
				"resource"=>$resource,
				"listName"=>$listName,
				"resource_id"=>$resource_id,
				);
			bfi_get_template("onselldetails/resourcedetails.php",$paramRef);	
	}

		add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
		do_action('bfi_head', "");
		if($sendAnalytics && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$obj = new stdClass;
			$obj->id = "" . $resource->ResourceId . " - Sales Resource";
			$obj->name = $resource->Name;
			$obj->category = $resource->MerchantCategoryName;
			$obj->brand = $resource->MerchantName;
			$obj->variant = 'NS';
//			$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
			echo '<script type="text/javascript"><!--
			';
				echo ('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
			echo "//--></script>";
		}

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
<?php get_footer( 'onselldetails' ); ?>

<?php
  
  }
  else {
    $task = BFCHelper::getVar('task','');
	$model = new BookingForConnectorModelOnSellUnit;
	$model->setResourceId($resource_id);
//	$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
	$resource = $model->getItem($resource_id);	 
	
//	$model = new BookingForConnectorModelMerchantDetails;
//	$merchant = $model->getItem($merchant_id);	 
//
//	if($task == 'getMerchantResources') {
//		if(!empty(BFCHelper::getVar('refreshcalc',''))){
//			bfi_setSessionFromSubmittedData();
//		}
//
//		$output = '';
//		bfi_get_template("onselldetails/search.php",array("total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"listNameAnalytics"=>$listNameAnalytics));	
//		die($output);
//	}   
	//------------------------------
	if(empty($task)){
			switch ( $layout) {
				case _x('inforequestpopup', 'Page slug', 'bfi' ):
					
					$merchant_id = $resource->MerchantId;
					$model = new BookingForConnectorModelMerchantDetails;
					$merchant = $model->getItem($merchant_id);
					$currentView = 'onsellunit';
					$orderType = "b";
					$task = "sendOnSellrequest";
					$popupview = true;

					$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
					$url_merchant_page = get_permalink( $merchantdetails_page->ID );
					$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
					$uriMerchant = $routeMerchant;

					$routeThanks = $uriMerchant .'/'._x('thankspopup', 'Page slug', 'bfi' );
					$routeThanksKo = $uriMerchant .'/'._x('errors', 'Page slug', 'bfi' );
					$checkoutspan = '+1 day';
					$checkin = new DateTime('UTC');
					$checkout = new DateTime('UTC');
					$paxes = 2;
					$pars = BFCHelper::getSearchParamsSession();
					if (!empty($pars)){

						$checkin = isset($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
						$checkout = isset($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');

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
					$paramRef = array(
						"merchant"=>$merchant,
						"layout"=>$layout,
						"currentView"=>$currentView,
						"resource"=>$resource,
						"popupview"=>$popupview,
						"task"=>$task,
						"checkoutId"=>$checkoutId,
						"checkinId"=>$checkinId,
						"orderType"=>$orderType,
						"routeThanks"=>$routeThanks,
						"routeThanksKo"=>$routeThanksKo,
						"paxes"=>$paxes,
						"checkin"=>$checkin,
						"checkout"=>$checkout
						);

					bfi_get_template("merchant-sidebar-contact.php",$paramRef);	
//					include(BFI()->plugin_path().'/templates/merchant-sidebar-contact.php'); // merchant template
					die($output);
					break;
				case _x('mapspopup', 'Page slug', 'bfi' ):
					$paramRef = array(
						"resource"=>$resource
						);
					bfi_get_template("onselldetails/mapspopup.php",$paramRef);	
//					include(BFI()->plugin_path().'/templates/onselldetails/mapspopup.php'); // merchant template
					die();
				break;
			}

	}

}
?>
