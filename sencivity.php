<?php

require_once 'sencivity.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function sencivity_civicrm_config(&$config) {
  _sencivity_civix_civicrm_config($config);

  $extensions = CRM_Extension_System::singleton()->getManager()->getStatuses();
  if ($extensions['com.drastikbydesign.stripe'] == CRM_Extension_Manager::STATUS_INSTALLED) {
    $config->fatalErrorHandler = 'sencivity_stripe_fatalHandler';
  }
}

/**
 * Implements hook_civicrm_xmlMenu().
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
 * Publishes a sensu check result reflecting the job execution result.
 * Status OK in case of success, WARNING in case of failure.
 */
function sencivity_civicrm_postJob($job, $params, $result) {
  $client = new CRM_Sencivity_Client();
  $ttl = NULL;
  if ($job->is_active) {
    if (isset($params['sensu_ttl'])) {
      $ttl = $params['sensu_ttl'];
    }
    else if ($job->run_frequency == 'Hourly') {
      $ttl = 4000;
    }
    else if ($job->run_frequency == 'Daily') {
      $ttl = 90000;
    }
  }

  if ($result['is_error']) {
    $output = "Job '$job->name' failed: " . CRM_Utils_Array::value('error_message', $result, 'no error message');
    $client->warning("civicrm_jobs_{$job->api_entity}_{$job->api_action}", $output, $ttl);
  }
  else {
    $output = "Job '$job->name' succeeded with value(s): " . CRM_Utils_Array::value('values', $result, 'no value');
    $client->ok("civicrm_jobs_{$job->api_entity}_{$job->api_action}", $output, $ttl);
  }
}


/**
 * Fatal error handler for Stripe webhook
 */
function sencivity_stripe_fatalHandler($vars) {
  if ($vars['message'] == 'Expected one Contribution but found 0') {
    $trace = $vars['exception']->getTraceAsString();
    if (stripos($trace, "civicrm_invoke('payment', 'ipn', '1')") !== FALSE) {
      $re = '/trxn_id.*(ch_[a-zA-Z0-9]*)/m';
      preg_match_all($re, serialize($vars), $matches, PREG_SET_ORDER, 0);
      $paymentId = $matches[0][1];
      $client = new CRM_Sencivity_Client();
      $client->warning('civicrm_stripe_webhook', "Got stripe error: " . $vars['message']
        . '(Stripe Payment Id: ' . $paymentId . ')');
    }
  }

  // We let CiviCRM handle the error as it normally would
  return FALSE;
}
