<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
global $post;

$trackorder = false;

$orderid = get_query_var( 'orderid', BFCHelper::getVar('payedOrderId',0) );

$actionmode = BFCHelper::getVar('actionmode',"");
$model = new BookingForConnectorModelPayment;
$model->populateState();

$item = $model->getItem($orderid);
$hasPayed = null;

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

  if(!isset($_GET['task'])) {

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
		if ($actionmode=="orderpayment"){
			
			//recupero quanti pagamenti sono stati effettuati
						
			$item->paymentCount =  BFCHelper::getTotalOrderPayments($item->order->OrderId);

			//sostituisco i dati dell'ordine da pagare con i dati passati e l'ordine con un suffisso in più
			
			/*$item = $this->get('Item');*/ 
			/*$item = "fet";*/
		}
 	
		
		if ($actionmode!='' && $actionmode!='cancel' && $actionmode!='donation' && $actionmode!='orderpayment'){


			$sandboxmode=false;
			if(!empty($item) && !empty($item->merchantPayment)  && !empty($item->merchantPayment->SandboxMode)){
				$sandboxmode=$item->merchantPayment->SandboxMode;

			}

			if ($item->order->Status!=5){
				$hasPayed = bfi_processPayment($actionmode,$item,$sandboxmode);
				/* eccezione per setefi che pretende un url di ritorno */
				
			}else {
				//$hasPayed = true;
				$hasPayed = bfi_processOrderPayment($actionmode,$item,$language,$sandboxmode);
			}
			/*
			$link = '';
			if ($hasPayed){
			 	
			}
			$app = JFactory::getApplication();
			$app->redirect($link, $msg);
			*/
		}
				
		if ($actionmode=='' && $actionmode!='donation'){
			 if ($item->order->Status!=5){
				 bfi_inizializePayment($orderid);
			 }
		}
		if ($actionmode=='orderpaid'){
			$trackorder = true;
			$hasPayed= ($item->order->Status==5);
		}
		
		if(isset($trackorder) && $trackorder) {
			$merchants = array();
			$merchants[] = $item->order->MerchantId;

			if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
				$criteoConfig = BFCHelper::getCriteoConfiguration(4, $merchants, $item->order->OrderId);
				if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
					echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>';
					echo '<script type="text/javascript"><!--
					';
					echo ('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "trackTransaction", id: "'. $criteoConfig->transactionid .'",  item: ['. json_encode($criteoConfig->orderdetails) .'] }
					);');
					echo "//--></script>";

	//				$document->addScript('//static.criteo.net/js/ld/ld.js');
	//				$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
	//				window.criteo_q.push( 
	//					{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
	//					{ event: "setSiteType", type: "d" }, 
	//					{ event: "setEmail", email: "" }, 
	//					{ event: "trackTransaction", id: "'. $criteoConfig->transactionid .'",  item: ['. json_encode($criteoConfig->orderdetails) .'] }
	//				);');
				}				
			}
						

			$listName = 'Resource List';
			if(COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
				add_action('bfi_head', 'bfi_google_analytics_EEc', 10, 1);
				do_action('bfi_head', $listName);

//			$analyticsEnabled = $this->checkAnalytics("Sales Resource List");
//			if($analyticsEnabled && $config->get('eecenabled', 0) == 1) {
				$purchaseObject = new stdClass;
				$purchaseObject->id = "" . $item->order->OrderId;
				$purchaseObject->affiliation = "" . $item->order->Label;
				$purchaseObject->revenue = "" . $item->order->TotalAmount;
				$purchaseObject->tax = 0.00;
				
				$allobjects = array();
				$svcTotal = 0;
				
				$allservices = array_values(array_filter(bfi_simpledom_load_string($order->NotesData)->xpath("//price"), function($prc) {
					return (string)$prc->tag == "extrarequested";
				}));
				
				foreach($allservices as $svc) {
					$svcObj = new stdClass;
					$svcObj->id = "" . (int)$svc->priceId . " - Service";
					$svcObj->name = (string)$svc->name;
					$svcObj->category = "Services";
					$svcObj->brand = $item->Name;
					$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
					$svcObj->price = round((float)$svc->discountedamount / (int)$svc->quantity, 2);
					$svcObj->quantity = (int)$svc->quantity;
					$allobjects[] = $svcObj;
					$svcTotal += (float)$svc->discountedamount;
				}
				
				$mainObj = new stdClass;
				$mainObj->id = "" . $item->order->RequestedItemId . " - Resource";
				$mainObj->name = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
				$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
				$mainObj->category = $item->MainCategoryName;
				$mainObj->brand = $item->Name;
				$mainObj->price = $item->order->TotalAmount - $svcTotal;
				$mainObj->quantity = 1;
				
				$allobjects[] = $mainObj;
				echo '<script type="text/javascript"><!--
				';
				echo ('callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {"step": 3,});
					   callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');
				echo "//--></script>";

					
			}
		}
		
		include(BFI()->plugin_path().'/templates/payment/payment.php'); // merchant template

?>
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

<?php
  
//  }
//  else {
//    $task = $_GET['task'];
}
?>