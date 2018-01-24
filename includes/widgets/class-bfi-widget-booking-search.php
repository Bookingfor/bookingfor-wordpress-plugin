<?php
/**
 * Product Search Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     2.0.0
 * @extends  WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'BFI_Widget_Booking_Search' ) ) {

class BFI_Widget_Booking_Search extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-widget_booking_search';
		$this->widget_description = __( 'A Search box for multimerchant, monomerchant and sell on.', 'bfi' );
		$this->widget_id          = 'bookingfor_booking_search';
		$this->widget_name        = __( 'BookingFor Search', 'bfi' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'bfi' )
			)
		);

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

//		parent::__construct();
	}


// widget form creation
function form($instance) {
	$language = $GLOBALS['bfi_lang'];
	$languageForm ='';
	if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
			global $sitepress;
			if($sitepress->get_current_language() != $sitepress->get_default_language()){
				$languageForm = "/" .ICL_LANGUAGE_CODE;
			}
	}

	// Check values
	// if( $instance) {
	// 	 $title = esc_attr($instance['title']);
	// } else {
	// 	 $title = '';
	// }
	
	
	$tablist = array();
	$tablist['0'] = __('Booking', 'bfi');
//	$tablist['1'] = __('Services', 'bfi');
	$tablist['2'] = __('Activities', 'bfi');
	$tablist['3'] = __('Real Estate', 'bfi');
	$tablistRealEstate = array('3');

	$availabilityTypeList = array();
	$availabilityTypeList['1'] = __('Nights', 'bfi');
	$availabilityTypeList['0'] = __('Days', 'bfi');
	$availabilityTypeList['2'] = __('Unit of times', 'bfi');
	$availabilityTypeList['3'] = __('Time slot', 'bfi');

	$groupByList = array();
	$groupByList['0'] = __('Resource', 'bfi');
	$groupByList['1'] = __('Merchant', 'bfi');
	$groupByList['2'] = __('Condominium', 'bfi');

	$itemTypeList = array();
	$itemTypeList['0'] = __('Resource', 'bfi');
	$itemTypeList['1'] = __('Service', 'bfi');

	$months = array();
	for($i = 1; $i <= 12; $i++){
		$dateObj = DateTime::createFromFormat('!m', $i);
		$months[$i]=date_i18n('F',$dateObj->getTimestamp());
	}
	$days = array();

	for($i = 5; $i <= 11; $i++){
		$dateObj = DateTime::createFromFormat('!d', $i);
		$days[$i-4]=date_i18n('l',$dateObj->getTimestamp());
	}

//	$allMerchantCategories = BFCHelper::getMerchantCategories();
	$allMerchantCategories = BFCHelper::getMerchantCategories($language);
	$merchantCategories = array();
	if (!empty($allMerchantCategories))
	{
		foreach($allMerchantCategories as $merchantCategory)
		{
			$merchantCategories[$merchantCategory->MerchantCategoryId] = $merchantCategory->Name;
		}
	}
	
	$allUnitCategories =  BFCHelper::GetProductCategoryForSearch($language,1);
	$unitCategories = array();
	if (!empty($allUnitCategories))
	{
		foreach($allUnitCategories as $unitCategory)
		{
			$unitCategories[$unitCategory->ProductCategoryId] = $unitCategory->Name;
		}
	}

	$allUnitCategoriesRealEstate =  BFCHelper::GetProductCategoryForSearch($language,2);
	$unitCategoriesRealEstate = array();
	if (!empty($allUnitCategoriesRealEstate))
	{
		foreach($allUnitCategoriesRealEstate as $unitCategory)
		{
			$unitCategoriesRealEstate[$unitCategory->ProductCategoryId] = $unitCategory->Name;
		}
	}

	$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
	$type = ( ! empty( $instance['type'] ) ) ? esc_attr($instance['type']) : 'multi';
	$tablistSelected = ( ! empty( $instance['tablistSelected'] ) ) ? $instance['tablistSelected'] : array();
	$blockmonths = ( ! empty( $instance['blockmonths'] ) ) ? $instance['blockmonths'] : array();
	$blockdays = ( ! empty( $instance['blockdays'] ) ) ? $instance['blockdays'] : array();
	$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '0';
	$showLocation = ( ! empty( $instance['showLocation'] ) ) ? esc_attr($instance['showLocation']) : '0';
	$showMapIcon = ( ! empty( $instance['showMapIcon'] ) ) ? esc_attr($instance['showMapIcon']) : '0';
	$showSearchText = ( ! empty( $instance['showSearchText'] ) ) ? esc_attr($instance['showSearchText']) : '0';
	$showAccomodations = ( ! empty( $instance['showAccomodations'] ) ) ? esc_attr($instance['showAccomodations']) : '0';
	$showDateRange = ( ! empty( $instance['showDateRange'] ) ) ? esc_attr($instance['showDateRange']) : '0';
	$showAdult = ( ! empty( $instance['showAdult'] ) ) ? esc_attr($instance['showAdult']) : '0';
	$showChildren = ( ! empty( $instance['showChildren'] ) ) ? esc_attr($instance['showChildren']) : '0';
	$showSenior = ( ! empty( $instance['showSenior'] ) ) ? esc_attr($instance['showSenior']) : '0';
	$showServices = ( ! empty( $instance['showServices'] ) ) ? esc_attr($instance['showServices']) : '0';
	$showOnlineBooking = ( ! empty( $instance['showOnlineBooking'] ) ) ? esc_attr($instance['showOnlineBooking']) : '0';
	$showMaxPrice = ( ! empty( $instance['showMaxPrice'] ) ) ? esc_attr($instance['showMaxPrice']) : '0';
	$showMinFloor = ( ! empty( $instance['showMinFloor'] ) ) ? esc_attr($instance['showMinFloor']) : '0';
	$showContract = ( ! empty( $instance['showContract'] ) ) ? esc_attr($instance['showContract']) : '0';

	$showSearchTextOnSell = ( ! empty( $instance['showSearchTextOnSell'] ) ) ? esc_attr($instance['showSearchTextOnSell']) : '0';
	$showMapIconOnSell = ( ! empty( $instance['showMapIconOnSell'] ) ) ? esc_attr($instance['showMapIconOnSell']) : '0';
	$showAccomodationsOnSell = ( ! empty( $instance['showAccomodationsOnSell'] ) ) ? esc_attr($instance['showAccomodationsOnSell']) : '0';


	$showBedRooms = ( ! empty( $instance['showBedRooms'] ) ) ? esc_attr($instance['showBedRooms']) : '0';
	$showRooms = ( ! empty( $instance['showRooms'] ) ) ? esc_attr($instance['showRooms']) : '0';
	$showBaths = ( ! empty( $instance['showBaths'] ) ) ? esc_attr($instance['showBaths']) : '0';
	$showOnlyNew = ( ! empty( $instance['showOnlyNew'] ) ) ? esc_attr($instance['showOnlyNew']) : '0';
	$showServicesList = ( ! empty( $instance['showServicesList'] ) ) ? esc_attr($instance['showServicesList']) : '0';

	$showNightSelector = ( ! empty( $instance['showNightSelector'] ) ) ? esc_attr($instance['showNightSelector']) : '0';
	$showDaySelector = ( ! empty( $instance['showDaySelector'] ) ) ? esc_attr($instance['showDaySelector']) : '0';
	$showServicesNightSelector = ( ! empty( $instance['showServicesNightSelector'] ) ) ? esc_attr($instance['showServicesNightSelector']) : '0';
	$showServicesDaySelector = ( ! empty( $instance['showServicesDaySelector'] ) ) ? esc_attr($instance['showServicesDaySelector']) : '0';

	
	$merchantCategoriesSelectedBooking = ( ! empty( $instance['merchantcategoriesbooking'] ) ) ? $instance['merchantcategoriesbooking'] : array();
	$merchantCategoriesSelectedActivities = ( ! empty( $instance['merchantcategoriesactivities'] ) ) ? $instance['merchantcategoriesactivities'] : array();
	$merchantCategoriesSelectedRealEstate = ( ! empty( $instance['merchantcategoriesrealestate'] ) ) ? $instance['merchantcategoriesrealestate'] : array();
	$unitCategoriesSelectedBooking = ( ! empty( $instance['unitcategoriesbooking'] ) ) ? $instance['unitcategoriesbooking'] : array();
	$unitCategoriesSelectedActivities = ( ! empty( $instance['unitcategoriesactivities'] ) ) ? $instance['unitcategoriesactivities'] : array();
	$unitCategoriesSelectedRealEstate = ( ! empty( $instance['unitcategoriesrealestate'] ) ) ? $instance['unitcategoriesrealestate'] : array();

	$availabilityTypesSelectedBooking = ( ! empty( $instance['availabilitytypesbooking'] ) ) ? $instance['availabilitytypesbooking'] : array(1);
	$availabilityTypesSelectedAvailability = ( ! empty( $instance['availabilitytypesactivities'] ) ) ? $instance['availabilitytypesactivities'] : array(2,3);

	$itemTypesSelectedBooking = ( ! empty( $instance['itemtypesbooking'] ) ) ? $instance['itemtypesbooking'] : array(0);
	$itemTypesSelectedActivities = ( ! empty( $instance['itemtypesactivities'] ) ) ? $instance['itemtypesactivities'] : array(1);
	
	$groupBySelectedBooking = ( ! empty( $instance['groupbybooking'] ) ) ? $instance['groupbybooking'] : array();
	$groupBySelectedActivities = ( ! empty( $instance['groupbyactivities'] ) ) ? $instance['groupbyactivities'] : array();

	?>
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo ($instance)?esc_attr($instance['title']):''; ?>" />
	</p>
	<p class="bookingoptions">
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showdirection'); ?>" value="1" <?php  echo ($showdirection=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Displays horizontally', 'bfi'); ?></label>
	</p>
	<p>
		<span class="bfitabselect"><?php _e('Tab', 'bfi'); ?></span><br />
		<?php  foreach ($tablist as $key => $value) { ?>
			<label class="checkbox bfitabsearch <?php  echo (in_array($key, $tablistRealEstate)) ? 'bfickbrealestate"' : 'bfickbbooking'; ?>"><input type="checkbox" name="<?php echo $this->get_field_name('tablistSelected'); ?>[]" value="<?php echo $key ?>" <?php  echo (in_array($key, $tablistSelected)) ? 'checked="checked"' : ''; ?> /><?php echo $value ?></label><br />
		<?php } ?>
	</p>
	<p class="bookingoptions">
		<label for="<?php echo $this->get_field_id('blockmonths'); ?>"><?php _e('Block Months', 'bfi'); ?>
		<?php 
			printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('blockmonths'),
	            $this->get_field_id('blockmonths')
            );
			for($i = 1; $i <= 12; $i++){
				printf(
					'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
					$i,
					in_array( $i, $blockmonths) ? 'selected="selected"' : '',
					$months[$i]
				);
			}
            echo '</select>';
            ?>
		</label>
	</p>
	<p class="bookingoptions">
		<label for="<?php echo $this->get_field_id('blockdays'); ?>"><?php _e('Disable Days', 'bfi'); ?>
		<?php 
			printf(
				'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
	            $this->get_field_name('blockdays'),
	            $this->get_field_id('blockdays')
            );
			for($i = 1; $i <= 7; $i++){
				printf(
					'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
					$i,
					in_array( $i, $blockdays) ? 'selected="selected"' : '',
					$days[$i]
				);
			}
            echo '</select>';
            ?>
		</label>
	</p>
	<?php if(!empty($merchantCategories) || !empty($unitCategories) || !empty($unitCategoriesRealEstate)){  ?>
	<p class="bfitabsearch0 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[0] ?></span><br />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategoriesbooking'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesbooking'),
					$this->get_field_id('merchantcategoriesbooking')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategories)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategoriesbooking'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesbooking'),
					$this->get_field_id('unitcategoriesbooking')
				);
				foreach ($unitCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label for="<?php echo $this->get_field_id('availabilitytypesbooking'); ?>"><?php _e('Search availability for', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('availabilitytypesbooking'),
					$this->get_field_id('availabilitytypesbooking')
				);
				foreach ($availabilityTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $availabilityTypesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('itemtypesbooking'); ?>"><?php _e('Items type', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('itemtypesbooking'),
					$this->get_field_id('itemtypesbooking')
				);
				foreach ($itemTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $itemTypesSelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('groupbybooking'); ?>"><?php _e('Default group by ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('groupbybooking'),
					$this->get_field_id('groupbybooking')
				);
				foreach ($groupByList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $groupBySelectedBooking) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
	</p>
	<p class="bfitabsearch2 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[2] ?></span><br />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategoriesactivities'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesactivities'),
					$this->get_field_id('merchantcategoriesactivities')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategories)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategoriesactivities'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesactivities'),
					$this->get_field_id('unitcategoriesactivities')
				);
				foreach ($unitCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label for="<?php echo $this->get_field_id('availabilitytypesactivities'); ?>"><?php _e('Search availability for', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('availabilitytypesactivities'),
					$this->get_field_id('availabilitytypesactivities')
				);
				foreach ($availabilityTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $availabilityTypesSelectedAvailability) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('itemtypesactivities'); ?>"><?php _e('Items type', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('itemtypesactivities'),
					$this->get_field_id('itemtypesactivities')
				);
				foreach ($itemTypeList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $itemTypesSelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
		<label for="<?php echo $this->get_field_id('groupbyactivities'); ?>"><?php _e('Default group by ', 'bfi'); ?>
			<?php 
				printf(
					'<select name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('groupbyactivities'),
					$this->get_field_id('groupbyactivities')
				);
				foreach ($groupByList as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $groupBySelectedActivities) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
		</label>
	</p>
	<p class="bookingoptions">
		<span><?php _e('Fields Visibility');?></span><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSearchText'); ?>" value="1" <?php  echo ($showSearchText=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Search text', 'bfi'); ?> <?php _e('(Merchants, Products, Tags, Merchants and Products Categories, Regions, States, Cities, Zones)', 'bfi') ?></label><br />
		<?php _e('or');?><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showLocation'); ?>" value="1" <?php  echo ($showLocation=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Destination', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAccomodations'); ?>" value="1" <?php  echo ($showAccomodations=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Type', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMapIcon'); ?>" value="1" <?php  echo ($showMapIcon=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Map Button', 'bfi'); ?></label><br />
		<br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showDateRange'); ?>" value="1" <?php  echo ($showDateRange=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Date Range', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAdult'); ?>" value="1" <?php  echo ($showAdult=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Adults', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showChildren'); ?>" value="1" <?php  echo ($showChildren=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Childrens', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSenior'); ?>" value="1" <?php  echo ($showSenior=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Senior', 'bfi'); ?></label><br />
		<!-- <label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showServices'); ?>" value="1" <?php  echo ($showServices=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Services', 'bfi'); ?></label><br /> -->
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showOnlineBooking'); ?>" value="1" <?php  echo ($showOnlineBooking=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Only Online Booking', 'bfi'); ?></label>
		<br /><br />
	</p>
	<p class="bfitabsearch3 widget-inside" >
		<span class="bfi-titletab"><?php echo $tablist[3] ?></span><br />
		<?php if(!empty($merchantCategories)){  ?>
			<label for="<?php echo $this->get_field_id('merchantcategories'); ?>"><?php _e('Merchant category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('merchantcategoriesrealestate'),
					$this->get_field_id('merchantcategoriesrealestate')
				);
				foreach ($merchantCategories as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $merchantCategoriesSelectedRealEstate) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<?php if(!empty($unitCategoriesRealEstate)){  ?>
			<label for="<?php echo $this->get_field_id('unitcategories'); ?>"><?php _e('Product category', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('unitcategoriesrealestate'),
					$this->get_field_id('unitcategoriesrealestate')
				);
				foreach ($unitCategoriesRealEstate as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $unitCategoriesSelectedRealEstate) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
		<?php }  ?>
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showSearchTextOnSell'); ?>" value="1" <?php  echo ($showSearchTextOnSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Search text', 'bfi'); ?> <?php _e('(Regions, States, Cities, Zones)', 'bfi') ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMapIconOnSell'); ?>" value="1" <?php  echo ($showMapIconOnSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Map Button', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showContract'); ?>" value="1" <?php  echo ($showContract=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Contract', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showAccomodationsOnSell'); ?>" value="1" <?php  echo ($showAccomodationsOnSell=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Type', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMaxPrice'); ?>" value="1" <?php  echo ($showMaxPrice=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Price', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showMinFloor'); ?>" value="1" <?php  echo ($showMinFloor=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Floor Area', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showBedRooms'); ?>" value="1" <?php  echo ($showBedRooms=='1') ? 'checked="checked"' : ''; ?> /><?php _e('BedRooms', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showRooms'); ?>" value="1" <?php  echo ($showRooms=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Rooms', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showBaths'); ?>" value="1" <?php  echo ($showBaths=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Baths', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showOnlyNew'); ?>" value="1" <?php  echo ($showOnlyNew=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Only New', 'bfi'); ?></label><br />
		<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showServicesList'); ?>" value="1" <?php  echo ($showServicesList=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Services list', 'bfi'); ?></label><br />

	</p>
	<?php }  ?>




	<p class="realestateoptions">
	</p>

	<?php
	}

	
	// update widget
	function update($new_instance, $old_instance) {

		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);
		  		  
		  $instance['tablistSelected'] =  ! empty( $new_instance[ 'tablistSelected' ] ) ? esc_sql( $new_instance['tablistSelected'] ) : "";
		  $instance['blockmonths'] = ! empty( $new_instance[ 'blockmonths' ] ) ? esc_sql( $new_instance['blockmonths'] ) : "";
		  $instance['blockdays'] = ! empty( $new_instance[ 'blockdays' ] ) ? esc_sql( $new_instance['blockdays'] ) : "";
		  
		  $instance['merchantcategoriesbooking'] = ! empty( $new_instance[ 'merchantcategoriesbooking' ] ) ? esc_sql( $new_instance['merchantcategoriesbooking'] ) : "";
		  $instance['merchantcategoriesservices'] = ! empty( $new_instance[ 'merchantcategoriesservices' ] ) ? esc_sql( $new_instance['merchantcategoriesservices'] ) : "";
		  $instance['merchantcategoriesactivities'] = ! empty( $new_instance[ 'merchantcategoriesactivities' ] ) ? esc_sql( $new_instance['merchantcategoriesactivities'] ) : "";
		  $instance['merchantcategoriesrealestate'] = ! empty( $new_instance[ 'merchantcategoriesrealestate' ] ) ? esc_sql( $new_instance['merchantcategoriesrealestate'] ) : "";
		  
		  $instance['unitcategoriesbooking'] = ! empty( $new_instance[ 'unitcategoriesbooking' ] ) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesbooking'] ) : "";
		  $instance['unitcategoriesservices'] = ! empty( $new_instance[ 'unitcategoriesservices' ] ) &&  in_array(1,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesservices'] ) : "";
		  $instance['unitcategoriesactivities'] = ! empty( $new_instance[ 'unitcategoriesactivities' ] ) &&  in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesactivities'] ) : "";
		  $instance['unitcategoriesrealestate'] = ! empty( $new_instance[ 'unitcategoriesrealestate' ] ) &&  in_array(3,$instance['tablistSelected']) ? esc_sql( $new_instance['unitcategoriesrealestate'] ) : "";

		  $instance['availabilitytypesbooking'] = ! empty( $new_instance[ 'availabilitytypesbooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['availabilitytypesbooking'] ) : "";
		  $instance['availabilitytypesactivities'] = ! empty( $new_instance[ 'availabilitytypesactivities' ] ) && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['availabilitytypesactivities'] ) : "";

		  $instance['itemtypesbooking'] = ! empty( $new_instance[ 'itemtypesbooking' ] )  && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['itemtypesbooking'] ) : "";
		  $instance['itemtypesactivities'] = ! empty( $new_instance[ 'itemtypesactivities' ] )  && is_array($instance['tablistSelected']) &&  in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['itemtypesactivities'] ) : "";

		  $instance['groupbybooking'] = ! empty( $new_instance[ 'groupbybooking' ] ) && is_array($instance['tablistSelected']) &&  in_array(0,$instance['tablistSelected']) ? esc_sql( $new_instance['groupbybooking'] ) : "";
		  $instance['groupbyactivities'] = ! empty( $new_instance[ 'groupbyactivities' ] ) && is_array($instance['tablistSelected']) &&   in_array(2,$instance['tablistSelected']) ? esc_sql( $new_instance['groupbyactivities'] ) : "";

		  $instance['showdirection'] =! empty( $new_instance[ 'showdirection' ] ) ? 1 : 0;
		  $instance['showLocation'] = ! empty( $new_instance[ 'showLocation' ] ) ? 1 : 0;
		  $instance['showMapIcon'] = ! empty( $new_instance[ 'showMapIcon' ] ) ? 1 : 0;
		  $instance['showSearchText'] = ! empty( $new_instance[ 'showSearchText' ] ) ? 1 : 0;
		  $instance['showAccomodations'] = ! empty( $new_instance[ 'showAccomodations' ] ) ? 1 : 0;
		  $instance['showDateRange'] = ! empty( $new_instance[ 'showDateRange' ] ) ? 1 : 0;
		  $instance['showAdult'] = ! empty( $new_instance[ 'showAdult' ] ) ? 1 : 0;
		  $instance['showChildren'] = ! empty( $new_instance[ 'showChildren' ] ) ? 1 : 0;
		  $instance['showSenior'] = ! empty( $new_instance[ 'showSenior' ] ) ? 1 : 0;
		  $instance['showServices'] = ! empty( $new_instance[ 'showServices' ] ) ? 1 : 0;
		  $instance['showOnlineBooking'] = ! empty( $new_instance[ 'showOnlineBooking' ] ) ? 1 : 0;
		  $instance['showMaxPrice'] = ! empty( $new_instance[ 'showMaxPrice' ] ) ? 1 : 0;
		  $instance['showMinFloor'] = ! empty( $new_instance[ 'showMinFloor' ] ) ? 1 : 0;
		  $instance['showContract'] = ! empty( $new_instance[ 'showContract' ] ) ? 1 : 0;
		  

		  $instance['showSearchTextOnSell'] = ! empty( $new_instance[ 'showSearchTextOnSell' ] ) ? 1 : 0;
		  $instance['showMapIconOnSell'] = ! empty( $new_instance[ 'showMapIconOnSell' ] ) ? 1 : 0;
		  $instance['showAccomodationsOnSell'] = ! empty( $new_instance[ 'showAccomodationsOnSell' ] ) ? 1 : 0;
		  $instance['showBedRooms'] = ! empty( $new_instance[ 'showBedRooms' ] ) ? 1 : 0;
		  $instance['showRooms'] = ! empty( $new_instance[ 'showRooms' ] ) ? 1 : 0;
		  $instance['showBaths'] = ! empty( $new_instance[ 'showBaths' ] ) ? 1 : 0;
		  $instance['showOnlyNew'] = ! empty( $new_instance[ 'showOnlyNew' ] ) ? 1 : 0;
		  $instance['showServicesList'] = ! empty( $new_instance[ 'showServicesList' ] ) ? 1 : 0;

		 return $instance;
	}
	
	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$args["title"] =  $title;
		$args["instance"] =  $instance;
		bfi_get_template("widgets/booking-search.php",$args);	
//		include(BFI()->plugin_path() .'/templates/widgets/booking-search.php');

//		$this->widget_end( $args );
	}
}
}