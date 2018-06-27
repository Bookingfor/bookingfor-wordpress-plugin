<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_single() && get_post_type() == 'merchantlist' ) {

$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

$currencyclass = bfi_get_currentCurrency();

//$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
//$formAction = get_permalink( $searchAvailability_page->ID );

$paymodes_text = array('freecancellation' => __('Free cancellation', 'bfi'),
						'freepayment' => __('No prepayment', 'bfi'),   
						'freecc' => __('Book without credit card', 'bfi')                                 
					);
$meals_text = array('ai' => __('All inclusive', 'bfi'),
						'fb' => __('Full board', 'bfi'),
						'hb' => __('Half board', 'bfi'),   
						'bb' => __('Breakfast included', 'bfi')                                 
					);
$rating_text = array('null' => __('Unrated', 'bfi'),
						'0' => __('Unrated', 'bfi'),
						'1' => __('1 star', 'bfi'),   
						'2' => __('2 stars', 'bfi'),
						'3' => __('3 stars', 'bfi'),
						'4' => __('4 stars', 'bfi'),
						'5' => __('5 stars', 'bfi'),  
						'6' => __('6 stars', 'bfi'),
						'7' => __('7 stars', 'bfi'),  
						'8' => __('8 stars', 'bfi'), 
						'9' => __('9 stars', 'bfi'),  
						'10' => __('10 stars', 'bfi'),                                 
					);
$avg_text = array('-1' => __('Unrated', 'bfi'),
						'0' => __('Very poor', 'bfi'),
						'1' => __('Poor', 'bfi'),   
						'2' => __('Disappointing', 'bfi'),
						'3' => __('Fair', 'bfi'),
						'4' => __('Okay', 'bfi'),
						'5' => __('Pleasant', 'bfi'),  
						'6' => __('Good', 'bfi'),
						'7' => __('Very good', 'bfi'),  
						'8' => __('Fabulous', 'bfi'), 
						'9' => __('Exceptional', 'bfi'),  
						'10' => __('Exceptional', 'bfi'),                                 
					);


$formAction = (isset($_SERVER['HTTPS']) ? "https" : "http") . ':' ."//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

$page = bfi_get_current_page() ;
if(!empty($page)){
	$formAction = str_replace('/page/'.$page."/","/",$formAction);
}

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

						
					$total = $model->getTotal();
					$items = $model->getItems();

					$GLOBALS['bfSearchedMerchantsItems'] = $items;
					$GLOBALS['bfSearchedMerchantsItemsTotal'] = $total;
					$GLOBALS['bfSearchedMerchantsItemsCurrSorting'] = $currSorting;
					$GLOBALS['bfSearchedMerchants'] = 1;

}


$formAction = filter_input( INPUT_GET, 'newsearch' )
       ? remove_query_arg( 'newsearch', $formAction )
       : $formAction;

$pars = BFCHelper::getSearchMerchantParamsSession();

$merchantCategoryId = isset($pars['merchantCategoryId']) ? $pars['merchantCategoryId'] : '';

$searchid = isset($_GET['searchid']) ? $_GET['searchid'] : '';

$filtersMerchantsServices = array();
$filtersMerchantsServices = array();
$filtersMerchantsTags = array();
$filtersMerchantsZones = array();
$filtersMerchantsRating = array();
$filtersMerchantsAvg = array();
$filtersMerchantsCategories = array();

$filterscount = BFCHelper::getEnabledFilterSearchMerchantParamsSession();
$firstFilters = BFCHelper::getFirstFilterSearchMerchantParamsSession();

if (!empty($firstFilters) ) {
	foreach ($firstFilters as $filter){
		switch ($filter->Name) {
			case 'mrcservices':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsServices[$item->Id] = $item;
					}
				}
				break; 
			case 'mrctags':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsTags[$item->Id] = $item;
					}
				}
				break; 
			case 'mrccategory':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsCategories[$item->Id] = $item;
					}
				}
				break; 
			case 'mrczones':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsZones[$item->Id] = $item;
					}
				}
				break; 
			case 'mrcrating':
				if(!empty( $filter->Items )){
					$allItems = $filter->Items;
					usort($allItems, function($a, $b)
					{
						return strcmp($b->Id,$a->Id);
					});
					foreach ($allItems as $item ) {
					   $filtersMerchantsRating[$item->Id] = $item;
					}
				}
				break; 
			case 'mrcavg':
				if(isset( $filter->Items )){
					$allItems = $filter->Items;
					usort($allItems, function($a, $b)
					{
						return strcmp($b->Id,$a->Id);
					});
					foreach ($allItems as $item ) {
					   $filtersMerchantsAvg[$item->Id] = $item;
					}
				}
				break; 
		} 
	}
}

$filtersMerchantsServicesCount = array();
$filtersMerchantsTagsCount = array();
$filtersMerchantsZonesCount = array();
$filtersMerchantsRatingCount = array();
$filtersMerchantsAvgCount = array();
$filtersMerchantsCategoriesCount = array();

if(!empty( $filterscount )){
	foreach ($filterscount as $filter){
		switch ($filter->Name) {
			case 'mrcservices':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsServicesCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrctags':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsTagsCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrccategory':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsCategoriesCount[$item->Id] = $item->Count;
					}
				}
				break; 				
			case 'mrczones':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsZonesCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrcrating':
				if(!empty( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsRatingCount[$item->Id] = $item->Count;
					}
				}
				break; 
			case 'mrcavg':
				if(isset( $filter->Items )){
					foreach ($filter->Items as $item ) {
					   $filtersMerchantsAvgCount[$item->Id] = $item->Count;
					}
				}
				break; 
		} 
	}	
}


$filtersSelected = BFCHelper::getFilterSearchMerchantParamsSession();

$filtersRatingValue = "";
$filtersAvgValue = "";
$filtersMerchantsServicesValue = "";
$filtersZonesValue = "";
$filtersTagsValue = "";
$filtersCategoriesValue = "";

if (isset($filtersSelected)) {
	$filtersRatingValue = isset( $filtersSelected[ 'rating' ] ) ? $filtersSelected[ 'rating' ] : "";
	$filtersAvgValue =  isset( $filtersSelected[ 'avg' ] ) ? $filtersSelected[ 'avg' ] : "";
	$filtersMerchantsServicesValue = ! empty( $filtersSelected[ 'merchantsservices' ] ) ? $filtersSelected[ 'merchantsservices' ] : "";
	$filtersZonesValue = ! empty( $filtersSelected[ 'zones' ] ) ? $filtersSelected[ 'zones' ] : "";
	$filtersTagsValue = ! empty( $filtersSelected[ 'tags' ] ) ? $filtersSelected[ 'tags' ] : "";
	$filtersCategoriesValue = ! empty( $filtersSelected[ 'merchantscategories' ] ) ? $filtersSelected[ 'merchantscategories' ] : "";
}

$filtersRating = array();
$filtersAvg = array();
$filtersZones = array();
$filtersTags = array();
$filtersCategories = array();
$filtersRatingCount = array();
$filtersAvgCount = array();
$filtersZonesCount = array();
$filtersTagsCount = array();
$filtersCategoriesCount = array();


	$filtersRating = $filtersMerchantsRating;
	$filtersAvg = $filtersMerchantsAvg;
	$filtersZones = $filtersMerchantsZones;
	$filtersTags = $filtersMerchantsTags;
	$filtersCategories = $filtersMerchantsCategories;
	$filtersRatingCount = $filtersMerchantsRatingCount;
	$filtersAvgCount = $filtersMerchantsAvgCount;
	$filtersZonesCount = $filtersMerchantsZonesCount;
	$filtersTagsCount = $filtersMerchantsTagsCount;
	$filtersCategoriesCount = $filtersMerchantsCategoriesCount;
$minvaluetoshow=1;
echo $before_widget;
?>
<div class="bfi-searchfilter">
<h3><?php _e('Filter by', 'bfi'); ?></h3>
<form action="<?php echo $formAction; ?>" method="post" id="searchMerchantformfilter" name="searchMerchantformfilter" >
	<input type="hidden" value="<?php echo $searchid ?>" name="searchid">
	<input type="hidden" value="0" name="limitstart">
	<input type="hidden" value="0" name="newsearch">
	<input type="hidden" name="filter_order" class="filterOrder" id="filter_order_filter" value="stay">
	<input type="hidden"  name="filter_order_Dir" class= "filterOrderDirection"id="filter_order_Dir_filter" value="asc">
<div id="bfi-filtertoggleMerchant">
	<?php if (isset($filtersCategories) &&  is_array($filtersCategories) && count($filtersCategories)>0) { 
	$filtersValueArr = explode ("|",$filtersCategoriesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Tipology', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersCategories as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="merchantscategories" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersCategoriesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersRating) &&  is_array($filtersRating) && count($filtersRating)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersRatingValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Star rating', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersRating as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="rating" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $rating_text[$item->Name] ?> </span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersRatingCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersAvg) &&  is_array($filtersAvg) && count($filtersAvg)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersAvgValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Review score', 'bfi') ?> </div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersAvg as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="avg" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $avg_text[$item->Name] ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersAvgCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersMerchantsServices) &&  is_array($filtersMerchantsServices) && count($filtersMerchantsServices)>$minvaluetoshow) { 
	$filtersValueArr = explode ("|",$filtersMerchantsServicesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Property facility', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersMerchantsServices as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="merchantsservices" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersMerchantsServicesCount, $itemId)  ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersZones) &&  is_array($filtersZones) && count($filtersZones)>$minvaluetoshow)  { 
	$filtersValueArr = explode ("|",$filtersZonesValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Destination', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersZones as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="zones" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersZonesCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if (isset($filtersTags) &&  is_array($filtersTags) && count($filtersTags)>0) { 
	$filtersValueArr = explode ("|",$filtersTagsValue);
	?>
		<div>
			<div class="bfi-option-title bfi-option-active"><?php _e('Tags', 'bfi') ?></div>
			<div class="bfi-filteroptions">
				<?php foreach ($filtersTags as $itemId => $item){?>
					<a href="javascript:void(0);" rel="<?php echo $itemId ?>" rel1="tags" class="<?php echo (in_array(strval($itemId), $filtersValueArr, true))?"bfi-filter-active":""; ?>">
					<span class="bfi-filter-label"><?php echo $item->Name ?></span>
					<span class="bfi-filter-count"><?php echo BFCHelper::bfi_returnFilterCount($item->Count, $filtersTagsCount, $itemId) ?></span>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php } ?>	

</div>
<div class="bfi-clearfix"></div>
	<input type="hidden" name="filters[rating]" id="filtersRatingsHidden" value="<?php echo $filtersRatingValue ?>" />
	<input type="hidden" name="filters[avg]" id="filtersAvgHidden" value="<?php echo $filtersAvgValue ?>" />
	<input type="hidden" name="filters[merchantsservices]" id="filtersMerchantsServicesHidden" value="<?php echo $filtersMerchantsServicesValue ?>" />
	<input type="hidden" name="filters[zones]" id="filtersZonesHidden" value="<?php echo $filtersZonesValue ?>" />
	<input type="hidden" name="filters[tags]" id="filtersTagsHidden" value="<?php echo $filtersTagsValue ?>" />
	<input type="hidden" name="filters[merchantscategories]" id="filtersMerchantsCategoriesHidden" value="<?php echo $filtersCategoriesValue ?>" />
</form>
</div>
<?php if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY)){ ?>
<div class="bfi-maps-static">
	<span class="bfi-showmap"><?php _e('Show map', 'bfi') ?></span>
	<img alt="Map" src="https://maps.google.com/maps/api/staticmap?center=<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSY?>,<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_POSX?>&amp;zoom=11&amp;size=400x250&key=<?php echo COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY ?>&" style="max-width: 100%;" />
</div>
<?php } ?>

<script type="text/javascript">
function bfi_applyfilterMerchantdata(){ 		

	jQuery("#bfi-filtertoggleMerchant .bfi-option-title ").click(function(){
		jQuery(this).toggleClass("bfi-option-active");
		jQuery(this).next("div").stop('true','true').slideToggle("normal",function() {
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
		});
	});

	jQuery('.bfi-filter-label').bind('mouseenter', function(){
		var $this = jQuery(this);
		var divWidthBefore = $this.width();
		$this.css('width','auto');
		$this.css('white-space','nowrap');
		var divWidth = $this.width();
		$this.width(divWidthBefore+1);
		$this.css('white-space','normal');
		if(divWidthBefore< divWidth && !$this.attr('title') ){
			$this.attr('title', $this.text());
			$this.tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
			});
			$this.tooltip("open");
		}
	});
	
	jQuery('#searchMerchantformfilter').submit(function( event ) {
				try
				{
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
				jQuery("input[name^='filters\\[']").val("");

				jQuery("a.bfi-filter-active").each(function(){
					currValue = jQuery(this).attr("rel");
					currHiddenInput = jQuery("input[name='filters\\["+jQuery(this).attr("rel1")+"\\]']");
					currHiddenInput.val(currHiddenInput.val() + "|" + currValue);
				});
				jQuery("input[name^='filters\\[']").each(function(){
					jQuery(this).val(jQuery(this).val().substr(1));
				});
				jQuery('body').block({
					message:"",
						overlayCSS: {backgroundColor: '#ffffff', opacity: 0.7}  
				});
		});

			jQuery('.bfi-filteroptions a').on('click',function() {
<?php 
if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1){
?>
//				currValue = jQuery(this).attr("rel");
				currValue = jQuery(this).find(".bfi-filter-label").first().text(); 
				listname = jQuery(this).attr("rel1");
				currAction = jQuery(this).hasClass("bfi-filter-active")? "Remove":"Add";
				callAnalyticsEEc("", "", listname + "|" + currValue, null, currAction, "Search Filters");
<?php 
}
?>
				jQuery(this).toggleClass("bfi-filter-active");
				jQuery(this).closest('form').submit();
			});
	
			if (jQuery.prototype.masonry){
				jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
			}
}

jQuery(document).ready(function() {
	bfi_applyfilterMerchantdata();
});  
</script>
<?php echo $after_widget; ?>
<div class="bfi-clearfix"></div>
<?php } ?>
