<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_POST['offer_id']) || empty($_POST['client_id'])) {
    echo json_encode(null);
    exit ;
}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT offer_id,DATE_FORMAT(next_renewal,'%d-%m-%Y') as next_renewal,marketing_plan_comment, DATE_FORMAT(service_starts,'%d-%m-%Y') as service_starts, DATE_FORMAT(service_ends,'%d-%m-%Y') as service_ends, DATE_FORMAT(marketing_plan_when,'%d-%m-%Y %H:%i') as marketing_plan_when, marketing_plan_location, marketing_plan_completed,request_access FROM offers where offer_id=?", array($_POST['offer_id']));


 	//market_plan - file validation
	$company_id=$_POST['client_id'];
	$offer_id=$_POST['offer_id'];
	$proposal_plan_fl1="./proposals/$company_id/".$offer_id."_plan.pps";
	$proposal_plan_fl2="./proposals/$company_id/".$offer_id."_plan.pptx";
	$proposal_plan_fl3="./proposals/$company_id/".$offer_id."_plan.pdf";
	
	$plan=null;
	if ((file_exists($proposal_plan_fl1) || file_exists($proposal_plan_fl2) || file_exists($proposal_plan_fl3))){
		$plan="exists";
	}
	
	//proposal approval - validation
	$approval=null;
	$approval = getScalar($db, "select approval_user_date from offers where offer_id=?",array($_POST['offer_id']));
	
	if ($approval!=null)
		$approval="exists";
	

    //unicode
    header("Content-Type: application/json", true);
    
	echo json_encode(array("record" => $r, "plan" => $plan, "approval" => $approval));

	
} catch (exception $e) {
    echo json_encode(null);
}
?>