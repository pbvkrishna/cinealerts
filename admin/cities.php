<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$top_form = '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=cities">Add City</a><br><br><form action="cpanel.php?option=cities" method="post">City Name: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /></span><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	
	case 'delete': delete($_REQUEST['cid']);
					break;
	case 'update': if(update_city($_POST))
				 {
				 	header("Location:cpanel.php?option=cities&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					
					$output = listall(0);
				 }
				 break;
	case 'save' :if(save_city($_POST))
				 {
				 	header("Location:cpanel.php?option=cities&msg=Sucessfully Added");
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
			$cond[] = ' city like "%'.$key.'%" ';
		}
		
	}
	if($cond)
	{
		$conds = " where ".implode(" or ", $cond) . " ";
	}
	$cities = mysql_query('select * from cities '.$conds);
	$totalrecords = mysql_num_rows($cities);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cid";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$cities = mysql_query('select * from cities '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);

	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='85%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	
	$list_output .= "<tr><th><b><a href='cpanel.php?option=cities&start=".$start."&orderby=city&sorder=".$next_order."'>City</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=cities&start=".$start."&orderby=lorder&sorder=".$next_order."'>List Order</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=cities&start=".$start."&orderby=district&sorder=".$next_order."'>District</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=cities&start=".$start."&orderby=country&sorder=".$next_order."'>Country</a></b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($city = mysql_fetch_array($cities))
	{
		
		$list_output .= "<tr><td><a href='cpanel.php?option=cities&task=edit&cid=".$city['cid']."'>".$city['city']."</a></td>";
		$list_output .= "<td>".$city['lorder']."</td>";
		$list_output .= "<td>".$city['district']."</td>";
		$list_output .= "<td>".$city['country']."</td>";
		$list_output .= "<td><a href='cpanel.php?option=cities&task=edit&cid=".$city['cid']."'>Edit</a> - <a href='cpanel.php?option=cities&task=delete&start=".$_REQUEST['start']."&cid=".$city['cid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table></td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_city = mysql_fetch_array(mysql_query("select * from cities where cid='".$_REQUEST['cid']."'"));
		$list_output .=view_form($edit_city);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 

function view_form($city)
{
	global $membertypes;
	
	$list_output .="<form action='cpanel.php?option=cities' method='post'>";
	if($city['cid'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='cid' value='".$city['cid']."'>";
	}
	else
	{
		$task = "save";
		$publish = " checked='checked' ";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>City Name:<br/><input type='text' name='city' value='".$city['city']."' /></td></tr>";
	$list_output .="<tr><td>District:<br/><input type='text' name='district' value='".$city['district']."' /></td></tr>";
	$list_output .="<tr><td>Country:<br/><input type='text' name='country' value='".$city['country']."' /></td></tr>";
	$list_output .="<tr><td>List Order:<br/><input type='text' name='lorder' value='".$city['lorder']."' /></td></tr>";
	if($city['publish'] == 1)
	{
		$publish = " checked='checked' ";
	}
	$list_output .="<tr><td>Publish:<br/><input type='checkbox' name='publish' value='1' ".$publish." /></td></tr>";
	$list_output .="<tr><td align='center'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="</table></form>";
	return $list_output;
}

function save_city($city)
{
	global $db;
	
	
	$query = "insert into cities(city, district, country, publish, lorder) values('".$_POST['city']."', '".$_POST['district']."', '".$_POST['country']."', '".$_POST['publish']."', '".$_POST['lorder']."')";
	
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

function update_city($city)
{
	global $db;
	
	
	$query = "update cities set city='".$_POST['city']."' , district= '".$_POST['district']."', country= '".$_POST['country']."', publish= '".$_POST['publish']."', lorder= '".$_POST['lorder']."' where cid='".$_POST['cid']."'";
		
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


function delete($cid)
{
	global $db;
	$query = "delete from cities where cid='".$cid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=cities&msg=City Deleted!");
}
?>
