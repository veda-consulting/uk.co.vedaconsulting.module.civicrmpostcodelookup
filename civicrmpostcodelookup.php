<?php

require_once 'civicrmpostcodelookup.civix.php';

// Postcode lookup providers
// FIXME: Move this list to option values
$GLOBALS["providers"] = array(
      'afd' => 'AFD',
      'civipostcode' => 'CiviPostcode',
      'experian' => 'Experian',
      'postcodeanywhere' => 'PostcodeAnywhere',
      );

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civicrmpostcodelookup_civicrm_config(&$config) {
  $settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Postcode Lookup', 'api_details');
  $settingsArray = unserialize($settingsStr);
  $config->CiviPostCodeLookupProvider = $settingsArray['provider'];

  _civicrmpostcodelookup_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civicrmpostcodelookup_civicrm_xmlMenu(&$files) {
  _civicrmpostcodelookup_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civicrmpostcodelookup_civicrm_install() {

  require_once 'CRM/Core/BAO/Setting.php';
  CRM_Core_BAO_Setting::setItem('',
          'CiviCRM Postcode Lookup',
          'api_details'
        );

  _civicrmpostcodelookup_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civicrmpostcodelookup_civicrm_uninstall() {
  _civicrmpostcodelookup_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civicrmpostcodelookup_civicrm_enable() {
  _civicrmpostcodelookup_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civicrmpostcodelookup_civicrm_disable() {
  _civicrmpostcodelookup_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civicrmpostcodelookup_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civicrmpostcodelookup_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civicrmpostcodelookup_civicrm_managed(&$entities) {
  _civicrmpostcodelookup_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civicrmpostcodelookup_civicrm_caseTypes(&$caseTypes) {
  _civicrmpostcodelookup_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civicrmpostcodelookup_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civicrmpostcodelookup_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Add navigation for Postcode Lookup under "Administer" menu
 *
 * @param $params associated array of navigation menus
 */
function civicrmpostcodelookup_civicrm_navigationMenu( &$params ) {
  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');

  // skip adding menu if there is no administer menu
  if ($administerMenuId) {
    // get the maximum key under adminster menu
    $maxKey = max( array_keys($params[$administerMenuId]['child']));
    $params[$administerMenuId]['child'][$maxKey+1] =  array (
      'attributes' => array (
        'label'      => 'Postcode Lookup',
        'name'       => 'Postcode Lookup',
        'url'        => 'civicrm/postcodelookup/settings?reset=1',
        'permission' => 'administer CiviCRM',
        'operator'   => NULL,
        'separator'  => TRUE,
        'parentID'   => $administerMenuId,
        'navID'      => $maxKey+1,
        'active'     => 1
      )
    );
  }
}

function civicrmpostcodelookup_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Contact_Form_Contact' ||
    $formName == 'CRM_Profile_Form_Edit' ||
    $formName == 'CRM_Event_Form_Registration_Register' ||
    $formName == 'CRM_Contribute_Form_Contribution_Main' ||
    $formName == 'CRM_Event_Form_ManageEvent_Location'
  ) {
    require_once 'CRM/Core/Resources.php';
    CRM_Core_Resources::singleton()
      ->addScriptFile('uk.co.vedaconsulting.module.civicrmpostcodelookup', 'js/jquery.ui.autocomplete.html.js', 110, 'html-header', FALSE)
      ->addStyleFile('uk.co.vedaconsulting.module.civicrmpostcodelookup', 'css/civipostcode.css', 110, 'page-header');
  }
}
