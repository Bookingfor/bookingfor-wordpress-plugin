<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * wspayform System Form
 * ================
 * 
 * */
$order = $item->order;

$merchantPayment = $item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;
$typeMode = $debugmode?'text':'hidden'; 


$paymentData = $merchantPayment->Data;

$url = $url_payment_page .'?actionmode=wspayform&payedOrderId=' . $order->OrderId;
$urlerror = $url_payment_page .'?actionmode=wspayform&payedOrderId=' . $order->OrderId;
$urlBack = $url;

//$uri                    = JURI::getInstance();
//$urlBase = $uri->toString(array('scheme', 'host', 'port'));


//$urlBack = $urlBase . JRoute::_('index.php?view=payment&actionmode=cancel&payedOrderId=' . $order->OrderId);
//$urlBack = $urlBase . JRoute::_('index.php?view=orders');

//$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=wspayform&payedOrderId=' . $order->OrderId);
//$urlBack = $url;
//$urlerror =  $urlBase . JRoute::_('index.php?view=payment&actionmode=error&payedOrderId=' . $order->OrderId);

$suffixOrder = "";
$overrideAmount = 0;
if (isset($item->paymentCount)) {
	$suffixOrder = (string)($item->paymentCount +1) ;
	$overrideAmount = $item->overrideAmount;
	}

$wspayform = new BFI_wspayform($merchantPayment->Data, $order, $language, $urlBack, $url, $suffixOrder,$overrideAmount,$debugmode);

?>
<?php if($debugmode):?>	
	Caparra da pagare <?php echo $order->DepositAmount ?><br />
<strong>TEST WSPayForm</strong><br />
<strong><?php echo $paymentData ?></strong><br />

<form action="<?php echo $wspayform->paymentUrl ?>" method="post">
	Payment Url: <?php echo $wspayform->paymentUrl ?><br />
	ShopID: <input id="ShopID" name="ShopID" type="<?php echo $typeMode ?>" title="ShopID" value="<?php echo $wspayform->shopID ?>" /> <br />
	importo: <input id="TotalAmount" name="TotalAmount" type="<?php echo $typeMode ?>" title="TotalAmount" value="<?php echo $wspayform->importo ?>" /> <br />
	ShoppingCartID: <input id="ShoppingCartID" name="ShoppingCartID" type="<?php echo $typeMode ?>" title="ShoppingCartID" value="<?php echo $wspayform->numord ?>" /> <br />
	CustomerEmail: <input id="CustomerEmail" name="CustomerEmail" type="<?php echo $typeMode ?>" title="CustomerEmail" value="<?php echo $wspayform->email ?>" /> <br />
	Lang: <input id="Lang" name="Lang" type="<?php echo $typeMode ?>" title="Lang" value="<?php echo $wspayform->languageId ?>" /> <br />
	ReturnErrorURL: <input id="ReturnErrorURL" name="ReturnErrorURL" type="<?php echo $typeMode ?>" title="ReturnErrorURL"  value="<?php echo $urlerror ?>" /> <br />
	ReturnURL: <input id="ReturnURL" name="ReturnURL" type="<?php echo $typeMode ?>" title="ReturnURL" value="<?php echo $wspayform->url ?>" /> <br />
	CancelURL: <input id="CancelURL" name="CancelURL" type="<?php echo $typeMode ?>" title="CancelURL" value="<?php echo $wspayform->urlBack ?>" /> <br />
	Signature: <input id="Signature" name="Signature" type="<?php echo $typeMode ?>" title="Signature"  value="<?php echo $wspayform->mac ?>" /> <br />
	<input type="submit" value="Invia">
</form>	
<?php else:?>
<form action="<?php echo $wspayform->paymentUrl ?>" method="post" id="paymentform">
	<input id="ShopID" name="ShopID" type="<?php echo $typeMode ?>" title="ShopID" value="<?php echo $wspayform->shopID ?>" />
	<input id="TotalAmount" name="TotalAmount" type="<?php echo $typeMode ?>" title="TotalAmount" value="<?php echo $wspayform->importo ?>" />
	<input id="ShoppingCartID" name="ShoppingCartID" type="<?php echo $typeMode ?>" title="ShoppingCartID" value="<?php echo $wspayform->numord ?>" />
	<input id="CustomerEmail" name="CustomerEmail" type="<?php echo $typeMode ?>" title="CustomerEmail" value="<?php echo $wspayform->email ?>" />
	<input id="Lang" name="Lang" type="<?php echo $typeMode ?>" title="Lang" value="<?php echo $wspayform->languageId ?>" />
	<input id="ReturnErrorURL" name="ReturnErrorURL" type="<?php echo $typeMode ?>" title="ReturnErrorURL"  value="<?php echo $urlerror ?>" />
	<input id="CancelURL" name="CancelURL" type="<?php echo $typeMode ?>" title="CancelURL"  value="<?php echo $wspayform->urlBack ?>" />
	<input id="ReturnURL" name="ReturnURL" type="<?php echo $typeMode ?>" title="ReturnURL" value="<?php echo $wspayform->url ?>" />
	<input id="Signature" name="Signature" type="<?php echo $typeMode ?>" title="Signature"  value="<?php echo $wspayform->mac ?>" />
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


