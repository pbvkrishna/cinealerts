<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$top_form .= '<link rel="stylesheet" type="text/css" href="../editor/widgEditor.css" /> 
<script type="text/javascript" src="../editor/widgEditor.js"></script>';

$top_form .= '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=articles">Add Article</a><br><br><form action="cpanel.php?option=articles" method="post">City: <select name="cid"><option value="0">All Cities</option> '.get_cities($_REQUEST['cid']).' </select> Search for: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /></span><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	
	case 'delete': delete($_REQUEST['aid']);
					break;
	case 'update': if(update_article($_POST))
				 {
				 	header("Location:cpanel.php?option=articles&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					
					$output = listall($_REQUEST['start']?$_REQUEST['start']:0);
				 }
				 break;
	case 'save' :if(save_article($_POST))
				 {
				 	header("Location:cpanel.php?option=articles&msg=Sucessfully Added");
				 }
				 else
				 {
					$msg = "Unable to save";
					$output = listall($_REQUEST['start']?$_REQUEST['start']:0);
				 }
				 break;
	default:$output = listall($_REQUEST['start']?$_REQUEST['start']:0);
}

$output = $top_form . $output;


//User Functions

function listall($start = 0)
{
	global $db;
	if(trim($_REQUEST['keyword']))
	{
		$keywords = trim($_REQUEST['keyword']);
		
			$cond[] = ' atitle like "%'.$keywords.'%" ';
		
	}
	if($_REQUEST['aid'])
	{
		$cond[] = ' aid = "'.$_REQUEST['aid'].'" ';
	}
	if($cond)
	{
		$conds = " where ".implode(" and ", $cond) . " ";
	}
	$articles = mysql_query('select * from articles '.$conds);
	$totalrecords = mysql_num_rows($articles);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$articles = mysql_query('select * from articles '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);
	
	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='45%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	
	$list_output .= "<th><b><a href='cpanel.php?option=articles&start=".$start."&orderby=atitle&sorder=".$next_order."'>Title</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=articles&start=".$start."&orderby=cid&sorder=".$next_order."'>City</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=articles&start=".$start."&orderby=atype&sorder=".$next_order."'>Type</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=articles&start=".$start."&orderby=publish&sorder=".$next_order."'>Publish</a></b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($article = mysql_fetch_array($articles))
	{
		
		$list_output .= "<tr><td><a href='cpanel.php?option=articles&task=edit&aid=".$article['aid']."&cid=".$article['cid']."&start=".$_REQUEST['start']."'>".$article['atitle']."</a></td>";
		$list_output .= "<td>".get_any("cities",$article['cid'],"city","cid")."</td>";
		$list_output .= "<td>".$article['atype']."</td>";
		$list_output .= "<td>".$article['publish']."</td>";
		$list_output .= "<td><a href='cpanel.php?option=articles&task=edit&aid=".$article['aid']."&cid=".$article['cid']."&start=".$_REQUEST['start']."'>Edit</a> - <a href='cpanel.php?option=articles&task=delete&start=".$_REQUEST['start']."&aid=".$article['aid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table>";
	$list_output .= pagenav($start,"admin/cpanel.php?option=articles&cid=".$_REQUEST['cid'],$totalrecords);
	$list_output .="</td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_article = mysql_fetch_array(mysql_query("select * from articles where aid='".$_REQUEST['aid']."'"));
		$list_output .=view_form($edit_article);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 

function view_form($article)
{
	global $membertypes;
	
	$list_output .="<form action='cpanel.php?option=articles' method='post' enctype='multipart/form-data'>";
	if($article['aid'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='aid' value='".$article['aid']."'>";
	}
	else
	{
		$task = "save";
	}
	$article['atitle'] = $article['atitle']?$article['atitle']:'';
	$article['pagetitle'] = $article['pagetitle']?$article['pagetitle']:"";
	$article['introtext'] = $article['introtext']?$article['introtext']:"";
	$article['maintext'] = $article['maintext']?$article['maintext']:"";
	if($article['publish'] == 1)
	{
		$publish = " checked='checked' ";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>City:<br/><select name='cid'><option value='0'>All Cities</option> ".get_cities($article['cid'])." </select></td></tr>";
	$list_output .="<tr><td>Article Title:<br/><input type='text' name='atitle' size='60' value='".$article['atitle']."' /></td></tr>";
	$list_output .="<tr><td>Page Title:<br/><input type='text' name='pagetitle' size='60' value='".$article['pagetitle']."' /></td></tr>";
	$list_output .="<tr><td>Article Type:<br/><select name='atype'> ".get_article_types($article['atype'])." </select></td></tr>";
	$list_output .="<tr><td>Publish<br/><input type='checkbox' name='publish' value='1' ".$publish." /></td></tr>";
	$list_output .="<tr><td colspan='2'>Intro Text:<br /><textarea rows='6' cols='40' name='introtext' class='widgEditor'>".$article['introtext']."</textarea></td></tr>";
	$list_output .="<tr><td colspan='2'>Main Text:<br /><textarea rows='6' cols='40' name='maintext' class='widgEditor'>".$article['maintext']."</textarea></td></tr>";

	$list_output .="<tr><td align='center'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="</table></form>";

	return $list_output;
}

function save_article($article)
{
	global $db;
	
	
	$query = "insert into articles(cid, atype, atitle, pagetitle, introtext, maintext, publish) values('".$_POST['cid']."', '".$_POST['atype']."', '".$_POST['atitle']."', '".$_POST['pagetitle']."', '".$_POST['introtext']."', '".$_POST['maintext']."', '".$_POST['publish']."')";
	
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

function update_article($article)
{
	global $db;
	$query = "update articles set atype='".$_POST['atype']."' , cid= '".$_POST['cid']."', atitle= '".$_POST['atitle']."', pagetitle= '".$_POST['pagetitle']."', introtext='".$_POST['introtext']."', maintext='".$_POST['maintext']."', publish='".$_POST['publish']."' where aid='".$_POST['aid']."'";
		
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


function delete($aid)
{
	global $db;
	$query = "delete from articles where aid='".$aid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=articles&msg=Article Deleted!");
}


function get_article_types($atype)
{
	$atypes = array("footer", "right");
	foreach($atypes as $aty)
	{
		if($aty == $atype)
		{
			$output .="<option value='".$aty."' selected='selected'>".$aty."</option>";		
		}
		else
		{
			$output .="<option value='".$aty."'>".$aty."</option>";		

		}
	}
	return $output;
}
?>