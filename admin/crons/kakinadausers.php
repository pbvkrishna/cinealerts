<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");


function show_email_movie($mid,$cid)
{
	global $db;
	
	$movie_name = get_any("movies",$mid,"movie","mid");
	$movie_city = get_any("cities",$cid,"city","cid");
		
	$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=").'">'.$movie_city.' Movies</a> > ';
	
	$out .= '<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'"><strong>'.$movie_name.'</strong></a><br/><br/> ';
	
	$out .= '<h1><a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'">'.$movie_name.' Movie in '.$movie_city.'</a></h1>'.view_rating($mid,"mid");
	
	$movies = mysql_query("select * from movies where mid = '".$mid."'");
	

	
	if($movie = mysql_fetch_array($movies))
	{
		$out .= '<table width="100%" cellspacing="2" cellpadding="2"  >';
		$out .='<tr><td width="240" align="left" rowspan="2">';
		$out .= '<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'">'.show_movie_thumb($mid,"thumbs/").'</a>';
		$out .='</td><td>';
		$out .='<strong>Crew/Cast:</strong> <br />';
		$out .='Hero:'.$movie['hero'].' <br />';
		$out .='Heroine:'.$movie['heroine'].' <br />';
		$out .='Others:'.$movie['other'].' <br />';
		$out .='Banner:'.$movie['banner'].' <br />';
		$out .='Director:'.$movie['director'].' <br />';
		$out .='Music:'.$movie['music'].' <br />';
		$out .='Producer:'.$movie['producer']." <br />";
		
		
		if($movie['rdate'] > date("Y-m-d"))
		{
			$out .="<span class='mainalert'><strong>Releasing on ".date("M jS, Y",strtotime($movie['rdate']))."</strong></span> ";
		}
		$out .='</td></tr>';
		$out .='<tr><td valign="middle" >';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'"><strong>Theaters List</strong></a><br/>';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=comments").'"><strong>Comments</strong></a><br/>';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=mobile_alerts").'"><strong>Alerts</strong></a><br/>';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=gallery").'"><strong>Photo Galleries</strong></a></td></tr>';
		$out .='</table>';
	}
	
	return $out;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Movie Changes in Theaters</title>
</head>

<body>
<form action="kakinadausers.php" method="post">
<input type="text" name="movies" size="100" />
<input type="submit" value="send-Emails" />
<?php
if($_POST['movies'])
{
	$movieslist = explode(",",$_POST['movies']);
	foreach($movieslist as $movie)
	{
		$mid = get_movie_id(trim($movie))?get_movie_id(trim($movie)):0;
		if($mid)
		{
			$movie_data .=show_email_movie($mid,3)."<br/>";
		}
	
	}
	if($movie_data)
	{
		//parse all the users from kakinada_users table and insert into email table
		$users = mysql_query("select * from kakinada_users");
		while($user = mysql_fetch_array($users))
		{
			$user_email=$user['email'];
			$user_name=$user['name'];
			$subject = "Movies Playing in kakinada Theaters.";
			$query = "insert into emails values('','Cinema Alert','alerts@cinealerts.com','".$user_name."','".$user_email."','".$subject."','".addslashes($movie_data)."')";
			mysql_query($query);
		$i++;
		}
		echo $i." - Emails qued";
	}
}

?>
</form>
</body>
</html>
