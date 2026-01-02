<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    $token = isset($_POST['token']) ? $_POST['token'] : null;
    
    if ($token  == null) {
        $returnArr    = generateResponse('false', "Token is required", 400);
    } else if ($uid == null) {
        $returnArr    = generateResponse('false', "User id is required", 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
        $returnArr    = generateResponse('false', "User id is not exists", 400);
    }  else {

            $table = "tbl_user";
            $field_values = ["registration_token" => $token ];
            
            $h = new Estate();
            
            $check = $h->restateupdateData_Api($field_values, $table ,  "where id=" . '?' . "", [$uid] );
            $returnArr    = generateResponse('true', "Registration Token Updated SuccessFully", 200);
        }
    
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
