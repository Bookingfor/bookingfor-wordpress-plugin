<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bfi_form_txt">
	<label for="externalCustomerId"><?php _e('Client code', 'bfi') ?></label>
	<input id="externalCustomerId" name="externalCustomerId" type="text" />
</div>
<div class="bfi_form_txt">
	<label for="externalOrderId"><?php _e('External order ID', 'bfi') ?></label>
	<input id="externalOrderId" name="externalOrderId" type="text" />
</div>	
<div class="bfi_form_txt">
	<label for="checkIn"><?php echo _e('Check-in','bfi') ?></label>
	<input id="checkIn" name="checkIn" type="text" />
</div>
<div class="bfi_form_txt">
	<label for="checkOut"><?php echo _e('Check-out ','bfi') ?></label>
	<input id="checkOut" name="checkOut" type="text" />
</div>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#formCheckMode").validate(
		    {
		        rules:
		        {
		        	externalOrderId: "required",
		        	externalCustomerId: "required",
		        	checkIn: {
			        		required: true,
			        		dateITA: true
			        	},
		        	checkOut:  {
		        		required: true,
		        		dateITA: true
		        	}
		        },
		        messages:
		        {
		        	externalOrderId: "<?php _e('This field is required.', 'bfi') ?>",
		        	externalCustomerId: "<?php _e('This field is required.', 'bfi') ?>",
		        	checkIn: {
		        		required:"<?php _e('This field is required.', 'bfi') ?>",
		        		dateITA:"<?php _e('Correct format: dd/mm/yyyy', 'bfi') ?>"
		        		},
		        	checkOut:  {
		        		required:"<?php _e('This field is required.', 'bfi') ?>",
			        	dateITA:"<?php _e('Correct format: dd/mm/yyyy', 'bfi') ?>"
		        		}
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