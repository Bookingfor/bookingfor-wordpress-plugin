<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingForConnectorModelCriteo Model
 */

if ( ! class_exists( 'BookingForConnectorModelCriteo' ) ) :
class BookingForConnectorModelCriteo
{
	private $helper = null;
	private $GetCriteoConfiguration = null;

	public function __construct($config = array())
	{
		$ws_url = COM_BOOKINGFORCONNECTOR_WSURL;
		$api_key = COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->helper = new wsQueryHelper($ws_url, $api_key);
		$this->GetCriteoConfiguration = '/GetCriteoConfiguration';
	}

	public function getCriteoConfiguration($pagetype = 0, $merchantsList = array(), $orderId = null)
	{
		
//		$language = JFactory::getLanguage()->getTag();
		$language = $GLOBALS['bfi_lang'];
		$current_page_URL = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on") ? "https://" : "http://";
		$current_page_URL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

		$options = array(
				'path' => $this->GetCriteoConfiguration,
				'data' => array(
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json',
					'pagetype' => $pagetype,
					'callerUrl' => BFCHelper::getQuotedString($current_page_URL),
					'merchantsList' => BFCHelper::getQuotedString(join(',', $merchantsList))
				)
		);
		if(isset($orderId) && !empty($orderId)) {
			$options["data"]["orderId"] = $orderId;
		}
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results;
			}elseif(!empty($res->d)){
				$return = json_decode($res->d->GetCriteoConfiguration);
			}
		}
		return $return;
	}
}
endif;