<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}


// include your code to connect to DB.
include ('config.php');

$table_columns = array(
'client_id',
'client_code',
'client_name',
'profile_guid_last_viewed_when',
'client_sector_id',
'client_sector_sub_id',
'client_rating_id',
'next_call',
'manager_name',
'telephone',
'mobile',
'email',
'owner',
'actions'
);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];

$wh=" where ";
if ($_SESSION["level"] != 9)
	$wh = " where owner=".$_SESSION["id"]." and ";
else if (!empty($_GET["user_id"]))
	$wh.= " owner = " . $_GET["user_id"]." and ";
	

//$sql="select client_id, client_code, client_name, telephone, mobile, email, DATE_FORMAT(owned_date,'%d-%m-%Y %H:%i') as owned_date, userA.fullname as owner,'' as actions
//from clients 
//left join users as userA on userA.user_id = clients.owner 
// {$wh} is_lead=0 ";

$sql = "SELECT client_code,client_id, client_name, DATE_FORMAT(profile_guid_last_viewed_when,'%d-%m-%Y %H:%i') as profile_guid_last_viewed_when, client_sectors.client_sector_name as client_sector_id, client_sector_subs.client_sector_sub_name as client_sector_sub_id, client_ratings.client_rating_name as client_rating_id, 
 (select DATE_FORMAT(client_call_next_call,'%d-%m-%Y %H:%i') as next_call from client_calls where client_id=clients.client_id order by client_call_next_call DESC LIMIT 1) as next_call, manager_name, telephone, mobile, email, users.fullname AS owner,'' as actions FROM `clients`
 LEFT JOIN client_sectors ON client_sectors.client_sector_id = clients.client_sector_id
 LEFT JOIN client_sector_subs ON client_sector_subs.client_sector_sub_id = clients.client_sector_sub_id
 LEFT JOIN client_ratings ON client_ratings.client_rating_id = clients.client_rating_id
 LEFT JOIN users ON users.user_id = clients.owner
  {$wh} is_lead=1 ";
  

$count_query_sql = "select count(*) from clients
 LEFT JOIN client_sectors ON client_sectors.client_sector_id = clients.client_sector_id
 LEFT JOIN client_sector_subs ON client_sector_subs.client_sector_sub_id = clients.client_sector_sub_id
 LEFT JOIN client_ratings ON client_ratings.client_rating_id = clients.client_rating_id
 LEFT JOIN users ON users.user_id = clients.owner
 {$wh} is_lead=1 ";


//////////////////////////////////////WHEN SEARCH TEXT SPECIFIED
if (isset($_GET["search"]) && !empty($_GET["search"]))
{
	$sdafsd= $_GET["search"];

	$like_str = " or #field# like :searchTerm";
	$where = " 0=1 ";

	foreach($table_columns as $col)
	{
		if ($col=="client_sector_id" || $col=="client_sector_sub_id" || $col=="client_rating_id" || $col=="next_call"  || $col=="actions")
			continue;
			
		$where.= str_replace("#field#",$col, $like_str);
	}

	$sql.= " and (". $where.")";
	$count_query_sql.= " and (". $where.")";
}

//////////////////////////////////////WHEN SORT COLUMN NAME SPECIFIED
if (isset($_GET["name"]) && isset($_GET["order"]))
{
	$name= $_GET["name"];
	$order= $_GET["order"];

	$sql.= " order by {$name} {$order}";
}

//echo $sql;
//exit;

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