<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$checkin = BFCHelper::getStayParam('checkin', new DateTime());
$totalResult = count($merchants);
$maxItemsView = 3;
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$listsId = array();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$img = BFI()->plugin_url() . "/assets/images/default.png";
$imgError = BFI()->plugin_url() . "/assets/images/default.png";

$merchantLogo = BFI()->plugin_url() . "/assets/images/defaults/default-s1.jpeg";
$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'medium');


$base_url = get_site_url();
$onlystay = true ;
if(!empty($_SESSION['search.params']['onlystay'])){
	$onlystay =  $_SESSION['search.params']['onlystay'] === 'false'? false: true;
}

// get searchresult page...
$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
$formAction = get_permalink( $searchAvailability_page->ID );


$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );

?>
<div class="searchmerchantstilte">
	<?php echo sprintf( __('%s Results  found from %s for %s nigth(s)', 'bfi'),$totalResult , $checkin->format('d/m/Y'), BFCHelper::getStayParam('duration') ) ?>
</div>

<div class="com_bookingforconnector-search-menu">
	<form action="<?php echo $formAction; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
		<fieldset style="border:none !important;"class="filters">
			<input type="hidden" class="filterOrder" name="filter_order" value="stay" />
			<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="asc" />
			<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
			<input type="hidden" name="limitstart" value="0" />
		</fieldset>
	</form>
	<div class="com_bookingforconnector-results-sort">
		<span class="com_bookingforconnector-sort-item"><?php echo _e('Order by' , 'bfi')?>:</span>
		<span class="com_bookingforconnector-sort-item<?php // echo $activePrice; ?>" rel="stay|asc" <?php //echo $hidesort ?>><?php echo _e('Lowest price first' , 'bfi'); ?></span>
		<span class="com_bookingforconnector-sort-item<?php //echo $activeRating; ?>" rel="rating|desc" <?php // echo $hidesort ?>><?php echo _e('Review score' , 'bfi'); ?></span>
		<span class="com_bookingforconnector-sort-item<?php // echo $activeOffer; ?>" rel="offer|desc" <?php //echo $hidesort ?>><?php echo _e('Best offers' , 'bfi'); ?></span>
	</div>
	<div class="com_bookingforconnector-view-changer">
		<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo _e('List' , 'bfi') ?></div>
		<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo _e('Grid' , 'bfi') ?></div>
	</div>
</div>








<div class="bfi-clearfix"></div>
<div class="com_bookingforconnector-search-merchants com_bookingforconnector-items bfi-row com_bookingforconnector-list">
<?php 

$counterj = 0 ;
$listResourceIds = array(); 
$listResourceIdsByMerchant = array();

?>  

<?php foreach ($merchants as $merchant): ?>
<?php 

	$rating = $merchant->Rating;	
	if ($rating>9 )
	{
		$rating = $rating/10;
	} 

//	$routeMerchant = $base_url.'/merchant-details/merchantdetails/'.$merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
//	$routeRating = $base_url.'/merchant-details/reviews/'.$merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
	$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
	$routeRating = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
	$routeInfoRequest = $routeMerchant .'/'._x('contactspopup', 'Page slug', 'bfi' );

	$httpsPayment = $merchant->PaymentType;
	
	$counter = 0;
	$merchantLat = $merchant->XGooglePos;
	$merchantLon = $merchant->YGooglePos;
	$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
	
	$merchantLogo = BFI()->plugin_url() . "/assets/images/defaults/default-s1.jpeg";
	$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
	
	if(!empty($merchant->LogoUrl)){
		$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logomedium');
		$merchantLogoError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'logomedium');
	}
	if(!empty($merchant->ImageUrl)){
		$merchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->ImageUrl, 'medium');
	}

//$modelj = new BookingForConnectorModelMerchantDetails;
//$merchantj = $modelj->getItem($merchant->MerchantId );

?>


	<div class="bfi-col-md-12 com_bookingforconnector-item-col">
		<div class="com_bookingforconnector-search-merchant com_bookingforconnector-item  bfi-row" >
			<div class="mrcgroup" id="bfcmerchantgroup<?php echo $merchant->MerchantId; ?>"><span class="bfcmerchantgroup"></span></div>
			<div class="com_bookingforconnector-item-details  bfi-row" >
				<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel bfi-col-md-4">
					<div id="com_bookingforconnector-search-merchant-carousel<?php echo $merchant->MerchantId; ?>" class="carousel" >
						<div class="carousel-inner" role="listbox">
							<div class="item active"><img src="<?php echo $merchantImageUrl; ?>"></div>
						</div>
						<?php if($isportal): ?>
							<a class="bfi_logo-grid" href="<?php echo $routeMerchant ?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogo; ?>" id="bfi_logo-grid-<?php echo $merchant->MerchantId?>" /></div></a>
						<?php endif; ?>
					</div>
				</div>
						<div class="com_bookingforconnector-item-primary bfi-col-md-6">
							<div class="bfi_item-primary-name">
								<a class="namelist" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>"><?php echo  $merchant->Name ?></a> 
								<span class="com_bookingforconnector-search-merchant-rating com_bookingforconnector-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
							<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
								<div class="ratinggrid">
									<?php if ($merchant->reviewCount>0):?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-search-merchant-review-value com_bookingforconnector-item-review-value" href="<?php echo $routeRating ?>" id="ratingAnchorvalue<?php echo $merchant->MerchantId?>" ><?php echo number_format((float)$merchant->reviewValue, 1, '.', '') ?></a>
										<a class="com_bookingforconnector-search-merchant-review-count com_bookingforconnector-item-review-count" href="<?php echo $routeRating ?>" id="ratingAnchorcount<?php echo $merchant->MerchantId?>" ><?php echo sprintf(__('Score from %s reviews' , 'bfi'),$merchant->reviewCount) ?></a>
									</div>
									<?php else: ?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="bfi_leaverating " href="<?php echo $routeRatingform ?>"><?php _e('Would you like to leave your review?', 'bfi') ?></a>
									</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							</div>
							<a class="namegrid" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>"><?php echo  $merchant->Name ?></a> 
							<div class="bfi_item-primary-address">
								<?php if ($showMerchantMap):?><i class="fa fa-map-marker fa-1"></i> 
								<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><span id="address<?php echo $merchant->MerchantId?>"></span></a>
								<?php endif; ?>
							</div>
							<?php if($showdata): ?>
								<div class="bfi_merchant-description" id="descr<?php echo $merchant->MerchantId?>"></div>
							<?php endif; ?>
							<div class="ratinglist">
								<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
									<?php if ($merchant->reviewCount>0):?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="com_bookingforconnector-search-merchant-review-value com_bookingforconnector-item-review-value" href="<?php echo $routeRating ?>" id="ratingAnchorvalue<?php echo $merchant->MerchantId?>" ><?php echo number_format((float)$merchant->reviewValue, 1, '.', '') ?></a>
										<a class="com_bookingforconnector-search-merchant-review-count com_bookingforconnector-item-review-count" href="<?php echo $routeRating ?>" id="ratingAnchorcount<?php echo $merchant->MerchantId?>" ><?php echo sprintf(__('Score from %s reviews' , 'bfi'),$merchant->reviewCount) ?></a>
									</div>
									<?php else: ?>
									<div class="com_bookingforconnector-search-merchant-reviews-ratings com_bookingforconnector-item-reviews-ratings">
										<a class="bfi_leaverating " href="<?php echo $routeRatingform ?>"><?php _e('Would you like to leave your review?', 'bfi') ?></a>
									</div>
									<?php endif; ?>
								<?php endif; ?>&nbsp;
							</div>
							<div class="bfi_item-primary-address"> 
								<span class="bfi_phone">
								<a  href="javascript:void(0);" 
									onclick="bookingfor.getData(urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes( $merchant->Name) ?>','PhoneView' )"  id="phone<?php echo $merchant->MerchantId?>"><?php echo _e('Show phone', 'bfi'); ?> </a>
								</span> - 					
								<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>"  ><?php echo  _e('Request info' , 'bfi') ?></a>
							</div>
						</div>

						<?php if($isportal): ?>
							<div class="bfi-item-secondary-logo bfi-col-md-2">
								<a class="bfi_logo-list" href="<?php echo $routeMerchant ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogo; ?>" id="bfi_logo-list-<?php echo $merchant->MerchantId?>" /></a>
							</div>
						<?php endif; ?>
					</div>

	
			<!-- resource list -->
				<?php 
					$count = 0; 
					$discount = 0;
					$maxviewExceeded = FALSE;
					$listResourceIdsByMerchant = array();

				?>
				<?php foreach($merchant->Resources as $resource) : ?>  
					<?php
					$resource->SimpleDiscountIds = "";
					$resource->DiscountIds = json_decode($resource->DiscountIds);
					if(is_array($resource->DiscountIds) && count($resource->DiscountIds)>0){
//						$tmpDiscountIds = array_unique(array_map(function ($i) { return $i->VariationPlanId; }, $resource->DiscountIds ));
						$resource->SimpleDiscountIds  = implode(',',$resource->DiscountIds);
					}		
					if($count < $maxItemsView){
						$listResourceIds[]= $resource->ResourceId; 
					}else{
						$listResourceIdsByMerchant[]= $resource->ResourceId; 
					}
				?>
				<?php if($count == $maxItemsView):?>
				<?php $maxviewExceeded = TRUE; ?>	
					<div id="showallresource<?php echo $merchant->MerchantId?>" style="display:none;" class="bfi-col-md-12">
				<?php endif; ?>
					<div class="com_bookingforconnector-search-resource-details com_bookingforconnector-item-secondary bfi-row" style="padding-top: 10px !important;padding-bottom: 10px !important;">
				<?php 
					$resourceName = BFCHelper::getLanguage($resource->ResName, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
					//$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
					//	if ($itemId<>0){
					//	$currUriresource.='&Itemid='.$itemId;
					//}
//					$resourceRoute = $base_url.'/accommodation-details/'.$resource->ResourceId.'-'.BFI()->seoUrl($resource->ResName);				   
					$resourceRoute = $url_resource_page . $resource->ResourceId.'-'.BFI()->seoUrl($resource->ResName);
					//$resourceRoute = JRoute::_($currUriresource);
					$bookingType = $resource->BookingType;
					$IsBookable = $resource->IsBookable;
					$btnText = __('Request info','bfi');
					$btnClass = "";
					if ($IsBookable){
						$btnText = __('Book Now','bfi');
						$btnClass = "bfi-btn-bookable";
					}
					$classofferdisplay = "";
					if (($resource->Price < $resource->TotalPrice) || $resource->IsOffer){
						$classofferdisplay = "com_bookingforconnector_highlight";
					}
					if (!empty($resource->RateplanId)){
						$resourceRoute .= "?pricetype=" . $resource->RateplanId;
					}
				?>
						<div class="bfi-item-secondary-name" style="padding-left:10px;">
							<a href="<?php echo $resourceRoute?>"><?php echo $resourceName; ?></a>
							<?php if ($resource->PercentVariation<0): ?><div class="specialoffer variationlabel" rel="<?php echo  $resource->SimpleDiscountIds ?>"  rel1="<?php echo  $resource->ResourceId ?>" ><?php echo $resource->PercentVariation ?>% <?php  _e(' Offer ' , 'bfi'); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></div><?php endif; ?>
						</div>
						<div class="bfi-col-md-12 divoffers" id="divoffers<?php echo  $resource->ResourceId ?>" style="display:none ;">
								<i class="fa fa-spinner fa-spin fa-fw margin-bottom"></i>
								<span class="sr-only">Loading...</span>
						</div>
						<div class="bfi-clearfix"></div>
					<div class="bfi-row secondarysection" >
								<div class="bfi-col-md-5 com_bookingforconnector-item-secondary-section-1 secondarysectionitem">	 
									<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes">
										<i class="fa fa-user"></i>
										<?php if ($resource->MinPaxes == $resource->MaxPaxes):?>
											<?php echo  $resource->MaxPaxes ?>
										<?php else: ?>
											<?php echo  $resource->MinPaxes ?>-<?php echo  $resource->MaxPaxes ?>
										<?php endif; ?>
									</div>
									<?php if (!$resource->IsCatalog && $onlystay ): ?>
										<div class="com_bookingforconnector-search-resource-details-availability com_bookingforconnector-item-secondary-availability">
										<?php if ($resource->Availability < 4): ?>
										  <span class="com_bookingforconnector-item-secondary-availability-low"><?php echo sprintf(__('Only %d available' , 'bfi'),$resource->Availability) ?></span>
										<?php else: ?>
										  <?php echo _e('Available' , 'bfi');?>
										<?php endif; ?>
										</div>

										<?php if (!$resource->IsBase): ?>
											<div class="com_bookingforconnector_res_rateplanname">
												<?php echo _e('Meals' , 'bfi'); ?>:<br />
												<span><?php echo $resource->RateplanName ?></span>
											</div>
										<?php endif; ?>

									<?php endif; ?>
								</div>
								<div class="bfi-col-md-5 com_bookingforconnector-item-secondary-section-2 secondarysectionitem">
									<?php if (!$resource->IsCatalog && $resource->Price > 0): ?>
										<div class="com_bookingforconnector-search-grouped-resource-details-price com_bookingforconnector-item-secondary-price">
											<span class="bfi-gray-highlight"><?php echo sprintf(__('Total for %d night/s' ,'bfi'),$resource->Days) ?></span>
											 <div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
												<?php if ($resource->Price < $resource->TotalPrice): ?>
												<?php 
												  $current_discount = $resource->PercentVariation;
												  $discount = $current_discount < $discount ? $current_discount : $discount;
												?>
												<span class="com_bookingforconnector_strikethrough"><span class="com_bookingforconnector-search-resource-details-stay-discount com_bookingforconnector-item-secondary-stay-discount bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($resource->TotalPrice,2, ',', '.')  ?></span></span>
												<?php endif; ?>
												<span class="bfi-item-secondary-stay-total"><?php echo BFCHelper::priceFormat($resource->Price,2, ',', '.') ?></span>
											</div>
										</div>
									<?php endif; ?>
								</div>
								<div class="bfi-col-md-2 secondarysectionitem">
									<?php if ($resource->Price > 0): ?>
											<a href="<?php echo $resourceRoute ?>" class=" bfi-item-secondary-more <?php echo $btnClass ?> "><?php echo $btnText ?></a>
									<?php else: ?>
											<a href="<?php echo $resourceRoute ?>" class=" bfi-item-secondary-more"><?php echo _e('Request info' , 'bfi')?></a>
									<?php endif; ?>
								</div>
						</div>
					</div>

    <?php $count++; ?>
	<?php endforeach; ?>
	 <?php if($maxviewExceeded == TRUE) : ?>
	 </div>
	<div class="com_bookingforconnector-search-resource-details-showmax bfi-col-md-12"><a onclick="showallresource('#showallresource<?php echo $merchant->MerchantId?>',this,'<?php echo implode(',',$listResourceIdsByMerchant) ?>')" style="padding-left:10px;"> <i class="icon-plus "></i><?php echo _e('SHOW ALL' , 'bfi') ?></a></div>
	 <?php endif; ?>	
	
					<div class="discount-box" style="display:<?php if($discount < 0) { ?>block<?php }else{ ?>none<?php } ?>;">
						<?php echo sprintf(__('Offer %d%%' , 'bfi'), number_format($discount, 1)); ?>
					</div>
				</div>
				<div class="bfi-clearfix"><br /></div>
			</div>
		<?php $listsId[]= $merchant->MerchantId; ?>
	<?php endforeach; ?>
</div>
<script type="text/javascript">
<!--
jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('bfi-col-md-6').addClass('bfi-col-md-12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('bfi-col-md-4');
	jQuery('.com_bookingforconnector-item-primary').addClass('bfi-col-md-6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('bfi-col-md-4').addClass('bfi-col-md-5');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('bfi-col-md-4').addClass('bfi-col-md-5');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('bfi-col-md-4').addClass('bfi-col-md-2');
	localStorage.setItem('display', 'list');
});

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('bfi-col-md-12').addClass('bfi-col-md-6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('bfi-col-md-4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('bfi-col-md-6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('bfi-col-md-5').addClass('bfi-col-md-4');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('bfi-col-md-5').addClass('bfi-col-md-4');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('bfi-col-md-2').addClass('bfi-col-md-4');
	localStorage.setItem('display', 'grid');
});

if (localStorage.getItem('display')) {
	if (localStorage.getItem('display') == 'list') {
		jQuery('#list-view').trigger('click');
	} else {
		jQuery('#grid-view').trigger('click');
	}
} else {
	 if(typeof bfc_display === 'undefined') {
		jQuery('#list-view').trigger('click');
	 } else {
		if (bfc_display == '1') {
			jQuery('#grid-view').trigger('click');
		} else { 
			jQuery('#list-view').trigger('click');
		}
	}
}


//	jQuery('.inforequest').bind('click', function() {
//		var merchantid = jQuery(this).attr('rel');
//		var id = jQuery(this).attr('id');
//		jQuery.blockUI({ message: '<h2>Processing</h2>' }); 
//		var queryMG = '<?php echo $base_url; ?>/get-inforequest-form?merchantid='+merchantid;
//		jQuery.getJSON(queryMG, function(data) {
//			jQuery.unblockUI();
//			jQuery(data).dialog({
//				title: jQuery(data).find('.merchant-name').val(),
//				close: function(event, ui) {
//					jQuery(this).dialog("close");
//					jQuery(this).remove();
//				},
//				'width' : '80%'
//			});
//			attachDatepicker(data);
//		});
//	});


//var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";
var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";
var cultureCodeMG = '<?php echo $language ?>';
var defaultcultureCodeMG = '<?php echo BFCHelper::$defaultFallbackCode ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';

var strRatingNoResult = "<?php _e('Would you like to leave your review?', 'bfi') ?>";
var strRatingBased = "<?php _e('Score from %s reviews', 'bfi') ?>";
var strRatingValuation = "<?php _e('Guest Rating', 'bfi') ?>";

var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
		showChars: '250'
};

var mg = [];

var loaded=false;

function getAjaxInformations(){
	if (!loaded)
	{
		loaded=true;
		var queryMG = "task=getMerchantGroups";
//		var urlgetMG = updateQueryStringParameter(urlCheck,"task","getMerchantGroups");

//		jQuery.getJSON(urlgetMG, function(data) {
		jQuery.post(urlCheck, queryMG, function(data) {
				if(data!=null){
					jQuery.each(data || [], function(key, val) {
						if (val.ImageUrl!= null && val.ImageUrl!= '') {
							var $imageurl = imgPathMG.replace("[img]", val.ImageUrl );		
							var $imageurlError = imgPathMGError.replace("[img]", val.ImageUrl );		
							/*--------getName----*/
							var $name = bookingfor.getXmlLanguage(val.Name,cultureCodeMG,defaultcultureCodeMG);
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


function getResourceslist(listResourceIdsToCheck,loadMerchantlist){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "resourcesId=" + listResourceIdsToCheck + "&language=<?php echo $language ?>";
		query +="&task=GetResourcesByIds";

	if(listResourceIdsToCheck!=''){
				if(loadMerchantlist){
					getlist();
				}
//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
				jQuery.each(data || [], function(key, val) {
					//price
					jQuery("#resourcestaytotal"+val.Resource.ResourceId).html("&nbsp; ");
					if (val.Resource.RatePlanStay != null && val.Resource.RatePlanStay != '' && val.Resource.RatePlanStay.SuggestedStay!= null) {
						var st = val.Resource.RatePlanStay.SuggestedStay;
						var TotalPrice = parseFloat(st.TotalPrice);
						var DiscountedPrice = parseFloat(st.DiscountedPrice);
						if(TotalPrice>0){
							var currnt = "<?php echo sprintf(__('Total for %d night/s','bfi'),'') ?>"
							jQuery("#totaldays"+val.Resource.ResourceId).html(currnt.replace("0", st.Days));
							if(DiscountedPrice< TotalPrice){
								jQuery("#resourcestaydiscount"+val.Resource.ResourceId).html(bookingfor.number_format(TotalPrice, 2, '.', ''));
							}
							jQuery("#resourcestaytotal"+val.Resource.ResourceId).html(bookingfor.number_format(DiscountedPrice, 2, '.', ''));
							if(st.IsOffer){
								jQuery(".container"+val.Resource.ResourceId).addClass("com_bookingforconnector_highlight");
							}
						}
					}
			});	
		},'json');

	}
}

function getlist(){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "merchantsId=" + listToCheck + "&language=<?php echo $language ?>&task=GetMerchantsByIds";
	if(listToCheck!='')
	
	var imgPath = "<?php echo $merchantImagePath ?>";
	var imgPathError = "<?php echo $merchantImagePathError ?>";

//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
				merchantLogo="<?php echo $merchantLogo ?>";
				merchantLogoError="<?php echo $merchantLogo ?>";

				
				if (val.AddressData != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					xmlDoc = jQuery.parseXML(val.AddressData);
					if(xmlDoc!=null){
						$xml = jQuery(xmlDoc);
						$indirizzo = $xml.find("indirizzo:first").text();
						$cap = $xml.find("cap:first").text();
						$comune = $xml.find("comune:first").text();
						$provincia = $xml.find("provincia:first").text();
					}else{
						$indirizzo = val.AddressData.Address;
						$cap = val.AddressData.ZipCode;
						$comune = val.AddressData.CityName;
						$provincia = val.AddressData.RegionName;
					}
					merchAddress = strAddress.replace("[indirizzo]",$indirizzo);
					merchAddress = merchAddress.replace("[cap]",$cap);
					merchAddress = merchAddress.replace("[comune]",$comune);
					merchAddress = merchAddress.replace("[provincia]",$provincia);
					jQuery("#address"+val.MerchantId).append(merchAddress);
				}
				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '<span class="bfcmerchantgroup">';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					$htmlmg += '</span>';
					jQuery("#bfcmerchantgroup"+val.MerchantId).html($htmlmg);
				}
<?php if($showdata): ?>
				$html = '';
				if (val.Description!= null && val.Description != ''){
					$html += bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(val.Description) + "</p>").text());
				}

				jQuery("#descr"+val.MerchantId).data('jquery.shorten', false);
				jQuery("#descr"+val.MerchantId).html($html);
				
				jQuery("#descr"+val.MerchantId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.MerchantId).shorten(shortenOption);
<?php endif; ?>

				jQuery("#container"+val.MerchantId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.MerchantId ).attr("href");
					}
				});

				if (val.RatingsContext!= null && (val.RatingsContext == '1' || val.RatingsContext == '3')){
					$htmlAvg = '';
					if (val.Avg != null && val.Avg != '' ) {
						jQuery("#ratingAnchorvalue"+val.MerchantId).html(bookingfor.number_format(val.Avg.Average, 1, '.', ''));
						jQuery("#ratingAnchorcount"+val.MerchantId).html(strRatingBased.replace("%s", val.Avg.Count));
					}
				}else{
					jQuery("#ratingAnchorvalue"+val.MerchantId).parent().hide();		
					jQuery("#ratingAnchorcount"+val.MerchantId).parent().hide();				
				}
				jQuery('span[id^="resourcestaytotal"]:visible:has(i)').html("&nbsp; ");

		});	
		},'json');
}

function getDiscountsAjaxInformations(discountIds,obj, fn){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {
			$html = '';
			jQuery.each(data || [], function(key, val) {
				var name = val.Name;
				var descr = val.Description;
				name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
				$html += '<p class="title">' + name + '</p>';
				descr = bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(descr) + "</p>").text());
				$html += '<p class="description ">' + descr + '</p>';
			});
			offersLoaded[discountIds] = $html;
			fn(obj,$html);
			//jQuery(obj).html($html);
	},'json');

}
function getRateplanAjaxInformations(rateplanId){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "rateplanId=" + rateplanId + "&language=<?php echo $language ?>&task=getRateplanDetails";
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {

				var name = bookingfor.getXmlLanguage(data.Name,cultureCode,defaultcultureCode);;
				name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
				jQuery("#divrateplanTitle"+rateplanId).html(name);

				var descr = bookingfor.getXmlLanguage(data.Description,cultureCode,defaultcultureCode);;
				descr = bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(descr) + "</p>").text());
				jQuery("#divrateplanDescr"+rateplanId).html(descr);
				jQuery("#divrateplanDescr"+rateplanId).removeClass("com_bookingforconnector_loading");
	},'json');

}

var offersLoaded = []
var rateplansLoaded = []
jQuery(document).ready(function() {
	getAjaxInformations();

	jQuery('.bfi-maps-static').click(function() {
		jQuery( "#bfi-maps-popup" ).dialog({
			open: function( event, ui ) {
				openGoogleMapSearch();
			},
			height: 500,
			width: 800,
		});
	});
	
	jQuery('.com_bookingforconnector-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
	  jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
	  
	  if(jQuery('#searchformfilter').length){
		  jQuery('#searchformfilter').submit();
	  }else{
		  jQuery('#bookingforsearchFilterForm').submit();
	  }
	});
	
	jQuery('#bookingforsearchFilterForm').ajaxForm({
			target:     '#bfcmerchantlist',
			replaceTarget: true, 
			url:        '<?php echo $formAction; ?>'+'', 
			data: 	{ format: 'raw', tmpl: 'component',limitstart:'0' },
			beforeSerialize:function() {
				try
				{
					jQuery("#filter_order_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order']").val());					
					jQuery("#filter_order_Dir_filter").val(jQuery("#bookingforsearchFilterForm input[name='filter_order_Dir']").val());	
					
				}
				catch (e)
				{
				}
			},
			beforeSend: function() {
				jQuery('#bfcmerchantlist').block();
			},
			success: showResponse,
			error: showError
	});
	
	jQuery(".variationlabel").click(function(){
		var show = function(resourceId, text){
					jQuery("#divoffers"+resourceId).empty();
					//jQuery("#divoffers"+resourceId).removeClass("com_bookingforconnector_loading");
					jQuery("#divoffers"+resourceId).html(text);
		};
		var discountIds = jQuery(this).attr('rel'); 
		var resourceId = jQuery(this).attr('rel1'); 
//		jQuery("#divoffers"+resourceId).slideToggle( "slow" );
		if(jQuery("#divoffers"+resourceId).is(":visible")){
			jQuery("#divoffers"+resourceId).slideUp("slow");
			jQuery(this).children().toggleClass("fa-angle-up fa-angle-down");
		  } else {
			jQuery("#divoffers"+resourceId).slideDown("slow");
			jQuery(this).children().toggleClass("fa-angle-up fa-angle-down");
		  }

		//if (jQuery.inArray(discountIds,offersLoaded)===-1)
		if(!offersLoaded.hasOwnProperty(discountIds))
		{
			getDiscountsAjaxInformations(discountIds,resourceId,show);
			//offersLoaded.push(discountIds);
		}else{
			show(resourceId,offersLoaded[discountIds]);
			//jQuery("#divoffers"+resourceId).html(offersLoaded[discountIds]);
		}
	});

	jQuery(".rateplanslabel").click(function(){
				var rateplanId = jQuery(this).attr('rel'); 
				if (jQuery.inArray(rateplanId,rateplansLoaded)===-1)
				{
					getRateplanAjaxInformations(rateplanId);
					rateplansLoaded.push(rateplanId);
				}
				jQuery("#divrateplan"+rateplanId).slideToggle( "slow" );
			});

});

function showallresource(who,elm,listid){
	getResourceslist(listid,false);
	jQuery(who).show();
	jQuery(elm).hide();

}

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.XGooglePos == '' || val.YGooglePos == '' || val.XGooglePos == null || val.YGooglePos == null)
				return true;
			var url = "<?php echo $url_merchant_page; ?>" + val.MerchantId + '-' + val.MerchantName + '/mapspopup';
//			url += '?format=raw&layout=map&merchantId=' + val.MerchantId;
			
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.XGooglePos, val.YGooglePos),
				map: currentMap
			});

			marker.url = url;
			marker.extId = val.MerchantId;

			oms.addMarker(marker);
					
			bounds.extend(marker.position);
		});
	}


//-->
</script>