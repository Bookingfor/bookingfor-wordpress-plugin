<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 ?>
<?php
global $post;
$currencyclass = bfi_get_currentCurrency();

$checkmode = get_query_var( 'checkmode', get_post_meta( $post->ID, 'checkmode', true ) );
$order = null;
?>
<?php

  if(!isset($_GET['task'])) {

get_header( 'orderdetails' );
?>
 <?php
		/**
		 * bookingfor_before_main_content hook.
		 *
		 * @hooked bookingfor_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked bookingfor_breadcrumb - 20
		 */
		do_action( 'bookingfor_before_main_content' );
		$model = new BookingForConnectorModelOrders;
		$model->populateState();
		$params = $model->getParam();
		if(empty($checkmode)){
			$checkmode = $params['checkmode'];
		}

	?>
	
	<h1 class="page-title"><?php _e('Order', 'bfi') ?></h1>
<?php
		$actionform = BFCHelper::getVar('actionform',"");
		
		if ($actionform=="login"){

			$order = $model->getOrderFromService(); 
		}
		if ($actionform=="insertemail"){
			$orderId = BFCHelper::getVar('orderId',"");
			$email = BFCHelper::getVar('email',"");
			$order = $model->updateEmail($orderId,$email); 
			/*$item = "fet";*/
		}

	
//	$checkmode = get_post_meta( $post->ID, 'checkmode', true );
$route = "";
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}


?>
<?php if (empty($order) && $checkmode!==0) :?>
	<?php if ($actionform == "login") :?>
		<div class="bfi-alert bfi-alert-danger">
			<strong><?php _e('Error, please try again', 'bfi') ?></strong>
		</div>
	<?php endif; ?>
		<form action="<?php echo  $route ?>" method="post" class="bfi-form-horizontal" id="formCheckMode">
			<div class="bfi_form-field">		
				<?php include(BFI()->plugin_path().'/templates/orderdetails/default_checkmode'.$checkmode.'.php'); // merchant template?>
				<input type="hidden" id="cultureCode" name="cultureCode" value="<?echo $language;?>" />
				<input type="hidden" id="actionform" name="actionform" value="login" />
				<div class="bfi-text-center" >
					<br />
					<button type="submit" class="bfi_send-sx"><?php _e('Send', 'bfi') ?></button>
				</div>
			</div>
		</form>
<?php else: ?>
<?php 
	$email = "";
	if(!empty($order)){
		$email = BFCHelper::getItem($order->CustomerData, 'email')."";
	}
 ?>
	<?php if ($email===""):?>
			<?php include(BFI()->plugin_path().'/templates/orderdetails/mailupdate.php'); ?>
	<?php else: ?>
		<?php if (!empty($order)) :?>
			<?php include(BFI()->plugin_path().'/templates/orderdetails/order.php'); ?>
		<?php endif; ?>
	<?php endif; ?>
	
	
	
<?php endif; ?>
		
<?php

	$layout = get_query_var( 'bfi_layout', '' );
//	$model->setResourceId($resource_id);
//	$model->setItemPerPage($num_per_page);


	switch ( $layout) {
//		case 'resources' :
//			$resources = $model->getItems('',0, $merchant_id);
//			$total = $model->getTotal();
//			include(BFI()->plugin_path().'/templates/merchantdetails/resources.php'); // merchant template
//		break;
//		case 'offers' :
//			$offers = $model->getItems('offers',0, $merchant_id);
//			$total = $model->getTotal('offers');
//			include(BFI()->plugin_path().'/templates/merchantdetails/offers.php'); // merchant template
//		break;
//		case 'offer' :
//			$offerId = get_query_var( 'bfi_id', 0 );
//			if(!empty($offerId)){
//				$offer = $model->getMerchantOfferFromService($offerId);
//				include(BFI()->plugin_path().'/templates/merchantdetails/offer-details.php'); // merchant template
//			}
//		break;
//		case 'thanks' :
//			include(BFI()->plugin_path().'/templates/merchantdetails/thanks.php'); // merchant template
//		break;
//		case 'errors' :
//			include(BFI()->plugin_path().'/templates/merchantdetails/errors.php'); // merchant template
//		break;
//		case 'packages' :
//			$packages = $model->getItems('packages',0, $merchant_id);
//			$total = $model->getTotal('packages');
//			include(BFI()->plugin_path().'/templates/merchantdetails/packages.php'); // merchant template
//		break;
//		case 'package' :
//			$packageId = get_query_var( 'bfi_id', 0 );
//			if(!empty($packageId)){
//				$offer = $model->getMerchantPackageFromService($packageId);
//				include(BFI()->plugin_path().'/templates/merchantdetails/package-details.php'); // merchant template
//			}
//		break;
//		case 'reviews' :
//			if(isset($_POST) && !empty($_POST)) {
//				$_SESSION['ratings']['filters']['typologyid'] = $_POST['filters']['typologyid'];
//			}
//			$ratings = $model->getItems('ratings',0, $merchant_id);
//			$total = $model->getTotal('ratings');
//			$summaryRatings = $model->getMerchantRatingAverageFromService($merchant_id);
//			include(BFI()->plugin_path().'/templates/merchantdetails/reviews.php'); // merchant template
//		break;
//		case 'review' :
//			include(BFI()->plugin_path().'/templates/merchantdetails/review.php'); // merchant template
//		break;
		
		default:
//			include(BFI()->plugin_path().'/templates/resourcedetails/resourcedetails.php'); // merchant template
	}

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
  
  }
//  else {
//    $task = $_GET['task'];

//}
?>