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
   * @see shortcut methods ok, warning, critical
   */
  public function sendResult($check, $status, $output = "") {
    $curl = curl_init("{$this->sensu_url}/results");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $jsonData = json_encode(array(
      'source' => $this->source,
      'name' => $check,
      'output' => $output,
      'status' => $status,
    ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($curl);
    if ($response === FALSE) {
      CRM_Core_Error::createError("Could not push check result for $check to Sensu.", 8000, 'Error');
      CRM_Core_Error::debug_var("sensu_response", curl_getinfo($curl));
    }
    curl_close($curl);
  }

  public function ok($check, $output = "") {
    $this->sendResult($check, static::RESULT_OK, $output);
  }

  public function warning($check, $output = "") {
    $this->sendResult($check, static::RESULT_WARNING, $output);
  }

  public function critical($check, $output = "") {
    $this->sendResult($check, static::RESULT_CRITICAL, $output);
  }
}
