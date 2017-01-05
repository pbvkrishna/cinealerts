<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;

$top_form = '
<script src="includes/autocomplete.js"></script>
<script src="includes/browser.js"></script>
<link href="includes/autocomplete.css" type="text/css" rel="stylesheet" />';
switch($_REQUEST['task'])
{
	case 'Update': if(update_featured($_POST))
				 {
				 	header("Location:cpanel.php?option=featured&msg=Sucessfully Updated");
				 }
				 else
				 {
					$msg = "Unable to update";
					
					$output = listall();
				 }
				 break;
	
	default:$output = listall();
}

$output = $top_form . $output;

//User Functions

function listall()
{
	global $db;
	
	$movies = mysql_query('select * from movies');
		while($movie=mysql_fetch_array($movies))
		{
			$movies_list[]="\"".$movie['movie']."\"";
		}
			
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"priority";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"ASC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$featureds = mysql_query('select * from featured order by '.$orderby.' '.$sort_order);
	$list_output .="<form method='post'>";
	
	$list_output .= "<table class='contenttable' width='40%' align='center'>";
	
	$list_output .= "<th><b><a href='cpanel.php?option=featured&orderby=priority&sorder=".$next_order."'>Priority</a></b></th>";
	$list_output .= "<th><b>Movie</b></th>";
	$list_output .= "</tr>";
	
	while($featured = mysql_fetch_array($featureds))
	{
		
		$list_output .= "<tr><td><input type='text' size='3' value='".$featured['priority']."' name='p[".$featured['id']."]' /></td>";
		$list_output .= "<td><input typ='text' size='30' value='".get_any("movies",$featured['mid'],"movie","mid")."' id='m[".$featured['id']."]' name='m[".$featured['id']."]' onKeyDown='if(event.keyCode!=13) { AutoComplete_Create(\"m[".$featured['id']."]\",new Array(".implode(",",$movies_list).").sort(),\"\",this.form,\"\", 8); }' /></td>";
		$list_output .= "</tr>";
	}
	$list_output .="<tr><td colspan='2' align='center'><input type='submit' name='task' value='Update'></td></tr></table></form>";

	
	return $list_output;
} 


function update_featured($details)
{
	global $db;
	$movies = $details['m'];
	$prority = $details['p'];
	
	for($i=1; $i<=10; $i++)
	{
				
		$mid = get_movie_id($movies[$i]);
		$query = "update featured set mid='".$mid."' , priority= '".$prority[$i]."' where id='".$i."'";
			
		$result=mysql_query($query);
	}
	if($result)
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>