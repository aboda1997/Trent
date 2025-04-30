<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

// API configuration
$apiKey = '666b0eda5bc4bc8ea9fc781d';
$baseCurrency = 'EGP'; // Base currency is Egyptian Pound
$targetCurrencies = ['USD', 'SAR', 'QAR', 'AED', 'EUR', 'IQD', 'JOD', 'KWD', 'OMR'];

// Fetch exchange rates
$exchangeRates = [];
$pass  = true;
foreach ($targetCurrencies as $currency) {
  $url = "https://v6.exchangerate-api.com/v6/{$apiKey}/pair/{$baseCurrency}/{$currency}";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($response, true);

  if ($data && $data['result'] === 'success') {
    $exchangeRates[$currency] = $data['conversion_rate'];
  } else {
    // Log error or use a default value
    $exchangeRates[$currency] = 0;
    $pass = false;
  }

  // Add delay to avoid hitting API rate limits
  sleep(1);
}

// Prepare data for database update
$currentDate = date('Y-m-d H:i:s');
$exchangeRates['UpdateDate'] = $currentDate;

// Update database
try {
  if ($pass) {
    $table = "tbl_exchange_rate";
    $h = new Estate();
    $keys = array_keys($exchangeRates);
    $values = array_values($exchangeRates);

    $check = $h->restateinsertdata_Api_Id($keys , $values, $table);
  } else {
    $table = "tbl_exchange_rate";
    $where = "";
    $h = new Estate();
    $check = $h->restateDeleteData_Api_fav($table, $where);
  }
} catch (Exception $e) {
  // Handle exceptions and return an error response
  $returnArr = generateResponse('false', "An error occurred!", 500, array(
    "error_message" => $e->getMessage()
  ), $e->getFile(),  $e->getLine());
  echo $returnArr;
}
