<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");


function email_featured_galleries()
{
	global $db, $gallerytypes;
	$list_output .= '<table width="100%" cellspacing="0" cellpadding="2">';	
	$list_output .= '<tr><th colspan="4"><h4>Featured Tollywood Galleries</h4></th></tr><tr>';
	$query = "select * from galleries where featured='1' and status='1' order by cdate DESC LIMIT 0, 12";
		$galleries = mysql_query($query);
		while($gallery = mysql_fetch_array($galleries))
		{
			$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
			if($iid)
			{
				$list_output .="<td width='".(100/4)."%' align='center'>";
				$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytypes[$gallery['gtype']])."'><img src='".set_url('galleries/thumbs/'.$iid.'.jpg')."' class='thumb' alt='".$gallery['gname']."' border='0' /><br />".$gallery['gname']."</a>";
				$list_output .="</td>";		
				$i++;
			}
			if($i%4 == 0)
			{
				$list_output .="</tr><tr>";
			}
		}
		$list_output .="</tr>";

	$list_output .="</table>";		
	return $list_output;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Latest Photo Galleries Newsletter</title>
</head>
<body>
<form action="gallerynewsletter.php" method="post">
<input type="text" name="gallery" size="3" />
<input type="submit" value="Send Galleries" />
<?php
if($_POST['gallery'] == 1)
{
	$gallery_data =email_featured_galleries()."<br/>";
	if($gallery_data)
	{
		$i=0;
		
		$users = mysql_query("select * from users where confirm='1'");
		while($user = mysql_fetch_array($users))
		{
			$user_email=$user['email'];
			$user_name=$user['name'];
			$subject = "Featured Tollywood Galleries";
			if(trim($user_email))
			{
				$query = "insert into emails values('','CineAlerts.com','alerts@cinealerts.com','".$user_name."','".$user_email."','".$subject."','".addslashes($gallery_data)."')";
				mysql_query($query);
				$i++;
			}
		}
		echo " - ".$i." - Emails qued<br/>";
		echo $gallery_data;
	}
}
?>
</form>
</body>
</html>
