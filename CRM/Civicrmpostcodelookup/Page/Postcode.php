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
    $postcode = CRM_Utils_Request::retrieve('term', 'String', $this, true);
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
    // Strip non-alpha characters
    $postcode = preg_replace('/\W/', '', $postcode);
    if (strlen($postcode) > 4) {
      ($space) ? $spacerChar = ' ' : $spacerChar = '';
      return preg_replace('/^(.*)(\d)(.*)/', "$1{$spacerChar}$2$3", $postcode);
    }
    return $postcode;
  }

}
