<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$currencyclass = bfi_get_currentCurrency();

$isFromSearch = false;
$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;
$fromsearchparam = "?lna=".$listNameAnalytics;

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$onselldetails_page = get_post( bfi_get_page_id( 'onselldetails' ) );
$url_resource_page = get_permalink( $onselldetails_page->ID );
$uri = $url_resource_page;

//$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
$page = bfi_get_current_page() ;

$pages = 0;
if($total>0){
	$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
}

$listsId = array();

$hasSuperior = !empty($merchant->RatingSubValue);
$rating = $merchant->Rating;
if ($rating > 9)
{
	$hasSuperior = ($merchant->Rating%10)>0;
	$rating = (int)($rating / 10);
} 

/*---------------IMPOSTAZIONI SEO----------------------*/
	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = $routeSeo; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/

?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>
<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-xs-9 ">
			<div class="bfi-title-name bfi-hideonextra"><?php echo  $merchant->Name?>
				<span class="bfi-item-rating">
					<?php for($i = 0; $i < $rating ; $i++) { ?>
					  <i class="fa fa-star"></i>
					<?php } ?>
					<?php if ($hasSuperior) { ?>
						&nbsp;S
					<?php } ?>
				</span>
			</div>
			<div class="bfi-search-title">
				<?php echo sprintf(__('%s available accommodations', 'bfi'), $total) ?>
			</div>
		</div>	
	<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
		<!-- <div class="bfi-col-xs-3 ">
			<div class="bfi-search-view-maps ">
			<span><?php _e('Map view', 'bfi') ?></span>
			</div>	
		</div>	 -->
	<?php } ?>
	</div>	
<?php if ($total > 0){ ?>

<div class="bfi-search-menu">
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
	<?php foreach ($resources as $currKey=>$resource){?>
	<?php 
		$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceDescription = BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'bbcode'=>'bbcode', 'striptags'=>'striptags')); 

		$resourceLat = $resource->XPos;
		$resourceLon = $resource->YPos;

		$isMapVisible = $resource->IsMapVisible;
		$isMapMarkerVisible = $resource->IsMapMarkerVisible;
		$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible && $isMapMarkerVisible);

		$currUriresource = $uri.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);		
		$resourceRoute = $currUriresource;
//		$routeRating = $currUriresource.'/'._x('rating', 'Page slug', 'bfi' );
//		$routeInfoRequest = $currUriresource.'/'._x('inforequestpopup', 'Page slug', 'bfi' );
//		$routeRapidView = $currUriresource.'/'._x('rapidview', 'Page slug', 'bfi' );

//		$routeMerchant = "";
//		if($isportal){
//			$routeMerchant = $url_merchant_page . $result->MerchantId .'-'.BFI()->seoUrl($result->MerchantName);
//		}

		$resource->Price = $resource->MinPrice;	
		$rating= 0;	//set 0 so not show 
		$ratingMrc= 0;	//set 0 so not show 
//		$rating = $resource->Rating;
//		if ($rating>9 )
//		{
//			$rating = $rating/10;
//		}
//		$ratingMrc = $resource->MrcRating;
//		if ($ratingMrc>9 )
//		{
//			$ratingMrc = $ratingMrc/10;
//		}
		if(!empty($resource->ImageUrl)){
			$resourceImageUrl = BFCHelper::getImageUrlResized('onsellunits',$resource->ImageUrl, 'medium');
		}
		$resourceNameTrack =  BFCHelper::string_sanitize($resourceName);

?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;' target="_blank" class="eectrack" data-type="Sales Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->ResourceId?>" target="_blank" class="eectrack" data-type="Sales Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><?php echo  $resource->Name ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
							<?php if($isportal){ ?>
							- <a href="<?php echo $routeMerchant.$fromsearchparam?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Merchant" data-id="<?php echo $resource->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><?php echo $resource->MerchantName; ?></a>
							<?php } ?>
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $ratingMrc; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
						</div>
						<div class="bfi-item-address">
							<?php if ($showResourceMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $resource->ResourceId?>)"><span id="address<?php echo $resource->ResourceId?>"></span></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $resource->MerchantId; ?>"></div>
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
						<div class="bfi-description"><?php echo $resourceDescription ?></div>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
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
							<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack" data-type="Sales Resource" data-id="<?php echo $resource->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-list="<?php echo $analyticsListName; ?>"><?php echo _e('Details' , 'bfi')?></a>
					</div>
				</div>
				<div class="bfi-clearfix"></div>
				<!-- end resource details -->
			</div>
				<div  class="bfi-ribbonnew bfi-hide" id="ribbonnew<?php echo $resource->ResourceId?>"><?php _e('New ad', 'bfi') ?></div>
		</div>
	</div>
	<?php 
	$listsId[]= $resource->ResourceId;
	?>
<?php } ?>
</div>
<?php if ($pages > 1 ) { ?>
	<div class="pagination">
<?php   

if( get_option('permalink_structure') ) {
	$format = 'page/%#%/';
} else {
	$format = '?paged=%#%';
}
//$paginationDetails = array();
//$paginationDetails['start'] = $page * COM_BOOKINGFORCONNECTOR_ITEMPERPAGE;

  $pagination_args = array(
    'base'            => $routeMerchant .'/' . _x( 'onsellunits', 'Page slug', 'bfi' ) . '/%_%',
    'format'          => $format,
    'total'           => $pages,
    'current'         => $page,
    'show_all'        => false,
    'end_size'        => 5,
    'mid_size'        => 2,
    'prev_next'       => true,
    'prev_text'       => __('&laquo;'),
    'next_text'       => __('&raquo;'),
    'type'            => 'plain',
    'add_args'        => false,
    'add_fragment'    => ''
  );

  $paginate_links = paginate_links($pagination_args);
    if ($paginate_links) {
      echo "<nav class='bfi-pagination'>";
//      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
      echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
      print $paginate_links;
      echo "</nav>";
    }	 ?>
	</div> <!-- endpagination -->
<?php } ?>

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

var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddressSimple = " ";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

var onsellunitDaysToBeNew = '<?php echo BFCHelper::$onsellunitDaysToBeNew ?>';
var nowDate =  new Date();
var newFromDate =  new Date();
newFromDate.setDate(newFromDate.getDate() - onsellunitDaysToBeNew); 
var listAnonymous = ",<?php echo COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE ?>,";

var shortenOption = {
	moreText: "<?php _e('Read More', 'bfi'); ?>",
	lessText: "<?php _e('Read Less', 'bfi'); ?>",
	showChars: '250'
};


var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>";
			query +="&task=GetResourcesOnSellByIds";

//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
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

	jQuery(".bfi-description").shorten(shortenOption);

});


//-->
</script>

<?php } else { ?>
<div class="bfi-noresults">
	<?php _e('No results available', 'bfi') ?>
</div>
<?php } ?>
	<div class="bfi-clearfix"></div>
	<?php  
	bfi_get_template("merchant_small_details.php",array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant));	
	?>
</div>