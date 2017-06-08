<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_POST['CHOOSEINVOICE_offerID']) || !isset($_POST['CHOOSEINVOICE_invoicedetailID'])) {
	header("Location: login.php");
	exit ;
}
	require_once ('config.php');
	require_once ('config_general.php');


	$db = connect();

	$row= null;
	///////////////////READ row
	$find_sql = "SELECT *,countries.country_name FROM `offers` 
	LEFT JOIN countries ON offers.country_id=countries.country_id
	LEFT JOIN clients ON clients.client_id=offers.company_id where offer_id=:id";
	$stmt     = $db->prepare($find_sql);
	$stmt->bindValue(':id', $_POST['CHOOSEINVOICE_offerID']);

	$stmt->execute();
	$row      = $stmt->fetchAll();
	///////////////////READ row

	//var_dump($row);
	if(sizeof($row) != 1){
		echo "no rec";
		exit;
	}
	
	$company_id = $row[0]['company_id'];
	

	write_log($db, 4, "Invoice Details setted for company ".$row[0]['client_name']." by seller ".$row[0]['offer_seller_name'], $company_id, $_SESSION['id']);

	$gen_invoice_answer = guid_solution();
	////update offer / set date + user
	$sql = "UPDATE offers set rec_guid_answer_invoice=:rec_guid_answer_invoice, invoice_detail_when=:invoice_detail_when, invoice_detail_user=:invoice_detail_user, invoice_detail_id=:invoice_detail_id where offer_id=:offer_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':rec_guid_answer_invoice' , $gen_invoice_answer);
	$stmt->bindValue(':offer_id' , $_POST['CHOOSEINVOICE_offerID']);
	$stmt->bindValue(':invoice_detail_id' , $_POST['CHOOSEINVOICE_invoicedetailID']);
	
	$stmt->bindValue(':invoice_detail_when' , date("Y-m-d H:i:s") );
	$stmt->bindValue(':invoice_detail_user' , $_SESSION['id']);

	$stmt->execute();

	echo $stmt->errorCode();
	
//returns 5chars
function guid_solution()
{
	$characters = '01729384352617089';
	$token = '';
	for($i=0; $i <= 4; $i++) {
		$token .= $characters[mt_rand(0, strlen($characters) - 1)];
	}
	return $token;
}
?>