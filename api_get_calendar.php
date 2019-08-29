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

$calendar_name = $_GET['Calendar'];
$calendar_name   = str_replace('"',"",str_replace("'","",$calendar_name));
$student_id = $_GET['Student'];
$student_id = str_replace('"',"",str_replace("'","",$student_id));
$teacher_id = $_GET['Teacher'];
$teacher_id = str_replace('"',"",str_replace("'","",$teacher_id));
$starttime = $_GET['Starttime'];
$starttime = str_replace('"',"",str_replace("'","",$starttime));
$endtime = $_GET['Endtime'];
$endtime = str_replace('"',"",str_replace("'","",$endtime));
$course_id = $_GET['Course'];
$course_id = str_replace('"',"",str_replace("'","",$course_id));
$subject_id = $_GET['Subject'];
$subject_id = str_replace('"',"",str_replace("'","",$subject_id));
$lesson_id = $_GET['Lesson'];
$lesson_id = str_replace('"',"",str_replace("'","",$lesson_id));
$lecture_id = $_GET['Lecture'];
$lecture_id = str_replace('"',"",str_replace("'","",$lecture_id));

try {

	$sql = "SELECT ".
		"tbl_event.cal_summary as calendar,".
		"case when tbl_event.absent_flag=1 then '休み1' ".
		"     when tbl_event.absent_flag=2 then '休み2' ".
		"     when tbl_event.alternative_flag=1 then '振替' ".
		"			else '' end as yasumi,".
		"tbl_event.member_no as student_no,".
		"tbl_member.name as student,".
		"case when tbl_event.course_id=2 then 'グループ' ".
		"     when tbl_event.course_id=3 then substring(tbl_member.name, 1, instr(tbl_member.name,' ')-1) ".
		"			else '' end as family,".
		"tbl_lecture.lecture_id as lecture_id,".
		"tbl_event.course_id as course,".
		"tbl_event.subject_id as subject,".
		"tbl_event.lesson_id as lesson,".
		"tbl_event.teacher_id as teacher_no,".
		"tbl_teacher.name as teacher,".
		"tbl_event.cal_evt_summary as comment,".
//		"(INSTR(REPLACE(REPLACE(tbl_event.cal_evt_summary,'（','('),'）',')'),'(仮)')) as kari_flag, ".
		"(tbl_event.cal_evt_summary REGEXP '[（(]仮[)）]') as kari_flag, ".
		"FROM_UNIXTIME(tbl_event.event_start_timestamp) as start,".
		"FROM_UNIXTIME(tbl_event.event_end_timestamp) as end ".
		"FROM tbl_event, tbl_member, tbl_teacher, tbl_lecture ".
		"WHERE tbl_event.member_no=tbl_member.no AND tbl_event.teacher_id=tbl_teacher.no ".
		"AND tbl_lecture.course_id=tbl_event.course_id ".
		"AND tbl_lecture.subject_id=tbl_event.subject_id ".
		"AND tbl_lecture.lesson_id=tbl_event.lesson_id ".
		"";
	$values = array();
	if ($calendar_name)	{ $sql .= " AND tbl_event.cal_summary=? ";$values[] = $calendar_name; }
	if ($student_id)	{ $sql .= " AND tbl_event.member_no=? ";	$values[] = $student_id; }
	if ($teacher_id)	{ $sql .= " AND tbl_event.teacher_id=? ";	$values[] = $teacher_id; }
	if ($starttime)		{ $sql .= " AND tbl_event.event_start_timestamp>=UNIX_TIMESTAMP(?) ";$values[] = $starttime; }
	if ($endtime)			{ $sql .= " AND tbl_event.event_end_timestamp<=UNIX_TIMESTAMP(?) ";	$values[] = $endtime; }
	if ($course_id)		{ $sql .= " AND tbl_event.course_id=? ";	$values[] = $course_id; }
	if ($subject_id)	{ $sql .= " AND tbl_event.subject_id=? ";	$values[] = $subject_id; }
	if ($lesson_id)		{ $sql .= " AND tbl_event.lesson_id=? ";	$values[] = $lesson_id; }
	if ($lecture_id)	{ $sql .= " AND tbl_lecture.lecture_id=? ";	$values[] = $lecture_id; }
	$stmt = $db->prepare($sql);
	$stmt->execute($values);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$str1 = '';
	if ($calendar_name) { $str1 .= "Calendar=$calendar_name, "; }
	if ($student_id)		{ $str1 .= "Student=$student_id, "; }
	if ($teacher_id)		{ $str1 .= "Teacher=$teacher_id, "; }
	if ($starttime)			{ $str1 .= "Starttime=$starttime, "; }
	if ($endtime)				{ $str1 .= "Endtime=$endtime, "; }
	if ($course_id)			{ $str1 .= "Course=$course_id, "; }
	if ($subject_id)		{ $str1 .= "Subject=$subject_id, "; }
	if ($lesson_id)			{ $str1 .= "Lesson=$lesson_id, "; }
	if ($str1) {
		$str2 = $str1."Not Found";
		$str1 .= "Found";
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

//header("Content-Type: text/plain; charset=utf-8");
header("Content-Type: application/json; charset=utf-8");

//echo $sql;
//echo "\n\n";
//var_dump($values);
echo json_encode($res);

?>
