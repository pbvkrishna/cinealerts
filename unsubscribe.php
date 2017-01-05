<?php
include("includes/configuration.php");
include("includes/functions.php");

if($_POST['email'])
{
	global $db;
	@mysql_query("insert into unsubsc(email) values('".addslashes($_POST['email'])."')");
	$msg = "We have received your email address for unsubscription. Please allow us 7 days to remove from our list";
	
	@mysql_query("delete from kakinada_users where email = '".$_POST['email']."'");

	@mysql_query("delete from hyderabad1 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad2 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad3 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad4 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad5 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad6 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad7 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad8 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad9 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad10 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabad11 where email = '".$_POST['email']."'");
	@mysql_query("delete from hyderabadusers where email = '".$_POST['email']."'");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Movie Changes in Theaters</title>
</head>

<body onload="document.unsubform.email.focus();">
<?=$msg?>
<h1>Enter your Email Address to Unsubscribe</h1>
<form action="unsubscribe.php" method="post" name="unsubform">
<input type="text" name="email" size="100" />
<input type="submit" value="UnSubscribe" />
</form>
Note: To remove your email address from our list will take 7 days of time.
</body>
</html>
