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

$showmap = true;
if($total<1){
	$showmap = false;
}

$currParam = BFCHelper::getSearchParamsSession();
$merchantResults = false;
$condominiumsResults = false;
$variationPlanIds = '';
$currencyclass = bfi_get_currentCurrency();
$checkin = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
$checkout = BFCHelper::getStayParam('checkout', new DateTime('UTC'));
$duration = $checkin->diff($checkout)->format('%a');
$paxes = 2;
$paxages = array();
$points =  '';
$merchantCategoryId = '';
$masterTypeId = '';
$availabilitytype = 1;
$itemtypes = '';
$merchantTagIds = '';
$condominiumId = '';
if (!empty($currParam)){
	$merchantResults = isset($currParam['merchantResults']) ? $currParam['merchantResults']: $merchantResults ;
	$condominiumsResults = isset($currParam['condominiumsResults']) ? $currParam['condominiumsResults']: $condominiumsResults ;
	$variationPlanIds = !empty($currParam['variationPlanIds']) ? $currParam['variationPlanIds'] : $variationPlanIds;
	if (!empty($pars['paxes'])) {
		$paxes = $pars['paxes'];
	}
	if (!empty($pars['paxages'])) {
		$paxages = $pars['paxages'];
	}
	$paxes = !empty($pars['paxes']) ? $pars['paxes'] : $paxes ;
	$paxages = !empty($pars['paxages']) ? $pars['paxages'] : $paxages ;
	$points = !empty($pars['points']) ? $pars['points'] : $points ;
	$merchantCategoryId = !empty($pars['merchantCategoryId']) ? $pars['merchantCategoryId'] : $merchantCategoryId ;
	$masterTypeId = !empty($pars['masterTypeId']) ? $pars['masterTypeId'] : $masterTypeId ;
	$availabilitytype = isset($pars['availabilitytype']) ? $pars['availabilitytype'] : $availabilitytype ;
	$itemtypes = !empty($pars['itemtypes']) ? $pars['itemtypes'] : $itemtypes ;
	$merchantTagIds = !empty($pars['merchantTagIds']) ? $pars['merchantTagIds'] : $merchantTagIds ;
}
if (empty($paxages)){
	$paxes = 2;
	$paxages = array(BFCHelper::$defaultAdultsAge, BFCHelper::$defaultAdultsAge);
}
if(empty( $condominiumId )){
	$condominiumId = BFCHelper::getVar('condominiumId','');
}

?>
	<?php 
	if (!empty($variationPlanIds )) {
	    
	$offers = json_decode(BFCHelper::getDiscountDetails($variationPlanIds,$language));
	
	foreach ($offers as $offer ) {
		if (!empty($offer)){ ?>
		<div class="bfi-content">
			<div class="bfi-title-name"><h1><?php echo  $offer->Name?></h1> </div>
			<?php if (!empty($offer->Description)) {?>
			<div class="bfi-description">
					<?php echo BFCHelper::getLanguage($offer->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')); ?>
			</div>
			<?php } ?>
		</div>
		<div class="bfi-clearfix "></div>
		<?php 
			} 
		}
	}
	?>
<div id="bfi-merchantlist"> 
	<?php if ($total > 0){ ?>
	<?php 
		if($merchantResults) {
			$merchants = $items ;
			bfi_get_template("search/default_merchants.php",array("merchants"=>$merchants,"totalAvailable"=>$totalAvailable,"total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"listNameAnalytics"=>$listNameAnalytics,"totPerson"=>$totPerson,"currSorting"=>$currSorting));	
//			include('default_merchants.php');
		}
		elseif ($condominiumsResults) {
			$merchants = $items ;
			bfi_get_template("search/default_condominiums.php",array("merchants"=>$merchants,"totalAvailable"=>$totalAvailable,"total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"listNameAnalytics"=>$listNameAnalytics,"totPerson"=>$totPerson,"currSorting"=>$currSorting));	
//			include('default_condominiums.php');
		}
		else {
			$results = $items ;
			bfi_get_template("search/default_resources.php",array("results"=>$results,"totalAvailable"=>$totalAvailable,"total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"listNameAnalytics"=>$listNameAnalytics,"totPerson"=>$totPerson,"currSorting"=>$currSorting));	
//			include('default_resources.php');
		}
		?>
		<?php
		if( get_option('permalink_structure') ) {
			$format = 'page/%#%/';
		} else {
			$format = '?paged=%#%';
		}
			$url = esc_url( get_permalink() ); 
		  $pagination_args = array(
			'base'            => $url. '%_%',
			'format'          => $format, //'?page=%#%',
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



		global $bfi_query_arg;
		$bfi_query_arg = array();
		add_filter( 'get_pagenum_link', 'bfi_remove_date_get_pagenum_link',1 );
		function bfi_remove_date_get_pagenum_link( $url ) {
			global $bfi_query_arg;
			$checkinFromUrl = filter_input( INPUT_GET, 'checkin');
			if(!empty($checkinFromUrl)){
				$bfi_query_arg['checkin'] = rawurlencode($checkinFromUrl);
				$url = remove_query_arg( 'checkin', $url );
			}
			$checkoutFromUrl = filter_input( INPUT_GET, 'checkout');
			if(!empty($checkoutFromUrl)){
				$bfi_query_arg['checkout'] = rawurlencode($checkoutFromUrl);
				$url = remove_query_arg( 'checkout', $url );
			}
			
			return  $url;
		}

		add_filter( 'paginate_links', function( $link )
		{
			global $bfi_query_arg;
			$filter_order = filter_input( INPUT_POST, 'filter_order');
			if(!empty($filter_order)){
				$link = remove_query_arg( 'filter_order', $link );
				$link = add_query_arg('filterorder' , $filter_order , $link);
			}else{
				$filter_order = filter_input( INPUT_GET, 'filter_order');
				if(!empty($filter_order)){
					$link = remove_query_arg( 'filter_order', $link );
					$link = add_query_arg('filterorder' , $filter_order , $link);
				}
			}
			$filter_order_dir = filter_input( INPUT_POST, 'filter_order_Dir');
			if(!empty($filter_order_dir)){
				$link = remove_query_arg( 'filter_order_Dir', $link );
				$link = add_query_arg('filterorderdir' , $filter_order_dir , $link);
			}else{
				$filter_order_dir = filter_input( INPUT_GET, 'filter_order_Dir');
				if(!empty($filter_order_dir)){
					$link = remove_query_arg( 'filter_order_Dir', $link );
					$link = add_query_arg('filterorderdir' , $filter_order_dir , $link);
				}
			}

			if(!empty($bfi_query_arg)){
				$link =  add_query_arg($bfi_query_arg , $link);
			}

			$link = filter_input( INPUT_GET, 'newsearch' ) ? remove_query_arg( 'newsearch', $link ) : $link;

			$link = str_replace('filterorder=',"filter_order=",$link);
			$link = str_replace('filterorderdir=',"filter_order_Dir=",$link);

			return $link;
		} );

		  $paginate_links = paginate_links($pagination_args);
			if ($paginate_links) {
			  echo "<nav class='bfi-pagination'>";
		//      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
			  echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
			  print $paginate_links;
			  echo "</nav>";
			}
			 ?>
	<?php }else{ ?>
<div class="bfi-content">
		<div class="bfi-noresults">
		<?php _e('No result available', 'bfi') ?>
				<?php 
				if($isportal ){
//					$merchantCategories = get_post_meta($post->ID, 'merchantcategories', true);
//					$rating = get_post_meta($post->ID, 'rating', true);
//					$cityids = get_post_meta($post->ID, 'cityids', true);
//					$currURL = esc_url( get_permalink() ); 
//
$page = bfi_get_current_page() ;
$start = ($page - 1) * COM_BOOKINGFORCONNECTOR_ITEMPERPAGE;			

if (empty($GLOBALS['bfSearchedMerchants'])) {
					
					$model = new BookingForConnectorModelMerchants;
					$model->populateState();	
					$model->setItemPerPage(COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);

					$filter_order = $model->getOrdering();
					$filter_order_Dir = $model->getDirection();

					$currParam = $model->getParam();
					$pars = BFCHelper::getSearchParamsSession();

					$merchantCategories = array();
					$tmpMerchantCategories = array();

					if(!empty($pars) && isset($pars{"merchantCategoryId"})){
						if(is_array($pars{"merchantCategoryId"})){
							$tmpMerchantCategories =  $pars{"merchantCategoryId"};
						}else{
							array_push($tmpMerchantCategories,$pars{"merchantCategoryId"});
						}						
					}
										
					foreach($tmpMerchantCategories as $merchantCategory){
						if (strpos($merchantCategory, '|') !== false) {
							$aMerchantCategory = explode('|',$merchantCategory);
							
							array_push($merchantCategories,$aMerchantCategory[1]);
						}else{
							array_push($merchantCategories,$merchantCategory);
						}
					}

					$currParam['categoryId'] = !empty($merchantCategories)?$merchantCategories:[];
					$currParam['rating'] = !empty($rating)?$rating:'';
					$currParam['cityids'] = !empty($cityids)?$cityids:[];
					$model->setParam($currParam);
						
					$items = $model->getItems(false, false, $start,COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
					$total = $model->getTotal();

					$GLOBALS['bfSearchedMerchantsItems'] = $items;
					$GLOBALS['bfSearchedMerchantsItemsTotal'] = $total;
					$GLOBALS['bfSearchedMerchantsItemsCurrSorting'] = $currSorting;
					$GLOBALS['bfSearchedMerchants'] = 1;
}else{
					$items = $GLOBALS['bfSearchedMerchantsItems'];
					$total = $GLOBALS['bfSearchedMerchantsItemsTotal'];
					$currSorting = $GLOBALS['bfSearchedMerchantsItemsCurrSorting'];
}
										
					$merchants = is_array($items) ? $items : array();
					$analiticsSubject = "'No Results Merchant List'";
					$nopopupmap=true;
					if (count((array)$merchants)>0) {
					?>
										<div class="bfi-content">
											<div class="bfi-check-more" data-type="merchant" data-id="<?php echo $merchants[0]->MerchantId?>" >
												<?php _e('Limited availability, but may sell out:', 'bfi') ?>
												<div class="bfi-check-more-slider">
												</div>
											</div>
										</div>

					<?php 
					}
//					include(BFI()->plugin_path().'/templates/merchantslist/merchantslist.php'); // merchant template
					bfi_get_template("merchantslist/merchantslist.php",array("merchants"=>$merchants,"total"=>$total,"items"=>$items,"pages"=>$pages,"page"=>$page,"currencyclass"=>$currencyclass,"analiticsSubject"=>$analiticsSubject,"nopopupmap"=>$nopopupmap,"filter_order"=>$filter_order,"filter_order_Dir"=>$filter_order_Dir ));	
				}else{
					$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
					$url_merchant_page = get_permalink( $merchantdetails_page->ID );
					$merchants = BFCHelper::getMerchantsSearch(null,0,1,null,null);
					foreach ($merchants as $merchant){
						$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
						$routeRating = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
						$routeRating = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
						$routeInfoRequest = $routeMerchant .'/'._x('contactspopup', 'Page slug', 'bfi' );
?>
							<a class="boxedpopup bfi-btn" href="<?php echo $routeInfoRequest?>" style="width: 100%;"><?php echo  _e('Request info' , 'bfi') ?></a>

<?php 
					}
				
				}
				?>
		</div>
</div>

<?php } ?>
	<div class="bfi-clearfix"></div>		
</div>
<script type="text/javascript">
<!--
var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
		showChars: '250'
};
jQuery(document).ready(function() {
	if(typeof jQuery.fn.button.noConflict !== 'undefined'){
		var btn = jQuery.fn.button.noConflict(); // reverts $.fn.button to jqueryui btn
		jQuery.fn.btn = btn; // assigns bootstrap button functionality to $.fn.btn
	}
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
	jQuery(".bfi-description").shorten(shortenOption);
	
	bfiCheckOtherAvailability();
});

	function showResponse(responseText, statusText, xhr, $form)  { 
		jQuery('#bfi-merchantlist').unblock();
		if(typeof getAjaxInformations === 'function' ) {
			getAjaxInformations();
		}
			// reset map
			mapSearch = undefined;
			oms =  undefined;

			// Attach modal behavior to document
			if (typeof(SqueezeBox) !== 'undefined'){
				SqueezeBox.initialize({});
				SqueezeBox.assign($$('#bfi-merchantlist  a.boxed'), { //change the divid (#contentarea) as to the div that you use for refreshing the content
					parse: 'rel'
				});
			}

			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
	}
	function showError(responseText, statusText, xhr, $form)  { 
		jQuery('#bfi-merchantlist').html('<?php echo __('No results available','bfi') ?>')
		jQuery('#bfi-merchantlist').unblock();
	}
	function bfiCheckOtherAvailability() {
		jQuery(".bfi-check-more").each(function(){
			var currType = jQuery(this).attr("data-type") || "";
			var currId = jQuery(this).attr("data-id");
			var currdata = {
				checkin: '<?php echo $checkin->format('Ymd'); ?>',
				duration: <?php echo $duration ?>,
				paxes:  <?php echo $paxes ?>,
				paxages:  '<?php echo implode('|',$paxages) ?>',
//				merchantId:  currId,
				condominiumId:  '<?php echo $condominiumId ?>',
				//resourceId:  currResourceId,
				cultureCode:  bfi_variable.bfi_cultureCode,
				points:  '<?php echo $points ?>',
				//	userid:  bookingfor.getUrlParameter("userid"),
				//tagids:  currTag,
				//merchantsList:  '',
				availabilityTypes:  '<?php echo $availabilitytype ?>',
				itemTypeIds:  '<?php echo $itemtypes ?>',
				//	domainLabel:  bookingfor.getUrlParameter("availabilitytype"),
				merchantCategoryIds:  '<?php echo $merchantCategoryId ?>',
				masterTypeIds:  '<?php echo $masterTypeId ?>',
				merchantTagsIds:  '<?php echo $merchantTagIds ?>',
				task:'GetAlternativeDates'
				};
				if(currType=="merchant"){
					currdata.merchantId = currId;
				}
				if(currType=="resource"){
					currdata.resourceId = currId;
				}
//			var query = "merchantsId=" + currMerchant + "&language=<?php echo $language ?>&task=GetOtherAvailability&checkin=<?php echo $checkin->format('dmY'); ?>";
			var currThis = this;
			bookingfor.waitSimpleWhiteBlock(jQuery(currThis));
			jQuery.post(bfi_variable.bfi_urlCheck, currdata, function(data) {
				jQuery(currThis).unblock();
				var currSlider = jQuery(currThis).find(".bfi-check-more-slider").first();
				var initialSlide = 0;
				var totalAlt = 0;
				var tt = JSON.parse(data);
				jQuery.each(JSON.parse(data) || [], function(key, val) {
					totalAlt = key;
					var price = val.BestValue ;
					var minstay = val.Duration ;
					if (initialSlide ==0 && val.Duration  == <?php echo $duration ?>)
					{
						initialSlide = key;
					}
					var checkinDate =  bookingfor.parseODataDate(val.StartDate);
					var checkoutDate = bookingfor.parseODataDate(val.EndDate);
					
					month1 = bookingfor.pad((checkinDate.getMonth() + 1),2);
					month2 = bookingfor.pad((checkoutDate.getMonth() + 1),2);
					day1 = bookingfor.pad((checkinDate.getDate()),2);
					day2 = bookingfor.pad((checkoutDate.getDate()),2);
					day1week = bookingfor.pad((checkinDate.getDay()),2);
					day2week = bookingfor.pad((checkoutDate.getDay()),2);
					if (typeof Intl == 'object' && typeof Intl.NumberFormat == 'function') {
						month1 = checkinDate.toLocaleString(localeSetting, { month: "short" });              
						month2 = checkoutDate.toLocaleString(localeSetting, { month: "short" });            
						day1week = checkinDate.toLocaleString(localeSetting, { weekday: "short" });   
						day2week = checkoutDate.toLocaleString(localeSetting, { weekday: "short" }); 
					}

					var currSearchUrl = window.location.href; 
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"checkin",bookingfor.convertDateToIta(checkinDate));
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"checkout",bookingfor.convertDateToIta(checkoutDate));
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"limitstart",0);
					currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"start",0);
//					if(currType=="merchant"){
//						currSearchUrl = bookingfor.updateQueryStringParameter(currSearchUrl,"filter_merchantid","merchantid:" + currId);
//					}
//					console.log(currSearchUrl);
					var curravailabilitytype = bookingfor.getUrlParameter("availabilitytype");
					var strSummaryDays = '<span class="bfi-check-more-los">'+minstay+' <?php echo strtolower (__('Nights', 'bfi')) ?>, '+day1week+' – '+day2week+'</span>'; 
					if (curravailabilitytype == "0") {
						minstay += 1;
						strSummaryDays ='<span class="bfi-check-more-los">'+minstay+' <?php echo strtolower (__('Days', 'bfi')) ?>, '+day1week+' – '+day2week+'</span>'; 
					}
					if(days<1){strSummaryDays ="";}

					currSlider.append('<a href="'+currSearchUrl+'"><span class="bfi-check-more-dates">'+day1+' '+month1+' - '+day2+' '+month2+'</span>'+strSummaryDays+'<span class="bfi-check-more-price"><?php _e('From', 'bfi') ?> <span class="bfi_<?php echo $currencyclass ?>">'+bookingfor.priceFormat(price, 2, ',', '.')+'</span></span></a>');
				});	
				var currSliderWidth = jQuery(currThis).width()-80;
				jQuery(currSlider).width(currSliderWidth);
				var ncolslick = Math.round(currSliderWidth/120);
				if (totalAlt>0)
				{
					jQuery(currSlider).slick({
						dots: false,
						draggable: false,
						arrows: true,
						initialSlide: initialSlide,
						infinite: false,
						slidesToShow: ncolslick,
						slidesToScroll: ncolslick,
					});
				}
				
			});

		});

	}
//-->
</script>
<?php if ($showmap) {  
$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;
	
?>
<div class="bfi-clearfix"></div>
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
			if (typeof MarkerWithLabel !== 'function' ){
				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = "<?php echo BFI()->plugin_url() ?>/assets/js/markerwithlabel.js";
				document.body.appendChild(script);
			}

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
			if(jQuery( "#bfi-maps-popup").length == 0) {
				jQuery("body").append("<div id='bfi-maps-popup'></div>");
			}
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
						var query = "task=searchjson&newsearch=0";
						var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
						for(var i = 0; i < hashes.length; i++)
						{
							hash = hashes[i].split('=');
							if(hash[0]!="newsearch"){
								query += "&" + hashes[i];
							}
						}

						jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

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

						},'json');
					}
					markersLoading = true;

				});
			}
		}
	}


	function showMarker(extId) {
		if(jQuery( "#bfi-maps-popup").length ){
			if(jQuery( "#bfi-maps-popup").hasClass("ui-dialog-content") && jQuery( "#bfi-maps-popup" ).dialog("isOpen" )){
						jQuery( "#bfi-maps-popup" ).dialog("option", "position", {my: "center", at: "center", of: window});
						jQuery(oms.getMarkers()).each(function() {
							if (this.extId != extId) return true; 
	//						var offset = jQuery('#bfi-maps-popup').offset();
	//						jQuery('html, body').scrollTop(offset.top-20);
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
	//						setTimeout(function() {showMarker(extId)},500);
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
		jQuery.get(marker.url, function (data) {
			mapSearch.setZoom(17);
			mapSearch.setCenter(marker.position);
			infowindow = new google.maps.InfoWindow({ content: data });
			infowindow.open(mapSearch, marker);
		});		
	}
//-->
</script>
<?php }else{ // showmap 
	if ($total > 0){ 
?>
<script type="text/javascript">
<!--
		function openGoogleMapSearch() {}
//-->
</script>

<?php
	}
} // showmap ?>