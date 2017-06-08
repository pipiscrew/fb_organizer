<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

try {
	include ('config.php');

	$db = connect();

	$r = 0;
	
//validation 1
	$f = getScalar($db,"select count(offer_advertise_detail_id) from offer_advertise_details where offer_id=?", array($_POST['offer_id']));
	
	if ($f>1)
		$r = "-error-For this offer found double record!\r\n\r\nOperation Aborted!";
	elseif ($f==0)
		$r = 0; //die($f);
	else {
		$r= getRow($db, "SELECT offer_advertise_detail_id, offer_id, user4_send_ppl_website, user4_increase_conversions, user4_boost_posts, user4_promote_page_likes, user4_get_installs_app, user4_increase_engag, user4_raise_attendance, user4_claim_offer, user4_video_views, ad_keywords, ad_client_goals, ad_fb1, ad_fb2, ad_fb3, ad_fb4, aud_countries, aud_age_min, aud_age_max, aud_gender, aud_languages, aud_interests, aud_behaviors, ad_connections, ad_placement_mobile, ad_placement_desktop, ad_placement_desktop_right, ad_placement_audience_network, DATE_FORMAT(daterec,'%d-%m-%Y %H:%i') as daterec FROM offer_advertise_details where offer_id=? limit 1", array($_POST['offer_id']));		
	}
	
/////////ad_obj - table
	//get users
	$content_users = getSet($db,"select * from users",null);
	$users_html="<option selected></option>";
	foreach($content_users as $row) {
		$users_html .= "<option value='" . $row["user_id"] . "'>" . $row["fullname"] . "</option>\r\n";
	}
	//get users
	
	$row_offer = getRow($db, "select offer_company_name,gen_a_budget,offer_contract_period,marketing_plan_comment,offer_company_manager_name,rec_guid,offer_total_amount,gen_budget,send_ppl_website, send_ppl_website_cost, increase_conversions, increase_conversions_cost, boost_posts, boost_posts_cost, promote_page_likes, promote_page_likes_cost, get_installs_app, get_installs_app_cost, increase_engag, increase_engag_cost, raise_attendance, raise_attendance_cost, claim_offer, claim_offer_cost, video_views, video_views_cost,offer_telephone,marketing_plan_location,DATE_FORMAT(marketing_plan_when,'%d-%m-%Y %H:%i') as marketing_plan_when,company_id,offer_seller_id,users.reply_mail as user_mail,offer_email from offers left join users on users.user_id = offers.offer_seller_id where offer_id=?", array($_POST['offer_id']));
	$ad_obj = get_services_table($row_offer,$users_html);
	//ad_obj - table
	
    //unicode
    header("Content-Type: application/json", true);
    
    $json                = array('rec'=> $r,'ad_obj' => $ad_obj);
    
	echo json_encode($json);

	
} catch (exception $e) {
    echo "-error-exception:".$e->getMessage();
}


function get_services_table($prop,$users){
	
	
$template = "<table width='800px' style='border-collapse: collapse;border-width: 1px; border-color: #c0c0c0;margin-bottom:20px;'>
	<tr>
		<td style='background:#376092; width:200px; padding:7px; color:#fff; font:bold 20px/14px Arial;' align='center'>Ad Manager</td>
		<td style='background:#376092; width:600px; padding:7px; color:#fff; font:bold 20px/14px Arial;' align='center'>Ad Objectives</td>
		<td style='background:#376092; width:100px; color:#fff; font:bold 20px/14px Arial;' align='center'>%</td><td style='background:#376092; width:100px; color:#fff; font:bold 20px/14px Arial;' align='center'>Daily</td><td style='background:#376092; width:100px; color:#fff; font:bold 20px/14px Arial;' align='center'>Weekly</td><td style='background:#376092;width:100px; color:#fff; font:bold 20px/14px Arial;' align='center'>Monthly</td><td style='background:#376092; width:100px; color:#fff; font:bold 20px/14px Arial;' align='center'>Contract</td>
	</tr>";
	
if ($prop["send_ppl_website"] > 0)
				$template .= "<tr>
						<td><select name='user4_send_ppl_website'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row1.png' style='margin-right:5px'> Send people to your website</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{send_ppl_website}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{send_ppl_website_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center' align='center'>{send_ppl_website_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{send_ppl_website_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{send_ppl_website_cost}</td>
					</tr>";
					
if ($prop["increase_conversions"] > 0)
				$template .= "<tr>
						<td><select name='user4_increase_conversions'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row2.png' style='margin-right:5px'> Increase conversions</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_conversions}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_conversions_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_conversions_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_conversions_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_conversions_cost}</td>
					</tr>";
					
if ($prop["boost_posts"] > 0)
				$template .= "<tr>
						<td><select name='user4_boost_posts'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row3.png' style='margin-right:5px'> Boost your posts</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{boost_posts}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{boost_posts_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{boost_posts_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{boost_posts_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{boost_posts_cost}</td>
					</tr>";
					
if ($prop["promote_page_likes"] > 0)
				$template .= "<tr>
						<td><select name='user4_promote_page_likes'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row4.png' style='margin-right:5px'> Promote your Page</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{promote_page_likes}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{promote_page_likes_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{promote_page_likes_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{promote_page_likes_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{promote_page_likes_cost}</td>
					</tr>";

if ($prop["get_installs_app"] > 0)
				$template .= "<tr>
						<td><select name='user4_get_installs_app'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row5.png' style='margin-right:5px'> Get installs of your app</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{get_installs_app}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{get_installs_app_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{get_installs_app_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{get_installs_app_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{get_installs_app_cost}</td>
					</tr>";

if ($prop["increase_engag"] > 0)
				$template .= "<tr>
						<td><select name='user4_increase_engag'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row6.png' style='margin-right:5px'> Increase engagement in your app</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_engag}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_engag_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_engag_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_engag_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{increase_engag_cost}</td>
					</tr>";

if ($prop["raise_attendance"] > 0)
				$template .= "<tr>
						<td><select name='user4_raise_attendance'>{$users}</select></td> 
						<td style='width:500px;vertical-align:middle;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row7.png' style='margin-right:5px'> Raise attendance at your event</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{raise_attendance}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{raise_attendance_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{raise_attendance_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{raise_attendance_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{raise_attendance_cost}</td>
					</tr>";

if ($prop["claim_offer"] > 0)
				$template .= "<tr>
						<td><select name='user4_claim_offer'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row8.png' style='margin-right:5px'> Get people to claim your offer</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{claim_offer}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{claim_offer_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{claim_offer_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{claim_offer_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{claim_offer_cost}</td>
					</tr>";
					
if ($prop["video_views"] > 0)
				$template .= "<tr>
						<td><select name='user4_video_views'>{$users}</select></td> 
						<td style='width:500px;border-width: 1px;border-style: solid;border-color: #c0c0c0;'><img src='../api/img/row9.png' style='margin-right:5px'> Get video views</td>
						<td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{video_views}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{video_views_daily}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{video_views_weekly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{video_views_monthly}</td><td style='width:100px;border-width: 1px;border-style: solid;border-color: #c0c0c0;' align='center'>{video_views_cost}</td>
					</tr>";

$template .= "</table>";

		$template = str_replace('{send_ppl_website}', $prop["send_ppl_website"], $template);
		$template = str_replace('{send_ppl_website_cost}', $prop["send_ppl_website_cost"]."€", $template);
		
		$template = str_replace('{increase_conversions}', $prop["increase_conversions"], $template);
		$template = str_replace('{increase_conversions_cost}', $prop["increase_conversions_cost"]."€", $template);
		
		$template = str_replace('{boost_posts}', $prop["boost_posts"], $template);
		$template = str_replace('{boost_posts_cost}', $prop["boost_posts_cost"]."€", $template);
		
		$template = str_replace('{promote_page_likes}', $prop["promote_page_likes"], $template);
		$template = str_replace('{promote_page_likes_cost}', $prop["promote_page_likes_cost"]."€", $template);
		
		$template = str_replace('{get_installs_app}', $prop["get_installs_app"], $template);
		$template = str_replace('{get_installs_app_cost}', $prop["get_installs_app_cost"]."€", $template);
		
		$template = str_replace('{increase_engag}', $prop["increase_engag"], $template);
		$template = str_replace('{increase_engag_cost}', $prop["increase_engag_cost"]."€", $template);
		
		$template = str_replace('{raise_attendance}', $prop["raise_attendance"], $template);
		$template = str_replace('{raise_attendance_cost}', $prop["raise_attendance_cost"]."€", $template);
		
		$template = str_replace('{claim_offer}', $prop["claim_offer"], $template);
		$template = str_replace('{claim_offer_cost}', $prop["claim_offer_cost"]."€", $template);
		
		$template = str_replace('{video_views}', $prop["video_views"], $template);
		$template = str_replace('{video_views_cost}', $prop["video_views_cost"]."€", $template);
		
		//the contract details used for all objective types
		$x_months = $prop["offer_contract_period"];
		$x_budget = $prop["gen_a_budget"];
		//the contract details used for all objective types
		
//		$x_total_budget = $x_months * $x_budget;
		
		$x_send_ppl_website = get_daily_weekly_monthly($x_budget, $x_months, $prop["send_ppl_website"]);
		$template = str_replace('{send_ppl_website_daily}',$x_send_ppl_website["daily"], $template);
		$template = str_replace('{send_ppl_website_weekly}', $x_send_ppl_website["weekly"], $template);
		$template = str_replace('{send_ppl_website_monthly}', $x_send_ppl_website["monthly"], $template);

		$x_increase_conversions = get_daily_weekly_monthly($x_budget, $x_months, $prop["increase_conversions"]);
		$template = str_replace('{increase_conversions_daily}',$x_increase_conversions["daily"], $template);
		$template = str_replace('{increase_conversions_weekly}', $x_increase_conversions["weekly"], $template);
		$template = str_replace('{increase_conversions_monthly}', $x_increase_conversions["monthly"], $template);

		$x_boost_posts = get_daily_weekly_monthly($x_budget, $x_months, $prop["boost_posts"]);
		$template = str_replace('{boost_posts_daily}',$x_boost_posts["daily"], $template);
		$template = str_replace('{boost_posts_weekly}', $x_boost_posts["weekly"], $template);
		$template = str_replace('{boost_posts_monthly}', $x_boost_posts["monthly"], $template);

		$x_promote_page_likes = get_daily_weekly_monthly($x_budget, $x_months, $prop["promote_page_likes"]);
		$template = str_replace('{promote_page_likes_daily}',$x_promote_page_likes["daily"], $template);
		$template = str_replace('{promote_page_likes_weekly}', $x_promote_page_likes["weekly"], $template);
		$template = str_replace('{promote_page_likes_monthly}', $x_promote_page_likes["monthly"], $template);

		$x_get_installs_app = get_daily_weekly_monthly($x_budget, $x_months, $prop["get_installs_app"]);
		$template = str_replace('{get_installs_app_daily}',$x_get_installs_app["daily"], $template);
		$template = str_replace('{get_installs_app_weekly}', $x_get_installs_app["weekly"], $template);
		$template = str_replace('{get_installs_app_monthly}', $x_get_installs_app["monthly"], $template);
		
		$x_increase_engag = get_daily_weekly_monthly($x_budget, $x_months, $prop["increase_engag"]);
		$template = str_replace('{increase_engag_daily}',$x_increase_engag["daily"], $template);
		$template = str_replace('{increase_engag_weekly}', $x_increase_engag["weekly"], $template);
		$template = str_replace('{increase_engag_monthly}', $x_increase_engag["monthly"], $template);
		
		$x_raise_attendance = get_daily_weekly_monthly($x_budget, $x_months, $prop["raise_attendance"]);
		$template = str_replace('{raise_attendance_daily}',$x_raise_attendance["daily"], $template);
		$template = str_replace('{raise_attendance_weekly}', $x_raise_attendance["weekly"], $template);
		$template = str_replace('{raise_attendance_monthly}', $x_raise_attendance["monthly"], $template);
		
		$x_claim_offer = get_daily_weekly_monthly($x_budget, $x_months, $prop["claim_offer"]);
		$template = str_replace('{claim_offer_daily}',$x_claim_offer["daily"], $template);
		$template = str_replace('{claim_offer_weekly}', $x_claim_offer["weekly"], $template);
		$template = str_replace('{claim_offer_monthly}', $x_claim_offer["monthly"], $template);
		
		$x_video_views = get_daily_weekly_monthly($x_budget, $x_months, $prop["video_views"]);
		$template = str_replace('{video_views_daily}',$x_video_views["daily"], $template);
		$template = str_replace('{video_views_weekly}', $x_video_views["weekly"], $template);
		$template = str_replace('{video_views_monthly}', $x_video_views["monthly"], $template);

return $template;
}

function get_daily_weekly_monthly($budget, $months, $percentage)
{
		$x_total_budget = $months * $budget;
		
		
		$x_objective_r = $x_total_budget * ($percentage / 100);
		$x_objective_days = ($x_objective_r / ($months * 30));
		$x_objective_weeks = ($x_objective_r / ($months * 4));
		$x_objective_month = ($x_objective_r / $months);
		
		$x_objective_days = add_thousand($x_objective_days,2);
		$x_objective_weeks = add_thousand($x_objective_weeks,2);
		$x_objective_month = add_thousand($x_objective_month,2);
		
		return array("daily"=>$x_objective_days."€","weekly" => $x_objective_weeks."€","monthly" => $x_objective_month."€");
}

function add_thousand($val, $decimal)
{
	return number_format( $val , $decimal , ',' , '.' );
}
?>