<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['client_appointment_id'])) {
    echo json_encode(null);
    exit ;
}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT client_appointment_id, client_appointment_client_id, client_appointment_is_lead, DATE_FORMAT(client_appointment_datetime,'%d-%m-%Y %H:%i') as client_appointment_datetime, client_appointment_location, client_appointment_google, client_appointment_comment,client_name,client_appointment_owner_id FROM client_appointments 
	left join clients on clients.client_id=	client_appointments.client_appointment_client_id
	where client_appointment_id=?", array($_POST['client_appointment_id']));

	$x= getSet($db, "SELECT * FROM client_appointment_participants where client_appointment_id=?", array($_POST['client_appointment_id']));
	
    //unicode
    header("Content-Type: application/json", true);
    
	$json = array('appointment'=> $r,'participants' => $x);

	echo json_encode($json);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>