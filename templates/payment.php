<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
global $post;

$orderid = get_query_var( 'orderid', BFCHelper::getVar('payedOrderId',0) );

$actionmode = BFCHelper::getVar('actionmode',"");
$model = new BookingForConnectorModelPayment;
$model->populateState();

//$item = $model->getItem($orderid);
$lastPayment = $model->GetLastOrderPayment($orderid);

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

?>
<?php

get_header( 'payment' );
?>
 <?php
		/**
		 * bookingfor_before_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked bookingfor_breadcrumb - 20
		 */
		do_action( 'bookingfor_before_main_content' );

	?>
	
	<h1 class="page-title"><?php _e('Payment', 'bfi') ?></h1>
<?php
$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
$url_cart_page = get_permalink( $cartdetails_page->ID );

$redirect = $url_cart_page . _x('thanks', 'Page slug', 'bfi' );
$redirecterror = $url_cart_page . _x('errors', 'Page slug', 'bfi' );

$errorPayment = false;
$invalidate=0;
$errorCode ="0";

if (empty($lastPayment) || $lastPayment->PaymentType!=3 || ($lastPayment->Status!=1 && $lastPayment->Status!=3 && $lastPayment->Status!=7 && $lastPayment->Status!=0 && $lastPayment->Status!=4 && $lastPayment->Status!=5 && $lastPayment->Status!=22 )) {
    $errorPayment= true;
	$errorCode ="1";

}
if($lastPayment->Status==1 ||$lastPayment->Status==3 || $lastPayment->Status==7 ){
	$invalidate=1;
}
if ($lastPayment->Status==5 ) {
    $errorPayment= true;
	$errorCode ="2";
}

		
		$paymentUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PAYMENTURL).$orderid."/".$lastPayment->OrderPaymentId;
		$typeMode="hidden";


	if ($errorPayment) {
			$redirecterror .= '?errorCode='.$errorCode;
			header( 'Location: ' . $redirecterror  );
			exit();
	}
		
?>
Se non verrà rediretto alla pagina del pagamento entro pochi secondi, clicchi il pulsante seguente:<br />
<form action="<?php echo $paymentUrl?>" method="post" id="bfi_paymentform">
	<input id="urlok" name="urlok" type="<?php echo $typeMode ?>" title="urlok" value="<?php echo $redirect?>" />
	<input id="urlko" name="urlko" type="<?php echo $typeMode ?>" title="urlko"  value="<?php echo $redirecterror ?>" />
	<input id="invalidate" name="invalidate" type="<?php echo $typeMode ?>" title="urlok" value="<?php echo $invalidate?>" />
	<input type="submit" value="Invia">
</form>
<script type="text/javascript">
<!--
		jQuery(function($) {
			jQuery("#bfi_paymentform").submit();
		});
//-->
</script>
	<?php
		/**
		 * bookingfor_after_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'bookingfor_after_main_content' );
	?>	
	<?php
		/**
		 * bookingfor_sidebar hook.
		 *
		 * @hooked bookingfor_get_sidebar - 10
		 */
//		do_action( 'bookingfor_sidebar' );
	?>
<?php get_footer( 'orderdetails' ); ?>
