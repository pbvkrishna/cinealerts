<?php
@session_start();
include("includes/configuration.php");
include("includes/functions.php");
$city = str_replace("_"," ",ucfirst(strtolower($_REQUEST['city'])));
$pathinfo =  pathinfo($_SERVER['REQUEST_URI']);
$cid = get_city_id($city)?get_city_id($city):($_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1);
$city = get_any("cities",$cid, "city","cid");

$_SESSION['user_sel_city'] = $cid;

if($pathinfo['extension'])
{
	$file_name =$pathinfo['dirname'];
}
else
{
	$file_name = substr($_SERVER['REQUEST_URI'],0,-1);
}
$file_name = str_replace("/","-",$file_name."/index.html");
$cachefile = "cache/".$cid."/".substr($file_name,1);
$cachetime = 1800;//SEC
if ((file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) && !$_SESSION['luid'] && substr($pathinfo['dirname'],1) !="")    
{
        ob_start();
	include($cachefile);
        ob_end_flush();
	exit();
}


$msg = str_replace("_"," ",$_REQUEST['msg']);
$city = str_replace("_"," ",ucfirst(strtolower($_REQUEST['city'])));
$movie = str_replace("_"," ",$_REQUEST['movie']);
$theater = str_replace("_"," ",$_REQUEST['theater']);
$article = get_any_record("articles",str_replace("_"," ",$_REQUEST['article']),"atitle");
$nid = $_REQUEST['nid'];
$news = $_REQUEST['news'];
$gallery = str_replace("_"," ",$_REQUEST['gallery']);
$upcoming = isset($_REQUEST['upcoming']);
$iid = $_REQUEST['iid'];

$start = $_REQUEST['start']?$_REQUEST['start']:0;

$cid = get_city_id($city)?get_city_id($city):($_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1);
$city = get_any("cities",$cid, "city","cid");

$_SESSION['user_sel_city'] = $cid;
if($_SERVER['HTTP_HOST']!="localhost")
{
$right_output .='<br/><div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=157552317652179";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script><div class="fb-like" data-href="http://www.facebook.com/CineAlerts" data-send="true" data-width="300" data-show-faces="false"></div>';
}
$right_output .= cities($cid);
if($_SERVER['HTTP_HOST']!="localhost")
{
$right_output .='<br/>ADVERTISEMENT<br/><script type="text/javascript"><!--
google_ad_client = "pub-3124772839944891";
/* 300x250, created 11/12/10 */
google_ad_slot = "5790973270";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';

$chitika_ad = '<script type="text/javascript">
ch_fluidH = 1;
ch_nump = "2";
ch_client = "pbvkrishna";
ch_width = 600;
ch_height = "auto";
ch_type = "mpu";
ch_sid = "Chitika Default";
ch_color_site_link = "0000CC";
ch_color_title = "0000CC";
ch_color_border = "FFFFFF";
ch_color_text = "000000";
ch_color_bg = "FFFFFF";
</script>
<script src="http://scripts.chitika.net/eminimalls/amm.js" type="text/javascript">
</script>';
}
else
{
	$right_output .="<div style='width:300px; height:250px; border:1px solid #CCCCCC'>Google Ad</div>";	
	$chitika_ad ="<div style='width:600px; height:120px;float:left; border:1px solid #CCCCCC'>Chitika Ad - 600 x Auto</div>";	
}

$right_output .=latest_movies($cid);
$right_output .=featured_galleries();
$right_output .="<div class='clear'></div>";


$right_output_cond .="<div>";
$right_output_cond .='Ads<br/><!-- Ads300X250 -->
<script type="text/javascript"><!--
google_ad_client = "ca-pub-7363809219244122";
google_ad_width = 300;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="//pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<script>
  var _comscore = _comscore || [];
  _comscore.push({ c1: "8", c2: "14880948" ,c3: "3" });
  (function() {
    var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;
    s.src = (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js";
    el.parentNode.insertBefore(s, el);
  })();
</script>
<noscript>
  <img src="http://b.scorecardresearch.com/p?c1=8&c2=14880948&c3=3&c15=&cv=2.0&cj=1" />
</noscript>
<script id="navegg" type="text/javascript" src="//tag.navdmp.com/tm18475.js" ></script>';
$right_output_cond .='Sponsor Ads:<br/>';
$right_output_cond .='Denna stil blir mer och mer populära eftersom de kan öka en kvinnas byst och få henne <a href="http://se.jennyjoseph.com/Festklanningar-c20/">festklänningar</a> att se väldigt sexig också<br/><br/>';
$right_output_cond .='<a href="http://www.dressfirst.se/Aftonklanningar-c17/" target="_blank" >aftonklänningar</a>,aftonklänningar online<br/><br/>';
$right_output_cond .='<a href="http://www.dressfirst.de/Brautjungfernkleider-c7/" target="_blank" >brautjungfernkleider</a>,brautjungfernkleider online<br/><br/>';
$right_output_cond .='<a href="http://www.dressfirst.nl/Avondjurken-c17/" target="_blank">avondjurken</a>,avondjurk<br/>';
if($_SERVER['REQUEST_URI'] == "/" || $_SERVER['REQUEST_URI'] == "/index.php" || $_SERVER['REQUEST_URI'] == "")
{
$right_output_cond .='<br/><a href="http://www.filmfrench.com">Film french</a>';
}
if($_SERVER['REQUEST_URI'] == "/kakinada/theater/list/")
{
$right_output_cond .='<br/><a href="http://www.filmfrench.com">Streaming vf</a>';
}
$right_output_cond .="</div>";
//Like Page
$right_output_cond .='<div><a href="http://www.facebook.com/CineAlerts" target="_blank" title="Follow me on Facebook"><img src="http://www.cinealerts.com/social/follow/facebook.jpg" border="0" alt="Follow Us on Facebook"/></a>';
$right_output_cond .='<a href="http://twitter.com/cinealerts/" target="_blank" title="Follow me on Twitter"><img src="http://www.cinealerts.com/social/follow/twitter.jpg" border="0" alt="Follow Us on Twitter"/></a>';
$right_output_cond .='<a href="https://plus.google.com/110246214966441234447?rel=author" target="_blank" title="Follow me on Google plus" rel="publisher"><img src="http://www.cinealerts.com/social/follow/google.jpg" border="0" alt="Follow Us on Google plus"/></a></div>';

if($upcoming)
{
	$upcoming_active = "class='active'";
	$start = $_REQUEST['start']?$_REQUEST['start']:0;
	$output = upcoming($start);
}
else if(trim($gallery)=="list")
{
	$gallery_active = "class='active'";
	if(isset($_REQUEST['gtype']))
	{
		$output = listall_galleries($start, $_REQUEST['gtype']);
	}
	else
	{
		$output = listall_galleries($start);
	}
	$output .= "<br /><br />".latest_galleries();
}
else if(trim($gallery))
{
	$gallery_active = "class='active'";
	$gid = get_any("galleries",$gallery, "gid", "gname");
	$output = view_gallery($gid,$_REQUEST['gtype'],$_REQUEST['start'], $_REQUEST['sel']);
	$output .= "<br /><br />".latest_galleries();
}
else if($nid) //Display News Item content
{
	$news_active = "class='active'";
	$output = display_news_item($nid);
	$output .= "<br /><br />".latest_news(1);
}
else if(isset($news) && (trim($news)=="" || trim($news)=="latest")) // List News items
{
	$news_active = "class='active'";
	$start = $_REQUEST['start']?$_REQUEST['start']:0;
	$output = listall_news($start, 1);
}
else if($article) //Display Article Content
{
	$home_active = "class='active'";
	set_css("includes/tabs/tabs.css");
	set_javascript("includes/tabs/prototype.lite.js");
	set_javascript("includes/tabs/moo.fx.js");
	set_javascript("includes/tabs/moo.fx.pack.js");
	$output = display_content($article);
	$output .='<script type="text/javascript">
	Element.cleanWhitespace("content");
	init();
	</script>';
}
else
if(!isset($_REQUEST['movie']) && !isset($_REQUEST['theater']))
{
	if(isset($_REQUEST['task']))
	{
		switch($_REQUEST['task'])
		{
			case 'poll': $output .=poll_display(2, $cid);
						 $home_active = "class='active'";
						 break;
			case 'poll_save': $output .=poll_save($_POST, $cid);
						$home_active = "class='active'";
						 break;
			case 'poll_result': $output .=poll_result($_REQUEST['pollid'], $cid);
						$home_active = "class='active'";
						 break;
			
			case 'login': $output .=loginform($_POST);
						  $right_output = "";
						  $login_active = "class='active'";
						  $home_active = "class='active'";
						  break;
			case 'forgot': $output .=forgot($_POST);
			   				$right_output = "";
							$home_active = "class='active'";
							break; 
			case 'save': if($uid = save_registration($_POST))
						 {
							header("Location:".set_url("index.php?task=confirm&uid=".$uid."&msg=Successfully registered"));
						 }
						 else
						 {
							if(!usernameavailability($_POST['username']))
							{
								$msg = "Username Not avilable";
								
							}
							else if(!mobilecheck($_POST['mobile']))
							{
								$msg = "User already registered with this Mobile no.";
							}
							$output .= register($_REQUEST);
						 }
						 $right_output = ""; 
  					     $register_active = "class='active'";
						 $home_active = "class='active'";

						 break;
				case 'update':$msg = update($_POST);
							  $output .= view_registration_form(get_any_record("users",$_SESSION['luid'],"uid"));	
							  $myaccount_active = "class='active'";
							  $home_active = "class='active'";
							break;
				case 'confirm':$output .=confirmform($_REQUEST['uid']);
						  $register_active = "class='active'";
						  $right_output = ""; 
						  $home_active = "class='active'";
						  break;
				case 'econfirm': $msg =econfirm($_REQUEST['uid'],$_REQUEST['email_confirm']);
						  $output .=confirmform($_REQUEST['uid']);
						  $register_active = "class='active'";
						  $right_output = ""; 
						  $home_active = "class='active'";
						  break;
				case 'mconfirm': $msg =mconfirm($_REQUEST['uid'],$_REQUEST['mobile_confirm']);
						  $output .=confirmform($_REQUEST['uid']);
						  $register_active = "class='active'";
						  $right_output = ""; 
						  $home_active = "class='active'";
						  break;
				case 'myaccount': $output .= view_registration_form(get_any_record("users",$_SESSION['luid'],"uid"));	
							$myaccount_active = "class='active'";
							$home_active = "class='active'";
							break;
				case 'myalerts': $output .=my_alerts($_POST);
							  $right_output = ""; 

							$myalerts_active = "class='active'";
							break;
				case 'logout': $_SESSION['luid']="";
				               unset($_SESSION['luid']);
							   
							   if(SEO_URLS)
								{
									$app_url = "msg/Successfully logged out/";
								}
								else
								{
									$app_url = "?&msg=Successfully logged out";
								}
							   header("Location:".$_SERVER['HTTP_REFERER'].$app_url);	
							   break;
				case 'search': $output.= search($cid, str_replace("_"," ",$_REQUEST['keyword']), $_REQUEST['stype'], $start);
				$home_active = "class='active'";
								break; 	
				default : $output .= register($_REQUEST);
		 				  $right_output = "";
						  $register_active = "class='active'";
						  $home_active = "class='active'";
		}
	}
	else
	{
		$output .= homepage($cid);
		$home_active = "class='active'";
		set_page_title($city ." Movie Theater Alerts | Show Times | Galleries");
		set_meta_data("Movies and theaters of Hyderabad, New Delhi, Bangalore, Chennai, Visakhapatnam, Vijayawada, Guntur and many more cities. It Alerts for movie changes in CINEMA theaters.", "movie, cinema, theaters, hyderabad, bangalore, chennai, visakhapatnam, vijayawada, guntur, releasing, new movies");
	}
}

if($cid && $theater)
{
	$tid = get_theater_id($cid, $theater)?get_theater_id($cid,$theater):0;
}

if(isset($_REQUEST['theater']) && $tid == 0)
{
	//List all theaters
	$output .= show_theaters($cid, $start,str_replace("_"," ",$_REQUEST['keyword']), str_replace("_"," ",$_REQUEST['area']));
    $theaters_active = "class='active'";

}
else if($tid)
{
	$output .=show_theater($tid); // Show Theater details
    $theaters_active = "class='active'";
}
else
{
	$mid = get_movie_id($movie)?get_movie_id($movie):0;
	if($mid)
	{
		$output .= show_movie($mid,$cid); //Show movie details in the city
	    $movies_active = "class='active'";
	}
	else if(($_REQUEST['movie']!="" || isset($_REQUEST['movie'])) && $mid == 0)
	{
	    $output .=show_movies($cid,$start);
	    $movies_active = "class='active'";
	}
}

set_css("includes/style.css");
//set_javascript("includes/jquery/jquery_1.8.3.js");
//set_javascript("includes/jquery/jcarousellite_1.0.1.js");
//ob_start("ob_gzhandler");
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" xmlns:og="http://opengraphprotocol.org/schema/" 
      xmlns:fb="http://www.facebook.com/2008/fbml" >
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=trim(str_replace("&","&amp;",($page_title?$page_title:PAGE_TITLE)))?></title>
<link href="<?=set_url("favicon.ico")?>" rel="shortcut icon" type="image/x-icon" />
<meta name="description" content="<?=strip_tags($meta_desc)?>" />
<meta name="keywords" content="<?=$meta_keys?>" />
<meta http-equiv="Expires" content="86400">

<?php $social_url = htmlentities('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>

<meta property="og:site_name" content="cinealerts.com"/>
<?php 
if($fb_title=='')
{
?>
<meta property="og:title" content="<?php echo trim(str_replace('&','&amp;',($page_title?$page_title:PAGE_TITLE))); ?>"/> 
<?php
}
else
{
?>
<meta property="og:title" content="<?php echo $fb_title; ?>"/>
<?php } ?>

<meta property="og:image" content="<?=$fb_image?>"/>            
<meta property="og:url" content="<?=$social_url?>"/>	
<meta property="og:description" content="<?=strip_tags($meta_desc)?>" />
<meta property="og:locale" content="en_US"/>
<meta property="og:type" content="movie"/>
<meta property="fb:app_id" content="1416489725230550"/>
<meta property="fb:admins" content="1196073037" />

<?php
echo $head_css;
echo $head_java;
?>
<script type='text/javascript'>
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
var gads = document.createElement('script');
gads.async = true;
gads.type = 'text/javascript';
var useSSL = 'https:' == document.location.protocol;
gads.src = (useSSL ? 'https:' : 'http:') + 
'//www.googletagservices.com/tag/js/gpt.js';
var node = document.getElementsByTagName('script')[0];
node.parentNode.insertBefore(gads, node);
})();
</script>

<script type='text/javascript'>
googletag.cmd.push(function() {
googletag.defineSlot('/44598069/MadAds120X600', [120, 600], 'div-gpt-ad-1383733668729-0').addService(googletag.pubads());
googletag.defineSlot('/44598069/MadAds160X600', [160, 600], 'div-gpt-ad-1383732921723-0').addService(googletag.pubads());
googletag.defineSlot('/44598069/MadAds300X250', [300, 250], 'div-gpt-ad-1383647030552-0').addService(googletag.pubads());
googletag.defineSlot('/44598069/MadAds468x60', [468, 60], 'div-gpt-ad-1383741830336-3').addService(googletag.pubads());
googletag.pubads().enableSingleRequest();
googletag.enableServices();
});
</script>
</head>
<body <?=$facebook_onload;?> >
<div class="container">
	<div class="top_bar"></div>
	<!--Header-->
	<div class="header">
		<div class="logo"><a href="<?=SITE_URL?>" title="Cinema Alerts"><img src="<?=set_url("images/logos/".strtolower($city).".jpg")?>" border="0"  alt="CineAlerts.com" /></a></div>
		<div class="logo_banner">
				<?php
				if($_SERVER['HTTP_HOST']!="localhost")
				{
				?>
					<script type="text/javascript"><!--
					google_ad_client = "pub-3124772839944891";
					/* 728x90, created 11/12/10 */
					google_ad_slot = "2189623305";
					google_ad_width = 728;
					google_ad_height = 90;
					//-->
					</script>
					<script type="text/javascript"
					src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
					</script>
				<?php
				}
				else
				{
					echo "<div style='width:728px; height:90px; border:1px solid #CCCCCC'>Google Ad</div>";
				}
				?>
		</div>
	</div> <!--End of header-->
	<!--Menu Bar-->
	<div class="menubar"> 
		<div class="menu" >
			<ul>
				<li <?=$home_active?>><a href="http://www.cinealerts.com">Home</a></li>
				<li <?=$movies_active?> ><a href="<?=set_url("index.php?city=".$city."&movie=")?>">Movies</a><?=menucities($cid)?></li>
				<li <?=$theaters_active?> ><a href="<?=set_url("index.php?city=".$city."&theater=")?>">Theaters</a><?=menucities($cid,"theater")?></li>
				<li <?=$gallery_active?> ><a href="<?=set_url("index.php?gallery=list")?>">Galleries</a><?=menugalleries($_REQUEST['gtype'])?></li>
				<li <?=$news_active?> ><a href="<?=set_url("index.php?news=latest")?>">News</a></li>
				<li <?=$upcoming_active?> ><a href="<?=set_url("index.php?upcoming=list")?>">Upcoming</a></li>
				<li <?=$myalerts_active?> ><a href="<?=set_url("index.php?city=".$city."&task=myalerts")?>">My Alerts</a></li>
			</ul>
		</div>				
		<div class="search_box" >
			<?php
			if($_REQUEST['stype']=="theaters")
			{
				$stype_theater = " checked='checked' ";
			}
			else
			{
				$stype_movies = " checked='checked' ";
			}
			?>
			<form action="<?=set_url("index.php")?>" method="post"><input type="hidden" value="search" name="task" /><input type="text" name="keyword" value="<?=str_replace("_"," ",$_REQUEST['keyword'])?>" size="10" /> in <input type="radio" value="movies" name="stype" <?=$stype_movies?> /> Movies <input type="radio" value="theaters" name="stype" <?=$stype_theater?> /> Theaters <input type="submit" class="search" value="" /> </form>
		</div>
		<?php
		if($_SESSION['luid'])
		{
		?>
		<div class="myaccount"><a href="<?=set_url("index.php?city=".$city."&task=myaccount")?>">My Account</a> | <a href="<?=set_url("index.php?city=".$city."&task=logout")?>">Logout</a></div>
		<?php
		}
		else
		{
		?>
		<div class="myaccount"><a href="<?=set_url("index.php?city=".$city."&task=registration")?>">Register</a> |
		<a href="<?=set_url("index.php?city=".$city."&task=login")?>">Sign In</a></div>
		<?php
		}
		?>
		<div class="clear"></div>
	</div><!--End of Menu-->
	<!--Content-->
	<div class="content">
		<?php
		if($right_output)
		{
			$right_output  = '<div class="right">'.$right_output;
			$right_output .= $right_output_cond;
			$right_output .= '</div>';
		}
		echo $right_output;
		?>
	
		<div class="middle">
				<?php
				if(trim($msg))
				{
				?>
					<div class="mainalert"><?=$msg?></div>
				<?php
				}
				?>
				<?=$output;?>
				<br/>
		</div>
		<div class='clear'></div>
		<div class="mainalert"><script type="text/javascript"><!--
google_ad_client = "ca-pub-7363809219244122";
google_ad_width = 980;
google_ad_height = 120;
//-->
</script>
<script type="text/javascript"
src="//pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<script>
  var _comscore = _comscore || [];
  _comscore.push({ c1: "8", c2: "14880948" ,c3: "3" });
  (function() {
    var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;
    s.src = (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js";
    el.parentNode.insertBefore(s, el);
  })();
</script>
<noscript>
<img src="http://b.scorecardresearch.com/p?c1=8&c2=14880948&c3=3&c15=&cv=2.0&cj=1" />
</noscript>
<script id="navegg" type="text/javascript" src="//tag.navdmp.com/tm18475.js" ></script>
		</div>
		<div class="footer"></div>
		<div class="footer_content">Copy Rights 2010, All Rights Reserved to <a href="http://www.cinealerts.com" title="Cinema Alerts: Cinealerts.com">Cinema Alerts</a><br /><br />
	<a href="http://cinealerts.blogspot.com" target="_blank" title="CineAlerts Blog">Blog</a> | <a href="<?=set_url("index.php?city=".$city."&article=Download Cinema Alerts Widget");?>"  title="CineAlerts Widgets and Gadgets">Desktop Widgets</a> | <a href="<?=set_url("index.php?city=".$city."&article=Advertise_With_Us");?>" title="Advertise with CineAlerts">Advertise With Us</a> | Site Map | <a href="<?=set_url("index.php?city=".$city."&article=Terms_and_Conditions");?>" title="Terms and Conditions">Terms &amp; Conditions</a> | <a href="<?=set_url("index.php?city=".$city."&article=Privacy_Policy");?>" title="Privacy policy">Privacy policy</a> | <a href="#" onclick='return false, window.open("<?=set_url("contactus.php")?>","jav","resizable=false,height=500,width=430,scrollbars=yes");' >Contact Us</a></div>
	</div>
	<!--End of Content-->
	
</div>
<?php
global $page_title;
$social_url = htmlentities('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
?>
<?php
if($_SERVER['HTTP_HOST']!="localhost")
{
?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19774500-1']);
  _gaq.push(['_setDomainName', '.cinealerts.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<?php
}
?>
</body>
</html>
<?php
if(!strpos($cachefile, "login") && !strpos($cachefile, "comments") && !strpos($cachefile, "myalerts") && !strpos($cachefile, "msg") && !$_SESSION['luid'] && substr($pathinfo['dirname'],1) !="")
{
	$fp = fopen($cachefile, 'w');
	fwrite($fp, ob_get_contents());
	fclose($fp);
}
ob_end_flush();
?>