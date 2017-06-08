<?php

require_once ('config.php');

if(!isset($_POST["tel"])){
	echo json_encode("error");
	return;
}

$db       = connect();

$r = getScalar($db,"select count(client_id) from clients where telephone=?",array($_POST["tel"]));

echo json_encode(array("rec_count"=>$r));

?>