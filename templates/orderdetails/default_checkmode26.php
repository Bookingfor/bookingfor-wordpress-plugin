<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bfi_form_txt">
	<label for="externalOrderId"><?php _e('External order ID', 'bfi') ?></label>
	<input id="externalOrderId" name="externalOrderId" type="text" />
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
		        	externalOrderId: "required",
		        	customerFirstname: "required",
		        	customerLastname: "required"
		        },
		        messages:
		        {
		        	externalOrderId: "<?php _e('This field is required.', 'bfi') ?>",
		        	customerFirstname: "<?php _e('This field is required.', 'bfi') ?>",
		        	customerLastname: "<?php _e('This field is required.', 'bfi') ?>"
				},
				errorClass: "bfi-error",
		        highlight: function(label) {
//			    	$(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
//			    	label
//			    		.text('ok!').addClass('valid')
//			    		.closest('.control-group').removeClass('error').addClass('success');
			    }
		    });
		});

</script>	