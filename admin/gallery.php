<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;

$start = $_REQUEST['start']?$_REQUEST['start']:0;
$top_form = '<link href="../calendar/calendar.css" type="text/css" rel="stylesheet" />
<script src="../calendar/calendar.js"></script>
<script src="../calendar/js_dateformat.js"></script>';

$top_form .= '<link rel="stylesheet" type="text/css" href="../editor/widgEditor.css" /> 
<script type="text/javascript" src="../editor/widgEditor.js"></script>';
$top_form .= '<table width="96%" align="center"><tr><td align="right"><br /><form action="cpanel.php?option=gallery" method="post">Gallery Name: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /></span><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{	
	case 'delete': delete($_REQUEST['gid']);
					break;
	case 'aprove': aprove($_REQUEST['gid'], $_REQUEST['aprove']);
					break;
	case 'featured': featured($_REQUEST['gid'], $_REQUEST['featured']);
					break;
	case 'update': if(update_gallery($_POST))
				 {
				 	header("Location:cpanel.php?option=gallery&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					$output = listall(0);
				 }
			break;
	case 'save' :if(save_gallery($_POST))
				 {
				 	header("Location:cpanel.php?option=gallery&msg=Sucessfully Added");
				 }
				 else
				 {
					$msg = "Unable to save";
					$output = listall(0);
				 }
		       break;
	case 'upload': if($_REQUEST['gid'])
			 	$output = uploadform($_REQUEST['gid']);
			break;
	case 'uploadimages':if($_REQUEST['gid'])
			 	{
					$output .= uploadimages($_REQUEST['gid']);
					$output .= uploadform($_REQUEST['gid']);
				}
			break;
	case 'deleteimage':if($_REQUEST['iid'])
			 	{
					$output .= deleteimage($_REQUEST['iid'], $_REQUEST['gid']);

				}
			break;

	default:$output = listall($start);
}

$output = $top_form . $output;


function listall($start = 0)
{
	global $db, $gallerytypes;
	if($_REQUEST['keyword'])
	{
		$keywords = trim($_REQUEST['keyword']);
		$keys = explode(" ", $keywords);
		foreach($keys as $key)
		{
			$cond[] = ' gname like "%'.$key.'%" ';
		}
		
	}
	if($cond)
	{
		$conds = " where ".implode(" or ", $cond) . " ";
	}
	$galleries = mysql_query('select * from galleries '.$conds);
	$totalrecords = @mysql_num_rows($galleries);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$galleries = mysql_query('select * from galleries '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);

	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='55%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	
	$list_output .= "<th><b><a href='cpanel.php?option=gallery&start=".$start."&orderby=gname&sorder=".$next_order."'>Gallery Name</a></b></th>";
	$list_output .= "<th><b>Description</b></th>";
	$list_output .= "<th><b>Keywords</b></th>";
	$list_output .= "<th><b>Type</b></th>";
	$list_output .= "<th><b>Status</b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=gallery&start=".$start."&orderby=featured&sorder=".$next_order."'>Featured</a></b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($gallery = mysql_fetch_array($galleries))
	{
		$aprove = "<a href='cpanel.php?option=gallery&task=aprove&gid=".$gallery['gid']."&start=".$start."&aprove=".($gallery['status']?0:1)."'><img src='images/".$gallery['status'].".png' border='0'></a>";
		$featured = "<a href='cpanel.php?option=gallery&task=featured&gid=".$gallery['gid']."&start=".$start."&featured=".($gallery['featured']?0:1)."'><img src='images/".$gallery['featured'].".png' border='0'></a>";
		$list_output .= "<tr><td><a href='cpanel.php?option=gallery&task=edit&gid=".$gallery['gid']."'>".$gallery['gname']."</a></td>";
		$list_output .= "<td>".$gallery['description']."</td>";
		$list_output .= "<td>".$gallery['keywords']."</td>";
		$list_output .= "<td>".$gallerytypes[$gallery['gtype']]."</td>";
		$list_output .= "<td>".$aprove."</td>";
		$list_output .= "<td>".$featured."</td>";
		$list_output .= "<td><a href='cpanel.php?option=gallery&task=upload&gid=".$gallery['gid']."'>Upload</a> - <a href='cpanel.php?option=gallery&task=edit&gid=".$gallery['gid']."'>Edit</a> - <a href='cpanel.php?option=gallery&task=delete&start=".$_REQUEST['start']."&gid=".$gallery['gid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table>";
	$list_output .= pagenav($start,"admin/cpanel.php?option=gallery",$totalrecords);
	$list_output .="</td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_gallery = mysql_fetch_array(mysql_query("select * from galleries where gid='".$_REQUEST['gid']."'"));
		$list_output .=view_form($edit_gallery);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 


function view_form($gallery)
{
	global $membertypes, $languagetypes, $gallerytypes;
	
	$list_output .="<form action='cpanel.php?option=gallery' name='gallery' method='post' enctype='multipart/form-data'>";
	if($gallery['gid'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='gid' value='".$gallery['gid']."'>";
	}
	else
	{
		$task = "save";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>Gallery Name:</td><td><input type='text' name='gname' value='".$gallery['gname']."' /></td></tr>";
	$list_output .="<tr><td>Type:</td><td><select name='gtype'>";
	
	foreach($gallerytypes as $val=>$gallerytype)
	{
		if($gallery['gtype'] == $val)
		{
			$gselected= " selected='selected' ";
		}
		else
		{
			$gselected = "";
		}
		$list_output .="<option value='".$val."' ".$gselected.">".$gallerytype."</option>";
	}
	$list_output .="</select></td></tr>";
	$list_output .="<tr><td colspan='2'>Description:<br /><textarea rows='6' cols='40' name='description' >".$gallery['description']."</textarea></td></tr>";
	$list_output .="<tr><td colspan='2'>Keywords:<br /><textarea rows='3' cols='40' name='keywords' >".$gallery['keywords']."</textarea></td></tr>";

	if($gallery['featured'])
	{
		$featured_checked = " checked='checked' ";
	}
	$list_output .="<tr><td>Featured:</td><td><input type='checkbox' name='featured' value='1' ".$featured_checked." /></td></tr>";
	$list_output .="<tr><td align='center' colspan='2'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="</table></form>";
	return $list_output;
}

function save_gallery($gallery)
{
	global $db;
	$query = "insert into galleries(gname,gtype, description,  keywords, featured) values('".$_POST['gname']."', '".$_POST['gtype']."', '".$_POST['description']."', '".$_POST['keywords']."', '".$_POST['featured']."')";
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

function viewgallery($gid, $start = 0)
{
	global $db, $gallerytypes;
	$dir = "../galleries/".$gid;
	$gallerydetails = get_any_record("galleries", $gid, "gid");
	$list_output = "Title:".$gallerydetails['gname']."<br/>";
	$list_output .= "Type:".$gallerytypes[$gallerydetails['gtype']]."<br/>";
	$list_output .= "Description:".$gallerydetails['description']."<br/>";
	$list_output .= "Keywords:".$gallerydetails['keywords']."<br/>";
	
	$query = "select * from galleryimages where gid='".$gid."'";
	$gallery = @mysql_query($query);
	$list_output .= '<table width="100%" cellspacing="0" cellpadding="6" >';	
	while($image = mysql_fetch_array($gallery))
	{
		if($i%4 == 0 && $i != 0)
		{
			$list_output .="</tr>";
		}
		if($i==0 || $i%4==0)
		{
			$list_output .= "<tr>";
		}
		$list_output .="<td width='".(100/4)."%' align='center'><img src='../galleries/thumbs/".$image['iid'].".jpg' /><br/><a href='cpanel.php?option=gallery&task=deleteimage&iid=".$image['iid']."&gid=".$gid."'>Delete</a></td>";
		$i++;
	}
	while($i%4 !=0)
	{
		$list_output .="<td width='".(100/4)."%'></td>";
		$i++;
	}
	$list_output .="</tr></table>";		
	return $list_output;
} 


function uploadform($gid)
{
	$list_output = "<table width='98%'><tr><td width='65%' valign='top'>";
	$list_output .= viewgallery($gid,0);
	$list_output .="</td><td valign='top'>Select upto 10 Images to upload";
	$list_output .="<form action='cpanel.php?option=gallery' method='post' enctype='multipart/form-data'>";
	$list_output .="<input type='file' name='img[]' value='Image' multiple='true' />";
	$list_output .="<input type='hidden' name='task' value='uploadimages' />";
	$list_output .="<input type='hidden' name='gid' value='".$gid."' />";
	$list_output .="<input type='submit' value='Upload' />";
	$list_output .="</form>";
	$list_output .="</td></tr></table>";
	return $list_output;
}

function uploadimages($gid)
{
	global $db;
	$count = count($_FILES['img']['tmp_name']);
	for($i=0; $i<$count; $i++)
	{
		if (is_uploaded_file($_FILES['img']['tmp_name'][$i])) 
		{
			$query = "insert into galleryimages(gid) values('".$gid."')";
			$result=mysql_query($query);
			if($result)
			{
				$id = mysql_insert_id();
				$path_parts = pathinfo($_FILES['img']['name'][$i]);
				$extension = $path_parts['extension'];
				$extension = strtolower($extension);
				if (is_uploaded_file($_FILES['img']['tmp_name'][$i])) 
				{
					if(!move_uploaded_file($_FILES['img']['tmp_name'][$i],"../galleries/".$id.".".$extension) && ($extension =="jpg"))
					{
						$msg = "Image upload error. Type Doesn't Support<br>";
					}
					else
					{
						create_thumb($id.".".$extension);
						resizeimage($id.".".$extension);
						watermark("../galleries/".$id.".".$extension,"../galleries/".$id.".".$extension);
						watermark("../galleries/".$id.".".$extension,"../galleries/".$id.".".$extension);
					}
				 }
			}
		}		
	}
}


function update_gallery($gallery)
{
	global $db;
	$query = "update galleries set gname='".$_POST['gname']."' ,gtype='".$_POST['gtype']."' , description='".$_POST['description']."' , keywords= '".$_POST['keywords']."', featured= '".$_POST['featured']."' where gid='".$_POST['gid']."'";
		
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


function delete($gid)
{
	global $db;
	$query = "delete from galleries where gid='".$gid."'";
	mysql_query($query);
	//@unlink("../galleries/".$gid.".jpg");
	//@unlink("../galleries/thumbs/".$gid.".jpg");
	header("Location:cpanel.php?option=gallery&msg=Gallery Deleted!");
}

function deleteimage($iid, $gid)
{
	global $db;
	$query = "delete from galleryimages where iid='".$iid."'";
	mysql_query($query);
	@unlink("../galleries/".$iid.".jpg");
	@unlink("../galleries/thumbs/".$iid.".jpg");
	header("Location:cpanel.php?option=gallery&task=upload&gid=".$gid."&msg=image Deleted!");
}


function resizeimage($img){
	ini_set('memory_limit', '-1');
	// The file
	$filename = '../galleries/'.$img;
	if(!file_exists($filename))
	{
	//	echo "Unable";
	}
	// Set a maximum height and width
	$width = 600;

	// Get new dimensions
	list($width_orig, $height_orig) = getimagesize($filename);
	
	$height = ($width * $height_orig) / $width_orig;
	
	// Resize
	$image_p = imagecreatetruecolor($width, $height);
	
	if ($_FILES['mimage']['type'] == "image/png")
		$image = imagecreatefrompng($filename);
  	else if ($_FILES['mimage']['type'] == "image/gif")
		$image = imagecreatefromgif($filename);
	else $image = imagecreatefromjpeg($filename);
		
	@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	// Output for Product list Image
	$image12 = '../galleries/'.$img;
	imagejpeg($image_p, $image12, 100);
}


function create_thumb($img){
	// The file
	$filename = '../galleries/'.$img;
	if(!file_exists($filename))
	{
		echo "Unable";
	}
	// Set a maximum height and width
	$width = GALLERY_THUMB_WIDTH;
	$height = GALLERY_THUMB_HEIGHT;

	// Get new dimensions
	list($width_orig, $height_orig) = getimagesize($filename);
	$height = ($width * $height_orig) / $width_orig;

	// Resize
	$image_p = imagecreatetruecolor($width, $height);
	
	if ($_FILES['mimage']['type'] == "image/png")
		$image = imagecreatefrompng($filename);
  	else if ($_FILES['mimage']['type'] == "image/gif")
		$image = imagecreatefromgif($filename);
	else $image = imagecreatefromjpeg($filename);
	@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	// Output for Product list Image
	$image12 = '../galleries/thumbs/'.$img;
	imagejpeg($image_p, $image12, 100);
}

function aprove($gid, $status)
{
	global $db;
	
	$query = "update galleries set status='".$status."' where gid='".$gid."'";
		
	$result=mysql_query($query);
	if($result)
	{
		header("Location:cpanel.php?option=gallery&msg=Aprove Satus Updated!");
	}
	else
	{
		return false;
	}
	
}
function featured($gid, $status)
{
	global $db;
	
	$query = "update galleries set featured='".$status."' where gid='".$gid."'";
		
	$result=mysql_query($query);
	if($result)
	{
		header("Location:cpanel.php?option=gallery&msg=Featured Satus Updated!");
	}
	else
	{
		return false;
	}
	
}

?>