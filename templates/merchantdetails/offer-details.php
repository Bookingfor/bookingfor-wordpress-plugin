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

/*---------------IMPOSTAZIONI SEO----------------------*/
	$payload["@type"] = "Organization";
	$payload["@context"] = "http://schema.org";
	$payload["name"] = $merchantName;
	$payload["description"] = $merchantDescriptionSeo;
	$payload["url"] = $routeSeo; 
	if (!empty($merchant->LogoUrl)){
		$payload["logo"] = "https:".BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
	}
/*--------------- FINE IMPOSTAZIONI SEO----------------------*/

?>
<script type="application/ld+json">// <![CDATA[
<?php echo json_encode($payload,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>
// ]]></script>
<div class="bfi-content">
	<?php if (!empty($offer)){ ?>
	<div>
		<?php
			$offer->OfferId =  $offer->VariationPlanId;
			$currvariationPlanId = $offer->VariationPlanId;
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
		<div class="bfi-title-name"><h1><?php echo  $offer->Name?></h1> </div>
		<div class="bfi-clearfix "></div>
	
		<ul class="bfi-menu-top">
			<!-- <li><a rel=".bfi-resourcecontainer-gallery"><?php echo  _e('Media' , 'bfi') ?></a></li> -->
			<?php if (!empty($offer->Description)){?><li><a rel=".bfi-description-data" ><?php echo  _e('Description', 'bfi') ?></a></li><?php } ?>
			<li class="bfi-book" ><a rel="#divcalculator"><?php echo  _e('Booking' , 'bfi') ?></a></li>
		</ul>
	
		<div class="bfi-resourcecontainer-gallery">
	<?php  
			$bfiSourceData = 'variationplans';
			$bfiImageData = null;
			$bfiVideoData = null;
			if(!empty($offer->Images)) {
				$bfiImageData = $offer->Images;
			}
			if(!empty($offer->VideoData)) {
				$bfiVideoData = $offer->VideoData;
			}
//			include(BFI()->plugin_path().'/templates/gallery.php');
			bfi_get_template("gallery.php",array("merchant"=>$merchant,"bfiSourceData"=>$bfiSourceData,"bfiImageData"=>$bfiImageData,"bfiVideoData"=>$bfiVideoData));	
	?>

		</div>
		<?php if (!empty($offer->Description) || (isset($resource->AttachmentsString) && !empty($resource->AttachmentsString)) ){?>
		<div class="bfi-description-data bfi-row">
			<div class="bfi-col-md-8 bfi-description-data">
				<?php echo BFCHelper::getLanguage($offer->Description, $language, null, array( 'striptags'=>'striptags', 'bbcode'=>'bbcode','ln2br'=>'ln2br')); ?>
			</div>
			<div class="bfi-col-md-4">
				<div class=" bfi-feature-data">
					<strong><?php _e('In short', 'bfi') ?></strong>
					<?php if(isset($resource->AttachmentsString) && !empty($resource->AttachmentsString)){
						?>
						<div  class="bfi-attachmentfiles">
						<?php 
									
						$resourceAttachments = json_decode($resource->AttachmentsString);
						
						foreach ($resourceAttachments as $keyAttachment=> $resourceAttachment) {
							if ($keyAttachment>COM_BOOKINGFORCONNECTOR_MAXATTACHMENTFILES) {
								break;
							}
							$resourceAttachmentName = $resourceAttachment->Name;
							$resourceAttachmentExtension= "";
							
							$path_parts = pathinfo($resourceAttachmentName);
							if(!empty( $path_parts['extension'])){
								$resourceAttachmentExtension = $path_parts['extension'];
								$resourceAttachmentName =  str_replace(".".$resourceAttachmentExtension, "", $resourceAttachmentName);
							}
							$resourceAttachmentIcon = bfi_get_file_icon($resourceAttachmentExtension);
							?>
							<?php echo $resourceAttachmentIcon ?> <a href="<?php echo $resourceAttachment->LinkValue ?>" target="_blank"><?php echo $resourceAttachmentName ?></a><br />
							<?php 
						}
					?>
						</div>
					<?php } ?>
				</div>
				<!-- AddToAny BEGIN -->
				<a class="bfi-btn bfi-alternative2 bfi-pull-right a2a_dd"  href="http://www.addtoany.com/share_save" ><i class="fa fa-share-alt"></i> <?php _e('Share', 'bfi') ?></a>
				<script async src="https://static.addtoany.com/menu/page.js"></script>
				<!-- AddToAny END -->
		</div>
		<?php } ?>
		<div class="bfi-clearfix "></div>
			<a name="calc"></a>
			<div id="divcalculator">
				<?php 
				$resourceId = 0;
				$condominiumId = 0;
				bfi_get_template("search_details.php",array("merchant"=>$merchant,"resourceId"=>$resourceId,"condominiumId"=>$condominiumId,"currvariationPlanId"=>$currvariationPlanId,"currencyclass"=>$currencyclass));	

				//include(BFI()->plugin_path().'/templates/search_details.php'); //merchant temp ?>
					

			</div>
	</div>
	
	<?php }else{?>
	<div class="com_bookingforconnector_merchantdetails-nooffers">
		<?php echo _e('No Results Found', 'bfi'); ?>
	</div>
	<?php } ?>

	<div class="bfi-clearfix"></div>
<?php
bfi_get_template("merchant_small_details.php",array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant));	
?>

</div>
<script type="text/javascript">
jQuery(function($)
		{
			jQuery('.bfcmenu li a').click(function(e) {
				e.preventDefault();
				jQuery('html, body').animate({ scrollTop: jQuery(jQuery(this).attr("rel")).offset().top }, 2000);
			});
			
		});

</script>