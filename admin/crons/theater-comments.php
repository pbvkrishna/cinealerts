<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");
$lastrun = get_any("lastruns","theater-comments", "lastrun", "crontype");
//Start of SMS
$query = "select * from comments where cdate >= '".$lastrun."' and aprove='1' and tid !='0'";
$comments = mysql_query($query);
while($comment = mysql_fetch_array($comments))
{
	$sms_message = "Comment on ".get_any("theaters",$comment['tid'],"theater","tid")." theater: ".substr($comment['comment'],0,60).".... - For more visit www.cinealerts.com";
	$query = "select * from subscribe where field='tid' and id='".$comment['tid']."' and status='1' and alert_type='4'";
	$users = mysql_query($query);
	while($user = mysql_fetch_array($users))
	{
		//Get User Details
		$query = "select mobile from users where uid='".$user['uid']."' and aprove='1' and mconfirm='1'";
		$mobiles = mysql_query($query);
		if($mobile = mysql_fetch_array($mobiles))
		{
			//Insert to send SMS
			$query = "insert into sms values('','+917799230000','".$mobile['mobile']."','".$sms_message."')";
			$ins = mysql_query($query);
			$sms_count++;
		}
	}
}


//Start of E-Mail
$query = "select * from comments where cdate >= '".$lastrun."' and aprove='1' and tid !='0'";
$comments = mysql_query($query);
while($comment = mysql_fetch_array($comments))
{
	$subject = "Comment on ".get_any("theaters",$comment['tid'],"theater","tid")." theater";
	$email_message = "<b>".$subject."</b><br/><br/>".substr($comment['comment'],0,60).".... - For more visit <a href='http://www.cinealerts.com'>www.cinealerts.com</a>";
	$query = "select * from subscribe where field='tid' and id='".$comment['tid']."' and status='1' and alert_type='6'";
	$users = mysql_query($query);
	while($user = mysql_fetch_array($users))
	{
		//Get User Details
		$query = "select * from users where uid='".$user['uid']."' and aprove='1' and confirm='1'";
		$emails = mysql_query($query);
		if($email = mysql_fetch_array($emails))
		{
			$end_email_message = "Hi ".$email['name'].",<br/><br/>
			
			".$email_message;
			//Insert to send SMS
			$query = "insert into emails values('','Cinema Alert','alerts@cinealerts.com','".$email['name']."','".$email['email']."','".$subject."','".addslashes($end_email_message)."')";
			$ins = mysql_query($query);
			$emails_count++;
		}
	}
}

if($emails_count || $sms_count)
{
	mysql_query("update lastruns set lastrun=now() where crontype='theater-comments'");
}
?>
