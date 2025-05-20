
<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

    $pol = array();
    $c = array();
    $sel = $rstate->query("select id , JSON_UNQUOTE(JSON_EXTRACT(question, '$.$lang_code')) as question , JSON_UNQUOTE(JSON_EXTRACT(answer, '$.$lang_code')) as answer  from tbl_faq  where status=1 ");
    while ($row = $sel->fetch_assoc()) {
        $pol['id'] = $row['id'];

        $pol['question'] = $row['question'];
        $pol['answer'] = $row['answer'];
        $c[] = $pol;
    }
    if (empty($c)) {
        $returnArr    = generateResponse('true', "FAQ List Not Founded!", 200, array(
            "faq_list" => $c
        ));
    } else {
        $returnArr    = generateResponse('true', "FAQ List Founded!", 200, array(
            "faq_list" => $c
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
