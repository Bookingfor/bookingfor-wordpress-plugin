<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$currencyclass = bfi_get_currentCurrency();

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

		<?php if ( apply_filters( 'bookingfor_show_page_title', true ) ) : ?>


		<?php endif; ?>


<?php
}
if(!isset($_GET['task'])) {
//	if(!isset($_POST['format'])) {
//		unset($_SESSION['search.filterparams']);
		bfi_setSessionFromSubmittedData();
//	}

	$page = bfi_get_current_page() ;
	
	$start = ($page - 1) * COM_BOOKINGFORCONNECTOR_ITEMPERPAGE;
    
    $searchmodel = new BookingForConnectorModelSearch;
		
$pars = BFCHelper::getSearchParamsSession();
$filterinsession = BFCHelper::getFilterSearchParamsSession();

$items =  array();
$total = 0;
$currSorting = "";

if (isset($pars['checkin']) && isset($pars['checkout'])){
	$now = new DateTime();
	$checkin = isset($pars['checkin']) ? $pars['checkin'] : new DateTime();
	$checkout = isset($pars['checkout']) ? $pars['checkout'] : new DateTime();
	
	if ($checkin == $checkout || $checkin->diff($checkout)->format("%a") <0 || $checkin < $now ){
		$nodata = true;
	}else{
		$items = $searchmodel->getItems(false, false, $start,COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
		
		$items = is_array($items) ? $items : array();
				
		$total=$searchmodel->getTotal();
		$currSorting=$searchmodel->getOrdering() . "|" . $searchmodel->getDirection();
	}

}
$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);

$merchant_ids = '';


$merchantResults = $_SESSION['search.params']['merchantResults'];
$condominiumsResults = $_SESSION['search.params']['condominiumsResults'];
$totPerson = $_SESSION['search.params']['paxes'];
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
		$sendData = true;		
		$maxItemsView = 3;
			
		if(!empty($items)) {
			if($merchantResults) {
//				$resIndex = 0;
				$listName = "Resources Group List";
				foreach($items as $itemkey => $itemValue) {
					$obj = new stdClass();
					$obj->Id = $itemValue->MerchantId . " - Merchant";
					$obj->MerchantId = $itemValue->MerchantId;
					$obj->Name = $itemValue->MrcName;
					$obj->MrcCategoryName = $itemValue->MrcCategoryName;
					$obj->MrcName = $itemValue->MrcName;
					$obj->Position = $itemkey;
					$totalItems[] = $obj;
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
				$sendData = false;
				$resIndex = 0;
				$listName = "Resources Group List";
				foreach($items as $mrckey => $mrcValue) {
					$obj = new stdClass();
					$obj->Id = $mrcValue->CondominiumId . " - Resource Group";
					$obj->MerchantId = $mrcValue->MerchantId;
					$obj->Name = $mrcValue->Name;
					$obj->MrcCategoryName = $mrcValue->MrcCategoryName;
					$obj->MrcName = $mrcValue->MerchantName;
					$obj->Position = $mrckey;
					$totalItems[] = $obj;
					foreach($mrcValue->Resources as $resKey => $resValue) {
						$objRes = new stdClass();
						$objRes->Id = $resValue->ResourceId . " - Resource";
						$objRes->GroupId = $mrcValue->CondominiumId;
						$objRes->MerchantId = $mrcValue->MerchantId;
						$objRes->Name = $resValue->ResName;
						$objRes->MrcName = $mrcValue->Name;
						$objRes->MrcCategoryName = $mrcValue->MrcCategoryName;
						$objRes->Position = $resIndex;
						if($resKey >= $maxItemsView) {
							$objRes->ExcludeInitial = true;
						}
						$totalItems[] = $objRes;
						$resIndex++;
					}
				}
			} else {
				$listName = "Resources Search List";
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
//		$analyticsEnabled = $this->checkAnalytics($listName) && $config->get('eecenabled', 0) == 1;
//		if(count($totalItems) > 0 && $analyticsEnabled) {
		add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
		do_action('bfi_head', $listName);
		if(count($totalItems) > 0 && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
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


	if($_SESSION['search.params']['merchantResults']) {
		$merchants = $items ;
		include(BFI()->plugin_path().'/templates/search/new-search-listing.php');	  
	} else {
		$results = $items ;
      
//      echo "<pre>";
//      echo BFI()->plugin_path();
//      echo "</pre>";
      
//	  $resource_ids = array();
//    
//      foreach ($items as $resource) {
//      $resource_ids[] = $resource->ResourceId;
//      }
//      $resource_ids = implode(',', $resource_ids);
   //   echo '<div id="idsforajax" rel="'.$resource_ids.'"></div>';
      include(BFI()->plugin_path().'/templates/search/new-search-listing.php');
    }
//    $output = '';
//    $output = $output. '</div>
//    </div>';
//  $url = $_SERVER['REQUEST_URI'];
//	$url = esc_url( get_permalink() ); 
//  $pagination_args = array(
//    'base'            => $url. '%_%',
//    'format'          => '?page=%#%',
//    'total'           => $pages,
//    'current'         => $page,
//    'show_all'        => false,
//    'end_size'        => 5,
//    'mid_size'        => 2,
//    'prev_next'       => true,
//    'prev_text'       => __('&laquo;'),
//    'next_text'       => __('&raquo;'),
//    'type'            => 'plain',
//    'add_args'        => false,
//    'add_fragment'    => ''
//  );
//
//  $paginate_links = paginate_links($pagination_args);
//    if ($paginate_links) {
//      echo "<nav class='custom-pagination'>";
////      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
//      echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
//      print $paginate_links;
//      echo "</nav>";
//    }
//    $output = $output. "</div></div>";

  
  }
  else {
    $task = $_GET['task'];
    if($task == 'GetMerchantsByIds') {
      $lists = $_GET['merchantsId'];
      $merchants = BFCHelper::getMerchantsByIds($lists);
      die($merchants);
    }
    else if($task == 'getMerchantGroups') {
      $merchantgroups = BFCHelper::getMerchantGroups();
      wp_send_json($merchantgroups);
    }
    else if($task == 'GetPhoneByMerchantId') {
    	$merchantId = $_GET['merchantid'];
    	$language = $GLOBALS['bfi_lang'];
      $phno = BFCHelper::GetPhoneByMerchantId($merchantId,$language);
      wp_send_json($phno);
    }
    else if($task == 'GetResourcesByIds') {
      $lists = $_GET['resourcesId'];
      $language = $GLOBALS['bfi_lang'];
      $resources = BFCHelper::GetResourcesByIds($lists,$language);
      @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
      echo $resources;
      die();
    }
    else if($task == 'getmarketinfo') {
    	$base_url = get_site_url();
    	$merchant_id = $_GET['merchantId'];
    	$model =  new BookingForConnectorModelMerchantDetails;
      $merchant = $model->getItem($merchant_id);
      $output = '<div class="com_bookingforconnector_map_merchantdetails" style="display:block;height:150px;overflow:auto; width: 300px;">
	<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t257">
		<h3 style="margin:0;" class="com_bookingforconnector_merchantdetails-name"><a class="com_bookingforconnector_merchantdetails-nameAnchor" href="'.$base_url.'/merchant-details/merchantdetails/'.$merchant->MerchantId.'-'.seoUrl($merchant->Name).'">'.$merchant->Name.'</a> 
			<br/><span class="bfi_merchantdetails-rating bfi_merchantdetails-rating'.$merchant->Rating.'">
				<span class="bfi_merchantdetails-ratingText">Rating '.$merchant->Rating.'</span>
			</span>
		</h3>
		<div class="com_bookingforconnector_merchantdetails-contacts" style="display:none;">
			<h3>'.__('Facility contacts data', 'bfi').'</h3>
			<div>'.__('Phone', 'bfi').': <br/>
			<!-- Fax: --> </div>
		</div>
	</div>
</div>';    
    die($output);
    }
    else if($task == 'getmarketinforesource') {
    	$base_url = get_site_url();
    	$resource_id = $_GET['resourceId'];
      $model = new BookingForConnectorModelResource;
      $resource = $model->getItem($resource_id);
	$merchant = $resource->Merchant;
      $output = '<div class="com_bookingforconnector_map_merchantdetails" style="display:block;height:150px;overflow:auto; width: 300px;">
	<div class="com_bookingforconnector_merchantdetails com_bookingforconnector_merchantdetails-t257">
		<h3 style="margin:0;" class="com_bookingforconnector_merchantdetails-name"><a class="com_bookingforconnector_merchantdetails-nameAnchor" href="'.$base_url.'/accommodation-details/resource/'.$resource->ResourceId.'-'.seoUrl($resource->Name).'">'.$resource->Name.'</a> 
			<br/><span class="bfi_merchantdetails-rating bfi_merchantdetails-rating'.$merchant->Rating.'">
				<span class="bfi_merchantdetails-ratingText">Rating '.$merchant->Rating.'</span>
			</span>
		</h3>
		<div class="com_bookingforconnector_merchantdetails-contacts" style="display:none;">
			<h3>'.__('Facility contacts data', 'bfi').'</h3>
			<div>'.__('Phone', 'bfi').': <br/>
			Fax: </div>
		</div>
	</div>
</div>';    
    die($output);    	
    }
    else if($task == 'getmarketinforesourceonsell') {
    	$base_url = get_site_url();
    	$resource_id = $_GET['resourceId'];
      $model = new BookingForConnectorModelOnSellUnit;
      $resource = $model->getItem($resource_id);
      $merchant = $resource->Merchant;
      $resource->Price = $resource->MinPrice;
      $resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
      $resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
      $zone = $resource->LocationZone;
      $location = $resource->LocationName;
      $contractType = ($resource->ContractType) ? 'contacttype1'  : 'contacttype';

//	   $img = "/images/default.png";
//	   $imgError = "/images/default.png";
	$img = plugins_url("images/default.png", dirname(__FILE__));
	$imgError = plugins_url("images/default.png", dirname(__FILE__));


      $route = '/merchant-details/sale/'.$resource_id.'-'.seoUrl($resource->Name);
	   if ($resource->ImageUrl != ''){
		  $img = BFCHelper::getImageUrlResized('onsellunits',$resource->ImageUrl , 'onsellunit_map_default');
		  $imgError = BFCHelper::getImageUrl('onsellunits',$resource->ImageUrl , 'onsellunit_map_default');
	   }elseif ($merchant->LogoUrl != ''){
		  $img = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'onsellunit_map_default');
		  $imgError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'onsellunit_map_default');
       }
      ob_start();
      include('templates/onsellmapmarker.php');
      $output = ob_get_contents();
      ob_end_clean();
      die($output);    	
    }
  }


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
