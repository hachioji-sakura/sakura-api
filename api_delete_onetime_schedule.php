<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
require_once("./const.inc");
require_once("./func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
// Comment out for temporary.
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}
$request_id = $_POST['id'];
$request_id = str_replace("'","",$request_id);
$request_id = str_replace('"',"",$request_id);
if (!$request_id){
// id is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
} 

$request_updateuser = $_POST['updateuser'];
$request_updateuser = str_replace("'","",$request_updateuser);
$request_updateuser = str_replace('"',"",$request_updateuser);
if (!$request_updateuser){
// update user is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
} 

$now = date('Y-m-d H:i:s');

try {

	$sql = "UPDATE tbl_schedule_onetime SET delflag = 1 ,";
		$sql .=" deletetime = ?, updateuser = ? WHERE id = ? " ;

		$stmt = $dbh->prepare($sql);
		$id = (int) $request_id;
		$stmt->bindValue(1, $now, PDO::PARAM_STR);
		$stmt->bindValue(2, $request_updateuser, PDO::PARAM_INT);
		$stmt->bindValue(3, $request_id, PDO::PARAM_INT);
		$stmt->execute();

		$delflag = 1;
	$sql = "INSERT INTO tbl_schedule_onetime_history (".
        	"id,".
        	"delflag,".
        	"deletetime,".
        	"updateuser)".
        	" VALUES ( ?, ?, ?, ? )"; 

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$delflag, PDO::PARAM_INT);
		$stmt->bindValue(3,$now, PDO::PARAM_STR);
		$stmt->bindValue(4,$request_updateuser, PDO::PARAM_INT);
		$stmt->execute();
		
		$res = array(
			'status'=>'0',
			);
exit_label:
		// exit the program.

} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($token);

echo json_encode($res);

?>
