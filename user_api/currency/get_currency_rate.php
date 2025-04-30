<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__),2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $currency = isset($_GET['currency']) ? $rstate->real_escape_string($_GET['currency']) : '';

    if ($currency == '') {
        $returnArr    = generateResponse('false', "Currency Key is required", 400);
    } else {

        $sel = $rstate->query("select * from tbl_exchange_rate
")->fetch_assoc();
        $c =  $sel[$currency] ?? 0;
        $date = $sel['UpdateDate']; // Your date from database (e.g., "2023-11-10 14:30:00")
        $today = new DateTime(); // Current date/time
        $updateDate = new DateTime($date);
        
        $interval = $today->diff($updateDate);
        $daysDifference = $interval->days;        
        if ($c == 0 || $daysDifference > 1) {
            $returnArr    = generateResponse('true', "Currency Rate Not Exists!", 200);
        } else {

            $returnArr    = generateResponse('true', "Currency Rate Exists!", 200, array(
                "currency_rate" => $c
            ));
        }
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
