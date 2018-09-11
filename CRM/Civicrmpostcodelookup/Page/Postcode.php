<?php

require_once 'CRM/Core/Page.php';

abstract class CRM_Civicrmpostcodelookup_Page_Postcode extends CRM_Core_Page {

  /**
   * Get the postcode from the submitted values, with or without space.
   * @param bool $space
   *
   * @return string
   */
  protected static function getPostcode($space = FALSE) {
    $postcode = CRM_Utils_Request::retrieve('term', 'String');
    return self::format($postcode, $space);
  }

  /**
   * Format a UK postcode so it has a space before the last digit, or doesn't if $space is FALSE
   * @param $postcode
   * @param bool $space
   *
   * @return string
   */
  protected static function format($postcode, $space) {
    // Convert to uppercase
    $postcode = strtoupper($postcode);
    // Strip non-alpha characters
    $postcode = preg_replace('/\W/', '', $postcode);
    if (strlen($postcode) > 4) {
      ($space) ? $spacerChar = ' ' : $spacerChar = '';
      return preg_replace('/^(.*)(\d)(.*)/', "$1{$spacerChar}$2$3", $postcode);
    }
    return $postcode;
  }

  /**
   * Is the provided postcode valid?
   * @param $postcode
   *
   * @return bool
   */
  protected static function isValidPostcode($postcode) {
    // Regex provided by UK Gov (https://en.wikipedia.org/wiki/Postcodes_in_the_United_Kingdom)
    $valid = (boolean)preg_match('/^([Gg][Ii][Rr] 0[Aa]{2})|((([A-Za-z][0-9]{1,2})|(([A-Za-z][A-Ha-hJ-Yj-y][0-9]{1,2})|(([A-Za-z][0-9][A-Za-z])|([A-Za-z][A-Ha-hJ-Yj-y][0-9]?[A-Za-z]))))\s?[0-9][A-Za-z]{2})$/', $postcode);
    return $valid;
  }

}
