<?php

require_once 'CRM/Core/Page.php';

class CRM_Civicrmpostcodelookup_Page_Civipostcode extends CRM_Core_Page {

	/*
	 * Function to get the Server URL and login credentials
	 */
	public static function getCivipostcodeCredentials($action = 1) {
		#################
		#Server settings
		#################
		$settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Postcode Lookup', 'api_details');
  	$settingsArray = unserialize($settingsStr);

		$servertarget = $settingsArray['server'];

		// Action : '1' - Address List, '2' - Address Lookup
		switch ($action) {
			case 1:
			  $servertarget = $servertarget . "/lookup/v1";
			  break;

			case 2:
			  $servertarget = $servertarget . "/getaddress/v1";
			  break;

			default:
			  $servertarget = $servertarget . "/lookup/v1";
		}

		$apiKey = $settingsArray['api_key'];

		$querystring = "key=$apiKey";
		return $servertarget ."?" . $querystring;
	}

	/*
	 * Function to get address list based on a Post code
	 */
	public static function search() {
		$postcode = CRM_Utils_Request::retrieve('term', 'String', $this, true);
		$number = CRM_Utils_Request::retrieve('number', 'String', $this, false);

		$querystring = self::getCivipostcodeCredentials(1);
		$querystring = $querystring . "&postcode=" . urlencode($postcode) . "&property=" . $number;

		###############
		#File Handling
		###############

		##Open the JSON Document##
		$filetoparse = fopen("$querystring","r") or die("Error reading JSON data.");
		$data = stream_get_contents($filetoparse);
		$simpleJSONData = json_decode($data);

		if (!empty($simpleJSONData)) {
			$addresslist = self::getAddressList($simpleJSONData, $postcode);
		}

		// highlight search results
		//$addresslist = CRM_Civicrmpostcodelookup_Utils::apply_highlight($addresslist, $postcode);

		##Close the JSON source##
		fclose($filetoparse);

		$config = CRM_Core_Config::singleton();
		if ($config->civiVersion < 4.5) {
			foreach ($addresslist as $key => $val) {
        echo "{$val['label']}|{$val['id']}\n";
      }
		} else {
			echo json_encode($addresslist);
		}
		exit;
	}

	private static function getAddressList($simpleJSONData, $postcode) {
		$addressList = array();
		$addressRow = array();
		$AddressListItem = $simpleJSONData->results;
		foreach ($AddressListItem as $key => $addressItem) {
			$addressLineArray = array();
			$addressLineArray[] = $addressItem->building_number;
			$addressLineArray[] = $addressItem->organisation_name;
			$addressLineArray[] = $addressItem->building_name;
			$addressLineArray[] = $addressItem->sub_building_name;
			$addressLineArray[] = $addressItem->thoroughfare_descriptor;
			$addressLineArray[] = $addressItem->post_town;
			$addressLineArray[] = $addressItem->postcode;

			$addressLineArray = array_filter($addressLineArray);

			$addressRow["id"] = (string) $addressItem->id;
		  $addressRow["value"] = $postcode;
		  $addressRow["label"] = @implode(', ', $addressLineArray);
		  array_push($addressList, $addressRow);
		}

		if (empty($addressList)) {
			$addressRow["id"] = '';
		  $addressRow["value"] = '';
		  $addressRow["label"] = 'Postcode Not Found';
		  array_push($addressList, $addressRow);
		}

		return $addressList;
	}

	/*
	 * Function to get address details based on the Civipostcode address id
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
		$querystring = self::getCivipostcodeCredentials(2);
		$querystring = $querystring . "&id=" . urlencode($moniker);

		###############
		#File Handling
		###############

		##Open the JSON Document##
		$filetoparse = fopen("$querystring","r") or die("Error reading JSON data.");
		$data = stream_get_contents($filetoparse);
		$simpleJSONData = json_decode($data);

		$address = array('id' => $moniker);
		$addressItem = $simpleJSONData->results[0];

		$addressLineArray = $addressLine1Array = array();


		//$addressLineArray[] = (string) $addressItem->organisation_name;
		//$addressLineArray[] = (string) $addressItem->building_name;

		if (!empty($addressItem->organisation_name)) {
			$addressLineArray[] = (string) $addressItem->organisation_name;
		} else {
			$addressLineArray[] = (string) $addressItem->building_number;
			$addressLineArray[] = (string) $addressItem->thoroughfare_descriptor;
		}

		$addressLineArray = array_filter($addressLineArray);
		$address["street"] = @implode(' ', $addressLineArray);

		//$addressLine1Array[] = (string) $addressItem->sub_building_name;
		if (!empty($addressItem->organisation_name)) {
			$addressLine1Array[] = (string) $addressItem->building_number;
			$addressLine1Array[] = (string) $addressItem->building_name;
			$addressLine1Array[] = (string) $addressItem->thoroughfare_descriptor;
		}
		$addressLine1Array = array_filter($addressLine1Array);
		$address["locality"] = @implode(' ', $addressLine1Array);

		$address["town"] = (string) $addressItem->post_town;
		$address["postcode"] = (string) $addressItem->postcode;

		##Close the JSON source##
		fclose($filetoparse);

		return $address;
	}
}
