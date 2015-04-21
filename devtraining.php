<?php

require_once 'devtraining.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function devtraining_civicrm_config(&$config) {
  _devtraining_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function devtraining_civicrm_xmlMenu(&$files) {
  _devtraining_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function devtraining_civicrm_install() {
  _devtraining_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function devtraining_civicrm_uninstall() {
  _devtraining_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function devtraining_civicrm_enable() {
  _devtraining_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function devtraining_civicrm_disable() {
  _devtraining_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function devtraining_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _devtraining_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function devtraining_civicrm_managed(&$entities) {
  _devtraining_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function devtraining_civicrm_caseTypes(&$caseTypes) {
  _devtraining_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function devtraining_civicrm_angularModules(&$angularModules) {
_devtraining_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function devtraining_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _devtraining_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function devtraining_civicrm_preProcess($formName, &$form) {

}

*/

function devtraining_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
	
	//~ file_put_contents(__DIR__ .'/log',$op.' '.$objectName.' '.print_r($objectRef,true)."\n",FILE_APPEND);
	//~ CRM_Core_Session::setStatus($message, $title);
	
	if (in_array($op,array('create','edit'))&&in_array($objectName,array('Address'))) {
		
		if (empty($objectRef->contact_id)||empty($objectRef->postal_code)) return;
		
		try {
			$cfids = civicrm_api3('CustomGroup', 'getsingle', array(
				'sequential' => 1,
				'name' => "constituent_information",
				'api.CustomField.getvalue' => array(
					'sequential' => 1,
					'return' => 'id',
					'name' => "county",
				),
			));
			if (empty($cfids['api.CustomField.getvalue'])) throw new Exception('county lookup failed');
		} catch (Exception $e) {
			CRM_Core_Error::debug_log_message(
				'com.blackbricksoftware.devtraining - '.$e->getMessage()
			);
			return;
		}

		$county = _devtrainingFetchCountyByPostalCode($objectRef->postal_code);
		if (empty($county)) return;
			
		try {
			$result = civicrm_api3('Contact', 'create', array(
				'id' => $objectRef->contact_id,
				'custom_'.$cfids['api.CustomField.getvalue'] => $county,
			));
		} catch (Exception $e) {
			CRM_Core_Error::debug_log_message(
				'com.blackbricksoftware.devtraining - '.$e->getMessage()
			);
			return;
		}
	}
}

function _devtrainingFetchCountyByPostalCode($postal_code) {
	
	$apikey = 'nk9yg3ek00g4yyw5';
	$url = 'https://www.zipwise.com/webservices/zipinfo.php';
	$querystring = http_build_query(array(
		'key' => $apikey,
		'zip' => $postal_code,
		'format' => 'json',
	));
	
	$httpreturn = CRM_Utils_HttpClient::singleton()->get($url.'?'.$querystring);
	if (CRM_Utils_Array::value(0,$httpreturn) !== CRM_Utils_HttpClient::STATUS_OK) {
		CRM_Core_Error::debug_log_message(
			'com.blackbricksoftware.devtraining - county zipwise lookup failed'
		);
		return false;
	}
	
	$json = json_decode(CRM_Utils_Array::value(1,$httpreturn,false));
	if (empty($json)||!property_exists($json->results,'county')) {
		CRM_Core_Error::debug_log_message(
			'com.blackbricksoftware.devtraining - county was not returned from zipwise'
		);
		return false;
	}
	
	return $json->results->county;
}
