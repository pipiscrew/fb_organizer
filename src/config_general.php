<?php

//check for session
if(!isset($_SESSION)) 
{ 
    session_start(); 
} 

function guid(){
    mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $uuid =  substr($charid, 0, 8)
            .substr($charid, 8, 4)
            .substr($charid,12, 4)
            .substr($charid,16, 4)
            .substr($charid,20,12);
	
    return $uuid;
}

//$log_type
//1-user.notify  2-user.info  3-user.danger
//4-admin.notify 5-admin.info 6-admin.danger
//tab_leads_details_proposal_admin_save.php
function write_log($db, $log_type, $log_text, $client_id = null, $user_id = null)
{
	if ($user_id==null && isset($_SESSION['id']))
		$user_id=$_SESSION['id'];
		
	try {
		//UTC
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone('UTC'));
		$sql_utc_date = $date->format('Y-m-d H:i:s');

		//PDO
		$sql = "INSERT INTO `logger` (log_UTC_when, log_type, log_text, user_id, client_id) VALUES (:log_UTC_when, :log_type, :log_text, :log_user_id, :log_client_id)";
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':log_UTC_when' , $sql_utc_date);
		$stmt->bindValue(':log_type' , $log_type);
		$stmt->bindValue(':log_text' , $log_text);
		$stmt->bindValue(':log_user_id' , $user_id);
		$stmt->bindValue(':log_client_id' , $client_id);

		$stmt->execute();
	} catch (Exception $e) {
		
	}	
}


function get_fb_info($page_handle, &$out_likes, &$out_tat)
{
    $out_likes = 0;
    $out_tat = 0;
 
    try
    {
        //////////////////////////get result
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER=> 1,
                CURLOPT_URL           => 'https://graph.facebook.com/'.$page_handle,
                CURLOPT_USERAGENT     => 'Mozilla/5.0 (compatible; ABrowse 0.4; Syllable)'
            ));
 
        // Send the request & save response to $response
        $response = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        //////////////////////////get result
 
        //////////////////////////parse result
        $detailsJSON = json_decode($response);
 
        if (array_key_exists('talking_about_count', $detailsJSON))
            $out_tat      = $detailsJSON->talking_about_count;
 
        if (array_key_exists('likes', $detailsJSON))
            $out_likes    = $detailsJSON->likes;
        //////////////////////////parse result
 
    } catch(Exception $e){
        $out_tat = $out_likes = 0;
        //echo $e->getMessage();
        //exit;
    }
}

//send mail only used on scheduled
function send_mail($subject, $body)
{
	$headers = "From: PipisCrew Robot <api@pipiscrew.com>\r\n";
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


//send mail used on when upload approval attachment aka proposal/approval_save.php
function send_mail_to_cookies($subject, $body)
{
	$headers = "From: pipiscrew Robot <api@pipiscrew.com>\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
	$message = '<html><body>'.$body;
	$message .= '</body></html>';

    if (mail("n.cookies@catertron.com", $subject, $message, $headers)) {
      return true;
    } else {
      return false;
    }
}

//send mail used when admin save that proposal paid aka tab_leads_details_proposal_admin_save.php
function send_mail_to_user($user_mail, $subject, $body)
{
	$headers = "From: pipiscrew Robot <api@pipiscrew.com>\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
	$message = '<html><body>'.$body;
	$message .= '</body></html>';

    if (mail($user_mail, $subject, $message, $headers)) {
      return true;
    } else {
      return false;
    }
}

//send mail used when admin save that proposal paid aka tab_proposal_send_approval_mail.php
function send_mail_to_user_proposal($user_mail, $client_mail,$subject, $body)
{
	$headers = "From: pipiscrew <proposal@pipiscrew.com>\r\nReply-to: {$user_mail}\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
	$message = '<html><body>'.$body;
	$message .= '</body></html>';

    if (mail($client_mail, $subject, $message, $headers)) {
      return "ok";
    } else {
      return "fail";
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////
// DATETIME FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////////

//get working day in month
function get_month_working_days($month, $year)
{
	$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year); // builtin function - get amount of days
	
	$exclude=0;

	for ($i = 1; $i <= $day_count; $i++) {
			$get_name = date('D', strtotime($year.'/'.$month.'/'.$i)); //get week day
		
			if($get_name == 'Sun' || $get_name == 'Sat'){
				$exclude+=1;
			}
	}
	
	return ($day_count - $exclude);
}

function get_end_of_the_month($month, $year)
{
	if (strlen($month)==1)
		$month="0".$month;
		
	//t returns the number of days in the month of a given date 
	$d = date("t", strtotime(date("{$year}-{$month}-d")));
	$m = date("{$year}-{$month}-{$d}"); //format back to mysql style!
	return  $m;

}

function get_month_back($month, $year, $backno)
{
	$start_date = date("$year-$month-01"); //convert to date
	$mod_date = strtotime($start_date."-$backno month"); //subtract -1!
	$m = date("Y-m-d 00:00",$mod_date); //format back to mysql style!
	
	return $m;	
}

class SimpleWorkingDays
{
	private function orthodox_eastern($year) { 
	    $a = $year % 4; 
	    $b = $year % 7; 
	    $c = $year % 19; 
	    $d = (19 * $c + 15) % 30; 
	    $e = (2 * $a + 4 * $b - $d + 34) % 7; 
	    $month = floor(($d + $e + 114) / 31); 
	    $day = (($d + $e + 114) % 31) + 1; 
	    
	    $de = mktime(0, 0, 0, $month, $day + 13, $year); 
	    
	    return date('Y-m-d',$de); 
	} 

	private function get_vacation_days($year)
	{
		$vacations = array();
		$easter = $this->orthodox_eastern($year);
		$vacations[] = "01.01"; //prwtoxronia
		$vacations[] = "06.01"; //8eofaneia
		$vacations[] = "25.03"; //25martioy
		$vacations[] = "01.05"; //1maioy - ergatikh protomagia
		$vacations[] = "15.08"; //15aug - koimhsh 8eotokoy
		$vacations[] = "26.10"; //26oct - polioyxoy 8essalonikhs
		$vacations[] = "28.10"; //28oct - epeteios toy oxi
		$vacations[] = "25.12"; //25dec - christmas
		$vacations[] = "26.12"; //26dec - christmas - 2nd day - syna3is tis 8eotokoy
		$vacations[] = date('d.m',strtotime($easter)); //orthodox easter
		$vacations[] = date('d.m',strtotime($easter."-48 days")); //clean monday
		$vacations[] = date('d.m',strtotime($easter."-2 days")); //big friday
		$vacations[] = date('d.m',strtotime($easter."+50 days")); //agioy pneymatos
		$vacations[] = date('d.m',strtotime($easter."+1 days")); //deytera toy pasxa
		
		return $vacations;
	}
		
	//get working days
	function get_month_working_days_between_range($date_one, $date_two)
	{
		$vacation_days = $this->get_vacation_days($year);
		
		$start = strtotime($date_one);
		$end = strtotime($date_two);
		
		$no_days=0;

		while ($start <= $end) {
			$get_name4vacation = date('d.m', $start);

			//when doesnt exist in vacation days			
			if (!in_array($get_name4vacation, $vacation_days)){
				$what_day = date("N", $start);
				
			    if($what_day < 6) // 6 and 7 are weekend days
			        $no_days++;
		    }
		    
		    $start += 86400; // +1 day
		}
		
		return $no_days;
	}

	//get working day in month
	function get_month_working_days($month, $year)
	{
		$day_count = cal_days_in_month(CAL_GREGORIAN, $month, $year); // builtin function - get amount of days
		$vacation_days = $this->get_vacation_days($year);
		
		$exclude=0;

		for ($i = 1; $i <= $day_count; $i++) {
				$get_name = date('D', strtotime($year.'/'.$month.'/'.$i)); //get day
			    $get_name4vacation = date('d.m', strtotime($year.'/'.$month.'/'.$i));
				
				if($get_name == 'Sun' || $get_name == 'Sat' || in_array($get_name4vacation, $vacation_days)){
					$exclude+=1;
				}
		}
		
		return ($day_count - $exclude);
	}
}

?>