<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
require_once("./const.inc");
$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
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

$request_lmsnotify = $_POST['lmsnotify'];
$request_lmsnotify = str_replace("'","",$request_lmsnotify);
$request_lmsnotify = str_replace('"',"",$request_lmsnotify);

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
               if ($request_lmsnotify){
                                // function call.
                        $result = set_lmsnotify($request_id);
                        if ($result === FALSE){
                                $res = array(
                                'status'=>'lmsimport failed'
                                );
                        } else {
                                $res = array(
                                'status'=>'0',
				'importstatus'=>$result
                                );
                        }
                } else {
                        $res = array(
                        'status'=>'0'
                        );
                }
exit_label:
		// exit the program.

} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		);

}

function set_lmsnotify($request_id){
                // this function notify update of the schedule to lms.
	$result = NULL;		// initialization.
        $senddata = array(
                'is_delete_data' => '1',
                'id' => $request_id
        );
        $query = http_build_query($senddata,"","&");
				// http-get:
	$platform = PLATFORM;
	if ($platform == 'staging' ){
       		$result = file_get_contents('https://staging.sakuraone.jp/import/schedules?'.$query);
	} else if ($platform == 'production' ){
       		$result = file_get_contents('https://sakuraone.jp/import/schedules?'.$query);
        }
	if ($result === FALSE ){		// not normal termination.
		return($result);
	}

				// http-post:
	if ($platform == 'staging' ){
	       	$url = 'https://staging.sakuraone.jp/import/schedules?'.$query;
	} else if ($platform == 'production' ){
       		$url = 'https://sakuraone.jp/import/schedules?'.$query;
        }
        $header = array(
                'Content-Type:application/x-www-form-urlencoded',
                'Content-Length: '.strlen($url),
		'Api-Token: 7511a32c7b6fd3d085f7c6cbe66049e7'
        );

        $options = array('http' => array(
        	       		 'method' => 'POST',
             			 'header' => implode("\r\n",$header)
                		)
        		);
	$ctx = stream_context_create($options);
       	$result = file_get_contents($url,false,$ctx);
	if(!empty($result)) $result = json_decode($result);
	return($result);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($token);

echo json_encode($res);

?>
