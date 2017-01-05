<?php
@session_start();
include_once("../includes/configuration.php");
include_once("../includes/functions.php");

$msg = $_REQUEST['msg'];
if($_POST['username'] && $_POST['password'])
{
	if(adminlogin($_POST['username'], $_POST['password']))
	{
		header("Location:cpanel.php?option=home&msg=Welcome to the Administration Panel.");
	}
	else
	{
		$msg = "Invalid Username or Password";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CineAlerts Administration</title>
<link rel="stylesheet" type="text/css" href="includes/style.css" /> 
 
</head>

<body>
<br /><br /><br /><br /><br /><br />
<table width="100%" align="center">
<tr>
<td align="center">
<?=$msg?><br /><br />
<form action="index.php" method="post">
<table width="400" cellpadding="2" cellspacing="10" style="border:1px solid #000000" class="whitetable">
	<tr><td colspan="2" align="center" class="bodytext"><b>Login</b></td></tr>
	<tr><td align="right"><b>Username:</b> </td><td><input type="text" name="username" /></td></tr>
	<tr><td align="right"><b>Password:</b> </td><td><input type="password" name="password" /></td></tr>
	<tr><td colspan="2" align="center"><input type="submit"  value="Login"/></td></tr>
</table>
</form>
</td>
</tr>
</table>
</body>
</html>