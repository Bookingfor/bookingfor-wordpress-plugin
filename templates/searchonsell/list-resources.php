<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$totalResult = count($results);
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$listsId = array();
$base_url = get_site_url();

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
//$merchantLogoUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s1.jpeg";
//
//$resourceLogoPath = BFCHelper::getImageUrlResized('onsellunits',"[img]", 'medium');
//$resourceLogoPathError = BFCHelper::getImageUrl('onsellunits',"[img]", 'medium');
//
//$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
//$merchantLogoUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
//
//$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
//$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

$searchOnSell_page = get_post( bfi_get_page_id( 'searchonsell' ) );
$formAction = get_permalink( $searchOnSell_page->ID );


$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );

$onselldetails_page = get_post( bfi_get_page_id( 'onselldetails' ) );
$url_resource_page = get_permalink( $onselldetails_page->ID );
$uri = $url_resource_page;

?>
<div class="bfi-content">
<div class="bfi-row">
	<div class="bfi-col-xs-9 ">
		<div class="bfi-search-title">
			<?php echo sprintf( __('Found %s results', 'bfi'),$totalResult ) ?>
		</div>
	</div>	
<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
	<div class="bfi-col-xs-3 ">
		<div class="bfi-search-view-maps ">
		<span><?php _e('Map view', 'bfi') ?></span>
		</div>	
	</div>	
<?php } ?>
</div>	

<div class="bfi-search-menu">
	<form action="<?php echo $formAction; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
			<input type="hidden" class="filterOrder" name="filter_order" value="" />
			<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="" />
			<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
			<input type="hidden" name="limitstart" value="0" />
	</form>
	<div class="bfi-results-sort">
		<span class="bfi-sort-item"><?php echo _e('Order by' , 'bfi')?>:</span>
		<span class="bfi-sort-item <?php echo $currSorting=="price|asc" ? "bfi-sort-item-active": "" ; ?>" rel="price|asc" ><?php echo _e('Lowest price first' , 'bfi'); ?></span>
		<span class="bfi-sort-item <?php echo $currSorting=="created|desc" ? "bfi-sort-item-active": "" ; ?>" rel="created|desc" ><?php echo _e('Latest ads' , 'bfi'); ?></span>
	</div>
	<div class="bfi-view-changer">
		<div class="bfi-view-changer-selected"><?php echo _e('List' , 'bfi') ?></div>
		<div class="bfi-view-changer-content">
			<div id="list-view"><?php echo _e('List' , 'bfi') ?></div>
			<div id="grid-view" class="bfi-view-changer-grid"><?php echo _e('Grid' , 'bfi') ?></div>
		</div>
	</div>
</div>

<div class="bfi-clearfix"></div>
<div id="bfi-list" class="bfi-row bfi-list">
	<?php foreach ($results as $result):?>
		<?php 
		$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

		$resource = $result;
		$resourceName = BFCHelper::getLanguage($result->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		if (!empty($result->OnSellUnitId)){
			$resource->ResourceId = $result->OnSellUnitId;
		}
		$resource->Price = $result->MinPrice;	

		$typeName =  BFCHelper::getLanguage($resource->CategoryName, $language);
		$contractType = ($resource->ContractType) ? __('To rent', 'bfi')  : __('On sale', 'bfi');
		$location = $resource->LocationName;
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

		$addressData = "";
		$resourceLat ="";
		$resourceLon = "";
		if(!empty($resource->XPos)){
			$resourceLat = $resource->XPos;
		}
		if(!empty($resource->YPos)){
			$resourceLon = $resource->YPos;
		}
		$isMapVisible = $resource->IsMapVisible;
		$isMapMarkerVisible = $resource->IsMapMarkerVisible;
		$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible && $isMapMarkerVisible);
		
		$currUriresource = $uri.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);
		
		$resourceRoute = $route = $currUriresource;		
		
		$routeMerchant = "";
		if($isportal){
			$routeMerchant = $url_merchant_page . $result->MerchantId .'-' .BFI()->seoUrl($result->MerchantName);
		}
		

		if(!empty($result->ImageUrl)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('onsellunits',$result->ImageUrl, 'medium');
		}
		$resource->RatingsContext = 0;	//set 0 so not show 
		$resource->MrcName = $resource->MerchantName;
		
		$rating = $resource->Rating;
		if ($rating>9 )
		{
			$rating = $rating/10;
		}
		$ratingMrc= 0;	//set 0 so not show 
//		$ratingMrc = $resource->MrcRating;
//		if ($ratingMrc>9 )
//		{
//			$ratingMrc = $ratingMrc/10;
//		}
	?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;'><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->ResourceId?>" target="_blank"><?php echo  $resourceName?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
							<?php if($isportal) { ?>
								- <a href="<?php echo $routeMerchant?>" class="bfi-subitem-title" target="_blank"><?php echo $resource->MrcName; ?></a>
								<span class="bfi-item-rating">
									<?php for($i = 0; $i < $ratingMrc; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
							<?php } ?>
							
						</div>
						<div class="bfi-item-address">
							<?php if ($showResourceMap):?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $resource->ResourceId?>)"><span id="address<?php echo $resource->ResourceId?>"></span></a>
							<?php endif; ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $resource->ResourceId; ?>"></div>
						<span class="bfi-label-alternative2 bfi-hide" id="showcaseresource<?php echo $resource->ResourceId?>">
							<?php _e('Vetrina', 'bfi') ?> 
							<i class="fa fa-angle-double-up"></i>
						</span>
						<span class="bfi-label-alternative bfi-hide" id="topresource<?php echo $resource->ResourceId?>">
							<?php _e('Top', 'bfi') ?>
							<i class="fa fa-angle-up"></i>
						</span>
						<span class="bfi-label bfi-hide" id="newbuildingresource<?php echo $resource->ResourceId?>">
							<?php _e('New!', 'bfi') ?>
							<i class="fa fa-home"></i>
						</span>

					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-5">
						<?php if (isset($resource->Rooms) && $resource->Rooms>0):?>
						<div class="bfi-icon-rooms">
							<?php echo $resource->Rooms ?> <?php _e('Rooms', 'bfi') ?>
						</div>
						<?php endif; ?>
						<?php if (isset($resource->Rooms) && $resource->Rooms>0 && isset($resource->Area) && $resource->Area>0 ){?>
						- 
						<?php } ?>
						
						<?php if (isset($resource->Area) && $resource->Area>0):?>
						<div class="bfi-icon-area  ">
							<?php echo  $resource->Area ?> <?php _e('m&sup2;', 'bfi') ?>
						</div>
						<?php endif; ?>
					</div>
					<div class="bfi-col-sm-4 bfi-pad0-10 bfi-text-right">
						<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
							<span class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>"> <?php echo BFCHelper::priceFormat($resource->Price,0, ',', '.')?></span>
						<?php else: ?>
							<?php _e('Contact Agent', 'bfi') ?>
						<?php endif; ?>
					
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
							<a href="<?php echo $resourceRoute ?>" class="bfi-btn" target="_blank"><?php echo _e('Details' , 'bfi')?></a>
					</div>
				</div>
				<div class="bfi-clearfix"></div>
				<!-- end resource details -->
				<div  class="ribbonnew bfi-hide" id="ribbonnew<?php echo $resource->ResourceId?>"><?php _e('New ad', 'bfi') ?></div>
		</div>
	</div>
	</div>
		<?php 
		$listsId[]= $result->ResourceId;
		?>
	<?php endforeach; ?>
</div>
</div>
<script type="text/javascript">
<!--
jQuery('#list-view').click(function() {
	jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
	jQuery('#bfi-list').removeClass('bfi-grid-group')
	jQuery('#bfi-list .bfi-item').addClass('bfi-list-group-item')
	jQuery('#bfi-list .bfi-img-container').addClass('bfi-col-sm-3')
	jQuery('#bfi-list .bfi-details-container').addClass('bfi-col-sm-9')

	localStorage.setItem('display', 'list');
});

jQuery('#grid-view').click(function() {
	jQuery('.bfi-view-changer-selected').html(jQuery(this).html());
	jQuery('#bfi-list').addClass('bfi-grid-group')
	jQuery('#bfi-list .bfi-item').removeClass('bfi-list-group-item')
	jQuery('#bfi-list .bfi-img-container').removeClass('bfi-col-sm-3')
	jQuery('#bfi-list .bfi-details-container').removeClass('bfi-col-sm-9')
	localStorage.setItem('display', 'grid');
});
	jQuery('#bfi-list .bfi-item').addClass('bfi-grid-group-item')

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfi_variable === 'undefined' || bfi_variable.bfi_defaultdisplay === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfi_variable.bfi_defaultdisplay == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}

var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddressSimple = " ";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var onsellunitDaysToBeNew = '<?php echo BFCHelper::$onsellunitDaysToBeNew ?>';
var nowDate =  new Date();
var newFromDate =  new Date();
newFromDate.setDate(newFromDate.getDate() - onsellunitDaysToBeNew); 
var listAnonymous = ",<?php echo COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE ?>,";


var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
		showChars: '150'
};

var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		if (cultureCode.length>1)
		{
			cultureCode = cultureCode.substring(0, 2).toLowerCase();
		}
		if (defaultcultureCode.length>1)
		{
			defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
		}
		var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>";
			query +="&task=GetResourcesOnSellByIds";

//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
				jQuery.each(data || [], function(key, val) {

				$html = '';

//				var addressData ="";
//				var arrData = new Array();
//				if (val.IsAddressVisible)
//				{
//					if(val.Address!= null && val.Address!=''){
//						arrData.push(val.Address);
//					}
//				}
//				if(val.LocationZone!= null && val.LocationZone!=''){
//					arrData.push(val.LocationZone);
//				}
//				if(val.LocationName!= null && val.LocationName!=''){
//					arrData.push(val.LocationName);
//				}
//				addressData = arrData.join(" - ");
//				addressData = strAddressSimple + addressData;
//				jQuery("#address"+val.ResourceId).append(addressData);

				var $indirizzo = "";
				var $cap = "";
				var $comune = "";
				var $provincia = "";
				
				if (val.IsAddressVisible)
				{
					$indirizzo = val.Address;
				}	
				$cap = val.ZipCode;
				$comune = val.CityName;
				$provincia = val.RegionName;

				addressData = strAddress.replace("[indirizzo]",$indirizzo);
				addressData = addressData.replace("[cap]",$cap);
				addressData = addressData.replace("[comune]",$comune);
				addressData = addressData.replace("[provincia]",$provincia);
				jQuery("#address"+val.ResourceId).html(addressData);

//					jQuery(".address"+val.ResourceId).html(addressData);
					
				if(val.AddedOn!= null){
					var parsedDate = new Date(parseInt(val.AddedOn.substr(6)));
					var jsDate = new Date(parsedDate); //Date object				
					var isNew = jsDate > newFromDate;
					if (isNew)
						{
							jQuery("#ribbonnew"+val.ResourceId).removeClass("bfi-hide");
						}
				}

				/* highlite seller*/
				if(val.IsHighlight){
							jQuery("#container"+val.ResourceId).addClass("com_bookingforconnector_highlight");
						}

				/*Top seller*/
				if (val.IsForeground)
					{
						jQuery("#topresource"+val.ResourceId).removeClass("bfi-hide");
//						jQuery("#borderimg"+val.ResourceId).addClass("bfi-hide");
					}

				/*Showcase seller*/
				if (val.IsShowcase)
					{
						jQuery("#topresource"+val.ResourceId).addClass("bfi-hide");
						jQuery("#showcaseresource"+val.ResourceId).removeClass("bfi-hide");
						jQuery("#lensimg"+val.ResourceId).removeClass("bfi-hide");
//						jQuery("#borderimg"+val.ResourceId).addClass("bfi-hide");
					}
				
				/*Top seller*/
				if(val.IsNewBuilding){
					jQuery("#newbuildingresource"+val.ResourceId).removeClass("bfi-hide");
				}


					jQuery(".container"+val.ResourceId).click(function(e) {
						var $target = jQuery(e.target);
						if ( $target.is("div")|| $target.is("p")) {
							document.location = jQuery( ".nameAnchor"+val.ResourceId ).attr("href");
						}
					});
			});	
		},'json');
	}
}

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.X == '' || val.Y == '' || val.X == null || val.Y == null)
				return true;
//			console.log(val);
			var url = "<?php echo $url_resource_page; ?>" + val.Id + '-' + val.Name + '/mapspopup';
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.X, val.Y),
				map: currentMap
			});
			marker.url = url;
			marker.extId = val.Id;
			/*
			google.maps.event.addListener(marker, 'click', (function(marker, key) {
				return function() {
				  showMarkerInfo(marker);
//				  infowindow.setContent(marker.position.toString());
//				  infowindow.open(map, marker);
				}
			  })(marker, key));
			*/
			oms.addMarker(marker,true);
					
			bounds.extend(marker.position);
		});
	}


<?php if(count($results)>0): ?>
	
jQuery(document).ready(function() {
	getAjaxInformations();
	jQuery('.bfi-maps-static,.bfi-search-view-maps').click(function() {
		jQuery( "#bfi-maps-popup" ).dialog({
			open: function( event, ui ) {
				openGoogleMapSearch();
			},
			height: 500,
			width: 800,
			dialogClass: 'bfi-dialog bfi-dialog-map'
		});
	});


	jQuery('.bfi-sort-item').click(function() {
		var rel = jQuery(this).attr('rel');
		var vals = rel.split("|"); 
		jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
		jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
		jQuery('#bookingforsearchFilterForm').submit();
	})

});
<?php endif; ?>


//-->
</script>
