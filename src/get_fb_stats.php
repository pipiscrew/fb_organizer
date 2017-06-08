<?php

session_start();

if (!isset($_SESSION["id"]) || !isset($_POST['pg'])) {
	echo "error010101010";
    exit ;
}
 
// include DB
//require_once ('config.php');
require_once ('config_general.php');

$l=0;
$t=0;

get_fb_info($_POST['pg'], $l, $t);

echo json_encode(array("l" => $l, "t" =>$t));
?>