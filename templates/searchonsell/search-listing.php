<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$base_url = get_site_url();

$showmap = true;
if($total<1){
	$showmap = false;
}

?>


<script type="text/javascript">
<!--
var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";
var cultureCode = '<?php echo $language ?>';
//-->
</script>
<div id="bfcmerchantlist">
	<div id="com_bookingforconnector-items-container-wrapper">
		<?php if ($total > 0): ?>
			<div class="com_bookingforconnector-items-container">
				<?php include('list-resources.php');?>
			</div>
		<?php else: ?>
			<div class="com_bookingforconnector_search-noresults">
			<?php _e('No result available', 'bfi') ?>
			</div>
		<?php endif; ?>

		<div class="bfi-clearboth"></div>		
	</div>
</div>
<script type="text/javascript">
<!--
	function showResponse(responseText, statusText, xhr, $form)  { 
		jQuery('#bfcmerchantlist').unblock();
		if(typeof getAjaxInformations === 'function' ) {
			getAjaxInformations();
		}
			// reset map
			mapSearch = undefined;
			oms =  undefined;

			// Attach modal behavior to document
			if (typeof(SqueezeBox) !== 'undefined'){
				SqueezeBox.initialize({});
				SqueezeBox.assign($$('#bfcmerchantlist  a.boxed'), { //change the divid (#contentarea) as to the div that you use for refreshing the content
					parse: 'rel'
				});
			}

			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
	}
	function showError(responseText, statusText, xhr, $form)  { 
		jQuery('#bfcmerchantlist').html('<?php echo __('No results available','bfi') ?>')
		jQuery('#bfcmerchantlist').unblock();
	}

//-->
</script>
<?php if ($showmap) {  
$posx = !empty(COM_BOOKINGFORCONNECTOR_GOOGLE_POSX) ? COM_BOOKINGFORCONNECTOR_GOOGLE_POSX : 0;
$posy = !empty(COM_BOOKINGFORCONNECTOR_GOOGLE_POSY) ? COM_BOOKINGFORCONNECTOR_GOOGLE_POSY : 0;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;
	
	?>
<div class="bfi-clearboth"></div>
<div id="bfi-maps-popup"></div>

<script type="text/javascript">
<!--
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
				script.src = "https://maps.google.com/maps/api/js?key=<?php echo $googlemapsapykey ?>&callback=handleApiReadySearch";
				document.body.appendChild(script);
			}else{
				if (typeof mapSearch !== 'object' ){
					handleApiReadySearch();
				}
			}
		}


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
//						jQuery.getJSON(urlCheck + '?' + 'task=searchonselljson&newsearch=0', function(data) {
						var query = "task=searchonselljson&newsearch=0";
						var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
						for(var i = 0; i < hashes.length; i++)
						{
							hash = hashes[i].split('=');
							if(hash[0]!="newsearch"){
								query += "&" + hashes[i];
							}
						}

						jQuery.post(urlCheck, query, function(data) {

								createMarkers(data, oms, bounds, mapSearch);
								if (oms.getMarkers().length > 0) {
									mapSearch.fitBounds(bounds);
								}
								markersLoaded = true;
								jQuery(jQuery('#bfi-maps-popup')).unblock();
						},'json');
					}
					markersLoading = true;
				});
			}
		}
	}

	function toggleMap() {
		jQuery('#mapcontainer').toggle();
		if (jQuery('#mapcontainer').is(":visible")) {
			openGoogleMapSearch();
		}
	}

	function showMap() {
//		jQuery('#mapcontainer').hide();
//		toggleMap();
		jQuery('#maptab').click();
	}

	function showMarker(extId) {
		if(jQuery( "#bfi-maps-popup").length ){
			jQuery( "#bfi-maps-popup" ).dialog({
				open: function( event, ui ) {
					openGoogleMapSearch();
					if(!markersLoaded) {
						setTimeout(function() {showMarker(extId)},500);
						return;
					}
					jQuery(oms.getMarkers()).each(function() {
						if (this.extId != extId) return true; 
//						var offset = jQuery('#bfi-maps-popup').offset();
//						jQuery('html, body').scrollTop(offset.top-20);
						showMarkerInfo(this);
						return false;
					});		
				},
				height: 500,
				width: 800,
			});
		}
	}

	function showMarkerInfo(marker) {
		if (infowindow) infowindow.close();
		jQuery.get(marker.url, function (data) {
			mapSearch.setZoom(17);
			mapSearch.setCenter(marker.position);
			infowindow = new google.maps.InfoWindow({ content: data });
			infowindow.open(mapSearch, marker);
		});		
	}
//-->
</script>
<?php }else{ // showmap ?>
<script type="text/javascript">
<!--
		function openGoogleMapSearch() {}
//-->
</script>

<?php } // showmap ?>