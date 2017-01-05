<?php
include("includes/configuration.php");
include("includes/functions.php");

function get_latest_alert()
{
	global $db;
	$query = "select * from movies where rdate <= '".date("Y-m-d",strtotime("+15 day"))."' and rdate>= '".date("Y-m-d",strtotime("-15 day"))."' LIMIT 0,8";
	$movies = mysql_query($query);
	
	while($movie = mysql_fetch_array($movies))
	{
		$output .= "<div class='contentdiv'>".show_mini_movie(1, $movie['mid'])."</div>";
	}
	return $output;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Cinema alerts from CineAlerts.com</title>
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
<meta name="description" content="Cinema alerts from CineAlerts.com" />
<meta name="keywords" content="advertise, cinealerts, alerts, movies, sms, alerts, email" />
<link href="includes/style.css" rel="stylesheet" />	
<link rel="stylesheet" type="text/css" href="includes/contentslider.css" />

<script type="text/javascript" src="includes/contentslider.js">

/***********************************************
* Featured Content Slider- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
***********************************************/

</script>
</head>
<body>
<table width="220" cellpadding="0" cellspacing="0" border="0" align="center" class="container">
<tr>
	<td class="top_bar"></td>
</tr>
<tr>
	<td align="center"><a href="http://www.cinealerts.com/" title="Cinema Alerts"></a><img src="images/logo.jpg" alt="CineAlerts.com Logo" border="0" /></a>

	</td>
</tr>
<tr>
	<td align="center">
		
<div id="slider1" class="sliderwrapper">

<?=get_latest_alert()?>

</div>

<div id="paginate-slider1" class="pagination">

</div>

<script type="text/javascript">

featuredcontentslider.init({
	id: "slider1",  //id of main slider DIV
	contentsource: ["inline", ""],  //Valid values: ["inline", ""] or ["ajax", "path_to_file"]
	toc: "#increment",  //Valid values: "#increment", "markup", ["label1", "label2", etc]
	nextprev: ["<<", ">>"],  //labels for "prev" and "next" links. Set to "" to hide.
	revealtype: "click", //Behavior of pagination links to reveal the slides: "click" or "mouseover"
	enablefade: [true, 0.2],  //[true/false, fadedegree]
	autorotate: [true, 8000],  //[true/false, pausetime]
	onChange: function(previndex, curindex){  //event handler fired whenever script changes slide
		//previndex holds index of last slide viewed b4 current (1=1st slide, 2nd=2nd etc)
		//curindex holds index of currently shown slide (1=1st slide, 2nd=2nd etc)
	}
})

</script>

	</td>
</tr>
</table>						
						
</body>
</html>