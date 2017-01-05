<?php
set_time_limit(0);
include("includes/configuration.php");
?>
<html>
<head>
<title>Optimize the Images</title>
</head>
<body>
<?php
$query = "select * from imgoptimization";
$res = mysql_query($query);
echo "<table>";

while($row = mysql_fetch_array($res))
{
    echo "<tr><td>".$row['optiontype']."</td><td>".$row['status']."</td></tr>";
}
echo "</table>";
?>
<form action="" method="post">
Optimize the Images: 
<select name="option">
<option value="news">News</option>
<option value="posters">Movie Posters</option>
<option value="gallery">Galleries</option>
</option>
</select>
<br/>
Password: <input type="password" name="pass" /><br/>
Max ID: <input type="text" name="maxid" /><br/>
<br/>
<input type="submit">
</form>


</body>
</html>
<?php
$opt_vars['news']['folder'] = "images/news";
$opt_vars['news']['table'] = "news";
$opt_vars['news']['id'] = "nid";

$opt_vars['posters']['folder'] = "posters";
$opt_vars['posters']['table'] = "movies";
$opt_vars['posters']['id'] = "mid";

$opt_vars['gallery']['folder'] = "galleries";
$opt_vars['gallery']['table'] = "galleryimages";
$opt_vars['gallery']['id'] = "iid";

if($_POST['option'] !="" && $_POST['pass'] == "poripireddy9868")
{
	optimize($_POST['option'], $_POST['maxid']);
}

function optimize($option, $maxid = 0)
{
	
        global $db, $opt_vars;
	$query = "select status from imgoptimization where optiontype='".$option."'";
	$res = mysql_query($query);
	if($row = mysql_fetch_array($res))
	{
                if($maxid > 0)
                {
                    $max_cond = " and ".$opt_vars[$option]['id']." <= '".$maxid."'";
                }
                else
                {
                    $max_cond = "";
                }
		$query = "select ".$opt_vars[$option]['id']." from ".$opt_vars[$option]['table']." where ".$opt_vars[$option]['id']." > '".$row['status']."'". $max_cond;
                
                 $id_res = mysql_query($query);
                 $last_id = process($id_res, $opt_vars[$option]['folder'],$opt_vars[$option]['id']);
                 if($row['status'] != $last_id && $last_id > $row['status'] )
		{
			mysql_query("update imgoptimization set status = '".$last_id."' where optiontype='".$option."'");
			echo $option." done Upto id: ".$last_id;
		}

		
	}
	
}
function process($ids, $folder, $column_id)
{
	global $db;
	while($id = mysql_fetch_array($ids))
	{
		$file = "./".$folder."/".$id[$column_id].".jpg";
		$thumb = "./".$folder."/thumbs/".$id[$column_id].".jpg";
		$optfile = "./".$folder."/optfile.jpg";
                $optfile_thumb = "./".$folder."/thumbs/optfile.jpg";
		if (file_exists($file)) 
		{
			exec("jpegtran -optimize ".$file." > ".$optfile);
			if(filesize($optfilesize)>0)
			{
			   @unlink($file);
			   exec("mv ".$optfile." ".$file);                          
                        }
                        @unlink($optfile);
		}
		if (file_exists($thumb)) 
		{
			exec("jpegtran -optimize ".$thumb." > ".$optfile_thumb);
			if(filesize($optfile_thumb)>0)
			{
			   @unlink($thumb);
			   exec("mv ".$optfile_thumb." ".$thumb);
                        }
                        @unlink($optfile_thumb);
		}
        $last_id = $id[$column_id];
	}
	return $last_id;	
}
?>