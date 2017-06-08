<?php
//session_start();
//
//if (!isset($_SESSION["u"]) || empty($_POST['client_invoice_detail_id'])) {
//    echo json_encode(null);
//    exit ;
//}

try {
	include ('config.php');

	$db = connect();

	$r= getRow($db, "SELECT client_invoice_detail_id, client_id, company_name, occupation, address, pobox, city, country_id, vat_no, tax_office_id FROM client_invoice_details where client_invoice_detail_id=?", array($_POST['client_invoice_detail_id']));

    //unicode
    header("Content-Type: application/json", true);
	echo json_encode($r);

	
} catch (exception $e) {
    echo json_encode(null);
}
?>