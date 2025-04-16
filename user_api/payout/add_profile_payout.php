<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__) , 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__) , 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__) , 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    $method_id = isset($_POST['method_id']) ? $_POST['method_id'] : '';
    $bank_account_number = isset($_POST['bank_account_number']) ? $_POST['bank_account_number'] : '';
    $wallet_number = isset($_POST['wallet_number']) ? $_POST['wallet_number'] : '';

    $name = $rstate->real_escape_string(isset($_POST['name']) ? $_POST['name'] : '');
    $full_name = $rstate->real_escape_string(isset($_POST['full_name']) ? $_POST['full_name'] : '');
    $lang = $rstate->real_escape_string(isset($_POST['lang']) ? $_POST['lang'] : 'en');
    $bank_name = $rstate->real_escape_string(isset($_POST['bank_name']) ? $_POST['bank_name'] : '');
    $fieldNames = [
        'profile_name' => [
            'en' => 'Profile Name',
            'ar' => 'اسم الملف الشخصي'
        ],
        'full_name' => [
            'en' => 'Full name',
            'ar' => 'الاسم الكامل'
        ],
        'bank_name' => [
            'en' => 'Bank name',
            'ar' => 'اسم البنك'
        ]
    ];
    $lang_ = load_specific_langauage($lang);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } 
    else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false',$lang_["unsupported_lang_key"] , 400);
    }    
    else if (validateIdAndDatabaseExistance($method_id, 'tbl_payout_methods') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_payout_method_id"], 400);
    } else if (checkTableStatus($method_id, 'tbl_payout_methods') === false) {
        $returnArr = generateResponse('false', $lang_["payout_method_not_allowed"], 400);
    } else if (!validateName($name, $fieldNames['profile_name'][$lang], 50 , $lang)['status'] ) {
        $returnArr    = generateResponse('false', validateName($name, $fieldNames['profile_name'][$lang] , 50 ,$lang)['response'], 400);
    }
    else if (!validateName($full_name, $fieldNames['full_name'][$lang], 100 , $lang)['status']) {
        $returnArr    = generateResponse('false', validateName($full_name,$fieldNames['full_name'][$lang], 100 , $lang)['response'], 400);
    }
    else if (!validateName($full_name, $fieldNames['bank_name'][$lang], 50 , $lang)['status']) {
        $returnArr    = generateResponse('false', validateName($full_name, $fieldNames['bank_name'][$lang] , 50, $lang)['response'], 400);
    }
     else {
       
        $created_at = date('Y-m-d H:i:s');



        if (!isset($returnArr)) {
            $GLOBALS['rstate']->begin_transaction();
            $h = new Estate();
            $table = "tbl_payout_profiles";
            $encoded_full_name = json_encode([
                "full_name" => $name,

            ], JSON_UNESCAPED_UNICODE);
            
            $encoded_name = json_encode([
                "name" => $name,

            ], JSON_UNESCAPED_UNICODE);
            
            $encoded_bank_name = json_encode([
                "bank_name" => $bank_name,

            ], JSON_UNESCAPED_UNICODE);

            $field_values = ["uid", "name", "bank_name", "full_name", "bank_account_number", "wallet_number" , "status"];
            $data_values = [$uid, $name, $bank_name, $full_name, $bank_account_number, $wallet_number,  1];
            $profile_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
            $GLOBALS['rstate']->commit();
        }
    }


    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        $returnArr    = generateResponse('true', $lang_["payout_profile_added"], 201, array(
            "Profile_id" => $profile_id,
            "profile_name" => $name
        ));

        echo $returnArr;
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
