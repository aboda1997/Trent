<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';
require  'get_pay_status.php';

header('Content-Type: application/json');
try {


    $merchant_ref_number = isset($_GET['merchant_ref_number']) ? $_GET['merchant_ref_number'] : null;
    $item_id = isset($_GET['item_id']) ? $_GET['item_id'] : 0;
    $final_total = isset($_GET['final_total']) ? $_GET['final_total'] : null;
    $non_completed_data = $rstate->query("select id from tbl_non_completed where id='" .  $item_id . "'")->num_rows;
    $book_data = $rstate->query("select id from tbl_book where item_id= '" .  $item_id . "'")->num_rows;

    if ($final_total == null) {
        $returnArr = generateResponse('false', 'you must enter the total paid value', 400);
    } else if ($item_id == 0) {
        $returnArr = generateResponse('false', 'you must enter the item id', 400);
    } else if ($merchant_ref_number == null) {
        $returnArr = generateResponse('false', 'you must enter the merchant ref number', 400);
    } else if ($merchant_ref_number == null) {
        $returnArr = generateResponse('false', 'you must enter the merchant ref number', 400);
    } else if ($non_completed_data == 0 && $book_data == 0) {
        $returnArr    = generateResponse('false', "Something Went Wrong Enure that sent data are correct", 400);
    } else {
        $where_conditions = [$item_id];
        $where = "where  id=" . '?' . "";
        $h = new Estate();

        $pay_status = getPaymentStatus($merchant_ref_number, $item_id, (int)$final_total);
        $paymentMethods = [
            'CARD' => 'CARD',
            'Mobile Wallet' => 'MWALLET',
            'PAYATFAWRY' => 'PayAtFawry'
        ];
        $method = $paymentMethods[$pay_status['method']] ?? '';
        if ($pay_status['status']) {
            $field = array('ref_number' => $merchant_ref_number,  'active' => '1',  'method' => $method);

            $check = $h->restateupdateData_Api($field, 'tbl_non_completed', $where, $where_conditions);
        } else {
            $field1 = array('ref_number' => $merchant_ref_number,   'method' => $method);

            $check = $h->restateupdateData_Api($field1, 'tbl_non_completed', $where, $where_conditions);
        }
        $returnArr    = generateResponse('true', "Payment status Founded", 200, array("status" => $pay_status['status']));
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
