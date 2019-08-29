<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../www/sakura/schedule/func.inc");

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

try {

	$sql = "SELECT ".
		"tbl_fee.teacher_id + 100000 as teacher_id,".
		"tbl_fee.member_no as student_id,".
		"tbl_lecture.lecture_id ".
		"FROM tbl_fee, tbl_teacher, tbl_lecture ".
		"WHERE tbl_fee.teacher_id=tbl_teacher.no AND tbl_teacher.del_flag!=1 ".
		"AND tbl_fee.lesson_id=tbl_lecture.lesson_id ".
		"AND tbl_fee.course_id=tbl_lecture.course_id ".
		"AND tbl_fee.subject_id=tbl_lecture.subject_id";
	if ($request_teacher_id) {
		if ($request_teacher_id  < 100000 ) {
			$res = array(
			'status'=>'error',
			'message'=>'teacher_idの指定形式が不正です',
			'description'=>$request_teacher_id,
			);
			goto exit_label;
		}
		$teacher_no = $request_teacher_id - 100000 ;
		$sql .= " AND teacher_id='$teacher_no '";
	}
	$sql .=' ORDER BY teacher_id, student_id';
	$stmt = $db->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($request_no) {
		$str1 = "teacher_id=$request_teacher_id Found";
		$str2 = "teacher_id=$request_teacher_id Not Found";
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
