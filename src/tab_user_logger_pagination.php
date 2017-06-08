<?php
session_start();

if (!isset($_SESSION["u"])) {
    header("Location: index.html");
    exit ;
}

// include your code to connect to DB.
include ('config.php');

$table_columns = array(
'log_id',
'log_UTC_when',
'log_type',
'log_text',
'user_id',
'client_id',

);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];

$start_dt = $_GET["start_date"];
$end_dt = $_GET["end_date"];

$wh=null;
if (!empty($start_dt) && !empty($end_dt))
{
	$wh=" (log_UTC_when BETWEEN '$start_dt' AND '$end_dt')";

}

$sql="select log_id, log_UTC_when, log_type, log_text, users.fullname as user_id, clients.client_name as client_id from logger 
 LEFT JOIN users ON users.user_id = logger.user_id
 LEFT JOIN clients ON clients.client_id = logger.client_id";
 
$count_query_sql = "select count(*) from logger LEFT JOIN users ON users.user_id = logger.user_id 
 LEFT JOIN clients ON clients.client_id = logger.client_id";


//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED
if (isset($_GET["search"]) && !empty($_GET["search"]))
{
	$sdafsd= $_GET["search"];

	$like_str = "";
	
	$like_str = " or #field# like :searchTerm";
	
	$where = " 0=1 ";

	foreach($table_columns as $col)
	{
		//special fields, because of joins
		if ($col=="user_id")
			$col = "users.fullname";

		if ($col=="client_id")
			$col = "clients.client_name";
		//special fields, because of joins
						
		$where.= str_replace("#field#",$col, $like_str);
	}

	if ($wh==null)
	{
		$sql.= " where ". $where;
		$count_query_sql.= " where ". $where;
	}else {
		$sql.= " where $wh and ($where)";
		$count_query_sql.= " where $wh and ($where)";
	}
}
elseif ($wh!=null) {
		$sql.= " where $wh";
		$count_query_sql.= " where $wh";
}

//////////////////////////////////////WHEN SORT COLUMN NAME SPECIFIED
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$name= $_GET["name"];
	$order= $_GET["order"];

	$sql.= " order by $name $order";
//	$sql.= " order by :col_name :col_order";
//$sql.= " order by log_UTC_when ASC"; 


}

//////////////////////////////////////PREPARE
$stmt = $conn->prepare($sql." limit :offset,:limit");

//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED *BIND*
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$stmt->bindValue(':searchTerm', '%'.$_GET["search"].'%');

//////////////////////////////////////WHEN COLSORT
if (isset($_GET["name"]) && isset($_GET["order"]))
{
//	$stmt->bindValue(':col_name', $name);
//	$stmt->bindValue(':col_order', $order);

//echo $sql 	;
//	echo $name." - ".$order;
//exit;
}

//////////////////////////////////////PAGINATION SETTINGS
$stmt->bindValue(':offset' , intval($offset), PDO::PARAM_INT);
$stmt->bindValue(':limit' , intval($limit), PDO::PARAM_INT);

	
//////////////////////////////////////FETCH ROWS
$stmt->execute();

$rows_sql = $stmt->fetchAll();

$rows     = array();
$x=-1;
foreach($rows_sql as $row_key){
	$x+=1;
	
	for($i = 0; $i < count($table_columns); $i++)
	{
		$rows[$x][$table_columns[$i]] = $row_key[$table_columns[$i]];
	}
	
}

//////////////////////////////////////COUNT TOTAL 
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$count_recs = getScalar($conn, $count_query_sql, array(':searchTerm' => '%'.$_GET["search"].'%'));
else
	$count_recs = getScalar($conn, $count_query_sql, null);

//////////////////////////////////////JSON ENCODE
$arr = array('total'=> $count_recs,'rows' => $rows);

header("Content-Type: application/json", true);

echo json_encode($arr);

?>