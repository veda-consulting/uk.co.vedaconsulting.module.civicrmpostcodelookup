<?php

class CRM_Civicrmpostcodelookup_Page_GetAddressIo extends CRM_Civicrmpostcodelookup_Page_Postcode {

  public static function isValidPostcode($postcode) {
    if (in_array($postcode, array('XX200X', 'XX404X', 'XX400X', 'XX401X', 'XX429X', 'XX500X'))) {
      // A getAddressIo test postcode
      return TRUE;
    }
    return parent::isValidPostcode($postcode);
  }
  /*
   * Function to get address list based on a Post code
   */
  public static function search() {
    $postcode = self::getPostcode(FALSE);

    if (!self::isValidPostcode($postcode)) {
      exit;
    }
    $number = CRM_Utils_Request::retrieve('number', 'String', $this, FALSE);
    $apiUrl = self::getAddressIoApiUrl($postcode, $number);
    // get address result from getAddress.io
    $addressData = self::addressAPIResult($apiUrl);

    $addresslist = array();
    if ($addressData['is_error']) {
      $addresslist[0]['value'] = '';
      $addresslist[0]['label'] = CRM_Utils_Array::value('Message', $addressData, 'Error in fetching address');
    } else {
      $addresslist = self::getAddressList($addressData, $postcode);
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

  /*
   * Function to get address details based on the selected address
   */
  public static function getaddress() {
    try {
      $selectedId = CRM_Utils_Request::retrieve('id', 'String', $this, TRUE, NULL, 'REQUEST', TRUE);
    }
    catch (CRM_Core_Exception $e) {
      exit;
    }

    // get postcode & address key from selectedId
    $selectedResult = explode('_', $selectedId);
    $postcode = $selectedResult[0];
    $addressKey = $selectedResult[1];

    $apiUrl = self::getAddressIoApiUrl(self::format($postcode, FALSE));

    // get address result from getAddress.io
    $addressData = self::addressAPIResult($apiUrl);

    $addresslist = array();
    if ($addressData['is_error']) {
      $address = array();
    } else {
      $addressItems = $addressData['addresses'];

      // selected result from the addressItems
      $addressItem = $addressItems[$addressKey];

      $address = self::formatAddressLines($selectedId, $addressItem);
      // Fix me : postcode not returned in the API result, hence using the one from the selected ID
      $address['postcode'] = $postcode;
    }

    $response = array(
      'address' => $address
    );

    echo json_encode($response);
    exit;
  }

  /*
   * Function to get the API URL
   */
  private static function getAddressIoApiUrl($postcode = NULL, $number = NULL) {
    #################
    #API settings
    #################
    $settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Postcode Lookup', 'api_details');
    $settingsArray = unserialize($settingsStr);

    $servertarget = $settingsArray['server'];

    // https://api.getAddress.io/find/{postcode}/{house}
    $servertarget = $servertarget . "/find";

    // search by postcode
    if ($postcode && !empty($postcode)) {
      $servertarget = $servertarget . "/" . $postcode;

      // search by house number
      if ($number && !empty($number)) {
        $servertarget = $servertarget . "/" . $number;
      }
    }

    $apiKey = $settingsArray['api_key'];

    $querystring = "api-key=$apiKey";
    return $servertarget ."?" . $querystring;
  }

  /**
   * Function to get Address result from getAddress.io
   *
   * @param $apiUrl
   *
   * @return array
   */
  private static function addressAPIResult($apiUrl) {
    $addressData = array();

    if (empty($apiUrl)) {
      $addressData['is_error'] = 1;
      CRM_Core_Error::debug_var('apiURL empty in get addressAPIResult ', ' ');
      return $addressData;
    }

    // Get the Address Data
    $curlSession = curl_init();
    curl_setopt_array($curlSession, array(
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $apiUrl,
      CURLOPT_USERAGENT => 'CiviCRM'
    ));

    $result = curl_exec($curlSession);
    $header = curl_getinfo($curlSession);

    $curlError['code'] = curl_errno($curlSession);
    $curlError['message'] = curl_error($curlSession);
    $curlError['httpCode'] = $header['http_code'];
    curl_close($curlSession);

    if ($header['http_code'] !== 200) {
      $addressData['is_error'] = 1;
      switch ($header['http_code']) {
        case 404:
          $addressData['Message'] = 'No addresses found';
          break;
        case 400:
          $addressData['Message'] = 'Invalid postcode';
          break;
        case 401:
          $addressData['Message'] = 'Invalid API Key';
          break;
        case 429:
          $addressData['Message'] = 'Too many requests';
          break;
        case 500:
          $addressData['Message'] = 'Server Error';
          break;
      }
    }
    elseif (curl_errno($curlSession)) {
      // Log & return error
      $addressData['is_error'] = 1;
      Civi::log()->debug('GetAddressIo cURL error: ' . print_r($curlError, TRUE));
      $addressData['Message'] = 'Unknown Error';
    }
    else {
      $resultObject = json_decode($result);
      $addressData = (array)$resultObject;
      $addressData['is_error'] = 0;
    }
    return $addressData;
  }

  /**
   * Format the list of found addresses
   *
   * @param $addressData
   * @param $postcode Full postcode, without space
   *
   * @return array
   */
  private static function getAddressList($addressData, $postcode) {
    $addressList = array();
    $addressRow = array();

    // return, if adddressData/postcode is empty
    if (empty($addressData) || empty($postcode)) {
      $addressRow["id"] = '';
      $addressRow["value"] = '';
      $addressRow["label"] = 'Postcode Not Found';
      array_push($addressList, $addressRow);
      return $addressList;
    }
    $postcode = self::format($postcode, TRUE);
    $AddressListItem = $addressData['addresses'];
    foreach ($AddressListItem as $key => $addressItem) {

      // FIX me : There is no address id found in th API, hence assigning combination of postcode & arrayresultID as rowId inorder to get the selected address later
      $addressId = $postcode . '_' . $key;

      $addressLineArray = self::formatAddressLines($addressId, $addressItem, TRUE);
      $addressLineArray['postcode'] = $postcode;

      $addressRow["id"] = $addressId;
      $addressRow["value"] = $postcode;
      $addressRow["label"] = @implode(', ', $addressLineArray);;
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

  private static function formatAddressLines($addressId, $addressItem, $forList = FALSE) {

    if (empty($addressItem)) {
      return;
    }

    $addressLines = explode(', ', $addressItem);

    if ($forList == FALSE) {
      $address = array('id' => $addressId);
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
    if (!empty($addressLines[5])) {
      $address["town"] = $addressLines[5];
    }

    // Get state/county
    $states = CRM_Core_PseudoConstant::stateProvince();

    $address["state_province_id"] = '';
    if (!empty($addressLines[6])) {

      $stateId = array_search($addressLines[6], $states);

      if ($stateId) {
        if ($forList) {
          // Display actual state name in selection list
          $address['state_province_id'] = $addressLines[6];
        }
        else {
          // Use state ID when returning to fill in details (via a select2)
          $address["state_province_id"] = $stateId;
        }
      }
    }

    return $address;
  }

}