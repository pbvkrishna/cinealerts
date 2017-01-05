<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;

if(isset($_REQUEST['start']))
{
	$start = $_REQUEST['start'];
}
else
{
	$start = 0;
}

$output = listall($_REQUEST['dir'],$start);

//User Functions

function listall($dir ="", $start = 0)
{
	global $db;
	$dir = "../songs/".$dir;
	if ($handle = opendir($dir)) 
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != "..") 
			{
				$l=strlen($file); 
				$ext=substr($file, -4, $l);  
			
				if($ext =='.mp3')
				{
					$list_output .='<a href="cpanel.php?option=audio&dir='.$file.'" >'.$file.'</a><br/>';
				}
				else
				{
					$list_output .='<a href="cpanel.php?option=audio&dir='.$file.'" >'.$file.'</a><br/>';
				}
			}
		}
	}
		
	return $list_output;
} 

?>
