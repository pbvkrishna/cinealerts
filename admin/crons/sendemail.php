<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");
$emails = mysql_query("select * from emails limit 0, 150");
while($email = mysql_fetch_array($emails))
{
	sendemail($email['toemail'], $email['toname'], $email['subject'], $email['message']);
	@mysql_query("delete from emails where eid = '".$email['eid']."'");
}
?>