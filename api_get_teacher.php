<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

$request_teacher_id = $_GET[teacher_id];
$request_teacher_id = str_replace("'","",$request_teacher_id);
$request_teacher_id = str_replace('"',"",$request_teacher_id);

if ($request_teacher_id) {
	$teacher_no = (int) $request_teacher_id ;
	if ($teacher_no < 100000 ) {
		$res = array(
			'status'=>'error',
			'message'=>'IDの指定形式が不正です',
			'description'=>$teacher_no,
			);
		goto exit_label;
	}
	$request_no = $teacher_no - 100000 ;
}

try {

	$sql = "SELECT ".
		"no + 100000 as teacher_id,".
		"name as teacher_name,".
		"furigana as teacher_furigana,".
		"lesson_id,".
		"lesson_id2,".
		"del_flag as unsubscribe,".
		"mail_address,".
		"password, ".
		"bank_no, ".
		"bank_branch_no, ".
		"bank_acount_type, ".
		"bank_acount_no, ".
		"bank_acount_name ".
		"FROM tbl_teacher WHERE del_flag!=1";
	if ($request_no) {
		$sql .= " AND no='$request_no'";
	}
	$stmt = $db->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rslt as &$row) {
		if ($row['unsubscribe'] == "2") { $row['unsubscribe'] = "1"; }
	}
	unset($row);

	if ($request_teacher_id) {
		$str1 = "teacher_id=$request_teacher_id Found";
		$str2 = "teacher_id=$request_teacher_id Not Found";
	} else {
		$str1 = "All data";
		$str2 = "No data";
	}

	if ($rslt) {
		$teacher_id = (int) $rslt['no'];
		$res = array(
			'status'=>'success',
			'message'=>'',
			'description'=>$str1,
			'data'=>$rslt
			);
	} else {
		$res = array(
			'status'=>'error',
			'message'=>'データが存在しません',
			'description'=>$str2,
			'data'=>$rslt
			);
	}
exit_label:

} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'message'=>'exception',
		'description'=>$e->getMessage(),
		'data'=>''
		);
}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>
