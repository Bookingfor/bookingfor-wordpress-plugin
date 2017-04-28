<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * Keyclient System Form
 * ================
 * 
 * */
$order = $this->item->order;

$merchantPayment = $this->item->merchantPayment;

$debugmode = false;
$typeMode = $debugmode?'text':'hidden'; 

$uri                    = JURI::getInstance();
$urlBase = $uri->toString(array('scheme', 'host', 'port'));


//$urlBack = $urlBase . JRoute::_('index.php?view=payment&actionmode=cancel&payedOrderId=' . $order->OrderId);
$urlBack = $urlBase . JRoute::_('index.php?view=orders');

$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=keyclient2014&payedOrderId=' . $order->OrderId);
$urlBack = $url;
$keyclient = new keyClient2014($merchantPayment->Data, $order, $this->language, $urlBack, $url);


?>
<?php if($debugmode):?>	
Caparra da pagare <?php echo $order->DepositAmount ?><br />
<strong>TEST Keyclient</strong><br />
<strong><?php echo $paymentData ?></strong><br />

<form action="<?php echo $keyclient->paymentUrl ?>" method="post">
	alias: <input id="IDNEGOZIO" name="alias" type="<?php echo $typeMode ?>" title="alias" value="<?php echo $keyclient->alias ?>" /> <br />
	importo: <input id="IMPORTO" name="importo" type="<?php echo $typeMode ?>" title="importo" value="<?php echo $keyclient->importo ?>" /> <br />
	divisa: <input id="VALUTA" name="divisa" type="<?php echo $typeMode ?>" title="divisa" value="<?php echo $keyclient->divisa ?>" /> <br />
	codTrans: <input id="NUMORD" name="codTrans" type="<?php echo $typeMode ?>" title="codTrans" value="<?php echo $keyclient->numord ?>" /> <br />
	EMAIL: <input id="EMAIL" name="EMAIL" type="<?php echo $typeMode ?>" title="EMAIL" value="<?php echo $keyclient->email ?>" /> <br />
	languageId: <input id="LINGUA" name="languageId" type="<?php echo $typeMode ?>" title="languageId" value="<?php echo $keyclient->languageId ?>" /> <br />
	url_back: <input id="URLBACK" name="url_back" type="<?php echo $typeMode ?>" title="url_back"  value="<?php echo $keyclient->urlBack ?>" /> <br />
	url: <input id="URLMS" name="url" type="<?php echo $typeMode ?>" title="url" value="<?php echo $keyclient->url ?>" /> <br />
	mac: <input id="MAC" name="mac" type="<?php echo $typeMode ?>" title="mac"  value="<?php echo $keyclient->mac ?>" /> <br />
	<input type="submit" value="Invia">
</form>	
<?php else:?>
<form action="<?php echo $keyclient->paymentUrl ?>" method="post" id="paymentform" style="display:none">
	<input id="IDNEGOZIO" name="alias" type="<?php echo $typeMode ?>" title="alias" value="<?php echo $keyclient->alias ?>" />
	<input id="IMPORTO" name="importo" type="<?php echo $typeMode ?>" title="importo" value="<?php echo $keyclient->importo ?>" />
	<input id="VALUTA" name="divisa" type="<?php echo $typeMode ?>" title="divisa" value="<?php echo $keyclient->divisa ?>" />
	<input id="NUMORD" name="codTrans" type="<?php echo $typeMode ?>" title="codTrans" value="<?php echo $keyclient->numord ?>" />
	<input id="EMAIL" name="EMAIL" type="<?php echo $typeMode ?>" title="EMAIL" value="<?php echo $keyclient->email ?>" />
	<input id="LINGUA" name="languageId" type="<?php echo $typeMode ?>" title="languageId" value="<?php echo $keyclient->languageId ?>" />
	<input id="URLBACK" name="url_back" type="<?php echo $typeMode ?>" title="url_back"  value="<?php echo $keyclient->urlBack ?>" />
	<input id="URLMS" name="url" type="<?php echo $typeMode ?>" title="url" value="<?php echo $keyclient->url ?>" />
	<input id="MAC" name="mac" type="<?php echo $typeMode ?>" title="mac"  value="<?php echo $keyclient->mac ?>" />
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


