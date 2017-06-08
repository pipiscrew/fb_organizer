<?php
session_start();
if ($_SESSION['level']!=9)
	die("You dont have permissions to access this area! Ask administrator for more!");


// include your code to connect to DB.
include ('config.php');

$table_columns = array(
'user_working_hour_id',
'user_id',
'date_start',
'date_end',
'reason'
);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];

$month = $_GET["month"];
$user= $_GET["user"];



$where="";

if (!empty($month))
{
	$year = date('Y'); //this year
	
	$month_calc = $month+1; //increase by 1
	$start_date = date("$year-$month_calc-01"); //convert to date
	$mod_date = strtotime($start_date."- 1 day"); //subtract -1!
	$m = date("Y-m-d",$mod_date); //format back to mysql style!

	//construct the query string!
	$where = " date_start BETWEEN '$year-$month-01' AND '$m'";	
	//$where = " date_start BETWEEN '2014-$month-01' AND '2014-$month-31'";
}
	
if (!empty($user))
{
	if (!empty($where))
		$where .= " and ";
	
		$where .= " user_working_hours.user_id=".$user;
}
	
if (!empty($where))
	$where = " where ".$where;


$sql="select user_working_hour_id, users.fullname as user_id, DATE_FORMAT(date_start,'%d-%m-%Y %H:%i') as date_start, DATE_FORMAT(date_end,'%d-%m-%Y %H:%i') as date_end,reason from user_working_hours 
 LEFT JOIN users ON users.user_id = user_working_hours.user_id".$where;
$count_query_sql = "select count(*) from user_working_hours".$where;

//echo $sql;
//exit;
//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED
if (isset($_GET["search"]) && !empty($_GET["search"]))
{
	$sdafsd= $_GET["search"];

	$like_str = " or #field# like :searchTerm";
	$where = " 0=1 ";

	foreach($table_columns as $col)
	{
		$where.= str_replace("#field#",$col, $like_str);
	}

	$sql.= " where ". $where;
	$count_query_sql.= " where ". $where;
}

//////////////////////////////////////WHEN SORT COLUMN NAME SPECIFIED
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$name= $_GET["name"];
	$order= $_GET["order"];

	$sql.= " order by :col_name :col_order";
}


//////////////////////////////////////PREPARE
$stmt = $conn->prepare($sql." limit :offset,:limit");
//echo $sql;
//exit;
//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED *BIND*
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$stmt->bindValue(':searchTerm', '%'.$_GET["search"].'%');

//////////////////////////////////////WHEN COLSORT
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$stmt->bindValue(':col_name', $name);
	$stmt->bindValue(':col_order', $order);
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
if (isset($_GET["search"]))
	$count_recs = getScalar($conn, $count_query_sql, array(':searchTerm' => '%'.$_GET["search"].'%'));
else
	$count_recs = getScalar($conn, $count_query_sql, null);

//////////////////////////////////////JSON ENCODE
$arr = array('total'=> $count_recs,'rows' => $rows);

header("Content-Type: application/json", true);

echo json_encode($arr);

?>