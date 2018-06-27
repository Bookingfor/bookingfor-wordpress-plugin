<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$currencyclass = bfi_get_currentCurrency();

//if(!isset($_GET['task'])) {
if(!isset($_POST['format'])) {
	get_header( 'searchavailability' ); ?>
		<?php
			/**
			 * bookingfor_before_main_content hook.
			 *
			 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
			 * @hooked bookingfor_breadcrumb - 20
			 */
			do_action( 'bookingfor_before_main_content' );
		?>
		<?php if ( apply_filters( 'bookingfor_show_page_title', true ) ) { ?>
		<?php } ?>
	<?php
}

bfi_setSessionFromSubmittedData();

$page = bfi_get_current_page() ;
$start = ($page - 1) * COM_BOOKINGFORCONNECTOR_ITEMPERPAGE;			
$searchmodel = new BookingForConnectorModelSearch;
		
$pars = BFCHelper::getSearchParamsSession();
$filterinsession = null;

$items =  array();
$total = 0;
$currSorting = "";

if (isset($pars['checkin']) && isset($pars['checkout'])){
	$now = new DateTime('UTC');
	$now->setTime(0,0,0);
	$checkin = isset($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
	$checkout = isset($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');

	$availabilitytype = isset($pars['availabilitytype']) ? $pars['availabilitytype'] : "1";
	
	$availabilitytype = explode(",",$availabilitytype);
	if (($checkin == $checkout && (!in_array("0",$availabilitytype) && !in_array("2",$availabilitytype)&& !in_array("3",$availabilitytype) ) ) || $checkin->diff($checkout)->format("%a") <0 || $checkin < $now ){
		$nodata = true;
	}else{
		if (empty($GLOBALS['bfSearched'])) {
		
			$filterinsession = BFCHelper::getFilterSearchParamsSession();
			$items = $searchmodel->getItems(false, false, $start,COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
			
			$items = is_array($items) ? $items : array();
					
			$total=$searchmodel->getTotal();
			$totalAvailable=$searchmodel->getTotalAvailable();
			$currSorting=$searchmodel->getOrdering() . "|" . $searchmodel->getDirection();
			$GLOBALS['bfSearched'] = 1;
		}else{
			$items = $GLOBALS['bfSearchedItems'];
			$total = $GLOBALS['bfSearchedItemsTotal'];
			$totalAvailable = $GLOBALS['bfSearchedItemsTotalAvailable'];
			$currSorting = $GLOBALS['bfSearchedItemsCurrSorting'];
		}
	}

}
$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);

$merchant_ids = '';


		$currParam = BFCHelper::getSearchParamsSession();
		$merchantResults = $currParam['merchantResults'];
		$condominiumsResults = $currParam['condominiumsResults'];
		$totPerson = (isset($currParam)  && isset($currParam['paxes']))? $currParam['paxes']:0 ;
/*-- criteo --*/
$criteoConfig = null;
if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
		$merchantsCriteo = array();
		if(!empty($items)) {
			$merchantsCriteo = array_unique(array_map(function($a) { return $a->MerchantId; }, $items));
		}
		$criteoConfig = BFCHelper::getCriteoConfiguration(1, $merchantsCriteo);
		if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
			echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>';
			echo '<script type="text/javascript"><!--
			';
			echo ('window.criteo_q = window.criteo_q || []; 
			window.criteo_q.push( 
				{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
				{ event: "setSiteType", type: "d" }, 
				{ event: "setEmail", email: "" }, 
				{ event: "viewSearch", checkin_date: "' . $pars["checkin"]->format('Y-m-d') . '", checkout_date: "' . $pars["checkout"]->format('Y-m-d') . '"},
				{ event: "viewList", item: ' . json_encode($criteoConfig->merchants) .' }
			);');
			echo "//--></script>";

			
	//		$document->addScript('//static.criteo.net/js/ld/ld.js');
	//		$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
	//		window.criteo_q.push( 
	//			{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
	//			{ event: "setSiteType", type: "d" }, 
	//			{ event: "setEmail", email: "" }, 
	//			{ event: "viewSearch", checkin_date: "' . $params["checkin"]->format('Y-m-d') . '", checkout_date: "' . $params["checkout"]->format('Y-m-d') . '"},
	//			{ event: "viewList", item: ' . json_encode($criteoConfig->merchants) .' }
	//		);');
		}	
	
}

/*-- criteo --*/

		$totalItems = array();
		$listName = "";
		$listNameAnalytics = 0;
		$sendData = true;
			
		if(!empty($items)) {
			if($merchantResults) {
//				$resIndex = 0;
				$listNameAnalytics = 1;
				$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];//"Merchants Group List";
				foreach($items as $itemkey => $itemValue) {
//					$obj = new stdClass();
//					$obj->Id = $itemValue->MerchantId . " - Merchant";
//					$obj->MerchantId = $itemValue->MerchantId;
//					$obj->Name = $itemValue->MrcName;
//					$obj->MrcCategoryName = $itemValue->MrcCategoryName;
//					$obj->MrcName = $itemValue->MrcName;
//					$obj->Position = $itemkey;
//					$totalItems[] = $obj;
					$objRes = new stdClass();
					$objRes->Id = $itemValue->ResourceId . " - Resource";
					$objRes->MerchantId = $itemValue->MerchantId;
					$objRes->Name = $itemValue->ResName;
					$objRes->MrcName = $itemValue->MrcName;
					$objRes->MrcCategoryName = $itemValue->MrcCategoryName;
					$objRes->Position = $itemkey;// $resIndex;
					$totalItems[] = $objRes;
				}
			} else if ($condominiumsResults) {
//				$sendData = false;
				$resIndex = 0;
				$listNameAnalytics = 2;
				$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Group List";
				foreach($items as $itemkey => $itemValue) {
//					$obj = new stdClass();
//					$obj->Id = $mrcValue->CondominiumId . " - Resource Group";
//					$obj->MerchantId = $mrcValue->MerchantId;
//					$obj->Name = $mrcValue->Name;
//					$obj->MrcCategoryName = $mrcValue->MrcCategoryName;
//					$obj->MrcName = $mrcValue->MerchantName;
//					$obj->Position = $mrckey;
//					$totalItems[] = $obj;
					$objRes = new stdClass();
					$objRes->Id = $itemValue->ResourceId . " - Resource";
					$objRes->CondominiumId= $itemValue->CondominiumId;
					$objRes->MerchantId = $itemValue->MerchantId;
					$objRes->Name = $itemValue->ResName;
					$objRes->MrcName = $itemValue->MrcName;
					$objRes->MrcCategoryName = $itemValue->MrcCategoryName;
					$objRes->Position = $itemkey;// $resIndex;
					$totalItems[] = $objRes;
				}
			} else {
				$listNameAnalytics = 3;
				$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";
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
			}
		}
//			if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT)) {
//				return true;
//			}

		$analyticsEnabled = COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
//		if(count($totalItems) > 0 && $analyticsEnabled) {
		add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
		do_action('bfi_head', $listName);
		if(count($totalItems) > 0 && $analyticsEnabled) {

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
//			$document->addScriptDeclaration('var currentResources = ' .json_encode($allobjects) . ';
//			var initResources = ' .json_encode($initobjects) . ';
//			' . ($sendData ? 'callAnalyticsEEc("addImpression", initResources, "list");' : ''));
			echo '<script type="text/javascript"><!--
			';
			echo ('var currentResources = ' .json_encode($allobjects) . ';
			var initResources = ' .json_encode($initobjects) . ';
			' . ($sendData ? 'callAnalyticsEEc("addImpression", initResources, "list");' : ''));
			echo "//--></script>";

		}
		
		//event tracking	


				bfi_get_template("search/default.php",array("totalAvailable"=>$totalAvailable,"total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"listNameAnalytics"=>$listNameAnalytics,"totPerson"=>$totPerson,"currSorting"=>$currSorting));	

//		include(BFI()->plugin_path().'/templates/search/default.php');	  

//  else {
//    $task = $_GET['task'];
//    if($task == 'GetMerchantsByIds') {
//      $lists = $_GET['merchantsId'];
//      $merchants = BFCHelper::getMerchantsByIds($lists);
//      die($merchants);
//    }
//    else if($task == 'getMerchantGroups') {
//      $merchantgroups = BFCHelper::getMerchantGroups();
//      wp_send_json($merchantgroups);
//    }
//    else if($task == 'GetPhoneByMerchantId') {
//    	$merchantId = $_GET['merchantid'];
//    	$language = $GLOBALS['bfi_lang'];
//      $phno = BFCHelper::GetPhoneByMerchantId($merchantId,$language);
//      wp_send_json($phno);
//    }
//    else if($task == 'GetResourcesByIds') {
//      $lists = $_GET['resourcesId'];
//      $language = $GLOBALS['bfi_lang'];
//      $resources = BFCHelper::GetResourcesByIds($lists,$language);
//      @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
//      echo $resources;
//      die();
//    }
//    else if($task == 'getmarketinfomerchant') {
//		$base_url = get_site_url();
//		$merchant_id = $_GET['merchantId'];
//		$model =  new BookingForConnectorModelMerchantDetails;
//		$merchant = $model->getItem($merchant_id);
//		$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
//		$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
//		$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
//		$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";
//
//		$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
//		$url_merchant_page = get_permalink( $merchantdetails_page->ID );
//		
//		$output = '<div class="bfi-mapdetails">
//					<div class="bfi-item-title">
//						<a href="'.$url_merchant_page .$merchant_id .'-'.BFI()->seoUrl($merchant->Name).'?fromsearch=1" target="_blank">'.$merchant->Name.'</a> 
//					</div>
//					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
//				</div>';    
//    die($output);
//    }
//    else if($task == 'getmarketinforesource') {
//		$base_url = get_site_url();
//		$resource_id = $_GET['resourceId'];
//		$model = new BookingForConnectorModelResource;
//		$resource = $model->getItem($resource_id);
//		$merchant = $resource->Merchant;
//		$indirizzo = isset($resource->Address)?$resource->Address:"";
//		$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
//		$comune = isset($resource->CityName)?$resource->CityName:"";
//		$stato = isset($resource->StateName)?$resource->StateName:"";
//		
//		$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
//		$url_resource_page = get_permalink( $accommodationdetails_page->ID );
//
//		$output = '<div class="bfi-mapdetails">
//					<div class="bfi-item-title">
//						<a href="'.$url_resource_page .$resource_id .'-'.BFI()->seoUrl($resource->Name).'?fromsearch=1" target="_blank">'.$resource->Name.'</a> 
//					</div>
//					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
//				</div>';    
//    die($output);    	
//    }
//    else if($task == 'getmarketinfocondominium') {
//		$base_url = get_site_url();
//		$resource_id = $_GET['resourceId'];
//		$model = new BookingForConnectorModelCondominiums;
//		$resource = $model->getCondominiumFromService($resource_id,$language);	 
//			
//		$indirizzo = isset($resource->Address)?$resource->Address:"";
//		$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
//		$comune = isset($resource->CityName)?$resource->CityName:"";
//		$stato = isset($resource->StateName)?$resource->StateName:"";
//		
//		$condominiumdetails_page = get_post( bfi_get_page_id( 'condominiumdetails' ) );
//		$url_condominium_page = get_permalink( $condominiumdetails_page->ID );
//		$routeCondominium = $url_condominium_page . $resource->CondominiumId.'-'.BFI()->seoUrl($resource->Name);
//
//		$output = '<div class="bfi-mapdetails">
//					<div class="bfi-item-title">
//						<a href="'.$routeCondominium.'?fromsearch=1" target="_blank">'.$resource->Name.'</a> 
//					</div>
//					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
//				</div>';    
//    die($output);    	
//    }
//    else if($task == 'getmarketinforesourceonsell') {
//		$base_url = get_site_url();
//		$resource_id = $_GET['resourceId'];
//		$model = new BookingForConnectorModelOnSellUnit;
//		$resource = $model->getItem($resource_id);
//		$merchant = $resource->Merchant;
//		$resource->Price = $resource->MinPrice;
//		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
//		$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
//		$zone = $resource->LocationZone;
//		$location = $resource->LocationName;
//		$contractType = ($resource->ContractType) ? 'contacttype1'  : 'contacttype';
//
//		//	   $img = "/images/default.png";
//		//	   $imgError = "/images/default.png";
//		$img = plugins_url("images/default.png", dirname(__FILE__));
//		$imgError = plugins_url("images/default.png", dirname(__FILE__));
//
//
//		$route = '/merchant-details/sale/'.$resource_id.'-'.seoUrl($resource->Name);
//		if ($resource->ImageUrl != ''){
//		  $img = BFCHelper::getImageUrlResized('onsellunits',$resource->ImageUrl , 'onsellunit_map_default');
//		  $imgError = BFCHelper::getImageUrl('onsellunits',$resource->ImageUrl , 'onsellunit_map_default');
//		}elseif ($merchant->LogoUrl != ''){
//		  $img = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'onsellunit_map_default');
//		  $imgError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'onsellunit_map_default');
//		}
//		ob_start();
//		include('templates/onsellmapmarker.php');
//		$output = ob_get_contents();
//		ob_end_clean();
//		die($output);    	
//    }
//  }


if(!isset($_POST['format'])) {

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


<?php get_footer( 'searchavailability' ); ?>
<?php
}
?>	
