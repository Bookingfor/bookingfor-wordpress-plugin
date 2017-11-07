<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url = "/" .ICL_LANGUAGE_CODE;
		}
}

$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
$url_cart_page = get_permalink( $cartdetails_page->ID );
$currentCartsItems = BFCHelper::getSession('totalItems', 0, 'bfi-cart');
?>
<a href="<?php echo $url_cart_page ?>" class="bfi-shopping-cart bfi-shopping-cart-widget"><i class="fa fa-shopping-cart "></i> <span class="bfibadge" style="<?php echo (COM_BOOKINGFORCONNECTOR_SHOWBADGE) ?"":"display:none"; ?>"><?php echo ($currentCartsItems>0) ?$currentCartsItems:"";?></span><?php _e('Cart', 'bfi') ?></a>
<div class="bfi-hide bfimodalcart">
	<div class="bfi-title"><?php _e('Cart', 'bfi') ?></div>
	<div class="bfi-body"><!-- <?php _e('Add to cart', 'bfi') ?> --></div>
	<div class="bfi-footer">
		<span class="bfi-btn bfi-alternative" onclick="jQuery('.bfi-shopping-cart').webuiPopover('destroy');"><?php _e('Continue shopping', 'bfi') ?></span>
		<span onclick="javascript:window.location.assign('<?php echo $url_cart_page ?>')" class="bfi-btn">Checkout</span>
	</div>
</div><!-- /.modal -->
