<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['CLIENT_CALLSid'])) {
    echo json_encode(null);
    exit ;
}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT client_call_id, client_id, DATE_FORMAT(client_call_datetime,'%d-%m-%Y %H:%i') as client_call_datetime, client_call_discussion, DATE_FORMAT(client_call_next_call,'%d-%m-%Y %H:%i') as client_call_next_call, chk_answered, chk_company_presented, chk_company_profile, chk_client_proposal, chk_appointment_booked, comment FROM client_calls where client_call_id=?", array($_POST['CLIENT_CALLSid']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>