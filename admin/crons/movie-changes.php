<?php
include("../../includes/configuration.php");
include("../../includes/functions.php");

function get_sms_theater($tid)
{
	global $db;
	$theaters = mysql_query("select * from theaters where tid='".$tid."'");
	
	if($theater = mysql_fetch_array($theaters))
	{
		$cid = $theater['cid'];
		$theater_name = get_any("theaters",$tid,"theater","tid");
		$city = get_any("cities",$cid,"city","cid");
		

		$out .= $theater['theater'].' Movies: ';
		//Get current Playing movies in theater
		$movies = mysql_query("select * from playing where tid = '".$tid."' and status='1' order by showid ASC");
		while($movie = mysql_fetch_array($movies))
		{
			$movie_shows[$movie['mid']][$movie['showid']] = $movie['timings'];
		}
		$i=0;
		
		if($movie_shows)
		{
			foreach($movie_shows as $mid => $shows)
			{
				$movies_p[] =get_any("movies",$mid,"movie","mid");
			}	
		}
		$out .= implode(", ",$movies_p);
	}
	return $out;
}

function get_email_theater($tid)
{
	global $db;
	$theaters = mysql_query("select * from theaters where tid='".$tid."'");
	
	if($theater = mysql_fetch_array($theaters))
	{
		$cid = $theater['cid'];
		$theater_name = get_any("theaters",$tid,"theater","tid");
		$city = get_any("cities",$cid,"city","cid");
		$out .='<a href="'.set_url("index.php?city=".$city."&theater=").'">'.$city.' Theaters</a> > ';
		$out .= '<a href="'.set_url("index.php?city=".$city."&theater=".$theater['theater']).'"><strong>'.$theater['theater'].'</strong></a><br/><br/> ';
		$out .= "<h1>".get_any("theaters",$tid,"theater","tid")." in ".get_any("cities",$theater['cid'],"city","cid")."</h1>".view_rating($tid,"tid");
		$out .= '<table width="100%" cellspacing="2" cellpadding="2" border="0">';
		$out .='<tr><td>';
		//Get current Playing movies in theater
		$movies = mysql_query("select * from playing where tid = '".$tid."' and status='1' order by showid ASC");
		while($movie = mysql_fetch_array($movies))
		{
			$movie_shows[$movie['mid']][$movie['showid']] = $movie['timings'];
		}
		$i=0;
		
		if($movie_shows)
		{
			$out .= "<h4>Movies Playing</h4>";
			$out .='<table width="100%" cellspacing="2" cellpadding="2" border="0">';
			$out .='<tr>';
			foreach($movie_shows as $mid => $shows)
			{
				if($i%3 == 0 && $i != 0)
				{
					$out .="</tr>";
				}
				if($i==0 || $i%3 == 0)
				{
					$out .= "<tr>";
				}
				@ksort($shows);
				$out .="<td align='center' width='33%'>";
				$rdate = get_any("movies",$mid, "rdate", "mid");
				if($rdate > date("Y-m-d"))
				{
					$out .="<span class='alert'>Releasing on ".date("M jS, Y",strtotime($rdate))."</span>";
				}
				$out .=show_mini_movie($cid, $mid)."<br/><span class='minifont'>".implode(", ",$shows)."</span></td>";
				$for_page_title[]= get_any("movies",$mid,"movie","mid");
				$i++;
			}
			while($i%3 !=0)
			{
				$out .="<td width='33%'>&nbsp;</td>";
				$i++;
			}
			$out .="</tr></table>";
		}
		$out .='</td></tr>';
		$out .='</table>';
	}
	return $out;
}

$lastrun = get_any("lastruns","movie-changes", "lastrun", "crontype");
//Start of SMS
$query = "select * from playing where sdate >= '".$lastrun."' and status='1' group by tid"; 
$changes = mysql_query($query);
while($change = mysql_fetch_array($changes))
{
	$sms_message = get_sms_theater($change['tid']);
	$query = "select * from subscribe where field='tid' and id='".$change['tid']."' and status='1' and alert_type='3'";
	$users = mysql_query($query);
	while($user = mysql_fetch_array($users))
	{
		//Get User Details
		$query = "select mobile from users where uid='".$user['uid']."' and aprove='1' and mconfirm='1'";
		$mobiles = mysql_query($query);
		if($mobile = mysql_fetch_array($mobiles))
		{
			//Insert to send SMS
			$query = "insert into sms values('','+917799230000','".$mobile['mobile']."','".$sms_message."')";
			$ins = mysql_query($query);
			$sms_count++;
		}
	}
}

//Start of Email
$query = "select * from playing where sdate >= '".$lastrun."' and status='1' group by tid"; 
$changes = mysql_query($query);
while($change = mysql_fetch_array($changes))
{
	$subject = get_any("theaters",$change['tid'],"theater","tid")." movie changes ";
	$email_message = get_email_theater($change['tid']);
	$query = "select * from subscribe where field='tid' and id='".$change['tid']."' and status='1' and alert_type='5'";
	$users = mysql_query($query);
	while($user = mysql_fetch_array($users))
	{
		//Get User Details
		$query = "select * from users where uid='".$user['uid']."' and aprove='1' and confirm='1'";
		$emails = mysql_query($query);
		if($email = mysql_fetch_array($emails))
		{
			$end_email_message = "Hi ".$email['name'].",<br /><br />
			
			".$email_message;
			//Insert to send SMS
			$query = "insert into emails values('','Cinema Alert','alerts@cinealerts.com','".$email['name']."','".$email['email']."','".$subject."','".addslashes($end_email_message)."')";
			
			$ins = mysql_query($query);
			
			//send directly
			$emails_count++;
		}
	}
}


if($emails_count || $sms_count)
{
	mysql_query("update lastruns set lastrun=now() where crontype='movie-changes'");
}
?>