<?php
function connect() {
	$mysql_hostname = "localhost";
	$mysql_user = "x";
	$mysql_password = "x";
	$mysql_database = "x";

	//setup a connection with mySQL
	$mysqli = new mysqli($mysql_hostname, $mysql_user, $mysql_password, $mysql_database);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}

	//enable utf8!
	$mysqli -> query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

	return $mysqli;
}

function executeSQL($db, $sql) {
	if ($stmt = $db -> prepare($sql)) {
		$stmt -> execute();
		$stmt -> close();
		}
	}
function getScalar($db, $sql, $params) {
	if ($stmt = $db -> prepare($sql)) {
		$types = str_repeat('s', count($params));
		
		if ($params!=null)
			bind_param_array($stmt, $types, $params);
		
		$stmt -> execute();

		$stmt->bind_result($f1); 

		$stmt->fetch();
		$stmt -> close();
		
		return $f1;
	} else
		return 0;
}

function getRow($db, $sql, $params) {
	if ($stmt = $db -> prepare($sql)) {
		$types = str_repeat('s', count($params));
		bind_param_array($stmt, $types, $params);
		
		$stmt -> execute();
		$result = $stmt->get_result();
		
		//$r = $result->fetch_row(); //complete row
		$r = $result->fetch_array();
		
		$stmt -> close();
		
		return $r;
	} else
		return 0;
}


//http://no2.php.net/manual/en/mysqli-stmt.bind-param.php#115028
function bind_param_array( $stmt,  $types,  $vars ){
    $php_command = '$stmt->bind_param( $types';
    for( $i=0;$i<count($vars);$i++)
    {
        $php_command .= ',$vars['.$i.']';
    }
    $php_command .= ');';
    return eval( $php_command );
}
?>