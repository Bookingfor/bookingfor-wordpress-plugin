<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
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

$fromsearchparam = "?fromsearch=1&lna=".$listNameAnalytics;
$showSearchTitle = true;
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$listsId = array();
$listResourceIds = array();
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$img = BFI()->plugin_url() . "/assets/images/default.png";
$imgError = BFI()->plugin_url() . "/assets/images/default.png";

$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'medium');


$base_url = get_site_url();
$onlystay = true ;
$currParam = BFCHelper::getSearchParamsSession();
if(isset($currParam) && isset($currParam['onlystay'])){
		$onlystay =  ($currParam['onlystay'] === 'false' || $currParam['onlystay'] === 0)? false: true;
}

// get searchresult page...
$searchAvailability_page = get_post( bfi_get_page_id( 'searchavailability' ) );
$formAction = get_permalink( $searchAvailability_page->ID );


$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );

$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
$url_resource_page = get_permalink( $accommodationdetails_page->ID );

$totalResult = $totalAvailable;

$checkin = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
$checkout = BFCHelper::getStayParam('checkout', new DateTime('UTC'));
$checkinstr = $checkin->format("d") . " " . date_i18n('F',$checkin->getTimestamp()) . ' ' . $checkin->format("Y") ;
$checkoutstr = $checkout->format("d") . " " . date_i18n('F',$checkout->getTimestamp()) . ' ' . $checkout->format("Y") ;

$counter = 0;

$currFilterOrder = "";
$currFilterOrderDirection = "";
if (!empty($currSorting) &&strpos($currSorting, '|') !== false) {
	$acurrSorting = explode('|',$currSorting);
	$currFilterOrder = $acurrSorting[0];
	$currFilterOrderDirection = $acurrSorting[1];
}
?>
<div class="bfi-content">
	<div class="bfi-row">
		<div class="bfi-col-xs-9 ">
			<?php if($showSearchTitle){ ?>
			<div class="bfi-search-title">
				<?php echo sprintf( __('Found %s results', 'bfi'),$totalResult ) ?>
				<?php if ($totalAvailable != $total) {
					echo " " . __('on', 'bfi') . " " . $total ." ";
				} ?>
			</div>
			<div class="bfi-search-title-sub">
				<?php echo sprintf( __('From %s to %s', 'bfi'),$checkinstr,$checkoutstr ) ?>
			</div>
			<?php } ?>
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
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $currFilterOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $currFilterOrderDirection ?>" />
				<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
				<input type="hidden" name="limitstart" value="0" />
		</form>
		<div class="bfi-results-sort">
			<span class="bfi-sort-item"><?php echo _e('Order by' , 'bfi')?>:</span>
			<span class="bfi-sort-item <?php echo $currSorting=="price|asc" ? "bfi-sort-item-active": "" ; ?>" rel="price|asc" ><?php echo _e('Lowest price first' , 'bfi'); ?></span>
			<span class="bfi-sort-item <?php echo $currSorting=="rating|desc" ? "bfi-sort-item-active": "" ; ?>" rel="rating|desc" ><?php echo _e('Review score' , 'bfi'); ?></span>
			<span class="bfi-sort-item <?php echo $currSorting=="offer|asc" ? "bfi-sort-item-active": "" ; ?>" rel="offer|asc" ><?php echo _e('Best offers' , 'bfi'); ?></span> 
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
<?php 
foreach ($merchants as $currKey => $merchant){

	$merchantName = $merchant->MrcName;
	$hasSuperior = !empty($merchant->MrcRatingSubValue);
	$rating = (int)$merchant->MrcRating;
	if ($rating>9 )
	{
		$rating = $rating/10;
		$hasSuperior = ($merchant->MrcRating%10)>0;
	} 

	$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchantName);
	$routeRating = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
	$routeInfoRequest = $routeMerchant .'/'._x('contactspopup', 'Page slug', 'bfi' );

	$httpsPayment = $merchant->PaymentType;
	
	$merchantLat = $merchant->MrcLat;
	$merchantLon = $merchant->MrcLng;
	$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
	
	$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
	
	if(!empty($merchant->MrcImageUrl)){
		$merchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->MrcImageUrl, 'medium');
	}

	$merchant->SimpleDiscountIds = "";
	$merchant->DiscountIds = json_decode($merchant->DiscountIds);
	if(is_array($merchant->DiscountIds) && count($merchant->DiscountIds)>0){
		$merchant->SimpleDiscountIds  = implode(',',$merchant->DiscountIds);
	}		

	$resourceName = BFCHelper::getLanguage($merchant->ResName, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$resourceRoute = $url_resource_page . $merchant->ResourceId.'-'.BFI()->seoUrl($merchant->ResName);

	$bookingType = $merchant->BookingType;
	$IsBookable = $merchant->IsBookable;
	$btnText = __('Request','bfi');
	$btnClass = "bfi-alternative";
	if ($IsBookable){
		$btnText = __('Book Now','bfi');
		$btnClass = "";
	}
	$classofferdisplay = "";
	if (($merchant->Price < $merchant->TotalPrice) || $merchant->IsOffer){
		$classofferdisplay = "bfi-highlight";
	}
	$resourceRoute .= $fromsearchparam;
	$routeMerchant .= $fromsearchparam;
	if (!empty($merchant->RateplanId)){
		$resourceRoute .= "&pricetype=" . $merchant->RateplanId;
	}

	$resourceNameTrack =  BFCHelper::string_sanitize($resourceName);
	$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
	$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MrcCategoryName);
?>
	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $routeMerchant ?>" style='background: url("<?php echo $merchantImageUrl; ?>") center 25%;background-size: cover;' target="_blank" class="eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><img src="<?php echo $merchantImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-9">
						<div class="bfi-item-title">
							<a href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" target="_blank" class="eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $merchantName ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
								<?php if ($hasSuperior) { ?>
									&nbsp;S
								<?php } ?>
							</span>
							<?php if((isset($merchant->IsRecommendedResult) && $merchant->IsRecommendedResult )) { ?><i class="fa fa-heart-o" aria-hidden="true" data-toggle="tooltip" title="<?php _e('Certainly it is our Preferred Merchant! They provide a great value and an excellent service.', 'bfi') ?>"></i>	<?php } ?>							
						</div>
						<div class="bfi-item-address">
							<?php if ($showMerchantMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><span id="address<?php echo $merchant->MerchantId?>"></span></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $merchant->MerchantId; ?>"></div>
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
						<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)){?>
								<div class="bfi-avg">
								<?php if ($merchant->MrcAVGCount>0){
									$totalInt = BFCHelper::convertTotal(number_format((float)$merchant->MrcAVG, 1, '.', ''));

									?>
									<a class="bfi-avg-value eectrack" href="<?php echo $routeMerchant ?>" target="_blank" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt] . " " . number_format((float)$merchant->MrcAVG, 1, '.', '') ?></a><br />
									<a class="bfi-avg-count eectrack" href="<?php echo $routeMerchant ?>" target="_blank" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo sprintf(__('%s reviews' , 'bfi'),$merchant->MrcAVGCount) ?></a>
								<?php } ?>
								</div>
						<?php } ?>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
<?php if( !empty($merchant->Availability) || $merchant->IsCatalog) { //ok disp ?>
				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-6">
						<?php if ($merchant->MaxPaxes>0):?>
							<div class="bfi-icon-paxes">
								<i class="fa fa-user"></i> 
								<?php if ($merchant->MaxPaxes==2){?>
								<i class="fa fa-user"></i> 
								<?php }?>
								<?php if ($merchant->MaxPaxes>2){?>
									<?php echo ($merchant->MinPaxes != $merchant->MaxPaxes)? $merchant->MinPaxes . "-" : "" ?><?php echo  $merchant->MaxPaxes ?>
								<?php }?>
							</div>
						<?php endif; ?>
						<a href="<?php echo $resourceRoute?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $counter?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $resourceName; ?></a>
					</div>
					<div class="bfi-col-sm-3 ">
						<?php if (!$merchant->IsCatalog && $onlystay ){ ?>
							<div class="bfi-availability">
							<?php if ($merchant->Availability < 2){ ?>
							  <span class="bfi-availability-low"><?php echo sprintf(__('Only %d available' , 'bfi'),$merchant->Availability) ?></span>
							<?php } ?>
							</div>
						<?php } ?>
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
						<?php if (!$merchant->IsCatalog && $onlystay ){ 
														
							if($merchant->IncludedMeals >-1){
								switch ($merchant->IncludedMeals) {
								    case bfi_Meal::Breakfast:
											_e("Breakfast included", 'bfi');
										break;
								    case bfi_Meal::BreakfastLunch:
									case bfi_Meal::BreakfastDinner:
									case bfi_Meal::LunchDinner :
											_e("Half board", 'bfi');
										break;
								    case bfi_Meal::BreakfastLunchDinner:
											_e("Full board", 'bfi');
										break;
								    case bfi_Meal::AllInclusive:
											_e("All Inclusive", 'bfi');
										break;
								        
								}
							}
						} else {?>
							<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $counter?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo _e('Request' , 'bfi')?></a>
						<?php } ?>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
																<!-- end resource details -->

				<?php if (!$merchant->IsCatalog && $onlystay && !empty($merchant->AvailabilityDate)){ ?>
				<!-- price details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-4 bfi-text-right ">
					<?php if ($merchant->MaxPaxes>0){?>
					<?php echo sprintf(__('Price for %s person' ,'bfi'),$totPerson) ?>
					<?php } ?>					
					</div>
					<div class="bfi-col-sm-5 bfi-text-right ">
							<div class="bfi-gray-highlight">
							<?php 
								$currCheckIn = DateTime::createFromFormat('Y-m-d\TH:i:s',$merchant->AvailabilityDate,new DateTimeZone('UTC'));
								$currCheckOut = DateTime::createFromFormat('Y-m-d\TH:i:s',$merchant->CheckOutDate,new DateTimeZone('UTC'));
								$currDiff = $currCheckOut->diff($currCheckIn);
								$hours = $currDiff->h;
								$minutes = $currDiff->i;

								switch ($merchant->AvailabilityType) {
									case 0:
										echo __('Total for', 'bfi');
										echo sprintf(__(' %d day/s' ,'bfi'),$merchant->Days);
										break;
									case 1:
										echo __('Total for', 'bfi');
										echo sprintf(__(' %d night/s' ,'bfi'),$merchant->Days);
										break;
									case 2:
										echo __('Total for', 'bfi');
										if($hours >0){
											echo sprintf(__(' %d hour/s' ,'bfi'),$hours);
										}
										if($minutes >0){
											echo sprintf(__(' %d minute/s' ,'bfi'),$minutes);
										}
										break;
									case 3:
										echo __("From", 'bfi');
										//sospeso momentaneamente
//										echo __('Total for', 'bfi');
//										if($hours >0){
//											echo sprintf(__('%d hour/s' ,'bfi'),$hours);
//										}
//										if($minutes >0){
//											echo sprintf(__('%d minute/s' ,'bfi'),$minutes);
//										}
										break;
								}
							?>
							</div>
							<?php if ($merchant->Price < $merchant->TotalPrice){ ?>
							<span class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?> bfi-cursor" rel="<?php echo $merchant->SimpleDiscountIds ?>"><?php echo number_format($merchant->TotalPrice,2, ',', '.')  ?><span class="bfi-no-line-through">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></span></span>
							<?php } ?>
							<span class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>  <?php echo ($merchant->Price < $merchant->TotalPrice)?"bfi-red":"" ?>" ><?php echo BFCHelper::priceFormat($merchant->Price,2, ',', '.') ?></span>
					</div>
					<div class="bfi-col-sm-3 bfi-text-right">
						<?php if ($merchant->Price > 0){ ?>
								<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $counter?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"" target="_blank"><?php echo $btnText ?></a>
						<?php }else{ ?>
								<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $counter?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"" target="_blank"><?php echo _e('Request' , 'bfi')?></a>
						<?php } ?>
					</div>
				</div>
				<div class="bfi-clearfix"></div>
				<!-- end price details -->
				<?php } ?>

<?php 
   
} else {  //ko disp:alternative
?>
<!-- merchant No resource  -->
				<?php
				$currStart = BFCHelper::getVar('limitstart','-1');
				if ($currKey==0 && $currStart =='0' ) {
				?>
					<div class="bfi-noavailability">
						<div class="bfi-alert bfi-alert-danger">
							<b><?php echo sprintf( __('Unfortunately we have no availability at this merchant for your dates: %s - %s', 'bfi') ,$checkinstr,$checkoutstr ) ?></b>
						</div>
					</div>
					<div class="bfi-check-more" data-type="merchant" data-id="<?php echo $merchant->MerchantId?>" >
						<?php _e('Limited availability, but may sell out:', 'bfi') ?>
						<div class="bfi-check-more-slider">
						</div>
					</div>
				<?php } else { ?>
					<div class="bfi-noavailability">
						<div class="bfi-alert bfi-alert-danger">
							<b>
							<?php if(rand(0, 1)==0) { ?>
								<?php _e('For a short time you missed it. Our last resource sold out a few days ago', 'bfi') ?>
							<?php }else{ ?>
								<?php _e("We're sorry! we do not have any availability for this resource.", 'bfi') ?>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				<div class="bfi-clearfix"></div>
<?php } ?>
			</div>
			<div class="bfi-discount-box" style="display:<?php echo ($merchant->PercentVariation < 0)?"block":"none"; ?>;">
				<?php echo sprintf(__('Offer %d%%' , 'bfi'), number_format($merchant->PercentVariation, 1)); ?>
			</div>
		</div>
	</div>
<?php 
	$listsId[]= $merchant->MerchantId;
	$listResourceIds[]= $merchant->ResourceId;
	$counter++;
}
?>

</div>
</div>
<script type="text/javascript">
<!--

var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";
var imgPathMG = "<?php echo BFCHelper::getImageUrlResized('tag','[img]', 'merchant_merchantgroup') ?>";
var imgPathMGError = "<?php echo BFCHelper::getImageUrl('tag','[img]', 'merchant_merchantgroup') ?>";

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
	var query = "merchantsId=" + listToCheck + "&language=<?php echo $language ?>&task=GetMerchantsByIds";
	if(listToCheck!='')
	
	var imgPath = "<?php echo $merchantImagePath ?>";
	var imgPathError = "<?php echo $merchantImagePathError ?>";

	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';

				if (val.AddressData != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					$indirizzo = val.AddressData.Address;
					$cap = val.AddressData.ZipCode;
					$comune = val.AddressData.CityName;
					$provincia = val.AddressData.RegionName;

					merchAddress = strAddress.replace("[indirizzo]",$indirizzo);
					merchAddress = merchAddress.replace("[cap]",$cap);
					merchAddress = merchAddress.replace("[comune]",$comune);
					merchAddress = merchAddress.replace("[provincia]",$provincia);
					jQuery("#address"+val.MerchantId).append(merchAddress);
				}
				if (val.TagsIdList!= null && val.TagsIdList != '')
				{
					var mglist = val.TagsIdList.split(',');
					$htmlmg = '';
					jQuery.each(mglist, function(key, mgid) {
						if(typeof mg[mgid] !== 'undefined' ){
							$htmlmg += mg[mgid];
						}
					});
					jQuery("#bfitags"+val.MerchantId).html($htmlmg);
				}
				jQuery("#container"+val.MerchantId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.MerchantId ).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		},'json');
}

function getDiscountsAjaxInformations(discountIds,obj, fn){
	var query = "discountId=" + discountIds + "&language=<?php echo $language ?>&task=getDiscountDetails";
	jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {
			$html = '';
			jQuery.each(data || [], function(key, val) {
				var name = val.Name;
				var descr = val.Description;
				name = bookingfor.nl2br(jQuery("<p>" + name + "</p>").text());
				$html += '<p class="title">' + name + '</p>';
				descr = bookingfor.nl2br(jQuery("<p>" + descr + "</p>").text());
				$html += '<p class="description ">' + bookingfor.stripbbcode(descr) + '</p>';
			});
			offersLoaded[discountIds] = $html;
			fn(obj,$html);
	},'json');

}

var offersLoaded = []

jQuery(document).ready(function() {
	/*----load sticky for other result...----*/
	jQuery('#bfi-list').on("cssClassChanged",function() {
		bfiCheckOtherAvailabilityResize();
	});

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

		if(jQuery('#searchformfilter').length){
			jQuery('#searchformfilter').submit();
		}else{
			jQuery('#bookingforsearchFilterForm').submit();
		}
	});

	jQuery(".bfi-discounted-price").on("click", function (e) {
		e.preventDefault();
		var bfi_wuiP_width= 400;
		if(jQuery(window).width()<bfi_wuiP_width){
			bfi_wuiP_width = jQuery(window).width()*0.7;
		}
		var showdiscount = function (obj, text) {
							obj.find("i").first().switchClass("fa-spinner fa-spin","fa-question-circle")
							obj.webuiPopover({
								content : text,
								container: document.body,
								closeable:true,
								placement:'auto-bottom',
								dismissible:true,
								trigger:'manual',
								type:'html',
								width : bfi_wuiP_width,
								style:'bfi-webuipopover'
							});
							obj.webuiPopover('show');

		};
		var discountIds = jQuery(this).attr('rel');

		if (!bookingfor.offersLoaded.hasOwnProperty(discountIds)) {
			jQuery(this).find("i").first().switchClass("fa-question-circle","fa-spinner fa-spin")
			bookingfor.GetDiscountsInfo(discountIds,"<?php echo $language ?>", jQuery(this), showdiscount);

		} else {
			showdiscount(jQuery(this), bookingfor.offersLoaded[discountIds]);
		}
	});		
//		jQuery(".bfi-percent-discount").on("blur", function (e) {
//			jQuery(this).webuiPopover('hide');
//		});	
	jQuery(".bfi-discounted-price").focusout(function () {
		jQuery(this).webuiPopover('hide');
	});	

});

	function createMarkers(data, oms, bounds, currentMap) {
		jQuery.each(data, function(key, val) {
			if (val.XGooglePos == '' || val.YGooglePos == '' || val.XGooglePos == null || val.YGooglePos == null)
				return true;
			var query = "merchantId=" + val.MerchantId+ '&language=<?php echo $language ?>&task=getmarketinfomerchant';
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(val.XGooglePos, val.YGooglePos),
				map: currentMap
			});

			marker.url = bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + query;
			marker.extId = val.MerchantId;

			oms.addMarker(marker);
					
			bounds.extend(marker.position);
		});
	}
	/*---resize slider on list/grid view change*/
	function bfiCheckOtherAvailabilityResize() {
		jQuery(".bfi-check-more").each(function(){
			var currSlider = jQuery(this).find(".bfi-check-more-slider").first();
			if(currSlider.hasClass("slick-slider")){
				var currSliderWidth = jQuery(this).width()-80;
				console.log(jQuery(this).width());
				console.log(currSliderWidth);
				jQuery(currSlider).width(currSliderWidth);
				var ncolslick = Math.round(currSliderWidth/120);
				jQuery(currSlider).slick('slickSetOption', 'slidesToShow', ncolslick, true);
				jQuery(currSlider).slick('slickSetOption', 'slidesToScroll', ncolslick, true);
			}
		});	
	}


//-->
</script>