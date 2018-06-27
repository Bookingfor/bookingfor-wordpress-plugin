<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
if ($isportal != 1){
	exit; 
}

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url = "/" .ICL_LANGUAGE_CODE;
		}
}


$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );

$cols = !empty($instance['itemspage'])? $instance['itemspage']: 4;
$tags =  $instance['tags']; 
$maxitems = !empty($instance['maxitems'])? $instance['maxitems']: 10; 
$descmaxchars = !empty($instance['descmaxchars'])? $instance['descmaxchars']: 300; 

if(!empty($tags)) {

$merchants = BFCHelper::getMerchantsExt($tags, 0, $maxitems);

if(!empty($merchants)){
if (count($merchants)<$cols) {
    $cols = count($merchants);
}
$carouselid = uniqid();

$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
	$cols = 1;
}


?>
<?php 
echo $before_widget;
// Check if title is set
if ( $title ) {
  echo $before_title . $title . $after_title;
}


?>

<div id="<?php echo $carouselid; ?>" class="bookingfor_carousel" >
		<?php foreach($merchants as $mrcKey => $merchant): ?>
		<?php 
		
			$hasSuperior = !empty($merchant->RatingSubValue);
			$rating = $merchant->Rating;
			if ($rating > 9)
			{
				$hasSuperior = ($merchant->Rating%10)>0;
				$rating = (int)($rating / 10);
			} 
			$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);
			$currMerchantImageUrl = $merchantImageUrl;
			if(!empty($merchant->DefaultImg)){
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$merchant->DefaultImg, 'medium');
			}
			if(!empty($merchant->ImageData)) {
				$images = explode(",", $merchant->ImageData);
				$currMerchantImageUrl = BFCHelper::getImageUrlResized('merchant',$images[0], 'medium');
			}
			$merchantDescription = BFCHelper::getLanguage($merchant->Description, $language, null, array('bbcode'=>'bbcode', 'striptags'=>'striptags'));
			$merchantDescription = BFCHelper::shorten_string($merchantDescription, $descmaxchars);
			$merchantDescription = BFCHelper::getLanguage($merchantDescription, $language, null, array('ln2br'=>'ln2br'));

		?>
			<div class="bfi-bookingforconnector-merchants" >
				<div class="bfi-row" >
					<div class="bfi-row" >
						<div class="bfi-col-md-12"><a href="<?php echo $routeMerchant?>" class="eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>" data-category="<?php echo $merchantCategoryNameTrack; ?>"><img src="<?php echo $currMerchantImageUrl; ?>" class="bfi-img-responsive center-block" /></a>
						</div>
					</div>
					<div class="bfi-row" >
						<div class="bfi-col-md-12 bfi-item-title" style="padding: 10px!important;">
						<a class="eectrack" href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack?>" data-category="<?php echo $merchantCategoryNameTrack; ?>"><?php echo  $merchant->Name ?></a> 
						<?php if($rating > 0){ ?>
								<span class="bfi-item-rating">
									<?php for($i = 0; $i < $rating; $i++) { ?>
										<i class="fa fa-star"></i>
									<?php } ?>	             
									<?php if ($hasSuperior) { ?>
										&nbsp;S
									<?php } ?>
								</span>
						<?php } ?>
						</div>
					</div>
					<div class="bfi-row bfi-hide" >
						<div class="bfi_merchant-description bfi-col-md-12" style="padding-left: 10px!important;padding-right: 10px!important;">
							<a href="<?php echo $routeMerchant ?>" id="nameAnchor<?php echo $merchant->MerchantId?>" class="eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack?>" data-category="<?php echo $merchantCategoryNameTrack; ?>"><?php echo  $merchant->Name ?></a> 
						</div>
					</div>
					<div class="bfi-row" >
						<div class="bfi_merchant-description bfi-col-md-12" style="padding: 10px!important;" id="descr<?php echo $merchant->MerchantId?>"><?php echo $merchantDescription;?></div>
					</div>
					<div class="bfi-row secondarysection">
						<div class="bfi-col-md-1  secondarysectionitem">
							&nbsp;
						</div>
						<div class="bfi-col-md-11  secondarysectionitem" style="padding: 10px!important;">
								<a href="<?php echo $routeMerchant?>" class="bfi-btn bfi-pull-right eectrack" data-type="Merchant" data-id="<?php echo $merchant->MerchantId?>" data-index="<?php echo $mrcKey?>" data-itemname="<?php echo $merchantNameTrack; ?>" data-brand="<?php echo $merchantNameTrack; ?>"  data-category="<?php echo $merchantCategoryNameTrack; ?>"><?php _e('Details', 'bfi') ?></a>
						</div>
					</div>
				</div>
			</div>		
		<?php endforeach; ?>
</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function() {
	var ncolslick = <?php echo $cols ?>;
	if(jQuery('#<?php echo $carouselid; ?>').width()<400 && <?php echo $cols ?>>2){
		ncolslick = 2;
	}
	if(jQuery('#<?php echo $carouselid; ?>').width()<200 && <?php echo $cols ?>>1){
		ncolslick = 1;
	}

	jQuery('#<?php echo $carouselid; ?>').slick({
		dots: false,
		draggable: false,
		arrows: true,
		infinite: true,
		slidesToShow: ncolslick,
		slidesToScroll: 1,
	});

	jQuery('#<?php echo $carouselid; ?>').on('setPosition', function(event, slick){
		var maxHeight = 0;
		jQuery('.com_bookingforconnector-item-col', jQuery(slick.$slider ))
		.each(function() { maxHeight = Math.max(maxHeight, jQuery(this).height()); })
		.height(maxHeight);
	});

});
//-->
</script>
<?php 
} //end empty merchansts
} //end empty tags
?>

