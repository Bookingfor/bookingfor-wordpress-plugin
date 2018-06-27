<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR==1){
?>
<style type="text/css">
.ui-datepicker-trigger.activeclass:after {
  top: 35px !important;
}
</style>
<?php
}
	$base_url = get_site_url();
	$formRoute = $base_url."/bfi-api/v1/task/?task=".$task ."&simple=1" ;
	$language = $GLOBALS['bfi_lang'];
	if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		$language = ICL_LANGUAGE_CODE;
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$base_url .= "/".ICL_LANGUAGE_CODE;
		}
	}
//	$privacy = BFCHelper::GetPrivacy($language);
//	$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);
	$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	$minCapacityPaxes = 0;
	$maxCapacityPaxes = 12;
	
	$idrecaptcha = uniqid("bfirecaptcha");
	$idform = uniqid("merchantdetailscontacts");

	$routePrivacy = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_PRIVACYURL);
	$routeTermsofuse = str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL);

	$infoSendBtn = sprintf(__('Choosing <b>Send</b> means that you agree to <a href="%3$s" target="_blank">Terms of use</a> of %1$s and <a href="%2$s" target="_blank">privacy and cookies statement.</a>.' ,'bfi'),$sitename,$routePrivacy,$routeTermsofuse);


	$formdisplay = "none";
	if (($layout  == '' && $currentView=='merchant') || isset($popupview)) {
		$formdisplay = "";
	}else{
	?>
		<span href="" class="bfi-opencontactform bfi-btn bfi-alternative"><?php _e('Request for Information', 'bfi') ?></span>
	<?php } ?>	

	<div class="bfi-contacts" style="display:<?php echo $formdisplay ?>">
		<h2 >
            
		<!-- <span class="bfi_merchantdetails-rating bfi_merchantdetails-rating<?php //echo $merchant->Rating; ?>">
			<span class="bfi_merchantdetails-ratingText">Rating <?php //echo $merchant->Rating; ?></span>
		</span>  -->
	</h2>
	<div align="center" class="bfi-form-contacts">
		<form method="post" id="<?php echo $idform ?>" class="form-validate merchantdetailscontacts" action="<?php echo $formRoute; ?>" novalidate="novalidate">
			<div class="bfi-form-field">
				<div class="bfi_form-title"><?php _e('Request for Information', 'bfi'); ?></div>
				<?php if(isset($resource)) {?>		
					<div class="">
						<?php echo $resource->Name; ?>
					</div><!--/span-->
				<?php } ?>	
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Name', 'bfi'); ?> *" type="text" value="" size="50" name="form[Name]" id="Name" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Surname', 'bfi'); ?> *" type="text" value="" size="50" name="form[Surname]" id="Surname" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Email', 'bfi'); ?> *" type="email" value="" size="50" name="form[Email]" id="Email" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Phone', 'bfi'); ?>*" type="text" value="" size="20" name="form[Phone]" id="Phone" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Address', 'bfi'); ?>*" type="text" value="" size="50" name="form[Address]" id="Address" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Cap', 'bfi'); ?>*" type="text" value="" size="20" name="form[Cap]" id="Cap" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('City', 'bfi'); ?>*" type="text" value="" size="50" name="form[City]" id="City" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
					<input placeholder="<?php _e('Province', 'bfi'); ?>*" type="text" value="" size="20" name="form[Provincia]" id="Provincia" required="" title="<?php _e('Mandatory', 'bfi'); ?>" aria-required="true">
				</div>
				<div class="bfi_form_txt">
						<select id="formNation" name="form[Nation]" class="bfi_input_select width90percent">
							<option value="AR">Argentina</option>
							<option value="AM">Armenia</option>
							<option value="AU">Australia</option>
							<option value="AZ">Azerbaigian</option>
							<option value="BE">Belgium</option>
							<option value="BY">Bielorussia</option>
							<option value="BA">Bosnia-Erzegovina</option>
							<option value="BR">Brazil</option>
							<option value="BG">Bulgaria</option>
							<option value="CA">Canada</option>
							<option value="CN">China</option>
							<option value="HR">Croatia</option>
							<option value="CY">Cyprus</option>
							<option value="CZ">Czech Republic</option>
							<option value="DK">Denmark</option>
							<option value="DE" <?php if($language == "de-DE") {echo "selected";}?>>Deutschland</option>
							<option value="EG">Egipt</option>
							<option value="EE">Estonia</option>
							<option value="FI">Finland</option>
							<option value="FR" <?php if($language == "fr-FR") {echo "selected";}?>>France</option>
							<option value="GE">Georgia</option>
							<option value="EN" <?php if($language == "en-GB") {echo "selected";}?>>Great Britain</option>
							<option value="GR" <?php if($language == "el-GR") {echo "selected";}?>>Greece</option>
							<option value="HU">Hungary</option>
							<option value="IS">Iceland</option>
							<option value="IN">Indian</option>
							<option value="IE">Ireland</option>
							<option value="IL">Israel</option>
							<option value="IT" <?php if($language == "it-IT") {echo "selected";}?>>Italia</option>
							<option value="JP">Japan</option>
							<option value="LV">Latvia</option>
							<option value="LI">Liechtenstein</option>
							<option value="LT">Lithuania</option>
							<option value="LU">Luxembourg</option>
							<option value="MK">Macedonia</option>
							<option value="MT">Malt</option>
							<option value="MX">Mexico</option>
							<option value="MD">Moldavia</option>
							<option value="NL">Netherlands</option>
							<option value="NZ">New Zealand</option>
							<option value="NO">Norvay</option>
							<option value="AT">Österreich</option>
							<option value="PL" <?php if($language == "pl-PL") {echo "selected";}?>>Poland</option>
							<option value="PT">Portugal</option>
							<option value="RO">Romania</option>
							<option value="SM">San Marino</option>
							<option value="SK">Slovakia</option>
							<option value="SI">Slovenia</option>
							<option value="ZA">South Africa</option>
							<option value="KR">South Korea</option>
							<option value="ES" <?php if($language == "es-ES") {echo "selected";}?>>Spain</option>
							<option value="SE">Sweden</option>
							<option value="CH">Switzerland</option>
							<option value="TJ">Tagikistan</option>
							<option value="TR">Turkey</option>
							<option value="TM">Turkmenistan</option>
							<option value="US" <?php if($language == "en-US") {echo "selected";}?>>USA</option>
							<option value="UA">Ukraine</option>
							<option value="UZ">Uzbekistan</option>
							<option value="VE">Venezuela</option>
						</select>
					</div>
<?php if(isset($resource)) {?>		
					<div class="bfi-hide">
						<?php echo $resource->Name; ?>
					</div><!--/span-->
				<input type="hidden" id="resourceId" name="form[resourceId]" value="<?php echo $resource->ResourceId;?>" > 
<?php 
$minCapacityPaxes = $resource->MinCapacityPaxes;
$maxCapacityPaxes = $resource->MaxCapacityPaxes;
if(empty($maxCapacityPaxes)) {
	$maxCapacityPaxes = 10;
}
 ?>
<?php } ?>
		<?php if ($merchant->HasResources && $layout !== 'onsellunits' && $layout !== 'onsellunit' && $currentView !== 'onsellunit'):?>
				<div class="bfi-row">   
					<div class="bfi-col-md-6 bfi-inline-field-right">
						<div class="bfi-inline-field"><label><?php _e('Check-in', 'bfi'); ?> </label></div>
						<input type="text" name="form[CheckIn]" id="<?php echo $checkinId ?>" value="<?php echo $checkin->format('d/m/Y') ?>" class="ui-datepicker-simple" />	
					</div>
					<div class="bfi-col-md-6 bfi-inline-field-left">
						<div class="bfi-inline-field"><label><?php _e('Check-out', 'bfi'); ?></label></div>
						<input type="text" name="form[CheckOut]" id="<?php echo $checkoutId ?>" value="<?php echo $checkout->format('d/m/Y') ?>" class="ui-datepicker-simple" />	
					</div>
				</div>
                
                
				<div class="bfi-inline-field-pers"><label><?php _e('Persons', 'bfi') ?> </label></div>
				<div class="bfi_form_txt">
				   <!-- <select name="form[Totpersons]" class="bfi-col-md-4">-->
					<select name="form[Totpersons]" class="bfi_input_select">
					<?php
					foreach (range($minCapacityPaxes, $maxCapacityPaxes) as $number) {
						?> <option value="<?php echo $number ?>" <?php selected( 2, $number ); ?>><?php echo $number ?></option><?php
					}
					?>
						</select>
				</div>
		<?php endif ?>	

		
		<div class="bfi-row">
            <div class="bfi-col-md-12" style="padding:0;">
              <textarea name="form[note]" style="height:200px;"  placeholder="<?php _e('Special Requests', 'bfi'); ?>"></textarea>    
            </div>
			<div class="bfi-col-md-12" style="display:none;">
				<br />
				<label id="mbfcPrivacyTitle"><?php _e('Personal data treatment', 'bfi') ?></label>
				<textarea id="mbfcPrivacyText" name="form[privacy]" class="bfi-col-md-12" style="height:200px;" readonly ><?php echo $privacy ?></textarea>    
			</div>
        </div>
			
		<div class="bfi-row">
             <div class="bfi-col-md-12 bfi-checkbox-wrapper">
			<input name="form[optinemail]" id="optinemail" type="checkbox">
			<label for="optinemail"><?php echo sprintf(__('Send me promotional emails from %1$s', 'bfi'),$sitename) ?></label>
			</div>
        </div>
		<?php bfi_display_captcha($idrecaptcha);  ?>
<div id="recaptcha-error-<?php echo $idrecaptcha ?>" style="display:none"><?php _e('Mandatory', 'bfi') ?></div>

				<input type="hidden" id="actionform" name="actionform" value="<?php echo $formlabel ?>" />
				<input type="hidden" name="form[merchantId]" value="<?php echo $merchant->MerchantId;?>" > 
				<input type="hidden" id="orderType" name="form[orderType]" value="<?php echo $orderType ?>" />
				<input type="hidden" id="cultureCode" name="form[cultureCode]" value="<?php echo $language;?>" />
				<input type="hidden" id="Fax" name="form[Fax]" value="" />
				<input type="hidden" id="VatCode" name="form[VatCode]" value="" />
				<input type="hidden" id="label" name="form[label]" value="" />
				<input type="hidden" id="redirect" name="form[Redirect]" value="<?php echo $routeThanks;?>" />
				<input type="hidden" id="redirecterror" name="Redirecterror" value="<?php echo $routeThanksKo;?>" />
<?php if(isset($popupview)){  ?>
		<div class="bfi-row bfi-footer-book" >
					<div class="bfi-col-md-10">
					<?php echo $infoSendBtn ?>
					</div>
					<div class="bfi-col-md-2 bfi-footer-send"><button type="submit" class="bfi-btn"><?php _e('Send', 'bfi') ?></button></div>
				</div>
<?php }else{  ?>
				<div class="bfi-footer-book" >
					<?php echo $infoSendBtn ?>
				</div>
				<div class=""><button type="submit" class="bfi-btn" style="width: 100%;" ><?php _e('Send', 'bfi') ?></button></div>
<?php } ?>

    
		</div>
</form>

	</div>
</div>
<script type="text/javascript">
<!--
		function checkDate<?php echo $checkinId?>($, obj, selectedDate) {
			instance = obj.data("datepicker");
			date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings);
			var d = new Date(date);
			d.setDate(d.getDate() + 1);
			$("#<?php echo $checkoutId?>").datepicker("option", "minDate", d);
		}

		jQuery(function($){
	            var <?php echo $checkinId?> = null;
                jQuery(function($) {
                    <?php echo $checkinId?> = function() { $("#<?php echo $checkinId?>").datepicker({
                        defaultDate: "+2d"
                        ,dateFormat: "dd/mm/yy"
                        , numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
						, minDate: '+0d'
						, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); }
						, beforeShow: function(dateText, inst) { 
							jQuery(this).attr("disabled", true);
							jQuery(inst.dpDiv).addClass('bfi-calendar');
							jQuery('#ui-datepicker-div').attr('data-before',"");
							jQuery('#ui-datepicker-div').addClass("bfi-checkin");
							jQuery('#ui-datepicker-div').removeClass("bfi-checkout");
							setTimeout(function() {
								jQuery("#ui-datepicker-div div.bfi-title").remove();
								jQuery("#ui-datepicker-div").prepend( "<div class=\"bfi-title\">Check-in</div>" );
							}, 1);
							}
						, onSelect: function(date) { checkDate<?php echo $checkinId?>(jQuery, jQuery(this), date); }
						, changeMonth: false
						, changeYear: false
                    })};
                    <?php echo $checkinId?>();
                });
                
                var <?php echo $checkoutId?> = null;
                jQuery(function($) {
                    <?php echo $checkoutId?> = function() { $("#<?php echo $checkoutId?>").datepicker({
                        defaultDate: "+2d"
                        ,dateFormat: "dd/mm/yy"
                        , numberOfMonths: parseInt("<?php echo COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR;?>")
						, onClose: function(dateText, inst) { jQuery(this).attr("disabled", false); }
						, beforeShow: function(dateText, inst) { 
							jQuery(this).attr("disabled", true); 
							jQuery(inst.dpDiv).addClass('bfi-calendar');
							jQuery('#ui-datepicker-div').attr('data-before',"");
							jQuery('#ui-datepicker-div').removeClass("bfi-checkin");
							jQuery('#ui-datepicker-div').addClass("bfi-checkout");
							setTimeout(function() {
								jQuery("#ui-datepicker-div div.bfi-title").remove();
								jQuery("#ui-datepicker-div").prepend( "<div class=\"bfi-title\">Check-out</div>" );
							}, 1);
							}
						, minDate: '+1d'
						, changeMonth: false
						, changeYear: false
                    })};
                    <?php echo $checkoutId?>();
                });

			jQuery(".bfi-opencontactform").click(function(e) {
				jQuery(this).hide();
				jQuery(".bfi-contacts").slideDown("slow",function() {
					if (jQuery.prototype.masonry){
						jQuery('.main-siderbar, .main-siderbar1').masonry('reload');
					}
				});
			});

			jQuery('.bfi-agreeprivacy').webuiPopover({
				title : jQuery("#mbfcPrivacyTitle").html(),
				content : jQuery("#mbfcPrivacyText").val(),
				container: "body",
				placement:"top",
				style:'bfi-webuipopover'
			}); 
			jQuery('.agreeadditionalPurpose').webuiPopover({
				title : jQuery("#mbfcAdditionalPurposeTitle").html(),
				content : jQuery("#mbfcAdditionalPurposeText").val(),
				container: "body",
				placement:"top",
				style:'bfi-webuipopover'
			}); 
			jQuery( window ).resize(function() {
			  jQuery('.bfi-agreeprivacy').webuiPopover('hide');

			});
			jQuery( window ).resize(function() {
			  jQuery('.agreeadditionalPurpose').webuiPopover('hide');

			});


			
					$("#<?php echo $idform ?>").validate(
					{
						invalidHandler: function(form, validator) {
							var errors = validator.numberOfInvalids();
							if (errors) {
								/*alert(validator.errorList[0].message);*/
								validator.errorList[0].element.focus();
							}
						},
						//errorPlacement: function(error, element) { //just nothing, empty  },
						errorClass: "bfi-error",
						highlight: function(label) {
							//$(label).removeClass('error').addClass('error');
							//$(label).closest('.control-group').removeClass('error').addClass('error');
						},
						success: function(label) {
							//label.addClass("valid").text("Ok!");
		//					$(label).remove();
							//label.hide();
							//label.removeClass('error');
							//label.closest('.control-group').removeClass('error');
						},
						submitHandler: function(form) {
							if (typeof grecaptcha === 'object') {
								var response = grecaptcha.getResponse(window.bfirecaptcha['<?php echo $idrecaptcha ?>']);
//								var response = grecaptcha.getResponse();
								//recaptcha failed validation
								if(response.length == 0) {
									$('#recaptcha-error-<?php echo $idrecaptcha ?>').show();
									return false;
								}
								//recaptcha passed validation
								else {
									$('#recaptcha-error-<?php echo $idrecaptcha ?>').hide();
								}					 
							}
<?php if(isset($popupview)){  ?>
            // Use Ajax to submit form data
					$("#<?php echo $idform ?>").ajaxSubmit({
						beforeSubmit: function(arr, $form, options) {
							//jQuery.blockUI({message: ''});
							bookingfor.waitBlockUI();
							$("#<?php echo $idform ?>").html('<?php _e('Sending...', 'bfi') ?>');
						},
						success:    function(result) {
							jQuery.unblockUI();
							$("#<?php echo $idform ?>").html(result);
						}
					}); 

<?php }else{  ?>
					var $form = $(form);
					if($form.valid()){
						//jQuery.blockUI({message: ''});
						bookingfor.waitBlockUI();
						if ($form.data('submitted') === true) {
							 return false;
						} else {
							// Mark it so that the next submit can be ignored
							$form.data('submitted', true);
							form.submit();
						}
					}
<?php }  ?>
						}

					});
				});
	


jQuery(document).ready(function() {
    jQuery(".merchantdetailscontacts .ui-datepicker-simple").click(function() {
        jQuery(".ui-datepicker-calendar td").click(function() {
            if (jQuery(this).hasClass('ui-state-disabled') == false) {
                jQuery(".merchantdetailscontacts .ui-datepicker-simple").each(function() {
                    jQuery(this).addClass("activeclass");
                });
                jQuery(".merchantdetailscontacts .ui-datepicker-simple").removeClass("activeclass");
                jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + 5 + "px");
            }
        });
    })
    jQuery(".merchantdetailscontacts .ui-datepicker-simple").click(function() {
        jQuery("#ui-datepicker-div").css("top", jQuery(this).offset().top + 5 + "px");
        jQuery(".merchantdetailscontacts .ui-datepicker-simple").each(function() {
            jQuery(this).removeClass("activeclass");
        });
        jQuery(this).addClass("activeclass");

    });
});
jQuery(window).load(function() {
	if (!!jQuery.uniform){
		jQuery.uniform.restore(jQuery('.merchantdetailscontacts input[type="checkbox"]'));
		jQuery.uniform.restore(jQuery('.merchantdetailscontacts select'));
	}
});

//-->
</script>