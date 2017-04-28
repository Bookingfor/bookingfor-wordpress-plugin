<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFormHelper' ) ) :

class RSFormHelper {

	public static function injectValues($form, $arr) {
		// Create a DOM object
		$html = new simple_html_dom();
		
		// Load HTML from a string
		$html->load($form,true,false);
		
		return self::replaceValues($arr, $html);
	}
	
	private static function replaceValues($arr, $html) // input single array and html object
	{
		foreach ($arr as $key => $value) { // loop through array elements
			// setup the search string that finds an input element with the id attribute equal to the key
			$findstring = "input[id=".$key."]";
				 
			// find and return the first object that matches the search
			$e = $html->find($findstring, 0);
			
			if ($e == null) {
				$e = $html->find("textarea[id=".$key."]", 0);
			}
				 
			// find the value attribute within the same element and insert the new value
			$e->value = $value;
		}
			 
		// output the updated html object
		return $html->save();
	}
	
	public static function checkOrderData($formData, $invalid = array(), $ccDataPrefix = 'cc_') {
		if ($formData == null) {
			$formData = $_POST['form'];
		}		
		if (self::canAcquireCCData($formData)) {
			$prefix = $ccDataPrefix;
			foreach ($formData as $key => $value) { // for each field
				if (substr($key, 0, strlen($prefix)) === $prefix) { // if it is a field we are searching for (prefixed)
					if ($value=='') { // if the value for the field is empty
						$invalid[] = RSFormProHelper::getComponentId($key); // add it to the invalid array
					}
				}
			}
		}
	}
	
	public static function canAcquireCCData($formData) {
		if ($formData == null) {
			$formData = $_POST['form'];
		}		
		
		if(!empty($formData['bookingType'])){
			$bt = $formData['bookingType'];
			if (is_array($bt)) { // if is an array (because it is sent using a select)
				$bt = $bt[0]; // keep only the first value
			}
			if ($bt != '') { // need to check for acquire cc data
				$btData = explode(':',$bt); // data is sent like 'ID:acquireccdata' -> '9:1' or '9:0' or '9:' (where zero is replaced by an empty char)
				if (count($btData) > 1) { // we have more than one value so data sent is correct
					if ($btData[1] != '') { // need to set mandatory for field credit card prefixed with 'cc_' (or other supplied prefix)
						return true;
					}
				}
			}
		}
		return false;
	}
	
	public static function getCCardData($formData) {
		if ($formData == null) {
			$formData = $_POST['form'];
		}
		
		$ccData = array(
				'Type' => self::getOptionsFromSelect($formData,'cc_circuito'),
				'TypeId' => self::getOptionsFromSelect($formData,'cc_circuito'),
				'Number' => $formData['cc_numero'],
				'Name' => $formData['cc_titolare'],
				'ExpiryMonth' => $formData['cc_mese'],
				'ExpiryYear' => $formData['cc_anno']
		);
		
		return $ccData;
	}
	
	public static function getCustomerData($formData) {
		if ($formData == null) {
			$formData = $_POST['form'];
		}

				
				$Firstname = isset($formData['Name'])?$formData['Name']:''; //
				$Lastname = isset($formData['Surname'])?$formData['Surname']:''; // => $formData['Surname'],
				$Email = isset($formData['Email'])?$formData['Email']:''; // => $formData['Email'],
				$Address = isset($formData['Address'])?$formData['Address']:''; // => $formData['Address'],
				$Zip = isset($formData['Cap'])?$formData['Cap']:''; // => $formData['Cap'],
				$City = isset($formData['City'])?$formData['City']:''; // => $formData['City'],
				$Country = isset($formData['Provincia'])?$formData['Provincia']:''; // => $formData['Provincia'],
				$Nation = isset($formData['Nation'])?self::getOptionsFromSelect($formData, 'Nation'):''; // => self::getOptionsFromSelect($formData, 'Nation'),
				$Phone = isset($formData['Phone'])?$formData['Phone']:''; // => $formData['Phone'],
				$Fax = isset($formData['Fax'])?$formData['Fax']:''; // => $formData['Fax'],
				$VatCode = isset($formData['VatCode'])?$formData['VatCode']:''; // => $formData['VatCode'],
				$Culture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
				$UserCulture = isset($formData['Culture'])?self::getOptionsFromSelect($formData, 'Culture'):''; // => self::getOptionsFromSelect($formData, 'Culture'),
				$Culture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$Culture; // => self::getOptionsFromSelect($formData, 'Culture'),
				$UserCulture = isset($formData['cultureCode'])?self::getOptionsFromSelect($formData, 'cultureCode'):$UserCulture; // => self::getOptionsFromSelect($formData, 'Culture'),
				$gender = isset($formData['Gender'])?self::getOptionsFromSelect($formData, 'Gender'):'';

		$customerData = array(
				'Firstname' => $Firstname,
				'Lastname' => $Lastname,
				'Email' => $Email,
				'Address' => $Address,
				'Zip' => $Zip,
				'City' => $City,
				'Country' => $Country,
				'Nation' => $Nation,
				'Phone' => $Phone,
				'Fax' => $Fax,
				'VatCode' => $VatCode,
				'Culture' => $Culture,
				'UserCulture' => $UserCulture,
				'BirthDate' => isset($formData['Birthday']) ? DateTime::createFromFormat('d/m/Y', $formData['Birthday'])->format("Y-m-d"): null,
				'Gender' => $gender,
		);
				
		return $customerData;
	}
	
	public static function getOptionsFromSelect($formData, $str){
		if ($formData == null) {
			$formData = $_POST['form'];
		}

		$aStr = isset($formData[$str])?$formData[$str]:null;
		if(isset($aStr))
		{
			if (!is_array($aStr)) return $aStr;
			$nStr = count($aStr);
			if ($nStr==1){
				return $aStr[0];
			}else
			{
				return implode($aStr, ',');
			}
		}
		return '';
	}
		
}
endif;