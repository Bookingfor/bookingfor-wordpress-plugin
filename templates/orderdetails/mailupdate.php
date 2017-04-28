<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$sitename = get_bloginfo();


$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$ssllogo = COM_BOOKINGFORCONNECTOR_SSLLOGO;
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
$base_url = get_site_url(null,'', $usessl ? "https" : null);




?>
	<div class="container-fluid">  
	<form action="<?php echo  $route ?>" method="post" class="form-horizontal" id="formEmail">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">   
			<div class="control-group">
				<div class="controls">
					<label><?php _e('Email', 'bfi'); ?></label>
					<input name="email" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6" placeholder="email" value="<?php echo $email;?>" >    
					<input type="hidden" id="actionform" name="actionform" value="insertemail" />
					<input type="hidden" name="orderId" value="<?php echo $order->OrderId;?>" />
					<button type="submit" class="button"><?php _e('Send', 'bfi') ?></button>
				</div>
			</div>
		</div>
	</form>
	</div>
<script type="text/javascript">
jQuery(function($)
		{
		    $("#formEmail").validate(
		    {
		    	invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        /*alert(validator.errorList[0].message);*/
                        validator.errorList[0].element.focus();
                    }
                },
		        rules:
		        {
		            email:
		            {
		                required: true,
		                email: true
		            }
		        },
		        messages:
		        {
		            email: "<?php _e('Please insert a valid email address!', 'bfi') ?>"
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
