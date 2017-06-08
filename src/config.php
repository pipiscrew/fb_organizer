<?php  
function connect() {
	$mysql_hostname = "localhost";
	$mysql_user = "x";
	$mysql_password = "x";
	$mysql_database = "x";
	
	$dbh = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_database", $mysql_user, $mysql_password, 
  array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
  ));
	

	
	return $dbh;
}

function getScalar($db, $sql, $params) {
	if ($stmt = $db -> prepare($sql)) {

		$stmt->execute($params);

		return $stmt->fetchColumn();
	} else
		return 0;
}

function getRow($db, $sql, $params) {
	if ($stmt = $db -> prepare($sql)) {

		$stmt->execute($params);

		return $stmt->fetch();
	} else
		return 0;
}

function getSet($db, $sql, $params) {
	if ($stmt = $db -> prepare($sql)) {

		$stmt->execute($params);

		return $stmt->fetchAll();
	} else
		return 0;
}

function executeSQL($db, $sql, $params) {
	if ($stmt = $db -> prepare($sql)) {

		$stmt->execute($params);

		return $stmt->rowCount();
	} else
		return false;
}
?>