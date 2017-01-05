<?php
@session_start();
include_once("../includes/configuration.php");
include_once("../includes/functions.php");
if($_REQUEST['limit'])
{
	$_SESSION['cur_limit'] = $_REQUEST['limit'];
	define('LIMIT',$_REQUEST['limit']);
}

if($_SESSION['cur_limit'])
{
	define('LIMIT',$_SESSION['cur_limit']);
}
$msg = $_REQUEST['msg'];
sessioncheck(4);

switch($_REQUEST['option'])
{
	case 'users': include("users.php");
				  break;
	case 'cities': include("cities.php");
				  break;
	case 'theaters': include("theaters.php");
				  break;
	case 'movies': include("movies.php");
				  break;
	case 'status': include("status.php");
				  break;
	case 'theaterstatus': include("theaterstatus.php");
				  break;
	case 'comments': include("comments.php");
				  break;
	case 'audio': include("audio.php");
				  break;
	case 'gallery': include("gallery.php");
				  break;
	case 'articles': include("articles.php");
				  break;
	case 'social': include("social.php");
				  break;
	case 'featured': include("featured.php");
				  break;
	case 'news': include("news.php");
				  break;
	case 'logout': 	unset($_SESSION['log_username']);
					unset($_SESSION['log_uid']);
					unset($_SESSION['log_user_type']);
					unset($_SESSION['log_name']);
					unset($_SESSION['log_email']);
					unset($_SESSION['log_mobile']);
					unset($_SESSION['log_city']);

				   header("Location:index.php?msg=You have sucessfully Logged out.");
				  break;
	default: $output = "Welcome";
}				 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>CineAlerts Administration</title>
<link rel="stylesheet" type="text/css" href="includes/style.css" /> 
<link rel="stylesheet" type="text/css" href="includes/menu.css" /> 
<script language="javascript" type="text/javascript" src="menu.js"></script>
<script language="javascript" type="text/javascript" src="rs.js"></script>

</head>

<body >
<table width="98%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr>
					<td><span style="font-size:20px; font-weight:bold; color:#CCCCCC; padding-left:20px;">CineAlerts Administration</span></td>
	</tr>
	
	<tr>
		<td valign="top">
		<?php
			include("includes/menu.php");
		?>
		</td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" class="pathway">
			 <?=$url?> <!-- Generated in menu.php file-->
		</td>
	</tr>
	<tr>
		<td height="20" valign="top" align="right" class="whitetable">
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
			Set Records Per Page: 
			<?php
			$cur_url = $_SERVER['REQUEST_URI'];
			if($_REQUEST['limit'])
			$s[$_REQUEST['limit']] = " selected = selected ";
			else
			$s[$_SESSION['cur_limit']] = " selected = selected ";
			?>
			
			<select name="limit" onchange="this.form.submit()">
				<option value="20" <?=$s[20]?> >20</option>
				<option value="30" <?=$s[30]?> >30</option>
				<option value="50" <?=$s[50]?> >50</option>
				<option value="75" <?=$s[75]?> >75</option>
				<option value="100" <?=$s[100]?> >100</option>
				<option value="125" <?=$s[125]?> >125</option>
			</select>
			</form>
		</td>
	</tr>
	<tr>
		<td align="center" class="alert">
		<?=$msg?>
		</td>
	</tr>
	
	<tr>
		<td height="400" valign="top" class="whitebox">
			<?=$output?>
		</td>
	</tr>

	
	<tr><td style="border-bottom:8px solid #0199CB;"></td></tr>
	<tr><td height="20"></td></tr>
	
</table>

</body>
</html>
