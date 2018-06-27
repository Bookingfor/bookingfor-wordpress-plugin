<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$currentView = '';
$orderType = "a";
$task = "sendContact";
$resource = null;
$merchant = null;

$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

if(BFI()->isMerchantPage()){
	$merchant_id = get_query_var( 'merchant_id', 0 );
	$model = new BookingForConnectorModelMerchantDetails;
	$merchant = $model->getItem($merchant_id);	 
	$currentView = 'merchant';
}
if(BFI()->isResourcePage()){
	$resource_id = get_query_var( 'resource_id', 0 );
	$model = new BookingForConnectorModelResource;
	$resource = $model->getItem($resource_id);
	$merchant_id = $resource->MerchantId;
	$model = new BookingForConnectorModelMerchantDetails;
	$merchant = $model->getItem($merchant_id);
	$currentView = 'resource';
	$orderType = "c";
	$task = "sendInforequest";
}
if(BFI()->isResourceOnSellPage()){
	$resource_id = get_query_var( 'resource_id', 0 );
	$model = new BookingForConnectorModelOnSellUnit;
	$resource = $model->getItem($resource_id);
	$merchant_id = $resource->MerchantId;
	$model = new BookingForConnectorModelMerchantDetails;
	$merchant = $model->getItem($merchant_id);
	$currentView = 'onsellunit';
	$orderType = "b";
	$task = "sendOnSellrequest";
}



//  $url = $_SERVER['REQUEST_URI'];
//  $parts = explode('/', $url);
  
  
//  if(in_array('merchant-details', $parts)) {	 
//	 $count = count($parts);
//    $merchant_name = $parts[$count - 1];
//    $part = explode('-', $merchant_name);
//    $merchant_id = $part[0];
//    $model = new BookingForConnectorModelMerchantDetails;
//    $merchant = $model->getItem($merchant_id);
//  }
//  else if(in_array('accommodation-details', $parts)) {
//  	 $count = count($parts);
//    $resource_name = $parts[$count - 1];
//    $part = explode('-', $resource_name);
//    $resource_id = $part[0];
//    $model = new BookingForConnectorModelResource;
//    $resource = $model->getItem($resource_id);
//    $merchant_id = $resource->Merchant->MerchantId;
//    $model = new BookingForConnectorModelMerchantDetails;
//    $merchant = $model->getItem($merchant_id);
//  }

if (!isset($merchant) || $merchant ==null) return;

$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

$moduleclass_sfx = ''; // classe modulo...

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$addressData = $merchant->AddressData;
$contacts = $merchant->ContactData;

$layout = get_query_var( 'bfi_layout', '' );

$resourceLat = null;
$resourceLon = null;

//if (!empty($merchant->XGooglePos) && !empty($merchant->YGooglePos)) {
//	$resourceLat = $merchant->XGooglePos;
//	$resourceLon = $merchant->YGooglePos;
//}
//if(!empty($merchant->XPos)){
//	$resourceLat = $merchant->XPos;
//}
//if(!empty($merchant->YPos)){
//	$resourceLon = $merchant->YPos;
//}


$merchantSiteUrl = '';
$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";
$phone = "";
$fax ="";

	if (empty($merchant->AddressData)){
		$indirizzo = $merchant->Address;
		$cap = $merchant->ZipCode;
		$comune = $merchant->CityName;
		$provincia = $merchant->RegionName;
		if (!empty($merchant->SiteUrl)) {
			$merchantSiteUrl =$merchant->SiteUrl;
			$parsed = parse_url($merchantSiteUrl);
			if (empty($parsed['scheme'])) {
				$merchantSiteUrl = 'http://' . ltrim($merchantSiteUrl, '/');
			}
		}
		$phone = $merchant->Phone;
		$fax = $merchant->Fax;

	}else{
		$addressData = $merchant->AddressData;
		$indirizzo = $addressData->Address;
		$cap = $addressData->ZipCode;
		$comune = $addressData->CityName;
		$provincia = $addressData->RegionName;
		if (!empty($addressData->SiteUrl)) {
			$merchantSiteUrl =$addressData->SiteUrl;
			$parsed = parse_url($merchantSiteUrl);
			if (empty($parsed['scheme'])) {
				$merchantSiteUrl = 'http://' . ltrim($merchantSiteUrl, '/');
			}
		}
		$phone = $addressData->Phone;
		$fax = $addressData->Fax;

	}


//$MerchantType = $merchant->MerchantTypeId;

$uriMerchant = $routeMerchant;
$route = $routeMerchant;

$uriMerchantResources = $uriMerchant .'/'._x( 'resources', 'Page slug', 'bfi' ).'?limitstart=0';
//$uriMerchantRateplanslist = $uriMerchant .'/rateplanslist';
$uriMerchantOffers = $uriMerchant .'/'._x('offers', 'Page slug', 'bfi' ).'?limitstart=0';
$uriMerchantPackages = $uriMerchant .'/'._x('packages', 'Page slug', 'bfi' ).'?limitstart=0';
$uriMerchantOnsellunits = $uriMerchant .'/'._x( 'onsellunits', 'Page slug', 'bfi' ).'?limitstart=0';
$uriMerchantRatings = $uriMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
//$uriMerchantContacts = $uriMerchant .'/contacts';
$uriMerchantRedirect = $uriMerchant .'/'._x('redirect', 'Page slug', 'bfi' );


$routeThanks = $uriMerchant .'/'._x('thanks', 'Page slug', 'bfi' );
$routeThanksKo = $uriMerchant .'/'._x('errors', 'Page slug', 'bfi' );

//$privacy = BFCHelper::GetPrivacy($language);

$merchantLogo = BFI()->plugin_url() . "/assets/images/defaults/default-s3.jpeg";
if (!empty($merchant->LogoUrl)){
	$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
}

$checkoutspan = '+1 day';
$checkin = new DateTime('UTC');
$checkout = new DateTime('UTC');
$paxes = 2;
$pars = BFCHelper::getSearchParamsSession();


if (!empty($pars)){

	$checkin = !empty($pars['checkin']) ? $pars['checkin'] : new DateTime('UTC');
	$checkout = !empty($pars['checkout']) ? $pars['checkout'] : new DateTime('UTC');

	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['merchantCategoryId'])) {
		$merchantCategoryId = $pars['merchantCategoryId'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	if (empty($pars['checkout'])){
		$checkout->modify($checkoutspan); 
	}
}
$checkinId = uniqid('checkin');
$checkoutId = uniqid('checkout');

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = $merchant->Rating;
if ($rating > 9)
{
	$hasSuperior = ($merchant->Rating%10)>0;
	$rating = (int)($rating / 10);
} 

?>


<?php 
echo $before_widget;
// Check if title is set
if ( $title ) {
  echo $before_title . $title . $after_title;
}
	
?>

<?php if(!preg_match("/form/",$_SERVER['REQUEST_URI'])){ ?>

<div class="bfi-modbookingforconnector <?php echo $moduleclass_sfx ?> ">
<div class="bfi-mod-bookingforconnector-inner">
	<div class="bfi-mod-bookingforconnector-vcard-wrapper">
		<div class="bfi-vcard-logo"><a href="<?php echo $route?>"><img src="<?php echo $merchantLogo?>" /></a></div>	
		<div class="bfi-vcard-name bfi-text-center">
			<a href="<?php echo $route?>"><?php echo  $merchant->Name?></a>
			<div class="bfi-item-rating">
				<?php for($i = 0; $i < $rating; $i++) { ?>
					<i class="fa fa-star"></i>
				<?php } ?>
				<?php if ($hasSuperior) { ?>
					&nbsp;S
				<?php } ?>
			</div>
		</div>
		<div class="bfi-merchant-simple bfi-text-center">
			<span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo $cap ?></span> <span class="locality"><?php echo $comune ?></span> <span class="region">(<?php echo $provincia ?>)</span><br />
			<span class="tel"><a  href="javascript:void(0);" onclick="bookingfor.getData(bfi_variable.bfi_urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + bfi_variable.bfi_cultureCode,this,'<?php echo  addslashes($merchant->Name) ?>','PhoneView')"  id="phone<?php echo $merchant->MerchantId?>"><?php _e('Show phone', 'bfi'); ?></a></span> - <?php if ($merchantSiteUrl != ''):?>
				<span class="website"><a target="_blank" href="<?php echo $uriMerchantRedirect; ?>"><?php _e('Web site', 'bfi'); ?></a></span>
			<?php endif;?>
		</div>
	</div>
	<ul class="bfi-merchant-menu">
		<?php if ($merchant->HasResources):?>
			<li class="bfi-merchant-menu-item <?php echo ($layout == 'resources' || $layout == 'resource' || $currentView=='resource' ) ? 'active' : '' ?>"><a href="<?php echo $uriMerchantResources; ?>"><?php _e('Proposals', 'bfi'); ?></a></li>
		<?php endif ?>
		<?php if ($merchant->HasOnSellUnits):?>
			<li class="bfi-merchant-menu-item <?php echo ($layout == 'onsellunits' || $layout == 'onsellunit') ? 'active' : '' ?>"><a href="<?php echo $uriMerchantOnsellunits; ?>"><?php _e('Real Estate', 'bfi'); ?></a></li>
		<?php endif ?>	
		<?php if ($merchant->HasResources):?>
			<?php if ($merchant->HasOffers || true):?>
				<li class="bfi-merchant-menu-item <?php echo ($layout == 'offers' || $layout == 'offer') ? 'active' : '' ?>"><a href="<?php echo $uriMerchantOffers; ?>"><?php _e('Offers', 'bfi'); ?></a></li>
			<?php endif ?>
		<?php endif;?>
		<?php if ($merchant->RatingsContext !== 0) :?>
			<li class="bfi-merchant-menu-item <?php echo ($layout == 'ratings' || $layout == 'rating') ? 'active' : '' ?>"><a href="<?php echo $uriMerchantRatings; ?>"><?php _e('Reviews', 'bfi'); ?></a></li>
		<?php endif ?>	
	</ul>

	<?php if (!empty($resourceLat) && !empty($resourceLon)) {?>
		<div class="bfi-merchantdetails-map" id="map_canvas" style="width:100%; height:250px">
			<a class="lightboxlink" onclick='javascript:openGoogleMapBF();' href="javascript:void(0)">
					<img src="//maps.googleapis.com/maps/api/staticmap?center=<?php echo $resourceLat?>,<?php echo $resourceLon?>&zoom=14&size=260x250&markers=color:blue%7C<?php echo $resourceLat?>,<?php echo $resourceLon?>&key=<?php echo $googlemapsapykey ?>" class="bfi-img-responsive" />
			</a>
		</div>
		<div id="map_canvasBF" style="width:100%; height:400px; display:none;"></div>
	<?php }?>
    <div>
    <?php 
				$paramRef = array(
					"merchant"=>$merchant,
					"layout"=>$layout,
					"currentView"=>$currentView,
					"resource"=>$resource,
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
	
//	include(BFI()->plugin_path().'/templates/merchant-sidebar-contact.php'); 
	?>
    </div>
</div>	
</div>
<?php if (!empty($resourceLat) && !empty($resourceLon)) :?>

<script type="text/javascript">
var mapBF;
		var myLatlngBF;

		// make map
		function handleApiReadyBF() {
			myLatlngBF = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlngBF,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapBF = new google.maps.Map(document.getElementById("map_canvasBF"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlngBF,
				  map: mapBF
			  });
		}
function openGoogleMapBF() {
			var width = jQuery(window).width()*0.9;
			var height = jQuery(window).height()*0.9;

			var $dialog = jQuery("#map_canvasBF").dialog({
					autoOpen: false,
					modal: true,
					resize: function( event, ui ) {
						google.maps.event.trigger(mapBF, 'resize');
						mapBF.setCenter(myLatlngBF);
					},
					height:height,
					width: width,
					fluid: true, //new option
					title: '<?php echo $merchant->Name; ?>',
				dialogClass: 'bfi-dialog bfi-dialog-map'
				});
			if (typeof google !== "undefined" || typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "//maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&callback=handleApiReadyBF";
				document.body.appendChild(script);

			}else{
				if (typeof mapBF !== 'object'){
					handleApiReadyBF();
				}
			}
			$dialog.dialog('open');
			if (typeof google !== "undefined")
			{
				if (typeof google === 'object' || typeof google.maps === 'object'){
					google.maps.event.trigger(mapBF, 'resize');
					mapBF.setCenter(myLatlngBF);
				}
			}
		}
</script>
	<?php endif?>
<?php } ?>
<?php echo $after_widget; ?>
<div class="bfi-clearfix"></div>