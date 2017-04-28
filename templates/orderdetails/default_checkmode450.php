<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$date = new DateTime();
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
	<input type="text" name="checkIn" id="checkIn" value="<?php echo $date->format('d/m/Y') ?>" class="calendar" />
</div>
<div class="bfi_form_txt">
	<label for="checkOut"><?php echo _e('Check-out ','bfi') ?></label>
	<input type="text" name="checkOut" id="checkOut" value="<?php echo $date->modify("+7 day")->format('d/m/Y') ?>" class="calendar" />
</div>
<script type="text/javascript">
var checkIn = null;
var checkOut = null;
jQuery(function($)
		{
			checkIn = function() { $("#checkIn").datepicker({
				defaultDate: "+2d"
				,changeMonth: true
				,changeYear: true
				,dateFormat: "dd/mm/yy"
				,beforeShow: function(input, inst) {$('#ui-datepicker-div').addClass('notranslate');}
				, minDate: '+0d', onSelect: function(dateStr) { $("#formCheckMode").validate().element(this); }
			})};
			checkIn();
                
			checkOut = function() { $("#checkOut").datepicker({
				defaultDate: "+2d"
				,changeMonth: true
				,changeYear: true
				,dateFormat: "dd/mm/yy"
				,beforeShow: function(input, inst) {$('#ui-datepicker-div').addClass('notranslate');}
				, minDate: '+7d', onSelect: function(dateStr) { $("#formCheckMode").validate().element(this); }
			})};
			checkOut();
			
			//fix Google Translator and datepicker
			$('.ui-datepicker').addClass('notranslate');
			
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
		        		dateITA: true,
		        		greaterThanDateITA : "#checkIn"
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
			        	dateITA:"<?php _e('Correct format: dd/mm/yyyy', 'bfi') ?>",
			        	greaterThanDateITA : "<?php _e('Check-in must be great than Check-out', 'bfi') ?>"
		        		}
		        	},
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