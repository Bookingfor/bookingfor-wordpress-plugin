<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

$rating_text = array('merchants_reviews_text_value_0' => __('Very poor', 'bfi'),
						'merchants_reviews_text_value_1' => __('Poor', 'bfi'),   
						'merchants_reviews_text_value_2' => __('Disappointing', 'bfi'),
						'merchants_reviews_text_value_3' => __('Fair', 'bfi'),
						'merchants_reviews_text_value_4' => __('Okay', 'bfi'),
						'merchants_reviews_text_value_5' => __('Pleasant', 'bfi'),  
						'merchants_reviews_text_value_6' => __('Good', 'bfi'),
						'merchants_reviews_text_value_7' => __('Very good', 'bfi'),  
						'merchants_reviews_text_value_8' => __('Fabulous', 'bfi'), 
						'merchants_reviews_text_value_9' => __('Exceptional', 'bfi'),  
						'merchants_reviews_text_value_10' => __('Exceptional', 'bfi'),                                 
					);


$merchantRules ='';
if(!empty($merchant->Rules)){
	$merchantRules = BFCHelper::getLanguage($merchant->Rules, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags', 'bbcode'=>'bbcode'));
}
$resourceLat = null;
$resourceLon = null;

if (!empty($merchant->XGooglePos) && !empty($merchant->YGooglePos)) {
	$resourceLat = $merchant->XGooglePos;
	$resourceLon = $merchant->YGooglePos;
}
if(!empty($merchant->XPos)){
	$resourceLat = $merchant->XPos;
}
if(!empty($merchant->YPos)){
	$resourceLon = $merchant->YPos;
}
$showMap = (($resourceLat != null) && ($resourceLon !=null) ); 

$modelmerchant =  new BookingForConnectorModelMerchantDetails;

$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";

//	$modelResource = new BookingForConnectorModelResource;
//	$images = array();

		
//	//	 $model = new BookingForConnectorModelResource;
//		$resource_id ='';
//
//    	//$model_resource_details = new BookingForConnectorModelMerchantDetails;
//    	$resources = $modelmerchant->getItems('resourcesajax', $merchant_id);
//	//$resources[0]->ResourceId
//		
//		foreach($resources as $resource){
//			$vartemp = "$resource->ResourceId";
//			$resource = $resource;
//			$language = $GLOBALS['bfi_lang']; 
//			$allstays = $modelResource->getStay($language , $vartemp );
//			if(!empty($allstays)){
//				
//				$resource_id = $resource->ResourceId;
//				break;
//			}
//			
//		}
//		
//	 $_SESSION['search.params']['resourceId'] = $resource_id;
// 
//  $resource = $modelResource->getItem($resource_id);

$fromSearch =  BFCHelper::getVar('fromsearch','0');

$routeSearch = $routeMerchant;
if(!empty($fromSearch)){
	$routeSearch .= "/?task=getMerchantResources&fromsearch=1";
}else{
	$routeSearch .= "/?task=getMerchantResources";
}

$payload["@type"] = "Organization";
$payload["@context"] = "http://schema.org";
$payload["name"] = $merchant->Name;
$payload["description"] = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br'));
$payload["url"] = ($isportal)? $routeMerchant: $base_url; 
if (!empty($merchant->LogoUrl)){
	$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
}
$rating = $merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
} 
$reviewavg = isset($merchant->Avg) ? $merchant->Avg->Average : 0;
$reviewcount = isset($merchant->Avg) ? $merchant->Avg->Count : 0;

?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload); ?>
// ]]></script>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
//-->
</script>
	<?php if($reviewcount>0){ ?>
	<div class="bfi-row">
		<div class="bfi-col-md-10">
	<?php } ?>
		<div class="bfi-title-name bfi-hideonextra"><?php echo  $merchant->Name?>
			<span class="com_bookingforconnector_resource-merchant-rating">
				<?php for($i = 0; $i < $rating; $i++) { ?>
				<i class="fa fa-star"></i>
				<?php } ?>
			</span>
		</div>
		<div class="bfi-address bfi-hideonextra">
			<i class="fa fa-map-marker fa-1"></i> <span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
			<?php if (($showMap)) :?><a class="bfi-map-link" rel="#merchant_map"><?php echo _e('Map' , 'bfi') ?></a><?php endif; ?>
		</div>	
	<?php if($reviewcount>0){ 
		$totalreviewavg = BFCHelper::convertTotal($reviewavg);
		?>
		</div>	
		<div class="bfi-col-md-2 bfi-cursor" id="bfi-avgreview">
			<div class="bfi-avgreview-top"><?php echo $rating_text['merchants_reviews_text_value_'.$totalreviewavg]; ?> <?php echo number_format($reviewavg, 1); ?></div>
			<div class="bfi-reviewcount-top"><?php echo $reviewcount; ?> <?php _e('Reviews', 'bfi') ?></div>
		</div>	
	</div>	
	<?php } ?>
<div class="clear"></div>

<div class="com_bookingforconnector_merchantdetails<?php echo BFCHelper::showMerchantRatingByCategoryId($merchant->MerchantTypeId)?>">
	<ul class="bfi-menu-top">
		<li><a rel=".resourcecontainer-gallery" data-toggle="tab"><?php echo  _e('Media' , 'bfi') ?></a></li>
       
		<?php if (!empty($resourceDescription)):?><li ><a rel=".bfi-description-data"><?php echo  _e('Description', 'bfi') ?></a></li><?php endif; ?>
		<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?><li><a rel=".bfi-ratingslist"><?php echo  _e('Reviews' , 'bfi') ?></a></li><?php endif; ?>
		<?php if (($showMap)) :?><li><a rel="#merchant_map"><?php echo _e('Map' , 'bfi') ?></a></li><?php endif; ?>
		<?php if ($merchant->HasResources):?><li class="book"><a rel="#divcalculator" data-toggle="tab"><?php echo  _e('Booking' , 'bfi') ?></a></li><?php endif; ?>
	</ul>

	<?php // echo  $this->loadTemplate('head'); ?>
	
	<div class="resourcecontainer-gallery">
		<?php  include('merchant-gallery.php');  ?>
	</div>
    	<hr>
	<div class="bfi-row">
		<div class="bfi-col-md-8 bfi-description-data">
			<?php echo  BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ?>		
		</div>	
		<div class="bfi-col-md-4">
			<div class=" bfi-feature-data">
				<strong><?php _e('In short', 'bfi') ?></strong>
				<div id="bfi-merchant-tags"></div>
			</div>
				<!-- AddToAny BEGIN -->
				<a class="bfi-smallbtn bfi-sharebtn a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php _e('Share', 'bfi') ?></a>
				<script async src="https://static.addtoany.com/menu/page.js"></script>
				<!-- AddToAny END -->
		</div>	
	</div>
	
	<?php if ($merchant->HasResources):?>
		<a name="calc"></a>
		<div id="divcalculator"><div style="padding:10px;text-align:center;"><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span></div></div>
	<?php endif; ?>	
	
	
	<div class="bfi-clearboth"></div>
	<?php 
	$services = [];
	if (!empty($merchant->ServiceIdList)){
		$services = BFCHelper::GetServicesByIds($merchant->ServiceIdList,$language);
	}
	?>	
	<?php if (!empty($services) && count($services ) > 0):?>
		<div class="bfi-facility"><?php echo  _e('Facility','bfi') ?></div>
		<div class="bfi-facility-list">
			<?php 
			$count=0;
			?>
			<?php foreach ($services as $service):?>
				<?php
				if ($count > 0) { 
					echo ',';
				}
				?>			
				<?php echo BFCHelper::getLanguage($service->Name, $language) ?>
				<?php $count += 1; ?>
			<?php endforeach?>
		</div>
	<?php endif; ?>	

	<div class="bfi-clearboth"></div>
	<?php  include(BFI()->plugin_path().'/templates/merchant_small_details.php');  ?>
	
	<?php if (($showMap)) :?>
	<br /><br />
<div id="merchant_map" style="width:100%;height:350px"></div>
	<script type="text/javascript">
	<!--
		var mapMerchant;
		var myLatlngMerchant;

		// make map
		function handleApiReadyMerchant() {
			myLatlngMerchant = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlngMerchant,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapMerchant = new google.maps.Map(document.getElementById("merchant_map"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlngMerchant,
				  map: mapMerchant
			  });
		}

		function openGoogleMapMerchant() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "http://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing&callback=handleApiReadyMerchant";
				document.body.appendChild(script);
			}else{
				if (typeof mapMerchant !== 'object'){
					handleApiReadyMerchant();
				}
			}
			redrawmap()
		}
		function redrawmap() {
			if (typeof google !== "undefined")
			{
				if (typeof google === 'object' || typeof google.maps === 'object'){
					google.maps.event.trigger(mapMerchant, 'resize');
					mapMerchant.setCenter(myLatlngMerchant);
				}
			}
		}

		jQuery(window).resize(function() {
			redrawmap()
		});

		jQuery(document).ready(function(){
				openGoogleMapMerchant();
		});

	//-->
	</script>
<?php endif; ?>


<?php if ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3):?>
	<br /><br />
	<div class="bfi-ratingslist">
	<?php
		$summaryRatings = $modelmerchant->getMerchantRatingAverageFromService($merchant_id);
		$ratings = $modelmerchant->getItemsRating($merchant_id);
		if ( false !== ( $temp_message = get_transient( 'temporary_message' ) )) :
			echo $temp_message;
			delete_transient( 'temporary_message' );
		endif;
	?>
		<?php include('merchant-ratings.php'); ?>
	</div>
<?php endif; ?>

</div>
<script type="text/javascript">
	var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
	var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
	var bfiMrcTags = '<?php echo $merchant->TagsIdList; ?>';
	var bfiTagsMg = [];
	var bfiTagsMgLoaded=false;
	function bfiGetTagsMg(){
		if (!bfiTagsMgLoaded && bfiMrcTags != null && bfiMrcTags != '')
		{
			bfiTagsMgLoaded=true;
			var queryMG = "task=getMerchantGroups";
			jQuery.post(urlCheck, queryMG, function(data) {
					if(data!=null){
						jQuery.each(JSON.parse(data) || [], function(key, val) {
							if (val.ImageUrl!= null && val.ImageUrl!= '') {
								var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
								var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
								/*--------getName----*/
								var $name = val.Name;
								/*--------getName----*/
								bfiTagsMg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
							} else {
								if (val.IconSrc != null && val.IconSrc != '') {
									bfiTagsMg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
								}
							}
						});	
						var mglist = bfiMrcTags .split(',');
						$htmlmg = '<span class="bfcmerchantgroup">';
						jQuery.each(mglist, function(key, mgid) {
							if(typeof bfiTagsMg[mgid] !== 'undefined' ){
								$htmlmg += bfiTagsMg[mgid];
							}
						});
						$htmlmg += '</span>';
						jQuery("#bfi-merchant-tags").html($htmlmg);
						jQuery('[data-toggle="tooltip"]').tooltip({
							position : { my: 'center bottom', at: 'center top-10' },
							tooltipClass: 'bfi-tooltip bfi-tooltip-top '
							});
					}
			},'json');
		}
	}
	
jQuery(function($) {
	jQuery('.bfi-menu-top li a,.bfi-map-link').click(function(e) {
		e.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
	});
	
	jQuery('#bfi-avgreview').click(function() {
		jQuery('html, body').animate({ scrollTop: jQuery(".bfi-ratingslist").offset().top }, 2000);
	});

	var  pagelist = "<?php echo $routeSearch; ?>";	
	  jQuery("#divcalculator").load(pagelist, function() {
	});
	
	bfiGetTagsMg();
	jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 

});
</script>