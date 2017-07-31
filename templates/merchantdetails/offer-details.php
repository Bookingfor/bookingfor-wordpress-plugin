<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

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

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;

$routeThanks = $routeMerchant .'/'. _x('thanks', 'Page slug', 'bfi' );
$routeThanksKo = $routeMerchant .'/'. _x('errors', 'Page slug', 'bfi' );

//$privacy = BFCHelper::GetPrivacy($language);

?>
<div class="bfi-content">
	<?php //include('merchant-head.php'); ?>
	<?php if (!empty($offer)){ ?>
	<div>
		<?php
			$offer->OfferId =  $offer->VariationPlanId;
//			$offer->Price = $offer->Value;
			$formRoute = $routeMerchant . '/?task=getMerchantResources&variationPlanId=' . $offer->OfferId;

			$offerName = BFCHelper::getLanguage($offer->Name, $language);
				
			$img = plugins_url("images/default.png", dirname(__FILE__));
			$imgError = plugins_url("images/default.png", dirname(__FILE__));

			if ($offer->DefaultImg != ''){
				$img = BFCHelper::getImageUrlResized('offers',$offer->DefaultImg , 'offer_list_default');
				$imgError = BFCHelper::getImageUrl('offers',$offer->DefaultImg , 'offer_list_default');
			}elseif ($merchant->LogoUrl != ''){
				$img = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'resource_list_default_logo');
				$imgError = BFCHelper::getImageUrl('merchant',$merchant->LogoUrl, 'resource_list_default_logo');

			}
		?>
		<div class="bfi-title-name"><?php echo  $offer->Name?> </div>
		<div class="bfi-clearfix "></div>
	
		<ul class="bfi-menu-top">
			<!-- <li><a rel=".bfi-resourcecontainer-gallery"><?php echo  _e('Media' , 'bfi') ?></a></li> -->
			<?php if (!empty($offer->Description)){?><li><a rel=".bfi-description-data" ><?php echo  _e('Description', 'bfi') ?></a></li><?php } ?>
			<li class="bfi-book" ><a rel="#divcalculator"><?php echo  _e('Booking' , 'bfi') ?></a></li>
		</ul>
	
		<div class="bfi-resourcecontainer-gallery">
			<?php 
			$images = array();
			$contextImg ="variationplans";
			if(!empty($offer->Images)) {
			  $strImg = str_replace(' ', '', $offer->Images);
			  foreach(explode(',', $strImg) as $image) {
				  $images[] = array('type' => 'image', 'data' => $image);
			  }
			}
			?>
			<?php  include('gallery.php');  ?>

		</div>
		<?php if (!empty($offer->Description)){?>
		<div class="bfi-description-data bfi-row">
			<div class="bfi-description-data bfi-col-md-12">
				<?php echo $offer->Description ?>		
			</div>
		</div>
		<?php } ?>
		<div class="bfi-clearfix "></div>
	
		<a name="calc"></a>
		<div id="divcalculator"><div style="padding:10px;text-align:center;"><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span></div></div>
	</div>
	
	<?php }else{?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo _e('No Results Found', 'bfi'); ?>
	</div>
	<?php } ?>

	<div class="bfi-clearboth"></div>
	<?php  include(BFI()->plugin_path().'/templates/merchant_small_details.php');  ?>

</div>
<script type="text/javascript">
<!--
var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";	
var cultureCode = '<?php echo $language ?>';
var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
//-->
</script>
<script type="text/javascript">
jQuery(function($)
		{
			jQuery('.bfcmenu li a').click(function(e) {
				e.preventDefault();
				jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
			});
			
			//$("#firstresources").hide();
			
			$("#divcalculator").load('<?php echo $formRoute?>', function() {
			});
		});

</script>