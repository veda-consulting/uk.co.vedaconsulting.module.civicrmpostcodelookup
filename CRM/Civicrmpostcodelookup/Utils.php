<?php

require_once 'CRM/Core/Page.php';

class CRM_Civicrmpostcodelookup_Utils {
  /**
 * mb_stripos all occurences
 * based on http://www.php.net/manual/en/function.strpos.php#87061
 *
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */
  public static function mb_stripos_all($haystack, $needle) {

    $s = 0;
    $i = 0;

    while(is_integer($i)) {

      $i = mb_stripos($haystack, $needle, $s);

      if(is_integer($i)) {
        $aStrPos[] = $i;
        $s = $i + mb_strlen($needle);
      }
    }

    if(isset($aStrPos)) {
      return $aStrPos;
    } else {
      return false;
    }
  }

  /**
   * Apply highlight to row label
   *
   * @param string $a_json json data
   * @param array $parts strings to search
   * @return array
   */
  public static function apply_highlight($a_json, $parts) {

    $p = count($parts);
    $rows = count($a_json);

    for($row = 0; $row < $rows; $row++) {

      $label = $a_json[$row]["label"];
      $a_label_match = array();

      for($i = 0; $i < $p; $i++) {

        $part_len = mb_strlen($parts[$i]);
        $a_match_start = self::mb_stripos_all($label, $parts[$i]);

        foreach($a_match_start as $part_pos) {

          $overlap = false;
          foreach($a_label_match as $pos => $len) {
            if($part_pos - $pos >= 0 && $part_pos - $pos < $len) {
              $overlap = true;
              break;
            }
          }
          if(!$overlap) {
            $a_label_match[$part_pos] = $part_len;
          }

        }

      }

      if(count($a_label_match) > 0) {
        ksort($a_label_match);

        $label_highlight = '';
        $start = 0;
        $label_len = mb_strlen($label);

        foreach($a_label_match as $pos => $len) {
          if($pos - $start > 0) {
            $no_highlight = mb_substr($label, $start, $pos - $start);
            $label_highlight .= $no_highlight;
          }
          $highlight = '<span class="hl_results">' . mb_substr($label, $pos, $len) . '</span>';
          $label_highlight .= $highlight;
          $start = $pos + $len;
        }

        if($label_len - $start > 0) {
          $no_highlight = mb_substr($label, $start);
          $label_highlight .= $no_highlight;
        }

        $a_json[$row]["label"] = $label_highlight;
      }

    }
    return $a_json;
  }

  /*
   * Get CiviCRM version using SQL
   * Using function to get version is not compatible with all versions
   */
  public static function getCiviVersion() {
    $sql = "SELECT version FROM civicrm_domain";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $dao->fetch();
    return $dao->version;
  }
}
