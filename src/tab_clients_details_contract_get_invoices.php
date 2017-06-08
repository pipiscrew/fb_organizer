<?php
session_start();

	if (!isset($_SESSION["u"]) || !isset($_POST["client_id"])) {
		header("Location: login.php");
		exit ;
	}

// include DB
require_once ('config.php');
 
$db             = connect();
 
$rows = null;
///////////////////READ Rows
$rows = getSet($db,"select client_invoice_detail_id, client_id, company_name, occupation, address, pobox, city, countries.country_name as country_id, vat_no, tax_offices.tax_office_name as tax_office_id from client_invoice_details 
 LEFT JOIN countries ON countries.country_id = client_invoice_details.country_id
 LEFT JOIN tax_offices ON tax_offices.tax_office_id = client_invoice_details.tax_office_id where client_id=?", array($_POST['client_id']));
///////////////////READ Rows
 
echo json_encode(array("recs" => $rows));
 
?>