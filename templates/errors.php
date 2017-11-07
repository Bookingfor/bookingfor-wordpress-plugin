<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$errorCode = BFCHelper::getVar('errorCode',"0");

$errorMessage = __('OPS, some errors occurred! Please try again later.', 'bfi');
if ($errorCode=="1") {
   $errorMessage = __('At this moment you cannot do payments.', 'bfi');
}
if ($errorCode=="2") {
   $errorMessage = __('At this moment you cannot do payments.', 'bfi');
}
?>
<div class="bfi-content">	
<br />
		<div class="bfi-alert bfi-alert-danger">
		 <?php echo $errorMessage ?>
		</div>
</div>