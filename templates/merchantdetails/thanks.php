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

?>
<div class="bfi-content">
<br />
<?php _e('Thank you for having chosen us.', 'bfi') ?>
<div class="bfi-clearfix"></div>
<?php  
bfi_get_template("merchant_small_details.php",array("merchant"=>$merchant,"routeMerchant"=>$routeMerchant));	
?>
</div>
