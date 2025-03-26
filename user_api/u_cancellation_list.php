<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

    $pol = array();
    $c = array();
    $sel = $rstate->query("select id , JSON_UNQUOTE(JSON_EXTRACT(description, '$.$lang_code')) as description , JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) as title , is_recommended	 from tbl_cancellation_policy ");
    while ($row = $sel->fetch_assoc()) {
        $pol['id'] = $row['id'];

        $pol['description'] = $row['description'];
        $pol['title'] = $row['title'];
        $pol['is_recommended'] = $row['is_recommended'];
        $c[] = $pol;
    }
    if (empty($c)) {
        $returnArr    = generateResponse('true', "Cancellation Policies List Not Founded!", 200, array(
            "cancellation_policies_list" => $c
        ));
    } else {
        $returnArr    = generateResponse('true', "Cancellation Policies List Founded!", 200, array(
            "cancellation_policies_list" => $c
        ));
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
