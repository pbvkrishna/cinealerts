<?php
$host = "localhost";
$dbname = "cinealer_alerts";
$dbuser = "cinealer_calerts";
$dbpassword = "Traceout007";

$host2 = "localhost";
$dbname2 = "vizagwor_vizag";
$dbuser2 = "vizagwor_vizag";
$dbpassword2 = "Krishna123";
update_vizag_movies();

function update_vizag_movies()
{
	global $host, $dbname, $dbuser, $dbpassword, $host2, $dbname2, $dbuser2, $dbpassword2;
	
	$db = mysql_connect($host,$dbuser,$dbpassword);
	mysql_select_db($dbname, $db);
	$theaters = mysql_query("select * from theaters where cid = '5'");
	while($theater = mysql_fetch_array($theaters))
	{
		$conds[] = "playing.tid='".$theater['tid']."'";
	}
	
	$cond .= "(".@implode(" or ", $conds).")";
	$cond .= " and  playing.status = 1";

	$total_records = @mysql_num_rows(mysql_query("select distinct(mid) from playing where ".$cond));
	$movies = mysql_query("select distinct(playing.mid) from playing, movies where playing.mid=movies.mid and ".$cond);
	
	$city = "Visakhapatnam";
	
	
	$db = mysql_connect($host2,$dbuser2,$dbpassword2);
	mysql_select_db($dbname2, $db);

	$query = mysql_query("update jos_simplereview_review set categoryID = '4' where categoryID = '2'");
	
	while($movie = @mysql_fetch_array($movies))
	{	
		$movie_name = addslashes(get_any("movies",$movie['mid'],"movie","mid"));
		$score = get_any("movies",$movie['mid'],"rating","mid");
		$rdate = addslashes(get_any("movies",$movie['mid'],"rdate","mid"));
		$moviedetails = get_movie_details($movie['mid'], 5);
		$playinglist = get_playing_theaters(5,$movie['mid']);
		$imageid = $movie['mid'].".jpg";
		$metakey = $movie_name.",movie, visakhapatnam, vizag, playing, movies, theaters, details";
		$metadesc = $movie_name." movie in vizag playing theaters, User reviews and comments posted on ".$movie_name." movie. List of the theaters playing ".$movie_name;
		$metadata = $movie_name." movie in Vizag playing theaters, User reviews and comments posted on ".$movie_name." movie. List of the theaters playing ".$movie_name;
		
		$db = mysql_connect($host2,$dbuser2,$dbpassword2);
		mysql_select_db($dbname2, $db);
		$rjy_check = mysql_query('select * from jos_simplereview_review where name = "'.trim($movie_name).'"');
		
		if($rjy_movie = @mysql_fetch_array($rjy_check))
		{
		//Update details
		$query = "update jos_simplereview_review set categoryID = '2', score='".$score."', content = '<p>{Review:Blurb}</p><br/><strong>Playing in Theaters</strong><br/>".addslashes($playinglist)."', blurb = '".addslashes($moviedetails)."', createdDate='".$rdate."' where reviewID = '".$rjy_movie['reviewID']."'";
		mysql_query($query);
		}
		else
		{
		//insert movie

		$query = "insert into jos_simplereview_review (categoryID, awardID, templateName, score, name, pageName, content, blurb, thumbnailURL, imageURL, createdDate, lastModifiedDate, createdByID, lastModifiedByID, published, userReview, status, metakey, metadesc, metadata) values('2','-1','default', '".$score."', '".$movie_name."', '".str_replace(" ","_",$movie_name)."', '<p>{Review:Blurb}</p><br/><strong>Playing in Theaters</strong><br/>".addslashes($playinglist)."', '".addslashes($moviedetails)."', 'http://www.cinealerts.com/posters/thumbs/".$imageid."', 'http://www.cinealerts.com/posters/".$imageid."', '".$rdate."', now(), '62', '62','1','0', 'pending', '".$metakey."', '".$metadesc."', '".$metadata."')";
		mysql_query($query) or die(mysql_error());
		
		}

	}

}

function get_movie_details($mid,$cid = 5)
{
	global $host, $dbname, $dbuser, $dbpassword, $host2, $dbname2, $dbuser2, $dbpassword2;
	
	$db = mysql_connect($host,$dbuser,$dbpassword);
	mysql_select_db($dbname, $db);
	
	$movie_city = "Visakhapatnam";
		
	$movies = mysql_query("select * from movies where mid = '".$mid."'");
	
	if($movie = mysql_fetch_array($movies))
	{
		$out .='<strong>Crew/Cast:</strong> <br />';
		$out .='Hero: '.$movie['hero'].' <br />';
		$out .='Heroine: '.$movie['heroine'].' <br />';
		$out .='Others: '.$movie['other'].' <br />';
		$out .='Banner: '.$movie['banner'].' <br />';
		$out .='Director: '.$movie['director'].' <br />';
		$out .='Music: '.$movie['music'].' <br />';
		$out .='Producer: '.$movie['producer']." <br />";
		
		
	}
	
	 return $out;
}

function get_any($table, $find, $return, $attrib = "id")
{
	global $host, $dbname, $dbuser, $dbpassword, $host2, $dbname2, $dbuser2, $dbpassword2;
	
	$db = mysql_connect($host,$dbuser,$dbpassword);
	mysql_select_db($dbname, $db);
	$anys = @mysql_query("select * from $table where $attrib = '$find'");
	if($any = @mysql_fetch_array($anys))
	{
		return $any[$return];
	}
	else
	{
		return "";
	}
}


function get_playing_theaters($cid = 5,$mid)
{
	global $host, $dbname, $dbuser, $dbpassword, $host2, $dbname2, $dbuser2, $dbpassword2;
	
	$db = mysql_connect($host,$dbuser,$dbpassword);
	mysql_select_db($dbname, $db);
        
	$theaters=mysql_query("select * from theaters where cid='".$cid."'");
	$out .="<table border='0' cellpadding='0' callspacing='0' >";
	while($theater = mysql_fetch_array($theaters))
	{
		$city_theaters[]=" tid = ".$theater['tid'];
	}
	
	if($city_theaters)
	{
		$cond = " and (".@implode(" or ", $city_theaters).")";	
	}
	$playings = mysql_query("select * from playing where mid = '".$mid."' ".$cond. " and status = 1 ");
	
	while($playing = mysql_fetch_array($playings))
	{
		$play_shows[$playing['tid']][$playing['showid']] = $playing['timings'];
	}
	if($play_shows)
	{
		$i=0;
		foreach($play_shows as $tid => $shows)
		{
			@ksort($shows);
	
			$out .='<tr>';	
			$tname = get_any("theaters",$tid,"theater","tid");
			$out .="<td align='left' width='40%'><b>".$tname."</b><br /><span class='minifont'>".get_any("theaters",$tid,"area","tid")."</span> </td><td width='40%'><span class='minifont'>".implode(",",$play_shows[$tid])."</span> </td>";				

			$out .="</tr>";	
			$out .="<tr><td colspan='2'><hr></hr></td></tr>";		

			$for_page_title[]= $tname;			
		}
		
	}
	else
	{
		$out .='<tr><td align="center"><br/><br/><h4>Not playing in the city</h4><br/></td></tr>';	
	}
	$out .="</table></td>";
	return $out;
}

?>