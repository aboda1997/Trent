<?php
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');

    // Decode JSON input to array
    $inputData = json_decode($input, true);

    // Extract required fields with default values if not present
    $fawryRefNumber = $inputData['fawryRefNumber'] ?? '';
    $merchantRefNumber = $inputData['merchantRefNumber'] ?? '';
    $orderAmount = number_format((float)$inputData['orderAmount'], 2, '.', '');
    $orderStatus = $inputData['orderStatus'] ?? '';
    $paymentMethod = $inputData['paymentMethod'] ?? '';
    $itemCode = $inputData['orderItems'][0]['itemCode'] ?? 0;
    $checkKey = $rstate->query("select id from payment where  merchantRefNumber =" . $merchantRefNumber . "");
    $get_secure_key = $rstate->query("select merchant_code ,secure_key from tbl_setting ");
    $secureKey = $get_secure_key->fetch_assoc()['secure_key'];
    $decrypted_secure_key = decryptData($secureKey,  dirname(dirname(__FILE__),2) . '/keys/private.pem'); 
    $h = new Estate();

    if (!verifyFawrySignature($inputData, $inputData['messageSignature'] , $decrypted_secure_key['data'])) {
        $returnArr    = generateResponse('false', "Not valid Data", 400);
    } else if ($checkKey->num_rows) {
        $field_values = ["orderStatus" => $orderStatus];
        $where = "where merchantRefNumber=" . '?' . " and itemId = ? ";
        $where_conditions = [$merchantRefNumber ,$itemCode];
        $_id = $h->restateupdateData_Api($field_values, 'payment', $where, $where_conditions);
        $returnArr    = generateResponse('true', "Success", 200);
    } else {
        // Prepare values in exact same order as field names
        $field_values = ["fawryRefNumber", "merchantRefNumber", "orderAmount", "orderStatus", "paymentMethod", "itemId"];
        $data_values = [
            $fawryRefNumber,
            $merchantRefNumber,
            $orderAmount,
            $orderStatus,
            $paymentMethod,
            $itemCode
        ];

        $returnArr    = generateResponse('true', "Success", 200);
        $check = $h->restateinsertdata_Api($field_values, $data_values, 'payment');
    }
    echo $returnArr;
} catch (Exception $e) {

    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}

function verifyFawrySignature(array $paymentData, string $receivedSignature, string $secureKey): bool
{
    // Strict formatting
    $paymentAmount = number_format((float)$paymentData['paymentAmount'], 2, '.', '');
    $orderAmount = number_format((float)$paymentData['orderAmount'], 2, '.', '');
    $paymentReference = $paymentData['paymentRefrenceNumber'] ?? "";

    // Trim all fields to avoid hidden characters
    $concatenatedString =
        trim($paymentData['fawryRefNumber']) .
        trim($paymentData['merchantRefNumber']) .
        $paymentAmount .
        $orderAmount .
        trim($paymentData['orderStatus']) .
        trim($paymentData['paymentMethod']) .
        $paymentReference .
        trim($secureKey);
    $expectedSignature = hash('sha256', $concatenatedString);
    // Compare securely (to prevent timing attacks)
    return hash_equals($expectedSignature, $receivedSignature);
}
