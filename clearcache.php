<?php
$cachetime = $_REQUEST['cachetime']?$_REQUEST['cachetime']:85401;//SEC
for($i=1; $i<=30;$i++)
{
	$totalfiles = 0;
        $deletedfiles = 0;
	$folder = "cache/".$i."/";
	$files = scandir($folder);
	foreach($files as $file)
	{
		if($file !="." and $file !="..")
		{
			$totalfiles++; 
			$cachefile = $folder.$file;
			if ((file_exists($cachefile) && (time() > filemtime($cachefile) + $cachetime ))) 
			{  
				unlink($cachefile);
                $deletedfiles++;
			}
		}
	}
	$output .= "City ID: ".$i." Files: ".$totalfiles." Deleted Files: ".$deletedfiles."<br/>";
	$allfilestotal += $totalfiles;
	$alldeletedfilestotal += $deletedfiles;
}
$output .= "Total Files: ".$allfilestotal." Total Deleted Files: ".$alldeletedfilestotal;
if($_REQUEST['show'])
{
    echo $output;
}
?>