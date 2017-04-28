<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * bnlpositivity System Form
 * ================
 * 
 * */
$order = $this->item->order;
$merchantPayment = $this->item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;
$donation = false;

$typeMode = $debugmode?'text':'hidden'; 

$uri                    = JURI::getInstance();
$urlBase = $uri->toString(array('scheme', 'host', 'port'));


//$urlBack = $urlBase . JRoute::_('index.php?view=payment&actionmode=cancel&payedOrderId=' . $order->OrderId);
$urlBack = $urlBase . JRoute::_('index.php?view=orders');
$overrideAmount =0;
$suffixOrder = "";
if (isset($this->item->paymentCount)) {
	$suffixOrder = (string)($this->item->paymentCount +1) ;
	$overrideAmount = $this->item->overrideAmount;
}

if ($this->actionmode=='donation')
{
	$donation = true;
}

$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=bnlpositivity&payedOrderId=' . $order->OrderId);
$urlBack = $url;
$bnlpositivity = new bnlpositivity($merchantPayment->Data, $order, $this->language, $urlBack, $url, $suffixOrder,$overrideAmount , $SandboxMode,$donation);


?>
<?php if($debugmode):?>	
Caparra da pagare <?php echo $order->DepositAmount ?><br />
<strong>TEST BNL POSitivity</strong><br />

<form action="<?php echo $bnlpositivity->paymentUrl ?>" method="post">
	Tipo transazione: <input type="<?php echo $typeMode ?>" name="txntype" value="<?php echo $bnlpositivity->txntype ?>"><br />
	timezone: <input type="<?php echo $typeMode ?>" name="timezone" value="CET">
	txndatetime: <input type="<?php echo $typeMode ?>" name="txndatetime" value="<?php echo $bnlpositivity->currentDateTime ?>">
	hash: <input type="<?php echo $typeMode ?>" name="hash" value="<?php echo $bnlpositivity->hash ?>">
	storename: <input type="<?php echo $typeMode ?>" name="storename" value="<?php echo $bnlpositivity->storename ?>">
	mode: <input type="<?php echo $typeMode ?>" name="mode" value="payonly">
	currency: <input type="<?php echo $typeMode ?>" name="currency" value="<?php echo $bnlpositivity->divisa ?>">
	language: <input type="<?php echo $typeMode ?>" name="language" value="<?php echo $bnlpositivity->language ?>">
	responseSuccessURL: <input type="<?php echo $typeMode ?>" name="responseSuccessURL" value="<?php echo $bnlpositivity->responseSuccessURL ?>">
	transactionNotificationURL: <input type="<?php echo $typeMode ?>" name="transactionNotificationURL" value="<?php echo $bnlpositivity->responseSuccessURL ?>">
	responseFailURL: <input type="<?php echo $typeMode ?>" name="responseFailURL" value="<?php echo $bnlpositivity->responseFailURL ?>">
	chargetotal: <input type="<?php echo $typeMode ?>" name="chargetotal" value="<?php echo $bnlpositivity->importo ?>">
	oid: <input type="<?php echo $typeMode ?>" name="oid" value="<?php echo $bnlpositivity->order_id ?>">
	addInfo3 <input type="<?php echo $typeMode ?>" name="addInfo3" value="<?php echo $bnlpositivity->info ?>">
	addInfo4 <input type="<?php echo $typeMode ?>" name="addInfo4" value="<?php echo $bnlpositivity->currentDateTime ?>">
	<input type="submit" value="Acquista">	
</form>	
<?php else:?>
<form action="<?php echo $bnlpositivity->paymentUrl ?>" method="post" id="paymentform" style="display:none">
	<input type="<?php echo $typeMode ?>" name="txntype" value="<?php echo $bnlpositivity->txntype ?>"><br />
	<input type="<?php echo $typeMode ?>" name="timezone" value="CET">
	<input type="<?php echo $typeMode ?>" name="txndatetime" value="<?php echo $bnlpositivity->currentDateTime ?>">
	<input type="<?php echo $typeMode ?>" name="hash" value="<?php echo $bnlpositivity->hash ?>">
	<input type="<?php echo $typeMode ?>" name="storename" value="<?php echo $bnlpositivity->storename ?>">
	<input type="<?php echo $typeMode ?>" name="mode" value="payonly">
	<input type="<?php echo $typeMode ?>" name="currency" value="<?php echo $bnlpositivity->divisa ?>">
	<input type="<?php echo $typeMode ?>" name="language" value="<?php echo $bnlpositivity->language ?>">
	<input type="<?php echo $typeMode ?>" name="responseSuccessURL" value="<?php echo $bnlpositivity->responseSuccessURL ?>">
	<input type="<?php echo $typeMode ?>" name="transactionNotificationURL" value="<?php echo $bnlpositivity->responseSuccessURL ?>">
	<input type="<?php echo $typeMode ?>" name="responseFailURL" value="<?php echo $bnlpositivity->responseFailURL ?>">
	<input type="<?php echo $typeMode ?>" name="chargetotal" value="<?php echo $bnlpositivity->importo ?>">
	<input type="<?php echo $typeMode ?>" name="oid" value="<?php echo $bnlpositivity->order_id ?>">
	<input type="<?php echo $typeMode ?>" name="addInfo3" value="<?php echo $bnlpositivity->info ?>">
	<input type="<?php echo $typeMode ?>" name="addInfo4" value="<?php echo $bnlpositivity->currentDateTime ?>">
	<input type="submit" value="Acquista">	
</form>
<script type="text/javascript">
<!--
		jQuery(function($) {
			jQuery("#paymentform").submit();
		});
//-->
</script>
<?php endif;?>


