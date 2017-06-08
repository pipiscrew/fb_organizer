<?php
session_start();

if(!isset($_SESSION['id']))
{
	die("error 0x5476");
}
//elseif (!isset($_POST["vars"])){
//	die("error 0x0021");
//}

date_default_timezone_set("UTC");
	
// include DB
require_once ('config.php');

$db = connect();

//LOGOFF TYPE
//1-LOGOUT
//2-APPOINTMENT
//3-EMERGENCY


$json = json_decode($_POST["vars"]);


$type = $json->type;
$reason = $json->reason;
$appoint_count = $json->appoint_count;

//var_dump($json);
//exit;

if (empty($type) || empty($reason))
	die("error");

$time_left = date('Y-m-d H:i:s');	

//echo $appoint_count;
//exit;
////
if ($appoint_count>0)
{	
	$extra_hours = (int) $appoint_count;
	$extra_hours = $extra_hours * 2;
	
	$time_left =date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s"). " + {$extra_hours}hours"));
}
	
	//on logout write it to dbase
	$u_id=$_SESSION['id'];	
	
	//query the last user record from table (aka where date_end is null)
	$id = getScalar($db, "select user_working_hour_id from user_working_hours where date_end is null and user_id=? order by user_working_hour_id DESC limit 1",array($u_id));
	
	if ($id)
		echo executeSQL($db, "update user_working_hours set date_end=?,logout_type=?,reason=? where user_working_hour_id=?", array($time_left,$type,$reason, $id));
		
		