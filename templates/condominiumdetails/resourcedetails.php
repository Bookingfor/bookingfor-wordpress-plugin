<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$merchant = $resource->Merchant;
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

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );
$resourceName = BFCHelper::getLanguage($resource->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$merchantName = BFCHelper::getLanguage($merchant->Name, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
$resourceDescription = BFCHelper::getLanguage($resource->Description, $GLOBALS['bfi_lang'], null, array('striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br'));
$resourceDescriptionSeo = BFCHelper::getLanguage($resource->Description, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags'));
$uri = $url_resource_page.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);

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


$resourceLat = null;
$resourceLon = null;

if (!empty($resource->XGooglePos) && !empty($resource->YGooglePos)) {
	$resourceLat = $resource->XGooglePos;
	$resourceLon = $resource->YGooglePos;
}
if(!empty($resource->XPos)){
	$resourceLat = $resource->XPos;
}
if(!empty($resource->YPos)){
	$resourceLon = $resource->YPos;
}
if(empty($resourceLat) && !empty($merchant->XPos)){
	$resourceLat = $merchant->XPos;
}
if(empty($resourceLon) && !empty($merchant->YPos)){
	$resourceLon = $merchant->YPos;
}
if(empty($resourceLat) && !empty($merchant->XGooglePos)){
	$resourceLat = $merchant->XGooglePos;
}
if(empty($resourceLon) && !empty($merchant->YGooglePos)){
	$resourceLon = $merchant->YGooglePos;
}

$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) );
$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;


$indirizzo = isset($resource->Address)?$resource->Address:"";
$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
$comune = isset($resource->CityName)?$resource->CityName:"";
$stato = isset($resource->StateName)?$resource->StateName:"";


$merchantRules = "";
if(isset($merchant->Rules)){
	$merchantRules = BFCHelper::getLanguage($merchant->Rules, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
}
$resourceRules = "";
if(isset($resource->Rules)){
	$resourceRules = BFCHelper::getLanguage($resource->Rules, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'));
}

$services = [];
if (!empty($resource->ServiceIdList)){
	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList, $language);
}

$resourceRoute = $uri;
$routeRating = $uri.'/'._x('review', 'Page slug', 'bfi' );
$routeInfoRequest = $uri.'/'._x('inforequestpopup', 'Page slug', 'bfi' );
$routeRapidView = $uri.'/'._x('rapidview', 'Page slug', 'bfi' );

$searchedRequest =  array(
	'pricetype' => BFCHelper::getStayParam('pricetype'),
	'rateplanId' => BFCHelper::getStayParam('rateplanId'),
	'variationPlanId' => BFCHelper::getStayParam('variationPlanId'),
	'state' => BFCHelper::getStayParam('state'),
	'gotCalculator' => isset($_REQUEST['calculate'])?$_REQUEST['calculate']:''
);

$ProductAvailabilityType = $resource->AvailabilityType;

$fromSearch =  BFCHelper::getVar('fromsearch','0');

$routeSearch = $uri;
if(!empty($fromSearch)){
	$routeSearch .= "/?task=getMerchantResources&fromsearch=1";
}else{
	$routeSearch .= "/?task=getMerchantResources";
}

$reviewavg = 0;
$reviewcount = 0;
$showReview = false;
$resource->IsCatalog = false;
$resource->MaxCapacityPaxes = 0;
$resource->TagsIdList = "";


//if ($merchant->RatingsContext != NULL && $merchant->RatingsContext > 0) {
//	$showReview = true;
//	if ($merchant->RatingsContext ==1 && !empty($merchant->Avg)) {
//		$reviewavg =  isset($merchant->Avg) ? $merchant->Avg->Average : 0;
//		$reviewcount =  isset($merchant->Avg) ? $merchant->Avg->Count : 0;
//	}
//	if ($merchant->RatingsContext ==2 || $merchant->RatingsContext ==3 ) {
//		$summaryRatings = $model->getRatingAverageFromService($merchant->MerchantId,$resource->ResourceId);
//		if(!empty($summaryRatings)){
//			$reviewavg = $summaryRatings->Average;
//			$reviewcount = $summaryRatings->Count;
//		}
//	}
//}

$payloadresource["@type"] = "Product";
$payloadresource["@context"] = "http://schema.org";
$payloadresource["name"] = $resourceName;
$payloadresource["description"] = $resourceDescriptionSeo;
$payloadresource["url"] = $resourceRoute; 
if (!empty($resource->ImageUrl)){
	$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('condominium',$resource->ImageUrl, 'logobig');
}
?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payloadresource); ?>
// ]]></script>
<?php 
$payload["@type"] = "Organization";
$payload["@context"] = "http://schema.org";
$payload["name"] = $merchant->Name;
$payload["description"] = BFCHelper::getLanguage($merchant->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br'));
$payload["url"] = ($isportal)? $routeMerchant: $base_url; 
if (!empty($merchant->LogoUrl)){
	$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
}
?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload); ?>
// ]]></script>

<div class="bfi-content bfi-hideonextra">	
	
	<?php if($reviewcount>0){ ?>
	<div class="bfi-row">
		<div class="bfi-col-md-10">
	<?php } ?>
			<div class="bfi-title-name bfi-hideonextra"><?php echo  $resourceName?> - <span class="bfi-cursor"><?php echo  $merchantName?></span></div>
			<div class="bfi-address bfi-hideonextra">
				<i class="fa fa-map-marker fa-1"></i> <?php if (($showResourceMap)) :?><a class="bfi-map-link" rel="#resource_map"><?php endif; ?><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
				<?php if (($showResourceMap)) :?></a><?php endif; ?>
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
<!-- Navigation -->	
	<ul class="bfi-menu-top bfi-hideonextra">
		<!-- <li ><a rel=".bfi-resourcecontainer-gallery"><?php echo  _e('Media' , 'bfi') ?></a></li> -->
		<?php if (!empty($resourceDescription)):?><li><a rel=".bfi-description-data"><?php echo  _e('Description', 'bfi') ?></a></li><?php endif; ?>
		<?php if($isportal): ?><li ><a rel=".bfi-merchant-simple"><?php echo  _e('Host' , 'bfi') ?></a></li>
		<?php if ($showReview):?><li><a rel=".bfi-ratingslist"><?php echo  _e('Reviews' , 'bfi') ?></a></li><?php endif; ?><?php endif; ?>
		<?php if(!$resource->IsCatalog): ?><li class="bfi-book"><a rel="#divcalculator"><?php echo  _e('Book now' , 'bfi') ?></a></li><?php endif; ?>
	</ul>
</div>

<div class="bfi-resourcecontainer-gallery bfi-hideonextra">
	<?php  
			$bfiSourceData = 'condominium';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($resource->ImagesData)) {
				$bfiImageData = $resource->ImagesData;
			}
			if(!empty($resource->VideoData)) {
				$bfiVideoData = $resource->VideoData;
			}
			bfi_get_template("gallery.php",array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
	?>
</div>

<div class="bfi-content">	
	<div class="bfi-row bfi-hideonextra">
		<div class="bfi-col-md-8 bfi-description-data">
			<?php echo $resourceDescription ?>		
		</div>	
		<div class="bfi-col-md-4">
			<div class=" bfi-feature-data">
				<strong><?php _e('In short', 'bfi') ?></strong>
				<div class="bfiresourcegroups" id="bfitags" rel="<?php echo $resource->TagsIdList ?>"></div>
				<?php if(isset($resource->Area) && $resource->Area>0  ){ ?><?php _e('Floor area', 'bfi') ?>: <?php echo $resource->Area ?> m&sup2; <br /><?php } ?>
				<?php if ($resource->MaxCapacityPaxes>0){?>
					<br />
					<?php if ($resource->MinCapacityPaxes<$resource->MaxCapacityPaxes){?>
						<?php _e('Min paxes', 'bfi') ?>: <?php echo $resource->MinCapacityPaxes ?><br />
					<?php } ?>
					<?php _e('Max paxes', 'bfi') ?>: <?php echo $resource->MaxCapacityPaxes ?><br />
				<?php } ?>
				<?php if((isset($resource->EnergyClass) && $resource->EnergyClass>0 ) || (isset($resource->EpiValue) && $resource->EpiValue>0 ) ){ ?>
				<!-- Table Details --><br />	
				<table class="bfi-table bfi-table-striped bfi-resourcetablefeature">
					<tr>
						<?php if(isset($resource->EnergyClass) && $resource->EnergyClass>0){ ?>
						<td class="bfi-col-md-"><?php _e('Energy Class', 'bfi'); ?>:</td>
						<td class="bfi-col-md-3" <?php if(!isset($resource->EpiValue)) {echo "colspan=\"3\"";}?>>
							<div class="bfi-energyClass bfi-energyClass<?php echo $resource->EnergyClass?>">
							<?php 
								switch ($resource->EnergyClass) {
									case 0: echo __('not set', 'bfi'); break;
									case 1: echo __('nondescript', 'bfi'); break;
									case 2: echo __('free property', 'bfi'); break;
									case 3: echo __('Under evaluation', 'bfi'); break;
									case 100: echo __('A+', 'bfi'); break;
									case 101: echo __('A', 'bfi'); break;
									case 102: echo __('B', 'bfi'); break;
									case 103: echo __('C', 'bfi'); break;
									case 104: echo __('D', 'bfi'); break;
									case 105: echo __('E', 'bfi'); break;
									case 106: echo __('F', 'bfi'); break;
									case 107: echo __('G', 'bfi'); break;
								}
							?>
							</div>
						</td>
						<?php } ?>
						<?php if(isset($resource->EpiValue) && $resource->EpiValue>0){ ?>
						<td class="bfi-col-md-"><?php _e('EPI Value', 'bfi'); ?>:</td>
						<td class="bfi-col-md-" <?php if(!isset($resource->EnergyClass)) {echo "colspan=\"3\"";}?>><?php echo $resource->EpiValue?> <?php echo $resource->EpiUnit?></td>
						<?php } ?>
					</tr>
				</table>
				<?php } ?>
				<?php if(isset($resource->AttachmentsString) && !empty($resource->AttachmentsString)){
					?>
					<div  class="bfi-attachmentfiles">
					<?php 
								
					$resourceAttachments = json_decode($resource->AttachmentsString);
					
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


	<div id="booknow">
		<?php if(!$resource->IsCatalog): ?>
			<!-- calc -->
			<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				//$resourceId = $resource->ResourceId;
				$condominiumId = $resource->CondominiumId;
				bfi_get_template("search_details.php",array("merchant"=>$merchant,"resourceId"=>$resourceId,"condominiumId"=>$condominiumId,"currencyclass"=>$currencyclass));	
				//include(BFI()->plugin_path().'/templates/search_details.php'); //merchant temp ?>
					

			</div>
		<?php endif; ?>
	</div>
	<div class="bfi-clearfix"></div>
	<?php  
	bfi_get_template("merchant_small_details.php",array("resource_id"=>$resource_id,"resource"=>$resource,"merchant"=>$merchant,"routeMerchant"=>$routeMerchant));	
	?>

<?php if (($showResourceMap)) {?>
<div class="bfi-content-map bfi-hideonextra">
<br /><br />
<div id="resource_map" style="width:100%;height:350px"></div>
</div>
	<script type="text/javascript">
	<!--
		var mapUnit;
		var myLatlng;

		// make map
		function handleApiReady() {
			myLatlng = new google.maps.LatLng(<?php echo $resourceLat?>, <?php echo $resourceLon?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapUnit = new google.maps.Map(document.getElementById("resource_map"), myOptions);
			var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: mapUnit
			  });
			redrawmap()
		}

		function openGoogleMapResource() {
			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReady";
				document.body.appendChild(script);
			}else{
				if (typeof mapUnit !== 'object'){
					handleApiReady();
				}
			}
		}
		function redrawmap() {
			if (typeof google !== "undefined")
			{
				if (typeof google === 'object' || typeof google.maps === 'object'){
					google.maps.event.trigger(mapUnit, 'resize');
					mapUnit.setCenter(myLatlng);
				}
			}
		}

		jQuery(window).resize(function() {
			redrawmap()
		});
		jQuery(document).ready(function(){
			openGoogleMapResource();
		});

	//-->

	</script>
<?php } ?>

<?php if ($showReview){?>
	<div class="bfi-ratingslist bfi-hideonextra">
	<?php
		$summaryRatings = 0;
		$ratings = null;
		if ($merchant->RatingsContext ==1){
			$modelmerchant =  new BookingForConnectorModelMerchantDetails;
			$summaryRatings = $modelmerchant->getMerchantRatingAverageFromService($merchant->MerchantId);
			$ratings = $modelmerchant->getItemsRating($merchant->MerchantId);
		}else{
			$summaryRatings = $model->getRatingAverageFromService($merchant->MerchantId,$resource->ResourceId);
			$ratings = $model->getRatingsFromService(0,5,$resource->ResourceId);
		}
		if ( false !== ( $temp_message = get_transient( 'temporary_message' ) )) :
			echo $temp_message;
			delete_transient( 'temporary_message' );
		endif;
	?>
		<?php  bfi_get_template('condominiumdetails/resource-ratings.php',array("merchant"=>$merchant,"summaryRatings"=>$summaryRatings,"ratings"=>$ratings,"routeMerchant"=>$routeMerchant,"routeRating"=>routeRating));  ?>
	</div>
<?php } ?>	
	<script type="text/javascript">
	<!--
	jQuery(function($) {
		jQuery('.bfi-menu-top li a,.bfi-map-link').click(function(e) {
			e.preventDefault();
			jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
		});
		jQuery('#bfi-avgreview').click(function() {
			jQuery('html, body').animate({ scrollTop: jQuery(".bfi-ratingslist").offset().top }, 2000);
		});
		jQuery('.bfi-title-name span').click(function() {
			jQuery('html, body').animate({ scrollTop: jQuery(".bfi-merchant-simple").offset().top }, 2000);
		});

		var rateplansTags = [];

		var  pagelist = "<?php echo $routeSearch; ?>";
		
//		jQuery("#divcalculator").load(pagelist, function() {});

		<?php if(isset($_REQUEST["pricetype"])){ ?>	
			
			jQuery('html, body').animate({
				scrollTop: jQuery("#divcalculator").offset().top
			}, 1000);

		<?php }  ?>	

	});
	function bfiGoToTop() {
		this.event.preventDefault();
		jQuery('html, body').animate({ scrollTop: jQuery(".bfi-title-name ").offset().top }, 2000);
	};
	//-->
	</script>
</div>
