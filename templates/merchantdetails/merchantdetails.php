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


//$merchantRules ='';
//if(!empty($merchant->Rules)){
//	$merchantRules = BFCHelper::getLanguage($merchant->Rules, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags', 'bbcode'=>'bbcode'));
//}
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

$fromSearch =  BFCHelper::getVar('fromsearch','0');

$routeSearch = $routeMerchant;
if(!empty($fromSearch)){
	$routeSearch .= "/?task=getMerchantResources&fromsearch=1";
}else{
	$routeSearch .= "/?task=getMerchantResources";
}

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = (int)$merchant->Rating;
if ($rating>9 )
{
	$rating = $rating/10;
	$hasSuperior = ($MerchantDetail->Rating%10)>0;
} 

$reviewavg = isset($merchant->Avg) ? $merchant->Avg->Average : 0;
$reviewcount = isset($merchant->Avg) ? $merchant->Avg->Count : 0;
$resourceDescription = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')) ;
$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

/*---------------IMPOSTAZIONI SEO----------------------*/

//moved before "get_header"
//	$merchantDescriptionSeo = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'nobr'=>'nobr', 'bbcode'=>'bbcode', 'striptags'=>'striptags')) ;
//	if (!empty($merchantDescriptionSeo) && strlen($merchantDescriptionSeo) > 170) {
//	    $merchantDescriptionSeo = substr($merchantDescriptionSeo,0,170);
//	}
//	$titleHead = "$merchantName ($comune, $stato) - $merchant->MainCategoryName - $sitename";
//	$keywordsHead = "$merchantName, $comune, $stato, $merchant->MainCategoryName";
//	$routeSeo = ($isportal)? $routeMerchant: $base_url;

//		$this->document->setTitle($titleHead);
//		$this->document->setDescription($merchantDescriptionSeo);
//		$this->document->setMetadata('keywords', $keywordsHead);
//		$this->document->setMetadata('robots', "index,follow");
//		
//		$this->document->setMetadata('og:type', "Organization");
//		$this->document->setMetadata('og:title', $titleHead);
//		$this->document->setMetadata('og:description', $merchantDescriptionSeo);
//		$this->document->setMetadata('og:url', $routeSeo);

	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br'));
	$payload["url"] = ($isportal)? $routeMerchant: $base_url; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/
?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload); ?>
// ]]></script>
<div class="bfi-content bfi-hideonextra">

	<?php if($reviewcount>0){ ?>
	<div class="bfi-row">
		<div class="bfi-col-md-10">
	<?php } ?>
		<div class="bfi-title-name bfi-hideonextra"><h1><?php echo  $merchant->Name?></h1>
			<span class="bfi-item-rating">
				<?php for($i = 0; $i < $rating; $i++) { ?>
				<i class="fa fa-star"></i>
				<?php } ?>
				<?php if ($hasSuperior) { ?>
					&nbsp;S
				<?php } ?>
			</span>
		</div>
		<div class="bfi-address bfi-hideonextra">
			<i class="fa fa-map-marker fa-1"></i> <?php if (($showMap)) :?><a class="bfi-map-link" rel="#merchant_map"><?php endif; ?><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
			<?php if (($showMap)) :?></a><?php endif; ?>
		</div>	
	<?php if($reviewcount>0){ 
		$totalreviewavg = BFCHelper::convertTotal($reviewavg);
		?>
		</div>	
		<div class="bfi-col-md-2 bfi-cursor bfi-avg bfi-text-right" id="bfi-avgreview">
			<a href="#bfi-rating-container" class="bfi-avg-value"><?php echo $rating_text['merchants_reviews_text_value_'.$totalreviewavg]; ?> <?php echo number_format($reviewavg, 1); ?></a><br />
			<a href="#bfi-rating-container" class="bfi-avg-count"><?php echo $reviewcount; ?> <?php _e('Reviews', 'bfi') ?></a>
		</div>	
	</div>	
	<?php } ?>
	<div class="bfi-clearfix"></div>
	<ul class="bfi-menu-top">
		<!-- <li><a rel=".bfi-resourcecontainer-gallery" data-toggle="tab"><?php echo  _e('Media' , 'bfi') ?></a></li> -->
       
		<?php if (!empty($resourceDescription)):?><li ><a rel=".bfi-description-data"><?php echo  _e('Description', 'bfi') ?></a></li><?php endif; ?>
		<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?><li><a rel=".bfi-ratingslist"><?php echo  _e('Reviews' , 'bfi') ?></a></li><?php endif; ?>
		<?php if (($showMap)) :?><li><a rel="#merchant_map"><?php echo _e('Map' , 'bfi') ?></a></li><?php endif; ?>
		<?php if ($merchant->HasResources):?><li class="bfi-book"><a rel="#divcalculator" data-toggle="tab"><?php echo  _e('Booking' , 'bfi') ?></a></li><?php endif; ?>
	</ul>
</div>
	
	<div class="bfi-resourcecontainer-gallery">
	<?php  
			$bfiSourceData = 'merchant';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($merchant->ImageData)) {
				$bfiImageData = $merchant->ImageData;
			}
			if(!empty($merchant->VideoData)) {
				$bfiVideoData = $merchant->VideoData;
			}
//			include(BFI()->plugin_path().'/templates/gallery.php');
			bfi_get_template("gallery.php",array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
	?>
	</div>
<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-md-8 bfi-description-data">
			<?php echo $resourceDescription ?>		
		</div>	
		<div class="bfi-col-md-4">
			<div class="bfi-feature-data">
				<strong><?php _e('In short', 'bfi') ?></strong>
				<div id="bfi-merchant-tags"></div>
				<?php if(isset($merchant->AttachmentsString) && !empty($merchant->AttachmentsString)){
					?>
					<div  class="bfi-attachmentfiles">
					<?php 
								
					$resourceAttachments = json_decode($merchant->AttachmentsString);
					
					foreach ($resourceAttachments as $keyAttachment=> $resourceAttachment) {
					    if ($keyAttachment>COM_BOOKINGFORCONNECTOR_MAXATTACHMENTFILES) {
					        break;
					    }
						$resourceAttachmentName = $resourceAttachment->Name;
						$resourceAttachmentExtension= "";
						
						$path_parts = pathinfo($resourceAttachmentName);
						if(!empty( $path_parts['extension'])){
							$resourceAttachmentExtension = $path_parts['extension'];
							$resourceAttachmentName =  str_replace(".".$resourceAttachmentExtension, "", $resourceAttachmentName);
						}
						$resourceAttachmentIcon = bfi_get_file_icon($resourceAttachmentExtension);
						?>
					    <?php echo $resourceAttachmentIcon ?> <a href="<?php echo $resourceAttachment->LinkValue ?>" target="_blank"><?php echo $resourceAttachmentName ?></a><br />
					    <?php 
					}
				?>
					</div>
				<?php } ?>
			</div>
				<!-- AddToAny BEGIN -->
				<a class="bfi-btn bfi-alternative2 bfi-pull-right a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php _e('Share', 'bfi') ?></a>
				<script async src="https://static.addtoany.com/menu/page.js"></script>
				<!-- AddToAny END -->
		</div>	
	</div>
	
	<?php if ($merchant->HasResources){?>
		<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				$resourceId = 0;
				$condominiumId = 0;
				bfi_get_template("search_details.php",array("merchant"=>$merchant,"resourceId"=>$resourceId,"condominiumId"=>$condominiumId,"currencyclass"=>$currencyclass));	
//				include(BFI()->plugin_path().'/templates/search_details.php'); //merchant temp ?>
			</div>
	<?php } ?>	
	
	
	<div class="bfi-clearfix"></div>
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
			<?php foreach ($services as $service){?>
				<?php
				if ($count > 0) { 
					echo ',';
				}
				?>			
				<?php echo BFCHelper::getLanguage($service->Name, $language) ?>
				<?php $count += 1; ?>
			<?php } ?>
		</div>
	<?php endif; ?>	

	<div class="bfi-clearfix"></div>

	<?php  bfi_get_template('merchant_small_details.php',array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant));  ?>
	
	<?php if (($showMap)) {?>
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
				script.src = "//maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReadyMerchant";
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
<?php } ?>


<?php if ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3){?>
	<div class="bfi-ratingslist">
	<?php
		$summaryRatings = $modelmerchant->getMerchantRatingAverageFromService($merchant_id);
		$modelmerchant->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
		$ratings = $modelmerchant->getItemsRating($merchant_id);
		if ( false !== ( $temp_message = get_transient( 'temporary_message' ) )) {
			echo $temp_message;
			delete_transient( 'temporary_message' );
		}
	?>
	<?php  bfi_get_template('merchantdetails/merchant-ratings.php',array("merchant"=>$merchant,"summaryRatings"=>$summaryRatings,"ratings"=>$ratings,"routeMerchant"=>$routeMerchant));  ?>
	</div>
<?php } ?>

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
			jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
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

	bfiGetTagsMg();
	jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 

});
</script>