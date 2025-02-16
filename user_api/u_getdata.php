 <?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang = load_specific_langauage('en');
 
 if($_GET['lang']){
$lang = load_specific_langauage($_GET['lang']);

 }

$uid = $data['uid'];
if($uid == '')
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went wrong  try again !");
}
else 
{ 
$count = $rstate->query("select * from tbl_user where id=".$uid."")->num_rows;
$check_owner = $rstate->query("select * from tbl_property where  add_user_id=" . $uid . "")->num_rows;

if($check_owner  >= 6){
		$rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id=" . $uid);

}else{
	$rstate->query("UPDATE tbl_user SET is_owner = 1 WHERE id=" . $uid);

}
$owner =$lang['Property_Manager'];
if($count != 0)
{
$wallet = $rstate->query("select * from tbl_user where id=".$uid."")->fetch_assoc();
if($wallet['is_owner']){
	$owner = $lang['owner'];
}
$curr = $rstate->query("select scredit,rcredit from tbl_setting")->fetch_assoc();
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Wallet Balance Get Successfully!", "owner" =>$owner, "code"=>$wallet['refercode'],"signupcredit"=>$curr['scredit'],"refercredit"=>$curr['rcredit'],"tax"=>$set['tax']);
}
else 
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Not Exist User!");
}
}
echo json_encode($returnArr);

