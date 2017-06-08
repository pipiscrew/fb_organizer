<?php
session_start();

require_once ('config.php');

if (!isset($_SESSION["u"]) || !isset($_SESSION["mail"])) {
	header("Location: login.php");
	exit ;
}

$db = connect();
	
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	require_once ('config_general.php');

	if (!isset($_POST["mail2_recipient"]) || !isset($_POST["mail2_subject"]) || !isset($_POST["mail2_body"]) || !isset($_POST["mail2_offer_rec_id"]))
		die("required field(s) missing");

	$res = send_mail_to_user_proposal($_SESSION['reply_mail'], $_POST["mail2_recipient"],$_POST["mail2_subject"],$_POST["mail2_body"]);
	
//	echo $res;
//	exit;
	if ($res == "ok")
		echo "<script>alert('Main sent!');window.close();</script>";
	else 
		echo "failed to deliver the email!\r\n\r\nplease try again!";

exit;
} else {

	if (!isset($_GET["id"]))
		die("Catastrophic Error");
		
	$r = getRow($db, "select *,DATE_FORMAT(marketing_plan_when,'%d-%m-%Y %H:%i') as marketing_plan_when2 from offers left join users on users.user_id=offers.offer_seller_id where offer_id=? ",array($_GET["id"]));
	
	$marketing_dt = "";
	$marketing_location = "";
	$manager = "";
	
	if ($_SESSION['level']!=9){
		
		if ($r["offer_seller_id"]!=$_SESSION['id'])
			die("Error 0xPlQnrTi3<br><br>Offer belongs to another user!");
	}
	else {
			$marketing_dt = $r["marketing_plan_when2"];
			$marketing_location = $r["marketing_plan_location"];
			$manager = $r["offer_company_manager_name"];
			
			if ($marketing_location=="pipiscrew")
				$marketing_location = "pipiscrew <a href='http://pipiscrew' target='_blank'>χάρτης</a>";
	}

}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>pipiscrew</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<link rel="shortcut icon" href="main.ico" type="image/png"/>
		<link rel="apple-touch-icon" href="main.ico" type="image/png"/>

		<!-- bootstrap 3.0.2 -->
		<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="css/jquery-te-green.css" rel="stylesheet"></link> 
		
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery-te-1.4.0.min.js"></script>

<script>
	var loading = $('<div class="modal-backdrop"></div><div class="progress progress-striped active loading"><div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">');
	

	var marketing_dt = '<?= $marketing_dt ?>';

	var manager = '<?= $manager ?>';
    $(function() {
    	
		//setup jQTE			
		$("#mail2_body").jqte({css:"jqte_green"});
		

var marketing_dt = "Αξιότιμε/η κ." + manager  + "<br><br>Η παρουσίαση του Marketing Strategy Plan - Στρατηγικού Πλάνου Διαφήμισης για την επαγγελματική σας σελίδα στο Facebook θα πραγματοποιηθεί στις <?= $marketing_dt ?><br><br>Τοποθεσία : <?= $marketing_location ?>";
//		marketing_dt = "Η παρουσίαση του Στρατηγικού πλάνου διαφήμισης για την επαγγελματική σας σελίδα στο Facebook θα πραγματοποιηθεί στις " + marketing_dt;
		
		$("#mail2_body").jqteVal(marketing_dt);
	})
		
</script>
	</head>
<body>

<div id="mail_div" style="margin: 0 auto;width: 500px;">
				<p style="background-color: #428bca;color:#fff;padding: 5px;" align=center>email will be send by proposal@watetron.com with <b>reply</b> property to <b><?=$_SESSION['reply_mail'];?></b></p>				
				
				<form id="formomailtwo" role="form" method="post" action="">

						<div class='form-group'>
							<label>Recipient (multiple addresses separated by semicolon) :</label>
							<input id='mail2_recipient' name='mail2_recipient' class='form-control' placeholder='Recipient' value="<?= $r["offer_email"]; ?>" required autofocus>
						</div>
						
						<div class='form-group'>
							<label>Subject :</label>
							<input id='mail2_subject' name='mail2_subject' class='form-control' placeholder='Subject' value="pipiscrew Presentation Day" required>
						</div>

							<input id='mail2_body' name='mail2_body' data-role="none" class='editor'>
							
							<input id='mail2_offer_rec_id' name='mail2_offer_rec_id' style="display: none">
													
<br>
							<button id="bntSend_mailtwo" class="btn btn-primary" type="submit" name="submit">
								send
							</button>

					</form>
</div>
</body>
</html>