<?php
include("includes/configuration.php");
include("includes/functions.php");
$query = mysql_query("insert into sms(sender,receiver,message) values('".$_REQUEST['sender']."','".$_REQUEST['receiver']."','".$_REQUEST['message']."')");
?>
