<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();

?>
<!-- {emailcloak=off} -->
<div class="bfi_form_txt">
		<label for="orderId"><?php _e('Booking reference', 'bfi') ?></label>
		<input id="orderId" name="orderId" type="text" />
</div>	
<div class="bfi_form_txt">
	<label for="email"><?php _e('Email', 'bfi'); ?></label>
	<input id="email" name="email" type="text" value="<?php echo $current_user->user_email; ?>" />
</div>	

<script type="text/javascript">
jQuery(function($)
		{
		    $("#formCheckMode").validate(
		    {
		        rules:
		        {
		        	orderId: "required",
		            email:
		            {
		                required: true,
		                email: true
		            },
		            accetto: "required"
		        },
		        messages:
		        {
		        	orderId: "<?php _e('This field is required.', 'bfi') ?>",
		            email: "<?php _e('This field is required.', 'bfi') ?>"
		        },
				errorClass: "bfi-error",
		        highlight: function(label) {
//			    	$(label).closest('.bfi-form-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
//			    	label
//			    		.text('ok!').addClass('valid')
//			    		.closest('.bfi-form-group').removeClass('error').addClass('success');
			    }
		    });
		});

</script>