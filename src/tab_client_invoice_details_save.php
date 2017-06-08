<?php
session_start();

if (!isset($_SESSION["u"])) {
    echo json_encode(null);
    exit ;
}
 
if (!isset($_POST['client_id_INVOICE']) || !isset($_POST['company_name']) || !isset($_POST['occupation']) || !isset($_POST['address_INVOICE']) || !isset($_POST['pobox']) || !isset($_POST['city']) || !isset($_POST['country_id_INVOICE']) || !isset($_POST['vat_no_INVOICE']) || !isset($_POST['tax_office_id_INVOICE'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
require_once ('config_general.php');
 
$db = connect();
 




if(isset($_POST['client_invoice_detailsFORM_updateID']) && !empty($_POST['client_invoice_detailsFORM_updateID']))
{
	$sql = "UPDATE client_invoice_details set company_name=:company_name, occupation=:occupation, address=:address, pobox=:pobox, city=:city, country_id=:country_id, vat_no=:vat_no, tax_office_id=:tax_office_id where client_invoice_detail_id=:client_invoice_detail_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_invoice_detail_id' , $_POST['client_invoice_detailsFORM_updateID']);
}
else
{
	$sql = "INSERT INTO client_invoice_details (client_id, company_name, occupation, address, pobox, city, country_id, vat_no, tax_office_id) VALUES (:client_id, :company_name, :occupation, :address, :pobox, :city, :country_id, :vat_no, :tax_office_id)";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':client_id' , $_POST['client_id_INVOICE']);
}


$stmt->bindValue(':company_name' , $_POST['company_name']);
$stmt->bindValue(':occupation' , $_POST['occupation']);
$stmt->bindValue(':address' , $_POST['address_INVOICE']);
$stmt->bindValue(':pobox' , $_POST['pobox']);
$stmt->bindValue(':city' , $_POST['city']);
$stmt->bindValue(':country_id' , $_POST['country_id_INVOICE']);
$stmt->bindValue(':vat_no' , $_POST['vat_no_INVOICE']);
$stmt->bindValue(':tax_office_id' , $_POST['tax_office_id_INVOICE']);

$stmt->execute();
 
$status = $stmt->errorCode();
 
 if ($status=="00000"){
 write_log($db, 4, "DetailsInvoice for company ". $_POST['company_name']." edited/added by seller ".$_SESSION['u'], $_POST['client_id_INVOICE'], $_SESSION['id']);	
 	
 }
 
echo $status; 
?>