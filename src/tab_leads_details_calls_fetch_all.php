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

	$find_sql = "SELECT client_call_id, client_id, DATE_FORMAT(client_call_datetime,'%d-%m-%Y %H:%i') as client_call_datetime, client_call_discussion, DATE_FORMAT(client_call_next_call,'%d-%m-%Y %H:%i') as client_call_next_call, chk_answered, chk_company_presented, chk_company_profile, chk_client_proposal, chk_appointment_booked, comment FROM client_calls where client_id=:id order by client_call_datetime DESC";
	$stmt      = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_POST['CLIENT_id']);
	
	$stmt->execute();
	$rows = $stmt->fetchAll();
	
	$row_template_call = <<<EOD
							   <tr>
							    	<td>{{client_call_id}}</td>
							    	<td>{{client_call_datetime}}</td>
							    	<td>{{chk_answered}}</td>
							    	<td>{{chk_company_presented}}</td>
							    	<td>{{chk_company_profile}}</td>
							    	<td>{{chk_client_proposal}}</td>
							    	<td>{{chk_appointment_booked}}</td>
							    	<td>{{client_call_next_call}}</td>
							   </tr>
EOD;

$tableRows=null;
	foreach($rows as $row) {
		$rowTBL = str_replace('{{client_call_id}}', $row['client_call_id'], $row_template_call);
		$rowTBL = str_replace('{{client_call_datetime}}', $row['client_call_datetime'], $rowTBL);
		$rowTBL = str_replace('{{client_call_next_call}}', $row['client_call_next_call'], $rowTBL);
		
		if ($row["chk_answered"]==1)
			$rowTBL = str_replace('{{chk_answered}}', '<span class="glyphicon glyphicon-ok">', $rowTBL);
		else 
			$rowTBL = str_replace('{{chk_answered}}', '<span class="glyphicon glyphicon-remove">', $rowTBL);
			
		//$rowTBL = str_replace('{{chk_answered}}', $row['chk_answered'], $rowTBL);
		
		if ($row["chk_company_presented"]==1)
			$rowTBL = str_replace('{{chk_company_presented}}', '<span class="glyphicon glyphicon-ok">', $rowTBL);
		else 
			$rowTBL = str_replace('{{chk_company_presented}}', '<span class="glyphicon glyphicon-remove">', $rowTBL);
			
	//	$rowTBL = str_replace('{{chk_company_presented}}', $row['chk_company_presented'], $rowTBL);

		if ($row["chk_company_profile"]==1)
			$rowTBL = str_replace('{{chk_company_profile}}', '<span class="glyphicon glyphicon-ok">', $rowTBL);
		else 
			$rowTBL = str_replace('{{chk_company_profile}}', '<span class="glyphicon glyphicon-remove">', $rowTBL);
			
		//$rowTBL = str_replace('{{chk_company_profile}}', $row['chk_company_profile'], $rowTBL);
		
		if ($row["chk_client_proposal"]==1)
			$rowTBL = str_replace('{{chk_client_proposal}}', '<span class="glyphicon glyphicon-ok">', $rowTBL);
		else 
			$rowTBL = str_replace('{{chk_client_proposal}}', '<span class="glyphicon glyphicon-remove">', $rowTBL);
			
	//	$rowTBL = str_replace('{{chk_client_proposal}}', $row['chk_client_proposal'], $rowTBL);

		if ($row["chk_appointment_booked"]==1)
			$rowTBL = str_replace('{{chk_appointment_booked}}', '<span class="glyphicon glyphicon-ok">', $rowTBL);
		else 
			$rowTBL = str_replace('{{chk_appointment_booked}}', '<span class="glyphicon glyphicon-remove">', $rowTBL);
		//$rowTBL = str_replace('{{chk_appointment_booked}}', $row['chk_appointment_booked'], $rowTBL);
		
		$tableRows.= $rowTBL;
	}
	
	$json = array('tableRows' => $tableRows);
	
	header("Content-Type: application/json", true);
	echo json_encode($json);


	
	
} catch (exception $e) {
    echo json_encode(null);
}
?>