<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang_code = 'en';
 
 if($_GET['lang']){
$lang_code = $_GET['lang'];
 }
$pol = array();
$c = array();
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang_code')) as name ,id from tbl_government ");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['name'] = $row['name'];
		
		
		
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
	$returnArr = array("government_list"=>$c,"ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"government Not Founded!");
}
else 
{
$returnArr = array("government_list"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Government List Founded!");
}
echo json_encode($returnArr);
?>