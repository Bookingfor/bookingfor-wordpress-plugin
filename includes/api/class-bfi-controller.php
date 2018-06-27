<?php
/**
 * Contains the query functions for Bookingfor which alter the front-end post queries and loops
 *
 * @class 		BFI_Controller
 * @version             2.0.5
 * @package		
 * @category	        Class
 * @author 		Bookingfor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Controller' ) ) :
/**
 * BFI_Controller Class.
 */
class BFI_Controller {

	/**
	 * Constructor for the query class. Hooks in methods.
	 *
	 * @access public
	 */
	private $formlabel = null;
	public function __construct() {
		$this->formlabel = COM_BOOKINGFORCONNECTOR_FORM_KEY;
	}
	
	public function handle_request(){ 
 		global $wp; 
		
		$task = isset($_REQUEST['task']) ? $_REQUEST['task'] :null ;

		if(!empty($task)){
			define( "DONOTCACHEPAGE", true ); // Do not cache this page
			if (method_exists($this, $task)){
				$message = $this->$task();
				$simple = isset($_REQUEST['simple']) ? $_REQUEST['simple'] :null ;
				if(!empty($simple)){
					$this->send_text_response($message);  
				}else{
					$this->send_json_response($message);  
				}

			}
		}else{
 			$this->send_response('Method not allowed'); 
		}
 	} 

	protected function send_response($msg){ 
 		$response['message'] = $msg; 
// 		header('content-type: application/json; charset=utf-8'); 
 	    echo json_encode($response)."\n"; 
 	    die();
//		exit; 
 	} 
	protected function send_json_response($msg){ 
// 		header('content-type: application/json; charset=utf-8'); 
 	    echo $msg."\n"; 
 	    die();
//		exit; 
 	} 
	protected function send_text_response($msg){ 
// 		header('content-type: text/plain; charset=utf-8'); 
 	    echo $msg."\n"; 
 	    die();
//		exit; 
 	} 

	protected function searchjson(){
		bfi_setSessionFromSubmittedData();
		$model = new BookingForConnectorModelSearch;
		$items = $model->getItems(true,true);
		echo $items;	
	}
	protected function searchonselljson(){
		$model = new BookingForConnectorModelSearchOnSell;
		$items = $model->getItems(true,true);
		echo $items;	
	}
	
	protected function getMerchantGroups(){
		$return = BFCHelper::getTags("","1"); //getTags
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo json_encode($return);      

	}

	protected function getResourceGroups(){
		$return = BFCHelper::getTags("","4"); //getTags
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo json_encode($return);      

	}
	protected function getDiscountDetails(){
		$ids=$_REQUEST['discountId'];
		$language=$_REQUEST['language'];
		$return = BFCHelper::getDiscountDetails($ids,$language);
		echo $return;	
	}

	protected function GetPhoneByMerchantId(){
		$merchantId = isset($_REQUEST['merchantid']) ? $_REQUEST['merchantid'] :0 ;
		$number = isset($_REQUEST['n']) ? $_REQUEST['n'] : '' ;
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetPhoneByMerchantId($merchantId,$language,$number);
		echo $return;      
	}
	
	function GetServicesByIds(){
		$listsId=isset($_REQUEST['ids']) ? $_REQUEST['ids'] : '' ;  
		$language= isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetServicesByIds($listsId,$language);
		echo json_encode($return);      	
	}
	function GetResourcesByIds(){
		$listsId=isset($_REQUEST['resourcesId']) ? $_REQUEST['resourcesId'] : '' ;  
		$language= isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetResourcesByIds($listsId,$language);
		echo $return;      	
	}
	function GetMerchantsByIds(){
		$listsId=BFCHelper::getVar('merchantsId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getMerchantsByIds($listsId,$language);
		echo $return;      
	
	}
	function GetCondominiumsByIds(){
		$listsId=BFCHelper::getVar('ids');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetCondominiumsByIds($listsId,$language);
		echo $return;      
	
	}
	
	function GetResourcesOnSellByIds(){
		$listsId=BFCHelper::getVar('resourcesId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetResourcesOnSellByIds($listsId,$language);
		echo $return;      	
	}

	function sendRating(){
		global $user;
		$redirect= $_POST['Redirect'];
		$redirecterror =  $_POST['Redirecterror']; 
		$name= $_POST['name'];
		$city= $_POST['city'];
		$typologyid= $_POST['typologyid'];
		$nation= $_POST['nation']; // BFCHelper::getVar('nation');
		$email= $_POST['email'];
		$value1=$_POST['hfvalue1'];
		$value2= $_POST['hfvalue2'];
		$value3= $_POST['hfvalue3'];
		$value4= $_POST['hfvalue4'];
		$value5= $_POST['hfvalue5'];
		$totale= $_POST['hftotale'];
		$pregi= $_POST['pregi'];
		$difetti= $_POST['difetti'];
		$merchantId= $_POST['merchantid'];
		$label= $_POST['label'];
		$user = $user;
		$cultureCode = $GLOBALS['bfi_lang'];
		$userId=null;
		if (!empty($user) && $user->uid != 0) {
			$userId=$user->uid ;
		}
		$checkin= $_POST['checkin'];
		$resourceId= $_POST['resourceId'];
		$hashorder= $_POST['hashorder'];
		$orderId = null;

		$otherData = "optinemail:".(isset($_POST['optinemail'])?$_POST['optinemail']:'')
			."|".BFCHelper::bfi_get_clientdata();

		if (empty($resourceId)){
			$resourceId = null;
		}
		if (!empty($hashorder)){
			$orderId = BFCHelper::decrypt($hashorder);
			if (!is_numeric($orderId))
			{
				$orderId = null;
			}
		}

		$return = BFCHelper::setRating($name, $city, $typologyid, $email, $nation, $merchantId,$value1, $value2, $value3, $value4, $value5, $totale, $pregi, $difetti, $userId, $cultureCode, $checkin, $resourceId, $orderId, $label, $otherData);	
		if ($return < 1){
			$return ="";
			$redirect = $redirecterror;
		}
		if ($return >0 && !empty($redirect)){
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}
			$redirect = $redirect . 'act=Rating';
		}		
//		if ($return < 1){
//			set_transient( 'temporary_message', __( 'There was some issue posting your review. Please try back later.' ), 60*60*12 );
//			$redirect = $redirecterror;
//		}
//		else {
//			set_transient( 'temporary_message', __( 'Your review was succesfully posted.' ), 60*60*12 );		
//		}
		wp_redirect($redirect);
		exit;
	}

	function getLocationZone(){
		$model = new BookingForConnectorModelMerchants;
		$items = $model->getItemsJson(true);
		echo $items;	
	}

	function listDate(){
		$resourceId = $_REQUEST['resourceId'];
		$ci = $_REQUEST['checkin'];
		$checkin = DateTime::createFromFormat('Ymd',$ci,new DateTimeZone('UTC'));
		$return = BFCHelper::getCheckOutDates($resourceId ,$checkin);
		echo $return;      
	}

	function getCompleteRateplansStay(){
//		$language=$_REQUEST['language'];
		$resourceId = isset($_REQUEST['resourceId'])?$_REQUEST['resourceId']:null;
		$ratePlanId=isset($_REQUEST['pricetype'])?$_REQUEST['pricetype']:null;
		$variationPlanId=isset($_REQUEST['variationPlanId'])?$_REQUEST['variationPlanId']:null;
		$selectablePrices=BFCHelper::getStayParam('extras');
		
		$pricetype=isset($_REQUEST['pricetype'])?$_REQUEST['pricetype']:null;

		$selectablePrices=isset($_REQUEST['selectableprices'])?$_REQUEST['selectableprices']:$selectablePrices;
		
		$availabilitytype=isset($_REQUEST['availabilitytype'])?$_REQUEST['availabilitytype']:null; 

		$checkIn =  BFCHelper::getStayParam('checkin',null);
		$duration  =  BFCHelper::getStayParam('duration');
		if ($availabilitytype == 0 || $availabilitytype ==1 ) // product TimePeriod
		{
			$checkIn->setTime(0,0,0);
		}
		if ($availabilitytype ==2 ) // product TimePeriod
		{
			$duration = isset($_REQUEST['duration'])?$_REQUEST['duration']:null; 
			$checkIn = DateTime::createFromFormat("YmdHis", $_REQUEST['CheckInTime'],new DateTimeZone('UTC'));
		}
		
		
		if(!isset($duration)){
			$duration =  BFCHelper::$defaultDaysSpan;
		}
		$packages  =  BFCHelper::getStayParam('packages');
		$paxages =  BFCHelper::getStayParam('paxages',null);
		$return = null;
		$return = BFCHelper::GetCompleteRatePlansStayWP($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId,null,null);
		
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
	}

	function getListCheckInDayPerTimes(){
//		$language=$_REQUEST['language'];
		$resourceId = isset($_REQUEST['resourceId'])?$_REQUEST['resourceId']:null;
		$fromDate=isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:null;
		$limitTotDays=isset($_REQUEST['limitTotDays'])?$_REQUEST['limitTotDays']:null;

		$return = null;
		$return = BFCHelper::GetListCheckInDayPerTimes($resourceId,$fromDate,$limitTotDays);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
	}

	function sendOnSellrequest(){
		$formData = $_POST['form'];

		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$otherData = array();
		if (!empty($formData['resourceId']))  {
				$sStay = array(
							'UnitId' => $formData['resourceId']
						);

				$suggestedStay = new stdClass(); 
				foreach ($sStay as $key => $value) 
				{ 
					$suggestedStay->$key = $value; 
				}
				$otherData["UnitId:"] = "UnitId:" . $formData['resourceId'];
			}
		if (!empty($formData['pageurl']))  {
				$otherData["pageurl:"] = "pageurl:" . $formData['pageurl'];
		}
		if (!empty($formData['title']))  {
				$otherData["title:"] = "title:" . $formData['title'];
		}
		if (!empty($formData['resourceId']))  {
				$otherData["onsellunitid:"] = "onsellunitid:" . $formData['resourceId'];
		}
		if (!empty($formData['accettazione']))  {
				$otherData["accettazione:"] = "accettazione:" . BFCHelper::getOptionsFromSelect($formData,'accettazione');
		}
		
		if (!empty($formData['optinemail']))  {
				$otherData["optinemail:"] = "optinemail:" . BFCHelper::getOptionsFromSelect($formData,'optinemail');
		}

					$otherData["clientdata:"] = BFCHelper::bfi_get_clientdata();
				

		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, implode("|",$otherData), null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}
		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactSale&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				;
		}
//		echo json_encode($return);      
//		$app = JFactory::getApplication();
//		if (empty($redirect)){
//			echo json_encode($return);      
//		}else{
//			$app->redirect($redirect, false);
//		}
//		$app->close();
		wp_redirect($redirect);
		exit;

	}
	function sendInforequest(){
		$formData = $_POST['form'];
//		JPluginHelper::importPlugin('captcha');
//		$dispatcher = JDispatcher::getInstance();
//		$result = $dispatcher->trigger('onCheckAnswer',$formData['recaptcha_response_field']);
//		if(!$result[0]){
//			die('Invalid Captcha Code');
//		}else{


		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		// create otherData (string)
				$otherData = "persone:".BFCHelper::getOptionsFromSelect($formData,'Totpersons')
					."|"."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione')
					."|"."optinemail:".(isset($formData['optinemail'])?$formData['optinemail']:'')
					."|".BFCHelper::bfi_get_clientdata();
		// create SuggestedStay
		$startDate = null;
		$endDate = null;
				if (!empty($formData['CheckIn']) && !empty($formData['CheckOut'])) {
				$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn'],new DateTimeZone('UTC'));
				$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut'],new DateTimeZone('UTC'));
					$sStay = array(
								'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'],new DateTimeZone('UTC'))->format('Y-m-d\TH:i:sO'),
								'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'],new DateTimeZone('UTC'))->format('Y-m-d\TH:i:sO'),
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "CheckIn:" . DateTime::createFromFormat('d/m/Y',$formData['CheckIn'],new DateTimeZone('UTC'))->format('Y-m-d') . "|" ."CheckOut:" . DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d') . "|" . "UnitId:" . $formData['resourceId'];
				}else{
			if (!empty($formData['resourceId']))  {
					$sStay = array(
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "UnitId:" . $formData['resourceId'];
				}
			}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	
//}

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}else{
			
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}

			$redirect = $redirect . 'act=ContactResource&orderid=' . $return->OrderId 
					 . '&merchantid=' . $return->MerchantId 
					 . '&OrderType=' . $return->OrderType 
					 . '&OrderTypeId=' . $return->OrderTypeId
					 . '&RequestType=' . $return->RequestType
					 . '&numAdults=' . $numAdults
					;
				if (!empty($startDate)){
					$redirect = $redirect . '&startDate=' . $startDate->format('Y-m-d')
					 . '&endDate=' . $endDate->format('Y-m-d')
					;
				}
		}
//		echo json_encode($return);      
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
//		$app->close();
		wp_redirect($redirect);
		exit;

	}
	function sendContact(){
		$formData = $_POST['form'];
//		$checkrecaptcha = true;
//		JPluginHelper::importPlugin('captcha');
//		$dispatcher = JDispatcher::getInstance();
//		if (!empty($formData['recaptcha_response_field'])) {
//			$result = $dispatcher->trigger('onCheckAnswer',$formData['recaptcha_response_field']);
//			if(!$result[0]){
//				$checkrecaptcha = false;
//				//die('Invalid Captcha Code');
//			}
//		}
//		if($checkrecaptcha){
		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		// create otherData (string)
		$numAdults = BFCHelper::getOptionsFromSelect($formData,'Totpersons');
		
		$otherData = "persone:".$numAdults
			."|"."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione')
			."|"."optinemail:".(isset($formData['optinemail'])?$formData['optinemail']:'')
			."|".BFCHelper::bfi_get_clientdata();
		// create SuggestedStay
		$startDate = null;
		$endDate = null;

		if ($formData['CheckIn'] != null && $formData['CheckOut'] != null) {
			
			$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn'],new DateTimeZone('UTC'));
			$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut'],new DateTimeZone('UTC'));
			
			$sStay = array(
						'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'],new DateTimeZone('UTC'))->format('Y-m-d\TH:i:sO'),
						'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'],new DateTimeZone('UTC'))->format('Y-m-d\TH:i:sO')
					);

			$suggestedStay = new stdClass(); 
			foreach ($sStay as $key => $value) 
			{ 
				$suggestedStay->$key = $value; 
			}
			$otherData .= "|" . "CheckIn:" . $startDate->format('Y-m-d') ."|" ."CheckOut:" . $endDate->format('Y-m-d');
		}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	
		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}

		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactMerchant&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				 . '&numAdults=' . $numAdults
				;
			if (!empty($startDate)){
				$redirect = $redirect . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				;
			}
		}

//		echo json_encode($return);      
//		$app = JFactory::getApplication();
////		$app->redirect($redirect, false);
//		if (empty($redirect)){
//			echo json_encode($return);      
//		}else{
//			$app->redirect($redirect, false);
//		}
//		$app->close();
//		}
		wp_redirect($redirect);
		exit;
	}

	//new send orders...
	function sendOrders(){
		$formData = $_POST['form'];
		if(empty($formData)){
		}
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		
		$customer = BFCHelper::getCustomerData($formData);

		$userNotes = $formData['note'];
		$cultureCode = $formData['cultureCode'];
		$merchantId = $formData['merchantId'];
		$orderType = $formData['orderType'];
		$label = $formData['label'];
		$label = $this->formlabel;
		$OrderJson = $formData['hdnOrderData'];
		$bookingTypeSelected = $formData['bookingtypeselected'];

//		$suggestedStays =  BFCHelper::CreateOrder($OrderJson,$cultureCode,$bookingTypeSelected);
		$suggestedStays = null;

//		$listCartorderid = array();
//		// recupero tutti i cartorderid per la cancellazione del carrello
//			$orderModel = json_decode(stripslashes($OrderJson));
//            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 )
//            {
//                foreach ($orderModel->Resources as $resource)
//                {
//					if(!empty($resource->CartOrderId)){
//						$listCartorderid[] = $resource->CartOrderId;
//					}
//				}
//			}
//		$listCartorderidstr = implode(",",$listCartorderid);
		
//		$suggestedStay = json_decode(stripslashes($formData['staysuggested']));
//		$req = json_decode(stripslashes($formData['stayrequest']), true);

		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$isgateway = $formData['isgateway'];


//		$otherData = "paxages:". str_replace("]", "" ,str_replace("[", "" , $req['paxages'] ))
//					."|"."checkin_eta_hour:".$formData['checkin_eta_hour'];
		$otherData = "checkin_eta_hour:".$formData['checkin_eta_hour']
					."|"."optinemail:".(isset($formData['optinemail'])?$formData['optinemail']:'')
					."|".BFCHelper::bfi_get_clientdata();
//		$customerDatas = array($customerData);

		$ccdata = null;
//		if (BFCHelper::canAcquireCCData($formData)) { 
		$ccdata = BFCHelper::getCCardData($formData);
		if (!empty($ccdata)) {
			$ccdata = BFCHelper::encrypt(json_encode($ccdata),$label.$customer['Email'] );
		}
//			}

		$orderData = array(
				'customerData' =>  array($customer),
				'suggestedStay' =>$suggestedStays,
				'creditCardData' => $ccdata,
				'otherNoteData' => $otherData,
				'merchantId' => $merchantId,
				'orderType' => $orderType,
				'userNotes' => $userNotes,
				'label' => $label,
				'cultureCode' => $cultureCode
				);

//		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, $ccdata);
		$orderData['pricetype'] = "";
		if(isset($formData['pricetype'])){
			$orderData['pricetype'] = $formData['pricetype'];
		}
		$orderData['label'] = $this->formlabel;;
//		$orderData['checkin_eta_hour'] = $formData['checkin_eta_hour'];
		$orderData['merchantBookingTypeId'] = $formData['bookingtypeselected'];
		$orderData['policyId'] = $formData['policyId'];

		$processOrder = null;
		if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$processOrder=false;
		}

		$tmpUserId = BFCHelper::bfi_get_userId();
		$currCart = BFCHelper::GetCartByExternalUser($tmpUserId, $language, true);

		$order = BFCHelper::setOrder(
                $orderData['customerData'], 
                $orderData['suggestedStay'], 
                $orderData['creditCardData'], 
                $orderData['otherNoteData'], 
                $orderData['merchantId'], 
                $orderData['orderType'], 
                $orderData['userNotes'], 
                $orderData['label'], 
                $orderData['cultureCode'], 
				$processOrder,
				$orderData['pricetype'],
				$orderData['merchantBookingTypeId'],
				$orderData['policyId']
                );
		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		if (!empty($order)){
//			// cancello il carrello
//			BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
//			BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
//			if(!empty($listCartorderidstr)){
//				$tmpUserId = bfi_get_userId();
//				$model = new BookingForConnectorModelOrders;
//				$currCart = $model->DeleteFromCartByExternalUser($tmpUserId, $cultureCode, $listCartorderidstr);
//			}	

			
			if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$payment_page = get_post( bfi_get_page_id( 'payment' ) );
			$url_payment_page = get_permalink( $payment_page->ID );

//			$redirect = $url_payment_page .'?orderId=' . $order->OrderId;
			$redirect = $url_payment_page .'/' . $order->OrderId;

			}else{
				$numAdults = 0;
//				if(isset($suggestedStays->Paxes)){
//					$persons= explode("|", $suggestedStays->Paxes);
//					foreach($persons as $person) {
//						$totper = explode(":", $person);
//						$numAdults += (int)$totper[1];
//					}
//				}

				$act = "OrderResource";
				if(!empty($order->OrderType) && strtolower($order->OrderType) =="b"){
					$act = "QuoteRequest";
				}

				$startDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->StartDate,'Y-m-d'),new DateTimeZone('UTC'));
				$endDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->EndDate,'Y-m-d'),new DateTimeZone('UTC'));
				
				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}

				$redirect = $redirect . 'act=' . $act  
				 . '&orderid=' . $order->OrderId 
				 . (!empty($order->MerchantId )?'&merchantid=' . $order->MerchantId :"") 
				 . '&OrderType=' . $order->OrderType 
				 . '&OrderTypeId=' . $order->OrderTypeId 
				 . '&totalamount=' . ($order->TotalAmount *100)
				 . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				 . (!empty($numAdults)?'&numAdults=' . $numAdults:"")
				;
			}
//			$urlredirpayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
//			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
		}
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
//		$app->close();
		wp_redirect($redirect);
		exit;

	}
	
	function addToCart(){
		//clear session data from request
		BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
		BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
		$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
		$bfiResetCart = (BFCHelper::getVar("bfiResetCart","0"));
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = null;
		if(!empty($OrderJson)){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$model = new BookingForConnectorModelOrders;
//			$currCart = BFCHelper::AddToCartByExternalUser($tmpUserId, $language, $OrderJson, $bfiResetCart);
			$currCart = BFCHelper::AddToCart($tmpUserId, $language, $OrderJson, $bfiResetCart);
			if(!empty($currCart)){
				$return = json_encode($currCart);
			}
		}
		echo $return;      
	}
	function DeleteFromCart(){
		$return = null;
		$CartOrderId = stripslashes(BFCHelper::getVar("bfi_CartOrderId"));
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
		$url_cart_page = get_permalink( $cartdetails_page->ID );
		$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
		if($usessl){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}

		if(!empty($CartOrderId)){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$currCart = BFCHelper::DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId);
			wp_redirect($url_cart_page);
			exit;

//			if(!empty($currCart)){
//				$return = json_encode($currCart);
//			}
//		}else{
//			$resources = BFCHelper::getSession('hdnOrderData', '', 'bfi-cart');
//			if(!empty($resources)){
//				$resources = json_decode($resources,true);
//				unset($resources[$CartOrderId]);
//				$resources = array_values($resources);
//				$currResourcesStr = json_encode($resources);
//				BFCHelper::setSession('hdnOrderData', $currResourcesStr, 'bfi-cart');
//			}
//			wp_redirect($url_cart_page);
//			exit;
		
		}
		$base_url = get_site_url();
		if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
				global $sitepress;
				if($sitepress->get_current_language() != $sitepress->get_default_language()){
					$base_url = "/" .ICL_LANGUAGE_CODE;
				}
		}
		wp_redirect($base_url);
		exit;
	}

	function addDiscountCodesToCart(){		
		$bficoupons = BFCHelper::getVar("bficoupons");
		$language = BFCHelper::getVar("bfilanguage");
//		$redirect = JRoute::_('index.php?option=com_bookingforconnector&view=cart');
		$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
		$url_cart_page = get_permalink( $cartdetails_page->ID );
		$usessl = COM_BOOKINGFORCONNECTOR_USESSL;
		if($usessl){
			$url_cart_page = str_replace( 'http:', 'https:', $url_cart_page );
		}
		if(!empty($bficoupons)){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$currCart = BFCHelper::AddDiscountCodesCartByExternalUser($tmpUserId, $language, $bficoupons);
		}
		wp_redirect($url_cart_page);
		exit;
//		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
//		$app->close();
	}

	public function SearchByText() {
		$return = '[]';
		$term = stripslashes(BFCHelper::getVar("bfi_term"));
		$maxresults = stripslashes(BFCHelper::getVar("bfi_maxresults"));
		$onlyLocations = stripslashes(BFCHelper::getVar("bfi_onlyLocations"));
		if(!isset($maxresults) || empty($maxresults)) {
			$maxresults = 5;
		} else {
			$maxresults = (int)$maxresults;
		}
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		if(!empty($term)) {
			$model = new BookingForConnectorModelSearch;
			$results = $model->SearchResult($term, $language, $maxresults, $onlyLocations);
			if(!empty($results)){
				$return = json_encode($results);
			}
		}
		echo $return;
	}

	function getmarketinfomerchant(){
		$resource_id=BFCHelper::getVar('merchantId');
		$language=BFCHelper::getVar('language');
		$merchant = BFCHelper::getMerchantFromServicebyId($resource_id);
		$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
		$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
		$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
		$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";
		
//		$db   = JFactory::getDBO();
//		$uri  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
//		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//		$itemId = intval($db->loadResult());
//		$currUriMerchant = $uri.'&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchantName);
//		if ($itemId<>0)
//			$currUriMerchant.='&Itemid='.$itemId;
//		$routeMerchant = JRoute::_($currUriMerchant.'&fromsearch=1');

		$merchantdetails_page = get_post( bfi_get_page_id( 'merchantdetails' ) );
		$url_merchant_page = get_permalink( $merchantdetails_page->ID );
		$routeMerchant = $url_merchant_page . $merchant->MerchantId.'-'.BFI()->seoUrl($merchant->Name).'?fromsearch=1';

		$output = '<div class="bfi-mapdetails">
					<div class="bfi-item-title">
						<a href="'.$routeMerchant.'" target="_blank">'.$merchant->Name.'</a> 
					</div>
					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
				</div>';    
		die($output);    	
	}

	function getmarketinforesource(){
		$resource_id=BFCHelper::getVar('resourceId');
		$language=BFCHelper::getVar('language');
		$resource = BFCHelper::GetResourcesById($resource_id);
		$merchant = $resource->Merchant;
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$indirizzo = isset($resource->Address)?$resource->Address:"";
		$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
		$comune = isset($resource->CityName)?$resource->CityName:"";
		$stato = isset($resource->StateName)?$resource->StateName:"";
		
//		$db   = JFactory::getDBO();
//		$uri  = 'index.php?option=com_bookingforconnector&view=resource';
//		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//		$itemId = intval($db->loadResult());
//		$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
//		if ($itemId<>0)
//			$currUriresource.='&Itemid='.$itemId;
//		if (!empty($resource->RateplanId)){
//			 $currUriresource .= "&pricetype=" . $resource->RateplanId;
//		}
//		$resourceRoute = JRoute::_($currUriresource.'&fromsearch=1');

		$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
		$url_resource_page = get_permalink( $accommodationdetails_page->ID );
		$resourceRoute = $url_resource_page . $resource->ResourceId .'-'.BFI()->seoUrl($resourceName).'?fromsearch=1';
		if (!empty($resource->RateplanId)){
			 $resourceRoute .= "&pricetype=" . $resource->RateplanId;
		}

		$output = '<div class="bfi-mapdetails">
					<div class="bfi-item-title">
						<a href="'.$resourceRoute.'" target="_blank">'.$resource->Name.'</a> 
					</div>
					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
				</div>';    
		die($output);    	
	}
	function getmarketinfocondominium(){
		$resource_id=BFCHelper::getVar('resourceId');
		$language=BFCHelper::getVar('language');
		$resource = BFCHelper::getCondominiumFromServicebyId($resource_id);
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$indirizzo = isset($resource->Address)?$resource->Address:"";
		$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
		$comune = isset($resource->CityName)?$resource->CityName:"";
		$stato = isset($resource->StateName)?$resource->StateName:"";
		
//		$db   = JFactory::getDBO();
//		$uri  = 'index.php?option=com_bookingforconnector&view=condominium';
//		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
//		$itemId = intval($db->loadResult());
//		$currUriresource = $uri.'&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName);
//		if ($itemId<>0)
//			$currUriresource.='&Itemid='.$itemId;
//		$resourceRoute = JRoute::_($currUriresource.'&fromsearch=1');

		$condominiumdetails_page = get_post( bfi_get_page_id( 'condominiumdetails' ) );
		$url_condominium_page = get_permalink( $condominiumdetails_page->ID );
		$resourceRoute = $url_condominium_page . $resource->CondominiumId.'-'.BFI()->seoUrl($resourceName).'?fromsearch=1';

		$output = '<div class="bfi-mapdetails">
					<div class="bfi-item-title">
						<a href="'.$resourceRoute.'" target="_blank">'.$resource->Name.'</a> 
					</div>
					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
				</div>';    
		die($output);    	
	}

	function GetAlternativeDates(){
//		$exampleData = '[{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-02-27T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-02-28T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-01T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-02T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-06T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-03-07T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":68.00,"Duration":1},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-02-27T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-02-28T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-01T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-02T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-03-06T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":136.00,"Duration":2},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-02-27T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-02-28T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-01T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-02T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":204.00,"Duration":3},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-02-28T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-01T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-02T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":272.00,"Duration":4},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-01T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-02T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":340.00,"Duration":5},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-02T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":408.00,"Duration":6},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-03T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":476.00,"Duration":7},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-04T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":544.00,"Duration":8},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-05T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-14T00:00:00","PaxAges":"18:0|18:0","BestValue":612.00,"Duration":9},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-06T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-14T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-15T00:00:00","PaxAges":"18:0|18:0","BestValue":680.00,"Duration":10},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-07T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-14T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-15T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-16T00:00:00","PaxAges":"18:0|18:0","BestValue":748.00,"Duration":11},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-08T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-14T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-15T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-16T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-17T00:00:00","PaxAges":"18:0|18:0","BestValue":816.00,"Duration":12},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-09T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-14T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-15T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-16T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-17T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-18T00:00:00","PaxAges":"18:0|18:0","BestValue":884.00,"Duration":13},{"StartDate":"2018-02-24T00:00:00","EndDate":"2018-03-10T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-02-25T00:00:00","EndDate":"2018-03-11T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-02-26T00:00:00","EndDate":"2018-03-12T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-02-27T00:00:00","EndDate":"2018-03-13T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-02-28T00:00:00","EndDate":"2018-03-14T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-03-01T00:00:00","EndDate":"2018-03-15T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-03-02T00:00:00","EndDate":"2018-03-16T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-03-03T00:00:00","EndDate":"2018-03-17T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-03-04T00:00:00","EndDate":"2018-03-18T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14},{"StartDate":"2018-03-05T00:00:00","EndDate":"2018-03-19T00:00:00","PaxAges":"18:0|18:0","BestValue":952.00,"Duration":14}]';
//		$return = $exampleData;
//		$return = json_encode($exampleData);
		$checkin = BFCHelper::getVar('checkin');
		$duration = BFCHelper::getVar('duration');
		$paxes = BFCHelper::getVar('paxes');
		$paxages = BFCHelper::getVar('paxages');
		$merchantId = BFCHelper::getVar('merchantId');
		$condominiumId = BFCHelper::getVar('condominiumId');
		$resourceId = BFCHelper::getVar('resourceId');
		$cultureCode = BFCHelper::getVar('cultureCode');
		$points = BFCHelper::getVar('points');
		$userid = BFCHelper::getVar('userid');
		$tagids = BFCHelper::getVar('tagids');
		$merchantsList = BFCHelper::getVar('merchantsList');
		$availabilityTypes = BFCHelper::getVar('availabilityTypes');
		$itemTypeIds = BFCHelper::getVar('itemTypeIds');
		$domainLabel = BFCHelper::getVar('domainLabel');
		$merchantCategoryIds = BFCHelper::getVar('merchantCategoryIds');
		$masterTypeIds = BFCHelper::getVar('masterTypeIds');
		$merchantTagsIds = BFCHelper::getVar('merchantTagsIds');
		$return = BFCHelper::GetAlternativeDates($checkin, $duration, $paxes, $paxages, $merchantId, $condominiumId, $resourceId, $cultureCode, $points, $userid, $tagids, $merchantsList, $availabilityTypes, $itemTypeIds, $domainLabel, $merchantCategoryIds, $masterTypeIds, $merchantTagsIds);
		echo json_encode($return);      
		// use die() because in IIS $mainframe->close() raise a 500 error 
//		$app = JFactory::getApplication();
//		$app->close();
		//$mainframe->close();
	}
	function bfilogin(){
		$return = "0";
		$email = BFCHelper::getVar('email');
		$password = BFCHelper::getVar('password');
		$twoFactorAuthCode = BFCHelper::getVar('twoFactorAuthCode');
		$deviceCodeAuthCode = BFCHelper::GetTwoFactorCookie();
		$return = BFCHelper::getLoginTwoFactor($email, $password, $twoFactorAuthCode,$deviceCodeAuthCode);		
		echo json_encode($return);      
	}

	function bfilogout(){
//		BFCHelper::DeleteTwoFactorCookie();
		BFCHelper::setSession('bfiUser', null, 'bfi-User');
		$return = "-1";
		echo json_encode($return);      
	}
	function bficookie(){

//echo "<pre>_COOKIE ";
//echo print_r($_COOKIE);
//echo "</pre>";

	}
	function bficurrUser(){
			$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
//echo "<pre>currUser: ";
//echo print_r($currUser);
//echo "</pre>";

	}


}
endif;