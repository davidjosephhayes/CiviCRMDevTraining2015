<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Devtraining_Form_Zipwise extends CRM_Core_Form {

  function setDefaultValues() {
    $defaults = array(
      'zipwise_apikey' => CRM_Core_BAO_Setting::getItem('Zipwise', 'apikey'),
    );
    return $defaults;
   }
	
  function buildQuickForm() {
	
    // add form elements
    $this->add(
      'text', // field type
      'zipwise_apikey', // field name
      ts('Zipwise API Key'), // field label
      '', // list of options
      true // is required
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    
    CRM_Core_BAO_Setting::setItem($values['zipwise_apikey'], 'Zipwise', 'apikey');
    
    CRM_Core_Session::setStatus(ts('Api Key set: "%1"', array(
      1 => $values['zipwise_apikey'],
    )));
    parent::postProcess();
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
