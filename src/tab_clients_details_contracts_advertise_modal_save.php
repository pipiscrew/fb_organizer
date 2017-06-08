<?php
session_start();

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

if (!isset($_POST['advertise_offerID']) || !isset($_POST['ad_keywords']) || !isset($_POST['ad_client_goals']) || !isset($_POST['ad_fb1']) || !isset($_POST['ad_fb2']) || !isset($_POST['ad_fb3']) || !isset($_POST['ad_fb4']) || !isset($_POST['aud_countries']) || !isset($_POST['aud_age_min']) || !isset($_POST['aud_age_max']) || !isset($_POST['aud_gender']) || !isset($_POST['aud_languages']) || !isset($_POST['aud_interests']) || !isset($_POST['aud_behaviors']) || !isset($_POST['ad_connections']) || !isset($_POST['ad_placement_mobile']) || !isset($_POST['ad_placement_desktop']) || !isset($_POST['ad_placement_desktop_right']) || !isset($_POST['ad_placement_audience_network'])){
	echo "error010101010";
	return;
}
 
//DB
require_once ('config.php');
 
$db = connect();
 
$user4_send_ppl_website="0";
$user4_increase_conversions="0";
$user4_boost_posts="0";
$user4_promote_page_likes="0";
$user4_get_installs_app="0";
$user4_increase_engag="0";
$user4_raise_attendance="0";
$user4_claim_offer="0";
$user4_video_views="0";

if (isset($_POST['user4_send_ppl_website']))
	$user4_send_ppl_website=$_POST['user4_send_ppl_website'];

if (isset($_POST['user4_increase_conversions']))
	$user4_increase_conversions=$_POST['user4_increase_conversions'];

if (isset($_POST['user4_boost_posts']))
	$user4_boost_posts=$_POST['user4_boost_posts'];

if (isset($_POST['user4_promote_page_likes']))
	$user4_promote_page_likes=$_POST['user4_promote_page_likes'];
	
if (isset($_POST['user4_get_installs_app']))
	$user4_get_installs_app=$_POST['user4_get_installs_app'];

if (isset($_POST['user4_increase_engag']))
	$user4_increase_engag=$_POST['user4_increase_engag'];
	
if (isset($_POST['user4_raise_attendance']))
	$user4_raise_attendance=$_POST['user4_raise_attendance'];

if (isset($_POST['user4_claim_offer']))
	$user4_claim_offer=$_POST['user4_claim_offer'];

if (isset($_POST['user4_video_views']))
	$user4_video_views=$_POST['user4_video_views'];
			
if(isset($_POST['offer_advertise_detailsFORM_updateID']) && !empty($_POST['offer_advertise_detailsFORM_updateID']))
{
	$sql = "UPDATE offer_advertise_details set offer_id=:offer_id, user4_send_ppl_website=:user4_send_ppl_website, user4_increase_conversions=:user4_increase_conversions, user4_boost_posts=:user4_boost_posts, user4_promote_page_likes=:user4_promote_page_likes, user4_get_installs_app=:user4_get_installs_app, user4_increase_engag=:user4_increase_engag, user4_raise_attendance=:user4_raise_attendance, user4_claim_offer=:user4_claim_offer, user4_video_views=:user4_video_views, ad_keywords=:ad_keywords, ad_client_goals=:ad_client_goals, ad_fb1=:ad_fb1, ad_fb2=:ad_fb2, ad_fb3=:ad_fb3, ad_fb4=:ad_fb4, aud_countries=:aud_countries, aud_age_min=:aud_age_min, aud_age_max=:aud_age_max, aud_gender=:aud_gender, aud_languages=:aud_languages, aud_interests=:aud_interests, aud_behaviors=:aud_behaviors, ad_connections=:ad_connections, ad_placement_mobile=:ad_placement_mobile, ad_placement_desktop=:ad_placement_desktop, ad_placement_desktop_right=:ad_placement_desktop_right, ad_placement_audience_network=:ad_placement_audience_network, daterec=:daterec where offer_advertise_detail_id=:offer_advertise_detail_id";
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':offer_advertise_detail_id' , $_POST['offer_advertise_detailsFORM_updateID']);
}
else
{
	//validation - when insert, check if any record exists for this offer!!
	$f = getScalar($db,"select count(offer_advertise_detail_id) from offer_advertise_details where offer_id=?", array($_POST['page_offerID']));
	
	if ($f>0)
		die("-error-For this offer found double record!\r\nPlease, inform the administrator!\r\n\r\nOperation Aborted!");
	//validation - when insert, check if any record exists for this offer!!
	
	$sql = "INSERT INTO offer_advertise_details (offer_id, user4_send_ppl_website, user4_increase_conversions, user4_boost_posts, user4_promote_page_likes, user4_get_installs_app, user4_increase_engag, user4_raise_attendance, user4_claim_offer, user4_video_views, ad_keywords, ad_client_goals, ad_fb1, ad_fb2, ad_fb3, ad_fb4, aud_countries, aud_age_min, aud_age_max, aud_gender, aud_languages, aud_interests, aud_behaviors, ad_connections, ad_placement_mobile, ad_placement_desktop, ad_placement_desktop_right, ad_placement_audience_network, daterec) VALUES (:offer_id, :user4_send_ppl_website, :user4_increase_conversions, :user4_boost_posts, :user4_promote_page_likes, :user4_get_installs_app, :user4_increase_engag, :user4_raise_attendance, :user4_claim_offer, :user4_video_views, :ad_keywords, :ad_client_goals, :ad_fb1, :ad_fb2, :ad_fb3, :ad_fb4, :aud_countries, :aud_age_min, :aud_age_max, :aud_gender, :aud_languages, :aud_interests, :aud_behaviors, :ad_connections, :ad_placement_mobile, :ad_placement_desktop, :ad_placement_desktop_right, :ad_placement_audience_network, :daterec)";
	$stmt = $db->prepare($sql);
}

$stmt->bindValue(':offer_id' , $_POST['advertise_offerID']);
$stmt->bindValue(':user4_send_ppl_website' , $user4_send_ppl_website, PDO::PARAM_INT);
$stmt->bindValue(':user4_increase_conversions' , $user4_increase_conversions, PDO::PARAM_INT);
$stmt->bindValue(':user4_boost_posts' , $user4_boost_posts, PDO::PARAM_INT);
$stmt->bindValue(':user4_promote_page_likes' , $user4_promote_page_likes, PDO::PARAM_INT);
$stmt->bindValue(':user4_get_installs_app' , $user4_get_installs_app, PDO::PARAM_INT);
$stmt->bindValue(':user4_increase_engag' , $user4_increase_engag, PDO::PARAM_INT);
$stmt->bindValue(':user4_raise_attendance' , $user4_raise_attendance, PDO::PARAM_INT);
$stmt->bindValue(':user4_claim_offer' , $user4_claim_offer, PDO::PARAM_INT);
$stmt->bindValue(':user4_video_views' , $user4_video_views, PDO::PARAM_INT);
$stmt->bindValue(':ad_keywords' , $_POST['ad_keywords']);
$stmt->bindValue(':ad_client_goals' , $_POST['ad_client_goals']);
$stmt->bindValue(':ad_fb1' , $_POST['ad_fb1']);
$stmt->bindValue(':ad_fb2' , $_POST['ad_fb2']);
$stmt->bindValue(':ad_fb3' , $_POST['ad_fb3']);
$stmt->bindValue(':ad_fb4' , $_POST['ad_fb4']);
$stmt->bindValue(':aud_countries' , $_POST['aud_countries']);
$stmt->bindValue(':aud_age_min' , $_POST['aud_age_min']);
$stmt->bindValue(':aud_age_max' , $_POST['aud_age_max']);
$stmt->bindValue(':aud_gender' , $_POST['aud_gender']);
$stmt->bindValue(':aud_languages' , $_POST['aud_languages']);
$stmt->bindValue(':aud_interests' , $_POST['aud_interests']);
$stmt->bindValue(':aud_behaviors' , $_POST['aud_behaviors']);
$stmt->bindValue(':ad_connections' , $_POST['ad_connections']);
$stmt->bindValue(':ad_placement_mobile' , $_POST['ad_placement_mobile']);
$stmt->bindValue(':ad_placement_desktop' , $_POST['ad_placement_desktop']);
$stmt->bindValue(':ad_placement_desktop_right' , $_POST['ad_placement_desktop_right']);
$stmt->bindValue(':ad_placement_audience_network' , $_POST['ad_placement_audience_network']);
$stmt->bindValue(':daterec' , date("Y-m-d H:i:s"));

$stmt->execute();
 
echo $stmt->errorCode(); 
?>