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

$merchantSiteUrl = '';
if ($merchant->AddressData->SiteUrl != '') {
	$merchantSiteUrl =$merchant->AddressData->SiteUrl;
	$parsed = parse_url($merchantSiteUrl);
	if (empty($parsed['scheme'])) {
		$merchantSiteUrl = 'http://' . ltrim($merchantSiteUrl, '/');
	}
//	if (strpos('http://', $merchantSiteUrl) == false) {
//		$merchantSiteUrl = 'http://' . $merchantSiteUrl;
//	}
}
$metodForm = "";
if (strpos($merchantSiteUrl,'%3f')!==false || strpos($merchantSiteUrl,'?')!==false ){
	$metodForm = "post";
}
if (strpos($merchantSiteUrl,'?post')!==false ){
	$metodForm = "post";
	$merchantSiteUrl = str_replace("?post", "", $merchantSiteUrl);
}


?>

<style>
	body#bd{
		background-color: #ffffff;
		background-image:none;
	}
</style>
<div class="bfi-content">
<div style="text-align:center;">

	<form method="<?php echo $metodForm?>" action="<?php echo $merchantSiteUrl?>" id="redirectfromsite" name="redirectfromsite">
		<?php _e('please wait...', 'bfi') ?>
		<a href="<?php echo $merchantSiteUrl?>"><?php echo $merchantSiteUrl?></a> 
		<br /><br />
<script type="text/javascript">
<!--
if (typeof(ga) !== 'undefined') {
	ga('send', 'event', 'Bookingfor', 'Website', '<?php echo $merchantSiteUrl?>');
	ga(function(){
		function gred(id){return document.getElementById?document.getElementById(id  ):document.all(id);}
		gred("redirectfromsite").submit();
	});
}else{
		function gred(id){return document.getElementById?document.getElementById(id  ):document.all(id);}
		gred("redirectfromsite").submit();
}
//-->
</script>
	</form>

</div>
</div>