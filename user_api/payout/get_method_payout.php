<?php 
require dirname( dirname(__FILE__) ,2).'/include/reconfig.php';
require dirname(dirname(__FILE__),2) . '/include/helper.php';
require_once dirname(dirname(__FILE__),2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__) , 2) . '/user_api/estate.php';
require_once dirname(dirname(__FILE__) , 2) . '/include/load_language.php';

header('Content-Type: application/json');
try{
	$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';
    $uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : '';

    $lang_ = load_specific_langauage($lang_code);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    }else{
$pol = array();
$c = array();
$sel = $rstate->query("SELECT id, img,  JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang_code')) AS title FROM tbl_payout_methods WHERE status=1");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['name'] = $row['title'];
		$pol['img'] = $row['img'];
		
		$c[] = $pol;
}
if(empty($c))
{

	$returnArr    = generateResponse('true', "Payout Method List Not Founded!", 200, array(
		"payout_method_list"=>$c
		,"length" => count($c),
	));
}
else 
{
$returnArr    = generateResponse('true', "Payout Method List Founded!", 200, array(
	"payout_method_list"=>$c
	,"length" => count($c),
));
}
    }
echo $returnArr;

} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile() ,  $e->getLine());
    echo $returnArr;
}
?>