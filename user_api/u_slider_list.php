<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
    $government_id = isset($_GET['government_id']) ? $_GET['government_id'] : null;
    $uid = isset($_GET['uid']) ? $_GET['uid'] : null;

    $pol = array();
    $c = array();

    $query = "SELECT government_id, uid , cat_id , id, img, JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) AS title 
        ,city_name ,compound_name
          FROM tbl_slider
          WHERE status=1
        ";

    if ($category_id) {
        $query .= " AND cat_id = " . $category_id;
    }

    if ($government_id) {
        $query .= " AND government_id = " . $government_id;
    }
    if ($uid) {
        $query .= " AND FIND_IN_SET(" . intval($uid) . ", uid)";
    }
    $query .=    " ORDER BY id DESC";
    $sel = $rstate->query($query);
    while ($row = $sel->fetch_assoc()) {

        $pol['id'] = $row['id'];
        $pol['title'] = $row['title'];
        $pol['city_name'] = $row['city_name']??'';
        $pol['compound_name'] = $row['compound_name']??'';
        $pol['img'] = $row['img'];
        $pol['category_id'] = $row['cat_id'] == 0 ? null : $row['cat_id'];
        $pol['government_id'] = $row['government_id'] == 0 ? null : $row['government_id'];
        $pol['user_list'] = getUserListFromIds($rstate, $row['uid']);





        $c[] = $pol;
    }
    if (empty($c)) {
        $returnArr    = generateResponse('true', "Slider List Not Founded!", 200, array(
            "slider_list" => $c,
            "length" => count($c),
        ));
    } else {

        $returnArr    = generateResponse('true', "Slider List Founded!", 200, array(
            "slider_list" => $c,
            "length" => count($c),
        ));
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}

function getUserListFromIds($rstate, $uidString)
{
    $user_list = [];

    // Check if string is not empty (after trimming)
    if (!empty($uidString) &&  !empty(trim($uidString))) {


        $result = $rstate->query("SELECT id, name FROM tbl_user WHERE id IN ($uidString)");

        // Build user_list array
        while ($user = $result->fetch_assoc()) {
            $user_list[] = [
                'id' => $user['id'],
                'name' => $user['name']
            ];
        }
    }

    return $user_list;
}
