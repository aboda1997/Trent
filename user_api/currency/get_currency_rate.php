<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $currency = isset($_GET['currency']) ? $rstate->real_escape_string($_GET['currency']) : '';

    if ($currency == '') {
        $returnArr    = generateResponse('false', "Currency Key is required", 400);
    } else {
        $c = 0;
        $sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang_code')) as name ,id from tbl_government
          WHERE status=1
");
        if ($c == 0) {
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
