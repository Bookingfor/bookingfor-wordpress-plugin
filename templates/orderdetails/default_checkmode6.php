<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();

?>
<!-- {emailcloak=off} -->

<div class="bfi_form_txt">
	<label for="externalOrderId"><?php _e('External order ID', 'bfi') ?></label>
	<input id="externalOrderId" name="externalOrderId" type="text" />
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
		        	externalOrderId: "required",
		            email:
		            {
		                required: true,
		                email: true
		            },
		            accetto: "required"
		        },
		        messages:
		        {
		        	externalOrderId: "<?php _e('This field is required.', 'bfi') ?>",
		            email: "<?php _e('This field is required.', 'bfi') ?>"
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