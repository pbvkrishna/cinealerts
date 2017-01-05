<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");
global $db;
$lastrun = get_any("lastruns","movie-comments", "lastrun", "crontype");
//Start of SMS
$query = "select * from comments where cdate >= '".$lastrun."' and aprove='1' and mid !='0'";
$comments = mysql_query($query);
while($comment = mysql_fetch_array($comments))
{
	$sms_message = "Comment on ".get_any("movies",$comment['mid'],"movie","mid")." movie: ".substr($comment['comment'],0,60).".... - For more visit www.cinealerts.com";
	$query = "select * from subscribe where field='mid' and id='".$comment['mid']."' and status='1' and alert_type='1'";
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
$query = "select * from comments where cdate >= '".$lastrun."' and aprove='1' and mid !='0'";
$comments = mysql_query($query);

while($comment = mysql_fetch_array($comments))
{
	$subject = "Comment on ".get_any("movies",$comment['mid'],"movie","mid")." movie";
	$email_message = "<b>".$subject.":</b><br /><br />".substr($comment['comment'],0,60).".... - For more visit <a href=\http://www.cinealerts.com'>www.cinealerts.com</a>";
	$query = "select * from subscribe where field='mid' and id='".$comment['mid']."' and status='1' and alert_type='2'";
	$users = mysql_query($query);
	while($user = mysql_fetch_array($users))
	{
		//Get User Details
		$query = "select * from users where uid='".$user['uid']."' and aprove='1' and confirm='1'";
		$emails = mysql_query($query);
		if($email = mysql_fetch_array($emails))
		{
			$end_email_message = "Hi ".$email['name'].",<br /><br />
			
			".$email_message;
			//Insert to send SMS
			$query = "insert into emails values('','Cinema Alert','alerts@cinealerts.com','".$email['name']."','".$email['email']."','".$subject."','".addslashes($end_email_message)."')";
			$ins = mysql_query($query);
                        if($ins)
			  $emails_count++;
		}
	}
}
echo $emails_count;
if($emails_count || $sms_count)
{
	mysql_query("update lastruns set lastrun=now() where crontype='movie-comments'");
}
?>