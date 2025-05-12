<?php
require dirname(dirname(__FILE__),2) . '/include/reconfig.php';
require dirname(dirname(__FILE__),2) . '/include/constants.php';
require dirname(dirname(__FILE__),2) . '/include/helper.php';
require dirname(dirname(__FILE__),2) . '/include/validation.php';
require_once dirname(dirname(__FILE__),2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__) , 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {

    $uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : null;

    // Get pagination parameters
    $lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : "en"; // Current page
    $lang_ = load_specific_langauage($lang);

    if ($uid == null) {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else {
        $data = array();
        $data_list = array();
        $query = "
        SELECT 
    b.id,
    b.check_in,
    b.check_out,
    b.total,
    p.title,
    p.image
FROM 
    tbl_book b
INNER JOIN 
    tbl_property p ON b.prop_id = p.id
WHERE 
    b.book_status IN ('Check_in', 'Completed')
    AND p.add_user_id = $uid
    ";

        $sel_length  = $rstate->query($query)->num_rows;
        $query_data = $rstate->query($query);
        while ($row = $query_data->fetch_assoc()) {
            $data['id'] = $row['id'];
            $imageArray = array_filter(explode(',', $row['image'] ?? ''));
            $vr = array();

            // Loop through each image URL and push to $vr array
            foreach ($imageArray as $image) {
                $vr[] = array('img' => trim($image));
            }
            $titleData = json_decode($row['title'], true);
            $data['title'] = $titleData[$lang];
            $data['check_in'] = $row['check_in'];
            $data['check_out'] = $row['check_out'];
            $data['total'] = $row['total'];
            $data['image'] = $vr;
        
            $data_list[]  = $data;
        }
        $returnArr = generateResponse(
            'true',
            "Ready For Payout Properties List Founded!",
            200,
            array(
                "Properties_list" => $data_list,
                "length" =>  $sel_length

            )
        );
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
