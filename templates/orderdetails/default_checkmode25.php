<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="bfi_form_txt">
	<label for="orderId"><?php _e('Booking reference', 'bfi') ?></label>
	<input id="orderId" name="orderId" type="text" />
</div>	
<div class="bfi_form_txt">
	<label for="customerFirstname"><?php _e('Name', 'bfi') ?></label>
	<input id="customerFirstname" name="customerFirstname" type="text" />
</div>
<div class="bfi_form_txt">
	<label for="customerLastname"><?php _e('Surname', 'bfi') ?></label>
	<input id="customerLastname" name="customerLastname" type="text" />
</div>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#formCheckMode").validate(
		    {
		        rules:
		        {
		        	orderId: "required",
		        	customerFirstname: "required",
		        	customerLastname: "required"
		        },
		        messages:
		        {
		        	orderId: "<?php _e('This field is required.', 'bfi') ?>",
		        	customerFirstname: "<?php _e('This field is required.', 'bfi') ?>",
		        	customerLastname: "<?php _e('This field is required.', 'bfi') ?>"
				},
		        highlight: function(label) {
			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.text('ok!').addClass('valid')
			    		.closest('.control-group').removeClass('error').addClass('success');
			    }
		    });
		});

</script>	