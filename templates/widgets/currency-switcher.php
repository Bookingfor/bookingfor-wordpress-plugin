<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$defaultCurrency = bfi_get_defaultCurrency();
$currentCurrency = bfi_get_currentCurrency();
$currencyExchanges = bfi_get_currencyExchanges();
if(empty($currencyExchanges) || count($currencyExchanges)<2){
	return; //no more currency than default
}
$currency_text = array('978' => __('Euro', 'bfi'),
						'191' => __('Kune', 'bfi'),
						'840' => __('U.S. dollar', 'bfi'),   
						'392' => __('Japanese yen', 'bfi'),
						'124' => __('Canadian dollar', 'bfi'),
						'36' => __('Australian dollar', 'bfi'),
						'643' => __('Russian Ruble', 'bfi'),  
						'200' => __('Czech koruna', 'bfi'),
						'702' => __('Singapore dollar', 'bfi'),  
						'826' => __('Pound sterling', 'bfi')                            
					);


?>
	<div class="bfi-currency-switcher">
		<div class="bfi-currency-switcher-selected bfi_<?php echo $currentCurrency ?>">&nbsp;</div>
		<div class="bfi-currency-switcher-content">
<?php 
foreach ($currencyExchanges as $currencyExchangeCode => $currencyExchange ) {
    ?>
			<div class="bfi-currency-switcher-selector bfi_<?php echo $currencyExchangeCode ?>" rel="<?php echo $currencyExchangeCode ?>"> <?php echo $currency_text[$currencyExchangeCode] ?><!-- (<span class=" bfi_<?php echo $defaultCurrency ?>">1</span> = <span class=" bfi_<?php echo $currencyExchangeCode ?>"><?php echo $currencyExchange?><span>) --></div>
    
<?php     
}
?>

		</div>
	</div>
<script type="text/javascript">
<!--
jQuery(document).ready(function() {
	jQuery('.bfi-currency-switcher-selector').click(function() {
		var currCurrency= "<?php echo $currentCurrency ?>";
		var newCurrency= jQuery(this).attr("rel");
		if(currCurrency!==newCurrency){
			window.location.href = bookingfor.updateQueryStringParameter(window.location.href ,"bfiselectedcurrency",newCurrency);
		}
	});
});  
	
//-->
</script>