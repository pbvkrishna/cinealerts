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
	 	$fp = fopen("../../sitemap.xml", "wc");
		$file_contents = $file_header . $file_contents . $file_footer;
		fwrite($fp, $file_contents);
		fclose($fp);
		$file_contents = "";
	}


?>




<?php
/*
* Sitemap Submitter
* Use this script to submit your site maps automatically to Google, Bing.MSN and Ask
* Trigger this script on a schedule of your choosing or after your site map gets updated.
*/

//Set this to be your site map URL
$sitemapUrl = "http://www.cinealerts.com/sitemap.xml";

// cUrl handler to ping the Sitemap submission URLs for Search Engines…
function myCurl($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return $httpCode;
}

//Google
$url = "http://www.google.com/webmasters/sitemaps/ping?sitemap=".$sitemapUrl;
$returnCode = myCurl($url);
$out = "<p>Google Sitemaps has been pinged (return code: $returnCode).</p>";

echo "<br/><br/>";
//Bing / MSN
$url = "http://www.bing.com/webmaster/ping.aspx?siteMap=".$sitemapUrl;
$returnCode = myCurl($url);
$out .="<p>Bing / MSN Sitemaps has been pinged (return code: $returnCode).</p>";

echo "<br/><br/>";
//ASK
$url = "http://submissions.ask.com/ping?sitemap=".urlencode($sitemapUrl);
$returnCode = myCurl($url);
$out .= "<p>ASK.com Sitemaps has been pinged (return code: $returnCode).</p>";

echo "<br/><br/>";
$url = "http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=YahooDemo&url=".urlencode($sitemapUrl);
$returnCode = myCurl($url);
$out .= "<p>Yahoo.com Sitemaps has been pinged (return code: $returnCode).</p>";

echo "<br/><br/>";
$url = "http://rpc.weblogs.com/pingSiteForm?name=".urlencode('CineAlerts')."&url=".urlencode('http://www.cinealerts.com')."&changesURL=".urlencode(sitemapUrl);
$returnCode = myCurl($url);
$out .= "<p>Weblogs.com Sitemaps has been pinged (return code: $returnCode).</p>";

//echo $out;
	$from_email = "crons@cinealerts.com";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$headers .= 'To: Krishna <pbvkrishna@yahoo.com>' . "\r\n";
	$headers .= 'From: CinemaAlerts <'.$from_email.'>' . "\r\n";
	$headers .='Reply-To: '.$from_email. "\r\n" .
   	 'X-Mailer: PHP/' . phpversion();
	$message = $out;

	$message .="
	Thanking you<br /><br />
			
	With Regards<br />
	Webmaster<br />"; 
	
	mail("pbvkrishna@yahoo.com","Google, Bing, Ask, Yahoo and Weblog Submitted.", $message,$headers);

?>
