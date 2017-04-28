<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$sitename = get_bloginfo();

$isFromSearch = false;
$base_url = get_site_url();
$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}

$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
$url_merchant_page = get_permalink( $merchantdetails_page->ID );
$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name);

$isportal = COM_BOOKINGFORCONNECTOR_ISPORTAL;
$showdata = COM_BOOKINGFORCONNECTOR_SHOWDATA;
$formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;

$jdate  = new DateTime('now'); // 3:20 PM, December 1st, 2012
$endjdate  = new DateTime('now -1 year'); // 3:20 PM, December 1st, 2012

$listDateArray = array();
while ($jdate > $endjdate) {
	$listDateArray[date_i18n('Ym01',$jdate->getTimestamp())] = date_i18n('F Y',$jdate->getTimestamp());
	$jdate->modify('-1 month');
}


$routeThanks = $routeMerchant .'/'._x('thanks', 'Page slug', 'bfi' );
$routeThanksKo = $routeMerchant .'/'._x('errors', 'Page slug', 'bfi' );

if ($merchant->RatingsContext !== 1 && $merchant->RatingsContext !== 3 ) {
//redirect almerchant senza possibilità di recensirla
		header ("Location: ". $route); 
		$app = JFactory::getApplication();
		$app->close();
}

$privacy = BFCHelper::GetPrivacy($language);
$additionalPurpose = BFCHelper::GetAdditionalPurpose($language);

$idrecaptcha = uniqid("bfirecaptcha");


?>
	<h2 class="bfi_merchant-name"><?php echo  $merchant->Name?> 
		<span class="com_bookingforconnector_resource-merchant-rating">
		  <?php for($i = 0; $i < $merchant->Rating; $i++) { ?>
		  <i class="fa fa-star"></i>
		  <?php } ?>
		</span>
	</h2>

<div class="clear"></div>

<form action="<?php echo $base_url ?>/bfi-api/v1/task/?task=sendRating&simple=1" method="post" class="form-horizontal" id="formRating" >
	<input type="hidden" id="merchantid" name="merchantid" value="<?php echo $merchant_id; ?>">
	<input type="hidden" id="cultureCode" name="cultureCode" value="<?php echo $GLOBALS['bfi_lang']; ?>">
	<input type="hidden" id="hashorder" name="hashorder" value="">
	<input type="hidden" id="resourceId" name="resourceId" value="">
	<input type="hidden" id="label" name="label" value="<?php echo $formlabel ?>">
	<input type="hidden" id="redirect" name="Redirect" value="<?php echo $routeThanks;?>" />
	<input type="hidden" id="redirecterror" name="Redirecterror" value="<?php echo $routeThanksKo;?>" />
	
	<div class="com_bookingforconnector_rating">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">   
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
				<label><?php _e('Name', 'bfi') ?></label>
				<input name="name" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12" placeholder="" value="">    
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
				<label><?php _e('City', 'bfi') ?></label>
				<input name="city" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12 " placeholder="" value="">   
			</div><!--/span-->
		</div><!--/row-->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
				<label><?php _e('Type of traveller', 'bfi'); ?></label>
				<select id="typologyid" name="typologyid">
					<option value="1"><?php _e('Solo travellers', 'bfi'); ?></option>
					<option value="2"><?php _e('Groups', 'bfi'); ?></option>
					<option value="3"><?php _e('Young couplet', 'bfi'); ?></option>
					<option value="4"><?php _e('Mature couples', 'bfi'); ?></option>
					<option value="5"><?php _e('Families with young children', 'bfi'); ?></option>
					<option value="6"><?php _e('Family with older children', 'bfi'); ?></option>
				</select>
			</div><!--/span-->
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
				<label><?php _e('Country', 'bfi'); ?></label>
				<select id="nation" name="nation" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12">
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
			</div><!--/span-->
		</div><!--/row-->                              
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
				<label><?php _e('E-mail', 'bfi'); ?>*</label>
				<input name="email" id="email" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12 " placeholder="email" value="" required="required">    
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>6">
				<label><?php _e('Confirm E-mail', 'bfi') ?>*</label>
				<input name="email2" id="email2" type="text" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12 " placeholder="email" value="" required="required">    
			</div><!--/span-->
		</div><!--/row-->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12">
				<label><?php _e('When did you travel?', 'bfi') ?></label>
				<select id="checkin" name="checkin">
					<?php foreach ($listDateArray as $itemKey=>$itemValue):?>
						<option value="<?php echo $itemKey ?>"><?php echo $itemValue ?></option>
					<?php endforeach; ?>
				</select>
			</div><!--/span-->
		</div><!--/row-->                              

		<br>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>4">
				<?php _e('Staff', 'bfi'); ?>:
					<input type="hidden" id="hfvalue1" name="hfvalue1" value="6">
					<span id="starscap1">6</span><br />
					<input title="1" type="radio" value="1" name="personale" class="starswrapper1 required">
					<input title="2" type="radio" value="2" name="personale" class="starswrapper1">
					<input title="3" type="radio" value="3" name="personale" class="starswrapper1">
					<input title="4" type="radio" value="4" name="personale" class="starswrapper1">
					<input title="5" type="radio" value="5" name="personale" class="starswrapper1">
					<input title="6" type="radio" checked value="6" name="personale" class="starswrapper1">
					<input title="7" type="radio" value="7" name="personale" class="starswrapper1">
					<input title="8" type="radio" value="8" name="personale" class="starswrapper1">
					<input title="9" type="radio" value="9" name="personale" class="starswrapper1">
					<input title="10" type="radio" value="10" name="personale" class="starswrapper1">
				<br />
				<?php _e('Services', 'bfi'); ?>:
					<input type="hidden" id="hfvalue2" name="hfvalue2" value="6">
					<span id="starscap2">6</span><br />
					<input title="1" type="radio" value="1" name="servizi" class="starswrapper2 required">
					<input title="2" type="radio" value="2" name="servizi" class="starswrapper2">
					<input title="3" type="radio" value="3" name="servizi" class="starswrapper2">
					<input title="4" type="radio" value="4" name="servizi" class="starswrapper2">
					<input title="5" type="radio" value="5" name="servizi" class="starswrapper2">
					<input title="6" type="radio" checked value="6" name="servizi" class="starswrapper2">
					<input title="7" type="radio" value="7" name="servizi" class="starswrapper2">
					<input title="8" type="radio" value="8" name="servizi" class="starswrapper2">
					<input title="9" type="radio" value="9" name="servizi" class="starswrapper2">
					<input title="10" type="radio" value="10" name="servizi" class="starswrapper2">
				<br />
				<?php _e('Clean', 'bfi'); ?>:
					<input type="hidden" id="hfvalue3" name="hfvalue3" value="6">
					<span id="starscap3">6</span><br />
					<input title="1" type="radio" value="1" name="pulizia" class="starswrapper3 required">
					<input title="2" type="radio" value="2" name="pulizia" class="starswrapper3">
					<input title="3" type="radio" value="3" name="pulizia" class="starswrapper3">
					<input title="4" type="radio" value="4" name="pulizia" class="starswrapper3">
					<input title="5" type="radio" value="5" name="pulizia" class="starswrapper3">
					<input title="6" type="radio" checked value="6" name="pulizia" class="starswrapper3">
					<input title="7" type="radio" value="7" name="pulizia" class="starswrapper3">
					<input title="8" type="radio" value="8" name="pulizia" class="starswrapper3">
					<input title="9" type="radio" value="9" name="pulizia" class="starswrapper3">
					<input title="10" type="radio" value="10" name="pulizia" class="starswrapper3">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>4">
				<?php _e('Comfort', 'bfi'); ?>:
					<input type="hidden" id="hfvalue4" name="hfvalue4" value="6">
					<span id="starscap4">6</span><br />
					<input title="1" type="radio" value="1" name="comfort" class="starswrapper4 required">
					<input title="2" type="radio" value="2" name="comfort" class="starswrapper4">
					<input title="3" type="radio" value="3" name="comfort" class="starswrapper4">
					<input title="4" type="radio" value="4" name="comfort" class="starswrapper4">
					<input title="5" type="radio" value="5" name="comfort" class="starswrapper4">
					<input title="6" type="radio" checked value="6" name="comfort" class="starswrapper4">
					<input title="7" type="radio" value="7" name="comfort" class="starswrapper4">
					<input title="8" type="radio" value="8" name="comfort" class="starswrapper4">
					<input title="9" type="radio" value="9" name="comfort" class="starswrapper4">
					<input title="10" type="radio" value="10" name="comfort" class="starswrapper4">
				<br />
				<?php _e('Value for money', 'bfi'); ?>:
					<input type="hidden" id="hfvalue5" name="hfvalue5" value="6">
					<span id="starscap5">6</span><br />
					<input title="1" type="radio" value="1" name="rapporto" class="starswrapper5 required">
					<input title="2" type="radio" value="2" name="rapporto" class="starswrapper5">
					<input title="3" type="radio" value="3" name="rapporto" class="starswrapper5">
					<input title="4" type="radio" value="4" name="rapporto" class="starswrapper5">
					<input title="5" type="radio" value="5" name="rapporto" class="starswrapper5">
					<input title="6" type="radio" checked value="6" name="rapporto" class="starswrapper5">
					<input title="7" type="radio" value="7" name="rapporto" class="starswrapper5">
					<input title="8" type="radio" value="8" name="rapporto" class="starswrapper5">
					<input title="9" type="radio" value="9" name="rapporto" class="starswrapper5">
					<input title="10" type="radio" value="10" name="rapporto" class="starswrapper5">
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>4 bfi-text-center">
				<div class="bfi-rating_valuation">
					<div ><?php _e('Valuation', 'bfi'); ?></div>
					<div class="bfi-rating-value" id="totale">6</div>
					<input type="hidden" id="hftotale" name="hftotale" value="6">
				</div>
			</div>
		</div>

		<br>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12">
				<label><?php _e("List of the facility's positive points", 'bfi'); ?> </label>
				<textarea name="pregi" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12" style="height:200px;"></textarea>    
			</div>
		</div>
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12">
				<label><?php _e("List of the facility's negative points", 'bfi'); ?> </label>
				<textarea name="difetti" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12" style="height:200px;"></textarea>    
			</div>
		</div>
			  
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>" style="display:none;">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label id="mbfcPrivacyTitle"><?php _e('Personal data treatment', 'bfi') ?></label>
				<textarea id="mbfcPrivacyText" name="form[privacy]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" readonly ><?php echo $privacy ?></textarea>    
			</div>
		</div><!--/row-->

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
             <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
		 	     <input name="form[accettazione]" id="agree" aria-invalid="true" aria-required="true" type="checkbox" required title="<?php _e('Mandatory', 'bfi') ?>">
			     <label  class="agreeprivacy"><?php _e('I accept personal data treatment', 'bfi') ?></label>
			</div>
		</div><!--/row-->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>" style="display:none;">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12">
				<label id="mbfcAdditionalPurposeTitle"><?php _e('Additional purposes', 'bfi') ?></label>
				<textarea id="mbfcAdditionalPurposeText" name="form[additionalPurpose]" class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12" style="height:200px;" readonly ><?php echo $additionalPurpose ?></textarea>    
			</div>
		</div><!--/row-->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>" style="display:<?php echo empty($additionalPurpose)?"none":"";?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 checkbox-wrapper">
				<input name="form[accettazioneadditionalPurpose]" id="agreeadditionalPurpose" aria-invalid="true" aria-required="true" required type="checkbox" title="<?php _e('Mandatory', 'bfi') ?>">
				<label class="agreeadditionalPurpose"><?php _e('I accept additional purposes', 'bfi') ?></label>
			</div>
		</div>

		<?php bfi_display_captcha($idrecaptcha);  ?>
<div id="recaptcha-error-<?php echo $idrecaptcha ?>" style="display:none"><?php _e('Mandatory', 'bfi') ?></div>
		   
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12 checkbox-wrapper">
				<input type="checkbox" value="true" name="privacyrating" id="privacyrating" required="required">
				<label for="privacyrating"><?php echo sprintf( __('I certify that this review is based on my own experience and is my genuine opinion of this accommodation, and that I have no personal or business relationship with this establishment, and have not been offered any incentive or payment originating from the establishment to write this review. I understand that %s has a zero-tolerance policy on fake reviews.', 'bfi'),$sitename); ?></label>    
			</div>
		</div>

		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW; ?>">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL; ?>12">
				<button type="submit" class="btn btn-info"><?php _e('send', 'bfi'); ?></button>
			</div>
		</div><!--/row-->
	</div>
</form>
<script type="text/javascript">
    	function setRating(field){
	     jQuery('.starswrapper'+field).rating({
		  split: 2,
		  focus: function(value, link){
		    jQuery('#starscap'+field).html(value || '');
		  },
		  blur: function(value, link){
		    jQuery('#starscap'+field).html(jQuery('#hfvalue'+field).val() || '');
		  },
		  callback: function(value, link){ 
		    jQuery('#starscap'+field).html(value || '');
			 jQuery('#hfvalue'+field).val(value)
			sommatoria();
		  }
		});
      }
	   
	   function sommatoria(){
	     var sommatotale = 0;
	     for (i=1;i<6 ;i++ ) {
		    sommatotale += parseInt(jQuery('#hfvalue'+i).val());
	     }
	     jQuery('#totale').html(Math.round( (sommatotale/5) *100 ) / 100);
	     jQuery('#hftotale').val(Math.round( (sommatotale/5) *100 ) / 100);
      }
      
      jQuery(document).ready(function () {
      	  for (i=1;i<6 ;i++ ) {
		    setRating(i);
	     }
		    jQuery("#formRating").validate(
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
		            },
					email2: {
						  equalTo: "#email"
					},
		        	confirmprivacy : "required",
					privacyrating : "required"
		        },
		        messages:
		        {
		        	confirmprivacy: "<?php _e('Mandatory', 'bfi') ?>",
		        	privacyrating: "<?php _e('Mandatory', 'bfi') ?>",
		            email: "<?php _e('Mandatory', 'bfi') ?>",
		            email2: "<?php _e('Mandatory', 'bfi') ?>"
		        },
		        highlight: function(label) {
			    	jQuery(label).closest('.control-group').removeClass('error').addClass('error');
			    },
			    success: function(label) {
			    	label
			    		.closest('.control-group').removeClass('error').addClass('success');
			    		//.text('ok!').addClass('valid')
			    },
			   submitHandler: function(form) {
					if (typeof grecaptcha === 'object') {
						var response = grecaptcha.getResponse(window.bfirecaptcha['<?php echo $idrecaptcha ?>']);
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
					
//					jQuery('#formRating').ajaxSubmit({
//						beforeSubmit: function(arr, $form, options) {
//							jQuery('#msgKo').hide()
//							$form.toggle();
//						},
//						success:   processJson
//					}); 
					 jQuery.blockUI();
					 form.submit();

			   }
		  });

			jQuery('.agreeprivacy').webuiPopover({
				title : jQuery("#mbfcPrivacyTitle").html(),
				content : jQuery("#mbfcPrivacyText").val(),
				container: "body",
				placement:"top"
			}); 
			jQuery('.agreeadditionalPurpose').webuiPopover({
				title : jQuery("#mbfcAdditionalPurposeTitle").html(),
				content : jQuery("#mbfcAdditionalPurposeText").val(),
				container: "body",
				placement:"top"
			}); 
			jQuery( window ).resize(function() {
			  jQuery('.agreeprivacy').webuiPopover('hide');

			});
			jQuery( window ).resize(function() {
			  jQuery('.agreeadditionalPurpose').webuiPopover('hide');

			});
			

	  });
</script>