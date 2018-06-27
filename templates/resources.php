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
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );
$uri = $url_resource_page;

//$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
$page = bfi_get_current_page() ;

$pages = 0;
if($total>0){
	$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
}

$listsId = array();

$listNameAnalytics =5;
$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";

?>
<div class="bfi-content">
<div class="bfi-row">
	<div class="bfi-col-xs-9 ">
		<div class="bfi-search-title">
			<?php echo sprintf(__('%s available accommodations', 'bfi'), $total) ?>
		</div>
	</div>	
<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
	<div class="bfi-col-xs-3">
		<div class="bfi-search-view-maps ">
		<span><?php _e('Map view', 'bfi') ?></span>
		</div>	
	</div>	
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
<?php 
	$listResourceIds = array();
	$listResourceMaps = array();
?>  
	<?php foreach ($resources as $currKey => $resource){?>
	<?php 
		
		$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
		if(!empty( $resource->ResName )){
			$resource->Name = $resource->ResName;
		}
		if(!empty( $resource->DefaultImg)){
			$resource->ImageUrl = $resource->DefaultImg;
		}
		if(!empty( $resource->MrcName)){
			$resource->MerchantName = $resource->MrcName;
		}
		
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceDescription = ""; //BFCHelper::getLanguage($resource->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

		$resourceLat = $resource->XPos;
		$resourceLon = $resource->YPos;
		if(isset($resource->ResLat) && !empty($resource->ResLat)  ){
			$resourceLat = $resource->ResLat;
			$resourceLon = $resource->ResLng;
		}
		
		$resource->MinPaxes = $resource->MinCapacityPaxes;
		$resource->MaxPaxes= $resource->MaxCapacityPaxes;
		
		$showResourceMap = (($resourceLat != null) && ($resourceLon !=null));
		
		$currUriresource = $uri.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);
		$resourceRoute = $route = $currUriresource;
		$routeRating = $routeRatingform = $currUriresource.'/'._x('rating', 'Page slug', 'bfi' );
		$routeMerchant = "";
		if($isportal){
			$routeMerchant = $url_merchant_page . $resource->MerchantId .'-'.BFI()->seoUrl($resource->MerchantName);
		}
		$rating = 0;
		$ratingMrc = 0;
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
			$resourceImageUrl = BFCHelper::getImageUrlResized('resources',$resource->ImageUrl, 'medium');
		}
			if(isset($resourceLat) && !empty($resourceLat)  ){
				$val= new StdClass;
				$val->Id = $resource->ResourceId ;
				$val->X = $resourceLat;
				$val->Y = $resourceLon;
				$listResourceMaps[] = $val;
			}

	?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $resourceRoute ?>" style='background: url("<?php echo $resourceImageUrl; ?>") center 25% / cover;' target="_blank"><img src="<?php echo $resourceImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $resource->ResourceId?>" target="_blank"><?php echo  $resource->Name ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
							<?php if($isportal){ ?>
							- <a href="<?php echo $routeMerchant?>" class="bfi-subitem-title" target="_blank"><?php echo $resource->MerchantName; ?></a>
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
							<div class="bfi-hide" id="markerInfo<?php echo $resource->ResourceId?>">
									<div class="bfi-item-title">
										<a href="<?php echo $resourceRoute ?>" target="_blank" ><?php echo  $resource->Name  ?></a> 
										<span class="bfi-item-rating">
											<?php for($i = 0; $i < $rating; $i++) { ?>
												<i class="fa fa-star"></i>
											<?php } ?>	             
										</span>
									</div>
									<span id="mapaddress<?php echo $resource->ResourceId?>"></span>
							</div>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $resource->ResourceId; ?>"></div>
						<div class="bfi-description" id="descr<?php echo $resource->ResourceId?>"><?php echo $resourceDescription ?></div>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-8">
						<?php if ($resource->MaxPaxes>0){?>
							<div class="bfi-icon-paxes">
								<i class="fa fa-user"></i> 
								<?php if ($resource->MaxPaxes==2){?>
								<i class="fa fa-user"></i> 
								<?php }?>
								<?php if ($resource->MaxPaxes>2){?>
									<?php echo ($resource->MinPaxes != $resource->MaxPaxes)? $resource->MinPaxes . "-" : "" ?><?php echo  $resource->MaxPaxes ?>
								<?php }?>
							</div>
						<?php } ?>
					
					</div>
					<div class="bfi-col-sm-4 bfi-text-right">
						<a href="<?php echo $resourceRoute ?>" class="bfi-btn" target="_blank"><?php echo _e('Details' , 'bfi')?></a>
					</div>
				</div>
				<!-- end resource details -->

				<div class="bfi-clearfix"></div>
				<!-- end price details -->
			</div>
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
 $currURL = esc_url( get_permalink() ); 
 $url = $currURL ;

  $pagination_args = array(
    'base'            => $url. '%_%',
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
	</div>
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
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";

var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
		showChars: '150'
};

var mg = [];

var loaded=false;
function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var queryMG = "task=getResourceGroups";
		jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(JSON.parse(data) || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name,bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);
							/*--------getName----*/
							mg[val.TagId] = '<img src="' + $imageurl + '" onerror="this.onerror=null;this.src=\'' + $imageurlError + '\';" alt="' + $name + '" data-toggle="tooltip" title="' + $name + '" />';
						} else {
							if (val.IconSrc != null && val.IconSrc != '') {
								mg[val.TagId] = '<i class="fa ' + val.IconSrc + '" data-toggle="tooltip" title="' + val.Name + '"> </i> ';
							}
						}
					});	
				}
				getlist();
		},'json');
	}
}

function getlist(){
	var query = "resourcesId=" + listToCheck + "&language=<?php echo $language ?>&task=GetResourcesByIds";
	if(listToCheck!='')
	

	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
			var eecitems = [];

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
				eecitems.push({
					id: "" + val.Resource.ResourceId + " - Resource",
					name: val.Resource.Name,
					category: val.Merchant.MainCategoryName,
					brand: val.Merchant.Name,
					position: key
				});
	
				var $indirizzo = "";
				var $cap = "";
				var $comune = "";
				var $provincia = "";
				
				$indirizzo = val.Resource.AddressData;
				$cap = val.Resource.ZipCode;
				$comune = val.Resource.CityName;
				$provincia = val.Resource.RegionName;

				addressData = strAddress.replace("[indirizzo]",$indirizzo);
				addressData = addressData.replace("[cap]",$cap);
				addressData = addressData.replace("[comune]",$comune);
				addressData = addressData.replace("[provincia]",$provincia);
				jQuery("#address"+val.Resource.ResourceId).html(addressData);
				jQuery("#mapaddress"+val.Resource.ResourceId).html(addressData);
				
<?php if($showdata): ?>
				if (val.Resource.Description!= null && val.Resource.Description != ''){
					$html += bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(val.Resource.Description) + "</p>").text());
				}
				jQuery("#descr"+val.Resource.ResourceId).data('jquery.shorten', false);
				jQuery("#descr"+val.Resource.ResourceId).html($html);
				
				jQuery("#descr"+val.Resource.ResourceId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.Resource.ResourceId).shorten(shortenOption);
<?php endif; ?>

				if (val.Resource.TagsIdList!= null && val.Resource.TagsIdList != '')
				{
					var mglist = val.Resource.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.Resource.ResourceId).html($htmlmg);
				}			

				jQuery(".container"+val.Resource.ResourceId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( ".nameAnchor"+val.Resource.ResourceId ).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		<?php if($analyticsEnabled): ?>
		callAnalyticsEEc("addImpression", eecitems, "list");
		<?php endif; ?>
		},'json');
}

		var mapSearch;
		var myLatlngsearch;
		var oms;
		var markersLoading = false;
		var infowindow = null;
		var markersLoaded = false;

		// make map
		function handleApiReadySearch() {
			myLatlngsearch = new google.maps.LatLng(<?php echo $posy ?>, <?php echo $posx ?>);
			var myOptions = {
					zoom: <?php echo $startzoom ?>,
					center: myLatlngsearch,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				}
			mapSearch = new google.maps.Map(document.getElementById("bfi-maps-popup"), myOptions);
			loadMarkers();
		}
		
		function openGoogleMapSearch() {

			if (typeof google !== 'object' || typeof google.maps !== 'object'){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&libraries=drawing,places&callback=handleApiReadySearch";
				document.body.appendChild(script);
			}else{
				if (typeof mapSearch !== 'object' ){
					handleApiReadySearch();
				}
			}
		}

	var bfiCurrMarkerId = 0;

	function loadMarkers() {
		var isVisible = jQuery('#bfi-maps-popup').is(":visible");
		 bookingfor.waitSimpleBlock(jQuery('#bfi-maps-popup'));
		if (mapSearch != null && !markersLoaded && isVisible) {
			if (typeof oms !== 'object'){
				jQuery.getScript("<?php echo BFI()->plugin_url() ?>/assets/js/oms.js", function(data, textStatus, jqxhr) {
					var bounds = new google.maps.LatLngBounds();
					oms = new OverlappingMarkerSpiderfier(mapSearch, {
							keepSpiderfied : true,
							nearbyDistance : 1,
							markersWontHide : true,
							markersWontMove : true 
						});

					oms.addListener('click', function(marker) {
						showMarkerInfo(marker);
					});
					if (!markersLoading) {
						var data = <?php echo json_encode($listResourceMaps) ?>; 
						createMarkers(data, oms, bounds, mapSearch);
						if (oms.getMarkers().length > 0) {
							mapSearch.fitBounds(bounds);
						}
						markersLoaded = true;
						jQuery(jQuery('#bfi-maps-popup')).unblock();
						if(bfiCurrMarkerId>0){
							setTimeout(function() {
								showMarker(bfiCurrMarkerId);
								bfiCurrMarkerId = 0;
								},10);
						}						
						
					}
					markersLoading = true;

				});
			}
		}
	}

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.X == '' || val.Y == '' || val.X == null || val.Y == null)
				return true;
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.X, val.Y),
				map: currentMap
			});
			marker.extId = val.Id;
			oms.addMarker(marker,true);
			bounds.extend(marker.position);
		});
	}

	function showMarker(extId) {
		if(jQuery( "#bfi-maps-popup").length ){
			if(jQuery( "#bfi-maps-popup").hasClass("ui-dialog-content") && jQuery( "#bfi-maps-popup" ).dialog("isOpen" )){
						jQuery( "#bfi-maps-popup" ).dialog("option", "position", {my: "center", at: "center", of: window});
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
							showMarkerInfo(this);
							return false;
						});		
			
			}else{
				jQuery( "#bfi-maps-popup" ).dialog({
					open: function( event, ui ) {
						if(!markersLoaded) {
							bfiCurrMarkerId = extId;
						}
						openGoogleMapSearch();
						if(!markersLoaded) {
							return;
						}
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
							showMarkerInfo(this);
							return false;
						});		
					},
					height: 500,
					width: 800,
					dialogClass: 'bfi-dialog bfi-dialog-map'
				});
			}
		}
	}

	var bfiLastZIndexMarker = 1000;

	function showMarkerInfo(marker) {
		if (infowindow) infowindow.close();
		marker.setZIndex(bfiLastZIndexMarker);
		bfiLastZIndexMarker +=1;
		var data = jQuery("#markerInfo"+marker.extId).html();
//			mapSearch.setZoom(17);
		mapSearch.setCenter(marker.position);
		infowindow = new google.maps.InfoWindow({ content: data });
		infowindow.open(mapSearch, marker);
	}
	
jQuery(document).ready(function() {
	getAjaxInformations();
	if(jQuery( "#bfi-maps-popup").length == 0) {
		jQuery("body").append("<div id='bfi-maps-popup'></div>");
	}
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

<?php } ?>
