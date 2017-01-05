<?php
@session_start();
ini_set('memory_limit', '64m');
include_once("../includes/configuration.php");
$start = $_REQUEST['start']?$_REQUEST['start']:0;
sessioncheck(4);
global $msg;
$top_form = '<link href="../calendar/calendar.css" type="text/css" rel="stylesheet" />
<script src="../calendar/calendar.js"></script>
<script src="../calendar/js_dateformat.js"></script>
<script src="includes/autocomplete.js"></script>
<script src="includes/browser.js"></script>
<link href="includes/autocomplete.css" type="text/css" rel="stylesheet" />';

$top_form .= '<table width="96%" align="center"><tr><td align="right"><br><form action="cpanel.php?option=theaterstatus" method="post">City: <select name="cid">'.get_cities($_REQUEST['cid']).'</select><input type="submit" value="Search" /><input type="hidden" name="limit" value="50" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	case 'save':save_status($_POST);
				break;
	default:$output = miniupdate();
			
}

$output = $top_form . $output;


//User Functions

function miniupdate()
{
	global $db;
	if($_REQUEST['cid'])
	{
		$theaters = mysql_query('select * from theaters where cid = "'.$_REQUEST['cid'].'"');
		while($theater=mysql_fetch_array($theaters))
		{
			$theaters_list[]="\"".$theater['theater']."\"";
		}
		
		$list_output .="<form action='cpanel.php?option=theaterstatus' method='post'><input type='hidden' name='cid' value='".$_REQUEST['cid']."'><input type='hidden' name='task' value='minisave'>";
		$list_output .= "<table class='contenttable' width='98%' align='center'>";
		$list_output .= "<tr><th><b>Theater</b></th></tr>";
		
		$list_output .= "<tr><td align='left'><input type='text' name='theater_name' id='theater_name' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"theater_name\",new Array(".@implode(",",$theaters_list).").sort(),\"ministatus_ajax.php\",this.form,\"mini\", 8); }' ></td><td colspan='7' id='theater_details'></td></tr>";
                $list_output .="</table></form>";
                $list_output .= "<table class='contenttable' width='98%' align='center'>";
		$list_output .="<tr><td  id='mini'></td></tr>";
		$list_output .="</table>";
	}
	return $list_output;
} 



function save_status($status)
{
	global $db;
	$timings_screens = $status['timings'];
	$allshows = $status['allshows'];
	$movies = $status['movie'];
	foreach($timings_screens as $tid => $timings)
	{
		foreach($timings as $screen => $times)
		{
			$mid = $show_times = array();
			$show_times = explode(",",$times);
			
			if($allshows[$tid])
			{
				$mid[0]=$mid[1]=$mid[2]=$mid[3]=$mid[4] = get_any("movies",$movies[$tid][0][0],"mid","movie");
			}
			else
			{
				$mid[0] = get_any("movies",$movies[$tid][$screen][0],"mid","movie");
				$mid[1] = get_any("movies",$movies[$tid][$screen][1],"mid","movie");
				$mid[2] = get_any("movies",$movies[$tid][$screen][2],"mid","movie");
				$mid[3] = get_any("movies",$movies[$tid][$screen][3],"mid","movie");
				$mid[4] = get_any("movies",$movies[$tid][$screen][4],"mid","movie");
				$mid[5] = get_any("movies",$movies[$tid][$screen][5],"mid","movie");					
			}
					
			$playingshows=array();
			$plays = mysql_query("select * from playing where tid='".$tid."' and screen='".$screen."' and status='1'");
			while($play = mysql_fetch_array($plays))
			{
				$playingshows[$play['showid']] = $play['mid'];
			}
			
			for($i=0; $i<=5;$i++)
			{	
				//Morning Show
				if($playingshows[$i] != $mid[$i])
				{
					$sql = mysql_query("update playing set status='0', edate=now() where mid='".$playingshows[$i]."' and tid='".$tid."'  and screen='".$screen."' and status='1' and showid='".$i."'");	
					if($mid[$i] && $show_times[$i])
					{
						$query = mysql_query("insert into playing(mid, tid, showid, screen, status,timings) values('".$mid[$i]."','".$tid."', '".$i."','".$screen."', '1', '".$show_times[$i]."')");
						
						$cid = get_any("theaters",$tid,"cid", "tid");
						$social_check = mysql_query("select * from social where cid='".$cid."' and mtid='".$mid[$i]."' and idtype='m'");
						if(!mysql_num_rows($social_check))
						{
							$surl = "index.php?city=".get_any("cities", $cid, "city","cid")."&movie=".get_any("movies",$mid[$i],"movie","mid");
							$surl_main = set_url($surl);
							$surl_comments = set_url($surl."&tab=comments");
							$surl_alerts = set_url($surl."&tab=mobile_alerts");
							$surl_invite = set_url($surl."&tab=email");
							
							/*$social = mysql_query("insert into social(surl, mtid, cid, tab) values('".$surl_main."','".$mid[$i]."','".$cid."','main'),('".$surl_comments."','".$mid[$i]."','".$cid."','comments'),('".$surl_alerts."','".$mid[$i]."','".$cid."','alerts'), ('".$surl_invite."','".$mid[$i]."','".$cid."','invite')");*/
						//	$social = mysql_query("insert into social(surl, mtid, cid, tab) values('".$surl_main."','".$mid[$i]."','".$cid."','main'),('".$surl_comments."','".$mid[$i]."','".$cid."','comments')");
						}
						
					}
				}
				else
				{
					//update vlues if already in theater
					$sql = mysql_query("update playing set timings ='".$show_times[$i]."'  where mid='".$playingshows[$i]."' and tid='".$tid."' and screen='".$screen."' and status='1' and showid='".$i."'");
				}
			}
		}
	}
	header("Location:cpanel.php?option=theaterstatus&cid=".$status['cid']."&msg=Updated");
}
?>