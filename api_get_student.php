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

$request_student_id = $_GET[student_id];
$request_student_id = str_replace("'","",$request_student_id);
$request_student_id = str_replace('"',"",$request_student_id);

try {

	$sql = "SELECT ".
		"no as student_id,".
		"tbl_member.name as student_name,".
		"furigana as student_furigana,".
		"substr(tbl_member.name, 1, instr(tbl_member.name,' ')-1) as family_name,".
		"substr(tbl_member.name, instr(tbl_member.name,' ')+1) as first_name,".
		"kind as student_kind,".
		"tbl_grade_name.name as grade,".
		"del_flag as unsubscribe,".
		"passwd as password,".
		"sheet_id,".
		"cid,".
		"jyukensei,".
		"birth_year,".
		"birth_month,".
		"birth_day,".
		"grade_adj,".
		"fee_free,".
		"gender, ".
		"mail_address ".
		"FROM tbl_member ".
		"LEFT JOIN tbl_grade_name ON tbl_grade_name.grade_id=tbl_member.grade ".
		"WHERE del_flag!=1";
	if ($request_student_id) {
		$str_student_id = strval($request_student_id);
		$str_student_id_len = strlen($str_student_id);
		if ($str_student_id_len == 1) {
			$str_student_id_complete = '00000'.$str_student_id;
		} else if ($str_student_id_len == 2) {
			$str_student_id_complete = '0000'.$str_student_id;
		} else if ($str_student_id_len == 3) {
			$str_student_id_complete = '000'.$str_student_id;
		} else if ($str_student_id_len == 4) {
			$str_student_id_complete = '00'.$str_student_id;
		} else if ($str_student_id_len == 5) {
			$str_student_id_complete = '0'.$str_student_id;
		} 

		$sql .= " AND no='$str_student_id_complete'";
	}
	$stmt = $db->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach ($rslt as &$row) {
		if ($row['unsubscribe'] == "2") { $row['unsubscribe'] = "1" ;}
		if ($row['unsubscribe'] == "3") { $row['unsubscribe'] = "0" ;}
	}
	unset($row);

	if ($request_student_id) {
		$str1 = "student_id=$request_student_id Found";
		$str2 = "student_id=$request_student_id Not Found";
	} else {
		$str1 = "All data";
		$str2 = "No data";
	}

	if ($rslt) {
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
