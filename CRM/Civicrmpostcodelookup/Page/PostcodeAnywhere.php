<?php

require_once 'CRM/Core/Page.php';

class CRM_Civicrmpostcodelookup_Page_PostcodeAnywhere extends CRM_Civicrmpostcodelookup_Page_Postcode {

	/*
	 * Function to get the Server URL and login credentials
	 */
	public static function getPostcodeAnywhereCredentials($action = 1) {
		#################
		#Server settings
		#################
		$settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Postcode Lookup', 'api_details');
  	$settingsArray = unserialize($settingsStr);

		$servertarget = $settingsArray['server'];

		// Action : '1' - Address List, '2' - Address Lookup
		switch ($action) {
			case 1:
			  $servertarget = $servertarget . "/PostcodeAnywhere/Interactive/Find/v1.10/xmla.ws";
			  break;

			case 2:
			  $servertarget = $servertarget . "/PostcodeAnywhere/Interactive/RetrieveById/v1.30/xmla.ws";
			  break;

			default:
			  $servertarget = $servertarget . "/PostcodeAnywhere/Interactive/Find/v1.10/xmla.ws";
		}

		$apiKey = urlencode($settingsArray['api_key']);
		$username = urlencode($settingsArray['username']);

		$querystring = "Key=$apiKey&UserName=$username";
		return $servertarget ."?" . $querystring;
	}

	/*
	 * Function to get address list based on a Post code
	 */
	public static function search() {
	  // PostcodeAnywhere API works with postcodes when they have a space and when they don't.
		$postcode = self::getPostcode();

		$querystring = self::getPostcodeAnywhereCredentials(1);
		$querystring = $querystring . "&SearchTerm=" . urlencode($postcode);

		//Make the request to Postcode Anywhere and parse the XML returned
		$simpleXMLData = simplexml_load_file($querystring);

		if (!empty($simpleXMLData)) {
			$addresslist = self::getAddressList($simpleXMLData, $postcode);
		}

		// Check CiviCRM version & return result as appropriate
		$civiVersion = CRM_Civicrmpostcodelookup_Utils::getCiviVersion();
		if ($civiVersion < 4.5) {
			foreach ($addresslist as $key => $val) {
        echo "{$val['label']}|{$val['id']}\n";
      }
		} else {
			echo json_encode($addresslist);
		}
		exit;
	}

	private static function getAddressList($simpleXMLData, $postcode) {
		$addressList = array();
		$addressRow = array();
		$AddressListItem = (array) $simpleXMLData->Rows;
		$AddressListItems = $AddressListItem['Row'];

		foreach ($AddressListItems as $key => $addressItem) {
			$addressItemArray = (array) $addressItem;
			$addressRow["id"] = (string) $addressItemArray['@attributes']['Id'];
		  $addressRow["value"] = $postcode;
		  $addressRow["label"] = $addressItemArray['@attributes']['StreetAddress'].', '.$addressItemArray['@attributes']['Place'];
		  array_push($addressList, $addressRow);

			/*$addressItemArray = (array) $addressItem;
			$addressList['items'][] = array('id' => (string) $addressItemArray['@attributes']['Id'], 'label' => (string) $addressItemArray['@attributes']['StreetAddress'].', '.$addressItemArray['@attributes']['Place']);*/
		}

		if (empty($addressList)) {
			$addressRow["id"] = '';
		  $addressRow["value"] = '';
		  $addressRow["label"] = 'Error: Postcode Not Found';
		  array_push($addressList, $addressRow);
		}

		return $addressList;
	}

	/*
	 * Function to get address details based on the PostcodeAnywhere addressid/postkey
	 */
	public static function getaddress() {
		$moniker = CRM_Utils_Request::retrieve('id', 'String', $this, true);

		$address = self::getAddressByMoniker($moniker);
		$response = array(
			'address' => $address
		);

		echo json_encode($response);
		exit;
	}

	private static function getAddressByMoniker($moniker) {

		// Get state/county
		$states = CRM_Core_PseudoConstant::stateProvince();

		$querystring = self::getPostcodeAnywhereCredentials(2);
		$querystring = $querystring . "&Id=" . urlencode($moniker);

		//Make the request to Postcode Anywhere and parse the XML returned
		$simpleXMLData = simplexml_load_file($querystring);

		$address = array('id' => $moniker);
		$addressItemRow = (array) $simpleXMLData->Rows;
		$addressItem = (array) $addressItemRow['Row'];

		$addressLineArray[] = $addressItem['@attributes']['Company'];
		$addressLineArray[] = $addressItem['@attributes']['BuildingName'];
		$addressLineArray[] = $addressItem['@attributes']['BuildingNumber'];
		$addressLineArray[] = $addressItem['@attributes']['PrimaryStreet'];
		$addressLineArray = array_filter($addressLineArray);
		$address["street_address"] = @implode(', ', $addressLineArray);

		$address["supplemental_address_1"] = $addressItem['@attributes']['SecondaryStreet'];
		$address["supplemental_address_2"] = $addressItem['@attributes']['DependentLocality'];

		$address["town"] = $addressItem['@attributes']['PostTown'];

		$address["postcode"] = $addressItem['@attributes']['Postcode'];

		$address["state_province_id"] = '';
		if ($stateId = array_search($addressItem['@attributes']['County'], $states)) {
			$address["state_province_id"] = $stateId;
		}

		return $address;
	}
}
