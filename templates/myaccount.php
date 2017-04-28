<?php
/**
 * The Template for displaying my account
 *
 * @see 	   
 * @author 	Bookingfor
 * @package 	Bookingfor/Templates
 * @version     2.0.5
 */

if (!defined('ABSPATH')) {
  exit;
}
get_header();
if(!is_user_logged_in()) {
  print __('You need to be loggedin in order to access this page.', 'bfi');
  get_footer();
  exit;
}
?>
<?php
$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
$formatDate = 'm/d/Y';
$now  = new DateTime('now'); // 3:20 PM, December 1st, 2012

$merchantImageUrl = BFI()->plugin_url() . "/assets/images/defaults/default-s6.jpeg";
$merchantImagePath = BFCHelper::getImageUrlResized('merchant', "[img]",'medium');
$merchantImagePathError = BFCHelper::getImageUrl('merchant', "[img]",'medium');

$language = $GLOBALS['bfi_lang'];
$languageForm ='';
if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
		global $sitepress;
		if($sitepress->get_current_language() != $sitepress->get_default_language()){
			$languageForm = "/" .ICL_LANGUAGE_CODE;
		}
}
$base_url = get_site_url();
$listsId = array();
$currencyclass = bfi_get_currentCurrency();

$model = new BookingForConnectorModelOrders;
$orders = $model->GetOrdersByExternalUser();
$contact_data = $model->getContactData();
$orderCount = $model->GetOrdersByExternalUserCount($language);
$email = '';
$name = '';
$surname = '';
$phone = '';
$customerLanguage = '';
$birthday =  new DateTime();
$address = '';
$city = '';
$province = '';
$country = '';
$zip = '';
$gender = '';

$base_url = get_site_url(null,'', $usessl ? "https" : null);
$formRoute = $base_url .'/bfi-api/v1/task?task=insertContact';

if($contact_data != NULL) {
  $formRoute = $base_url .'/bfi-api/v1/task?task=updateContact';
  $email = $contact_data->GetContactData->Email;
  $name = $contact_data->GetContactData->Name;
  $surname = $contact_data->GetContactData->MainAddress->Surname;
  $phone = $contact_data->GetContactData->Phone;
  $customerLanguage = $contact_data->GetContactData->Language;
//  $birthday = $contact_data->GetContactData->Birthday;
  if (isset($contact_data->GetContactData->Birthday)) $birthday =  DateTime::createFromFormat($formatDate,BFCHelper::parseJsonDate($contact_data->GetContactData->Birthday,$formatDate));

  $address = $contact_data->GetContactData->MainAddress->Address;
  $city = $contact_data->GetContactData->MainAddress->City;
  $province = $contact_data->GetContactData->MainAddress->Province;
  $country = $contact_data->GetContactData->MainAddress->Country;
  $zip = $contact_data->GetContactData->MainAddress->ZipCode;
  $gender = $contact_data->GetContactData->Gender;

}
?>
<div id="my-account-tabs">
  <ul>
    <li><a href="#tabs-1"><i class="fa fa-user" aria-hidden="true"></i> <?php _e('Personal Informations', 'bfi') ?></a></li>
    <li><a href="#tabs-2"><i class="fa fa-briefcase" aria-hidden="true"></i> <?php _e('Reservations', 'bfi') ?></a></li>
    <li><a href="#tabs-3"><i class="fa fa-cog" aria-hidden="true"></i> <?php _e('Settings', 'bfi') ?></a></li>
  </ul>
  <div id="tabs-1" class="tabcontainer">
  <!-- social network Block start-->
	<div class="social-networks hidden">
		<h3><?php _e('Social Network', 'bfi') ?></h3>
		<h5><?php _e('Give your social profiles', 'bfi') ?></h5>
		<p><?php _e('Connect with your account via your social networks , one less password to remember!', 'bfi') ?></p>
		<a href="" class="btn btn-default"><i class="fa fa-facebook-official" aria-hidden="true"></i> Facebook</a> 
		<a href="" class="btn btn-default"><i class="fa fa-google-plus" aria-hidden="true"></i>Google</a>
	</div>
   
    <!-- social network Block end -->
      <!-- Personal Informations Block start-->
	<div class="social-networks personal-networks">
		<h3><?php _e('Personal Informations', 'bfi') ?></h3>
		<h5><?php _e('Your profile', 'bfi') ?></h5>
		<p><?php _e('This information is used to automatically fill in your details and book more quickly. We do not share it publicly.', 'bfi') ?></p>
		<form method="post" id="resourcedetailsrequest" class="form-validate" action="<?php echo $formRoute; ?>">
			<div class="guest-form-details">
				<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?>">
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
						<div class="form-group">
							<label for="name"><?php _e('Name', 'bfi') ?>:</label>
							<input name="form[Name]" value="<?php print $name; ?>" type="text" class="form-control" id="name">
						</div>
						<div class="form-group">
							<label for="surname"><?php _e('Surname', 'bfi') ?>:</label>
							<input name="form[Surname]" value="<?php print $surname; ?>" type="text" class="form-control" id="surname">
						</div>
						<div class="form-group">
							<label for="email"><?php _e('Email', 'bfi') ?>:</label>
							<input name="form[Email]" value="<?php print $email; ?>" type="text" class="form-control" id="email">
						</div>
						<div class="form-group">
							<label for="BirthDate"><?php _e('Date of birth', 'bfi') ?></label>
							<input name="form[Birthday]" value="<?php print $birthday->format('d/m/Y'); ?>" type="text" class="form-control" id="BirthDate">
						</div>
						<div class="form-group">
							<label for="Gender"><?php _e('Gender', 'bfi') ?>:</label>
							<select name="form[Gender]" id="Gender">
								<option value="0" <?php if($gender == 0) {echo "selected";}?> <?php print $gender; ?> ><?php _e('Male', 'bfi') ?></option>
								<option value="1" <?php if($gender == 1) {echo "selected";}?> ><?php _e('Female', 'bfi') ?></option>
							</select>
						</div>
					</div>
					<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>6">
						<div class="form-group">
							<label for="address"><?php _e('Address', 'bfi') ?></label>
							<input name="form[Address]" value="<?php print $address; ?>" type="text" class="form-control" id="address">
						</div>
						<div class="form-group">
							<label for="city"><?php _e('City', 'bfi') ?></label>
							<input value="<?php print $city; ?>" name="form[City]" type="text" class="form-control" id="city">
						</div>
						<div class="form-group">
							<label for="country"><?php _e('Country', 'bfi') ?>:</label>
							<select name="form[Nation]" id="country">
								<option value="IT"  <?php if($country == "IT") {echo "selected";}?> >Italia</option>
								<option value="EN" <?php if($country == "EN") {echo "selected";}?>>Great Britain</option>
								<option value="DE" <?php if($country == "DE") {echo "selected";}?>>Deutschland</option>
								<option value="FR" <?php if($country == "FR") {echo "selected";}?>>France</option>
								<option value="ES" <?php if($country == "ES") {echo "selected";}?>>Spain</option>
								<option value="HU" <?php if($country == "HU") {echo "selected";}?>>Hungary</option
								<option value="BE" <?php if($country == "BE") {echo "selected";}?>>Belgium</option>
								<option value="AT" <?php if($country == "AT") {echo "selected";}?>>Österreich</option>
								<option value="CZ" <?php if($country == "CZ") {echo "selected";}?>>Czech Republic</option>
								<option value="HR" <?php if($country == "HR") {echo "selected";}?>>Croatia</option>
								<option value="BR" <?php if($country == "BR") {echo "selected";}?>>Brazil</option>
								<option value="AR" <?php if($country == "AR") {echo "selected";}?>>Argentina</option>
								<option value="AM" <?php if($country == "AM") {echo "selected";}?>>Armenia</option>
								<option value="AU" <?php if($country == "AU") {echo "selected";}?>>Australia</option>
								<option value="AZ" <?php if($country == "AZ") {echo "selected";}?>>Azerbaigian</option>
								<option value="BY" <?php if($country == "BY") {echo "selected";}?>>Bielorussia</option>
								<option value="CH" <?php if($country == "CH") {echo "selected";}?>>Switzerland</option>
								<option value="DK" <?php if($country == "DK") {echo "selected";}?>>Denmark</option>
								<option value="FI" <?php if($country == "FI") {echo "selected";}?>>Finland</option>
								<option value="IE" <?php if($country == "IE") {echo "selected";}?>>Ireland</option>
								<option value="LU" <?php if($country == "LU") {echo "selected";}?>>Luxembourg</option>
								<option value="LI" <?php if($country == "LI") {echo "selected";}?>>Liechtenstein</option>
								<option value="NO" <?php if($country == "NO") {echo "selected";}?>>Norvay</option>
								<option value="NL" <?php if($country == "NL") {echo "selected";}?>>Netherlands</option>
								<option value="PT" <?php if($country == "PT") {echo "selected";}?>>Portugal</option>
								<option value="PL" <?php if($country == "PL") {echo "selected";}?>>Poland</option>
								<option value="SE" <?php if($country == "SE") {echo "selected";}?>>Sweden</option>
								<option value="SK" <?php if($country == "SK") {echo "selected";}?>>Slovakia</option>
								<option value="US" <?php if($country == "US") {echo "selected";}?>>USA</option>
								<option value="BG" <?php if($country == "BG") {echo "selected";}?>>Bulgaria</option>
								<option value="BA" <?php if($country == "BA") {echo "selected";}?>>Bosnia-Erzegovina</option>
								<option value="CA" <?php if($country == "CA") {echo "selected";}?>>Canada</option>
								<option value="CY" <?php if($country == "CY") {echo "selected";}?>>Cyprus</option>
								<option value="EG" <?php if($country == "EG") {echo "selected";}?>>Egipt</option>
								<option value="EE" <?php if($country == "EE") {echo "selected";}?>>Estonia</option>
								<option value="GE" <?php if($country == "GE") {echo "selected";}?>>Georgia</option>
								<option value="GR" <?php if($country == "GR") {echo "selected";}?>>Greece</option>
								<option value="IL" <?php if($country == "IL") {echo "selected";}?>>Israel</option>
								<option value="IN" <?php if($country == "IN") {echo "selected";}?>>Indian</option>
								<option value="IS" <?php if($country == "IS") {echo "selected";}?>>Iceland</option>
								<option value="JP" <?php if($country == "JP") {echo "selected";}?>>Japan</option>
								<option value="KR" <?php if($country == "KR") {echo "selected";}?>>South Korea</option>
								<option value="LV" <?php if($country == "LV") {echo "selected";}?>>Latvia</option>
								<option value="LT" <?php if($country == "LT") {echo "selected";}?>>Lithuania</option>
								<option value="MT" <?php if($country == "MT") {echo "selected";}?>>Malt</option>
								<option value="MX" <?php if($country == "MX") {echo "selected";}?>>Mexico</option>
								<option value="MK" <?php if($country == "MK") {echo "selected";}?>>Macedonia</option>
								<option value="MD" <?php if($country == "MD") {echo "selected";}?>>Moldavia</option>
								<option value="NZ" <?php if($country == "NZ") {echo "selected";}?>>New Zealand</option>
								<option value="RO" <?php if($country == "RO") {echo "selected";}?>>Romania</option>
								<option value="CN" <?php if($country == "CN") {echo "selected";}?>>China</option>
								<option value="SM" <?php if($country == "SM") {echo "selected";}?>>San Marino</option>
								<option value="SI" <?php if($country == "SI") {echo "selected";}?>>Slovenia</option>
								<option value="TJ" <?php if($country == "TJ") {echo "selected";}?>>Tagikistan</option>
								<option value="TM" <?php if($country == "TM") {echo "selected";}?>>Turkmenistan</option>
								<option value="TR" <?php if($country == "TR") {echo "selected";}?>>Turkey</option>
								<option value="UA" <?php if($country == "UA") {echo "selected";}?>>Ukraine</option>
								<option value="UZ" <?php if($country == "UZ") {echo "selected";}?>>Uzbekistan</option>
								<option value="VE" <?php if($country == "VE") {echo "selected";}?>>Venezuela</option>
								<option value="ZA" <?php if($country == "ZA") {echo "selected";}?>>South Africa</option>
							</select>
						</div>
						<div class="form-group">
							<label for="provincia"><?php _e('State/Province', 'bfi') ?>:</label>
							<input name="form[Provincia]" value="<?php print $province; ?>" type="text" class="form-control" id="provincia">
						</div>
						<div class="form-group">
							<label for="cap"><?php _e('Zip Code', 'bfi') ?>:</label>
							<input name="form[Cap]" value="<?php print $zip; ?>" type="text" class="form-control" id="cap">
						</div>
						<div class="form-group bfi-hide">
							<label for="language"><?php _e('Language', 'bfi') ?>:</label>
							<input type="text" class="form-control" id="language" value="<?php echo $customerLanguage ?>">
						</div>
						<div class="form-group">
							<label for="phone"><?php _e('Phone', 'bfi') ?>:</label>
							<input name="form[Phone]" value="<?php print $phone; ?>" type="text" class="form-control" id="phone">
						</div>
					</div>
				</div>
				<div class="submitblock"><button type="submit" id="btnbfFormSubmit"><?php _e('Submit', 'bfi') ?></button></div>
			</div>
		</form>
	</div>
</div>
 

<div id="tabs-2" class="tabcontainer">
   <div class="reservation-list-wrapper">
      <?php if(!empty($orders)): ?>
      <?php $merchantIds = "";  ?>
   <?php foreach($orders as $order) : ?>
   <?php
	$dateCheckin = DateTime::createFromFormat($formatDate, BFCHelper::parseJsonDate($order->CheckIn));
	$dateCheckout = DateTime::createFromFormat($formatDate, BFCHelper::parseJsonDate($order->CheckOut));

	$order_resource_summary = json_decode($order->ResourcesString);
	$TimeSlotId = $order_resource_summary[0]->TimeSlotId;
	$TimeSlotStart = $order_resource_summary[0]->TimeSlotStart;
	$TimeSlotEnd = $order_resource_summary[0]->TimeSlotEnd;



	$ShowTimeSlot = !empty($TimeSlotId);

	$resource_names = array();
	$mrcName = '';
	$accomodation_total = 0;
	$merchantId = "";
	foreach($order_resource_summary as $order_resource_item) {
		if(empty($mrcName)){
			$mrcName = $order_resource_item->MrcName;;
		}
		if(empty($merchantId)){
			$merchantId = $order_resource_item->MerchantId;;
		}
         $resource_names[] = $order_resource_item->ResName;
         $accomodation_total += $order_resource_item->TotalPrice;
     }
     $resource_names = implode('<br />', $resource_names);
     $listsId[]= $merchantId;
   ?>
<div class="reservation-list">
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_ROW ?> reservation-detail">
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>10">
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 reservation-slider">
				<div id="com_bookingforconnector-search-merchant-carousel<?php echo $merchantId ."_".$order->OrderId; ?>" class="carousel">
					<div class="carousel-inner" role="listbox">
						<div class="item active"><img src="<?php echo $merchantImageUrl; ?>"></div>
					</div>
				</div>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8 reservation-content">
				<h2>
					<?php print $mrcName; ?> <span id="merchant_rating<?php echo $merchantId ."_".$order->OrderId; ?>" class="com_bookingforconnector-item-rating"></span>	  
				<h2>
				<p style="line-height:normal;"><?php print $resource_names; ?></p>
				<p class="location"><a href="javascript:void(0);" onclick="showMarker(<?php echo $merchantId?>)"><span id="address<?php echo $merchantId ."_".$order->OrderId; ?>"></span></a></p>
				<p>
					<span class="price-reserv  bfi_<?php echo $currencyclass ?>"><?php echo BFCHelper::priceFormat($accomodation_total);  ?></span> 
					<span class="reserv-number"><?php _e('Reservation number', 'bfi') ?> <?php print $order->OrderId; ?></span>
				</p>
			</div>
		</div>
		<!-- Reservation left -->
		<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>2 reserv-right">
			<div class="checkin-time">
				<p><?php _e('Check-in', 'bfi') ?></p>
				<p>
					<span class="numb"><?php print $dateCheckin->format('d'); ?></span> 
					<span class="day-date"><?php print date_i18n('D',$dateCheckin->getTimestamp()); ?><br />
					<?php if($ShowTimeSlot) {
						$startHour = new DateTime("2000-01-01 0:0:00.1"); 
						$endHour = new DateTime("2000-01-01 0:0:00.1"); 
						$startHour->add(new DateInterval('PT' . $TimeSlotStart . 'M'));
						$endHour->add(new DateInterval('PT' . $TimeSlotEnd . 'M'));
					?>
					<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?><br />
					<?php } 
					if (!empty($order_resource_summary[0]->CheckInTime) && !empty($order_resource_summary[0]->TimeDuration))
					{
						$startHour = DateTime::createFromFormat("YmdHis", $order_resource_summary[0]->CheckInTime);
						$endHour = DateTime::createFromFormat("YmdHis", $order_resource_summary[0]->CheckInTime);
						$endHour->add(new DateInterval('PT' . $order_resource_summary[0]->TimeDuration . 'M'));
					?>
						(<?php echo  $startHour->format('H:i') ?> - <?php echo  $endHour->format('H:i') ?>)<br />
					<?php 
					} 
					?>				
					<?php print date_i18n('M',$dateCheckin->getTimestamp()); ?> 
					<?php print $dateCheckin->format('Y'); ?>
					</span>
				</p>
			</div>
			<?php if(!$ShowTimeSlot && empty($order_resource_summary[0]->CheckInTime)) : ?>
			<div class="checkout-time">
				<p><?php _e('Check-out', 'bfi') ?></p>
				<p>
					<span class="numb"><?php print $dateCheckout->format('d'); ?></span> 
					<span class="day-date"><?php print date_i18n('D',$dateCheckout->getTimestamp()); ?><br />
					<?php print date_i18n('M',$dateCheckout->getTimestamp()); ?> 
					<?php print $dateCheckout->format('Y'); ?></span>
				</p>
			</div>
			<?php endif;  ?>
		</div>
	<!-- Reservation right -->
	</div>
	<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 reservation-booking">
		<a href="#" class="booking-detail" rel="<?php print $order->OrderId; ?>" id="booking-detail-<?php print $order->OrderId; ?>"><?php _e('Booking Detail', 'bfi') ?></a> 
		<?php if($dateCheckin < $now) { ?>
		<a href="#" class="cancel-booking"><?php _e('Cancel Booking', 'bfi') ?></a> 
		<?php }  ?>
		<!--            <a href="#" class="your-opi">Give your opinion on trip</a> 
		<a href="#" class="book-again">Book Again</a>
		-->        
	</div>
</div>
<!--reservation-list-->
<?php endforeach; ?>
<?php endif; ?>

		<!--pagination-->
		<?php if($orderCount > 5) { ?>
		  <div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>12 pagination_main">
			<ul class="pagination">
				<li><a href="#">«</a></li>
			<?php
			  $per_page = 5;
			  $pages = floor($orderCount / $per_page);
			  $current_page = isset($_GET['pager']) ? $_GET['pager'] : 0;
			  for($i = 0; $i < $pages + 1; $i++) {
				if($i != 0) {
				  $pagerUrl = $base_url .'/my-account/?pager='.$i .'#tabs-2';
				}
				else $pagerUrl = $base_url .'/my-account';
				if($current_page == $i) {
				  $active_class = 'active';
				}
				else $active_class = 'passive';
			?>
				<li><a class="<?php print $active_class; ?>" href="<?php print $pagerUrl; ?>"><?php print $i + 1; ?></a></li>
			<?php
			  }
			?>
				<li><a href="#">»</a></li>
			</ul>
		  <!--pagination-->   
		  </div>
		<?php } ?>
	</div>
	<div class="reservation-details-wrapper"></div>
</div>
<!-- tab 2-->


	<div id="tabs-3" class="tabcontainer">
      <!-- Password Block start-->
	   <div class="social-networks passwork-block">
		   <h3><?php _e('Password', 'bfi') ?></h3>
		   <p><?php _e("You want to change your password? Click on the button below and we'll send you an email with a link to reset it.", 'bfi') ?></p>
		   <a href="<?php echo $base_url.'/wp-admin/profile.php'; ?>" class="btn btn-primary"><i class="fa fa-circle-o" aria-hidden="true"></i> <?php _e('Change your password', 'bfi') ?></a>
	   </div>
	   
		<!-- Password  Block end -->
		
			<!-- newsletter Block start-->
		<div class="social-networks newsletter-block hidden">
			<h3>Newsletter</h3>

			<p><?php _e('Choose how often you want to receive offers and suggestions, do not miss the best offers!', 'bfi') ?></p>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>4 newsletter-booking">
				<a href="mailto:<?php echo $email ?>" class="email-newsletter"><?php echo $email ?></a>
			</div>
			<div class="<?php echo COM_BOOKINGFORCONNECTOR_BOOTSTRAP_COL ?>8 newsletter-radio">
				<div class="radio">
					<label><input type="radio" name="optradio" selected><?php _e('Receive newsletter', 'bfi') ?></label>
				</div>
				<div class="radio">
					<label><input type="radio" name="optradio"><?php _e('I do not care', 'bfi') ?></label>
				</div>
			</div>
		</div>
   
    <!-- newsletter  Block end -->
	</div>
</div>
<script type="text/javascript">

	var urlCheck = "<?php echo $base_url ?>/bfi-api/v1/task";
	var cultureCode = '<?php echo $language ?>';
	var defaultcultureCode = '<?php echo BFCHelper::$defaultFallbackCode ?>';
	var strAddress = "[indirizzo] - [cap] - [comune] ([provincia])";

	var listToCheck = "<?php echo implode(",", $listsId) ?>";
	msg1 = "<?php _e('Fetching Reservation Details', 'bfi') ?>";
	msg2 = "<?php _e('Please be patient', 'bfi') ?>";
	var img1 = new Image();

jQuery(function($){

	jQuery('.booking-detail').bind('click', function(e) {
	bookingfor.waitBlockUI(msg1, msg2,img1);
	var orderId = jQuery(this).attr('rel');
	url = urlCheck +'?task=fetchOrderDetails&orderId=' +orderId;
	jQuery.get(url, function(data) {
		jQuery.unblockUI();
		jQuery('.reservation-details-wrapper').html(data).show();
		jQuery('.reservation-list-wrapper').hide();
		jQuery('.backlist a').bind('click', function(e) {
		jQuery('.reservation-details-wrapper').html('').hide();
		jQuery('.reservation-list-wrapper').show();
		e.preventDefault();
		});
	});
	e.preventDefault();
	});
	$("#BirthDate").datepicker({
		changeMonth: true,
		changeYear: true,
		maxDate: "-18Y",
		minDate:"-125Y",
		yearRange: "-125:-18",
		dateFormat: "dd/mm/yy"
	});
	
	getMerchantsDetails();
  });

function getMerchantsDetails(){
	if (cultureCode.length>1)
	{
		cultureCode = cultureCode.substring(0, 2).toLowerCase();
	}
	if (defaultcultureCode.length>1)
	{
		defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
	}

	var query = "merchantsId=" + listToCheck + "&language=<?php echo $language ?>&task=GetMerchantsByIds";
	if(listToCheck!='')
	
	var imgPath = "<?php echo $merchantImagePath ?>";
	var imgPathError = "<?php echo $merchantImagePathError ?>";

//	jQuery.getJSON(urlCheck + "?" + query, function(data) {
	jQuery.post(urlCheck, query, function(data) {

				if(typeof callfilterloading === 'function'){
					callfilterloading();
					callfilterloading = null;
				}
			jQuery.each(data || [], function(key, val) {
				$html = '';
            if (val.ImageData!= null && val.ImageData!= '') {
					var imgSliderData = '';
					var ImageData = val.ImageData.split(',');
					var start = 0;
                jQuery.each(ImageData,function(index){
                  // new system with preresized images
					  imgLogo = imgPath.replace("[img]", jQuery.trim(ImageData[index]));

					  // old system with resized images on the fly
					  imgLogoError = imgPathError.replace("[img]", ImageData[index]);
					  if(start == 0) {
					    imgSliderData = imgSliderData + '<div class="item active"><img src="'+imgLogo+'"></div>';					  
					  }
					  else {
					    imgSliderData = imgSliderData + '<div class="item"><img src="'+imgLogo+'"></div>';
				     }
				     start++;
                });
				jQuery("div[id^='com_bookingforconnector-search-merchant-carousel"+val.MerchantId +"']").each(function( index ) {
					jQuery(this).carousel("pause").removeData();
					jQuery(this).find(".carousel-inner").first().html(imgSliderData);
					jQuery(this).carousel('pause');
				});
				}
				
				if (val.AddressData != '') {
					var $indirizzo = val.AddressData.Address;
					var $cap = val.AddressData.ZipCode;
					var $comune = val.AddressData.CityName;
					var $provincia = val.AddressData.RegionName;

					merchAddress = strAddress.replace("[indirizzo]",$indirizzo);
					merchAddress = merchAddress.replace("[cap]",$cap);
					merchAddress = merchAddress.replace("[comune]",$comune);
					merchAddress = merchAddress.replace("[provincia]",$provincia);
					jQuery("span[id^='address"+val.MerchantId +"']").each(function( index ) {
						jQuery(this).append(merchAddress);
					});

				}
				var mrcRating = Number(val.Rating );
				if (mrcRating>0 ) {
					var stars = "";
					for (var i=0; i<mrcRating;i++ )
					{
						stars += '<i class="fa fa-star"></i>';
					}
					jQuery("span[id^='merchant_rating"+val.MerchantId +"']").each(function( index ) {
						jQuery(this).append(stars);
					});

				}

		});	
		jQuery('[data-toggle="tooltip"]').tooltip({
			position : { my: 'center bottom', at: 'center top-10' },
			tooltipClass: 'bfi-tooltip bfi-tooltip-top '
		}); 
		},'json');
}



</script>
<?php get_footer(); ?>
