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
'client_code',
'is_lead',
'nextcall',
'clientname',
'sector',
'subsector',
'manager',
'rating',
'telephone',
'mobile',
'mail',
'owner'
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
	$where = " and (profile_sent_when BETWEEN '$start_dt' AND '$end_dt')";

}

///////////////////////////extra-combo user
$user= $_GET["user"];

if (!empty($user))
{
        $where .= " and owner=".$user;
}
///////////////////////////combo user

$sql="SELECT client_code,client_id, is_lead, client_name as clientname,clients.is_lead as is_lead, client_sectors.client_sector_name as sector, client_sector_subs.client_sector_sub_name as subsector, client_ratings.client_rating_name as rating, 
 (select DATE_FORMAT(client_call_next_call,'%d-%m-%Y %H:%i')  from client_calls where client_id=clients.client_id order by client_call_next_call DESC LIMIT 1) as nextcall
 , manager_name as manager, telephone, mobile, email as mail, users.fullname AS owner FROM `clients`
 LEFT JOIN client_sectors ON client_sectors.client_sector_id = clients.client_sector_id
 LEFT JOIN client_sector_subs ON client_sector_subs.client_sector_sub_id = clients.client_sector_sub_id
 LEFT JOIN client_sources ON client_sources.client_source_id = clients.client_source_id
 LEFT JOIN client_ratings ON client_ratings.client_rating_id = clients.client_rating_id
 LEFT JOIN countries ON countries.country_id = clients.country_id
 LEFT JOIN users ON users.user_id = clients.owner
   where 1=1 {$where}";
   
$count_query_sql = "select count(*) from clients where 1=1 {$where}";


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