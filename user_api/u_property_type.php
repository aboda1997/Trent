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
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(title, '$.$lang_code')) as title , id , img from tbl_category where status=1");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['title'] = $row['title'];
		$pol['img'] = $row['img'];
		
		$c[] = $pol;
	
	
}
if(empty($c))
{
	$returnArr = array("type_list"=>$c,"ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"Property Type Not Founded!");
}
else 
{
$returnArr = array("type_list"=>$c,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Property Type List Founded!");
}
echo json_encode($returnArr);
?>