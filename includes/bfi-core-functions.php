<?php
/**
 * BookingFor Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		BookingFor
 * @category 	Core
 * @package 	Bookingfor/Functions
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function bfi_setSessionFromSubmittedData() {
	$ci = BFCHelper::getStayParam('checkin', new DateTime('UTC'));
	$merchantCategoryId = isset($_REQUEST['merchantCategoryId']) ? $_REQUEST['merchantCategoryId'] : array();
	$cultureCode = isset($_REQUEST['cultureCode']) ? $_REQUEST['cultureCode'] : '';
	$availabilitytype =  isset($_REQUEST['availabilitytype']) ? $_REQUEST['availabilitytype'] : 1;
	$duration = BFCHelper::getStayParam('duration');
	if($availabilitytype == 2 && isset($_REQUEST['duration'])){
		$duration = $_REQUEST['duration'];
	}
	if($availabilitytype == 3 ){
		$duration = 1;
	}

	$itemtypes =  isset($_REQUEST['itemtypes']) ? $_REQUEST['itemtypes'] : '';
	$groupresulttype =  isset($_REQUEST['groupresulttype']) ? $_REQUEST['groupresulttype'] : 1;
	
	$merchantResults = false;
	if(COM_BOOKINGFORCONNECTOR_ISPORTAL){
		//$merchantResults = !empty($merchantCategoryId) && in_array($merchantCategoryId, BFCHelper::getCategoryMerchantResults($cultureCode));
		$merchantResults = ($groupresulttype==1);
	}
	$currParamInSession = BFCHelper::getSearchParamsSession();
	$currParam = array(
		'searchid' => isset($_REQUEST['searchid']) ? $_REQUEST['searchid'] : uniqid('', true),
		'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : '',
		'newsearch' => isset($_REQUEST['newsearch']) ? $_REQUEST['newsearch'] : '0',
		'checkin' => BFCHelper::getStayParam('checkin', new DateTime('UTC')),
		'checkout' => BFCHelper::getStayParam('checkout', $ci->modify(BFCHelper::$defaultDaysSpan)),
		'duration' => $duration,
		'searchTerm' => isset($_REQUEST['searchTerm']) ? $_REQUEST['searchTerm'] : '',
		'searchTermValue' => isset($_REQUEST['searchTermValue']) ? $_REQUEST['searchTermValue'] : '',
		'stateIds' => isset($_REQUEST['stateIds']) ? $_REQUEST['stateIds'] : '',
		'regionIds' => isset($_REQUEST['regionIds']) ? $_REQUEST['regionIds'] : '',
		'cityIds' => isset($_REQUEST['cityIds']) ? $_REQUEST['cityIds'] : '',
		'merchantIds' => isset($_REQUEST['merchantIds']) ? $_REQUEST['merchantIds'] : '',
		'merchantTagIds' => isset($_REQUEST['merchantTagIds']) ? $_REQUEST['merchantTagIds'] : '',
		'productTagIds' => isset($_REQUEST['productTagIds']) ? $_REQUEST['productTagIds'] : '',
		'paxages' => BFCHelper::getStayParam('paxages'),
		'masterTypeId' => isset($_REQUEST['masterTypeId']) ? $_REQUEST['masterTypeId'] : '',
		'merchantResults' => $merchantResults,
		'merchantCategoryId' => $merchantCategoryId,
		'merchantId' => isset($_REQUEST['merchantId']) ? $_REQUEST['merchantId'] : 0,
		'zoneId' => isset($_REQUEST['locationzone']) ? $_REQUEST['locationzone'] : 0,
		'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : -1,
		'availabilitytype' => $availabilitytype,
		'itemtypes' => $itemtypes,
		'groupresulttype' => $groupresulttype,
		'locationzone' => isset($_REQUEST['locationzone']) ? $_REQUEST['locationzone'] : 0,
		'cultureCode' => $cultureCode,
		'paxes' => isset($_REQUEST['persons']) ? $_REQUEST['persons'] : count(BFCHelper::getStayParam('paxages')),
		'tags' => isset($_REQUEST['tags']) ? $_REQUEST['tags'] : '',
		'resourceName' =>  isset($_REQUEST['resourceName']) ? $_REQUEST['resourceName'] : 0,
		'refid' => isset($_REQUEST['refid']) ? $_REQUEST['refid'] : 0,
		'condominiumsResults' => isset($_REQUEST['condominiumsResults']) ? $_REQUEST['condominiumsResults'] : '',
		'pricerange' => isset($_REQUEST['pricerange']) ? $_REQUEST['pricerange'] : '',
		'onlystay' => isset($_REQUEST['onlystay']) ? $_REQUEST['onlystay'] : 0,
		'resourceId' => isset($_REQUEST['resourceId']) ? $_REQUEST['resourceId'] : '',
		'extras' => isset($_REQUEST['extras']) ? $_REQUEST['extras'] : '',
		'packages' => isset($_REQUEST['packages']) ? $_REQUEST['packages'] : '',
		'pricetype' => isset($_REQUEST['pricetype']) ? $_REQUEST['pricetype'] : '',
		'filters' => isset($_REQUEST['filters']) ? $_REQUEST['filters'] : '',
		'rateplanId' => isset($_REQUEST['pricetype']) ? $_REQUEST['pricetype'] : '',
		'variationPlanId' => isset($_REQUEST['variationPlanId']) ? $_REQUEST['variationPlanId'] : '',
		'gotCalculator' => isset($_REQUEST['gotCalculator']) ? $_REQUEST['gotCalculator'] : '',
		'totalDiscounted' => isset($currParamInSession['totalDiscounted']) ? $currParamInSession['totalDiscounted'] : '',
		'suggestedstay' => isset($currParamInSession['suggestedstay']) ?$currParamInSession['suggestedstay'] : '',
		'variationPlanIds' => isset($_REQUEST['variationPlanId']) ? $_REQUEST['variationPlanId'] : '',
		'points' => BFCHelper::getVar('searchType')=="1" ? BFCHelper::getVar('points') : "",
	);
	BFCHelper::setSearchParamsSession($currParam);
}


function bfi_get_template_part( $slug, $name = '' ) {
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/bookingfor/slug-name.php
	if ( $name && ! BFI_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}-{$name}.php", BFI()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( BFI()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = BFI()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/bookingfor/slug.php
	if ( ! $template && ! BFI_TEMPLATE_DEBUG_MODE ) {
		$template = locate_template( array( "{$slug}.php", BFI()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'bfi_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function bfi_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	$located = bfi_locate_template( $template_name, $template_path, $default_path );
	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'bfi_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'bookingfor_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'bookingfor_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like bfi_get_template, but returns the HTML instead of outputting.
 * @see bfi_get_template
 * @since 2.5.0
 * @param string $template_name
 */
function bfi_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	bfi_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function bfi_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = BFI()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = BFI()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template/
	if ( ! $template || BFI_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'bookingfor_locate_template', $template, $template_name, $template_path );
}


/**
 * Get an image size.
 *
 * Variable is filtered by bookingfor_get_image_size_{image_size}.
 *
 * @param mixed $image_size
 * @return array
 */
function bfi_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'shop_thumbnail', 'shop_catalog', 'shop_single' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'bookingfor_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function bfi_enqueue_js( $code ) {
	global $bfi_queued_js;

	if ( empty( $bfi_queued_js ) ) {
		$bfi_queued_js = '';
	}

	$bfi_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function bfi_print_js() {
	global $bfi_queued_js;

	if ( ! empty( $bfi_queued_js ) ) {
		// Sanitize.
		$bfi_queued_js = wp_check_invalid_utf8( $bfi_queued_js );
		$bfi_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $bfi_queued_js );
		$bfi_queued_js = str_replace( "\r", '', $bfi_queued_js );

		$js = "<!-- BookingFor JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $bfi_queued_js });\n</script>\n";

		/**
		 * bookingfor_queued_js filter.
		 *
		 * @since 2.6.0
		 * @param string $js JavaScript code.
		 */
		echo apply_filters( 'bookingfor_queued_js', $js );

		unset( $bfi_queued_js );
	}
}

/**
 * Set a cookie - wrapper for setcookie using WP constants.
 *
 * @param  string  $name   Name of the cookie being set.
 * @param  string  $value  Value of the cookie.
 * @param  integer $expire Expiry of the cookie.
 * @param  string  $secure Whether the cookie should be served only over https.
 */
function bfi_setcookie( $name, $value, $expire = 0, $secure = false ) {
	if ( ! headers_sent() ) {
		setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure );
	} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		headers_sent( $file, $line );
		trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
	}
}


/**
 * Get a log file path.
 *
 * @since 2.2
 * @param string $handle name.
 * @return string the log file path.
 */
function bfi_get_log_file_path( $handle ) {
	return trailingslashit( BFI_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
}


/**
 * BookingFor Core Supported Themes.
 *
 * @since 2.2
 * @return string[]
 */
function bfi_get_core_supported_themes() {
	return array( 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}

/**
 * Enables template debug mode.
 */
function bfi_template_debug_mode() {
	if ( ! defined( 'BFI_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'bookingfor_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'BFI_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'BFI_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'bfi_template_debug_mode', 20 );






/**
 * Display a BookingFor help tip.
 *
 * @since  2.5.0
 *
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
if ( ! function_exists( 'bfi_help_tip' ) ) {
	function bfi_help_tip( $tip, $allow_html = false ) {
		if ( $allow_html ) {
			$tip = bfi_sanitize_tooltip( $tip );
		} else {
			$tip = esc_attr( $tip );
		}

		return '<span class="bookingfor-help-tip" data-tip="' . $tip . '"></span>';
	}
}

if ( ! function_exists( 'bfi_remove_querystring_var' ) ) {
	function bfi_remove_querystring_var($url, $key) { 
		$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
		$url = substr($url, 0, -1); 
		return $url; 
	}
}
if ( ! function_exists( 'bfi_add_querystring_var' ) ) {
	function bfi_add_querystring_var($url, $key, $value) {
		$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
		$url = substr($url, 0, -1);
		if (strpos($url, '?') === false) {
			return ($url . '?' . $key . '=' . $value);
		} else {
			return ($url . '&' . $key . '=' . $value);
		}
	}
}
if ( ! function_exists( 'bfi_verify_captcha' ) ) {
	function bfi_verify_captcha( $parameter = true )
	{
		if( isset( $_POST['g-recaptcha-response'] ) && !empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASECRETKEY) )
		{
			$response = json_decode(wp_remote_retrieve_body( wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=".COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASECRETKEY."&response=" .$_POST['g-recaptcha-response'] ) ), true );

			if( $response["success"] )
			{
				return $parameter;
			}
		}

		return false;
	}
}
//	add_filter("preprocess_comment", "bfi_verify_captcha");
if ( ! function_exists( 'bfi_display_captcha' ) ) {
	function bfi_display_captcha($idrecaptcha) {
		if( !empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY) && !empty(COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASECRETKEY)  ){
			$currCaptcha = '<div id="' . $idrecaptcha . '"  class="g-recaptcha bfi-recaptcha" ' . 
			' data-sitekey="' . COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHAKEY .'" 
			  data-theme="' . COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHATHEMEKEY .'" 
			  data-size="' . COM_BOOKINGFORCONNECTOR_GOOGLE_GOOGLERECAPTCHASIZEKEY .'"
			  ></div>';
			
			echo $currCaptcha;
		}
	}
}
//	function bfi_get_userId() {
//		$tmpUserId = BFCHelper::getSession('tmpUserId', null , 'com_bookingforconnector');
//		if(empty($tmpUserId)){
//			$uid = get_current_user_id();
//			$user = get_user_by('id', $uid);
//			if (!empty($user->ID)) {
//				$tmpUserId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//			}
//			if(empty($tmpUserId)){
//				$tmpUserId = uniqid($_SERVER["SERVER_NAME"]);
//			}
//			BFCHelper::setSession('tmpUserId', $tmpUserId , 'com_bookingforconnector');
//		}
//
//		return $tmpUserId;
//	}

if ( ! function_exists( 'bfi_get_defaultCurrency' ) ) {
	function bfi_get_defaultCurrency() {
		$tmpDefaultCurrency = BFCHelper::getSession('defaultcurrency', null , 'com_bookingforconnector');
		if(empty($tmpDefaultCurrency)){
			$tmpDefaultCurrency = BFCHelper::getDefaultCurrency();
			BFCHelper::setSession('defaultcurrency', $tmpDefaultCurrency , 'com_bookingforconnector');
		}
		return $tmpDefaultCurrency;
	}
}
if ( ! function_exists( 'bfi_get_currentCurrency' ) ) {
	function bfi_get_currentCurrency() {
		$tmpCurrentCurrency = BFCHelper::getSession('currentcurrency', COM_BOOKINGFORCONNECTOR_CURRENTCURRENCY , 'com_bookingforconnector');
		if(empty($tmpCurrentCurrency)){
			$tmpCurrentCurrency = BFCHelper::getDefaultCurrency();
			BFCHelper::setSession('currentcurrency', $tmpCurrentCurrency , 'com_bookingforconnector');
		}
		return $tmpCurrentCurrency;
	}
}
if ( ! function_exists( 'bfi_set_currentCurrency' ) ) {
	function bfi_set_currentCurrency($selectedCurrency) {
		$tmpCurrentCurrency = BFCHelper::getSession('currentcurrency', null , 'com_bookingforconnector');
		$tmpCurrencyExchanges = BFCHelper::getCurrencyExchanges();
		if (isset($tmpCurrencyExchanges[$selectedCurrency]) ) {
			BFCHelper::setSession('currentcurrency', $selectedCurrency , 'com_bookingforconnector');
			$tmpCurrentCurrency = $selectedCurrency;
		}
		return $tmpCurrentCurrency;
	}
}
if ( ! function_exists( 'bfi_get_currencyExchanges' ) ) {
	function bfi_get_currencyExchanges() {
		$tmpCurrencyExchanges = BFCHelper::getSession('currencyexchanges', null , 'com_bookingforconnector');
		if(empty($tmpCurrencyExchanges)){
			$tmpCurrencyExchanges = BFCHelper::getCurrencyExchanges();
			//BFCHelper::setSession('currencyexchanges', $tmpCurrencyExchanges , 'com_bookingforconnector');
		}
		return $tmpCurrencyExchanges;
	}
}
if ( ! function_exists( 'bfi_get_file_icon' ) ) {
	function bfi_get_file_icon($fileExtension) {
	  $iconFile = '<i class="fa fa-file-o"></i>';
	  if (empty($fileExtension)) {
	      return $iconFile;
	  }
	  $fileExtension = strtolower($fileExtension);
	  // List of official MIME Types: http://www.iana.org/assignments/media-types/media-types.xhtml
	  static $font_awesome_file_icon_classes = array(
		// Images
		'gif' => '<i class="fa fa-file-image-o"></i>',
		'jpeg' => '<i class="fa fa-file-image-o"></i>',
		'jpg' => '<i class="fa fa-file-image-o"></i>',
		'png' => '<i class="fa fa-file-image-o"></i>',
		// Audio
		'mp3' => '<i class="fa fa-file-audio-o"></i>',
		'wma' => '<i class="fa fa-file-audio-o"></i>',
			
		// Video
		'avi' => '<i class="fa fa-file-video-o"></i>',
		'flv' => '<i class="fa fa-file-video-o"></i>',
		'mpg' => '<i class="fa fa-file-video-o"></i>',
		'mpeg' => '<i class="fa fa-file-video-o"></i>',
		// Documents
		'pdf' => '<i class="fa fa-file-pdf-o"></i>',
		'txt' => '<i class="fa fa-file-text-o"></i>',
		'html' => '<i class="fa fa-file-code-o"></i>',
		'json' => '<i class="fa fa-file-code-o"></i>',
		// Archives
		'gzip' => '<i class="fa fa-file-archive-o"></i>',
		'zip' => '<i class="fa fa-file-archive-o"></i>',
	  );

	  if (isset($font_awesome_file_icon_classes[$fileExtension])) {
		$iconFile = $font_awesome_file_icon_classes[$fileExtension];
	  }

	  return $iconFile;
	}
}
