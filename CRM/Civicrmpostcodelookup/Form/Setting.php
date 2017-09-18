<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Civicrmpostcodelookup_Form_Setting extends CRM_Core_Form {
  function buildQuickForm() {

    $settingsStr = CRM_Core_BAO_Setting::getItem('CiviCRM Postcode Lookup', 'api_details');

    $settingsArray = unserialize($settingsStr);

    // Postcode loookup Provider
    $this->add(
      'select', // field type
      'provider', // field name
      ts('Provider'), // field label
      $this->getProviderOptions(), // list of options
      true // is required
    );

    // Server URL
    $this->addElement(
      'text',
      'server',
      ts('Server URL'),
      array('size' => 50),
      true
    );

    // API Key
    $this->addElement(
      'text',
      'api_key',
      ts('API Key'),
      array('size' => 50),
      false
    );

     // Serial Number
    $this->addElement(
      'text',
      'serial_number',
      ts('Serial Number'),
      array('size' => 20),
      false
    );

    // Username
    $this->addElement(
      'text',
      'username',
      ts('Username'),
      array('size' => 20),
      false
    );

     // Password
    $this->addElement(
      'text',
      'password',
      ts('Password'),
      array('size' => 20),
      false
    );

    //MV#4367 Location Types
    $locationTypes = array_flip(CRM_Core_PseudoConstant::get('CRM_Core_DAO_Address', 'location_type_id'));

    $this->addCheckBox('location_type_id',
     ts('Location Types'),
      $locationTypes,
      NULL, NULL, NULL, NULL,
      array('&nbsp;&nbsp;')
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    $this->setDefaults($settingsArray);

    $this->addFormRule( array( 'CRM_Civicrmpostcodelookup_Form_Setting', 'formRule' ) );

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  static function formRule( $values ){

    $errors = array();

    // Server is mandatory fo AFD and CiviPostcode. Server URL is in QAS lib for Experian
    if ($values['provider'] == 'afd' || $values['provider'] == 'civipostcode' || $values['provider'] == 'postcodeanywhere') {
      if (empty($values['server'])) {
        $errors['server'] = ts( "Server URL is mandatory." );
      }
    }

    // Check all mandatory values are entered for AFD
    if ($values['provider'] == 'afd') {
      if (empty($values['serial_number'])) {
        $errors['serial_number'] = ts( "Serial Number is mandatory." );
      }
      if (empty($values['password'])) {
        $errors['password'] = ts( "Password is mandatory." );
      }
    }

    // Check all mandatory values are entered for Civipostcode
    if ($values['provider'] == 'civipostcode') {
      if (empty($values['api_key'])) {
        $errors['api_key'] = ts( "API Key is mandatory." );
      }
    }

    // Check all mandatory values are entered for Experian
    if ($values['provider'] == 'experian') {
      if (empty($values['username'])) {
        $errors['username'] = ts( "Username is mandatory." );
      }
      if (empty($values['password'])) {
        $errors['password'] = ts( "Password is mandatory." );
      }
    }

    // Check all mandatory values are entered for PostcodeAnywhere
    if ($values['provider'] == 'postcodeanywhere') {
      if (empty($values['username'])) {
        $errors['username'] = ts( "Username is mandatory." );
      }
      if (empty($values['api_key'])) {
        $errors['api_key'] = ts( "API Key is mandatory." );
      }
    }

    return $errors;
  }

  function postProcess() {
    $values = $this->exportValues();

    $settingsArray = array();
    $settingsArray['provider'] = $values['provider'];

    // AFD
    if ($values['provider'] =='afd')  {
      $settingsArray['server'] = $values['server'];
      $settingsArray['serial_number'] = $values['serial_number'];
      $settingsArray['password'] = $values['password'];
    }

    // Civipostcode
    if ($values['provider'] =='civipostcode')  {
      $settingsArray['server'] = $values['server'];
      $settingsArray['api_key'] = $values['api_key'];
    }

    // Experian
    if ($values['provider'] =='experian')  {
      $settingsArray['username'] = $values['username'];
      $settingsArray['password'] = $values['password'];
    }

    // PostcodeAnywhere
    if ($values['provider'] =='postcodeanywhere')  {
      $settingsArray['server'] = $values['server'];
      $settingsArray['api_key'] = $values['api_key'];
      $settingsArray['username'] = $values['username'];
    }

    //MV#4367 amend Location Types into settings
    if (!empty($values['location_type_id']))  {
      $settingsArray['location_type_id'] = $values['location_type_id'];
    }
    
    $settingsStr = serialize($settingsArray);

    CRM_Core_BAO_Setting::setItem($settingsStr,
          'CiviCRM Postcode Lookup',
          'api_details'
        );

    $message = "Settings saved.";
    CRM_Core_Session::setStatus($message, 'Postcode Lookup', 'success');
  }

  function getProviderOptions() {
    $options = array(
      '' => ts('- select -'),
    ) + $GLOBALS["providers"];

    return $options;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
