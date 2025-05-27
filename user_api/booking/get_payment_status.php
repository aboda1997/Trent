<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';
require  'fawry_pull_status.php';

header('Content-Type: application/json');
try {


    $merchant_ref_number = isset($_GET['merchant_ref_number']) ? $_GET['merchant_ref_number'] : null;
    $item_id = isset($_GET['item_id']) ? $_GET['item_id'] : 0;
    $final_total = isset($_GET['final_total']) ? $_GET['final_total'] : null;
    $non_completed_data = $rstate->query("select id from tbl_non_completed where id=" . $item_id)->num_rows;

    if ($final_total == null) {
        $returnArr = generateResponse('false', 'you must enter the total paid value', 400);
    } else if ($item_id == 0) {
        $returnArr = generateResponse('false', 'you must enter the item id', 400);
    } else if ($merchant_ref_number == null) {
        $returnArr = generateResponse('false', 'you must enter the merchant ref number', 400);
    } else if ($merchant_ref_number == null) {
        $returnArr = generateResponse('false', 'you must enter the merchant ref number', 400);
    } else if ($non_completed_data == 0) {
        $returnArr    = generateResponse('false', "Something Went Wrong Enure that sent data are correct", 400);
    }else {
        $get_secure_key = $rstate->query("select merchant_code ,secure_key from tbl_setting ")->fetch_assoc();
        $secureKey = $get_secure_key['secure_key'];
        $merchantCode = $get_secure_key['merchant_code'];
        $decrypted_secure_key =  decryptData($secureKey,  dirname(dirname(__FILE__), 2) . '/keys/private.pem')['data'];
        $decrypted_code = decryptData($merchantCode,  dirname(dirname(__FILE__), 2) . '/keys/private.pem')['data'];

        $check_push_pay = $rstate->query("SELECT orderStatus, orderAmount, itemId AS itemCode FROM payment WHERE merchantRefNumber = '" . $merchant_ref_number . "'");
        // Trim all fields to avoid hidden characters
        $concatenatedString =
            trim($decrypted_code) .
            trim($merchant_ref_number) .
            trim($decrypted_secure_key);
        $expectedSignature = hash('sha256', $concatenatedString);
        $check_pull_pay = getFawryPaymentStatus($decrypted_code, $merchant_ref_number, $expectedSignature);

        $pay_status = isPaymentValid($check_push_pay, $check_pull_pay, $item_id, (int)$final_total);

        $returnArr    = generateResponse('true', "Payment status Founded", 200, array("status" => $pay_status));
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}


function isPaymentValid($check_push_pay, $check_pull_pay, $item_id, $total_as_int)
{
    // Check if push payment has rows and pull payment status exists
    if ($check_push_pay->num_rows && $check_pull_pay["status"]) {
        $check_push_pay_data = $check_push_pay->fetch_assoc();

        // Convert amounts to integers for strict comparison
        $push_amount = (int)$check_push_pay_data['orderAmount'];
        $pull_amount = (int)$check_pull_pay['orderAmount'];

        // Verify all conditions with integer comparison
        return (
            $check_push_pay_data['orderStatus'] == 'PAID' &&
            $check_push_pay_data['itemCode'] == $item_id &&
            $check_pull_pay['orderStatus'] == 'PAID' &&
            $check_pull_pay['itemCode'] == $item_id &&
            $push_amount === $total_as_int &&
            $pull_amount === $total_as_int
        );
    }

    return false;
}
