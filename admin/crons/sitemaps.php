<?php
include("../../includes/configuration.php");

$file_header = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">';

$file_footer = '
</urlset>';

$urls = mysql_query("select * from urls");

$i = 1;
$page =1;
while($url = mysql_fetch_array($urls))
{
	if(($i%200) == 0)
	{
		$fp = fopen("../../sitemap/sitemap_".$page.".xml", "wc");
		$file_contents = $file_header . $file_contents . $file_footer;
		fwrite($fp, $file_contents);
		fclose($fp);
		$file_contents = "";
		$page ++;
	}

	$news = strpos($url['url'], "news");
	$gallery = strpos($url['url'], "gallery");
	$theaters = strpos($url['url'], "theater");
	$movies = strpos($url['url'], "movie");
	$playing = strpos($url['url'], "playing");
	if($gallery || $news)
	{
		$priority = "1.0";
		$frequency = "daily";
	}
	else if($theaters)
	{
		$priority = "1.0";
		$frequency = "daily";
	}
	else
	{
		$priority = "1.0";
		$frequency = "daily";
	}

	$file_contents .='
	<url>
		<loc>'.$url['url'].'</loc>
		<lastmod>'.date('Y-m-d', strtotime($url['cdate'])).'</lastmod>
		<changefreq>'.$frequency.'</changefreq>
		<priority>'.$priority.'</priority>
	</url> ';
	$i++;
}
	
	if(trim($file_contents) !="")
	{
	 	$fp = fopen("../../sitemap/sitemap_".$page.".xml", "wc");
		$file_contents = $file_header . $file_contents . $file_footer;
		fwrite($fp, $file_contents);
		fclose($fp);
		$file_contents = "";
		$page++;
	}

	$from_email = "crons@cinealerts.com";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$headers .= 'To: Krishna <pbvkrishna@yahoo.com>' . "\r\n";
	$headers .= 'From: CinemaAlerts <'.$from_email.'>' . "\r\n";
	$headers .='Reply-To: '.$from_email. "\r\n" .
   	 'X-Mailer: PHP/' . phpversion();
	$message = "<br />Files Generated: ".($page-1)."<br/><br/>URL's: ".($i-1)."<br /><br />";

	$message .="
	Thanking you<br /><br />
			
	With Regards<br />
	Webmaster<br />"; 
	
	@mail("pbvkrishna@yahoo.com",($page-1)." Sitemaps Generated.", $message,$headers);


?>
