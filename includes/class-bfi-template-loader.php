<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'BFI_Template_Loader' ) ) :
/**
 * Template Loader
 *
 * @class 		BFI_Template
 * @version     2.0.5
 * @package		BookingFor/Classes
 * @category	Class
 * @author 		BookingFor
 */
class BFI_Template_Loader {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		add_filter( 'icl_ls_languages',  array( __CLASS__, 'wpml_ls_filter' ) );
		add_filter( 'pll_the_language_link',  array( __CLASS__, 'pll_the_language_link_filter' ), 10, 2 );
//		add_filter( 'comments_template', array( __CLASS__, 'comments_template_loader' ) );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. bookingfor looks for theme.
	 * overrides in /theme/bookingfor/ by default.
	 *
	 * For beginners, it also looks for a bookingfor.php template first. If the user adds.
	 * this to the theme (containing a bookingfor() inside) this will be used for all.
	 * bookingfor templates.
	 *
	 * @param mixed $template
	 * @return string
	 */
	
	public static function wpml_ls_filter($languages) {
		if( !is_admin() && defined( 'ICL_SITEPRESS_VERSION' ) && !ICL_PLUGIN_INACTIVE ){
			global $sitepress;
			foreach($languages as $lang_code => $language){
				$currUrl = $languages[$lang_code]['url'];
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'resource_id','/','-');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'resource_name','','/');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'merchant_id','/','-');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'merchant_name','','/');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'checkmode','/','/');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'orderid','/','/');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'bfi_layout','','/');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'bfi_id');
				$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'bfi_name');
				$languages[$lang_code]['url']  = $currUrl;
			}
			if($_SERVER["QUERY_STRING"]){
				if(strpos(basename($_SERVER['REQUEST_URI']), $_SERVER["QUERY_STRING"]) !== false){
					foreach($languages as $lang_code => $language){
						$currUrl = $languages[$lang_code]['url']. '?'.$_SERVER["QUERY_STRING"];
						$currUrl = bfi_remove_querystring_var($currUrl,'cultureCode');
						$currUrl = bfi_add_querystring_var($currUrl,'cultureCode', $lang_code);
						$languages[$lang_code]['url']  = $currUrl;
					}
				}
			}

			return $languages;
		}
	}

	public static function pll_the_language_link_filter($url, $slug) {
		if( !is_admin() && defined( 'POLYLANG_VERSION' ) ){
			$currUrl = $url;
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'resource_id','','-');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'resource_name','','/');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'merchant_id','','-');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'merchant_name','','/');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'checkmode','/','/');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'orderid','/','/');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'bfi_layout','','/');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'bfi_id');
			$currUrl = BFI_Template_Loader::insert_query_var($currUrl, 'bfi_name');
			$url = $currUrl;
			if($_SERVER["QUERY_STRING"]){
				if(strpos(basename($_SERVER['REQUEST_URI']), $_SERVER["QUERY_STRING"]) !== false){
						$currUrl = $url . '?'.$_SERVER["QUERY_STRING"];
						$url   = $currUrl;
					}
				}
			}

			return $url;
	}

	public static function insert_query_var($url, $query_var,$prefix ='',$postfix='') {
    if (get_query_var($query_var) != '') {
        $url = $url.$prefix.get_query_var($query_var).$postfix;      
    }
    return $url;
	}

	public static function template_loader( $template ) {
		
		
		$find = array( 'bfi.php' );
		$file = '';

		if (function_exists('is_embed')) {
			if ( is_embed() ) {
				return $template;
			}
		}
		if ( is_single() && get_post_type() == 'merchantlist' ) {

			$file 	= 'merchantslist.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}


		if ( is_page( bfi_get_template_page_id( 'searchavailability' ) )) {
			$file 	= 'search-availability.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'searchonsell' ) )) {
			$file 	= 'search-onsell.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}

		if ( is_page( bfi_get_template_page_id( 'merchantdetails' ) )) {
			$file 	= 'merchantdetails.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'condominiumdetails' ) )) {
			$file 	= 'condominiumdetails.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'accommodationdetails' ) )) {
			$file 	= 'resourcedetails.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'onselldetails' ) )) {
			$file 	= 'onselldetails.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'orderdetails' ) )) {
			$file 	= 'orderdetails.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'payment' ) )) {
			$file 	= 'payment.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		if ( is_page( bfi_get_template_page_id( 'cartdetails' ) )) {
			$file 	= 'cartdetails.php';
			$find[] = $file;
			$find[] = BFI()->template_path() . $file;
		}
		
		if ( $file ) {
			$template       = locate_template( array_unique( $find ) );
			if ( ! $template ) {
				$template = BFI()->plugin_path() . '/templates/' . $file;
			}
		}

		return $template;
	}

}
endif;

BFI_Template_Loader::init();