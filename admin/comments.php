<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$top_form = '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=comments">Add Comment</a><br><br><form action="cpanel.php?option=comments" method="post">Comment: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	
	case 'delete': delete($_REQUEST['com_id']);
					break;
	case 'aprove': aprove($_REQUEST['com_id'], $_REQUEST['aprove']);
					break;
	case 'update': if(update_comment($_POST))
				 {
				 	header("Location:cpanel.php?option=comments&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					
					$output = listall(0);
				 }
				 break;
	case 'save' :if(save_comment($_POST))
				 {
				 	header("Location:cpanel.php?option=comments&msg=Sucessfully Added");
				 }
				 else
				 {
					$msg = "Unable to save";
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
	if($_REQUEST['keyword'])
	{
		$keywords = trim($_REQUEST['keyword']);
		$keys = explode(" ", $keywords);
		foreach($keys as $key)
		{
			$cond[] = ' comment like "%'.$key.'%" ';
		}	
	}
	if($cond)
	{
		$conds = " where ".implode(" or ", $cond) . " ";
	}
	$comments = mysql_query('select * from comments '.$conds);
	$totalrecords = mysql_num_rows($comments);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$comments = mysql_query('select * from comments '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);

	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='70%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	$list_output .= "<tr><th><b>Username</b></th>";
	$list_output .= "<th><b>Name</b></th>";
	$list_output .= "<th><b>Movie</b></th>";
	$list_output .= "<th><b>Theater</b></th>";
	$list_output .= "<th><b>Comment</b></th>";
	$list_output .= "<th><b>Aprove</b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($comment = mysql_fetch_array($comments))
	{
		$aprove = "<a href='cpanel.php?option=comments&task=aprove&com_id=".$comment['com_id']."&start=".$start."&aprove=".($comment['aprove']?0:1)."'><img src='images/".$comment['aprove'].".png' border='0'></a>";
		$list_output .= "<tr><td>".get_any("users",$comment['uid'], "username", "uid")."</td>";
		$list_output .= "<td>".get_any("users",$comment['uid'], "name", "uid")."</td>";
		$list_output .= "<td>".get_any("movies",$comment['mid'], "movie", "mid")."</td>";
		$list_output .= "<td>";
		
		$theater_details = get_any_record("theaters",$comment['tid'],"tid");
		
		$list_output .=$theater_details['theater'].", ".get_any("cities",$theater_details['cid'], "city","cid")."</td>";
		$list_output .= "<td>".stripslashes($comment['comment'])."</td>";
		$list_output .= "<td>".$aprove."</td>";
		$list_output .= "<td><a href='cpanel.php?option=comments&task=edit&com_id=".$comment['com_id']."'>Edit</a> - <a href='cpanel.php?option=comments&task=delete&start=".$_REQUEST['start']."&com_id=".$comment['com_id']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table></td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_comment = mysql_fetch_array(mysql_query("select * from comments where com_id='".$_REQUEST['com_id']."'"));
		$list_output .=view_form($edit_comment);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 

function view_form($comment)
{
	global $membertypes;
	
	$list_output .="<form action='cpanel.php?option=comments' method='post'>";
	if($comment['com_id'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='com_id' value='".$comment['com_id']."'>";
	}
	else
	{
		$task = "save";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>Movie: ".get_any("movies",$comment['mid'], "movie", "mid")."</td></tr>";
	$list_output .="<tr><td>Theater: ".get_any("theaters",$comment['tid'], "theater","tid")."</td></tr>";
	$list_output .= "<td><textarea name='comment' cols='40' rows='5'>".stripslashes($comment['comment'])."</textarea></td>";

	$list_output .="<tr><td align='center'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="</table></form>";
	return $list_output;
}

function save_comment($comment)
{
	global $db;
	
	
	
		return false;
	
}

function update_comment($comment)
{
	global $db;
	
	
	$query = "update comments set comment='".$_POST['comment']."' where com_id='".$_POST['com_id']."'";
		
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

function aprove($com_id, $aprove)
{
	global $db;
	
	
	$query = "update comments set aprove='".$aprove."' where com_id='".$com_id."'";
		
	$result=mysql_query($query);
	if($result)
	{
		header("Location:cpanel.php?option=comments&msg=Aprove Satus Updated!");
	}
	else
	{
		return false;
	}
	
}


function delete($com_id)
{
	global $db;
	$query = "delete from comments where com_id='".$com_id."'";
	//Send mail to user
	mysql_query($query);
	header("Location:cpanel.php?option=comments&msg=Comment Deleted!");
}
?>
