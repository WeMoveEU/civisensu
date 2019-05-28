<?php

/**
 * This class is used to call the Sensu API
 */
class CRM_Sencivity_Client {

  const RESULT_OK = 0;
  const RESULT_WARNING = 1;
  const RESULT_CRITICAL = 2;

  private $sensu_url;
  private $sensu_client;

  public function __construct() {
    $this->sensu_url = Civi::settings()->get('sensu_url');
    $this->source = Civi::settings()->get('sensu_client');
  }

  /**
   * Sends a check result
   * @param $check: string - name of the check
   * @param $status: int - check status, see RESULT_* constants
   * @param $output: string - optional description of the result
   * @param $ttl: int - optional number of seconds before which this check should send a result again
   * @see shortcut methods ok, warning, critical
   */
  public function sendResult($check, $status, $output = "", $ttl = NULL) {
    $curl = curl_init("{$this->sensu_url}/results");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $jsonData = array(
      'source' => $this->source,
      'name' => $check,
      'output' => $output,
      'status' => $status,
    );
    if ($ttl) {
      $jsonData['ttl'] = intval($ttl);
    }
    $jsonData = json_encode($jsonData);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($curl);
    if ($response === FALSE) {
      CRM_Core_Error::createError("Could not push check result for $check to Sensu.", 8000, 'Error');
      CRM_Core_Error::debug_log_message("Could not push check result for $check to Sensu.");
      CRM_Core_Error::debug_var("sensu_response", curl_getinfo($curl));
    }
    curl_close($curl);
  }

  public function ok($check, $output = "", $ttl = NULL) {
    $this->sendResult($check, static::RESULT_OK, $output, $ttl);
  }

  public function warning($check, $output = "", $ttl = NULL) {
    $this->sendResult($check, static::RESULT_WARNING, $output, $ttl);
  }

  public function critical($check, $output = "", $ttl = NULL) {
    $this->sendResult($check, static::RESULT_CRITICAL, $output, $ttl);
  }
}
