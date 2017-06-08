<?php
	session_start();

	if (!isset($_SESSION["u"])) {
		header("Location: login.php");
		exit ;
	}
	else if ($_SESSION['level']!=9) {
		die("You are not authorized to view this!");
	}

// include your code to connect to DB.
include ('config.php');

$table_columns = array(
'client_id',
"client_code",
"is_lead",
"client_name",
"client_call_datetime",
"chk_answered",
"chk_company_presented",
"chk_company_profile",
"chk_client_proposal",
"chk_appointment_booked",
"client_call_next_call",
"fullname",
"owner",
);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];

///////////////////////////////////extra-dates
$start_dt = $_GET["start_date"];
$end_dt = $_GET["end_date"];

$where ="";
if (!empty($start_dt) && !empty($end_dt))
{
	$where = " and (client_call_datetime BETWEEN '{$start_dt}' AND '{$end_dt}')";

}

///////////////////////////extra-combo user
$user= $_GET["user"];

if (!empty($user))
{
        $where .= " and owner=".$user;
}
///////////////////////////combo user

$sql="select clients.client_id as client_id, is_lead,client_code,client_name,DATE_FORMAT(client_call_datetime,'%d-%m-%Y %H:%i') as client_call_datetime,chk_answered,chk_company_presented,chk_company_profile,chk_client_proposal,chk_appointment_booked,
 DATE_FORMAT(client_call_next_call,'%d-%m-%Y %H:%i') as client_call_next_call,users.fullname as owner from client_calls 
left join clients on clients.client_id = client_calls.client_id 
left join users on users.user_id = clients.owner

   where 1=1 {$where}";
   
$count_query_sql = "select count(*) from client_calls 
 left join clients on clients.client_id = client_calls.client_id 
 where 1=1 {$where}";



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

	$sql.= " order by {$_GET["name"]} {$_GET["order"]}";
}


//////////////////////////////////////PREPARE
$stmt = $conn->prepare($sql." limit :offset,:limit");

//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED *BIND*
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$stmt->bindValue(':searchTerm', '%'.$_GET["search"].'%');

//////////////////////////////////////WHEN COLSORT
//if (isset($_GET["name"]) && isset($_GET["order"]))
//{
//	$stmt->bindValue(':col_name', $name);
//	$stmt->bindValue(':col_order', $order);
//}

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
	
	
	$rows[$x]['seen'] = "<span class='label label-primary'>{$rows[$x]['seen']}</span>"; 

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