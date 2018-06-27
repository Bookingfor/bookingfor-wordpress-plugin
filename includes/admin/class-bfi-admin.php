<?php
/**
 * BookingFor Admin
 *
 * @class    BFI_Admin
 * @author   Bookingfor
 * @category Admin
 * @package  Bookingfor/Admin
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BFI_Admin' ) ) :
/**
 * BFI_Admin class.
 */
class BFI_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action("admin_menu", array( $this, 'admin_menu' ), 9 );
//		add_action("admin_menu", array( $this, 'add_bfi_merchantlist_menu_item' ), 10);
		add_action("admin_init", array( $this, 'display_bfi_fields' ));
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
//		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
	}
	public function admin_menu() {
		$icon = BFI()->plugin_url() . '/assets/images/logo_16.png';

		add_menu_page("BookingFor", "BookingFor", "manage_options", "bfi-settings", null, $icon, '99.3');
		add_submenu_page('bfi-settings',"BookingFor Settings", "Settings", "manage_options", "bfi-settings", array( $this, 'bfi_settings_page' ));
	}


	public function admin_styles() {
		global $wp_scripts;

		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// Register admin styles
		wp_register_style( 'bookingfor_admin_menu_styles', BFI()->plugin_url() . '/assets/css/menu.css', array(), BFI_VERSION );
//		wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

		// Sitewide menu CSS
		wp_enqueue_style( 'bookingfor_admin_menu_styles' );

//		// Admin styles for BFI pages only
//		if ( in_array( $screen_id, BFI_get_screen_ids() ) ) {
//			wp_enqueue_style( 'bookingfor_admin_styles' );
//			wp_enqueue_style( 'jquery-ui-style' );
//			wp_enqueue_style( 'wp-color-picker' );
//		}
	}

//	public function add_bfi_menu_item()
//	{
//	}
//
//	public function add_bfi_merchantlist_menu_item()
//	{
//	}
	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

//		// Setup/welcome
//		if ( ! empty( $_GET['page'] ) ) {
//			switch ( $_GET['page'] ) {
//				case 'bfi-setup' :
//					include_once( 'class-bfi-admin-setup-wizard.php' );
//				break;
//			}
//		}

	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
//			case 'dashboard' :
//				include( 'class-bfi-admin-dashboard.php' );
//			break;
			case 'options-permalink' :
				include( 'class-bfi-admin-permalink-settings.php' );
			break;
//			case 'users' :
//			case 'user' :
//			case 'profile' :
//			case 'user-edit' :
//				include( 'class-bfi-admin-profile.php' );
//			break;
		}
	}

//	/**
//	 * Handle redirects to setup/welcome page after install and updates.
//	 *
//	 * For setup wizard, transient must be present, the user must have access rights, and we must ignore the network/bulk plugin updaters.
//	 */
//	public function admin_redirects() {
//		// Nonced plugin install redirects (whitelisted)
//		if ( ! empty( $_GET['bfi-install-plugin-redirect'] ) ) {
//			$plugin_slug = bfi_clean( $_GET['bfi-install-plugin-redirect'] );
//
//			if ( current_user_can( 'install_plugins' ) && in_array( $plugin_slug, array( 'bookingfor-gateway-stripe' ) ) ) {
//				$nonce = wp_create_nonce( 'install-plugin_' . $plugin_slug );
//				$url   = self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug . '&_wpnonce=' . $nonce );
//			} else {
//				$url = admin_url( 'plugin-install.php?tab=search&type=term&s=' . $plugin_slug );
//			}
//
//			wp_safe_redirect( $url );
//			exit;
//		}
//
//		// Setup wizard redirect
//		if ( get_transient( '_bfi_activation_redirect' ) ) {
//			delete_transient( '_bfi_activation_redirect' );
//
//			if ( ( ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'bfi-setup' ) ) ) || is_network_admin() || isset( $_GET['activate-multi'] ) || ! current_user_can( 'manage_bookingfor' ) || apply_filters( 'bookingfor_prevent_automatic_wizard_redirect', false ) ) {
//				return;
//			}
//
//			// If the user needs to install, send them to the setup wizard
//			if ( BFI_Admin_Notices::has_notice( 'install' ) ) {
//				wp_safe_redirect( admin_url( 'index.php?page=bfi-setup' ) );
//				exit;
//			}
//		}
//	}

//	/**
//	 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin.
//	 */
//	public function prevent_admin_access() {
//		$prevent_access = false;
//
//		if ( 'yes' === get_option( 'bookingfor_lock_down_admin', 'yes' ) && ! is_ajax() && basename( $_SERVER["SCRIPT_FILENAME"] ) !== 'admin-post.php' && ! current_user_can( 'edit_posts' ) && ! current_user_can( 'manage_bookingfor' ) ) {
//			$prevent_access = true;
//		}
//
//		$prevent_access = apply_filters( 'bookingfor_prevent_admin_access', $prevent_access );
//
//		if ( $prevent_access ) {
//			wp_safe_redirect( bfi_get_page_permalink( 'myaccount' ) );
//			exit;
//		}
//	}

	public function bfi_settings_page(){
		$valid_nonce = isset($_REQUEST['_wpnonce']) ? wp_verify_nonce($_REQUEST['_wpnonce'], 'bfi-cache') : false;
		if ( $valid_nonce ) {
			if(isset($_REQUEST['bfi_delete_cache'])) {
				self::bfi_delete_files_cache();
			}
		}
		include('views/html-admin-settings.php');
	}
	public function is_dir_empty($dir) {
		if (!is_readable($dir)) return NULL; 
		return (count(scandir($dir)) == 2);
	}
	function bfi_delete_files_cache() {
	
		if (!file_exists (COM_BOOKINGFORCONNECTOR_CACHEDIR)) {
			return false;
		}

		$dir = trailingslashit( COM_BOOKINGFORCONNECTOR_CACHEDIR );

		if ( is_dir( $dir ) && $dh = @opendir( $dir ) ) {
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if ( $file != '.' && $file != '..' && $file != '.htaccess' && is_file( $dir . $file ) )
					@unlink( $dir . $file );
			}
			closedir( $dh );
			@rmdir( $dir );
?>
<div class="updated notice">
    <p><?php _e('Cache are cleaned', 'bfi') ?></p>
</div>
<?php 
		}
		return true;

	}
	
	public function display_bfi_subscription_key_element()
	{
		?>
			<input type="text" name="bfi_subscription_key" id="bfi_subscription_key" value="<?php echo get_option('bfi_subscription_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_itemperpage_key_element()
	{
		?>
			<input name="bfi_itemperpage_key" id="bfi_itemperpage_key" value="<?php echo get_option('bfi_itemperpage_key',10); ?>"  style="line-height:normal;" 
			type="number" style="width:50px;" class="" placeholder="" min="5" man="20" step="1" />
		<?php
	}

	public function display_bfi_maxqtSelectable_key_element(){
		?>
			<input type="text" name="bfi_maxqtselectable_key" id="bfi_maxqtselectable_key" value="<?php echo get_option('bfi_maxqtselectable_key',20); ?>"  style="line-height:normal;" 
				type="number" style="width:50px;" class="" placeholder="" min="0" man="50" step="1" />
	<?php
	}

	public function display_bfi_api_key_element()
	{
		?>
			<textarea type="text" name="bfi_api_key" id="bfi_api_key" rows="6" cols="50" style="line-height:normal;"><?php echo get_option('bfi_api_key'); ?></textarea>
		<?php
	}

	public function display_bfi_form_key_element()
	{
		?>
			<input type="text" name="bfi_form_key" id="bfi_form_key" value="<?php echo get_option('bfi_form_key',site_url()); ?>"  style="line-height:normal;" />
		<?php
	}

	public function display_bfi_ssllogo_key_element()
	{
		?>
			<textarea type="text" name="bfi_ssllogo_key" id="bfi_ssllogo_key" rows="6" cols="50" style="line-height:normal;"><?php echo get_option('bfi_ssllogo_key'); ?></textarea>
			<br />(html code)
		<?php
	}

	public function display_bfi_useproxy_key_element()
	{
		?>
			<input type="checkbox" id="bfi_useproxy_key" name="bfi_useproxy_key" value="1" <?php checked(get_option('bfi_useproxy_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_usessl_key_element()
	{
		?>
			<input type="checkbox" id="bfi_usessl_key" name="bfi_usessl_key" value="1" <?php checked(get_option('bfi_usessl_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_isportal_key_element()
	{
		?>
			<input type="checkbox" id="bfi_isportal_key" name="bfi_isportal_key" value="1" <?php checked(get_option('bfi_isportal_key',1), 1, true ); ?> />
		<?php
	}
	
	public function display_bfi_showdata_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showdata_key" name="bfi_showdata_key" value="1" <?php checked(get_option('bfi_showdata_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_sendtocart_key_element()
	{
		?>
			<input type="checkbox" id="bfi_sendtocart_key" name="bfi_sendtocart_key" value="1" <?php checked(get_option('bfi_sendtocart_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_showbadge_key_element()
	{
		?>
			<input type="checkbox" id="bfi_showbadge_key" name="bfi_showbadge_key" value="1" <?php checked(get_option('bfi_showbadge_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_enablecoupon_key_element()
	{
		?>
			<input type="checkbox" id="bfi_enablecoupon_key" name="bfi_enablecoupon_key" value="1" <?php checked(get_option('bfi_enablecoupon_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_urlproxy_key_element()
	{
		?>
			<input type="text" name="bfi_urlproxy_key" id="bfi_urlproxy_key" value="<?php echo get_option('bfi_urlproxy_key','127.0.0.1:8888'); ?>"  style="line-height:normal;" />
		<?php
	}
	


	public function display_bfi_posx_key_element()
	{
		?>
			<input type="text" name="bfi_posx_key" id="bfi_posx_key" value="<?php echo get_option('bfi_posx_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_posy_key_element()
	{
		?>
			<input type="text" name="bfi_posy_key" id="bfi_posy_key" value="<?php echo get_option('bfi_posy_key'); ?>"  style="line-height:normal;" />
		<?php
	}
	public function display_bfi_startzoom_key_element()
	{
		?>
		<select id="bfi_startzoom_key" name="bfi_startzoom_key">
				<?php
				foreach (range(10, 17) as $number) {
					?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_startzoom_key',15), $number ); ?>><?php echo $number ?></option><?php
				}
				?>
		</select>
		<?php
	}

	public function display_bfi_googlemapskey_key_element()
	{
		?>
			<input type="text" name="bfi_googlemapskey_key" id="bfi_googlemapskey_key" value="<?php echo get_option('bfi_googlemapskey_key'); ?>"  style="line-height:normal;" /> <br />
			Get a key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" >https://developers.google.com/maps/documentation/javascript/get-api-key</a> 
			<br />Enable 'Google Static Maps API' and 'Google Maps JavaScript API'
		<?php
	}

	public function display_bfi_googlerecaptcha_key_element()
	{
		?>
			<input type="text" name="bfi_googlerecaptcha_key" id="bfi_googlerecaptcha_key" value="<?php echo get_option('bfi_googlerecaptcha_key'); ?>"  style="line-height:normal;" /> <a href="https://www.google.com/recaptcha/admin" target="_blank" >Get reCAPTCHA</a> 
		<?php
	}
	public function display_bfi_googlerecaptcha_secret_key_element()
	{
		?>
			<input type="text" name="bfi_googlerecaptcha_secret_key" id="bfi_googlerecaptcha_secret_key" value="<?php echo get_option('bfi_googlerecaptcha_secret_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}
	
	

	public function display_bfi_defaultdisplaylist_key_element(){
		?>
		<select id="bfi_defaultdisplaylist_key" name="bfi_defaultdisplaylist_key">
			<option value="0" <?php echo get_option('bfi_defaultdisplaylist_key',0) == 0 ? "selected" : "" ?>>List</option>
			<option value="1" <?php echo get_option('bfi_defaultdisplaylist_key',1) == 1 ? "selected" : "" ?>>Grid</option>
		</select>
	<?php
	}

	public function display_bfi_googlerecaptcha_theme_key_element(){
		?>
		<select id="bfi_googlerecaptcha_theme_key" name="bfi_googlerecaptcha_theme_key">
			<option value="light" <?php echo get_option('bfi_googlerecaptcha_theme_key','light') == 'light' ? "selected" : "" ?>>light</option>
			<option value="dark" <?php echo get_option('bfi_googlerecaptcha_theme_key','light') == 'dark' ? "selected" : "" ?>>dark</option>
		</select>
	<?php
	}
	
	
	public function display_bfi_gaenabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_gaenabled_key" name="bfi_gaenabled_key" value="1" <?php checked(get_option('bfi_gaenabled_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_gaaccount_key_element()
	{
		?>
			<input type="text" name="bfi_gaaccount_key" id="bfi_gaaccount_key" value="<?php echo get_option('bfi_gaaccount_key'); ?>"  style="line-height:normal;" /> 
		<?php
	}

	public function display_bfi_criteoenabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_criteoenabled_key" name="bfi_criteoenabled_key" value="1" <?php checked(get_option('bfi_criteoenabled_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_eecenabled_key_element()
	{
		?>
			<input type="checkbox" id="bfi_eecenabled_key" name="bfi_eecenabled_key" value="1" <?php checked(get_option('bfi_eecenabled_key',0), 1, true ); ?> />
		<?php
	}

	public function display_bfi_enablecache_key_element()
	{
		?>
			<input type="checkbox" id="bfi_enablecache_key" name="bfi_enablecache_key" value="1" <?php checked(get_option('bfi_enablecache_key',1), 1, true ); ?> />
		<?php
	}

	public function display_bfi_adultsage_key_element(){
		?>
		<select id="bfi_adultsage_key" name="bfi_adultsage_key">
			<?php
			foreach (range(0, 120) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_adultsage_key',18), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_adultsqt_key_element(){
		?>
		<select id="bfi_adultsqt_key" name="bfi_adultsqt_key">
			<?php
			foreach (range(0, 12) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_adultsqt_key',2), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_childrensage_key_element(){
		?>
		<select id="bfi_childrensage_key" name="bfi_childrensage_key">
			<?php
			foreach (range(0, 25) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_childrensage_key',12), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_senioresage_key_element(){
		?>
		<select id="bfi_senioresage_key" name="bfi_senioresage_key">
			<?php
			foreach (range(40, 120) as $number) {
				?> <option value="<?php echo $number ?>" <?php selected( get_option('bfi_senioresage_key',65), $number ); ?>><?php echo $number ?></option><?php
			}
			?>
		</select>
	<?php
	}

	public function display_bfi_currentcurrency_key_element(){
		$defaultCurrency = bfi_get_defaultCurrency();
		$currencyExchanges = bfi_get_currencyExchanges();
		$currency_text = array('978' => __('Euro', 'bfi'),
						'191' => __('Kune', 'bfi'),
						'840' => __('U.S. dollar', 'bfi'),   
						'392' => __('Japanese yen', 'bfi'),
						'124' => __('Canadian dollar', 'bfi'),
						'36' => __('Australian dollar', 'bfi'),
						'643' => __('Russian Ruble', 'bfi'),  
						'200' => __('Czech koruna', 'bfi'),
						'702' => __('Singapore dollar', 'bfi'),  
						'826' => __('Pound sterling ', 'bfi')                            
					);
		?>
		<select id="bfi_currentcurrency_key" name="bfi_currentcurrency_key">
		<?php 
		foreach ($currencyExchanges as $currencyExchangeCode => $currencyExchange ) {
		?>
			<option value="<?php echo $currencyExchangeCode ?>" <?php echo get_option('bfi_currentcurrency_key',$defaultCurrency) == $currencyExchangeCode ? "selected" : "" ?>><?php echo $currency_text[$currencyExchangeCode] ?></option>
		<?php 
		}
		?>
		</select>
		<?php

		
	}


	public function display_bfi_fields()
	{
		add_settings_section("section", "All Settings", null, "bfi-options");
		
		add_settings_field("bfi_subscription_key", "Subscription Key *",  array( $this, 'display_bfi_subscription_key_element'), "bfi-options", "section");
		add_settings_field("bfi_api_key", "API Key *",  array( $this, 'display_bfi_api_key_element'), "bfi-options", "section");
		add_settings_field("bfi_form_key", "Referrer *",  array( $this, 'display_bfi_form_key_element'), "bfi-options", "section");		
		
		add_settings_field("bfi_currentcurrency_key", "Default currency",  array( $this, 'display_bfi_currentcurrency_key_element'), "bfi-options", "section");
		add_settings_field("bfi_usessl_key", "Use SSL",  array( $this, 'display_bfi_usessl_key_element'), "bfi-options", "section");
		add_settings_field("bfi_ssllogo_key", "Certificate logo",  array( $this, 'display_bfi_ssllogo_key_element'), "bfi-options", "section");
		add_settings_field("bfi_itemperpage_key", "Item per page",  array( $this, 'display_bfi_itemperpage_key_element'), "bfi-options", "section");
		add_settings_field("bfi_maxqtselectable_key", "Max selectable item",  array( $this, 'display_bfi_maxqtSelectable_key_element'), "bfi-options", "section");
		add_settings_field("bfi_defaultdisplaylist_key", "Default list view",  array( $this, 'display_bfi_defaultdisplaylist_key_element'), "bfi-options", "section");


		add_settings_field("bfi_isportal_key", "Multimerchant", array( $this, 'display_bfi_isportal_key_element'), "bfi-options", "section");
		add_settings_field("bfi_showdata_key", "Show Descriptions on lists", array( $this, 'display_bfi_showdata_key_element'), "bfi-options", "section");
		add_settings_field("bfi_sendtocart_key", "Send guest directly to cart", array( $this, 'display_bfi_sendtocart_key_element'), "bfi-options", "section");
//		add_settings_field("bfi_showbadge_key", "Show badge number items on cart", array( $this, 'display_bfi_showbadge_key_element'), "bfi-options", "section");
		add_settings_field("bfi_enablecoupon_key", "Enable coupon feature", array( $this, 'display_bfi_enablecoupon_key_element'), "bfi-options", "section");


		add_settings_section("sectionmaps", "Maps Settings", null, "bfi-options");
		add_settings_field("bfi_posx_key", "Longitude *",  array( $this, 'display_bfi_posx_key_element'), "bfi-options", "sectionmaps");
		add_settings_field("bfi_posy_key", "Latitude *",  array( $this, 'display_bfi_posy_key_element'), "bfi-options", "sectionmaps");
		add_settings_field("bfi_startzoom_key", "Start Zoom",  array( $this, 'display_bfi_startzoom_key_element'), "bfi-options", "sectionmaps");
		add_settings_field("bfi_googlemapskey_key", "Google maps key *",  array( $this, 'display_bfi_googlemapskey_key_element'), "bfi-options", "sectionmaps");
		
		add_settings_section("sectionrecaptcha", "Google recaptcha Settings", null, "bfi-options");
		add_settings_field("bfi_googlerecaptcha_key", "Site key",  array( $this, 'display_bfi_googlerecaptcha_key_element'), "bfi-options", "sectionrecaptcha");
		add_settings_field("bfi_googlerecaptcha_secret_key", "Secret key",  array( $this, 'display_bfi_googlerecaptcha_secret_key_element'), "bfi-options", "sectionrecaptcha");
		add_settings_field("bfi_googlerecaptcha_theme_key", "Theme",  array( $this, 'display_bfi_googlerecaptcha_theme_key_element'), "bfi-options", "sectionrecaptcha");


		add_settings_section("sectionperson", "Person Settings", null, "bfi-options");
		add_settings_field("bfi_adultsage_key", "Min adult's age",  array( $this, 'display_bfi_adultsage_key_element'), "bfi-options", "sectionperson");
		add_settings_field("bfi_adultsqt_key", "Preset adults in search",  array( $this, 'display_bfi_adultsqt_key_element'), "bfi-options", "sectionperson");
		add_settings_field("bfi_childrensage_key", "Preset children's age in search",  array( $this, 'display_bfi_childrensage_key_element'), "bfi-options", "sectionperson");
		add_settings_field("bfi_senioresage_key", "Min seniores's age",  array( $this, 'display_bfi_senioresage_key_element'), "bfi-options", "sectionperson");
		
		add_settings_section("sectionproxy", "Proxy Settings", null, "bfi-options");
		add_settings_field("bfi_useproxy_key", "Use Proxy",  array( $this, 'display_bfi_useproxy_key_element'), "bfi-options", "sectionproxy");
		add_settings_field("bfi_urlproxy_key", "Url's Proxy",  array( $this, 'display_bfi_urlproxy_key_element'), "bfi-options", "sectionproxy");

		add_settings_section("sectionanalyitics", "Analyitics Settings", null, "bfi-options");
		add_settings_field("bfi_gaenabled_key", "Enabled",  array( $this, 'display_bfi_gaenabled_key_element'), "bfi-options", "sectionanalyitics");
		add_settings_field("bfi_gaaccount_key", "Analytics account ID",  array( $this, 'display_bfi_gaaccount_key_element'), "bfi-options", "sectionanalyitics");
		add_settings_field("bfi_eecenabled_key", "Enable Enhanced Ecommerce",  array( $this, 'display_bfi_eecenabled_key_element'), "bfi-options", "sectionanalyitics");
		add_settings_field("bfi_criteoenabled_key", "Enable Criteo",  array( $this, 'display_bfi_criteoenabled_key_element'), "bfi-options", "sectionanalyitics");
		
		add_settings_section("sectionperformance", "Performance Settings", null, "bfi-options");
		add_settings_field("bfi_enablecache_key", "Use Cache",  array( $this, 'display_bfi_enablecache_key_element'), "bfi-options", "sectionperformance");

		register_setting("section", "bfi_subscription_key");
		register_setting("section", "bfi_api_key");
		register_setting("section", "bfi_form_key");
		register_setting("section", "bfi_currentcurrency_key");
		register_setting("section", "bfi_usessl_key");
		register_setting("section", "bfi_ssllogo_key");
		register_setting("section", "bfi_itemperpage_key");
		register_setting("section", "bfi_maxqtselectable_key");
		register_setting("section", "bfi_defaultdisplaylist_key");
		register_setting("section", "bfi_isportal_key");
		register_setting("section", "bfi_showdata_key");
		register_setting("section", "bfi_sendtocart_key");
//		register_setting("section", "bfi_showbadge_key");
		register_setting("section", "bfi_enablecoupon_key");
			
		register_setting("section", "bfi_posx_key");
		register_setting("section", "bfi_posy_key");
		register_setting("section", "bfi_startzoom_key");
		register_setting("section", "bfi_googlemapskey_key");

		register_setting("section", "bfi_googlerecaptcha_key");
		register_setting("section", "bfi_googlerecaptcha_secret_key");
		register_setting("section", "bfi_googlerecaptcha_theme_key");

		register_setting("section", "bfi_adultsage_key");
		register_setting("section", "bfi_adultsqt_key");
		register_setting("section", "bfi_childrensage_key");
		register_setting("section", "bfi_senioresage_key");

		register_setting("section", "bfi_useproxy_key");
		register_setting("section", "bfi_urlproxy_key");

		register_setting("section", "bfi_gaenabled_key");
		register_setting("section", "bfi_gaaccount_key");
		register_setting("section", "bfi_eecenabled_key");
		register_setting("section", "bfi_criteoenabled_key");

		register_setting("section", "bfi_enablecache_key");



	}
	
	
	
	/**
	 * Change the admin footer text on BookingFor admin pages.
	 *
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_bookingfor' ) ) {
			return;
		}
		$current_screen = get_current_screen();
		$bfi_pages       = bfi_get_screen_ids();

		// Set only wc pages
		$bfi_pages = array_flip( $bfi_pages );
		if ( isset( $bfi_pages['profile'] ) ) {
			unset( $bfi_pages['profile'] );
		}
		if ( isset( $bfi_pages['user-edit'] ) ) {
			unset( $bfi_pages['user-edit'] );
		}
		$bfi_pages = array_flip( $bfi_pages );

		// Check to make sure we're on a BookingFor admin page
		if ( isset( $current_screen->id ) && apply_filters( 'bookingfor_display_admin_footer_text', in_array( $current_screen->id, $bfi_pages ) ) ) {
			// Change the footer text
			if ( ! get_option( 'bookingfor_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>BookingFor</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you from Bookingfor in advance!', 'bfi' ), '<a href="https://wordpress.org/support/view/plugin-reviews/bookingfor?filter=5#postform" target="_blank" class="bfi-rating-link" data-rated="' . esc_attr__( 'Thanks', 'bfi' ) . '">', '</a>' );
				bfi_enqueue_js( "
					jQuery( 'a.bfi-rating-link' ).click( function() {
						jQuery.post( '" . BFI()->ajax_url() . "', { action: 'bookingfor_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			} else {
				$footer_text = __( 'Thank you for selling with BookingFor.', 'bfi' );
			}
		}

		return $footer_text;
	}
}
endif;
return new BFI_Admin();