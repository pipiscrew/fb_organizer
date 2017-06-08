<?php
session_start();

if (!isset($_SESSION["u"])) {
	header("Location: login.php");
	exit ;
}

require_once ('config.php');
require_once ('config_general.php');

$db   = connect();

$user_id = array();
//$user_id[] = $_SESSION['id'];
//$user_id=$_SESSION['id'];

if ($_SESSION['level']==9 && isset($_POST["user_id"]))
{//when admin

	if (isset($_POST["is_department"]) && $_POST["is_department"]=="1")
	{ //when department
	
		//split by comma
		$arr_user_levels = explode(",", $_POST["user_id"]);
		
		//validation is int!
		array_walk($arr_user_levels, function( &$value ) {
				 $value = (int) $value;
		});
		
		$user_id = array();
		
		//ask for users ID belong to this department
		$users = getSet($db,"select user_id from users where user_level_id in (9,".implode(',',$arr_user_levels).")", null);
		
		//$user_idsss = "0";
		
		foreach($users as $row) {
			$user_id[] = $row['user_id'];
			//$user_idsss .= ",".$row['user_id'];
		}
		
		//$user_id= $user_idsss;
	}
	else 
	{
		//$user_id=$_POST["user_id"];
		$user_id[] = $_POST["user_id"];
				
	}	
}
else 
{
	$user_id[] = $_SESSION['id'];
}

		
//
if (empty($user_id))
	{echo "empty";
	exit;}
	
$clickable_user = $user_id[0];

if (sizeof($user_id)>1)
	$clickable_user = 9999;
	
//var_dump($user_id);
//exit;

//echo "select ifNull(FORMAT(sum(gen_total),0),0) from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),3)."' AND '".date('Y').'-'.date('m').'-01'." 00:00:00'";
//exit;

  function rangeWeek($datestr) {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('N', $dt)==1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
    $res['end'] = date('N', $dt)==7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
    return $res;
    }
    
  function rangeMonth($datestr) {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d', strtotime('last day of this month', $dt));
    return $res;
    }

$mysql_of_this_end_month = get_end_of_the_month(date('m'),date('Y'));
$mysql_yday = date('Y-m-d', strtotime('-1 day'));

////////////////////////////////////////////////////
$week_range = rangeWeek(date('Y-m-d'));
$prev_week_start = strtotime($week_range['start']. " -7 days");
$prev_week_end = strtotime($week_range['end']. " -7 days");

$mysql_7days = array("start" => date("Y-m-d", $prev_week_start), "end" => date("Y-m-d", $prev_week_end));

//var_dump($mysql_7days);
//exit;

$mysql_month = rangeMonth(date('Y-m-d'));
////////////////////////////////////////////////////


?>

					var tbl_rows = "";
					
					//only clients @ BUDGET
					var seller_budget_yday  = "<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".$mysql_yday." 00:00:00' AND '".$mysql_yday." 23:59:59'",null); ?>";
					var seller_budget_day  = "<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'",null); ?>";
					var seller_budget_week  = "<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and  offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".$mysql_7days['start']." 00:00:00' AND '".$mysql_7days['end']." 23:59:59'", null); ?>";
					//YEARWEEK(is_paid_when, 1) = YEARWEEK(CURDATE(), 1)
					var seller_budget_month = "<?php $seller_budget_month = getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and  offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); echo $seller_budget_month;?>";
					var seller_budget_3month = '<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0) from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),3)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					
					var seller_budget_6month = '<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and  offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),6)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_budget_9month = '<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and  offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),9)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_budget_12month = '<?= getScalar($db,"select ifNull(FORMAT(sum(offer_total_amount),0),0)  from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and  offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),12)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_budget_graph_html =  '<div id="seller_budget_graph_month" style="width:100px; height:80px"></div>';
					tbl_rows = "<tr><td>Total Budget</td><td><a href='index_dashboard_sales_01_budget.php?dstart=<?=$mysql_yday;?>&dend=<?=$mysql_yday;?>&u=<?=$clickable_user;?>'>"+seller_budget_yday+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?dstart=<?=date("Y-m-d");?>&dend=<?=date("Y-m-d");?>&u=<?=$clickable_user;?>'>"+seller_budget_day+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?dstart=<?=$mysql_7days['start'];?>&dend=<?=$mysql_7days['end'];?>&u=<?=$clickable_user;?>'>"+seller_budget_week+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?month=1&dstart=<?=$mysql_month['start'];?>&dend=<?=$mysql_month['end'];?>&u=<?=$clickable_user;?>'>"+seller_budget_month+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),3),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_budget_3month+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),6),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_budget_6month+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),9),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_budget_9month+"&euro;</a></td><td><a href='index_dashboard_sales_01_budget.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),12),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_budget_12month+"&euro;</a></td><td align='center' valign='top'>" +seller_budget_graph_html + "</td></tr>";


					
					//leads+clients @ PROPOSALS
					var seller_proposal_yday  = "<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".$mysql_yday." 00:00:00' AND '".$mysql_yday." 23:59:59'", null); ?>";
					var seller_proposal_day  = "<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'", null); ?>";
					var seller_proposal_week  = "<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".$mysql_7days['start']." 00:00:00' AND '".$mysql_7days['end']." 23:59:59'", null); ?>";
					//YEARWEEK(offer_date_rec, 1) = YEARWEEK(CURDATE(), 1)
					var seller_proposal_month = "<?php $seller_proposal_month = getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); echo $seller_proposal_month; ?>";
					var seller_proposal_3month = '<?= getScalar($db,"select count(offer_id) from offers  where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".get_month_back(date('m'),date('Y'),3)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_proposal_6month = '<?= getScalar($db,"select count(offer_id) from offers  where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".get_month_back(date('m'),date('Y'),6)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_proposal_9month = '<?= getScalar($db,"select count(offer_id) from offers  where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".get_month_back(date('m'),date('Y'),9)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_proposal_12month = '<?= getScalar($db,"select count(offer_id) from offers where  offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".get_month_back(date('m'),date('Y'),12)."' AND '".$mysql_of_this_end_month." 00:00:00'", null); ?>';
					var seller_proposal_graph_html =  '<div id="seller_proposal_graph_month" style="width:100px; height:80px"></div>';
					tbl_rows += "<tr><td>Total Proposals</td><td><a href='index_dashboard_sales_02_proposal.php?dstart=<?=$mysql_yday;?>&dend=<?=$mysql_yday;?>&u=<?=$clickable_user;?>'>"+seller_proposal_yday+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?dstart=<?=date("Y-m-d");?>&dend=<?=date("Y-m-d");?>&u=<?=$clickable_user;?>'>"+seller_proposal_day+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?dstart=<?=$mysql_7days['start'];?>&dend=<?=$mysql_7days['end'];?>&u=<?=$clickable_user;?>'>"+seller_proposal_week+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?month=1&dstart=<?=$mysql_month['start'];?>&dend=<?=$mysql_month['end'];?>&u=<?=$clickable_user;?>'>"+seller_proposal_month+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),3),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_proposal_3month+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),6),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_proposal_6month+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),9),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_proposal_9month+"</a></td><td><a href='index_dashboard_sales_02_proposal.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),12),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_proposal_12month+"</a></td><td align='center' valign='top'>" +seller_proposal_graph_html + "</td></tr>";
					
					
					
					//leads+clients @ PROFILE
					var seller_profile_yday  = "<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".$mysql_yday."' AND '".$mysql_yday."'", null); ?>";
					var seller_profile_day  = "<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".date("Y-m-d")."' AND '".date("Y-m-d")."'", null); ?>";
					var seller_profile_week  = "<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".$mysql_7days['start']."' AND '".$mysql_7days['end']."'", null); ?>";
					//YEARWEEK(profile_sent_when, 1) = YEARWEEK(CURDATE(), 1)
					var seller_profile_month = "<?php $seller_profile_month = getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".date('Y').'-'.date('m').'-01'."' AND '".get_end_of_the_month(date('m'), date('Y'))."'", null); echo $seller_profile_month; ?>";
					var seller_profile_3month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),3)."' AND '".$mysql_of_this_end_month."'", null); ?>';
					var seller_profile_6month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),6)."' AND '".$mysql_of_this_end_month."'", null); ?>';
					var seller_profile_9month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),9)."' AND '".$mysql_of_this_end_month."'", null); ?>';
					var seller_profile_12month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),12)."' AND '".$mysql_of_this_end_month."'", null); ?>';
					var seller_profile_graph_html =  '<div id="seller_profile_graph_month" style="width:100px; height:80px"></div>';
					tbl_rows += "<tr><td>Total Profile</td><td><a href='index_dashboard_sales_03_profile.php?dstart=<?=$mysql_yday;?>&dend=<?=$mysql_yday;?>&u=<?=$clickable_user;?>'>"+seller_profile_yday+"</a></td><td><a href='index_dashboard_sales_03_profile.php?dstart=<?=date("Y-m-d");?>&dend=<?=date("Y-m-d");?>&u=<?=$clickable_user;?>'>"+seller_profile_day+"</a></td><td><a href='index_dashboard_sales_03_profile.php?dstart=<?=$mysql_7days['start'];?>&dend=<?=$mysql_7days['end'];?>&u=<?=$clickable_user;?>'>"+seller_profile_week+"</a></td><td><a href='index_dashboard_sales_03_profile.php?month=1&dstart=<?=$mysql_month['start'];?>&dend=<?=$mysql_month['end'];?>&u=<?=$clickable_user;?>'>"+seller_profile_month+"</a></td><td><a href='index_dashboard_sales_03_profile.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),3),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_profile_3month+"</a></td><td><a href='index_dashboard_sales_03_profile.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),6),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_profile_6month+"</a></td><td><a href='index_dashboard_sales_03_profile.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),9),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_profile_9month+"</a></td><td><a href='index_dashboard_sales_03_profile.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),12),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_profile_12month+"</a></td><td align='center' valign='top'>" +seller_profile_graph_html + "</td></tr>";
					

					//leads+clients @ CALLS
					var seller_call_yday  = "<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".$mysql_yday." 00:00:00' AND '".$mysql_yday." 23:59:59'", null); ?>";
					var seller_call_day  = "<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'", null); ?>";
					var seller_call_week  = "<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".$mysql_7days['start']."' AND '".$mysql_7days['end']." 23:59:59'", null); ?>";
					var seller_call_month = "<?php $seller_call_month = getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); echo $seller_call_month; ?>";
					var seller_call_3month = '<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".get_month_back(date('m'),date('Y'),3)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_call_6month = '<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".get_month_back(date('m'),date('Y'),6)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_call_9month = '<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".get_month_back(date('m'),date('Y'),9)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_call_12month = '<?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".get_month_back(date('m'),date('Y'),12)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_call_graph_html =  '<div id="seller_call_graph_month" style="width:100px; height:80px"></div>';
					tbl_rows += "<tr><td>Total Calls</td><td><a href='index_dashboard_sales_04_calls.php?dstart=<?=$mysql_yday;?>&dend=<?=$mysql_yday;?>&u=<?=$clickable_user;?>'>"+seller_call_yday+"</a></td><td><a href='index_dashboard_sales_04_calls.php?dstart=<?=date("Y-m-d");?>&dend=<?=date("Y-m-d");?>&u=<?=$clickable_user;?>'>"+seller_call_day+"</a></td><td><a href='index_dashboard_sales_04_calls.php?dstart=<?=$mysql_7days['start'];?>&dend=<?=$mysql_7days['end'];?>&u=<?=$clickable_user;?>'>"+seller_call_week+"</a></td><td><a href='index_dashboard_sales_04_calls.php?month=1&dstart=<?=$mysql_month['start'];?>&dend=<?=$mysql_month['end'];?>&u=<?=$clickable_user;?>'>"+seller_call_month+"</a></td><td><a href='index_dashboard_sales_04_calls.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),3),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_call_3month+"</a></td><td><a href='index_dashboard_sales_04_calls.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),6),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_call_6month+"</a></td><td><a href='index_dashboard_sales_04_calls.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),9),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_call_9month+"</a></td><td><a href='index_dashboard_sales_04_calls.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),12),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_call_12month+"</a></td><td align='center' valign='top'>" +seller_call_graph_html + "</td></tr>";
										
					
					//clients
					var seller_client_yday  = "<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".$mysql_yday." 00:00:00' AND '".$mysql_yday." 23:59:59'", null); ?>";
					var seller_client_day  = "<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'", null); ?>";
					var seller_client_week  = "<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and YEARWEEK(is_paid_when, 1) = YEARWEEK(CURDATE(), 1)", null); ?>";
					var seller_client_month = "<?php $seller_client_month = getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); echo $seller_client_month; ?>";
					var seller_client_3month = '<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),3)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_client_6month = '<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),6)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_client_9month = '<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),9)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_client_12month = '<?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".get_month_back(date('m'),date('Y'),12)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_client_graph_html =  '<div id="seller_client_graph_month" style="width:100px; height:80px"></div>';
					tbl_rows += "<tr><td>Total Clients</td><td><a href='index_dashboard_sales_05_client.php?dstart=<?=$mysql_yday;?>&dend=<?=$mysql_yday;?>&u=<?=$clickable_user;?>'>"+seller_client_yday+"</a></td><td><a href='index_dashboard_sales_05_client.php?dstart=<?=date("Y-m-d");?>&dend=<?=date("Y-m-d");?>&u=<?=$clickable_user;?>'>"+seller_client_day+"</a></td><td><a href='index_dashboard_sales_05_client.php?dstart=<?=$mysql_7days['start'];?>&dend=<?=$mysql_7days['end'];?>&u=<?=$clickable_user;?>'>"+seller_client_week+"</a></td><td><a href='index_dashboard_sales_05_client.php?month=1&dstart=<?=$mysql_month['start'];?>&dend=<?=$mysql_month['end'];?>&u=<?=$clickable_user;?>'>"+seller_client_month+"</a></td><td><a href='index_dashboard_sales_05_client.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),3),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_client_3month+"</a></td><td><a href='index_dashboard_sales_05_client.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),6),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_client_6month+"</a></td><td><a href='index_dashboard_sales_05_client.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),9),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_client_9month+"</a></td><td><a href='index_dashboard_sales_05_client.php?month=1&dstart=<?=substr(get_month_back(date('m'),date('Y'),12),0,10);?>&dend=<?=$mysql_of_this_end_month;?>&u=<?=$clickable_user;?>'>"+seller_client_12month+"</a></td><td align='center' valign='top'>" +seller_client_graph_html + "</td></tr>";
					
					//leads
					var seller_lead_yday  = "<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".$mysql_yday." 00:00:00' AND '".$mysql_yday." 23:59:59'", null); ?>";
					var seller_lead_day  = "<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'", null); ?>";
					var seller_lead_week  = "<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and YEARWEEK(owned_date, 1) = YEARWEEK(CURDATE(), 1)", null); ?>";
					var seller_lead_month = "<?php $seller_lead_month = getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); echo $seller_lead_month; ?>";
					var seller_lead_3month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".get_month_back(date('m'),date('Y'),3)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_lead_6month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".get_month_back(date('m'),date('Y'),6)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_lead_9month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".get_month_back(date('m'),date('Y'),9)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_lead_12month = '<?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".get_month_back(date('m'),date('Y'),12)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_lead_graph_html =  '<div id="seller_lead_graph_month" style="width:100px; height:80px"></div>';
					tbl_rows += "<tr><td>Total Leads</td><td>"+seller_lead_yday+"</td><td>"+seller_lead_day+"</td><td>"+seller_lead_week+"</td><td>"+seller_lead_month+"</td><td>"+seller_lead_3month+"</td><td>"+seller_lead_6month+"</td><td>"+seller_lead_9month+"</td><td>"+seller_lead_12month+"</td><td align='center' valign='top'>" +seller_lead_graph_html + "</td></tr>";

<?php if ($_SESSION['level']==9) { ?>

					//invoices
					var seller_invoice_yday  = "<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".$mysql_yday." 00:00:00' AND '".$mysql_yday." 23:59:59'", null); ?>";
					var seller_invoice_day  = "<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".date("Y-m-d")." 00:00:00' AND '".date("Y-m-d")." 23:59:59'", null); ?>";
					var seller_invoice_week  = "<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and YEARWEEK(invoice_sent_when, 1) = YEARWEEK(CURDATE(), 1)", null); ?>";
					var seller_invoice_month = "<?php $seller_invoice_month = getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); echo $seller_invoice_month; ?>";
					var seller_invoice_3month = '<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),3)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_invoice_6month = '<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),6)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_invoice_9month = '<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),9)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_invoice_12month = '<?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".get_month_back(date('m'),date('Y'),12)." 00:00:00' AND '".$mysql_of_this_end_month." 23:59:59'", null); ?>';
					var seller_invoice_graph_html =  '<div id="seller_invoice_graph" style="width:100px; height:80px"></div>';
					tbl_rows += "<tr><td>Total Invoice</td><td>"+seller_invoice_yday+"</td><td>"+seller_invoice_day+"</td><td>"+seller_invoice_week+"</td><td>"+seller_invoice_month+"</td><td>"+seller_invoice_3month+"</td><td>"+seller_invoice_6month+"</td><td>"+seller_invoice_9month+"</td><td>"+seller_invoice_12month+"</td><td align='center' valign='top'>" +seller_invoice_graph_html + "</td></tr>";
<?php } ?>

					$("#sales_rows").html(tbl_rows);
					
					
					
					
					////////////////////////////////////
					//instatiate *speedometers*
					////////////////////////////////////
<?php if ($_SESSION['level']==9) { ?>
					var seller_invoice_graph = new JustGage(
					{
						id: "seller_invoice_graph",
						value: <?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and service_starts is not null  and service_ends is not null and invoice_sent_when BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); ?>,
						min: 0,
						max:  30,
						title: " ",
						label: "monthly"

					});
<?php } ?>
					
					<?php
					
function add_thousand($val, $decimal)
{
	return number_format( $val , $decimal , ',' , '.' );
}

//					number_format(getScalar($db,"select ifnull(REPLACE(FORMAT(sum(gen_total),0),',','.'),0) from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and offer_date_rec BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null),2);
					
					$seller_budget_graph = getScalar($db,"select ifnull(sum(gen_total),0) from offers left join clients on clients.client_id = offers.company_id where clients.is_lead=0 and offer_seller_id in (".implode(',',$user_id).") and is_paid=1 and offer_date_rec BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null);
					$seller_budget_graph_title = add_thousand($seller_budget_graph,2);
					$seller_budget_graph = str_replace(".","",$seller_budget_graph_title);
					$seller_budget_graph = str_replace(",",".",$seller_budget_graph);
//$seller_budget_graph =3942.40;
//$seller_budget_graph=(float)$seller_budget_graph;


					?>
						
					var seller_budget_graph = new JustGage(
					{
						id: "seller_budget_graph_month",
						valueFontColor : "#FFFFFF",
						title : ' <?= $seller_budget_graph_title; ?>',
						value: <?= $seller_budget_graph; ?>,
						min: 0,
						max: "70000",
						label: "month"

					});
					
					//show for all is ok
					var seller_proposal_graph = new JustGage(
					{
						id: "seller_proposal_graph_month",
						value: <?= getScalar($db,"select count(offer_id) from offers where offer_seller_id in (".implode(',',$user_id).") and offer_date_rec BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); ?>,
						min: 0,
						max: 100,
						title: " ",
						label: "monthly"

					});
					
					var seller_profile_graph_month = new JustGage(
					{
						id: "seller_profile_graph_month",
						value: <?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and profile_sent=1 and profile_sent_when BETWEEN '".date('Y-m-01')."' AND '".get_end_of_the_month(date('m'), date('Y'))."'",null); ?>,
						min: 0,
						max: 300,
						title: " ",
						label: "monthly"

					});
					
					var seller_call_graph_month = new JustGage(
					{
						id: "seller_call_graph_month",
						value: <?= getScalar($db,"select count(client_call_id) from client_calls left join clients on clients.client_id = client_calls.client_id where owner in (".implode(',',$user_id).") and client_call_datetime BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'", null); ?>,
						min: 0,
						max:   <?= 100 ?>,
						title: " ",
						label: "daily"

					});
					
					var seller_client_graph = new JustGage(
					{
						id: "seller_client_graph_month",
						value: <?= getScalar($db,"select count(client_id) from clients left join offers on offers.company_id=clients.client_id where owner in (".implode(',',$user_id).") and is_lead=0 and is_paid_when BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59'", null); ?>,
						min: 0,
						max: 30,
						title: " ",
						label: "monthly"

					});
					
					
					var seller_lead_graph = new JustGage(
					{
						id: "seller_lead_graph_month",
						value: <?= getScalar($db,"select count(client_id) from clients where owner in (".implode(',',$user_id).") and is_lead=1 and owned_date BETWEEN '".date('Y-m-d')." 00:00:00' AND '".date('Y-m-d')." 23:59:59'", null); ?>,
						min: 0,
						max: 100,
						title: " ",
						label: "daily"

					});




<?php



/////////////////////////////score is in PHP!
						$count_power_seller = getScalar($db,"SELECT count(user_working_hour_id) FROM user_working_hours WHERE user_id in (".implode(',',$user_id).") and date_start BETWEEN '".date('Y').'-'.date('m').'-01'." 00:00:00' AND '".get_end_of_the_month(date('m'), date('Y'))." 23:59:59' and  (EXTRACT(HOUR_SECOND from date_start) between 100000 and 102000) AND (EXTRACT(HOUR_SECOND from date_end) between 180500 and 235900)", null);
						
						$score  = 0;
						
//						var_dump($user_id);
//						exit;
						if ($_SESSION["level"]==9 && sizeof($user_id)==1 && ($user_id[0]==4 || $user_id[0]==5))
							 $score=10;
						else {
								$score += ($seller_call_month / 5)*0.01;
								$score += ($seller_budget_month / 2000)*0.5;
								$score += ($seller_proposal_month/5)*0.08; //0.3
								$score += ($seller_profile_month/10)*0.07; //0.3
								$score += ($seller_client_month/5)*0.4;
								$score += ($seller_lead_month/20)*0.1;
								$score += ($count_power_seller*0.05);
							}
						
						if ($score>10)
						 	$score= 10;
						 	
						$score_txt = number_format($score,2);
						
						echo "$('#score_txt').html('{$score_txt}')";
?>
