<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$start = $_REQUEST['start']?$_REQUEST['start']:0;
$top_form = '
<script src="includes/autocomplete.js"></script>
<script src="includes/browser.js"></script>
<link href="includes/autocomplete.css" type="text/css" rel="stylesheet" />';
$top_form .= '<link rel="stylesheet" type="text/css" href="../editor/widgEditor.css" /> 
<script type="text/javascript" src="../editor/widgEditor.js"></script>';
switch($_REQUEST['task'])
{
	case 'update': if(update_news($_POST))
			{
				header("Location:cpanel.php?option=news&msg=Sucessfully Updated");
				break;
			}
			else
			{
				$msg = "Unable to update";
				$output = listall(0);
			}
			break;
	case 'delete': delete($_REQUEST['nid']);
					break;
	case 'save' :if(save_news($_POST))
			{
				header("Location:cpanel.php?option=news&msg=Sucessfully News Added");
			}
			else
			{
				$msg = "Unable to save";
				$output = listall(0);
			}
			
			break;
	default:$output = listall($start);
			break;
}

$output = $top_form . $output;

//User Functions

function listall($start)
{
	global $db;

	if($_REQUEST['task'] == 'edit' && $_REQUEST['nid'] !='')
	{
		$edit_news = mysql_fetch_array(mysql_query("select * from news where nid='".$_REQUEST['nid']."'"));
		$list_output .=view_form($edit_news);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	
	
			
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$news = mysql_query('select * from news order by '.$orderby.' '.$sort_order);
	$totalrecords = mysql_num_rows($news);
	$list_output .= "<table class='contenttable' width='80%' align='center'>";
	
	$list_output .= "<tr><th width='20'></th><th><b><a href='cpanel.php?option=news&orderby=title&sorder=".$next_order."'>Title</a></b></th><th width='50'>Actions</th>";
	$list_output .= "</tr>";
	
	while($new = mysql_fetch_array($news))
	{
		
		$list_output .= "<tr><td>".++$i."</td><td>".$new['title']."</td><td><a href='cpanel.php?option=news&task=edit&nid=".$new['nid']."'>Edit</a> - <a href='cpanel.php?option=news&task=delete&start=".$_REQUEST['start']."&nid=".$new['nid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
		
	}
	
	$list_output .="</table>";
	$list_output .= pagenav($start,"admin/cpanel.php?option=news",$totalrecords);

	
	return $list_output;
} 

function view_form($news)
{

	$movies = mysql_query('select * from movies');
	while($movie=mysql_fetch_array($movies))
	{
		$movies_list[]="\"".$movie['movie']."\"";
	}
	
	$list_output .="<form action='cpanel.php?option=news' name='news' method='post' enctype='multipart/form-data'>";
	if($news['nid'])
	{
		$task="update";
		$img_up = "<img src='../images/news/".$news['nid'].".jpg' border='0' />";
		$list_output .="<input type='hidden' name='nid' value='".$news['nid']."'>";
		if($news['publish'] == 1)
		{
			$publish = " checked='checked' ";
		}
	}
	else
	{
		$task = "save";
		$publish = " checked='checked' ";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	
	$list_output .="<tr><td>Title:</td><td><input type='text' size='50' name='title' value='".$news['title']."' /> Publish: <input type='checkbox' name='publish' value='1' ".$publish." /></td></tr>";
	
	$list_output .="<tr><td colspan='2'>Intro Text:<br /><textarea rows='4' cols='40' name='introtext' class='widgEditor'>".$news['introtext']."</textarea></td></tr>";

	$list_output .="<tr><td colspan='2'>Mail Text:<br /><textarea rows='6' cols='40' name='maintext' class='widgEditor'>".$news['maintext']."</textarea></td></tr>";

	$list_output .="<tr><td>Image: (250 x 250)</td><td><input type='file' name='nimage' />".$img_up."</td></tr>";
	$list_output .= "<tr><td>Related Movie 1:</td><td><input typ='text' size='30' value='".$news['relatedto1']."' id='relatedto1' name='relatedto1' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"relatedto1\",new Array(".implode(",",$movies_list).").sort(),\"\",\"\",\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	$list_output .= "<tr><td>Related Movie 2:</td><td><input typ='text' size='30' value='".$news['relatedto2']."' id='relatedto2' name='relatedto2' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"relatedto2\",new Array(".implode(",",$movies_list).").sort(),\"\",\"\",\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	$list_output .= "<tr><td>Related Movie 3:</td><td><input typ='text' size='30' value='".$news['relatedto3']."' id='relatedto3' name='relatedto3' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"relatedto3\",new Array(".implode(",",$movies_list).").sort(),\"\",\"\",\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	$list_output .= "<tr><td>Related Movie 4:</td><td><input typ='text' size='30' value='".$news['relatedto4']."' id='relatedto4' name='relatedto4' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"relatedto4\",new Array(".implode(",",$movies_list).").sort(),\"\",\"\",\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	$list_output .= "<tr><td>Related Movie 5:</td><td><input typ='text' size='30' value='".$news['relatedto5']."' id='relatedto5' name='relatedto5' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"relatedto5\",new Array(".implode(",",$movies_list).").sort(),\"\",\"\",\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	$list_output .= "<tr><td>Related Movie 6:</td><td><input typ='text' size='30' value='".$news['relatedto6']."' id='relatedto6' name='relatedto6' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"relatedto6\",new Array(".implode(",",$movies_list).").sort(),\"\",\"\",\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	$list_output .="<tr><td align='center' colspan='2'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="<tr><td align='center' colspan='2'><img src='../images/news/thumbs/".$news['nid'].".jpg' /></td></tr>";

	
	$list_output .="</table></form>";
	return $list_output;
}

function save_news($news)
{
	global $db;
	
	
	$query = "insert into news(title, introtext,  maintext, relatedto1, relatedto2, relatedto3, relatedto4, relatedto5, relatedto6,publish) values('".$_POST['title']."', '".$_POST['introtext']."', '".$_POST['maintext']."', '".$_POST['relatedto1']."', '".$_POST['relatedto2']."', '".$_POST['relatedto3']."', '".$_POST['relatedto4']."', '".$_POST['relatedto5']."', '".$_POST['relatedto6']."', '".$_POST['publish']."')";
	$result=mysql_query($query);
	if($result)
	{
			$id = mysql_insert_id();
			
			$path_parts = pathinfo($_FILES['nimage']['name']);
			$extension = $path_parts['extension'];
				
			$extension = strtolower($extension);
				
			if (is_uploaded_file($_FILES['nimage']['tmp_name'])) 
			{
				if(!move_uploaded_file($_FILES['nimage']['tmp_name'],"../images/news/".$id.".".$extension) && ($extension =="jpg"))
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

function update_news($news)
{
	global $db;
	
	
	$query = "update news set title='".$_POST['title']."' , introtext='".$_POST['introtext']."' , maintext= '".$_POST['maintext']."', relatedto1='".$_POST['relatedto1']."', relatedto2='".$_POST['relatedto2']."', relatedto3='".$_POST['relatedto3']."', relatedto4='".$_POST['relatedto4']."', relatedto5='".$_POST['relatedto5']."', relatedto6='".$_POST['relatedto6']."', publish='".$_POST['publish']."' where nid='".$_POST['nid']."'";
		
	$result=mysql_query($query);
	if($result)
	{
			$id=$_POST['nid'];
			$path_parts = pathinfo($_FILES['nimage']['name']);
			$extension = $path_parts['extension'];
				
			$extension = strtolower($extension);
				
			if(is_uploaded_file($_FILES['nimage']['tmp_name'])) 
			{       

				if(!move_uploaded_file($_FILES['nimage']['tmp_name'],"../images/news/".$id.".".$extension) && ($extension =="jpg"))
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

function delete($nid)
{
	global $db;
	$query = "delete from news where nid='".$nid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=news&msg=Deleted!");
}



function create_thumb($img){
	// The file
	$filename = '../images/news/'.$img;
	if(!file_exists($filename))
	{
		echo "Unable";
                exit();
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
	
	if ($_FILES['nimage']['type'] == "image/png")
		$image = imagecreatefrompng($filename);
  	if ($_FILES['nimage']['type'] == "image/jpeg")
		$image = imagecreatefromjpeg($filename);
  	if ($_FILES['nimage']['type'] == "image/gif")
		$image = imagecreatefromgif($filename);
		
	@imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

	// Output for Product list Image
	$image12 = '../images/news/thumbs/'.$img;
	imagejpeg($image_p, $image12, 100);
	return $image12;
}
?>