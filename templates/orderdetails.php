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
//	$checkmode = get_post_meta( $post->ID, 'checkmode', true );
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

$route = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ORDERURL);

?>		<form action="<?php echo  $route ?>" method="post" class="bfi-form-vertical" id="formCheckMode" target="_blank">
			<div class="bfi-form-field">		
				<?php bfi_get_template('orderdetails/default_checkmode'.$checkmode.'.php'); ?>
				<input type="hidden" id="cultureCode" name="cultureCode" value="<?php echo $language;?>" />
				<input type="hidden" id="actionform" name="actionform" value="login" />
				<input name="checkmode" type="hidden" value="<?php echo $checkmode;?>">
				<div class="bfi-text-center" >
					<br />
					<button type="submit" class="bfi-btn"><?php _e('Send', 'bfi') ?></button>
				</div>
			</div>
		</form>		
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