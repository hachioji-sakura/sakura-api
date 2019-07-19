<?php
ini_set( 'display_errors', 0 );
require_once("/home/hachiojisakura/www/sakura/schedule/const/const.inc");
require_once("/home/hachiojisakura/www/sakura/schedule/func.inc");

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
$request_attend_id = $_POST['attend_id'];
$request_attend_id = str_replace("'","",$request_attend_id);
$request_attend_id = str_replace('"',"",$request_attend_id);

$request_schedule_id = $_POST['schedule_id'];
$request_schedule_id = str_replace("'","",$request_schedule_id);
$request_schedule_id = str_replace('"',"",$request_schedule_id);


if (!$request_attend_id && !$request_schedule_id){
		// attend_id or schedule_id is a mandatory parameter.
	$res = array(
	'status'=>'1',
	);
	goto exit_label;
} 

if ($request_attend_id && $request_schedule_id){
		// both attend_id and schedule_id are specified .
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

$request_attend = $_POST['attend'];
$request_attend = str_replace("'","",$request_attend);
$request_attend = str_replace('"',"",$request_attend);

$now = date('Y-m-d H:i:s');
$user="hachiojisakura";
$pass="20160401sakurasaku";

try {

$dbh=new PDO('mysql:host=mysql720.db.sakura.ne.jp;dbname=hachiojisakura_calendar;charset=utf8',$user,$pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


	$sql = "UPDATE tbl_attend SET updateuser = '$request_updateuser',updatetime = '$now'";
		if ($request_attend){
			$sql .=" ,attend = '$request_attend' " ;
		}
		if ($request_attend_id){
			$sql .=" WHERE id = '$request_attend_id' " ;
		} else if ($request_schedule_id){
			$sql .=" WHERE schedule_id = '$request_schedule_id' " ;
		}

		$stmt = $dbh->prepare($sql);
		$stmt->execute();

		$updatetype = 'u';

	$sql = "INSERT INTO tbl_attend_history (".
        	"id,".
        	"updatetype,".
        	"schedule_id,".
        	"attend,".
        	"updatetime,".
        	"updateuser".
        	") VALUES ( ?, ?, ?, ?, ?, ?)"; 

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_attend_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$updatetype, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_schedule_id, PDO::PARAM_INT);
		$stmt->bindValue(4,$request_attend, PDO::PARAM_STR);
		$stmt->bindValue(5,$now, PDO::PARAM_STR);
		$stmt->bindValue(6,$request_updateuser, PDO::PARAM_STR);

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
