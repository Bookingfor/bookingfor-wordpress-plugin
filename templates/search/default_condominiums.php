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

$checkin = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
$checkout = BFCHelper::getStayParam('checkout', new DateTime('UTC'));
$checkinstr = $checkin->format("d") . " " . date_i18n('F',$checkin->getTimestamp()) . ' ' . $checkin->format("Y") ;
$checkoutstr = $checkout->format("d") . " " . date_i18n('F',$checkout->getTimestamp()) . ' ' . $checkout->format("Y") ;
//$totalResult = count($merchants);
$totalResult = $totalAvailable;

$language = $GLOBALS['bfi_lang'];
$languageForm ='';

$listsId = array();
$listResourceIds = array(); 

if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

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

$condominiumdetails_page = get_post( bfi_get_page_id( 'condominiumdetails' ) );
$url_condominium_page = get_permalink( $condominiumdetails_page->ID );

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
			<div class="bfi-search-title">
				<?php echo sprintf( __('Found %s results', 'bfi'),$totalResult ) ?>
				<?php if ($totalAvailable != $total) {
					echo " " . __('on', 'bfi') . " " . $total ." ";
				} ?>
			</div>
			<div class="bfi-search-title-sub">
				<?php echo sprintf( __('From %s to %s', 'bfi'),$checkinstr,$checkoutstr ) ?>
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
				<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $currFilterOrder ?>" />
				<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $currFilterOrderDirection ?>" />
				<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
				<input type="hidden" name="limitstart" value="0" />
		</form>
		<div class="bfi-results-sort">
			<span class="bfi-sort-item"><?php echo _e('Order by' , 'bfi')?>:</span>
			<span class="bfi-sort-item <?php echo $currSorting=="price|asc" ? "bfi-sort-item-active": "" ; ?>" rel="price|asc" ><?php echo _e('Lowest price first' , 'bfi'); ?></span>
			<!-- <span class="bfi-sort-item <?php echo $currSorting=="rating|desc" ? "bfi-sort-item-active": "" ; ?>" rel="rating|desc" ><?php echo _e('Review score' , 'bfi'); ?></span> -->
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


	$currName = BFCHelper::getLanguage($merchant->GrpName, $GLOBALS['bfi_lang'], null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
	$isCondominium = (!empty( $merchant->CondominiumId ) && ($merchant->CondominiumId != $merchant->ResourceId));

	$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->MrcName);
	$routeCondominium = $url_condominium_page . $merchant->CondominiumId.'-'.BFI()->seoUrl($merchant->GrpName);
//	$routeCondominium = $url_condominium_page . $merchant->CondominiumId.'-toreplace-';
	
	$routeRating = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
	$routeInfoRequest = $routeMerchant .'/'._x('contactspopup', 'Page slug', 'bfi' );

	$httpsPayment = $merchant->PaymentType;
	
	$counter = 0;
	$merchantLat = $merchant->MrcLat;
	$merchantLon = $merchant->MrcLng;
	$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
	
	$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
	
	if(!empty($merchant->GrpImageUrl)){
		$merchantImageUrl = BFCHelper::getImageUrlResized('condominium',$merchant->GrpImageUrl, 'medium');
	}

	$routeRatingform = $routeMerchant._x('review', 'Page slug', 'bfi' );

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
	$routeCondominium .= $fromsearchparam;
	if (!empty($merchant->RateplanId)){
		$resourceRoute .= "&pricetype=" . $merchant->RateplanId;
	}
	if(!$isCondominium){
		$currName = $resourceName;
		$routeCondominium = $resourceRoute;
		if(!empty($merchant->ImageUrl)){
			$merchantImageUrl = BFCHelper::getImageUrlResized('resources',$merchant->ImageUrl, 'medium');
		}
	}
	
	$ratingMrc = (int)$merchant->MrcRating;
	if ($ratingMrc>9 )
	{
		$ratingMrc = $ratingMrc/10;
		$hasSuperiorMrc = ($merchant->MrcRating%10)>0;
	} 

	$resourceDataTypeTrack =  ($isCondominium)?"Resource Group":"Resource";
	$resourceNameTrack =  BFCHelper::string_sanitize($currName);
	$merchantNameTrack =  BFCHelper::string_sanitize($merchantName);
	$merchantCategoryNameTrack =  BFCHelper::string_sanitize($merchant->MrcCategoryName);

?>


	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $routeCondominium ?>" style='background: url("<?php echo $merchantImageUrl; ?>") center 25%;background-size: cover;' target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><img src="<?php echo $merchantImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-12">
						<div class="bfi-item-title">
							<a href="<?php echo $routeCondominium ?>" id="nameAnchor<?php echo $merchant->CondominiumId?>" target="_blank" class="eectrack" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo  $currName ?></a> 
							<?php if($isportal) { ?>
								- <a href="<?php echo $routeMerchant?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Merchant" data-id="<?php echo $resource->MerchantId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $merchantName; ?></a>
								<span class="bfi-item-rating">
									<?php for($i = 0; $i < $ratingMrc; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
								</span>
							<?php } ?>
						</div>
						<div class="bfi-item-address">
							<?php if ($showMerchantMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->ResourceId?>)"><span id="address<?php echo $merchant->CondominiumId?>"></span></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $merchant->CondominiumId; ?>"></div>
					</div>
					<?php if(false) { ?>
						<div class="bfi-col-sm-3 bfi-text-right">
							<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)){?>
									<div class="bfi-avg">
									<?php if ($merchant->MrcAVGCount>0){
										$totalInt = BFCHelper::convertTotal(number_format((float)$merchant->MrcAVG, 1, '.', ''));

										?>
										<a class="bfi-avg-value eectrack" href="<?php echo $routeRating ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt] . " " . number_format((float)$merchant->MrcAVG, 1, '.', '') ?></a><br />
										<a class="bfi-avg-count eectrack" href="<?php echo $routeRating ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo sprintf(__('%s reviews' , 'bfi'),$merchant->MrcAVGCount) ?></a>
									<?php } ?>
									</div>
							<?php } ?>
						</div>
					<?php } ?>
					
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->

<?php if( !empty($merchant->Availability) || $merchant->IsCatalog) { //ok disp ?>
				<!-- resource details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-6">
						<?php if ($merchant->MaxPaxes>0){?>
							<div class="bfi-icon-paxes">
								<i class="fa fa-user"></i> 
								<?php if ($merchant->MaxPaxes==2){?>
								<i class="fa fa-user"></i> 
								<?php }?>
								<?php if ($merchant->MaxPaxes>2){?>
									<?php echo ($merchant->MinPaxes != $merchant->MaxPaxes)? $merchant->MinPaxes . "-" : "" ?><?php echo  $merchant->MaxPaxes ?>
								<?php }?>
							</div>
						<?php } ?>
						<?php if($isCondominium) { ?>
							<a href="<?php echo $resourceRoute?>" class="bfi-subitem-title eectrack" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $resourceName; ?></a>
						<?php } ?>
						
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
							<a href="<?php echo $resourceRoute ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="Resource" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo _e('Request' , 'bfi')?></a>
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
								if(!empty( $merchant->AvailabilityDate )){
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
								}
							?>
							</div>
							<?php if ($merchant->Price < $merchant->TotalPrice){ ?>
							<span class="bfi-discounted-price bfi-discounted-price-total bfi_<?php echo $currencyclass ?> bfi-cursor" rel="<?php echo $merchant->SimpleDiscountIds ?>"><?php echo number_format($merchant->TotalPrice,2, ',', '.')  ?><span class="bfi-no-line-through">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></span></span>
							<?php } ?>
							<span class="bfi-price bfi-price-total bfi_<?php echo $currencyclass ?>  <?php echo ($merchant->Price < $merchant->TotalPrice)?"bfi-red":"" ?>" ><?php echo BFCHelper::priceFormat($merchant->Price,2, ',', '.') ?></span>
					</div>
				<?php } ?>
					<div class="bfi-col-sm-3 bfi-text-right">
						<?php if ($merchant->Price > 0){ ?>
								<a href="<?php echo $routeCondominium ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo $btnText ?></a>
						<?php }else{ ?>
								<a href="<?php echo $routeCondominium ?>" class="bfi-btn eectrack <?php echo $btnClass ?>" target="_blank" data-type="<?php echo $resourceDataTypeTrack ?>" data-id="<?php echo $merchant->ResourceId?>" data-index="<?php echo $currKey?>" data-itemname="<?php echo $resourceNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"><?php echo _e('Request' , 'bfi')?></a>
						<?php } ?>
					</div>
				</div>
				<div class="bfi-clearfix"></div>
				<!-- end price details -->
			</div>
<?php } ?>
			<div class="bfi-discount-box" style="display:<?php echo ($merchant->PercentVariation < 0)?"block":"none"; ?>;">
				<?php echo sprintf(__('Offer %d%%' , 'bfi'), number_format($merchant->PercentVariation, 1)); ?>
			</div>
		</div>
	</div>
		
<?php 

		if($isCondominium){
			$listsId[]= $merchant->CondominiumId; 			
		}else{
			$listResourceIds[]= $merchant->ResourceId; 
		}
	} 
?>
</div>
</div>
<script type="text/javascript">
<!--

var listToCheck = "<?php echo implode(",", $listsId) ?>";
var listToCheckResources = "<?php echo implode(",", $listResourceIds) ?>";
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
	if(listToCheck!=''){
		var query = "ids=" + listToCheck + "&language=<?php echo $language ?>&task=GetCondominiumsByIds";
		jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
                    var name = bookingfor.getXmlLanguage(val.Name, bfi_variable.bfi_cultureCode, bfi_variable.bfi_defaultcultureCode);

				if (val.Address != null && val.Address != '') {
					var merchAddress = "";
					var $indirizzo = "";
					var $cap = "";
					var $comune = "";
					var $provincia = "";
					
					$indirizzo = val.Address;
					$cap = val.ZipCode;
					$comune = val.CityName;
					$provincia = val.RegionName;

					merchAddress = strAddress.replace("[indirizzo]", $indirizzo);
					merchAddress = merchAddress.replace("[cap]", $cap);
					merchAddress = merchAddress.replace("[comune]", $comune);
					merchAddress = merchAddress.replace("[provincia]", $provincia);
					jQuery("#address"+val.CondominiumId).append(merchAddress);
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
					jQuery("#bfitags"+val.CondominiumId).html($htmlmg);
				}
				
				jQuery("#container"+val.CondominiumId).click(function(e) {
					var $target = jQuery(e.target);
					if ( $target.is("div")|| $target.is("p")) {
						document.location = jQuery( "#nameAnchor"+val.CondominiumId ).attr("href");
					}
				});
		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		},'json');
	}


	if(listToCheckResources!=''){
		var query = "resourcesId=" + listToCheckResources + "&language=<?php echo $language ?>&task=GetResourcesByIds";
		

		jQuery.post(bfi_variable.bfi_urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
	
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
		},'json');
	}
	


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
			if (val.Resource.XGooglePos == '' || val.Resource.YGooglePos == '' || val.Resource.XGooglePos == null || val.Resource.YGooglePos == null)
				return true;
			var query = "resourceId=" + val.Resource.CondominiumId + '&language=<?php echo $language ?>&task=getmarketinfocondominium';
			if(val.Resource.CondominiumId == val.Resource.ResourceId ){
				query = "resourceId=" + val.Resource.CondominiumId + '&language=<?php echo $language ?>&task=getmarketinforesource';
			}
			var marker = new MarkerWithLabel({
				position: new google.maps.LatLng(val.Resource.XGooglePos, val.Resource.YGooglePos),
				map: currentMap,
				labelContent: val.Resource.Price>0?val.Resource.Price:val.Resource.ResourceName,
				labelAnchor: new google.maps.Point(22, 0),
				labelClass: ("bfi-labels-marker " + (val.Resource.Price>0?' bfi_<?php echo $currencyclass ?> ':'')) , // the CSS class for the label
				labelStyle: {opacity: 0.75}

			});
			marker.url = bfi_variable.bfi_urlCheck + ((bfi_variable.bfi_urlCheck.indexOf('?') > -1)? "&" :"?") + query;
			marker.extId = val.Resource.ResourceId;

			oms.addMarker(marker);
					
			bounds.extend(marker.position);
		});
	}


//-->
</script>