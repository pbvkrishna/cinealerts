<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");
$smss = mysql_query("select * from sms limit 0, 50");
while($sms = mysql_fetch_array($smss))
{
	sendsms($sms['receiver'], $sms['message']);
	@mysql_query("delete from sms where id = '".$sms['id']."'");
}
?>