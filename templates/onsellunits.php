<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//all data from bfi_Shortcodes

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

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$onselldetails_page = get_post( bfi_get_page_id( 'onselldetails' ) );
$url_resource_page = get_permalink( $onselldetails_page->ID );
$uri = $url_resource_page;

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$posx = COM_BOOKINGFORCONNECTOR_GOOGLE_POSX;
$posy = COM_BOOKINGFORCONNECTOR_GOOGLE_POSY;
$startzoom = COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM;
$googlemapsapykey = COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY;


$counterResources = 1;

$resourceLogoPath = BFCHelper::getImageUrlResized('resources',"[img]", 'medium');
$resourceLogoPathError = BFCHelper::getImageUrl('resources',"[img]", 'medium');

$merchantLogoPath = BFCHelper::getImageUrlResized('merchant',"[img]", 'logomedium');
$merchantLogoPathError = BFCHelper::getImageUrl('merchant',"[img]", 'logomedium');

$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
$merchantLogoUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

//$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
$page = bfi_get_current_page() ;


$pages = 0;
if($total>0){
	$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
}
$listName = 'Sales Resource List';
if($filter_order == 'AddedOn' && $filter_order_Dir == 'desc'){
$listName .= ' - Latest';
}

		if($resources  != null && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
			do_action('bfi_head', $listName);
			$allobjects = array();
			foreach ($resources as $key => $value) {
				$obj = new stdClass;
				$obj->id = "" . $value->ResourceId . " - Sales Resource";
				$obj->name = $value->Name;
				$obj->category = $value->MerchantCategoryName;
				$obj->brand = $value->MerchantName;
				$obj->position = $key;
				$allobjects[] = $obj;
			}
//			$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');
			echo '<script type="text/javascript"><!--
			';
			echo ('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');
			echo "//--></script>";
		}


?>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
//-->
</script>

<div id="com_bookingforconnector-items-container-wrapper">
	<div class="com_bookingforconnector-items-container">
	<?php if ($resources  != null): ?>
	<div class="com_bookingforconnector-search-menu">
		<div class="com_bookingforconnector-view-changer">
			<div id="list-view" class="com_bookingforconnector-view-changer-list active"><i class="fa fa-list"></i> <?php echo _e('List' , 'bfi') ?></div>
			<div id="grid-view" class="com_bookingforconnector-view-changer-grid"><i class="fa fa-th-large"></i> <?php echo _e('Grid' , 'bfi') ?></div>
		</div>
	</div>
	<div class="bfi-clearfix"></div>
	
	<div class="com_bookingforconnector-search-resources com_bookingforconnector-items <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> com_bookingforconnector-list">
		<?php 
		foreach($resources as $resource) :  ?>
	<?php 
		$result = $resource; 
		$resource->Price = $result->MinPrice;
		$resourceImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
		$merchantLogoUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s1.jpeg";
		$resourceName = BFCHelper::getLanguage($result->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$resourceLat = '';
		$resourceLon = '';
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
		$showResourceMap = (($resourceLat != null) && ($resourceLon !=null) && $isMapVisible && $isMapMarkerVisible);

	
	
	$currUriresource = $uri.$resource->ResourceId.'-'.BFI()->seoUrl($resourceName);
	
	$resourceRoute = $currUriresource;
	$routeRating = $currUriresource.'/'._x('rating', 'Page slug', 'bfi' );
	$routeInfoRequest = $currUriresource.'/'._x('inforequestpopup', 'Page slug', 'bfi' );
	$routeRapidView = $currUriresource.'/'._x('rapidview', 'Page slug', 'bfi' );

	$routeMerchant = "";
	if($isportal){
		$routeMerchant = $url_merchant_page . $result->MerchantId .'-'.BFI()->seoUrl($result->MerchantName);
	}

	if(!empty($result->ImageUrl)){
		$resourceImageUrl = BFCHelper::getImageUrlResized('onsellunits',$result->ImageUrl, 'medium');
	}

	$merchantLogoUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s1.jpeg";
	if(!empty($resource->LogoUrl)){
		$merchantLogoUrl = BFCHelper::getImageUrlResized('merchant',$resource->LogoUrl, 'logomedium');
	}
?>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 com_bookingforconnector-item-col" >
			<div class="com_bookingforconnector-search-resource com_bookingforconnector-item <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
				<div class="mrcgroup" id="bfcmerchantgroup<?php echo $result->ResourceId; ?>"><span class="bfcmerchantgroup"></span></div>
				<div class="com_bookingforconnector-item-details  <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" >
						<div class="com_bookingforconnector-search-merchant-carousel com_bookingforconnector-item-carousel <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4">
							<div id="com_bookingforconnector-search-resource-carousel<?php echo $result->ResourceId; ?>" class="carousel">
								<div class="carousel-inner" role="listbox">
										<div class="item active"><img src="<?php echo $resourceImageUrl; ?>"></div>
								</div>
								<?php if($isportal): ?>
									<a class="bfi_logo-grid" href="<?php echo $routeMerchant?>" id="merchantname<?php echo $result->ResourceId?>"><div class="containerlogo"><img class="com_bookingforconnector-logo" id="bfi_logo-grid-<?php echo $result->ResourceId?>" src="<?php echo $merchantLogoUrl; ?>" /></div></a>
								<?php endif; ?>
							</div>
						</div>
						<div class="com_bookingforconnector-item-primary <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
							<div class="bfi_item-primary-name">
								<a href="<?php echo $resourceRoute ?>" id="nameAnchor<?php echo $result->ResourceId?>"><?php echo  $resourceName; ?></a>
							</div>
							<div class="bfi_item-primary-address">
								<?php if ($showResourceMap):?>
									<a href="javascript:void(0);" onclick="showMarker(<?php echo $result->ResourceId?>)"><span class="address<?php echo $result->ResourceId?>"></span></a>
								<?php endif; ?>
							</div>
							<span class="showcaseresource hidden" id="showcaseresource<?php echo $resource->ResourceId?>">
								<?php _e('Vetrina', 'bfi') ?> 
								<i class="fa fa-angle-double-up"></i>
							</span>
							<span class="topresource hidden" id="topresource<?php echo $resource->ResourceId?>">
								<?php _e('Top', 'bfi') ?>
								<i class="fa fa-angle-up"></i>
							</span>
							<span class="newbuildingresource hidden" id="newbuildingresource<?php echo $resource->ResourceId?>">
								<?php _e('New!', 'bfi') ?>
								<i class="fa fa-home"></i>
							</span>
							<div class="bfi_item-primary-address"> 
								<span class="bfi_phone"><a  href="javascript:void(0);" onclick="bookingfor.getData(urlCheck,'merchantid=<?php echo $result->MerchantId ?>&task=GetPhoneByMerchantId&language=' + cultureCode,this,'<?php echo  addslashes($result->MerchantName) ?>','PhoneView' )"  class="phone<?php echo $result->ResourceId?>"><?php _e('Show phone', 'bfi') ?></a></span>
								<a class="boxedpopup com_bookingforconnector_email" href="<?php echo $routeInfoRequest?>"  ><?php _e('Request info', 'bfi') ?></a>
							</div>
						</div><!--  COL 6-->
						<?php if($isportal): ?>
							<div class="com_bookingforconnector-item-secondary-logo <?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2">
								<a class="bfi_logo-list" id="merchantname2<?php echo $result->ResourceId?>" href="<?php echo $routeMerchant ?>"><img class="com_bookingforconnector-logo" src="<?php echo $merchantLogoUrl; ?>" id="bfi_logo-list-<?php echo $result->ResourceId?>" /></a>
							</div> <!--  COL 2-->
						<?php endif; ?>
					</div>
						<div class="bfi-clearfix"></div>

						<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> secondarysection"  style="padding-top: 10px !important;padding-bottom: 10px !important;">
							<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5 com_bookingforconnector-item-secondary-section-1 secondarysectionitem">	 
								<?php if (isset($resource->Rooms) && $resource->Rooms>0):?>
								<div class="com_bookingforconnector-search-resource-paxes com_bookingforconnector-item-secondary-paxes rooms<?php echo $result->ResourceId?>">
									<?php echo $resource->Rooms ?> <?php _e('Rooms', 'bfi') ?>
								</div>
								<?php endif; ?>
								<?php if (isset($resource->Area) && $resource->Area>0):?>
								<div class="com_bookingforconnector_merchantdetails-resource-area  ">
									<?php echo  $resource->Area ?> <?php _e('m&sup2;', 'bfi') ?>
								</div>
								<?php endif; ?>
							</div>

								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 com_bookingforconnector-item-secondary-section-2 secondarysectionitem">
									<div class="com_bookingforconnector-search-resource-details-price com_bookingforconnector-item-secondary-price">
										<span class="bfi-gray-highlight">&nbsp;</span>
										<div class="com_bookingforconnector-search-resource-details-stay-price com_bookingforconnector-item-secondary-stay-price">
											<span class="bfi-item-secondary-stay-total bfi_<?php echo $currencyclass ?>" style="margin-top: 12px;">
												<?php if ($resource->Price != null && $resource->Price > 0 && isset($resource->IsReservedPrice) && $resource->IsReservedPrice!=1 ) :?>
													<?php echo BFCHelper::priceFormat($resource->Price,0, ',', '.')?>
												<?php else: ?>
													<?php _e('Contact Agent', 'bfi') ?>
												<?php endif; ?>
											</span>
										</div>
									</div>
								</div>
								<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3 secondarysectionitem">
									<a href="<?php echo $resourceRoute ?>" class=" bfi-item-secondary-more"><?php _e('Details', 'bfi') ?></a>
								</div>
							</div>
		
			<div  class="ribbonnew hidden" id="ribbonnew<?php echo $resource->ResourceId?>"><?php _e('New ad', 'bfi') ?></div>
		</div>
				<div class="bfi-clearfix"><br /></div>
	  </div>
		<?php 
		$listsId[]= $result->ResourceId;
		?>
	<?php endforeach; ?>
	</div>
		<?php //if (!$isFromSearch && $pagination->get('pages.total') > 1) : ?>
		<?php if (!$isFromSearch && $pages > 1 ) : ?>
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
$url = $currURL; //$_SERVER['REQUEST_URI'];

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

  $paginate_links = paginate_links($pagination_args);
    if ($paginate_links) {
      echo "<nav class='custom-pagination'>";
//      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
      echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
      print $paginate_links;
      echo "</nav>";
    }	 ?>
			</div>
		<?php endif; ?>

<script type="text/javascript">
<!--
jQuery('#list-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12');
	jQuery('.com_bookingforconnector-item-carousel').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3');
	localStorage.setItem('display', 'list');
})

jQuery('#grid-view').click(function() {
	jQuery('.com_bookingforconnector-view-changer div').removeClass('active');
	jQuery(this).addClass('active');
	jQuery('.com_bookingforconnector-items').removeClass('com_bookingforconnector-list');
	jQuery('.com_bookingforconnector-items').addClass('com_bookingforconnector-grid');
	jQuery('.com_bookingforconnector-items > div').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-carousel').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-primary').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6');
	jQuery('.com_bookingforconnector-item-secondary-section-1').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>5');
	jQuery('.com_bookingforconnector-item-secondary-section-2').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4');
	jQuery('.com_bookingforconnector-item-secondary-section-3').removeClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3').addClass('<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>3');
	localStorage.setItem('display', 'grid');
})

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


var listToCheck = "<?php echo implode(",", $listsId) ?>";
var strAddressSimple = " ";
var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

var onsellunitDaysToBeNew = '<?php echo BFCHelper::$onsellunitDaysToBeNew ?>';
var nowDate =  new Date();
var newFromDate =  new Date();
newFromDate.setDate(newFromDate.getDate() - onsellunitDaysToBeNew); 
var listAnonymous = ",<?php echo COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE ?>,";

var strRatingNoResult = "<?php _e('Would you like to leave your review?', 'bfi') ?>";
var strRatingBased = "<?php _e('Score from %s reviews', 'bfi') ?>";
var strRatingValuation = "<?php _e('Guest Rating', 'bfi') ?>";

var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';

var listAnonymous = ",<?php echo COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE ?>,";
var shortenOption = {
		moreText: "<?php _e('Read more', 'bfi'); ?>",
		lessText: "<?php _e('Read less', 'bfi'); ?>",
		showChars: '250'
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
		var imgPath = "<?php echo $resourceLogoPath ?>";
		var imgPathError = "<?php echo $resourceLogoPathError ?>";

//		jQuery.getJSON(urlCheck + "?" + query, function(data) {
		jQuery.post(urlCheck, query, function(data) {
				jQuery.each(data || [], function(key, val) {

					$html = '';
	
					imgLogo="<?php echo $resourceImageUrl ?>";
					imgLogoError="<?php echo $resourceImageUrl ?>";
					
					imgMerchantLogo="<?php echo $merchantImageUrl ?>";
					imgMerchantLogoError="<?php echo $merchantImageUrl ?>";

				var addressData ="";
				var arrData = new Array();
				if (val.IsAddressVisible)
				{
					if(val.Address!= null && val.Address!=''){
						arrData.push(val.Address);
					}
				}
				if(val.LocationZone!= null && val.LocationZone!=''){
					arrData.push(val.LocationZone);
				}
				if(val.LocationName!= null && val.LocationName!=''){
					arrData.push(val.LocationName);
				}
				addressData = arrData.join(" - ");
				addressData = strAddressSimple + addressData;
				jQuery("#address"+val.ResourceId).append(addressData);

				if(listAnonymous.indexOf(","+val.MainMerchantCategoryId+",")<0){
//					var tmpHref = jQuery("#merchantname"+val.ResourceId).attr("href");
//					if (tmpHref && !tmpHref.endsWith("-"))
//					{
//						tmpHref += "-";
//					}
//					jQuery("#merchantname"+val.ResourceId).attr("href", tmpHref + make_slug(val.MerchantName));
//					jQuery("#logomerchant"+val.ResourceId).attr('src',imgMerchantLogo);
//					jQuery("#logomerchant"+val.ResourceId).attr('onerror',"this.onerror=null;this.src='" + imgMerchantLogoError + "';");

				}else{
					jQuery("#merchantname"+val.ResourceId).hide();
					jQuery("#merchantname2"+val.ResourceId).hide();
				}

				jQuery(".descr"+val.ResourceId).removeClass("com_bookingforconnector_loading");

<?php if($showdata): ?>
				if (val.Description!= null && val.Description != ''){
					$html += bookingfor.nl2br(jQuery("<p>" + bookingfor.stripbbcode(val.Description) + "</p>").text());
				}
				jQuery("#descr"+val.ResourceId).data('jquery.shorten', false);
				jQuery("#descr"+val.ResourceId).html($html);
				
				jQuery("#descr"+val.ResourceId).removeClass("com_bookingforconnector_loading");
				jQuery("#descr"+val.ResourceId).shorten(shortenOption);
<?php endif; ?>
					
				if(val.AddedOn!= null){
					var parsedDate = new Date(parseInt(val.AddedOn.substr(6)));
					var jsDate = new Date(parsedDate); //Date object				
					var isNew = jsDate > newFromDate;
					if (isNew)
						{
							jQuery("#ribbonnew"+val.ResourceId).removeClass("hidden");
						}
				}

				/* highlite seller*/
				if(val.IsHighlight){
							jQuery("#container"+val.ResourceId).addClass("com_bookingforconnector_highlight");
						}

				/*Top seller*/
				if (val.IsForeground)
					{
						jQuery("#topresource"+val.ResourceId).removeClass("hidden");
//						jQuery("#borderimg"+val.ResourceId).addClass("hidden");
					}

				/*Showcase seller*/
				if (val.IsShowcase)
					{
						jQuery("#topresource"+val.ResourceId).addClass("hidden");
						jQuery("#showcaseresource"+val.ResourceId).removeClass("hidden");
						jQuery("#lensimg"+val.ResourceId).removeClass("hidden");
//						jQuery("#borderimg"+val.ResourceId).addClass("hidden");
					}
				
				/*Top seller*/
				if(val.IsNewBuilding){
					jQuery("#newbuildingresource"+val.ResourceId).removeClass("hidden");
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
	jQuery('.mod_bookingformaps-static').click(function() {
     jQuery( "#mod_bookingformaps-popup" ).dialog({
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
//	  jQuery('#bookingforsearchFilterForm').submit();
	  jQuery('#searchformfilter').submit();
	});

});


//-->
</script>

	<?php else:?>
	<div class="com_bookingforconnector_merchantdetails-noresources">
		<?php _e('No results available', 'bfi') ?>
	</div>
	<?php endif?>
</div>
</div>
