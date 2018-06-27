<?php
/*
Plugin Name: BookingFor
Description: BookingFor integration Code for Wordpress
Version: 3.2.5
Author: BookingFor
Author URI: http://www.bookingfor.com/
*/
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'BookingFor' ) ) :
final class BookingFor {
	
	public $version = '3.2.5';
	public $currentOrder = null;
	
	protected static $_instance = null;
	
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	public function template_path() {
		return apply_filters( 'bookingfor_template_path', 'bookingfor/' );
	}

	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'frontend' :
				return ! is_admin();
//			case 'ajax' :
//				return defined( 'DOING_AJAX' );
//			case 'cron' :
//				return defined( 'DOING_CRON' );
//			case 'frontend' :
//				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
	public function __construct() {

		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'bookingfor_loaded' );
	}

	private function define_constants() {		
		$subscriptionkey= get_option('bfi_subscription_key', '');
		$apikey= get_option('bfi_api_key', '');
		$form_key= get_option('bfi_form_key', '');
		$XGooglePosDef = get_option('bfi_posx_key', 0);
		$YGooglePosDef = get_option('bfi_posy_key', 0);
		$startzoom = get_option('bfi_startzoom_key',15);
		$googlemapskey = get_option('bfi_googlemapskey_key','');
		$itemperpage = get_option('bfi_itemperpage_key',10);
		$googlerecaptchakey = get_option('bfi_googlerecaptcha_key','');
		$googlerecaptchasecretkey = get_option('bfi_googlerecaptcha_secret_key','');
		$googlerecaptchathemekey = get_option('bfi_googlerecaptcha_theme_key','light');
		$googlerecaptchasizekey = get_option('bfi_googlerecaptcha_size_key','normal');

		$isportal = get_option('bfi_isportal_key', 1);
		$showdata = get_option('bfi_showdata_key', 1);
		$sendtocart = get_option('bfi_sendtocart_key', 0);
		$showbadge = 1;// get_option('bfi_showbadge_key', 1);
		$enablecoupon = get_option('bfi_enablecoupon_key', 0);
		
		$usessl = get_option('bfi_usessl_key',0);
		$ssllogo = get_option('bfi_ssllogo_key','');

		$useproxy = get_option('bfi_useproxy_key',0);
		$urlproxy = get_option('bfi_urlproxy_key','127.0.0.1:8888');
		
		$gaenabled = get_option('bfi_gaenabled_key', 0);
		$gaaccount = get_option('bfi_gaaccount_key', '');
		$eecenabled = get_option('bfi_eecenabled_key', 0);
		$criteoenabled = get_option('bfi_criteoenabled_key', 0);

		$enablecache = get_option('bfi_enablecache_key', 1);

		$bfi_adultsage_key = get_option('bfi_adultsage_key', 18);
		$bfi_adultsqt_key = get_option('bfi_adultsqt_key', 2);
		$bfi_childrensage_key = get_option('bfi_childrensage_key', 12);
		$bfi_senioresage_key = get_option('bfi_senioresage_key', 65);
		$bfi_maxqtSelectable_key = get_option('bfi_maxqtSelectable_key', 20);
		$bfi_defaultdisplaylist_key = get_option('bfi_defaultdisplaylist_key', 0);
		

		$bfi_currentcurrency = get_option('bfi_currentcurrency_key', '');

		$nMonthinCalendar = 2;

		$useragent=$_SERVER['HTTP_USER_AGENT'];

		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
			$nMonthinCalendar = 1;
		}

		$this->define( 'BFI_VERSION', $this->version );
		$this->define( 'COM_BOOKINGFORCONNECTOR_MONTHINCALENDAR', $nMonthinCalendar );
		$subscriptionkey = strtolower($subscriptionkey);
		if(strpos($subscriptionkey,'https://') !== false){
			$subscriptionkey = str_replace("https://", "", $subscriptionkey);
			$subscriptionkey = str_replace(".bookingfor.com/modules/bookingfor/services/bookingservice.svc", "", $subscriptionkey);
			$subscriptionkey = str_replace("/", "", $subscriptionkey);
		}
		$bfiBaseUrl = 'https://' . $subscriptionkey . '.bookingfor.com';
//		$bfiBaseUrl = 'http://localhost:58892';
//		$bfiBaseUrl =  'http://' . $subscriptionkey . '.bookingfor.com:58892';

		$cachetime = get_option('cachetime ', 3600); // 1 hour default
		$cachedir = get_option('cachedir', WP_CONTENT_DIR . '/cache/bookingfor');

		$this->define( 'COM_BOOKINGFORCONNECTOR_CACHEDIR', $cachedir );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CACHETIME', $cachetime );

		$this->define( 'COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY', $subscriptionkey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_API_KEY', $apikey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_FORM_KEY', $form_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_WSURL', $bfiBaseUrl .'/modules/bookingfor/services/bookingservice.svc' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ORDERURL', $bfiBaseUrl .'/Public/{language}/orderlogin' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_PAYMENTURL', $bfiBaseUrl .'/Public/{language}/payment/' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_PRIVACYURL', $bfiBaseUrl .'/Public/{language}/privacy' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_TERMSOFUSEURL', $bfiBaseUrl .'/Public/{language}/termsofuse' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTLOGIN', $bfiBaseUrl .'/Public/{language}/?openloginpopup=1' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTREGISTRATION', $bfiBaseUrl .'/Public/{language}/Account/Register' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ACCOUNTFORGOTPASSWORD', $bfiBaseUrl .'/Public/{language}/Account/sendforgotpasswordlink' );

		$this->define( 'COM_BOOKINGFORCONNECTOR_CURRENTCURRENCY', $bfi_currentcurrency );
		$this->define( 'COM_BOOKINGFORCONNECTOR_MAXATTACHMENTFILES', 3 );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_IMGURL', $subscriptionkey . '/bookingfor/images' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_IMGURL_CDN', '//cdnbookingfor.blob.core.windows.net/' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_BASEIMGURL', 'https://cdnbookingfor.blob.core.windows.net/' . $subscriptionkey . '/bookingfor/images' );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_POSX', $XGooglePosDef );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_POSY', $YGooglePosDef );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_STARTZOOM', $startzoom );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLEMAPSKEY', $googlemapskey );

		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY', $googlerecaptchakey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASECRETKEY', $googlerecaptchasecretkey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHATHEMEKEY', $googlerecaptchathemekey );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASIZEKEY', $googlerecaptchasizekey );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDER', false);
		$this->define( 'COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM', "");
		$this->define( 'COM_BOOKINGFORCONNECTOR_ANONYMOUS_TYPE', "3,4");
		$this->define( 'COM_BOOKINGFORCONNECTOR_ITEMPERPAGE', $itemperpage );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_ISPORTAL', $isportal );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWDATA', $showdata );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SENDTOCART', $sendtocart );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SHOWBADGE', $showbadge );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLECOUPON', $enablecoupon );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_USESSL', $usessl );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SSLLOGO', $ssllogo );

		$this->define( 'COM_BOOKINGFORCONNECTOR_ADULTSAGE', $bfi_adultsage_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_ADULTSQT', $bfi_adultsqt_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CHILDRENSAGE', $bfi_childrensage_key );
		$this->define( 'COM_BOOKINGFORCONNECTOR_SENIORESAGE', $bfi_senioresage_key );

		$this->define( 'COM_BOOKINGFORCONNECTOR_USEPROXY', $useproxy );
		$this->define( 'COM_BOOKINGFORCONNECTOR_URLPROXY', $urlproxy );

		$this->define( 'COM_BOOKINGFORCONNECTOR_GAENABLED', $gaenabled );
		$this->define( 'COM_BOOKINGFORCONNECTOR_GAACCOUNT', $gaaccount );
		$this->define( 'COM_BOOKINGFORCONNECTOR_EECENABLED', $eecenabled );
		$this->define( 'COM_BOOKINGFORCONNECTOR_CRITEOENABLED', $criteoenabled );

		$this->define( 'COM_BOOKINGFORCONNECTOR_ENABLECACHE', $enablecache );

		$this->define( 'COM_BOOKINGFORCONNECTOR_MAXQTSELECTABLE', $bfi_maxqtSelectable_key );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST', $bfi_defaultdisplaylist_key );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_KEY', 'WZgfdUps' );
		
		$this->define( 'COM_BOOKINGFORCONNECTOR_CARTMULTIMERCHANTENABLED', false );


	}

	private function init_hooks() {
		register_activation_hook(__FILE__,array( 'BFI_Install', 'install' ));
//		add_action('init', function() {
//				$regex = '^bfi-api/v1/(/[^/]*)?$';
//				$location = 'index.php?_api_controller=$matches[1]';
//				$priority = 'top';
//				add_rewrite_rule( $regex, $location, $priority );
//		});
		add_action( 'admin_notices', array( $this, 'bfi_plugin_admin_notices' ) );
		add_action('parse_request', array($this, 'sniff_requests'), 0);
		add_action('parse_request', array($this, 'bfi_change_currency'), 0);

		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'bfi_StartSession' ), 0 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'bfi_Shortcodes', 'init' ) );
		add_action( 'wp_logout', array( $this, 'bfi_EndSession' ) );
		if ( $this->is_request( 'frontend' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this , 'bfi_load_scripts' ) ,1 ); // spostata priorità altrimenti sovrascrive template
//			add_action( 'wp_enqueue_scripts', array( $this , 'bfi_load_scripts_locale' ) );
			add_action ( 'wp_head', array( $this , 'bfi_js_variables' ) );
			//remove canonical 
			add_filter( 'wpseo_canonical', '__return_false' );
		}
		if ( $this->is_request( 'admin' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this , 'bfi_load_admin_scripts' ) );
		}

//		register_activation_hook( __FILE__, array( 'BFI_Install', 'install' ) );
//		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
//		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
//		add_action( 'init', array( $this, 'init' ), 0 );
//		add_action( 'init', array( 'bfi_Shortcodes', 'init' ) );
//		add_action( 'init', array( 'BFI_Emails', 'init_transactional_emails' ) );
//		add_action( 'init', array( $this, 'wpdb_table_fix' ), 0 );
//		add_action( 'switch_blog', array( $this, 'wpdb_table_fix' ), 0 );
	}


	function bfi_plugin_admin_notices() {
		$bfSubscriptionKey = COM_BOOKINGFORCONNECTOR_SUBSCRIPTION_KEY;
		if (is_plugin_active(plugin_basename( __FILE__ )) && empty($bfSubscriptionKey)) {
			echo "<div class='error'><p><b>Complete BookingFor Settings <a href='". admin_url('admin.php?page=bfi-settings')."'>here</a></b></p></div>";
		}
	}
	/**	Sniff Requests 
	*	This is where we hijack all API requests 
	* 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness 
	*	@return die if API request 
	*/ 
	public function sniff_requests(){ 
		
		global $wp; 
//		echo "<pre>sniff_requests --";
//		echo $wp->query_vars['_api_controller'];
//		echo $_REQUEST['prova'] ;

//		echo "</pre>";
		if(isset($wp->query_vars['_api_controller'])){ 
			include_once( 'includes/BFCHelper.php' );
			include_once( 'includes/wsQueryHelper.php' );
			include_once( 'includes/api/class-bfi-controller.php' );
			$bfi_api = new BFI_Controller;
			$bfi_api->handle_request();
//			die();
			exit; 
		} 
	} 

	public function bfi_change_currency(){ 
		
		global $wp; 
		if(isset($_REQUEST['bfiselectedcurrency'])){ 
			bfi_set_currentCurrency($_REQUEST['bfiselectedcurrency']);
		} 
	} 

	public static function bfi_StartSession() {
		if(!session_id()) {
//			  ini_set('session.save_handler', 'files'); 
			session_start();
		}
	}
	public static function bfi_EndSession() {
		session_destroy();
	}

	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function include_template_functions() {
		include_once( 'includes/bfi-template-functions.php' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		include_once( 'includes/BFCHelper.php' );
		include_once( 'includes/wsQueryHelper.php' );
		include_once( 'includes/class-bfi-post-types.php' ); // Registers post types

		include_once( 'includes/bfi-core-functions.php' );
		include_once( 'includes/bfi-widget-functions.php' );
		include_once( 'includes/class-bfi-install.php' );
		include_once( 'includes/bfi-page-functions.php' );
		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
//			$this->bfi_load_scripts();			
		}
		if ( $this->is_request( 'admin' ) ) {
//			$this->bfi_load_admin_scripts();			
			include_once( 'includes/admin/class-bfi-admin.php' );
			// Meta-Box Class
			include_once( 'includes/admin/class-bfi-admin-meta-boxes.php' );
			include_once('includes/model/merchants.php' );
			include_once('includes/model/resources.php' );
			include_once('includes/model/portal.php' );
			include_once('includes/model/tag.php');
			include_once('includes/model/onsellunits.php' );
		}
		include_once( 'includes/class-bfi-query.php' ); // The main query class
		include_once( 'includes/class-bfi-shortcodes.php' );                     // Shortcodes class

		$this->query = new BFI_Query();
		$this->shortcodes = new bfi_Shortcodes();
		$cryptoVersion = BFCHelper::encryptSupported();
		$this->define( 'COM_BOOKINGFORCONNECTOR_CRYPTOVERSION', $cryptoVersion );

	}
	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/bfi-template-hooks.php' );
		include_once( 'includes/class-bfi-template-loader.php' );                // Template Loader
		include_once( 'includes/SimpleDOM.php' );
		include_once( 'includes/model/criteo.php' );
		include_once('includes/model/services.php' );
		include_once('includes/model/search.php' );
		include_once('includes/model/resource.php' );
		include_once('includes/model/resources.php' );
		include_once('includes/model/condominiums.php' );
		include_once('includes/model/ratings.php' );
		include_once('includes/model/portal.php' );
		include_once('includes/model/payment.php' );
		include_once('includes/model/orders.php' );
		include_once('includes/model/merchants.php' );
		include_once('includes/model/merchantdetails.php' );
		include_once('includes/model/inforequest.php' );
		include_once('includes/model/onsellunit.php' );
		include_once('includes/model/onsellunits.php' );
		include_once('includes/model/searchonsell.php' );
		include_once('includes/model/tag.php');
	}


	public function bfi_load_admin_scripts(){
		wp_enqueue_script('jquery');
		wp_enqueue_script('bf_admin', plugins_url( 'assets/js/bf_admin.js', __FILE__ ),array(),$this->version);
//		wp_enqueue_style( 'bf_admin_css', plugins_url( 'assets/css/basic.css', __FILE__ ));
		wp_enqueue_style('bookingfor', plugins_url( 'assets/css/bookingfor.css', __FILE__ ),array(),$this->version,'all');
		
		wp_enqueue_script('admin_select2_js', plugins_url( 'assets/js/select2/js/select2.min.js', __FILE__ ), array('jquery'));
		wp_enqueue_style('admin_select2_css', plugins_url( 'assets/js/select2/css/select2.min.css', __FILE__ ),array(),$this->version,'all');
	}

	public function bfi_load_scripts(){
		wp_enqueue_style('wp-jquery-ui-dialog');
		wp_enqueue_style('jquery-ui-style', plugins_url( 'assets/jquery-ui/themes/smoothness/jquery-ui.min.css', __FILE__ ),array(),$this->version,'all');
		wp_enqueue_style('fontawesome', plugins_url( 'assets/css/font-awesome.min.css', __FILE__ ),array(),$this->version,'all');
		wp_enqueue_style('magnificpopup', plugins_url( 'assets/css/magnific-popup.css', __FILE__ ),array(),$this->version,'all');
		wp_enqueue_style('webuipopover', plugins_url( 'assets/js/webui-popover/jquery.webui-popover.min.css', __FILE__ ),array(),$this->version,'all');
		wp_enqueue_style('bookingfor', plugins_url( 'assets/css/bookingfor.css', __FILE__ ),array(),$this->version,'all');

		$template = strtolower(get_option( 'template' ));		
		if ( file_exists(BFI()->plugin_path() . '/assets/css/theme/bookingfor' . $template . '.css') ) {
						wp_enqueue_style('bookingfor' . $template, plugins_url( 'assets/css/theme/bookingfor' . $template . '.css', __FILE__ ),array(),$this->version,'all');
		}

		wp_enqueue_script('jquery');
		
		wp_enqueue_script('validate', plugins_url( 'assets/js/jquery-validation/jquery.validate.min.js', __FILE__ ),array(),$this->version);
		wp_enqueue_script('validateadditional', plugins_url( 'assets/js/jquery-validation/additional-methods.min.js', __FILE__ ),array(),$this->version);
		wp_enqueue_script('validateadditionalcustom', plugins_url( 'assets/js/jquery.validate.additional-custom-methods.js', __FILE__ ),array(),$this->version,true);
		
		wp_enqueue_script('rating', plugins_url( 'assets/js/jquery.rating.pack.js', __FILE__ ),array(),$this->version);
		wp_enqueue_script('magnificpopup', plugins_url( 'assets/js/jquery.magnific-popup.min.js', __FILE__ ),array(),$this->version);
		wp_enqueue_script('webuipopover', plugins_url( 'assets/js/webui-popover/jquery.webui-popover.min.js', __FILE__ ),array(),$this->version);
		wp_enqueue_script('shorten', plugins_url( 'assets/js/jquery.shorten.js', __FILE__ ),array(),$this->version);
		
		wp_enqueue_script("jquery-effects-core");
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery-form');

		wp_enqueue_script('blockui', plugins_url( 'assets/js/jquery.blockUI.js', __FILE__ ),array(),$this->version);
		wp_register_script('bfi', plugins_url( 'assets/js/bfi.js', __FILE__ ),array(),$this->version,true);
		wp_enqueue_script('bfi');

		wp_enqueue_script('bfisearchonmap', plugins_url( 'assets/js/bfisearchonmap.js', __FILE__ ),array(),$this->version);
		wp_enqueue_script('bficalendar', plugins_url( 'assets/js/bfi_calendar.js', __FILE__ ),array(),$this->version,true);
		
		if(!empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY)){
			wp_enqueue_script('recaptchainit', plugins_url( 'assets/js/recaptcha.js', __FILE__ ),array(),$this->version);
		}
	}
	public function bfi_js_variables(){
		$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
		$url_cart_page = get_permalink( $cartdetails_page->ID );
		if(COM_BOOKINGFORCONNECTOR_USESSL){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}
		
		?>
	  <script type="text/javascript">
		/* <![CDATA[ */
		var bfi_variable = {
			"bfi_urlCheck":<?php echo json_encode( get_site_url() .'/bfi-api/v1/task'); ?>,
				"bfi_cultureCode":<?php echo json_encode($this->language); ?>,
				"bfi_defaultcultureCode":"en-gb",
				"defaultCurrency":<?php echo json_encode(bfi_get_defaultCurrency()); ?>,
				"currentCurrency":<?php echo json_encode(bfi_get_currentCurrency()); ?>,
				"CurrencyExchanges":<?php echo json_encode(BFCHelper::getCurrencyExchanges()); ?>,
				"bfi_defaultdisplay":<?php echo json_encode(COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST); ?>,
				"bfi_sendtocart":<?php echo json_encode(COM_BOOKINGFORCONNECTOR_SENDTOCART); ?>,			
				"bfi_eecenabled":<?php echo json_encode(COM_BOOKINGFORCONNECTOR_EECENABLED); ?>,			
				"bfi_carturl":"<?php echo $url_cart_page; ?>",			
			};
		/* ]]> */
	  </script><?php
	}
//	public function bfi_load_scripts_locale(){
//		$bfi_variable = array( 
//			'bfi_urlCheck' =>  get_site_url() .'/bfi-api/v1/task',
//			'bfi_cultureCode' => $this->language,
//			'bfi_defaultcultureCode' => 'en-gb',
//			'defaultCurrency' => bfi_get_defaultCurrency(),
//			'currentCurrency' => bfi_get_currentCurrency(),
//			'CurrencyExchanges' => BFCHelper::getCurrencyExchanges(),
//			'bfi_defaultdisplay'=>COM_BOOKINGFORCONNECTOR_DEFAULTDISPLAYLIST,
//			);
//		wp_localize_script( 'bfi', 'bfi_variable', $bfi_variable );
//		if(substr($this->language,0,2)!='en'){
////			wp_enqueue_script('jquery-ui-datepicker_locale',plugins_url( 'assets/jquery-ui/i18n/datepicker-' . substr($this->language,0,2) . '.js', __FILE__ ));
//		}
//	}

	public function seoUrl($string) {
		//Lower case everything
		$string = strtolower($string);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);
		return $string;
	}

	public function isMerchantPage(){
		global $post;
		$merchantdetails_page_id = bfi_get_template_page_id( 'merchantdetails' );
		if (!empty($post) &&  $post->ID == $merchantdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isCondominiumPage(){
		global $post;
		$condominiumdetails_page_id = bfi_get_template_page_id( 'condominiumdetails' );
		if (!empty($post) &&  $post->ID == $condominiumdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isResourcePage(){
		global $post;
		$accommodationdetails_page_id = bfi_get_template_page_id( 'accommodationdetails' );
		if (!empty($post) &&  $post->ID == $accommodationdetails_page_id ){
			return true;
		}
		return false;
	}
	public function isResourceOnSellPage(){
		global $post;
		$onselldetails_page_id = bfi_get_template_page_id( 'onselldetails' );
		if (!empty($post) &&  $post->ID == $onselldetails_page_id ){
			return true;
		}
		return false;
	}
	public function isSearchPage(){
		global $post;
		$searchavailability_page_id = bfi_get_template_page_id( 'searchavailability' );
		if (!empty($post) &&  $post->ID == $searchavailability_page_id ){
			return true;
		}
		return false;
	}
	public function isSearchOnSellPage(){
		global $post;
		$searchonsell_page_id = bfi_get_template_page_id( 'searchonsell' );
		if (!empty($post) &&  $post->ID == $searchonsell_page_id ){
			return true;
		}
		return false;
	}

	public function isCartPage(){
		global $post;
		$cartdetails_page_id = bfi_get_template_page_id( 'cartdetails' );
		if (!empty($post) &&  $post->ID == $cartdetails_page_id ){
			return true;
		}
		return false;
	}

	public function init() {
		do_action( 'before_bookingfor_init' );


		// Set up localisation.
		$this->load_plugin_textdomain();
		if ( $this->is_request( 'frontend' ) ) {
//			$this->bfi_load_scripts_locale();
			
			//
//			remove_filter( 'the_content', 'wpautop' ); //Cambia le doppie interruzioni di riga nel testo in paragrafi HTML (<p>...</p>). https://codex.wordpress.org/it:Riferimento_funzioni/wpautop
//			remove_filter( 'the_content', 'convert_chars' );
//			remove_filter( 'the_content', 'wptexturize' );
		}

		
		// Init action.
		do_action( 'bookingfor_init' );
	}


	function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'bookingfor' );
//		$l = get_locale();
		if(defined('ICL_LANGUAGE_CODE')){
			$locale =ICL_LANGUAGE_CODE;
		}

		$this->language = $this->return_lang_mapping($locale);
		$GLOBALS['bfi_lang'] = $this->language;
		load_plugin_textdomain( 'bfi', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}

	function return_lang_mapping($lang) {
		$lang_array = array(
			'en' => 'en-GB',
			'it' => 'it-IT',
			'de' => 'de-DE',
			'pl' => 'pl-PL',
			'ru' => 'ru-RU',
			'hu' => 'hu-HU',
			'cs' => 'cs-CZ',
			'cz' => 'cs-CZ',
			'gr' => 'el-GR',
			'fr' => 'fr-FR',
			'es' => 'es-ES',
			'hr' => 'hr-HR',
			'nl' => 'nl-NL',
			'en_GB' => 'en-GB',
			'en-GB' => 'en-GB',
			'en_US' => 'en-GB',
			'en-US' => 'en-GB',
			'ru_RU' => 'ru-RU',
			'ru-RU' => 'ru-RU',
			'pl_PL' => 'pl-PL',
			'pl-PL' => 'pl-PL',
			'it_IT' => 'it-IT',
			'it-IT' => 'it-IT',
			'hu_HU' => 'hu-HU',
			'hu-HU' => 'hu-HU',
			'de_DE' => 'de-DE',
			'de-DE' => 'de-DE',
			'cs_CZ' => 'cs-CZ',
			'cs-CZ' => 'cs-CZ',
			'el_GR' => 'el-GR',
			'el-GR' => 'el-GR',
			'fr_FR' => 'fr-FR',
			'fr-FR' => 'fr-FR',
			'es_ES' => 'es-ES',
			'es-ES' => 'es-ES',
			'hr_HR' => 'hr-HR',
			'hr-HR' => 'hr-HR',
			'nl-NL' => 'nl-NL',
			'nl_NL' => 'nl-NL'
		);
		if(isset($lang_array[$lang])) {
		  return $lang_array[$lang];
		}
		else {
		  return 'it-IT';
		}
	}
}

endif;

function BFI() {
	return BookingFor::instance();
}

// Global for backwards compatibility.
$GLOBALS['bookingfor'] = BFI();