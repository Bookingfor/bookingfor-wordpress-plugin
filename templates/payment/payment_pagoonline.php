<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
 * ================
 * Pagoonline System Form
 * ================
 * 
 * */

$order = $this->item->order;

$merchantPayment = $this->item->merchantPayment;

$debugmode = false;
$SandboxMode = $merchantPayment->SandboxMode;

$uri                    = JURI::getInstance();
$urlBase = $uri->toString(array('scheme', 'host', 'port'));

$urlok = $urlBase . JRoute::_('index.php?view=payment&actionmode=pagoonline&payedOrderId=' . $order->OrderId);
$urlko =  $urlBase . JRoute::_('index.php?view=payment&actionmode=error&payedOrderId=' . $order->OrderId);
if ($this->actionmode=="donation"){
$urlok = $urlBase . JRoute::_('index.php?view=payment&actionmode=pagoonline&payedOrderId=donation' . $order->OrderId);
$urlko =  $urlBase . JRoute::_('index.php?view=payment&actionmode=errordonation');
}

$suffixOrder = "";
$overrideAmount = null;
if (isset($this->item->paymentCount)) {
	$suffixOrder = (string)($this->item->paymentCount +1) ;
	$overrideAmount = $this->item->overrideAmount;
	}

$pagoonline = new pagoonline($merchantPayment->Data, $order, $this->language, $urlok, $urlko, $suffixOrder,$overrideAmount,$SandboxMode);

if ($this->actionmode=="donation"){
	$pagoonline->causalePagamento=  JRequest::getVar('causale','');
}


?>
<?php if($debugmode):?>	
	Caparra da pagare <?php echo $order->DepositAmount ?><br />
	<strong>TEST Pagoonline</strong><br />
	email: <?php echo $pagoonline->email ?><br />
	<a href="<?php echo $pagoonline->getUrl() ; ?>"><?php echo $pagoonline->getUrl() ; ?></a>

<?php else:?>
	<!-- Invio transazione alla banca -->
	<script language="JavaScript">
	<!--
		document.location.href="<?php echo $pagoonline->getUrl() ; ?>";
	//-->
	</script>
<?php endif;?>


