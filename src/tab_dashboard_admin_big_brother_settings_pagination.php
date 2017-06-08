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
'expense_template_id',
'expense_category_id',
'expense_sub_category_id',
'price'
);

$conn     = connect();

if (!is_numeric($_GET["limit"]) || !is_numeric($_GET["offset"]))
{
	echo "error";
	exit;
}

$limit = $_GET["limit"];
$offset= $_GET["offset"];


$sql="select expense_template_id, tblA.expense_category_name as expense_category_id, tblB.expense_category_name as expense_sub_category_id, price from expense_templates 
 LEFT JOIN expense_categories as tblA ON tblA.expense_category_id = expense_templates.expense_category_id
 LEFT JOIN expense_categories as tblB ON tblB.expense_category_id = expense_templates.expense_sub_category_id ";

//select expense_id, expense_category_id, expense_sub_category_id, DATE_FORMAT(expense_daterec,'%d-%m-%Y') as expense_daterec, price, comment, created_owner_id, DATE_FORMAT(created_date,'%d-%m-%Y %H:%i') as created_date from expenses ";
$count_query_sql = "select count(*) from expense_templates";


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
if (isset($_GET["search"]) && !empty($_GET["search"]))
	$count_recs = getScalar($conn, $count_query_sql, array(':searchTerm' => '%'.$_GET["search"].'%'));
else
	$count_recs = getScalar($conn, $count_query_sql, null);

//////////////////////////////////////JSON ENCODE
$arr = array('total'=> $count_recs,'rows' => $rows);

header("Content-Type: application/json", true);

echo json_encode($arr);

?>