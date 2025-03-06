<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang_code = 'en';
 
 if($_GET['lang']){
$lang_code = $_GET['lang'];
 }
$pol = array();
$c = array();
$sel = $rstate->query("SELECT id, img, JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) AS title FROM tbl_facility WHERE status=1");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['title'] = $row['title'];
		$pol['img'] = $row['img'];
		
		
		
		
		$c[] = $pol;
	
	
}
if(empty($c))
{

	$returnArr    = generateResponse('false', "Facility List Not Founded!", 200, array(
		"facility_list"=>$c
		,"length" => count($c),
	));
}
else 
{
$returnArr    = generateResponse('true', "Facility List Founded!", 200, array(
	"facility_list"=>$c
	,"length" => count($c),
));
}
echo $returnArr;
?>