<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$top_form .= '<link rel="stylesheet" type="text/css" href="../editor/widgEditor.css" /> 
<script type="text/javascript" src="../editor/widgEditor.js"></script>';

$top_form .= '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=theaters">Add Theater</a><br><br><form action="cpanel.php?option=theaters" method="post">City: <select name="cid"><option value="0">All Cities</option> '.get_cities($_REQUEST['cid']).' </select> Theater Name: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /></span><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	
	case 'delete': delete($_REQUEST['tid']);
					break;
	case 'update': if(update_theater($_POST))
				 {
				 	header("Location:cpanel.php?option=theaters&cid=".$_REQUEST['cid']."&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					
					$output = listall($_REQUEST['start']?$_REQUEST['start']:0);
				 }
				 break;
	case 'save' :if(save_theater($_POST))
				 {
				 	header("Location:cpanel.php?option=theaters&cid=".$_REQUEST['cid']."&msg=Sucessfully Added");
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
		
			$cond[] = ' theater like "%'.$keywords.'%" ';
		
	}
	if($_REQUEST['cid'])
	{
		$cond[] = ' cid = "'.$_REQUEST['cid'].'" ';
	}
	if($cond)
	{
		$conds = " where ".implode(" and ", $cond) . " ";
	}
	$theaters = mysql_query('select * from theaters '.$conds);
	$totalrecords = mysql_num_rows($theaters);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"theater";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$theaters = mysql_query('select * from theaters '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);
	
	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='65%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	
	$list_output .= "<th><b><a href='cpanel.php?option=theaters&start=".$start."&orderby=theater&sorder=".$next_order."'>Theater</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=theaters&start=".$start."&orderby=cid&sorder=".$next_order."'>City</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=theaters&start=".$start."&orderby=shows&sorder=".$next_order."'>Shows</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=theaters&start=".$start."&orderby=screens&sorder=".$next_order."'>Screens</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=theaters&start=".$start."&orderby=timings&sorder=".$next_order."'>Timings</a></b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($theater = mysql_fetch_array($theaters))
	{
		
		$list_output .= "<tr><td><a href='cpanel.php?option=theaters&task=edit&cid=".$theater['cid']."&tid=".$theater['tid']."&start=".$_REQUEST['start']."'>".$theater['theater']."</a></td>";
		$list_output .= "<td>".get_any("cities",$theater['cid'],"city","cid")."</td>";
		$list_output .= "<td>".$theater['shows']."</td>";
		$list_output .= "<td>".$theater['screens']."</td>";
		$list_output .= "<td>".$theater['timings']."</td>";
		$list_output .= "<td><a href='cpanel.php?option=theaters&task=edit&cid=".$theater['cid']."&tid=".$theater['tid']."'>Edit</a> - <a href='cpanel.php?option=theaters&task=delete&start=".$_REQUEST['start']."&tid=".$theater['tid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table>";
	$list_output .= pagenav($start,"admin/cpanel.php?option=theaters&cid=".$_REQUEST['cid'],$totalrecords);
	$list_output .="</td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_theater = mysql_fetch_array(mysql_query("select * from theaters where tid='".$_REQUEST['tid']."'"));
		$list_output .=view_form($edit_theater);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 

function view_form($theater)
{
	global $membertypes;
	
	$list_output .="<form action='cpanel.php?option=theaters' method='post' enctype='multipart/form-data'>";
	if($theater['tid'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='tid' value='".$theater['tid']."'>";
	}
	else
	{
		$task = "save";
	}
	$theater['shows'] = $theater['shows']?$theater['shows']:4;
	$theater['screens'] = $theater['screens']?$theater['screens']:1;
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>Theater:<br/><input type='text' name='theater' value='".$theater['theater']."' /></td></tr>";
	$list_output .="<tr><td>City:<br/><select name='cid'> ".get_cities($theater['cid'])." </select></td></tr>";
	$list_output .="<tr><td>Area:<br/><input type='text' name='area' value='".$theater['area']."' /></td></tr>";
	$list_output .="<tr><td>Country:<br/><select name='country'> ".get_countries($theater['country'])." </select></td></tr>";
	$list_output .="<tr><td>Shows:<br/><input type='text' name='shows' value='".$theater['shows']."' /></td></tr>";
	$list_output .="<tr><td>Screens:<br/><input type='text' name='screens' value='".$theater['screens']."' /></td></tr>";
	$list_output .="<tr><td>Timings:<br/><input type='text' name='timings' value='".$theater['timings']."' /></td></tr>";
	$list_output .="<tr><td>Contact No.:<br/><input type='text' name='contact' value='".$theater['contact']."' /></td></tr>";

	$list_output .="<tr><td colspan='2'>Review:<br /><textarea rows='6' cols='40' name='review' class='widgEditor'>".$theater['review']."</textarea></td></tr>";
	$list_output .="<tr><td>Image: (250 x 250)</td><td><input type='file' name='timage' /></td></tr>";

	$list_output .="<tr><td align='center'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="</table></form>";
	$list_output .="<tr><td align='center' colspan='2'><img src='../theaters/thumbs/".$theater['tid'].".jpg' /></td></tr>";

	return $list_output;
}

function save_theater($theater)
{
	global $db;
	
	
	$query = "insert into theaters(theater, cid, area, country, shows, screens, timings, contact, review) values('".$_POST['theater']."', '".$_POST['cid']."', '".$_POST['area']."', '".$_POST['country']."', '".$_POST['shows']."', '".$_POST['screens']."', '".$_POST['timings']."', '".$_POST['contact']."', '".$_POST['review']."')";
	
	$result=mysql_query($query);
	if($result)
	{
			$id = mysql_insert_id();
			
			$path_parts = pathinfo($_FILES['timage']['name']);
			$extension = $path_parts['extension'];
				
			$extension = strtolower($extension);
				
			if (is_uploaded_file($_FILES['timage']['tmp_name'])) 
			{
				if(!move_uploaded_file($_FILES['timage']['tmp_name'],"../theaters/".$id.".".$extension) && ($extension =="jpg"))
				{
					$msg = "Image upload error. Type Doesn't Support<br>";
				}
				else
				{
					create_thumb($id.".".$extension);
				}
			 }
		return true;
	}
	else
	{
		return false;
	}
}

function update_theater($theater)
{
	global $db;
	$query = "update theaters set theater='".$_POST['theater']."' , cid= '".$_POST['cid']."', area= '".$_POST['area']."', country= '".$_POST['country']."', shows='".$_POST['shows']."', screens='".$_POST['screens']."', timings='".$_POST['timings']."', contact='".$_POST['contact']."', review='".$_POST['review']."' where tid='".$_POST['tid']."'";
		
	$result=mysql_query($query);
	if($result)
	{
		$id=$_POST['tid'];
		$path_parts = pathinfo($_FILES['timage']['name']);
		$extension = $path_parts['extension'];
			
		$extension = strtolower($extension);
			
		if (is_uploaded_file($_FILES['timage']['tmp_name'])) 
		{
			if(!move_uploaded_file($_FILES['timage']['tmp_name'],"../theaters/".$id.".".$extension) && ($extension =="jpg"))
			{
				$msg = "Image upload error. Type Doesn't Support<br>";
			}
			else
			{
				create_thumb($id.".".$extension);
			}
		 }
		return true;
	}
	else
	{
		return false;
	}
}


function delete($tid)
{
	global $db;
	$query = "delete from theaters where tid='".$tid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=theaters&msg=Theater Deleted!");
}

function create_thumb($img){
	// The file
	$filename = '../theaters/'.$img;
	if(!file_exists($filename))
	{
		echo "Unable";
	}
	
	// Set a maximum height and width
	$width = THUMB_WIDTH;
	$height = THUMB_HEIGHT;

	// Get new dimensions
	list($width_orig, $height_orig) = getimagesize($filename);
	if ($width && ($width_orig < $height_orig)) {
	   $width = ($height / $height_orig) * $width_orig;
	} else {
	   $height = ($width / $width_orig) * $height_orig;
	}

	// Resize
	$image_p = imagecreatetruecolor($width, $height);
	
	if ($_FILES['timage']['type'] == "image/png")
		$image = imagecreatefrompng($filename);
  	if ($_FILES['timage']['type'] == "image/jpeg")
		$image = imagecreatefromjpeg($filename);
  	if ($_FILES['timage']['type'] == "image/gif")
		$image = imagecreatefromgif($filename);
		
	@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	//Output for Product list Image
	$image12 = '../theaters/thumbs/'.$img;
	imagejpeg($image_p, $image12, 100);
	imagepng($image_p, $image12, 9);
	return $image12;
}

?>
