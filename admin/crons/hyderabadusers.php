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
	
	$out .= "<h1>".$movie_name." Movie in ".$movie_city."</h1>".view_rating($mid,"mid");
	
	$movies = mysql_query("select * from movies where mid = '".$mid."'");
	

	
	if($movie = mysql_fetch_array($movies))
	{
		$out .= '<table width="100%" cellspacing="2" cellpadding="2"  >';
		$out .='<tr><td width="240" align="left" rowspan="2">';
		$out .= show_movie_thumb($mid,"");
		$out .='</td><td>';
		$out .='<strong>Crew/Cast:</strong> <br />';
		$out .='Hero: '.$movie['hero'].' <br />';
		$out .='Heroine: '.$movie['heroine'].' <br />';
		$out .='Others: '.$movie['other'].' <br />';
		$out .='Banner: '.$movie['banner'].' <br />';
		$out .='Director: '.$movie['director'].' <br />';
		$out .='Music: '.$movie['music'].' <br />';
		$out .='Producer: '.$movie['producer']." <br />";
		
		
		if($movie['rdate'] > date("Y-m-d"))
		{
			$out .="<span class='mainalert'><strong>Releasing on ".date("M jS, Y",strtotime($movie['rdate']))."</strong></span> ";
		}
		$out .='</td></tr>';
		$out .='<tr><td valign="middle" >';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'"><strong>Playing in Theaters</strong></a><br/>';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=comments").'"><strong>Comments</strong></a><br/>';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=mobile_alerts").'"><strong>Alert Me on mobile and email</strong></a><br/>';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=email").'"><strong>Invite a friend to watch this movie</strong></a></td></tr>';
		$out .='</table>';
	}
	
	return $out;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Hyderabad Users</title>
</head>

<body>
<form action="hyderabadusers.php" method="post">
Movies: <input type="text" name="movies" size="100" /><br />From Table <input type="text" name="ftable" size="20" /> Ex. hyderabad1, hyderabad2.....11<br />
<input type="submit" value="send-Emails" />
<?php
if(trim($_POST['movies']) && $_POST['ftable'])
{
	$movieslist = explode(",",$_POST['movies']);
	foreach($movieslist as $movie)
	{
		$mid = get_movie_id(trim($movie))?get_movie_id(trim($movie)):0;
		if($mid)
		{
			$movie_data .= show_email_movie($mid,1)."<br/>";
		}
	
	}
	if($movie_data)
	{
		//parse all the users from hyderabad1,2,3,4,5,6,7,8,9,10,11 table and insert into email table
		$users = mysql_query("select * from ".$_POST['ftable']);
		while($user = mysql_fetch_array($users))
		{
			$user_email=$user['email'];
			$user_name=$user['fname']." ".$user['lname'];
			$subject = "Movies Playing in Hyderabad.";
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
