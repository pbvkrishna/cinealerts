<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$top_form = '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=comments">Add Comment</a><br><br><form action="cpanel.php?option=social" method="post">City: <select name="cid"><option value="0">All Cities</option> '.get_cities($_REQUEST['cid']).' </select><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	
	case 'delete': delete($_REQUEST['sid']);
					break;
	case 'update': if(update_status($_REQUEST))
					 {
						header("Location:cpanel.php?option=social&msg=Sucessfully Updated");
					 }
					 else
					 {
						$msg = "Unable to update";
						
						$output = listall(0);
					 }
					break;
	default:$output = listall(0);
}

$output = $top_form . $output;


//User Functions

function listall($start = 0)
{
	global $db, $membertypes;
	
	if($_REQUEST['cid'])
	{
		$conds = ' where cid = "'.$_REQUEST['cid'].'" ';
	}
	
	$socials = mysql_query('select * from social '.$conds);
	$totalrecords = mysql_num_rows($socials);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$socials = mysql_query('select * from social '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	$list_output .= "<tr><th><b>URL</b></th>";
	$list_output .= "<th><b>Facebook</b></th>";
	$list_output .= "<th><b>Twitter</b></th>";
	$list_output .= "<th><b>G-Buzz</b></th>";
	$list_output .= "<th><b>Digg</b></th>";
	$list_output .= "<th><b>Y-Buzz</b></th>";
	$list_output .= "<th><b>LinkedIn</b></th>";
	$list_output .= "<th><b>MySpace</b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($social = mysql_fetch_array($socials))
	{
		
		$list_output .= "<tr><td><a href='".$social['surl']."' target='_blank'>".get_any("movies",$social['mtid'], "movie","mid")."-".$social['tab'].",".get_any("cities",$social['cid'],"city","cid")."</a></td>";
		
		$facebook = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=facebook&svalue=".($social['facebook']?0:1)."'><img src='images/".$social['facebook'].".png' border='0'></a>";
		$list_output .= "<td>".$facebook."</td>";
		$twitter = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=twitter&svalue=".($social['twitter']?0:1)."'><img src='images/".$social['twitter'].".png' border='0'></a>";
		$list_output .= "<td>".$twitter."</td>";
		$gbuzz = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=gbuzz&svalue=".($social['gbuzz']?0:1)."'><img src='images/".$social['gbuzz'].".png' border='0'></a>";
		$list_output .= "<td>".$gbuzz."</td>";
		$digg = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=digg&svalue=".($social['digg']?0:1)."'><img src='images/".$social['digg'].".png' border='0'></a>";
		$list_output .= "<td>".$digg."</td>";
		$ybuzz = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=ybuzz&svalue=".($social['ybuzz']?0:1)."'><img src='images/".$social['ybuzz'].".png' border='0'></a>";
		$list_output .= "<td>".$ybuzz."</td>";
		$linkedin = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=linkedin&svalue=".($social['linkedin']?0:1)."'><img src='images/".$social['linkedin'].".png' border='0'></a>";
		$list_output .= "<td>".$linkedin."</td>";
		$myspace = "<a href='cpanel.php?option=social&task=update&sid=".$social['sid']."&start=".$start."&site=myspace&svalue=".($social['myspace']?0:1)."'><img src='images/".$social['myspace'].".png' border='0'></a>";
		$list_output .= "<td>".$myspace."</td>";
	
		
		$list_output .= "<td><a href='cpanel.php?option=social&task=delete&start=".$_REQUEST['start']."&sid=".$social['sid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table>";
	
	
	
	return $list_output;
} 


function update_status($social)
{
	global $db;
	
	$query = "update social set ".$_REQUEST['site']."='".$_REQUEST['svalue']."' where sid='".$_REQUEST['sid']."'";
		
	$result=mysql_query($query);
	if($result)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}



function delete($sid)
{
	global $db;
	$query = "delete from social where sid='".$sid."'";
	//Send mail to user
	mysql_query($query);
	header("Location:cpanel.php?option=social&msg=URL Deleted!");
}
?>
