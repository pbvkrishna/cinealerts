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
$top_form .= '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=movies">Add Movie</a><br><br><form action="cpanel.php?option=movies" method="post">Movie Name: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /></span><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{	
	case 'delete': delete($_REQUEST['mid']);
					break;
	case 'update': if(update_movie($_POST))
				 {
				 	header("Location:cpanel.php?option=movies&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					
					$output = listall(0);
				 }
				 break;
	case 'save' :if(save_movie($_POST))
				 {
				 	header("Location:cpanel.php?option=movies&msg=Sucessfully Added");
				 }
				 else
				 {
					$msg = "Unable to save";
					$output = listall(0);
				 }
				 break;
	default:$output = listall($start);
}

$output = $top_form . $output;


//User Functions
function listall($start = 0)
{
	global $db;
	if($_REQUEST['keyword'])
	{
		$keywords = trim($_REQUEST['keyword']);
		$keys = explode(" ", $keywords);
		foreach($keys as $key)
		{
			$cond[] = ' movie like "%'.$key.'%" ';
		}
		
	}
	if($cond)
	{
		$conds = " where ".implode(" or ", $cond) . " ";
	}
	$movies = mysql_query('select * from movies '.$conds);
	$totalrecords = mysql_num_rows($movies);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"rdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$movies = mysql_query('select * from movies '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);

	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='55%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	
	$list_output .= "<th><b><a href='cpanel.php?option=movies&start=".$start."&orderby=movie&sorder=".$next_order."'>Movie</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=movies&start=".$start."&orderby=rating&sorder=".$next_order."'>Rating</a></b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($movie = mysql_fetch_array($movies))
	{
		
		$list_output .= "<tr><td><a href='cpanel.php?option=movies&task=edit&mid=".$movie['mid']."'>".$movie['movie']."</a></td>";
		$list_output .= "<td>".$movie['rating']."</td>";
		$list_output .= "<td><a href='cpanel.php?option=movies&task=edit&mid=".$movie['mid']."'>Edit</a> - <a href='cpanel.php?option=movies&task=delete&start=".$_REQUEST['start']."&mid=".$movie['mid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table>";
	$list_output .= pagenav($start,"admin/cpanel.php?option=movies",$totalrecords);
	$list_output .="</td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_movie = mysql_fetch_array(mysql_query("select * from movies where mid='".$_REQUEST['mid']."'"));
		$list_output .=view_form($edit_movie);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 

function view_form($movie)
{
	global $membertypes, $languagetypes;
	
	$list_output .="<form action='cpanel.php?option=movies' name='movies' method='post' enctype='multipart/form-data'>";
	if($movie['mid'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='mid' value='".$movie['mid']."'>";
	}
	else
	{
		$task = "save";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>Movie:</td><td><input type='text' name='movie' value='".$movie['movie']."' /></td></tr>";
	$list_output .="<tr><td>Language:</td><td><select name='language'>";
	
	foreach($languagetypes as $languagetype)
	{
		if($movie['language'] == $languagetype)
		{
			$lselected= " selected='selected' ";
		}
		else
		{
			$lselected = "";
		}
		$list_output .="<option ".$lselected.">".$languagetype."</option>";
	}
	$list_output .="</select></td></tr>";
	$list_output .="<tr><td>Hero:</td><td><input type='text' name='hero' value='".$movie['hero']."' /></td></tr>";
	$list_output .="<tr><td>Heroine:</td><td><input type='text' name='heroine' value='".$movie['heroine']."' /></td></tr>";
	$list_output .="<tr><td>Others:</td><td><input type='text' name='other' value='".$movie['other']."' /></td></tr>";
	$list_output .="<tr><td>Banner:</td><td><input type='text' name='banner' value='".$movie['banner']."' /></td></tr>";
	$list_output .="<tr><td>Director:</td><td><input type='text' name='director' value='".$movie['director']."' /></td></tr>";
	$list_output .="<tr><td>Music:</td><td><input type='text' name='music' value='".$movie['music']."' /></td></tr>";
	$list_output .="<tr><td>Producer:</td><td><input type='text' name='producer' value='".$movie['producer']."' /></td></tr>";
	$list_output .="<tr><td>Order:</td><td><input type='text' name='morder' value='".$movie['morder']."' /></td></tr>";
	$list_output .="<tr><td>Rating:</td><td><input type='text' name='rating' value='".$movie['rating']."' /></td></tr>";
	$list_output .="<tr><td>Release Date:</td><td><input type='text' name='rdate' readonly='readonly' value='".$movie['rdate']."' /><img src='../calendar/b_calendar.jpg'  onClick='displayCalendar(document.movies.rdate,\"yyyy-mm-dd\",this); event.keyCode=9;'/></td></tr>";
	$list_output .="<tr><td colspan='2'>Review:<br /><textarea rows='6' cols='40' name='review' class='widgEditor'>".$movie['review']."</textarea></td></tr>";
	$list_output .="<tr><td>Image: (250 x 250)</td><td><input type='file' name='mimage' /></td></tr>";

	$list_output .="<tr><td align='center' colspan='2'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="<tr><td align='center' colspan='2'><img src='../posters/thumbs/".$movie['mid'].".jpg' /></td></tr>";
	
	$list_output .="</table></form>";
	return $list_output;
}

function save_movie($movie)
{
	global $db;
	
	
	$query = "insert into movies(movie, language,  hero, heroine, other, banner, director, music, producer, morder, rating, review, rdate) values('".trim($_POST['movie'])."', '".$_POST['language']."', '".trim($_POST['hero'])."', '".$_POST['heroine']."', '".$_POST['other']."','".$_POST['banner']."','".$_POST['director']."','".$_POST['music']."','".$_POST['producer']."','".$_POST['morder']."','".$_POST['rating']."','".$_POST['review']."','".$_POST['rdate']."')";
	$result=mysql_query($query);
	if($result)
	{
			$id = mysql_insert_id();
			
			$path_parts = pathinfo($_FILES['mimage']['name']);
			$extension = $path_parts['extension'];
				
			$extension = strtolower($extension);
				
			if (is_uploaded_file($_FILES['mimage']['tmp_name'])) 
			{
				if(!move_uploaded_file($_FILES['mimage']['tmp_name'],"../posters/".$id.".".$extension) && ($extension =="jpg"))
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

function update_movie($movie)
{
	global $db;
	
	
	$query = "update movies set movie='".trim($_POST['movie'])."' , language='".$_POST['language']."' , hero= '".$_POST['hero']."', heroine='".$_POST['heroine']."', other='".$_POST['other']."', banner='".$_POST['banner']."', director='".$_POST['director']."', music='".$_POST['music']."', producer='".$_POST['producer']."', morder='".$_POST['morder']."', rating='".$_POST['rating']."', review='".$_POST['review']."', rdate='".$_POST['rdate']."' where mid='".$_POST['mid']."'";
		
	$result=mysql_query($query);
	if($result)
	{
			$id=$_POST['mid'];
			$path_parts = pathinfo($_FILES['mimage']['name']);
			$extension = $path_parts['extension'];
				
			$extension = strtolower($extension);
				
			if (is_uploaded_file($_FILES['mimage']['tmp_name'])) 
			{
				if(!move_uploaded_file($_FILES['mimage']['tmp_name'],"../posters/".$id.".".$extension) && ($extension =="jpg"))
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


function delete($mid)
{
	global $db;
	$query = "delete from movies where mid='".$mid."'";
	mysql_query($query);
	@unlink("../posters/".$mid.".jpg");
	@unlink("../posters/thumbs/".$mid.".jpg");
	header("Location:cpanel.php?option=movies&msg=Movie Deleted!");
}

function create_thumb($img){
	// The file
	$filename = '../posters/'.$img;
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
	
	if ($_FILES['mimage']['type'] == "image/png")
		$image = imagecreatefrompng($filename);
  	if ($_FILES['mimage']['type'] == "image/jpeg")
		$image = imagecreatefromjpeg($filename);
  	if ($_FILES['mimage']['type'] == "image/gif")
		$image = imagecreatefromgif($filename);
	
		
	@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	$WaterMarkText = "CineAlerts.com";
	$black = imagecolorallocate($image_p, 255, 255, 255);
	$font = '../includes/monofont.ttf';
	$font_size = 14;

	imagettftext($image_p, $font_size, 0, 5, ($height/2), $black, $font, $WaterMarkText);

	// Output for Product list Image
	$image12 = '../posters/thumbs/'.$img;
	imagejpeg($image_p, $image12, 100);
	return $image12;
}

?>
