<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['CLIENT_id'])) {
//if (empty($_POST['CLIENT_id'])) {
    echo json_encode(null);
    exit ;
}

try {
	include ('config.php');

	$db = connect();

	$find_sql = "select client_appointment_id, client_appointment_is_lead, DATE_FORMAT(client_appointment_datetime,'%d-%m-%Y %H:%i') as client_appointment_datetime, client_appointment_location, (select count(client_appointment_participant_id) from client_appointment_participants) AS participants from client_appointments where client_appointment_client_id= :id order by client_appointment_datetime";
	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_POST['CLIENT_id']);
	
	$stmt->execute();
	$rows = $stmt->fetchAll();
	
	$row_template_call = <<<EOD
							   <tr>
							    	<td>{{client_appointment_id}}</td>
							    	<td>{{client_appointment_is_lead}}</td>
							    	<td>{{client_appointment_datetime}}</td>
							    	<td>{{client_appointment_location}}</td>
							    	<td>{{client_appointment_part}}</td>


							   </tr>
EOD;

	$tableRows=null;

	foreach($rows as $row) {
		$rowTBL = str_replace('{{client_appointment_id}}', $row['client_appointment_id'], $row_template_call);
		
		if ($row["client_appointment_is_lead"]==1)
			$rowTBL = str_replace('{{client_appointment_is_lead}}', '<span class="glyphicon glyphicon-ok">', $rowTBL);
		else 
			$rowTBL = str_replace('{{client_appointment_is_lead}}', '<span class="glyphicon glyphicon-remove">', $rowTBL);
				
		$rowTBL = str_replace('{{client_appointment_datetime}}', $row['client_appointment_datetime'], $rowTBL);
		$rowTBL = str_replace('{{client_appointment_location}}', $row['client_appointment_location'], $rowTBL);
		$rowTBL = str_replace('{{client_appointment_part}}', $row['participants'], $rowTBL);
		
		$tableRows.= $rowTBL;
	}
	
	$json = array('tableRows' => $tableRows);
	
	header("Content-Type: application/json", true);
	echo json_encode($json);


	
	
} catch (exception $e) {
    echo json_encode(null);
}
?>