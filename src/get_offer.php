<?php

//Likes
//Engagement
//Branding
//Website Clicks
//Apps
//Sales


if(!isset($_POST['page_url']) || !isset($_POST['email']) || !isset($_POST['telephone']) || !isset($_POST['country']) ||
	!isset($_POST['category']) || !isset($_POST['contract']) || !isset($_POST['budget']) || !isset($_POST['ad_type']) ||
	!isset($_POST['proposaldate']) || !isset($_POST['proposaldatetill']) || !isset($_POST['city']) || !isset($_POST['company_manager_name']) ||
	!isset($_POST['company_name']) || !isset($_POST['apps']) || !isset($_POST['post_manage']) || !isset($_POST['offer_type']) || !isset($_POST['seller_id']) ||
	!isset($_POST['app_ad_budget']) || !isset($_POST['language']) ||
	
	!isset($_POST['send_ppl_website']) || !isset($_POST['increase_conversions']) || !isset($_POST['boost_posts']) || !isset($_POST['promote_page_likes']) || !isset($_POST['get_installs_app']) || !isset($_POST['increase_engag']) || !isset($_POST['raise_attendance']) || !isset($_POST['claim_offer']) || !isset($_POST['video_views'])
	) 	
	{


	echo json_encode(array("error"=> "ERROR : Variable is not defined! \r\n\r\nOperation Aborted!"));
	exit;
}
else {
	//contract period
	$contract= $_POST['contract'];
	
	//app count 
	$apps = $_POST['apps'];
	
	$send_ppl_website = intval($_POST["send_ppl_website"]);
	$increase_conversions = intval($_POST["increase_conversions"]);
	$boost_posts = intval($_POST["boost_posts"]);
	$promote_page_likes = intval($_POST["promote_page_likes"]);
	$get_installs_app = intval($_POST["get_installs_app"]);
	$increase_engag = intval($_POST["increase_engag"]);
	$raise_attendance = intval($_POST["raise_attendance"]);
	$claim_offer = intval($_POST["claim_offer"]);
	$video_views = intval($_POST["video_views"]);
	
	$percentage_calculation = $send_ppl_website + $increase_conversions + $boost_posts + $promote_page_likes + $get_installs_app + $increase_engag + $raise_attendance + $claim_offer + $video_views;
	
	if ($percentage_calculation>100)
	{
		echo json_encode(array("error"=> "ERROR : The objective percentages is more than 100%!!\r\n\r\nOperation Aborted!"));
		exit;
	}
	
	//////////////BUDGET///////////////////
	if (isset($_POST['budget']))
		$budget  = $_POST['budget'];
	else 
		$budget=0;
		
	if ($budget==null)
		$budget=0;
	//////////////BUDGET///////////////////
	
	// >>9/3/2015 *hotfix* offer with post managment, one more 151.1 on AD FEE
	if ($budget==151.1)
	{
		$budget=$total_budget=$adsfee_val=$adsfee_total=$adsfee_val_discount_money=0;
	}
	
	//////////////EXTRA BUDGET///////////////////
	if (isset($_POST['extra_budget']) && $apps>0)
		$apps_price  = $_POST['extra_budget'];
	else 
		$apps_price=0;
		
	if ($apps_price==null)
		$apps_price=0;
	//////////////EXTRA BUDGET///////////////////
		
	//total budget	
	$total_budget = $budget * $contract;

	//calculate the cost for each objective
	$promote_page_likes_cost = percentage_of_value($total_budget, $promote_page_likes);
	$send_ppl_website_cost = percentage_of_value($total_budget, $send_ppl_website);
	$increase_conversions_cost = percentage_of_value($total_budget, $increase_conversions);
	$boost_posts_cost = percentage_of_value($total_budget, $boost_posts);
	$get_installs_app_cost = percentage_of_value($total_budget, $get_installs_app);
	$increase_engag_cost = percentage_of_value($total_budget, $increase_engag);
	$raise_attendance_cost = percentage_of_value($total_budget, $raise_attendance);
	$claim_offer_cost = percentage_of_value($total_budget, $claim_offer);
	$video_views_cost = percentage_of_value($total_budget, $video_views);
	//calculate the cost for each objective
	
	//$_POST['app_ad_budget']
	$app_ad_budget = 0;
	
	if (isset($_POST['app_ad_budget']) && $apps>0)
		$app_ad_budget = $_POST['app_ad_budget'];
		
	$total_budget = $total_budget + $app_ad_budget;
}

date_default_timezone_set("UTC");

$PG_TAT   = 0;
$PG_LIKES = 0;

try
{

	$details     = getFB_detail_page($_POST['page_url']);
	$detailsJSON = json_decode($details);

	if (array_key_exists('talking_about_count', $detailsJSON))
		$PG_TAT      = $detailsJSON->talking_about_count;
	else 
		$PG_TAT      = 	$_POST['unknown_tat'];
		
	if (array_key_exists('likes', $detailsJSON))
		$PG_LIKES    = $detailsJSON->likes;
	else 
		$PG_LIKES      = 	$_POST['unknown_likes'];

} catch(Exception $e){
	echo $e->getMessage();
	exit;
}



require_once ('0qCE7mL9_config.php');
require_once ('config_general.php');

$ad_type = $_POST['ad_type'];

	
$db      = connect();



if ($PG_TAT==null)
	$PG_TAT=0;
	
	
$discount_perc = intval($_POST['discount']);

////////////////////////////////////////// GET TAT
$tat     = getScalar2($db, "select calc_val_min, calc_val_max from tat where show_val_min<=? and show_val_max>=?",array($PG_TAT,$PG_TAT));
 
$db_tat_min = (float) $tat[0];
$db_tat_max = (float) $tat[1];
 
$new_tat_min = 0;
$new_tat_max = 0;


$new_tat_min = $db_tat_min;
$new_tat_max = $db_tat_max;

////////////////////////////////////////// GET TAT


////////////////////////////////////////// GET IMPRESSIONS
$impression       = getScalar2($db, "select impression_calc_val_min, impression_calc_val_max from impressions where impression_show_val_min<=? and impression_show_val_max>=?",array($budget,$budget));
$db_impression_min = (float) $impression[0];
$db_impression_max = (float) $impression[1];
////////////////////////////////////////// GET IMPRESSIONS

////////////////////////////////////////// GET APP IMPRESSIONS
$app_impression_min=$app_impression_max=0;
if ($app_ad_budget>0){
	$appimpression       = getScalar2($db, "select app_impression_calc_val_min, app_impression_calc_val_max from app_impressions where app_impression_show_val_min<=? and app_impression_show_val_max>=?",array($app_ad_budget,$app_ad_budget));
	$db_appimpression_min =  $appimpression[0];
	$db_appimpression_max =  $appimpression[1];	
	
	$app_impression_min = (int) $db_appimpression_min / $apps;
	$app_impression_max = (int) $db_appimpression_max / $apps;
}

////////////////////////////////////////// GET APP IMPRESSIONS

////////////////////////////////////////// GET LIKES
if (!empty($_POST["promote_page_likes"])){

	$category = getScalar3($db, "select category_min, category_max, category_name from categories where category_id=?", array($_POST['category']));

	$country = getScalar3($db, "select country_min, country_max, country_name from countries where country_id=?", array($_POST['country']));
	$db_country_min = (float) $country[0];
	$db_country_max = (float) $country[1];
	$db_country_txt = $country[2];

	$new_like_min= ($promote_page_likes_cost / $db_country_min) + $category[0];
	$new_like_max= ($promote_page_likes_cost / $db_country_max) + $category[1];
}
else {
	$new_like_min = $new_like_max = 0;
	
}
////////////////////////////////////////// GET LIKES

////////////////////////////////////////// WEBCLICKS
$db_web_click_min=$db_web_click_max=0;
if (!empty($send_ppl_website) || !empty($increase_conversions))
{
	$total_web_click = $send_ppl_website_cost + $increase_conversions_cost;
	
	$web_click       = getScalar2($db, "select web_click_calc_val_min, web_click_calc_val_max from web_clicks where web_click_show_val_min<=? and web_click_show_val_max>=?",array($total_web_click,$total_web_click));
	$db_web_click_min = (int) $web_click[0];
	$db_web_click_max = (int) $web_click[1];
	


}
////////////////////////////////////////// WEBCLICKS


///////////////////////////////////////// APPS
		$apps_page_count = 0 ;
		$apps_page_merge = 0 ;
		$apps_name_change = 0 ;

		 if(isset($_POST["page_merge"]) )
		 {
		 	$apps_page_count += 1;
		 	$apps_page_merge = 1;
		 }

		 if(isset($_POST["name_change"]) ){
			 $apps_page_count += 1;
		 	 $apps_name_change = 1;
		 }
		

		$apps_page_count_price = $apps_page_count * 250;
		$apps_page_count_price_total =  $apps_price+$apps_page_count_price;
///////////////////////////////////////// APPS

		



////////////////////////////////////////////////COMMON

if ($budget==151.1)
{
	$budget=$total_budget=$adsfee_val=$adsfee_total=$adsfee_val_discount_money=0;
}
else {
	
	//AD FEE
	$adsfee_val = getScalar($db, "select adfee_val from ad_fees where adfee_show_val_min<=? and adfee_show_val_max>=?",array($budget,$budget));
	$adsfee_total = $adsfee_val * $contract;

	$adsfee_val_discount_money = 0;
	
	if ($discount_perc>0)
	{
		$adsfee_val_discount_money = $adsfee_total * ($discount_perc / 100);
		//$adsfee_total_with_discount = $adsfee_total - $adsfee_val_discount_money;
	}
}		
			
	//post_manage
	$post_manage = $_POST["post_manage"];
	$post_manage_val=0;

	switch ($post_manage){
	case 2 :
		$post_manage_val=160;
		break;
	case 3 :
		$post_manage_val=190;//230;
		break;
	case 4:
		$post_manage_val=250;//290;
		break;
	case 7 :
		$post_manage_val=440;
		break;
	case 14 :
		$post_manage_val=690;
		break;
	default :
		$post_manage_val=0;
		break;
		}
			
	$post_manage_total = $post_manage_val * $contract;
	
	//language
	if ($_POST["language"]!= "Greek")
	{
		$post_manage_val = $post_manage_val+ 50;
		$post_manage_total = $post_manage_total + ( 50 * $contract);
	}
	
	//graphics
	$graphics_switch = "0";

	if (isset($_POST['graphics_sw'])) {
		if ($_POST['graphics_sw'] == "on")
			$graphics_switch = 1;
		else
			$graphics_switch = "0";
	}
	
	if($graphics_switch==1)
	{
		$post_manage_val = $post_manage_val+ 50;
		$post_manage_total = $post_manage_total + ( 50 * $contract);
	}
/////////////////////////////////////////

//subtotal
$subtotal = $apps_page_count_price_total+$post_manage_total+$total_budget+$adsfee_total;

//tax
$tax_val=0;
if ($_POST['country']==5)
{
	//when UK only add TAX
	$tax_val = $subtotal*0.2;
}


//total
//$total_proposal = $tax_val + $subtotal;
$total_proposal = $tax_val + ($subtotal-$adsfee_val_discount_money);


if (isset($_POST['save_rec']) && $_POST['save_rec'] == 1){
	
	$t_fee_discount_val = "";
	if ($adsfee_val_discount_money>0)
		$t_fee_discount_val =  ' (-'.$adsfee_val_discount_money.'€)';
	
	//@ html needs jQuery submit, will see!
	echo json_encode(array('TAT'=>array((int)$new_tat_min,(int)$new_tat_max),'IMPRESSIONS'=>array((int)$db_impression_min,(int)$db_impression_max),
	'APPIMPRESSIONS'=>array((int)$app_impression_min,(int)$app_impression_max),
	'LIKES'=>array((int)$new_like_max,(int)$new_like_min),
	'WEBCLICKS'=>array((int) $db_web_click_min, (int) $db_web_click_max),
	'T_FEES'=>array(add_thousand($adsfee_total,2) .'€'. $t_fee_discount_val ),
	'T_AD_BUDGET'=> array(add_thousand($total_budget,2).'€'),
	'T_POST'=> array(add_thousand($post_manage_total,2).'€'),
	'T_APPS'=> array(add_thousand($apps_page_count_price_total,2).'€'),
	'T_PROP'=> array(add_thousand($total_proposal,2).'€') ));
}
elseif (isset($_POST['save_rec']) && $_POST['save_rec'] == 2){
//save as doc

		////////////////////////////////////////// GET SellerName
		$seller = getScalar($db, "select fullname from users where user_id=?",array($_POST['seller_id']));
		////////////////////////////////////////// GET SellerName
		
	$comment = "";

	$company_id=0;

	if (isset($_POST['comment']))
		$comment=$_POST['comment'];
	
	if(isset($_POST['cust_id']) && !empty($_POST['cust_id']))
	{
		$offer_id=$_POST['cust_code'];
		$company_id=$_POST['cust_id'];
	}

	//get current customer OFFERS count
	$prop_count = getScalar($db, "select count(offer_id)+1 from offers where company_id=?", array($company_id));


//////////////////SQL

$proposaldate=null;
if (!empty($_POST['proposaldate']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['proposaldate']);
	
	$proposaldate =	$dt->format('Y-m-d');
}

$proposaldatetill=null;
if (!empty($_POST['proposaldatetill']))
{
	$dt = DateTime::createFromFormat('d-m-Y', $_POST['proposaldatetill']);
	
	$proposaldatetill =	$dt->format('Y-m-d');
}

$guid = guid();
$guid_solution = guid_solution();

//save to db
	$db = new PDO("mysql:host=localhost:3307;dbname=test", "root", "usbw",
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
                    
	$sql = "INSERT INTO `offers` (offer_no, graphics_switch, offer_total_amount, gen_webclicks_min, gen_webclicks_max, language, gen_fee_discount_percentage,gen_fee_discount_money, offer_seller_name, offer_date_rec, offer_page_url, offer_company_name, offer_company_manager_name, offer_proposal_date, offer_proposal_valid_date, offer_posting_management, offer_apps, offer_apps_page_merge, offer_apps_name_change, offer_city, offer_email, offer_telephone, country_id, category_id, offer_contract_period, offer_adverts_platform, gen_page, gen_aprice, gen_budget, gen_fee, gen_subtotal, gen_tax, gen_total, gen_olikes, gen_otat, gen_n1likes, gen_n2likes, gen_n1tat, gen_n2tat, gen_n1views, gen_n2views, comment,offer_apps_page_merge_url_one,offer_apps_page_merge_url_two,offer_apps_name_change_old_page_name,offer_apps_name_change_old_page_url,offer_apps_name_change_new_page_name,offer_apps_name_change_new_page_url, company_id, gen_a_fee, gen_a_budget, gen_extra_budget, gen_a_pprice, gen_pprice, offer_type, offer_seller_id, apps, rec_guid, rec_guid_answer, apps_name_change, apps_page_merge, gen_aprice_app_cost, gen_aprice_pages_cost, app_impression_min, app_impression_max, app_ad_budget, send_ppl_website ,send_ppl_website_cost ,increase_conversions ,increase_conversions_cost ,boost_posts ,boost_posts_cost ,promote_page_likes ,promote_page_likes_cost ,get_installs_app ,get_installs_app_cost ,increase_engag ,increase_engag_cost ,raise_attendance ,raise_attendance_cost ,claim_offer ,claim_offer_cost ,video_views ,video_views_cost) VALUES (:offer_no, :graphics_switch, :offer_total_amount, :gen_webclicks_min, :gen_webclicks_max, :language, :gen_fee_discount_percentage, :gen_fee_discount_money, :offer_seller_name, :offer_date_rec, :offer_page_url, :offer_company_name, :offer_company_manager_name, :offer_proposal_date, :offer_proposal_valid_date, :offer_posting_management, :offer_apps, :offer_apps_page_merge, :offer_apps_name_change, :offer_city, :offer_email, :offer_telephone, :country_id, :category_id, :offer_contract_period, :offer_adverts_platform, :gen_page, :gen_aprice, :gen_budget, :gen_fee, :gen_subtotal, :gen_tax, :gen_total, :gen_olikes, :gen_otat, :gen_n1likes, :gen_n2likes, :gen_n1tat, :gen_n2tat, :gen_n1views, :gen_n2views, :comment, :offer_apps_page_merge_url_one, :offer_apps_page_merge_url_two, :offer_apps_name_change_old_page_name, :offer_apps_name_change_old_page_url, :offer_apps_name_change_new_page_name, :offer_apps_name_change_new_page_url, :company_id, :gen_a_fee, :gen_a_budget, :gen_extra_budget, :gen_a_pprice, :gen_pprice, :offer_type, :offer_seller_id, :apps, :rec_guid, :rec_guid_answer, :apps_name_change, :apps_page_merge, :gen_aprice_app_cost, :gen_aprice_pages_cost, :app_impression_min, :app_impression_max, :app_ad_budget, :send_ppl_website, :send_ppl_website_cost, :increase_conversions, :increase_conversions_cost, :boost_posts, :boost_posts_cost, :promote_page_likes, :promote_page_likes_cost, :get_installs_app, :get_installs_app_cost, :increase_engag, :increase_engag_cost, :raise_attendance, :raise_attendance_cost, :claim_offer, :claim_offer_cost, :video_views, :video_views_cost)";
	$stmt = $db->prepare($sql);
	
	$stmt->bindValue(':offer_no' , $offer_id."_".$prop_count);
	$stmt->bindValue(':offer_seller_name' , $seller);
	$stmt->bindValue(':offer_date_rec' , date('Y-m-d G:i'));
	$stmt->bindValue(':offer_page_url' , $_POST['page_url']);
	$stmt->bindValue(':offer_company_name' , $_POST['company_name']);
	$stmt->bindValue(':offer_company_manager_name' , $_POST['company_manager_name']);
	$stmt->bindValue(':offer_proposal_date' , $proposaldate);
	$stmt->bindValue(':offer_proposal_valid_date' , $proposaldatetill);
	$stmt->bindValue(':offer_posting_management' , $_POST['post_manage']);
	$stmt->bindValue(':offer_apps' , $_POST['apps']);

	$stmt->bindValue(':offer_apps_page_merge' , isset($_POST["page_merge"])?1:0);
	$stmt->bindValue(':offer_apps_name_change' , isset($_POST["name_change"])?1:0);

	$stmt->bindValue(':offer_city' , $_POST['city']);
	$stmt->bindValue(':offer_email' , $_POST['email']);
	$stmt->bindValue(':offer_telephone' , $_POST['telephone']);
	$stmt->bindValue(':country_id' , $_POST['country']);
	$stmt->bindValue(':category_id' , $_POST['category']);
	$stmt->bindValue(':offer_contract_period' , $_POST['contract']);
	$stmt->bindValue(':offer_adverts_platform' , $_POST['ad_type']);


	$stmt->bindValue(':gen_page' , $apps_page_count);
	$stmt->bindValue(':gen_aprice' , $apps_page_count_price_total);
	$stmt->bindValue(':gen_budget' , $total_budget);
	$stmt->bindValue(':gen_fee' , $adsfee_total);

//
	$stmt->bindValue(':gen_a_fee' , $adsfee_val);	
	$stmt->bindValue(':gen_fee_discount_money' , $adsfee_val_discount_money);
	$stmt->bindValue(':gen_fee_discount_percentage' , $discount_perc);
//

	$stmt->bindValue(':gen_subtotal' , $subtotal);
	
	$stmt->bindValue(':gen_tax' , $tax_val);
	$stmt->bindValue(':gen_total' , $total_budget);
	$stmt->bindValue(':gen_olikes' , $PG_LIKES);
	$stmt->bindValue(':gen_otat' , $PG_TAT);
	$stmt->bindValue(':gen_n1likes' , $new_like_max);
	$stmt->bindValue(':gen_n2likes' , $new_like_min);
	$stmt->bindValue(':gen_n1tat' , $new_tat_min);
	$stmt->bindValue(':gen_n2tat' , $new_tat_max);

	$stmt->bindValue(':gen_n1views' , $db_impression_min);
	$stmt->bindValue(':gen_n2views' , $db_impression_max);
	$stmt->bindValue(':comment' , $comment);
	
	$stmt->bindValue(':offer_apps_page_merge_url_one' , $_POST['page_one']);
	$stmt->bindValue(':offer_apps_page_merge_url_two' , $_POST['page_two']);

	$stmt->bindValue(':offer_apps_name_change_old_page_name' , $_POST['old_page_name']);
	$stmt->bindValue(':offer_apps_name_change_old_page_url' , $_POST['old_url']);
	$stmt->bindValue(':offer_apps_name_change_new_page_name' , $_POST['new_page_name']);
	$stmt->bindValue(':offer_apps_name_change_new_page_url' , $_POST['new_url']);
	$stmt->bindValue(':company_id' , $company_id);
	
	
	$stmt->bindValue(':gen_a_budget' , $budget);
	$stmt->bindValue(':gen_extra_budget' , $apps_price);
	$stmt->bindValue(':gen_a_pprice' , $post_manage_val);
	$stmt->bindValue(':gen_pprice' , $post_manage_total);
	
	$stmt->bindValue(':app_impression_min' , $app_impression_min);
	$stmt->bindValue(':app_impression_max' , $app_impression_max);
		
	//1-New
	//2-Update
	//3-Renewal
	$stmt->bindValue(':offer_type' , $_POST['offer_type']);
	
	$stmt->bindValue(':offer_seller_id' , $_POST['seller_id']);
	
	
	
	$stmt->bindValue(':apps' , $apps);
	
	$stmt->bindValue(':rec_guid' , $guid);
	$stmt->bindValue(':rec_guid_answer' , $guid_solution);
	
	$stmt->bindValue(':apps_name_change' , $apps_name_change);
	$stmt->bindValue(':apps_page_merge' , $apps_page_merge);
	
	$stmt->bindValue(':gen_aprice_app_cost' , $apps_page_count_price);
	$stmt->bindValue(':gen_aprice_pages_cost' , $apps_page_count_price);
	
	$stmt->bindValue(':app_ad_budget' , $app_ad_budget);
	$stmt->bindValue(':language' , $_POST['language']);
	$stmt->bindValue(':gen_webclicks_min' , $db_web_click_min);
	$stmt->bindValue(':gen_webclicks_max' , $db_web_click_max);
	
	$stmt->bindValue(':send_ppl_website' , $send_ppl_website);
	$stmt->bindValue(':send_ppl_website_cost' , $send_ppl_website_cost);
	$stmt->bindValue(':increase_conversions' , $increase_conversions);
	$stmt->bindValue(':increase_conversions_cost' , $increase_conversions_cost);
	$stmt->bindValue(':boost_posts' , $boost_posts);
	$stmt->bindValue(':boost_posts_cost' , $boost_posts_cost);
	$stmt->bindValue(':promote_page_likes' , $promote_page_likes);
	$stmt->bindValue(':promote_page_likes_cost' , $promote_page_likes_cost);
	$stmt->bindValue(':get_installs_app' , $get_installs_app);
	$stmt->bindValue(':get_installs_app_cost' , $get_installs_app_cost);
	$stmt->bindValue(':increase_engag' , $increase_engag);
	$stmt->bindValue(':increase_engag_cost' , $increase_engag_cost);
	$stmt->bindValue(':raise_attendance' , $raise_attendance);
	$stmt->bindValue(':raise_attendance_cost' , $raise_attendance_cost);
	$stmt->bindValue(':claim_offer' , $claim_offer);
	$stmt->bindValue(':claim_offer_cost' , $claim_offer_cost);
	$stmt->bindValue(':video_views' , $video_views);
	$stmt->bindValue(':video_views_cost' , $video_views_cost);
	
	$stmt->bindValue(':graphics_switch' , $graphics_switch);
	
	
	$stmt->bindValue(':offer_total_amount' , $total_proposal);

	
	//save to db	
	$stmt->execute();


	//get lastinsertedID
	$last_id = $db->lastInsertId();


	  if(!is_numeric($last_id)){
	  	send_mailERROR("ERROR!!","Record is not saved, lastInsertId is not numeric!!");
	  	die('Record is not saved, lastInsertId is not numeric!!...');
	  }

//write to log
//must connect without config.php because has the same function name!
//	$mysql_hostname = "localhost";
//	$mysql_user = "root";
//	$mysql_password = "usbw";
//	$mysql_database = "test"; 
//	
//	$dbPDO = new PDO("mysql:host=$mysql_hostname;dbname=$mysql_database", $mysql_user, $mysql_password, 
//  array(
//    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
//  ));
  
//write_log($dbPDO, $log_type, $log_text, $client_id = null, $user_id = null)
write_log($db, 5, $_SERVER['REMOTE_ADDR']." read the offer ({$offer_id}) for company ".$_POST['company_name']." (".$_POST['page_url'].")" , $company_id, $_POST['seller_id']);

//echo "recced! ".$last_id;
//send_mail_to_user($_POST['email'], "")
//echo $last_id;

echo json_encode(array('last_id' => $last_id, 'guid' =>$guid, 'guid_solution' => $guid_solution));
}
else {

	
}





function sum_percentage($init_val,$percentage)
{
	$res = $init_val + ($init_val * ($percentage / 100));

	return $res;
}

function percentage_of_value($init_val,$percentage)
{
	$res =($init_val * ($percentage / 100));

	return $res;
}

function abstraction_percentage($init_val,$percentage)
{
	$res = $init_val - ($init_val * ($percentage / 100));

	return $res;
}

function getScalar2($db, $sql, $params)
{
	if($stmt = $db -> prepare($sql)){
		$types = str_repeat('s', count($params));

		if($params != null)
		bind_param_array($stmt, $types, $params);

		$stmt -> execute();

		$stmt->bind_result($f1,$f2);

		$stmt->fetch();
		$stmt -> close();

		return array($f1,$f2);
	}
	else
	return 0;
}

function getScalar3($db, $sql, $params)
{
	if($stmt = $db -> prepare($sql)){
		$types = str_repeat('s', count($params));

		if($params != null)
		bind_param_array($stmt, $types, $params);

		$stmt -> execute();

		$stmt->bind_result($f1,$f2,$f3);

		$stmt->fetch();
		$stmt -> close();

		return array($f1,$f2,$f3);
	}
	else
	return 0;
}

//https://developers.facebook.com/docs/graph-api/reference - inside on each function writes the API version supported
function getFB_detail_page($id)
{
	// Get cURL resource
	$curl = curl_init();
	// Set some options - we are passing in a useragent too here
	curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER=> 1,
			CURLOPT_URL           => 'https://graph.facebook.com/'.$id,
			CURLOPT_USERAGENT     => 'Mozilla/5.0 (compatible; ABrowse 0.4; Syllable)'
		));

	// Send the request & save response to $resp
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);

	return $resp;
}

function sum_and_div($no1,$no2)
{
	try {
		$m=$no1+$no2;
		return (int)$m/2;		
	} 
	catch (Exception $e)
	{
		return 0;
	}
}

function add_thousand($val, $decimal)
{
	return number_format( $val , $decimal , ',' , '.' );
}

function send_mailERROR($subject, $body)
{
	$headers = "From: costas@pipiscrew.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
	$message = '<html><body>'.$body;
	$message .= '</body></html>';

    if (mail("c.pipilios@pipiscrew.com", $subject, $message, $headers)) {
      return true;
    } else {
      return false;
    }
}

//function guid(){
//    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
//    $charid = strtoupper(md5(uniqid(rand(), true)));
//    $uuid =  substr($charid, 0, 8)
//            .substr($charid, 8, 4)
//            .substr($charid,12, 4)
//            .substr($charid,16, 4)
//            .substr($charid,20,12);
//	
//    return $uuid;
//}

//returns 5chars
function guid_solution()
{
	$characters = '01729384352617089';
	$token = '';
	for($i=0; $i <= 4; $i++) {
		$token .= $characters[mt_rand(0, strlen($characters) - 1)];
	}
	return $token;
}
    
?>