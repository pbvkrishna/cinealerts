<?php
@session_start();
include_once("../includes/configuration.php");
include_once("../includes/functions.php");
global $msg;

if($_REQUEST['theater_name'] && !$_REQUEST['tid'])
{
	echo theater_details($_REQUEST['theater_name']);
}

function theater_details($theater_name)
{
	global $db;
	if($_REQUEST['cid'])
	{
	
		$movies = mysql_query('select * from movies');
		while($movie=mysql_fetch_array($movies))
		{
			$movies_list[]="\"".$movie['movie']."\"";
		}
		
		$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"theater";
		$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"ASC";
		
		$next_order = ($sort_order == "DESC")?"ASC":"DESC";
		
		$theaters = mysql_query('select * from theaters where cid = "'.$_REQUEST['cid'].'" and theater="'.$theater_name.'" order by '.$orderby.' '.$sort_order);
	
		$list_output .="<form action='cpanel.php?option=status' method='post'><input type='hidden' name='cid' value='".$_REQUEST['cid']."'><input type='hidden' name='task' value='save'>";
		$list_output .= "<table class='contenttable' width='98%' align='center'>";
		
		$list_output .= "<th><b><a href='cpanel.php?option=status&start=".$start."&orderby=theater&sorder=".$next_order."'>Theater</a></b></th>";
		$list_output .= "<th><b>Timings</b></th>";
		$list_output .= "<th><b>Morning Show</b></th><th><b>Matinee</b></th><th><b>1st Show</b></th><th><b>2nd Show</b></th><th><b>Mid Show</b></th></tr>";
		
		while($theater = mysql_fetch_array($theaters))
		{

			for($screen = 1; $screen <= $theater['screens']; $screen++)
			{
				
				$movies = mysql_query("select * from playing where tid = '".$theater['tid']."' and screen = '".$screen."' and status='1' order by showid ASC");
				$mov = array();
				$times=array();
				while($movie = mysql_fetch_array($movies))
				{
					$mov[$movie['showid']]=get_any("movies",$movie['mid'],"movie","mid");
					$times[] =$movie['timings']; 
				}
				$ctime = $times?implode(",",$times):$theater['timings'];
				$list_output .= "<tr><td>".$theater['theater']."</td>";
				
				$list_output .= "<td><input type='text' name='timings[".$theater['tid']."][".$screen."]' value='".$ctime."' /></td>";
				$sid = 0;
				while($sid < $theater['shows'])
				{
					$allshows = "<input type='checkbox' name='allshows[".$theater['tid']."][".$screen."]' value='1'> All Shows";
					$list_output .= "<td><input type='text' name='movie[".$theater['tid']."][".$screen."][".$sid."]' id='movie[".$theater['tid']."][".$screen."][".$sid."]' value='".$mov[$sid]."' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"movie[".$theater['tid']."][".$screen."][".$sid."]\",new Array(".implode(",",$movies_list).").sort(),\"\",this.form,\"\", 8); }' ></td>";
					$sid++;
				}
				
			}
		}
		$list_output .="<tr><td colspan='8' align='center'><input type='hidden' name='minisave' value='minisave' /><input type='submit' value='Save' /></table></form>";
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
			}
					
			$playingshows=array();
			$plays = mysql_query("select * from playing where tid='".$tid."' and screen='".$screen."' and status='1'");
			while($play = mysql_fetch_array($plays))
			{
				$playingshows[$play['showid']] = $play['mid'];
			}
			
			for($i=0; $i<5;$i++)
			{	
				//Morning Show
				if($playingshows[$i] != $mid[$i])
				{
					$sql = mysql_query("update playing set status='0' where mid='".$playingshows[$i]."' and tid='".$tid."'  and screen='".$screen."' and status='1' and showid='".$i."'");	
					if($mid[$i] && $show_times[$i])
					{
						$query = mysql_query("insert into playing(mid, tid, showid, screen, status,timings) values('".$mid[$i]."','".$tid."', '".$i."','".$screen."', '1', '".$show_times[$i]."')");
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
        if($_REQUEST['minisave'] == "minisave")
        {
        	header("Location:cpanel.php?option=theaterstatus&cid=".$status['cid']."&msg=Updated");
        }
        else
        {
	header("Location:cpanel.php?option=status&cid=".$status['cid']."&msg=Updated");
        }
}
?>
