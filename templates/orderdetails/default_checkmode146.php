<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$date = new DateTime('UTC');
?>

<div class="bfi_form_txt">
	<label for="externalOrderId"><?php _e('External order ID', 'bfi') ?></label>
	<input id="externalOrderId" name="externalOrderId" type="text" />
</div>	
<div class="bfi_form_txt">
	<label for="customerLastname"><?php _e('Surname', 'bfi') ?></label>
	<input id="customerLastname" name="customerLastname" type="text" />
</div>
<div class="bfi_form_txt">
	<label for="checkIn"><?php echo _e('Check-in','bfi') ?></label>
	<input type="text" name="checkIn" id="checkIn" value="<?php echo $date->format('d/m/Y') ?>" class="calendar" />
</div>
<script type="text/javascript">
var checkIn = null;
jQuery(function($)
		{
			checkIn = function() { jQuery("#checkIn").datepicker({
				defaultDate: "+2d"
				,changeMonth: true
				,changeYear: true
				,dateFormat: "dd/mm/yy"
				,beforeShow: function(input, inst) {
					jQuery('#ui-datepicker-div').addClass('notranslate');
					jQuery(inst.dpDiv).addClass('bfi-calendar');
					jQuery(inst.dpDiv).attr('data-before',"");
					jQuery(inst.dpDiv).removeClass("bfi-checkin");
					jQuery(inst.dpDiv).removeClass("bfi-checkout");
					}
				, minDate: '+0d'
				, onSelect: function(dateStr) {
					jQuery("#formCheckMode").validate().element(this);
					}
			})};
			checkIn();
			//fix Google Translator and datepicker
			jQuery('.ui-datepicker').addClass('notranslate');

			jQuery("#formCheckMode").validate(
		    {
		        rules:
		        {
		        	externalOrderId: "required",
		        	checkIn: {
			        		required: true,
			        		dateITA: true
			        	},
		        	customerLastname: "required"
		        },
		        messages:
		        {
		        	externalOrderId: "<?php _e('This field is required.', 'bfi') ?>",
		        	checkIn: {
		        		required:"<?php _e('This field is required.', 'bfi') ?>",
		        		dateITA:"<?php _e('Correct format: dd/mm/yyyy', 'bfi') ?> "
		        		},
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