<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$merchant = $resource->Merchant;
$base_url = get_site_url();
$currencyclass = bfi_get_currentCurrency();

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


$resource->Price = $resource->MinPrice;
$typeName =  BFCHelper::getLanguage($resource->CategoryName, $language);
$zone = $resource->LocationZone;
$location = $resource->CityName;

$resourceLat = "";
$resourceLon = "";
if(!empty($resource->XGooglePos)){
	$resourceLat = $resource->XGooglePos;
}
if(!empty($resource->YGooglePos)){
	$resourceLon = $resource->YGooglePos;
}

if(!empty($resource->XPos)){
	$resourceLat = $resource->XPos;
}
if(!empty($resource->YPos)){
	$resourceLon = $resource->YPos;
}

$isMapVisible = $resource->IsMapVisible;
$isMapMarkerVisible = $resource->IsMapMarkerVisible;
$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible);
if ($isMapMarkerVisible){
	$htmlmarkerpoint = "&markers=color:blue%7C" . $resourceLat . "," . $resourceLon;
}

$indirizzo = "";
$cap = "";
$comune = "";
$provincia = "";
$indirizzo = $resource->Address;
$cap = $resource->ZipCode;
$comune = $resource->CityName;
$provincia = $resource->RegionName;
$stato = isset($resource->StateName)?$resource->StateName:"";

$deltapricePerCent = 20;
$deltaprice = 1;
if($resource->Price>0){
	$deltaprice = $resource->Price * $deltapricePerCent / 100;
}
$contractTypeId = $resource->ContractType;

$contractType = ($resource->ContractType) ? __('To rent', 'bfi')  : __('On sale', 'bfi');
$dateUpdate =  BFCHelper::parseJsonDate($resource->AddedOn); 
if($resource->UpdatedOn!=''){
	$dateUpdate =  BFCHelper::parseJsonDate($resource->UpdatedOn);
}


$categoryId = $resource->CategoryId;
$zoneId = $resource->ZoneId;

$pricemax = round(($resource->Price + $deltaprice), 0, PHP_ROUND_HALF_UP); 
$pricemin = round(($resource->Price - $deltaprice), 0, PHP_ROUND_HALF_DOWN); 
if (!empty($resource->ServiceIdList)){
	$services=BFCHelper::GetServicesByIds($resource->ServiceIdList, $language);
}

$formRoute =  $uri;
$resourceRoute = $uri;

$searchedRequest =  array(
	'pricetype' => BFCHelper::getStayParam('pricetype'),
	'rateplanId' => BFCHelper::getStayParam('rateplanId'),
	'variationPlanId' => BFCHelper::getStayParam('variationPlanId'),
	'state' => BFCHelper::getStayParam('state'),
	'gotCalculator' => isset($_REQUEST['calculate'])?$_REQUEST['calculate']:''
);

$payloadresource["@type"] = "Product";
$payloadresource["@context"] = "http://schema.org";
$payloadresource["name"] = $resourceName;
$payloadresource["description"] = $resourceDescriptionSeo;
$payloadresource["url"] = $resourceRoute; 
if (!empty($resource->ImageUrl)){
	$payloadresource["image"] = "https:".BFCHelper::getImageUrlResized('onsellunits',$resource->ImageUrl, 'logobig');
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

<div class="bfi-content">	

	<div class="bfi-title-name"><?php echo  $resourceName?> - <span class="bfi-cursor"><?php echo  $merchantName?></span></div>
<?php if ($resource->IsAddressVisible) { ?>
	<div class="bfi-address">
				<i class="fa fa-map-marker fa-1"></i> <?php if (($showResourceMap)) {?><a class="bfi-map-link" rel="#resource_map"><?php } ?><span class="street-address"><?php echo $indirizzo ?></span>, <span class="postal-code "><?php echo  $cap ?></span> <span class="locality"><?php echo $comune ?></span>, <span class="region"><?php echo  $stato ?></span>
				<?php if (($showResourceMap)) {?></a><?php } ?>
	</div>	
<?php } ?>
	<div class="bfi-clearfix"></div>
<!-- Navigation -->	
	<ul class="bfi-menu-top">
		<!-- <li><a rel=".bfi-resourcecontainer-gallery" data-toggle="tab"><?php echo  _e('Media' , 'bfi') ?></a></li> -->
		<?php if (!empty($resourceDescription)):?><li><a rel=".bfi-description-data"><?php echo  _e('Description', 'bfi') ?></a></li><?php endif; ?>
		<?php if($isportal): ?><li ><a rel=".bfi-merchant-simple"><?php echo  _e('Host' , 'bfi') ?></a></li><?php endif; ?>
		<?php if (($showResourceMap)) :?><li><a rel="#resource_map"><?php echo _e('Map' , 'bfi') ?></a></li><?php endif; ?>
	</ul>
</div>
	<div class="bfi-resourcecontainer-gallery">
	<?php  
			$bfiSourceData = 'onsellunits';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($resource->ImageData)) {
				$bfiImageData = $resource->ImageData;
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
				<?php if(isset($resource->Area) && $resource->Area>0  ){ ?><?php _e('Floor area', 'bfi') ?>: <?php echo $resource->Area ?> m&sup2; <br /><?php } ?>
				<?php if ($resource->MaxCapacityPaxes>0){?>
					<br />
					<?php if ($resource->MinCapacityPaxes<$resource->MaxCapacityPaxes){?>
						<?php _e('Min paxes', 'bfi') ?>: <?php echo $resource->MinCapacityPaxes ?><br />
					<?php } ?>
					<?php _e('Max paxes', 'bfi') ?>: <?php echo $resource->MaxCapacityPaxes ?><br />
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

	<table class="bfi-table bfi-table-striped bfi-resourcetablefeature ">
		<tr>
			<td class="bfi-col-md-3"><?php _e('Contract', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $contractType?></td>
			<td class="bfi-col-md-3"><?php _e('Price', 'bfi') ?></td>
			<td class="bfi-col-md-3">
			<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
						 <span class="bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($resource->Price,0, ',', '.')?></span>
			<?php else: ?>
					<?php _e('Contact Agent', 'bfi') ?>
			<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php _e('Type', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $typeName?></td>
			<td class="bfi-col-md-3"><?php _e('Floor area', 'bfi') ?> (m&sup2;)</td>
			<td class="bfi-col-md-3"><?php echo $resource->Area?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php _e('Province', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $location?></td>
			<td class="bfi-col-md-3"><?php _e('Rooms', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php echo $resource->Rooms?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php _e('Area', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php echo  $zone?></td>
			<td class="bfi-col-md-3"><?php _e('Last update', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php echo $dateUpdate?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php _e('Floor', 'bfi') ?>:</td>
			<td class="bfi-col-md-3">
				<?php 
					if($resource->Floor >0){
						echo $resource->Floor ."&#176;";
					}else{
						switch ($resource->Floor) {
							case 0: _e('Ground floor', 'bfi'); break;
							case -1: _e('Top floor', 'bfi'); break;
							case -2: _e('Attic', 'bfi'); break;
							case -3: _e('Multiple levels', 'bfi'); break;
							case -4: _e('ND', 'bfi'); break;
							default: _e('ND', 'bfi'); break;
						}
					}
				
				?>
			</td>
			<td class="bfi-col-md-3"><?php _e('Condition', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php 
					if($resource->IsNewBuilding){
						_e('New', 'bfi');
					}else{
						switch ($resource->Status) {
							case 1: _e('To be refurbished', 'bfi'); break;
							case 2: _e('Good', 'bfi'); break;
							case 3: _e('Excellent', 'bfi'); break;
							case 4: _e('Refurbished', 'bfi'); break;
							case 5: _e('Habitable', 'bfi'); break;
							default: _e('ND', 'bfi'); break;
						}
					}

			?></td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php _e('Bathrooms', 'bfi') ?>:</td>
			<td class="bfi-col-md-3">
				<?php echo (($resource->Baths >-1 )? $resource->Baths : __('ND', 'bfi') ) ?>
			</td>
			<td class="bfi-col-md-3">&nbsp;</td>
			<td class="bfi-col-md-3">&nbsp;</td>
		</tr>
		<tr>
			<td class="bfi-col-md-3"><?php _e('Heating', 'bfi') ?>:</td>
			<td class="bfi-col-md-3">
				<?php
					switch ($resource->CentralizedHeating) {
						case 0: _e('Autonomous', 'bfi'); break;
						case 1: _e('Communal', 'bfi'); break;
						case -1: _e('Pay per use', 'bfi'); break;
						case -2: _e('ND', 'bfi'); break;
					}
				?>
			</td>
			<td class="bfi-col-md-3"><?php _e('Box Auto', 'bfi') ?>:</td>
			<td class="bfi-col-md-3"><?php 
				if(!isset($resource->Garages) && !isset($resource->ParkingPlaces)){
					_e('ND', 'bfi');
				}else{
					if ($resource->Garages>0) {
						echo $resource->Garages;
					}
					if ($resource->Garages>0 && $resource->ParkingPlaces>0) {
						echo " + ";
					}
					if ($resource->ParkingPlaces>0) {
						echo $resource->ParkingPlaces .  __('Parking space', 'bfi');
					}

				}
				?>
			</td>
		</tr>
		<?php if((isset($resource->EnergyClass) && $resource->EnergyClass>0 ) || (isset($resource->EpiValue) && $resource->EpiValue>0 ) ): ?>
		<tr>
			<?php if(isset($resource->EnergyClass) && $resource->EnergyClass>0): ?>
			<td class="bfi-col-md-3"><?php _e('Energy efficiency class', 'bfi') ?>:</td>
			<td class="bfi-col-md-3" <?php if(!isset($resource->EpiValue)) {echo "colspan=\"3\"";}?>>
				<div class="bfi-energyClass bfi-energyClass<?php echo $resource->EnergyClass?>">
				<?php 
					switch ($resource->EnergyClass) {
						case 0: __('not set', 'bfi'); break;
						case 1: __('Nondescript', 'bfi'); break;
						case 2: __('Free property', 'bfi'); break;
						case 3: __('Under evaluation', 'bfi'); break;
						case 100: echo "A+"; break;
						case 101: echo "A"; break;
						case 102: echo "B"; break;
						case 103: echo "C"; break;
						case 104: echo "D"; break;
						case 105: echo "E"; break;
						case 106: echo "F"; break;
						case 107: echo "G"; break;
					}
				?>
				</div>
			</td>
			<?php endif ?>
			<?php if(isset($resource->EpiValue) && $resource->EpiValue>0): ?>
			<td class="bfi-col-md-3"><?php _e('IPE', 'bfi') ?>:</td>
			<td class="bfi-col-md-3" <?php if(!isset($resource->EnergyClass)) {echo "colspan=\"3\"";}?>><?php echo $resource->EpiValue?> <?php echo $resource->EpiUnit?></td>
			<?php endif ?>
		</tr>
		<?php endif ?>
<?php 
if(!empty($resource->Services)){
echo ("<tr>\n");
$i = 0;
foreach($resource->Services as $service) {
?>
			<td class="bfi-col-md-3"><?php echo BFCHelper::getLanguage($service->Name, $language) ?>:</td>
			<td class="bfi-col-md-3"><?php _e('Yes', 'bfi') ?></td>
<?php
    if ($i % 2 === 1) {
        echo("</tr>\n<tr>");
    }
    $i++;
}
echo("</tr>\n");
				} ?>
	</table>

	<?php if(isset($resource->CanCollaborate) && $resource->CanCollaborate) :?>
		<div class="bfi-pull-right cancollaborate"><?php _e('Real estate agency collaboration', 'bfi') ?></div>
		<br />
	<?php endif ?>

	<div class="bfi-clearfix"></div>
	<?php  
	bfi_get_template("merchant_small_details.php",array("resource_id"=>$resource_id,"merchant"=>$merchant,"routeMerchant"=>$routeMerchant));	
	?>

<?php if (($showResourceMap)) :?>
<br /><br />
<div id="resource_map" style="width:100%;height:350px"></div>
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
			if ('<?php echo $isMapMarkerVisible?>' == "1") {
				var marker = new google.maps.Marker({
					position: myLatlng,
					map: mapUnit,
					title: '<?php echo $resourceName?>'
				});
			}
			else {
				var circle = new google.maps.Circle({
					strokeColor: "#FF0000",
					strokeWeight: 2,
					fillOpacity: 0,
					center: myLatlng,
					radius: 300, //in metri (1000m = 1Km)
					map: mapUnit,
					title: '<?php echo $resourceName?>'
				});
			}
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
<?php endif; ?>

<br>
<br>
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

	var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
		showChars: '250'
	};

	jQuery(".bfi-description-data").shorten(shortenOption);

});
//-->
</script>
</div>
