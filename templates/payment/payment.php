<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*
=====================
	IDpaymentSystem	
=====================
		1	BankPass
		2	Pagoonline
		3	PaymentGateway
		4	ICheckOut
		5	KeyClient
		6	Setefi
		7	WSPayForm
		8	Activa
*/
//$route= JRoute::_('index.php?view=orders&checkmode=' . $checkmode);

$order = $item->order;
$merchantPayments = $item->merchantPayments;
$merchantPayment=null;

if (isset( $item->merchantPayment)){
	$merchantPayment = $item->merchantPayment;
	$paymentSystemId = $merchantPayment->PaymentSystemId;
//	$paymentSystemRef = null;
	$paymentSystemRef = strtolower($merchantPayment->PaymentSystemName);

//	$actionmode =  $this->actionmode;
//	$hasPayed = $this->hasPayed;

	//echo "<pre>";
	//echo $actionmode;
	//echo "</pre>";
$payment_page = get_post( bfi_get_page_id( 'payment' ) );
$url_payment_page = get_permalink( $payment_page->ID );

$orderdetails_page = get_post( bfi_get_page_id( 'orderdetails' ) );
$url_orderdetails_page = get_permalink( $orderdetails_page->ID );
	$routeOrderPayed = $url_orderdetails_page . '/5/?actionform=login&orderid=' . $order->OrderId . '&email=' . BFCHelper::getItem($order->CustomerData, 'email');


//die();


	?>
		<?php if ($actionmode=='donation'):?>
			<?php
			$routeOrderPayed = url_orderdetails_page . '?actionmode=donation';

			//echo "<pre>merchantPayment:<br />";
			//echo print_r($merchantPayment);
			//echo "</pre>";

				if(!empty($paymentSystemRef)){
					include('payment_'.$paymentSystemRef.'.php');
				}
			?>
		<?php else:?>

			<?php if ($hasPayed!==null):?>
				<?php if ($hasPayed):?>
					<p class="success">
						<?php _e('Thank you: payment process successfully completed', 'bfi') ?>
						<!-- <br/>
						<a href="<?php echo $routeOrderPayed?>" ><?php _e('Print', 'bfi') ?></a> -->
					</p>
				<?php else:?>
					<p class="error">
						<?php _e('Payment process not successfully completed', 'bfi') ?>
					</p>
				<?php endif;?>
			<?php else:?>
				<?php
					switch ( $actionmode) {
						case 'cancel' :
?>
					<p class="error">
						<?php _e('Payment process not successfully completed and has been deleted', 'bfi') ?>
					</p>
					<br />
<?php
					break;
					case 'error' :
					case 'errordonation' :
						
?>
					<p class="error">
						<?php _e('Some problems occured durint payment process, please contact merchant if you are sure that your payment process completed', 'bfi') ?>
					</p>
					<br />
<?php
					break;

				 default :
?>
				<?php if ($actionmode!='errordonation' && $order!=null && $order->DepositAmount>0 && $paymentSystemRef!=null) :?>
					<!-- Form normale <br/>
					IDordine = <?php echo $order->OrderId?><br />
					tipologia di pagamento: <?php echo $paymentSystemRef ?><br /> -->
					<?php if ($actionmode!='error'):?>
						<?php include('payment_'.$paymentSystemRef.'.php'); ?>
					<?php endif;?>
				<?php else:?>
					<?php if ($actionmode!='errordonation'):?>
						<p class="error">
							<?php _e('Error inserting data', 'bfi') ?>:
								<?php if ($order==null):?>  
								<?php _e('Order doesn\'t exist', 'bfi') ?> <br />
								<?php endif;?>
								<?php if ($order!=null && $order->DepositAmount<1):?>
								<?php _e('Deposit does not exist', 'bfi') ?><br />
								<?php endif;?>
								<?php if ($paymentSystemRef==null ):?>
								<?php _e('At this moment you cannot do payments.', 'bfi') ?><br />
								<?php endif;?>
						</p>
					<?php endif;?>
				<?php endif;?>
<?php
					break;
					}

?>
				

			<?php endif;?>
		<?php endif;?>
	<?php }else{?>
		<?php _e('At this moment you cannot do payments.', 'bfi') ?><br />
	<?php }?>
