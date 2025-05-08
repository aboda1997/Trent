<?php 
require_once dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
$pol = array();
$c = array();
$pro_id  = $_GET['pro_id'] ?? '';
if($pro_id == '' )
{
	$returnArr = generateResponse('false', "Something Went Wrong!", 400);

}
else 
{
 $book = $rstate->query("select check_in,check_out from tbl_book where prop_id=".$pro_id." and book_status!='Cancelled'");
 $l = array();
 while($row = $book->fetch_assoc())
 {
	 $l[] = $row;
 }
 if(empty($l))
{
	$returnArr    = generateResponse('true', "date List Not Founded!", 200, array(
		"date_list" => $l,
	));   
}
else 
{

$returnArr    = generateResponse('true', "date List Founded!", 200, array(
	"date_list" => $l,
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
?>