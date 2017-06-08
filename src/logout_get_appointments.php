<?php
session_start();

if(!isset($_SESSION['id']))
{
	die("Error 0x5476");
   // echo "login ok-{$_SESSION['id']}";
}

//exit;
//
//if (!isset($_POST["vars"])){
//	die("error 0x0021");
//}
//
//$json = json_decode($_POST["vars"]);
//
//$password_hash = $json->MPBBomn9;
//$user_id = $json->user_id;
//
//
//if (empty($password_hash) || empty($user_id)) {
//	die("error 0x0071");
//}
//else if ($password_hash!="yy|fvYj<{K^9 >*&c(|F)OD6#>/eo]345Oik6h7R|B46p9vcY}L</>YNDz~F`f=")
//{
//	die("error 000A1");
//}

//DB
require_once ('config.php');
 
$db = connect();
 
$now = date("Y-m-d");
$mod_date = strtotime($now."+ 1 days");
$mod_date2 = date("Y-m-d",$mod_date);

$date=new DateTime(); //this returns the current date time
$date2 = $date->format('Y-m-d');

$rows = getSet($db,"select clients.client_id,clients.client_name,DATE_FORMAT(client_appointment_datetime,'%d-%m-%Y %H:%i') as client_appointment_datetime from client_appointment_participants
 left join client_appointments on client_appointments.client_appointment_id = client_appointment_participants.client_appointment_id
 left join clients on clients.client_id = client_appointments.client_appointment_client_id
 where client_appointment_participants.user_id=? and client_appointment_datetime between '$date2 00:00' and '$mod_date2 23:59'",array($_SESSION['id']));

$output = array();
 foreach($rows as $row) {
	$output[] = array('id' => $row['client_id'], 'handle' => $row['client_name'], 'when' => $row['client_appointment_datetime']);
}
 
//$json = array('recs' => $rows);

header("Content-Type: application/json", true);
echo json_encode($output);

?>
 
 