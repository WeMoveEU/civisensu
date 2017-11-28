<?php

require_once 'sencivity.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function sencivity_civicrm_config(&$config) {
  _sencivity_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function sencivity_civicrm_xmlMenu(&$files) {
  _sencivity_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function sencivity_civicrm_install() {
  _sencivity_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function sencivity_civicrm_uninstall() {
  _sencivity_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function sencivity_civicrm_enable() {
  _sencivity_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function sencivity_civicrm_disable() {
  _sencivity_civix_civicrm_disable();
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
function sencivity_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _sencivity_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function sencivity_civicrm_managed(&$entities) {
  _sencivity_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function sencivity_civicrm_caseTypes(&$caseTypes) {
  _sencivity_civix_civicrm_caseTypes($caseTypes);
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
function sencivity_civicrm_angularModules(&$angularModules) {
_sencivity_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function sencivity_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _sencivity_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Publish a sensu check result reflecting the job execution result.
 * Status OK in case of success, WARNING in case of failure.
 */
function sencivity_civicrm_postJob($job, $params, $result) {
  if ($result['is_error']) {
    $status = 1;
    $output = "Job '$job->name' failed: " . CRM_Utils_Array::value('error_message', $result, 'no error message');
  } else {
    $status = 0;
    $output = "Job '$job->name' succeeded with value(s): " . CRM_Utils_Array::value('values', $result, 'no value');
  }

  $curl = curl_init("http://localhost:4567/results");
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $jsonData = json_encode(array(
    'source' => 'sencivity',
    'name' => 'civicrm_jobs',
    'output' => $output,
    'status' => $status,
  ));
  curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);

  $response = curl_exec($curl);
  CRM_Core_Error::debug_var("sensu response", curl_getinfo($curl));
}

