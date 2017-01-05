<?php
include("includes/configuration.php");
include("includes/functions.php");
$msg = str_replace("_"," ",$_REQUEST['msg']);

$city = str_replace("_"," ",ucfirst(strtolower($_REQUEST['city'])));
$movie = str_replace("_"," ",$_REQUEST['movie']);
$theater = str_replace("_"," ",$_REQUEST['theater']);
$article = get_any_record("articles",str_replace("_"," ",$_REQUEST['article']),"atitle");
$nid = $_REQUEST['nid'];
$news = $_REQUEST['news'];

$start = $_REQUEST['start']?$_REQUEST['start']:0;

$cid = get_city_id($city)?get_city_id($city):($_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1);
$city = get_any("cities",$cid, "city","cid");

$_SESSION['user_sel_city'] = $cid;

$right_output .= cities($cid);
if($_SERVER['HTTP_HOST']!="localhost")
{
$right_output .='<script type="text/javascript"><!--
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
}
else
{
	$right_output .="<div style='width:300px; height:250px; border:1px solid #CCCCCC'>Google Ad</div>";	
}

$right_output .=latest_movies($cid);

$right_output_cond ="<table cellspacing='0' cellpadding='0' width='300'>";
$right_output_cond .="<tr><td>";

$right_output_cond .='<a href="http://facebook.com/cinealerts/" target="_blank" title="Follow me on Facebook"><img src="http://www.cinealerts.com/social/follow/facebook.jpg" border="0" alt="Follow Us on Facebook"/></a></td><td>';
$right_output_cond .='<a href="http://twitter.com/cinealerts/" target="_blank" title="Follow me on Twitter"><img src="http://www.cinealerts.com/social/follow/twitter.jpg" border="0" alt="Follow Us on Twitter"/></a>';
$right_output_cond .="</td></tr>";
$right_output_cond .="</table>";
if($nid)
{
	$news_active = "class='active'";
	$output = display_news_item($nid);
	$output .= "<br /><br />".latest_news(1);
}
else if(isset($news) && (trim($news)=="" || trim($news)=="latest"))
{
	$news_active = "class='active'";
	$start = $_REQUEST['start']?$_REQUEST['start']:0;
	$output = listall_news($start, 1);
}
else if($article)
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
							header("Location:".set_url("index.php?task=confirm&uid=".$uid."&msg=Sucessfully registered"));
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
									$app_url = "msg/Sucessfully logged out/";
								}
								else
								{
									$app_url = "?&msg=Sucessfully logged out";
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
		set_page_title($city ." Movie & Theater mobile alerts | SMS & Mail alerts | Movie releases");
		set_meta_data("About movies and theaters of Hyderabad, Kakinada, Visakhapatnam, Vijayawada and Rajahmundry cities. CineAlerts.com alerts on mobiles for movie changes in theaters.", "movie, cinema, theaters, alerts, hyderabad, coming soon, releasing, new movies");
		 $right_output_cond = "";
		

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
	else if(isset($_REQUEST['movie']) && $mid == 0)
	{
		$output .=show_movies($cid,$start);
	    $movies_active = "class='active'";
	}
}

set_css("includes/style.css");
set_javascript("includes/connect.js");
@ob_start("ob_gzhandler");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?=trim(str_replace("&","&amp;",($page_title?$page_title:PAGE_TITLE)))?></title>
<link href="<?=set_url("favicon.ico")?>" rel="shortcut icon" type="image/x-icon" />
<meta name="description" content="<?=$meta_desc?>" />
<meta name="keywords" content="<?=$meta_keys?>" />
<?php
echo $head_css;
echo $head_java;
?>
</head>
<body>
<table width="980" cellpadding="0" cellspacing="0" align="center" class="container">
<tr>
	<td class="top_bar"></td>
</tr>
<tr>
	<td >
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="232"><a href="http://www.cinealerts.com" title="Cinema Alerts"><img src="<?=set_url("images/logos/".strtolower($city).".jpg")?>" border="0"  alt="CineAlerts.com" /></a></td>
			<td align="left" id="logo_banner">
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
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td class="menubar">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="10">&nbsp;</td>
				<td class="menu" width="390">
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td <?=$home_active?> ><a href="<?=set_url("index.php?city=".$city)?>">Home</a></td>
							<td <?=$movies_active?> ><a href="<?=set_url("index.php?city=".$city."&movie=")?>">Movies</a></td>
							<td <?=$news_active?> ><a href="<?=set_url("index.php?city=".$city."&news=")?>">News</a></td>
							<td <?=$theaters_active?> ><a href="<?=set_url("index.php?city=".$city."&theater=")?>">Theaters</a></td>
							<td <?=$myalerts_active?> ><a href="<?=set_url("index.php?city=".$city."&task=myalerts")?>">My Alerts</a></td>
							
						</tr>
					</table>
				</td>
				
				<td class="search_box" >
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
							<form action="<?=set_url("index.php")?>" method="post"><input type="hidden" value="search" name="task" /><input type="text" name="keyword" value="<?=str_replace("_"," ",$_REQUEST['keyword'])?>" size="15" /> in <input type="radio" value="movies" name="stype" <?=$stype_movies?> /> Movies <input type="radio" value="theaters" name="stype" <?=$stype_theater?> /> Theaters <input type="submit" class="search" value="" /> </form></td>
				
		<?php
				if($_SESSION['luid'])
				{
				?>
				<td align="right"><div class="myaccount"><a href="<?=set_url("index.php?city=".$city."&task=myaccount")?>">My Account</a> | <a href="<?=set_url("index.php?city=".$city."&task=logout")?>">Logout</a></div></td>
				<?php
				}
				else
				{
				?>
				<td align="right"><div class="myaccount"><a href="<?=set_url("index.php?city=".$city."&task=registration")?>">Register</a> |
				<a href="<?=set_url("index.php?city=".$city."&task=login")?>">Sign In</a></div></td>
				<?php
				}
				?>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
		<!--Main content-->
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="middle">
				<!--Middle Content-->
				<?php
				if(trim($msg))
				{
				?>
					<div class="mainalert"><?=$msg?></div>
				<?php
				}
				?>
				<?=$output;?>
				<!--End of Middle Content-->
			</td>
			<?php
			if($right_output)
			{
				$right_output = '<td width="310" class="right">
				<!--Right Column-->'.$right_output;

				$right_output .= $right_output_cond;
				$right_output .= '<!--End of Right Column--></td>';
			}
			echo $right_output;
			?>
		</tr>
		</table>	
		<!--End of Main Content-->	
	</td>
</tr>
<tr>
	<td class="footer"></td>
</tr>
<tr>
	<td class="footer_content">Copy Rights 2010, All Rights Reserved to <a href="http://www.cinealerts.com" title="Cinema Alerts: Cinealerts.com">Cinema Alerts</a><br /><br />
	<a href="http://cinealerts.blogspot.com" target="_blank" title="CineAlerts Blog">Blog</a> | <a href="<?=set_url("index.php?city=".$city."&article=Download Cinema Alerts Widget");?>"  title="CineAlerts Widgets and Gadgets">Desktop Widgets</a> | <a href="<?=set_url("index.php?city=".$city."&article=Advertise_With_Us");?>" title="Advertise with CineAlerts">Advertise With Us</a> | Site Map | <a href="<?=set_url("index.php?city=".$city."&article=Terms_and_Conditions");?>" title="Terms and Conditions">Terms &amp; Conditions</a> | <a href="<?=set_url("index.php?city=".$city."&article=Privacy_Policy");?>" title="Privacy policy">Privacy policy</a> | <a href="#" onclick='return false, window.open("<?=set_url("contactus.php")?>","jav","resizable=false,height=500,width=430,scrollbars=yes");' >Contact Us</a></td>
</tr>
</table>
<?php
global $page_title;
$social_url = htmlentities('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
?>
<!--Connect and share bar-->
<div id="topbar">
<div>
 <a href="http://twitter.com/cinealerts" title="Twitter" target="_blank"><img src="<?=set_url("social/share/twitter.png")?>" border="0" alt="Twitter" /></a>
 <a href="http://facebook.com/cinealerts" title="Facebook" target="_blank"><img src="<?=set_url("social/share/facebook.png")?>" border="0" alt="Facebook" /></a> 
 <a href="http://digg.com/login/nc516e62e8d366adca85692563eaef40e" title="Digg" target="_blank"><img src="<?=set_url("social/share/digg.png")?>" border="0" alt="Digg" /></a>
 <a href="http://in.linkedin.com/in/cinealerts" title="LindedIn" target="_blank"><img src="<?=set_url("social/share/linked_in.png")?>" border="0" alt="LinkedIn" /></a>
 <a href="http://www.myspace.com/552368232" title="MySpace" target="_blank"><img src="<?=set_url("social/share/myspace.png")?>" border="0" alt="MySpace" /></a>
 <a href="http://cinealerts.hi5.com" title="LindedIn" target="_blank"><img src="<?=set_url("social/share/hi5.png")?>" border="0" alt="Hi5" /></a>
 <!--<a href="http://www.orkut.co.in/Main#Profile?uid=5237007838423815938" title="Orkut" target="_blank"><img src="<?=set_url("social/share/orkut.png")?>" border="0" alt="Orkut" /></a>-->
 <a href="http://groups.yahoo.com/group/cinealerts" title="Yahoo Groups" target="_blank"><img src="<?=set_url("social/share/yahoo_groups.png")?>" border="0" alt="Yahoo Groups" /></a>
 <a href="http://www.ibibo.com/cinealerts" title="ibibo" target="_blank"><img src="<?=set_url("social/share/ibibo.png")?>" border="0" alt="ibibo" /></a>
 <a href="http://www.delicious.com/cinealerts" title="Delicious" target="_blank"><img src="<?=set_url("social/share/delicious.png")?>" border="0" alt="Delicious" /></a>
 <!--<a href="http://www.google.com/profiles/cinealerts" title="Google" target="_blank"><img src="<?=set_url("social/share/google.png")?>" border="0" alt="Google" /></a>-->
 <a href="http://www.tagged.com/cinealerts" title="Tagged" target="_blank"><img src="<?=set_url("social/share/tagged.png")?>" border="0" alt="Tagged" /></a>
</div>

<div><a href="http://labs.google.co.in/smschannels/subscribe/cinemaalerts" title="Google SMS Alerts" target="_blank"><img src="<?=set_url("social/share/google_sms.png")?>" border="0" alt="Google SMS" /></a>
</div>
<div id="advertise">
<a href="<?=set_url("index.php?city=".$city."&article=Advertise_With_Us");?>" title="Advertise with CineAlerts">ADVERTISE HERE</a>
</div>

</div><!--End og bar-->
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
</body>
</html>
<?php
ob_end_flush();
?>