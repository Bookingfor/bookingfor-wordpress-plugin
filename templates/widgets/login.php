<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
$base_url = get_site_url();
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url = "/" .ICL_LANGUAGE_CODE;
		}
}

$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
$accountLoginUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ACCOUNTLOGIN);
$accountRegistrationUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ACCOUNTREGISTRATION);
$accountForgotPasswordUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ACCOUNTFORGOTPASSWORD);

$formRouteLogin = $base_url . "/bfi-api/v1/task/?task=bfilogin"; 

$currModID = uniqid('bfilogin');

?>
<?php 
echo $before_widget;
// Check if title is set
if ( $title ) {
  echo $before_title . $title . $after_title;
}

?>
<div class="bfi-mod-bookingforlogin">
<?php if($currUser==null) { ?>

<form action="<?php echo $formRouteLogin ?>" id="bfi-login-form<?php echo $currModID ?>" class="bfi-form bfi-form-vertical bfi-row">
	<div class="bfi-container">
		<div id="bfi-login-msg<?php echo $currModID ?>">
			<span id="bfi-text-login-msg<?php echo $currModID ?>"></span>
		</div>
<!-- pchLogin -->
		<div id="pchLogin">
			<div class="bfi_form_txt">
				<label for="bfiloginEmail<?php echo $currModID ?>" ><?php _e('Email', 'bfi'); ?></label>
				<input id="bfiloginEmail<?php echo $currModID ?>" value="" name="email" type="email"  class="bfi-inputtext" placeholder='<?php _e('Email', 'bfi') ?>'
				autocomplete="email" onfocus="this.removeAttribute('readonly');" readonly 
				data-rule-required="true" data-rule-email="true" data-msg-required="<?php _e('This field is required.', 'bfi') ?>" data-msg-email="<?php _e('Please enter a valid email address', 'bfi') ?>" aria-required="true"
				/>
			</div>
			<div class="bfi_form_txt">
				<label for="bfiloginPassword<?php echo $currModID ?>"><?php _e('Password', 'bfi'); ?></label>
				<input id="bfiloginPassword<?php echo $currModID ?>" name="password" type="password" class="bfi-inputtext" placeholder='<?php _e('Password', 'bfi') ?>' 
				data-rule-required="true" data-msg-required="<?php _e('This field is required.', 'bfi') ?>" aria-required="true"
				/>
			</div>
			<div class="checkbox" style="display: none">
				<label>
					<input type="checkbox"> Remember me
				</label>
			</div>
		</div>
<!-- pchTwoFactorAuthentication -->
		<div id="pchTwoFactorAuthentication<?php echo $currModID ?>" class="bfi-hide">
			<div id="pchTwoFactorAuthenticationError<?php echo $currModID ?>">
				<?php _e('An email was sent to {0}. Please check your email box and type the authentication code you have received.', 'bfi') ?>
			</div>
			<div class="bfi_form_txt">
				<label for="twoFactorAuthCode<?php echo $currModID ?>"><?php _e('Two-factor secure authentication', 'bfi') ?></label>
				<input id="twoFactorAuthCode<?php echo $currModID ?>" name="twoFactorAuthCode" type="text" placeholder="<?php _e('Two-factor secure authentication', 'bfi') ?>" data-rule-required="true" data-msg-required="<?php _e('This field is required.', 'bfi') ?>" aria-required="true" />
			</div>
		</div>
<!-- bfibtnSendLogin -->
		<div class="bfi-form-sep">
			<a href="javascript: void(0);" class="bfi-btn bfi-btn-warning bfi-btn-lg bfi-btn-block" id="bfibtnSendLogin<?php echo $currModID ?>"><?php _e('Login', 'bfi') ?></a>
			<a href="javascript: void(0);" class="bfi-btn bfi-btn-warning bfi-btn-lg bfi-btn-block " style="display:none" id="bfibtnSendConfirm<?php echo $currModID ?>"><?php _e('Confirm', 'bfi') ?></a>
		</div>
		<div class="bfi-form-sep">
			<a href="javascript: bfilostpass();" id="bfibtnforgotpassword<?php echo $currModID ?>" target="" class="bfi-login-link"><?php _e('Lost password', 'bfi') ?></a>
			<a href="<?php echo $accountRegistrationUrl ?>" target="_blank" class="bfi-login-link"><?php _e('Sign in', 'bfi') ?></a>
		</div>
	</div>
</form>
<form id="bfi-lostpass-form<?php echo $currModID ?>" action="<?php echo $accountForgotPasswordUrl ?>" class="bfi-form bfi-form-vertical bfi-row bfi-hide">
		<div id="pchLostpass<?php echo $currModID ?>" class="bfi-container">
			<div>
				<?php _e('Forgot Password', 'bfi') ?>
			</div>
			 <div>              
                <?php _e('If you are having difficulty logging in, you can reset your password by typing the Email Address that you used while registration. You will receive an email with a new password.', 'bfi') ?>
            </div>
			<div class="bfi_form_txt">
				<input id="bfiLostEmail<?php echo $currModID ?>" name="email" type="email"  class="bfi-inputtext" placeholder='<?php _e('Email', 'bfi') ?>' 
				data-rule-required="true" data-rule-email="true" data-msg-required="<?php _e('This field is required.', 'bfi') ?>" data-msg-email="<?php _e('Please enter a valid email address', 'bfi') ?>" aria-required="true"
			</div>
		</div>
		<div class="bfi-clearfix"></div>
		<div class="bfi-form-sep">
			<a href="javascript: void(0);" class="bfi-btn" id="bfibtnSendLostpass<?php echo $currModID ?>"><?php _e('Send', 'bfi') ?></a>
		</div>
		<div class="bfi-form-sep">
			<a href="javascript: bfilostpassback();" class="bfi-login-link"><?php _e('Back', 'bfi') ?></a>
		</div>
	</div>
</form>

<?php }else{ ?>
<div class="bfi-form bfi-form-vertical bfi-row">
	<?php _e('Welcome', 'bfi') ?> <?php echo $currUser->Name ?> <?php echo $currUser->Surname  ?>
	<a href="<?php echo $accountLoginUrl ?>" class="bfi-login-link" target="_blank"><?php _e('My account', 'bfi') ?></a>
	<div class="bfi-form-sep">
		<a href="javascript: void(0);" class="bfi-btn" id="bfibtnLogout<?php echo $currModID ?>"><?php _e('Logout', 'bfi') ?></a>
	</div>
</div>
<?php } ?>
<!-- Other button -->
<?php echo $after_widget; ?>
<script type="text/javascript">
jQuery(function($)
		{
		    jQuery("#bfi-login-form<?php echo $currModID ?> ").validate(
		    {
				errorClass: "bfi-error",
				submitHandler: function(form) {
					var $form = jQuery(form);
					bookingfor.waitSimpleWhiteBlock($form);
					jQuery(form).ajaxSubmit({
						dataType:'json',
						success:    function(data) {
							jQuery($form).unblock();
							if (data == "-1") {
								jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php _e('Login success', 'bfi') ?>');
								jQuery('#pchLogin<?php echo $currModID ?>').hide();
								location.reload();
							}
							else {
								if (data == "1") {
									jQuery('#pchTwoFactorAuthentication<?php echo $currModID ?>').show();
									jQuery('#btnresendcode<?php echo $currModID ?>').show();
									jQuery('#btnforgotpassword<?php echo $currModID ?>').hide();
									jQuery('#bfibtnSendConfirm<?php echo $currModID ?>').show();
									jQuery('#bfibtnSendLogin<?php echo $currModID ?>').hide();
									
									var currmsg = jQuery('#pchTwoFactorAuthenticationError<?php echo $currModID ?>').html();
									currmsg = currmsg.replace('{0}', jQuery('#bfiloginEmail<?php echo $currModID ?>').val());
									jQuery('#pchTwoFactorAuthenticationError<?php echo $currModID ?>').html(currmsg);
									jQuery('#pchLogin<?php echo $currModID ?>').hide();
								} else if (data == "2") {
									$('#pchTwoFactorAuthenticationError<?php echo $currModID ?>').html("<?php _e('Code not valid.', 'bfi') ?>");
								} else if (data.length > 3 && data.substring(0, 1) == "3") {
									var timelock = data.substring(2, data.length);
									var d = new Date(timelock);
									var timelockStr = d.toLocaleString('<?php echo substr($language,0,2); ?>')
									var currmsg = "<?php _e('Access not valid. Access will be denied until {0}.', 'bfi') ?>";
									currmsg = currmsg.replace('{0}', timelockStr);
									$('#pchLogin<?php echo $currModID ?>').show();
									$('#pchTwoFactorAuthentication<?php echo $currModID ?>').hide();
									$('#bfibtnforgotpassword<?php echo $currModID ?>').show();
									$('#btnresendcode<?php echo $currModID ?>').hide();
									$('#twoFactorAuthCode<?php echo $currModID ?>').val('');
									$('#pchLoginAuthenticationError<?php echo $currModID ?>').html(currmsg);
									$('#pchLoginAuthenticationError<?php echo $currModID ?>').show();
								} else {
									jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php _e('Login failed', 'bfi') ?>');
								}
							}
						}
					});
				}
		    });
			var v = jQuery("#bfi-lostpass-form<?php echo $currModID ?>").validate({
				errorClass: "bfi-error",
				submitHandler: function(form) {
					var $form = jQuery(form);
					bookingfor.waitSimpleWhiteBlock($form);
					jQuery(form).ajaxSubmit({
						success:    function(data) {
							bfilostpassback();
							$form.unblock();
							if (data==true)
							{
								jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php _e('Link has been sent to registered email', 'bfi') ?>');
							}else{
								jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php _e('Please enter valid registered email', 'bfi') ?>');
							}
						}
					});
				}
			});
			jQuery('#bfibtnSendLogin<?php echo $currModID ?>').click(function() {       
				jQuery("#bfi-login-form<?php echo $currModID ?>").submit();
			});
			jQuery('#bfibtnSendConfirm<?php echo $currModID ?>').click(function() {       
				jQuery("#bfi-login-form<?php echo $currModID ?>").submit();
			});
			
			jQuery('#bfibtnSendLostpass<?php echo $currModID ?>').click(function() {       
				jQuery("#bfi-lostpass-form<?php echo $currModID ?>").submit();
			});
			jQuery('#bfibtnLogout<?php echo $currModID ?>').click(function() {       
				var queryMG = "task=bfilogout";
				jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
						if(data=="-1"){
							location.reload();
						}; 
				},'json');				
			});
		});
	function bfilostpass(){
		jQuery("#bfi-login-form<?php echo $currModID ?> ").hide();
		jQuery("#bfi-lostpass-form<?php echo $currModID ?>").show();
	}
	function bfilostpassback(){
		jQuery("#bfi-login-form<?php echo $currModID ?> ").show();
		jQuery("#bfi-lostpass-form<?php echo $currModID ?>").hide();
	}

</script>
</div>	<!-- module -->
