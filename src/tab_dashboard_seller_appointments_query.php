<?php
session_start();

if(!isset($_SESSION["u"])){
	header("Location: login.php");
	exit ;
}

require_once ('config.php');

$db = connect();

$record = array();

$rows = getSet($db,"select client_appointment_id as id,client_name as title,client_appointment_datetime as start,client_appointment_owner_id,fullname from client_appointments 
left join clients on clients.client_id = client_appointments.client_appointment_client_id 
left join users on users.user_id = client_appointments.client_appointment_owner_id 
where client_appointment_datetime between '".$_GET["start"]."' and '".$_GET["end"]."' order by client_appointment_owner_id", null);

//where client_appointment_owner_id=".$_GET["user_id"]." and client_appointment_datetime between '".$_GET["start"]."' and '".$_GET["end"]."'", null);

//
//
$colors = array("#0037C5", "#008C0A", "#E90815", "#E90CAA", "#0ff104", "#12445b", "#e8ddfb", "#a28713");
$color_no = -1;

$prev_owner="0";

foreach($rows as $row) {
	if ($prev_owner!=$row['client_appointment_owner_id'])
		$color_no+=1;
	
	
	$datetime = new DateTime($row['start']);

	$record[] = array("id" => $row['id'],"title" => $row['title'],"start" => $datetime->format(DateTime::ISO8601),"color" => $colors[$color_no],"owner" => $row['fullname']);
	
	$prev_owner = $row['client_appointment_owner_id'];
}

echo json_encode($record);
?>