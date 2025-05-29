<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
    $uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : '';

    $lang_ = load_specific_langauage($lang_code);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else {
        $pol = array();
        $cc = array();
        $c = array();
        $sel = $rstate->query("SELECT PL.book_id,PL.id, PL.payout_status , PL.requested_at , PL.cancel_reason  FROM tbl_payout_list PL  WHERE  uid =" . $uid . "");
        while ($row = $sel->fetch_assoc()) {
            $book_id = $row['book_id'];
            $bd = $rstate->query("select * from tbl_book where id=" . $book_id . "")->fetch_assoc();

            $pol['id'] = $row['id'];
            $pol['payout_status'] = $row['payout_status'];
            $pol['requested_at'] = $row['requested_at'];
            $pol['cancel_reason'] = $row['cancel_reason'];
            $pol['total'] = $bd['total'];
            $pol['prop_title'] = json_decode($bd['prop_title'], true)[$lang_code] ?? '';

            $c[] = $pol;
        }
        $sel = $rstate->query("
       SELECT 
         ROUND(SUM(CASE WHEN PL.payout_status = 'Pending' THEN CAST(B.total AS DECIMAL(65,30)) ELSE CAST(0 AS DECIMAL(65,30)) END) , 2)AS total_pending,
         ROUND(SUM(CASE WHEN PL.payout_status = 'Completed' THEN CAST(B.total AS DECIMAL(65,30)) ELSE CAST(0 AS DECIMAL(65,30)) END),2 )AS total_completed
      
         FROM 
        tbl_payout_list PL
    INNER JOIN 
        tbl_book B ON PL.book_id = B.id
        WHERE 
             PL.uid = " . $uid . "
    ")->fetch_assoc();
        $cc['total_pending'] = round((float)$sel['total_pending'], 2);;
        $cc['total_completed'] = round((float)$sel['total_completed'], 2);

        $returnArr    = generateResponse('true', "Payout Request List Founded!", 200, array(
            "payout_request_list" => $c,
            "earning" => $cc,
            "length" => count($c),
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
