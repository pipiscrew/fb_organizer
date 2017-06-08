<?php
session_start();

if (!isset($_SESSION["u"]) || empty($_GET['client_updateID']) || empty($_GET['offerID'])) {
	echo json_encode(null);
    exit ;
}

$company_id = $_GET['client_updateID'];
$filepath="./proposals/$company_id/".$_GET['offerID']."_plan.pptx";

if(!file_exists($filepath)){ // file does not exist
    die("file not found ($filepath)");
} else {
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=".$_GET['offerID']."_plan.pptx");
    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: binary");

    // read the file from disk
    readfile($filepath);
}

?>