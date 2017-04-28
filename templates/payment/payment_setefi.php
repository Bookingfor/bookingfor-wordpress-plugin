<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * Setefy System Form
 * ================
 * 
 * */
$order = $item->order;

$merchantPayment = $item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;
$donation = false;


$paymentData = $merchantPayment->Data;

$url = $url_payment_page .'?actionmode=setefiServer&payedOrderId=' . $order->OrderId;
$urlerror = $url_payment_page .'?actionmode=error&payedOrderId=' . $order->OrderId;

//$uri                    = JURI::getInstance();
//$urlBase = $uri->toString(array('scheme', 'host', 'port'));

//$urlerror =  $urlBase . JRoute::_('index.php?view=payment&actionmode=errordonation&payedOrderId=' . $order->OrderId);
//$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=setefiServer&payedOrderId=' . $order->OrderId);

$suffixOrder = "";
$overrideAmount = 0;
if (isset($item->paymentCount)) {
	$suffixOrder = (string)($item->paymentCount +1) ;
	$overrideAmount = $item->overrideAmount;
}

if ($actionmode=='donation')
{
	$donation = true;
	$urlerror = $url_payment_page .'?actionmode=errordonation&payedOrderId=' . $order->OrderId;
}

$setefi = new BFI_setefi($merchantPayment->Data, $order, $language, $urlerror, $url, $suffixOrder,$overrideAmount , $SandboxMode,$donation);

?>
<script type="text/javascript">
<!--
	document.location = '<?php echo $setefi->requestUrl; ?>';
//-->
</script>
