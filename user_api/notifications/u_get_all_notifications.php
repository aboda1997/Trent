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
    // Get pagination parameters
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
    $itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 10; // Items per page
    // Calculate offset
    $offset = ($page - 1) * $itemsPerPage;

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else {
        $pol = array();
        $c = array();
        $query = "SELECT *  FROM tbl_notification_head  WHERE  uid =" . $uid . "";

        $sel_length  = $rstate->query($query)->num_rows;
        $query .= " LIMIT " . $itemsPerPage . " OFFSET " . $offset;
        $sel = $rstate->query($query);

        while ($row = $sel->fetch_assoc()) {

            $pol['id'] = $row['id'];
            $pol['title'] = $row['title'];
            $pol['created_at'] = $row['created_at'];
            $pol['body'] = $row['body'];
            $pol['is_seen'] = (bool)$row['is_seen'];
            $pol['img'] = $row['img'];

            $c[] = $pol;
        }
        if (empty($c)) {

            $returnArr    = generateResponse('true', "Notification List Not Founded!", 200, array(
                "notification_list" => $c,
                "length" => $sel_length,
            ));
        } else {
            $returnArr    = generateResponse('true', "Notification List Founded!", 200, array(
                "notification_list" => $c,
                "length" => $sel_length,
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
