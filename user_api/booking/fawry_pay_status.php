<?php
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {
    $input = json_decode(file_get_contents('php://input'), true); 
    $field_values = [ "res"];
    $data_values = [$input];

    $h = new Estate();
    $check = $h->restateinsertdata_Api($field_values, $data_values, 'payment');
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $field_values = [ "res"];
    $data_values = ["tes the data"];

    $h = new Estate();
    $check = $h->restateinsertdata_Api($field_values, $data_values, 'payment');
  
}

