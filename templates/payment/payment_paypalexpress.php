<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * PaypalExpress System Form
 * ================
 * 
 * */
//$order = $item->order;
//$merchantPayment = $item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;

$donation = false;


$paymentData = $merchantPayment->Data;

$url = $url_payment_page .'?actionmode=paypalexpress&payedOrderId=' . $order->OrderId;
$urlBack = $url;

//$urlBack = $urlBase . JRoute::_('index.php?view=payment&actionmode=cancel&payedOrderId=' . $order->OrderId);
//$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=paypalexpress&payedOrderId=' . $order->OrderId);
//$urlBack = $url;

$suffixOrder = "";
if (isset($item->paymentCount)) {
	$suffixOrder = (string)($item->paymentCount +1) ;
	$overrideAmount = $item->overrideAmount;
}

if ($actionmode=='donation')
{
	$donation = true;
}

$paypalExpress = new BFI_paypalExpress($merchantPayment->Data, $order, $language, $urlBack, $url,$SandboxMode,$donation);


?><?php echo $paypalExpress->getUrl(); ?>