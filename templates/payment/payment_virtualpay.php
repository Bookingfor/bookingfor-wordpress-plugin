<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * VirtualPay System Form
 * ================
 * 
 * */
$order = $this->item->order;
$merchantPayment = $this->item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;
$typeMode = $debugmode?'text':'hidden'; 
//if($debugmode){
//	$order->OrderId = 'ORD00023';
//	$order->ExternalId = 'cod1';
//}

$uri                    = JURI::getInstance();
$urlBase = $uri->toString(array('scheme', 'host', 'port'));


//$urlBack = $urlBase . JRoute::_('index.php?view=payment&actionmode=cancel&payedOrderId=' . $order->OrderId);
$urlBack = $urlBase . JRoute::_('index.php?view=orders');
$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=virtualpay&payedOrderId=' . $order->OrderId);

if ($this->actionmode=="donation"){
	$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=virtualpay&payedOrderId=donation' . $order->OrderId);
	$urlBack =  $urlBase . JRoute::_('index.php?view=payment&actionmode=errordonation');
}
//
//if ($this->actionmode=='donation'){
//	$urlBack = $urlBase . JRoute::_('index.php?view=orders&donation=1');
//}

//$urlBack = $url;
$virtualpay = new virtualpay($merchantPayment->Data, $order, $this->language, $urlBack, $url);

if($SandboxMode){
	$virtualpay->separatori  = '';
	$virtualpay->urlack = '';
	$virtualpay->urlnack = '';
}

?>
<?php if($debugmode):?>	
<strong>TEST virtualpay</strong><br />

Caparra da pagare <?php echo $order->DepositAmount ?><br />

<form action="<?php echo $virtualpay->paymentUrl ?>" method="post">
	MERCHANT_ID: <input id="MERCHANT_ID" name="MERCHANT_ID" type="<?php echo $typeMode ?>" title="merchant_id" value="<?php echo $virtualpay->merchant_id ?>" /> <br />
	ORDER_ID: <input id="ORDER_ID" name="ORDER_ID" type="<?php echo $typeMode ?>" title="order_id" value="<?php echo $virtualpay->order_id ?>" /> <br />
	IMPORTO: <input id="IMPORTO" name="IMPORTO" type="<?php echo $typeMode ?>" title="importo" value="<?php echo $virtualpay->importo ?>" /> <br />
	DIVISA: <input id="DIVISA" name="DIVISA" type="<?php echo $typeMode ?>" title="divisa" value="<?php echo $virtualpay->divisa ?>" /> <br />
	ABI: <input id="ABI" name="ABI" type="<?php echo $typeMode ?>" title="ABI" value="<?php echo $virtualpay->abi ?>" /> <br />
	ITEMS: <input id="ITEMS" name="ITEMS" type="<?php echo $typeMode ?>" title="items"  value="<?php echo $virtualpay->items ?>" /> <br />
	EMAIL: <input id="EMAIL" name="EMAIL" type="<?php echo $typeMode ?>" title="email" value="<?php echo $virtualpay->email ?>" /> <br />
	LINGUA: <input id="LINGUA" name="LINGUA" type="<?php echo $typeMode ?>" title="lingua" value="<?php echo $virtualpay->lingua ?>" /> <br />
	URLOK: <input id="URLOK" name="URLOK" type="<?php echo $typeMode ?>" title="urlok" value="<?php echo $virtualpay->urlok ?>" /> <br />
	URLKO: <input id="URLKO" name="URLKO" type="<?php echo $typeMode ?>" title="urlko"  value="<?php echo $virtualpay->urlko ?>" /> <br />
	MAC: <input id="MAC" name="MAC" type="<?php echo $typeMode ?>" title="mac"  value="<?php echo $virtualpay->mac ?>" /> <br />
	<input type="submit" value="Invia">
</form>	
<?php else:?>
<form action="<?php echo $virtualpay->paymentUrl ?>" method="post" id="paymentform">
	<input id="MERCHANT_ID" name="MERCHANT_ID" type="<?php echo $typeMode ?>" title="merchant_id" value="<?php echo $virtualpay->merchant_id ?>" />
	<input id="ORDER_ID" name="ORDER_ID" type="<?php echo $typeMode ?>" title="order_id" value="<?php echo $virtualpay->order_id ?>" />
	<input id="IMPORTO" name="IMPORTO" type="<?php echo $typeMode ?>" title="importo" value="<?php echo $virtualpay->importo ?>" />
	<input id="DIVISA" name="DIVISA" type="<?php echo $typeMode ?>" title="divisa" value="<?php echo $virtualpay->divisa ?>" />
	<input id="ABI" name="ABI" type="<?php echo $typeMode ?>" title="ABI" value="<?php echo $virtualpay->abi ?>" />
	<input id="ITEMS" name="ITEMS" type="<?php echo $typeMode ?>" title="items"  value="<?php echo $virtualpay->items ?>" />
	<input id="EMAIL" name="EMAIL" type="<?php echo $typeMode ?>" title="email" value="<?php echo $virtualpay->email ?>" />
	<input id="LINGUA" name="LINGUA" type="<?php echo $typeMode ?>" title="lingua" value="<?php echo $virtualpay->lingua ?>" />
	<input id="URLKO" name="URLKO" type="<?php echo $typeMode ?>" title="urlko"  value="<?php echo $virtualpay->urlko ?>" />
	<input id="URLOK" name="URLOK" type="<?php echo $typeMode ?>" title="urlok" value="<?php echo $virtualpay->urlok ?>" />
	<input id="MAC" name="MAC" type="<?php echo $typeMode ?>" title="mac"  value="<?php echo $virtualpay->mac ?>" />
	<input type="submit" value="Invia">
</form>
<script type="text/javascript">
<!--
		jQuery(function($) {
			jQuery("#paymentform").submit();
		});
//-->
</script>
<?php endif;?>


