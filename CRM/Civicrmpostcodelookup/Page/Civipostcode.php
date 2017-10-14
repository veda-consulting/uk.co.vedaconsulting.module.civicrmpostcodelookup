<?php

require_once 'CRM/Core/Page.php';

class CRM_Civicrmpostcodelookup_Page_Civipostcode extends CRM_Civicrmpostcodelookup_Page_Postcode {

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
		$postcode = self::getPostcode(TRUE);
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
			if ($simpleJSONData->is_error == 1) {
				$addresslist[0]['value'] = '';
				$addresslist[0]['label'] = $simpleJSONData->error;
			} else {
				$addresslist = self::getAddressList($simpleJSONData, $postcode);
			}
		}

		// highlight search results
		//$addresslist = CRM_Civicrmpostcodelookup_Utils::apply_highlight($addresslist, $postcode);

		##Close the JSON source##
		fclose($filetoparse);

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

	private static function getAddressList($simpleJSONData, $postcode) {
		$addressList = array();
		$addressRow = array();
		$AddressListItem = $simpleJSONData->results;
		foreach ($AddressListItem as $key => $addressItem) {
			$addressLineArray = self::formatAddressLines($addressItem, TRUE);
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
		$addressObj = $simpleJSONData->results[0];

		$address = self::formatAddressLines($addressObj);

		##Close the JSON source##
		fclose($filetoparse);

		return $address;
	}

	private static function formatAddressLines($addressObj, $forList = FALSE) {
		if (empty($addressObj)) {
			return;
		}

		// Format address lines based on Royal Mail PAF address assembler (https://github.com/AllenJB/PafUtils)
		require_once 'CRM/PafUtils/Address.php';
		$addressLineObj = new Address();
        $addressLineObj->setUdprn($addressObj->udprn)
        	->setPostCode($addressObj->postcode)
            ->setPostTown($addressObj->post_town)
            ->setDependentLocality($addressObj->dependent_locality)
            ->setDoubleDependentLocality($addressObj->double_dependent_locality)
            ->setThoroughfare($addressObj->thoroughfare_descriptor)
            ->setDependentThoroughfare($addressObj->dependent_thoroughfare_descriptor)
            ->setBuildingNumber($addressObj->building_number)
            ->setBuildingName($addressObj->building_name)
            ->setSubBuildingName($addressObj->sub_building_name)
            ->setPoBox($addressObj->po_box)
            ->setDepartmentName($addressObj->department_name)
            ->setOrganizationName($addressObj->organisation_name)
            ->setPostcodeType($addressObj->postcode_type)
            ->setSuOrganizationIndicator($addressObj->su_organisation_indicator)
            ->setDeliveryPointSuffix($addressObj->delivery_point_suffix);
        $addressLines = $addressLineObj->getAddressLines();

        if ($forList == FALSE) {
			$address = array('id' => $addressObj->id);
		}

		if (!empty($addressLines[0])) {
			$address["street_address"] = $addressLines[0];
		}
		if (!empty($addressLines[1])) {
			$address["supplemental_address_1"] = $addressLines[1];
		}
		if (!empty($addressLines[2])) {
			$address["supplemental_address_2"] = $addressLines[2];
		}
		$address["town"] = (string) $addressObj->post_town;
		$address["postcode"] = (string) $addressObj->postcode;

		return $address;
	}
}
