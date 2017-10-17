<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$totalResult = count($merchants);
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url = "/" .ICL_LANGUAGE_CODE;
		}
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;

$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );

$startswith = !empty($currParam['startswith'])?$currParam['startswith']:'';

$currURL = esc_url( get_permalink() ); 
$formAction = $currURL;
//$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
//$page = (get_query_var('page')) ? get_query_var('page') : 1;
$page = bfi_get_current_page() ;

$pages = 0;
if($total>0){
	$pages = ceil($total / COM_BOOKINGFORCONNECTOR_ITEMPERPAGE);
}

$currSorting= $filter_order . "|" . $filter_order_Dir;

?>
<?php if (count($merchants)>0) : ?>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
//-->
</script>
<div class="bfi-content">
<div class="bfi-search-menu">
	<form action="<?php echo $formAction; ?>" method="post" name="bookingforsearchForm" id="bookingforsearchFilterForm">
			<input type="hidden" class="filterOrder" name="filter_order" value="<?php echo $filter_order ?>" />
			<input type="hidden" class="filterOrderDirection" name="filter_order_Dir" value="<?php echo $filter_order_Dir ?>" />
			<input type="hidden" name="searchid" value="<?php //echo   $searchid ?>" />
			<input type="hidden" name="startswith" id="startswith" value="<?php echo $startswith ?>" />
			<input type="hidden" name="limitstart" value="0" />
	</form>
	<div class="bfi-results-sort">
		<span class="bfi-sort-item"><?php echo _e('Order by' , 'bfi')?>:</span>
		<span class="bfi-sort-item <?php echo $currSorting=="Name|asc" ? "bfi-sort-item-active": "" ; ?>" rel="Name|asc" ><?php _e('A-Z', 'bfi') ?></span>
		<span class="bfi-sort-item <?php echo $currSorting=="Name|desc" ? "bfi-sort-item-active": "" ; ?>" rel="Name|desc" ><?php _e('Z-A', 'bfi') ?></span>
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
	<?php $listResourceIds = array(); ?>  
	<?php foreach ($merchants as $merchant): ?>
		<?php 
			$rating = $merchant->Rating;
			if ($rating>9 )
			{
				$rating = $rating/10;
			} 
			$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
			$routeRating = $routeMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
			$routeInfoRequest = $routeMerchant .'/'._x('contactspopup', 'Page slug', 'bfi' );
			
			$counter = 0;
			$merchantLat = $merchant->XPos;
			$merchantLon = $merchant->YPos;
			$showMerchantMap = (($merchantLat != null) && ($merchantLon !=null));
			$showMerchantMap = false;
			$merchantLogo = BFI()->plugin_url() . "/assets/images/defaults/default-s1.jpeg";
			$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
			
			if(!empty($merchant->LogoUrl)){
				$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logomedium');
				$merchantLogoError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'logomedium');
			}
			if(!empty($merchant->DefaultImg)){
				$merchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->DefaultImg, 'medium');
			}
			
			$merchantSiteUrl = '';
			if ($merchant->SiteUrl != '') {
				$merchantSiteUrl =$merchant->SiteUrl;
				if (strpos('http://', $merchantSiteUrl) == false) {
					$merchantSiteUrl = 'http://' . $merchantSiteUrl;
				}
				$merchantSiteUrlstripped = str_replace('http://', "", $merchantSiteUrl);
				if (strpos($merchantSiteUrlstripped,'?') !== false) {
					$tmpurl = explode("?",$merchantSiteUrlstripped);
					$merchantSiteUrlstripped = $tmpurl[0];
				}
			}
			$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
			$merchantDescription = BFCHelper::getLanguage($merchant->Description, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

		?>


	<div class="bfi-col-sm-6 bfi-item">
		<div class="bfi-row bfi-sameheight" >
			<div class="bfi-col-sm-3 bfi-img-container">
				<a href="<?php echo $routeMerchant ?>" style='background: url("<?php echo $merchantImageUrl; ?>") center 25% / cover;'><img src="<?php echo $merchantImageUrl; ?>" class="bfi-img-responsive" /></a> 
			</div>
			<div class="bfi-col-sm-9 bfi-details-container">
				<!-- merchant details -->
				<div class="bfi-row" >
					<div class="bfi-col-sm-10">
						<div class="bfi-item-title">
							<a href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" target="_blank"><?php echo  $merchantName ?></a> 
							<span class="bfi-item-rating">
								<?php for($i = 0; $i < $rating; $i++) { ?>
									<i class="fa fa-star"></i>
								<?php } ?>	             
							</span>
						</div>
						<div class="bfi-item-address">
							<?php if ($showMerchantMap){?>
							<a href="javascript:void(0);" onclick="showMarker(<?php echo $merchant->MerchantId?>)"><?php }?><span id="address<?php echo $merchant->MerchantId?>"></span><?php if ($showMerchantMap){?></a>
							<?php } ?>
						</div>
						<div class="bfi-mrcgroup" id="bfitags<?php echo $merchant->MerchantId; ?>"></div>
						<div class="bfi-description"><?php echo $merchantDescription ?></div>
					</div>
					<div class="bfi-col-sm-2 bfi-text-right">
						<?php if ($isportal && ($merchant->RatingsContext ==1 || $merchant->RatingsContext ==3)):?>
								<div class="bfi-avg">
								<?php if ($merchant->MrcAVGCount>0){
									$totalInt = BFCHelper::convertTotal(number_format((float)$merchant->MrcAVG, 1, '.', ''));

									?>
									<a class="bfi-avg-value" href="<?php echo $routeRating ?>" ><?php echo $rating_text['merchants_reviews_text_value_'.$totalInt] . " " . number_format((float)$merchant->MrcAVG, 1, '.', '') ?></a><br />
									<a class="bfi-avg-count" href="<?php echo $routeRating ?>" ><?php echo sprintf(__('%s reviews' , 'bfi'),$merchant->MrcAVGCount) ?></a>
								<?php }else{ ?>
									<!-- <a class="bfi-avg-leaverating " href="<?php echo $routeRatingform ?>"><?php _e('Would you like to leave your review?', 'bfi') ?></a> -->
								<?php } ?>
								</div>
						<?php endif; ?>
					</div>
				</div>
				<div class="bfi-clearfix bfi-hr-separ"></div>
				<!-- end merchant details -->
					<div class=" bfi-text-right">
							<a href="<?php echo $routeMerchant ?>" class="bfi-btn"><?php echo _e('Details' , 'bfi')?></a>
					</div>
				<div class="bfi-clearfix"></div>
			</div>
		</div>
	</div>
		<?php $listsId[]= $merchant->MerchantId; ?>
	<?php endforeach; ?>
</div>
</div>
<?php
  
  $url = $currURL ;
//$fragment = "&filter_order=" . $filter_order. "&filter_order_Dir=" . $filter_order_Dir;
  $pagination_args = array(
    'base'            => $url. '%_%',
    'format'          => '?page=%#%',
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
    'add_fragment'    => '' //$fragment
  );

  $paginate_links = paginate_links($pagination_args);
    if ($paginate_links) {
      echo "<nav class='bfi-pagination'>";
//      echo "<span class='page-numbers page-num'>Page " . $page . " of " . $numpages . "</span> ";
      echo "<span class='page-numbers page-num'>".__('Page', 'bfi')." </span> ";
      print $paginate_links;
      echo "</nav>";
    }
 ?>
<script type="text/javascript">
<!--

jQuery(document).ready(function() {
	jQuery('.bfi-sort-item').click(function() {
	  var rel = jQuery(this).attr('rel');
	  var vals = rel.split("|"); 
	  jQuery('#bookingforsearchFilterForm .filterOrder').val(vals[0]);
	  jQuery('#bookingforsearchFilterForm .filterOrderDirection').val(vals[1]);
	  jQuery('#bookingforsearchFilterForm').submit();
	});
});

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


	jQuery('.inforequest').bind('click', function() {
		var merchantid = jQuery(this).attr('rel');
		var id = jQuery(this).attr('id');
		bookingfor.waitBlockUI();
//		jQuery.blockUI({ message: '<h2>Processing</h2>' }); 
		var queryMG = '<?php echo $base_url; ?>/get-inforequest-form?merchantid='+merchantid;
		jQuery.getJSON(queryMG, function(data) {
			jQuery.unblockUI();
			jQuery(data).dialog({
				title: jQuery(data).find('.merchant-name').val(),
				close: function(event, ui) {
					jQuery(this).dialog("close");
					jQuery(this).remove();
				},
				'width' : '80%'
			});
			attachDatepicker(data);
		});
	});


var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";
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
					jQuery.each(JSON.parse(data) || [], function(key, val) {
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
	
//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {
		var eecitems = [];

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
			jQuery(".eectrack[data-id=" + val.MerchantId + "]").attr("data-category", val.MainCategoryName);
			eecitems.push({
				id: "" + val.MerchantId + " - Merchant",
				name: val.Name,
				category: val.MainCategoryName,
				brand: val.Name,
				position: key
			});
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
		<?php if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1): ?>
			<?php if(isset($analiticsSubject)): ?>
				callAnalyticsEEc("addImpression", eecitems, "list", <?php echo $analiticsSubject; ?>);
			<?php else: ?>
				callAnalyticsEEc("addImpression", eecitems, "list");
			<?php endif; ?>
		<?php endif; ?>
	},'json');
}


jQuery(document).ready(function() {
	getAjaxInformations();
<?php if(!isset($nopopupmap)):  ?>
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
<?php endif; ?>
	jQuery(".bfi-description").shorten(shortenOption);

});

//-->
</script>
<?php endif; ?>
