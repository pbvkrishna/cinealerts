<?php
//loginform(POST) //loginform and login check
//forgot(POST)
//adminlogin(username,password,user_type) // For admin login
//sessioncheck($usertype = 0) // checks User accessabilty permission on current page
//usernameavailability(username)
//mobilecheck(mobile, $uid = 0)
//sendemail($toemail, $toname, $subject, $message)
//sendsms($tomobile,$message)
//get_allcities(scity = "", $append_path) //All Cities in AP
//get_cities(cid = 0) (Admin added)
//get_any(table, find_string, return_attrib, find_in_attrib = "id")
//get_any_record(table, find_string, find_in_attrib = "id") //returns Complete Record
//set_page_title(title, reset)
//get_social_share()
//top_movies($cid)
//latest_movies($cid)
//coming_movies($cid)
//featured_movie($cid)
//latest_comments($cid)
//cities($cid)
//set_meta_data(description, keywords)
//set_css(url)
//set_javascript(url)
//set_url($url) returns absolute URL. If SEO_URL is enabled then returns SEO URL
//pathway($cid,$tid,$mid)
//register(POST)
//view_registration_form(POST)
//save_registration(POST)
//confirmform($uid)
//econfirm($uid, $ecode)
//mconfirm($uid,$mcode)
//comments($mid, field = "mid") //field = "mid"/"tid"
//view_rating($id, $field="mid"); //field = "mid"/"tid"
//subscribe($cid, $id, $alert_type, $uid = 0 ,$field="mid") //ID will be either mid/tid based on field
//my_alerts($_POST)
//sendlink($_REQUEST)
//search($_REQUEST['keyword'], $_REQUEST['stype'], $start)
//get_city_id(city)
//get_movie_id(movie)
//get_theater_id(city_id, Theater)
//show_movies($cid,$start)
//show_mini_movie($mid,$cid)
//show_movie_thumb($mid, $path_append);
//show_movie($mid,$cid);
//languages($cid);
//show_playing_theaters($cid,$mid)
//show_theaters($cid,$start)
//areafilter($cid)
//show_mini_theater($tid,$cid)
//show_theater_thumb($tid,$path_append)
//show_theater($tid);
//display_intro_content($article);
//display_content($article) //article row as perameter
//homepage($cid)
//poll_display(pollid,$cid)
//poll_save($d_POST, $cid)
//poll_result(pollid, $cid)
//latest_news($show_all_tumbs=1,$num_rec = 8)
//listall_galleries($start);
//latest_galleries();

function loginform($user)
{
	global $db;
	if($user['username'] && $user['password'])
	{
		$users = mysql_query("select * from users where username='".$user['username']."' and (password='".md5($user['password'])."' or (tmp_password = '".$user['password']."' and tmp_password !='0' and tmp_password !=''))");
		if($luser = mysql_fetch_array($users))
		{
			$_SESSION['luid'] = $luser['uid'];
			if($luser['confirm'] && $luser['mconfirm'])
			{
				
				if(SEO_URLS)
				{
					$reff = explode("/",$_SERVER['HTTP_REFERER']);
				}
				else
				{
					$reff = explode("&",strstr($_SERVER['HTTP_REFERER'],"?"));
				}
				
				if(@in_array("login",$reff) && @in_array("task",$reff) && SEO_URLS)
				{
					header("Location:".set_url("index.php"));
				}
				else if(@in_array("task=login",$reff) && !SEO_URLS)
				{
					header("Location:".set_url("index.php"));
				}
				else
				{
					header("Location:".set_url($_SERVER['HTTP_REFERER']));
				}
			}
			else
			{
				header("Location:".set_url("index.php?task=confirm&uid=".$luser['uid']));

			}
		}
		else
		{
			if(SEO_URLS)
			{
				$app_url = "msg/Invalid Username or Password/";
			}
			else
			{
				$app_url = "&msg=Invalid Username or Password";
			}
			header("Location:".set_url($_SERVER['HTTP_REFERER'].$app_url));
		}
	}
	
	$out .="<div class='loginform'>";	
	$out .="<h4>Sign In</h4>";	
	$out .="<form action='".set_url("index.php?task=login")."' method='post'>";
	$out .="<div  class='regform'>";	
	$out .="<div class='field'><div class='fieldname'>Username:</div><div class='fieldvalue'><input type='text' name='username' /></div></div>" ;		
	$out .="<div class='field'><div class='fieldname'>Password:</div><div class='fieldvalue'><input type='password' name='password' /></div></div>"; 
	$out .="<div class='login'><input type='submit' value='Login'  class='submit' /></div>";
	
	$out .="<div class='login'>Forgot Username or Password? <a  href=\"".set_url('index.php?task=forgot')."\"><span class='mainalert'>Click here</span></a></div>";
	$out .="</div></form>"; 
	$out .="</div>"; 
	 
	return $out;
}
function forgot($details)
{
	global $db;
	$out .="<div class='forgot' >";	
	$out .="<h1>Forgot Username or Password?</h1>";			
	$form .= "<div class='forgot_div' >";	
	$form .= "<fieldset><legend><b>By E-Mail ID:</b></legend><br/>";
	$form .= "<form method='post' action=''>";
	$form .= "E-Mail ID: <input type='text' name='email' /> ";
	$form .= "<input type='submit' value='send Password' /><br/><br/>";
	$form .= "</form>";
	$form .= "</fieldset>";
	$form .= "</div>";
     /*   $form .= "<div class='or'><br/><br/><br/> <span class='mainalert'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Or</span> "; 
	$form .= "</div>";
        $form .= "<div class='forgot_div'>";		
	$form .= "<fieldset><legend><b>By Mobile No.:</b></legend><br/>";
	$form .= "<form method='post' action=''>";
	$form .= "Mobile No.: ";
	$form .= "+91<input type='text' name='mobile' /> ";
	$form .= "<input type='submit' value='send Password' /><br/><br/>";
	$form .= "</form>";
	$form .= "</fieldset>";
	$form .= "</div>";	*/	
	if(trim($details['mobile']) || trim($details['email']))
	{
		$cond = " where ";
		if($details['mobile'])
		{
			$cond .=" mobile = '".$details['mobile']."' ";
		}
		else if($details['email'])
		{
			$cond .=" email = '".$details['email']."'";
		}
		$users = mysql_query("select * from users ".$cond);
		
		if($user = @mysql_fetch_array($users))
		{
			$pwd = str_shuffle("AabBcCdDEefFgGhHiJjKklmnpqRsTtuvWXyYZ123456");
			$tmp_password = substr($pwd, 0,8);
			$uquery = mysql_query("update users set tmp_password='".$tmp_password."', tmp_date=now() ".$cond);
			if($uquery)
			{
				//SEND E-Mail and SMS
				
				$smsmessage = "Username: ".$user['username']."\r
				TEMP Password: ".$tmp_password." \r
				(This will work only for next 3 days)\r";
				$message = "Hello ".$user['name']."<br/><br/>
				
				You have requested for New Password. The following are your login details:<br/><br/>
				
				".str_replace("\r", "<br/>",$smsmessage)."
				<br/><br/>
				";
				
				
				sendemail($user['email'], $user['name'], "CineAlerts: Forgot Password", $message);
				//sendsms($user['mobile'], $smsmessage);
				$out .="<br /><br /><div class='mainalert'>Your New Password has been sent to your E-Mail address.<br/><br/> Note that this password will work only for 3 days. So, we prefer to change your Password with in 3 days.</div>";
			}
		}
		else
		{
			$out .="<div class='mainalert'>Invalid E-Mail ID/Mobile No.</div>";
			$out .=$form;
		}
	}
	else
	{
		$out .= $form;
	}
	$out .="</div>";
	return $out;
}
function adminlogin($username, $password, $type = 4)
{
	global $db;
	$query = "select * from users where username='".stripslashes(trim($username))."' and password= '".md5($password)."' and user_type = '".$type."'";
	$users = mysql_query($query);
	if($user = mysql_fetch_array($users))
	{
		$_SESSION['log_username'] = $user['username'];
		$_SESSION['log_uid']= $user['uid'];
		$_SESSION['log_user_type'] = $user['user_type'];
		$_SESSION['log_name'] = $user['name'];
		$_SESSION['log_email'] = $user['email'];
		$_SESSION['log_mobile'] = $user['mobile'];
		$_SESSION['log_city'] = $user['city'];
		return true;
	}
	else
	{
		return false;	
	}
}

function sessioncheck($usertype = 0)
{
	if(!trim($_SESSION['log_username']) && $_SESSION['log_user_type'] < $usertype)
	{
		header("Location:".set_url("index.php?msg=You do not have permissions to access"));
	}
}

function usernameavailability($username)
{
	global $db;
	$query = "select * from users where username = '".$username."'";
	$rows = mysql_query($query);
	if(mysql_num_rows($rows))
	{
		return false;
	}
	else
	{
		return true;
	}
}

function mobilecheck($mobile, $uid = 0)
{
	global $db;
	if($uid)
	{
		$cond = " and uid != '".$uid."'";
	}
	$query = "select * from users where mobile = '".$mobile."'".$cond;
	$rows = mysql_query($query);
	if(mysql_num_rows($rows))
	{
		return false;
	}
	else
	{
		return true;
	}
}
function sendsms($tomobile,$message)
{
	$url = "http://bulksms.mysmsmantra.com:8080/WebSMS/SMSAPI.jsp?username=bvkrishnap&password=2137286943&sendername=CALERTS&mobileno=91".$tomobile."&message=".$message;

//Sending SMS
			$host = "bulksms.mysmsmantra.com";
			$request = ""; 					//initialize the request variable
			$param["username"] = "bvkrishnap"; 		//	this is the username of your SMS account
			$param["password"] = "2137286943"; 	//this is the password of our SMS account
			$param["message"] = substr(str_replace("\n","\r",$message),0,160); 	//this is the message that we want to send
			$param["mobileno"] = $tomobile; 		//these are the recipients of the message

			$param["sendername"] = "CALERTS";
			foreach($param as $key=>$val) 	// Traverse through each member of the param array
				{ 
				$request.= $key."=".urlencode($val); //we have to urlencode the values
				$request.= "&"; //append the ampersand (&) sign after each paramter/value pair
				}
			$request = substr($request, 0, strlen($request)-1); //remove the final ampersand sign from the request
			$script = "/WebSMS/SMSAPI.jsp";
			$request_length = strlen($request);
			$method = "GET"; 
			if ($method == "GET") 
				{
				$script .= "?$request";
				}
			//Now comes the header which we are going to post. 
			$header = "$method $script HTTP/1.1\r\n";
			$header .= "Host: $host\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: $request_length\r\n";
			$header .= "Connection: close\r\n\r\n";
			$header .= "$request\r\n";
		
			//Now we open up the connection
			$socket = @fsockopen($host, 8080, $errno, $errstr); 
			if ($socket) //if its open, then...
				{ 
				fputs($socket, $header); // send the details over
				while(!feof($socket))
					{
					$output=$output.fgets($socket); //get the results 
					}
				fclose($socket); 
				} 
}

function sendemail($toemail, $toname, $subject, $message)
{
	
	$from_email = "webmaster@cinealerts.com";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	$headers .= 'To: '.$toname.' <'.$toemail.'>' . "\r\n";
	$headers .= 'From: CineAlerts.com <'.$from_email.'>' . "\r\n";
	$headers .='Reply-To: '.$from_email. "\r\n" .
   	 'X-Mailer: PHP/' . phpversion();
	$message = "<br />".$message."<br /><br />";

	$message .="
	Thanking you<br /><br />
			
	With Regards<br />
	Webmaster<br />
	www.cinealerts.com<br/><br />
	
	------------------------------------------------------<br />
	<a href='https://www.cinealerts.com/unsubscribe.php' title='Unsubscribe'>Click Here to unsubscribe from list.</a><br />"; 
	$message .="<a href='https://www.cinealerts.com/task/myalerts/' title='Unsubscribe my alerts'>Click Here to unsubscribe your alerts.</a>"; 
	
	@mail($toemail,$subject, $message,$headers);
}
function get_allcities($scity = "", $append_path="")
{
	include($append_path."cities.php");
	foreach($allcities as $city )
	{
		if($scity == $city)
		{
			$selected= " selected='selected' ";
		}
		else
		{
			$selected="";
		}
		$output .= "<option value='".$city."' ".$selected.">".$city."</option>";
	}
	return $output;
}

function get_cities($cid = 0, $opt = "cid")
{
	global $db;
	
	
	$cities = mysql_query("select * from cities");
	while($city = mysql_fetch_array($cities))
	{
		if($cid == $city[$opt])
		{
			$selected= " selected='selected' ";
		}
		else
		{
			$selected="";
		}
		$output .= "<option value='".$city[$opt]."' ".$selected.">".$city['city']."</option>";
	}
	return $output;
}

function get_countries($scountry)
{
	$countries = array("India", "USA");
	foreach($countries as $country)
	{
		if($scountry == $country)
		{
			$output .="<option value='".$country."' selected='selected'>".$country."</option>";		
		}
		else
		{
			$output .="<option value='".$country."'>".$country."</option>";		

		}
	}
	return $output;
}

function get_any($table, $find, $return, $attrib = "id")
{
	global $db;
	$anys = @mysql_query("select * from $table where $attrib = '$find' LIMIT 0,1");
	if($any = @mysql_fetch_array($anys))
	{
		return $any[$return];
	}
	else
	{
		return "";
	}
}

function get_any_record($table, $find, $attrib = "id")
{
	global $db;
	$anys = @mysql_query("select * from $table where $attrib = '$find' LIMIT 0,1");
	if($any = @mysql_fetch_array($anys))
	{
		return $any;
	}
	else
	{
		return "";
	}
}

function set_page_title($title, $reset=0)
{
	global $page_title;
	if($reset)
	{
		$page_title = $title;
	}
	else
	{
		$page_title = $page_title ." ".$title;
	}
	$page_title = trim(str_replace(",","",$page_title));
}

function set_meta_data($description, $keywords)
{
	global $meta_desc, $meta_keys;
	if(trim($description))
	{
		$meta_desc .= trim($description)." ";
	}
	if(trim($keywords))
	{
		$meta_keys .= trim($keywords).",";
	}	
}

function fb_image($imageurl, $title)
{
	global $fb_image, $fb_title;
	$fb_image = $imageurl ;
	$fb_title = $title ;
}

function set_css($url)
{
	global $head_css;
	$head_css .= "<link rel=\"stylesheet\" href=\"".set_url($url)."\" type=\"text/css\" />";
}

function set_javascript($url)
{
	global $head_java;
	$head_java .="<script language=\"javascript\" type=\"text/javascript\" src=\"".set_url($url)."\"></script>";
}

function set_url($url)
{
	global $db;
	if(SEO_URLS == 1)
	{
		$url = str_replace("https://www.cinealerts.com/","",$url);
		$url = str_replace("https://cinealerts.com/","",$url);
		$url = str_replace("https://www.cinealerts.com","",$url);
		$url = str_replace("https://cinealerts.com","",$url);

		$url = str_replace("http://www.cinealerts.com/","",$url);
		$url = str_replace("http://cinealerts.com/","",$url);
		$url = str_replace("www.cinealerts.com/","",$url);
		$url = str_replace("cinealerts.com/","",$url);
		$url = str_replace("http://www.cinealerts.com","",$url);
		$url = str_replace("http://cinealerts.com","",$url);
		$url = str_replace("www.cinealerts.com","",$url);
		$url = str_replace("cinealerts.com","",$url);

		$surl = substr($url,10);
		$nsurl = strstr($surl, '?');
		if($nsurl)
		{
			$surl = $nsurl;			
		}
		$urlparts = explode("&", $surl);
		foreach ($urlparts as $i => $value) 
		{
			$tmpAr = explode("=", $value);
			$urlpart[$tmpAr[0]] = $tmpAr[1]?$tmpAr[1]."/":"";
			
		}
		if($urlpart['city'] || $urlpart['gallery'] || $urlpart['news'] || $urlpart['upcoming'])
		{
			$nurl = str_replace("city=","",$surl);
			if(isset($urlpart['theater']) && $urlpart['theater']=="")
			{
				$nurl = str_replace("theater=","theater=list",$nurl);;
			}
			if(isset($urlpart['movie']) && $urlpart['movie']=="")
			{
				$nurl = str_replace("movie=","movie=playing",$nurl);;
			}
			
			$nurl = str_replace("&","=",$nurl);
			$turl = explode("=",$nurl);
			$nurl = implode("/",$turl);
			if(substr($nurl,"-1") !="/")
			{
				$nurl .="/";
			}
		}
		else
		{
			$nurl = $url;
		}
		$nurl = str_replace(" ","-",$nurl);

		//Develop SEO URL
		$pathinfo = pathinfo($nurl);
		if($pathinfo['extension'] == "jpg")
		{
			$nurl = strtolower(IMG_SITE_URL.$nurl);
		}
		else
		{
			$nurl = strtolower(SITE_URL.$nurl);
		}
		if($pathinfo['extension'] == "" && !$urlpart['start'] && !$urlpart['gallery'] && !$urlpart['tab'] && !strstr($nurl,"/msg/") && !strstr($nurl,"/logout/") && !strstr($nurl,"/registration/") && !strpos($nurl, "&"))
			@mysql_query("insert into urls(url) values('".$nurl."')");
		
		return $nurl;
	}
	else
	{
		$pathinfo = pathinfo($url);
		if($pathinfo['extension'] == "jpg")
		{
			$nurl = strtolower(IMG_SITE_URL.$url);
		}
		else
		{
			$nurl = strtolower(SITE_URL.$url);
		}
		return str_replace(" ","_",$nurl);
	}
}

function get_social_share()
{
	global $page_title,$meta_desc;
	

	$social_url = htmlentities('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	//+1d Google	
	$out = '<g:plusone size="medium" annotation="none"></g:plusone>

<script type="text/javascript">
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/plusone.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>';
	//Facebook
	$out .= '<a href="http://www.facebook.com/sharer.php?u='.$social_url.'&amp;t='.str_replace("&","&amp;",trim($page_title)).'" title="Facebook" target="_blank"><img src="'.set_url("social/circular/facebook_32.png").'" border="0" alt="Facebook" /></a>';
	//twitter
	$out .= '<a href="http://twitter.com/?status='.$page_title.' '.$social_url.'" title="Twitter" target="_blank"><img src="'.set_url("social/circular/twitter_32.png").'" border="0" alt="Twitter" /></a>';
	//Digg
	$out .= '<a href="http://digg.com/submit?phase=2&amp;url='.$social_url.'&amp;title='.str_replace("&","&amp;",trim($page_title)).'" title="Digg" target="_blank"><img src="'.set_url("social/circular/digg_32.png").'" border="0" alt="Digg" /></a>';	
	//LinkeIn
	$out .= '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url='.$social_url.'&amp;title='.str_replace("&","&amp;",trim($page_title)).'&amp;ro=false" title="LinkedIn" target="_blank"><img src="'.set_url("social/circular/linkedin_32.png").'" border="0" alt="Linkedin" /></a>';	
	$out .= "<a href=\"http://www.delicious.com/save\" onclick=\"window.open('http://www.delicious.com/save?v=5&amp;noui&amp;jump=close&amp;url='+encodeURIComponent(location.href)+'&amp;title='+encodeURIComponent(document.title), 'delicious','toolbar=no,width=550,height=550'); return false;\"><img src=\"".set_url("social/circular/delicious_32.png")."\" border=\"0\" alt=\"Delicious\" /></a>"; 
	return $out;
}

function pathway($cid = 0,$tid = 0, $mid = 0)
{
	
	if($cid)
	{
		$city = get_any("cities",$cid,"city","cid");
		$out = '<a href="'.set_url("index.php?city=".$city).'"><strong>'.$city.'</strong></a>';
		if($tid)
		{
			$theater = get_any("theaters",$tid,"theater","tid");
			$out .= '<a href="'.set_url("index.php?city=".$city."&theater=".$theater).'"><strong>'.$theater.'</strong></a>';
		}
		if($mid)
		{
			$movie = get_any("movies",$mid,"movie","mid");
			$out .= '<a href="'.set_url("index.php?city=".$city."&movie=".$movie).'"><strong>'.$movie.'</strong></a>';
		}
	}
	
	return $out;
}
function register($user)
{
	if($member['register']=="")
	{
		$out .=view_registration_form($user);
	}
	return $out;
}


function view_registration_form($user)
{
	$out .="<form action='".set_url("index.php")."' method='post' onSubmit='return validate()'  name='userform'>";
	if($user['uid'])
	{
		$task="update";
		$out .="<input type='hidden' name='uid' value='".$user['uid']."' />";
		$out .="<input type='hidden' name='username' value='".$user['username']."' />";
		$label = "Account Details";
		$button_label = "Update";
	}
	else
	{
		$task = "save";
		$label = "Account Registration";
		$button_label = "Register";
	}
	$out .="<input type='hidden' name='task' value='".$task."' />";
	set_javascript("includes/ajax.js");
	set_javascript("includes/regform.js");
	set_css("includes/passwordstrength.css");

	$out .="<div class='registration'>";	
	$out .="<h1>".$label."</h1>";
	$out .="<div  class='regform1' >"; 


	if($user['uid'])
	{
		$out .="<div class='field1'><div class='fieldname1'>Username:</div><div class='fieldvalue1'><b>".$user['username']."</b></div></div>";	
	}
	else
	{	
		$out .="<div class='field1'><div class='fieldname1'>Username:</div><div class='fieldvalue1'><input type='text' name='username' value='".$user['username']."' onchange='loadpro(\"uavial\",\"".SITE_URL."ajax/usercheck.php?username=\"+this.value)' /></div><div id='uavial' class='alert'></div></div>";
	}	
	$out .="<div class='field1'><div class='fieldname1'>Password:</div><div class='fieldvalue1'><input type='password' name='password' value='' onchange='passwordstrength(this.value)' /></div><div class='alert'><div id='passwordstrength' class='strength0'></div><div id='passworddescription'></div></div></div>";
	$out .="<div class='field1'><div class='fieldname1'>Re Type Password:</div><div class='fieldvalue1'><input type='password' name='rpassword' value='' onchange='passwordmatch(this.form.password.value, this.value)' /></div><div id='pmatch' class='alert'></div></div>";
	$out .="<div class='field1'><div class='fieldname1'>Name:</div><div class='fieldvalue1'><input type='text' name='name' value='".$user['name']."' /></div></div>";
	$out .="<div class='field1'><div class='fieldname1'>E-Mail:</div><div class='fieldvalue1'><input type='text' name='email' value='".$user['email']."' onchange='loadpro(\"eavial\",\"".SITE_URL."ajax/usercheck.php?email=\"+this.value)' /></div><div id='eavial' class='alert'></div></div>";
	$out .="<div class='field1'><div class='fieldname1'>Mobile No.:</div><div class='fieldvalue1'><input type='text' name='mobile' value='".$user['mobile']."' onchange='loadpro(\"mavial\",\"".SITE_URL."ajax/usercheck.php?mobile=\"+this.value)' /></div><div id='mavial' class='alert'></div></div>";
	$out .="<div class='field1'><div class='fieldname1'>City:</div><div class='fieldvalue1'><select name='city'> ".get_allcities($user['city'])."<option value='other'>Other</option></select></div></div>";
	$out .='<div class="field1"><div class="fieldname1">Enter Captcha:</div><div class="fieldvalue1" style="width:280px;"><input type="text" name="security_code" onchange="return captcha();" id="security_code" value="" class="form-control validate[required] text-input" /><img src="'.SITE_URL.'includes/CaptchaSecurityImages.php?width=110&height=35&characters=6" /></div></div>';
	$out .="<div><input type='submit' value='".$button_label."'  class='submit' /></div>";
	$out .="</div>";
	if($task == "save")
	{
		$out .="<div class='benifits'><h4>Benifits:</h4>

					<ol>
						<li>Get New movie release alerts</li>
						<li>Movies playing in a particular theater</li>
						<li>Comments of a movie.</li>
						<li>Show times of every movie.</li>
						<li>You can comment and rate movie.</li>
					</ol>";
		
	}
	$out .="</div></div></form><script>document.userform.username.focus();</script>";
	return $out;
}
function captcha($security_code)
{
echo "<script>alert($security_code);</script>";
if(($_SESSION['security_code'] == $security_code && !empty($_SESSION['security_code'] )) )
{
return true;
unset($_SESSION['security_code']);
}
else
{
return false;
}
}
function save_registration($user)
{
	global $db;
	if(!usernameavailability($_POST['username']))
	{
		return false;
	}
	if(!mobilecheck($_POST['mobile']))
	{
		return false;
	}
	if(!captcha($_POST['security_code']))
	{
		return false;
	}
	
	$admin_aprove = ADMIN_APROVE?0:1;
	$email_verification = EMAIL_VERIFICATION?0:1;
	$mobile_verification = MOBILE_VERIFICATION?0:1;
	
	$code = rand(10000,99999);
	$mcode = rand(1000, 9999);
	$query = "insert into users(username, password, name, email, mobile, city, confirm, mconfirm, aprove, user_type,activationcode, mcode) values('".$_POST['username']."', '".md5($_POST['password'])."', '".$_POST['name']."','".$_POST['email']."','".$_POST['mobile']."','".$_POST['city']."', '".$email_verification."', '".$mobile_verification."','".$admin_aprove."','0', '".$code."', '".$mcode."' )";
	
	$result=mysql_query($query);
	if($result)
	{
			$header="";
			
			if(EMAIL_VERIFICATION)
			{
				//Activation Link
				$subject = "Email Verification code at CineAlerts.com";
				
				$url = set_url("index.php?task=econfirm&uid=".mysql_insert_id()."&email_confirm=".$code);
				$message="Hi ".$_REQUEST['name']."<br/><br/>				
				You have successfully register at www.cineAlerts.com. You are Just few steps away to use the services.<br/><br/>
				
				Your E-Mail Verification Code: ".$code."<br/><br/>
				
				(or)<br/><br/>
				
				Please Visit the below Link to confirm the Verification.<br/>
				
				".$url."
				<br/><br/>
				
				Or Just Login to www.cinealerts.com and Enter the verification code provided above.<br/><br/>
				
				";
			}
			else
			{
				$subject = "Successfully Registered at CineAlerts.com";
				$message="Hi ".$_REQUEST['name']."<br/><br/>
				
				Thank you for register at cineAlerts.com.<br/><br/>
				
				
				";
			}
			sendemail($_REQUEST['email'],$_REQUEST['name'], $subject,$message);
			if(MOBILE_VERIFICATION)
			{
				//Activation CODE
				$sms_message="Hi ".$_REQUEST['name'].",\rYour CineAlerts.com Confirm Code: ".$mcode;
			}
			else
			{
				$sms_message="Hi ".$_REQUEST['name'].",\rThank you for registering in CineAlerts.com";
			}
			sendsms($_REQUEST['mobile'],$sms_message);
			/////////////////////////////////
			
			$header="";
			$message="";
			//mail('Administrator','Registration Pending Approval.', $message,$header);
		return mysql_insert_id();
	}
	else
	{
		return false;
	}
}

function update($user)
{
	global $db;
	if($user['uid'])
	{
		if($user['password'] != "")
		 $pass_query = ", password = '".md5($user['password'])."'";
		 
		$ldetails = get_any_record("users",$user['uid'],"uid");
		if($ldetails['email'] != $user['email'])
		{
			$ecode = rand(10000, 99999);
			$email_query = ", email='".$user['email']."', confirm='0', activationcode='".$ecode."' ";
			//send email activation code to new mail ID
		}
		if($ldetails['mobile'] != $user['mobile'])
		{
			$mcode = rand(1000, 9999);
			$mobile_query = ", mobile='".$user['mobile']."', mconfirm='0', mcode='".$mcode."' ";
			//send SMS activation code to new mobile No.
			//smsnow($user['mobile'],$sms_message.$mcode);
		}
		$query = "update users set name='".$user['name']."', city='".$user['city']."' ".$pass_query.$email_query.$mobile_query." where uid='".$user['uid']."'";
		if(mysql_query($query))
		{
			return "Updated your details";
		}
		else
		{
			return "Invalid details";
		}
	}
	else
	{
		return "Invalid Login";
	}
}

function confirmform($uid)
{
	$user_details = get_any_record("users",$uid,"uid");

	if($_REQUEST['remail'])
	{
		//Activation Link
		$subject = "Email Verification code at CineAlerts.com";
		
		$url = set_url("index.php?task=econfirm&uid=".$uid."&email_confirm=".$user_details['activationcode']);
		$message="Hi ".$user_details['name']."<br/><br/>
		
		You have successfully register at cineAlerts.com. You are Just few steps away to use the services.<br/><br/>
		
		Your E-Mail Verification Code: ".$user_details['activationcode']."<br/><br/>
		
		Please Visit the below Link to confirm the Verification.<br/><br/>
		
		".$url."<br/><br/>
		
		Or Just Login to cinealerts.com and Enter the verification code provided above.<br/><br/>
		
		
		";
		sendemail($user_details['email'],$user_details['name'], $subject,$message);
		$msg = "<span class='mainalert'>E-Mail verification code has been resent.</span>";
	}
	
	if($_REQUEST['rmobile'])
	{
		//Activation CODE
		$sms_message="Hi ".$user_details['name'].",\rYour CineAlerts.com Confirm Code: ".$user_details['mcode'];
		sendsms($user_details['mobile'],$sms_message);
		$msg = "<span class='mainalert'>Mobile verification code has been resent.</span>";
	}

	$mstatus = get_any("users",$uid, "mconfirm","uid");
	$estatus = get_any("users",$uid, "confirm","uid");
	
	$out .="<h1>Step 2: Confirm Your Details</h1>";
	$out .= $msg;
	
	$out .="<div class='registration'>";
	
	$out .="<fieldset>
<legend><b>E-Mail Confirmation</b></legend><br/>";
	if($estatus)
	{
		$out .= "Your E-Mail Verification has been completed.";
	}
	else
	{
		$out .="<form action='".set_url("index.php?task=econfirm")."' method='post'><input type='hidden' name='uid' value='".$uid."' />";
		$out .=" Code: <input type='text' name='email_confirm' /> <input type='submit' value='Confirm E-Mail'  class='submit' /></form><br /><br />";
		$out .="You will receive an E-Mail Shortly. To Confirm your E-Mail address you can can click on the link provided in E-Mail or Enter the Code provided in E-Mail, in the above box <br/><br><strong>Note: Please check your Spam Box for the mail. To prevent receiving in spam box, Please add the email address to your contacts list and remove spam filter.</strong><br/><br/>";
		$out .="E-mail Not received? <a href='".set_url("index.php?task=confirm&uid=".$uid."&remail=1")."'><b>Click here to resend E-Mail</b></a>";
	}
	$out .="</fieldset>";
	
	if(MOBILE_VERIFICATION)
	{
		$out .="<div >";
	
		$out .="<fieldset>
					<legend><b>Mobile Confirmation</b></legend><br />";
		if($mstatus)
		{
			$out .= "Your Mobile Verification has been completed.";
		}
		else
		{
			$out .="<form action='".set_url("index.php?task=mconfirm")."' method='post'><input type='hidden' name='uid' value='".$uid."' />";
			$out .=" Code: <input type='text' name='mobile_confirm' />";
			$out .=" <input type='submit' value='Confirm Mobile' class='submit' /></form><br /><br />";
			$out .="You will receive an SMS Shortly. To Confirm your mobile you can just enter the Code provided in SMS, in the above box <br/><br>";
			//$out .="SMS Not received? <a href='".set_url("index.php?task=confirm&uid=".$uid."&rmobile=1")."'><b>Click here to resend SMS</b></a>";
			$out .="<b>SMS Not received? Write a mail to contact@cinealerts.com with your username and Email address.</b>";
		}
		$out .="</fieldset>";
		$out .="</div>";
	}
	else
	{
		$mstatus = 1;		
	}
	
	if($estatus && $mstatus)
	{
		$out .="<div><a href='".set_url("index.php?task=myaccount")."'>Go to my Account</a></div>";
	}
	
	$out .="</div>";
	return $out;
}


function econfirm($uid, $ecode)
{
	global $db;
	$users = mysql_query("select * from users where uid='".$uid."' and activationcode='".$ecode."'");
	if($user = mysql_fetch_array($users))
	{
		if(mysql_query("update users set confirm='1' where uid='".$uid."'"))
		{
			return "E-Mail ID Confirmed";
		}
		else
		{
			return "Invalid E-mail Verification Code";
		}
	}
	else
	{
		return "Invalid E-mail Verification Code";
	}
}

function mconfirm($uid, $mcode)
{
	global $db;
	$users = mysql_query("select * from users where uid='".$uid."' and mcode='".$mcode."'");
	if($user = mysql_fetch_array($users))
	{
		if(mysql_query("update users set mconfirm='1' where uid='".$uid."'"))
		{
			return "Mobile No. Confirmed";
		}
		else
		{
			return "Invalid Mobile Verification Code";
		}
	}
	else
	{
		return "Invalid Mobile Verification Code";
	}
}

function comments($id, $field = "mid") //$id either mid or tid based on field value (mid/tid)
{
	
	global $db;
	
	//List all comments of the movie/theater
	$comments = mysql_query("select * from comments where ".$field." = '".$id."' and aprove = '1' order by cdate DESC");
	
	if(mysql_num_rows($comments)==0)
	{
		$form .="<br/><b>Be the first person to comment!</b><br/>";
	}
	else
	{
		$i=1;
		while($comment = mysql_fetch_array($comments))
		{
			
			if($i%2==0)
				$bgcolor = " bgcolor= '#FFFFFF' ";
			else
				$bgcolor = "";
			$out .="<div class='comment'><b>".get_any("users",$comment['uid'], "name", "uid")."</b> <span class='minifont'>[".date("jS F, Y, g:i a",strtotime($comment['cdate']))."]</span>";
			$out .="<br/><br/>".stripslashes($comment['comment'])."<br/><br/>";
			$out .="</div>";
			$i++;
		}
	}
	$out .="<div class='clear'></div>";
	if($_SESSION['luid'])
	{
		$comment_status = mysql_query("select * from comments where ".$field." = '".$id."' and uid='".$_SESSION['luid']."'");
		
		if(@mysql_num_rows($comment_status) == 0)
		{
			$form .='<form method="post">';
			$form .='<input type="hidden" name="'.$field.'" value="'.$id.'" />';
			$form .='Rating: <input type="radio" name="rating" value="1.0" />1 <input type="radio" name="rating" value="2.0" />2 <input type="radio" name="rating" value="3.0" checked="checked" />3 <input type="radio" name="rating" value="4.0" />4 <input type="radio" name="rating" value="5.0" />5 out of 5<br/> '; 
			$form .='Comment:<br /><textarea name="comment" cols="55" rows="3"></textarea><br/>';
			$form .='<input type="submit" value="Add Comment" class="submit" />';
			$form .='</form>';
			if($field == "mid")
			{
				$release = get_any("movies",$id, "rdate", "mid");
				if($release > date('Y-m-d'))
					$form = "<br/><b>Movie not yet released.</b><br/>";
			}
		
			if(trim($_REQUEST['comment']))
			{
				$query = "insert into comments (mid,tid,rating,comment,uid) values('".$_REQUEST['mid']."','".$_REQUEST['tid']."', '".$_REQUEST['rating']."', '".trim(nl2br(addslashes($_REQUEST['comment'])))."', '".$_SESSION['luid']."')";
				if(mysql_query($query))
				{
					$out .="<br/><b>Comment Added. You can view your comment once the administrator has aproved.</b><br/>";
				}
				else
				{
					$out .="Invalid Comment!";
					$out .=$form;
				}
			}
			else
			{
				$out .=$form;
			}
		}
		else
		{
			$out .="<br/><b>You have already commented.</b><br/>";
		}
	}
	else
	{
		$out .="<br /><h1>Please Login/Register to comment</h1>";
		$out .=loginform($_POST);
	}

	return $out;
}
function gallery_comments($gid) //$gid is gallery ID
{
	
	global $db;
	
	//List all comments of the movie/theater
	$gallery_comments = mysql_query("select * from gallery_comments where gid='".$gid."' and approve = '1' order by cdate DESC");
	$out .="<div class='movie_details'>";
	if(mysql_num_rows($gallery_comments)==0)
	{
		$form .="<br/><div class='icons'><b>Be the first person to comment!</b></div><br/>";
	}
	else
	{
		$i=1;
		while($comment = mysql_fetch_array($gallery_comments))
		{
			
			if($i%2==0)
				$bgcolor = " bgcolor= '#FFFFFF' ";
			else
				$bgcolor = "";
			$out .="<div class='comment'><b>".get_any("users",$comment['uid'], "name", "uid")."</b> <span class='minifont'>[".date("jS F, Y, g:i a",strtotime($comment['cdate']))."]</span>";
			$out .="<br/><br/>".stripslashes($comment['comment'])."<br/><br/>";
			$out .="</div>";
			$i++;
		}
	}
	$out .="";
	if($_SESSION['luid'])
	{
		$comment_status = mysql_query("select * from gallery_comments where gid='".$gid."' and uid='".$_SESSION['luid']."'");
		
		if(@mysql_num_rows($comment_status) == 0)
		{
			$form .='<div class="icons"><form method="post">';
			$form .='<input type="hidden" name="gid" value="'.$gid.'" />';
			$form .='Rating: <input type="radio" name="rating" value="1.0" />1 <input type="radio" name="rating" value="2.0" />2 <input type="radio" name="rating" value="3.0" checked="checked" />3 <input type="radio" name="rating" value="4.0" />4 <input type="radio" name="rating" value="5.0" />5 out of 5<br/> '; 
			$form .='Comment:<br /><textarea name="comment" cols="55" rows="3"></textarea><br/>';
			$form .='<input type="submit" value="Add Comment" class="submit" />';
			$form .='</form></div>';
			/*if($field == "mid")
			{
				$release = get_any("movies",$gId, "cdate" );
				if($release > date('Y-m-d'))
					$form = "<br/><b>Movie not yet released.</b><br/>";
			}*/
		
			if(trim($_REQUEST['comment']))
			{
				$query = "insert into gallery_comments (gid,rating,comment,uid) values('".$_REQUEST['gid']."', '".$_REQUEST['rating']."', '".trim(nl2br(addslashes($_REQUEST['comment'])))."', '".$_SESSION['luid']."')";
				if(mysql_query($query))
				{
					$out .="<br/><div class='icons'><b>Comment Added. You can view your comment once the administrator has aproved.</b></div><br/>";
				}
				else
				{
					$out .="Invalid Comment!";
					$out .=$form;
				}
			}
			else
			{
				$out .=$form;
			}
		}
		else
		{
			$out .="<br/><div  class='icons'><b>You have already commented.</b></div><br/>";
		}
	}
	else
	{
		$out .="<br /><div class='icons'><h1>Please Login/Register to comment</h1></div>";
		$out .=loginform($_POST);
	}
	$out .="</div>";
	return $out;
}


function view_rating($id, $field="mid", $usercount = 1)
{
	global $db;
	$ratings = mysql_query("select avg(rating) as rating, sum(aprove) as users from comments where ".$field."='".$id."' and aprove='1'");
	
	if($rating = mysql_fetch_array($ratings))
	{
		$rating['rating'] = $rating['rating']?$rating['rating']:0;
		$star = round(($rating['rating']*2), 0);
		$star = (round($star)/2)*10;
		$out = "<img src='".set_url("stars/".$star."star.gif")."' alt='".$star." rating' border='0' />";
		if($rating['rating'])
		{			
			if($usercount)
			{
				$out .='<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">';
				$out .=" <span itemprop=\"ratingValue\">".number_format($rating['rating'],1,".","")."</span> out of <span itemprop=\"bestRating\">5</span>";
				$out .=" <span class='minifont'>[<span itemprop=\"ratingCount\">".$rating['users']."</span> users]</span>";
				$out .="</span>";
			}
		}
	}
	return $out;	
}

function subscribe($cid, $id, $alert_type, $uid = 0 ,$field="mid")
{
	global $movie_alert_types, $theater_alert_types, $db;
	
	$subscriptions = mysql_query("select * from subscribe where uid = '".$uid."' and cid='".$cid."' and ((id = '".$id."' and field='".$field."') or field = '' ) order by alert_type ASC");
	
	while($subscribe = mysql_fetch_array($subscriptions))
	{
		$subs[$subscribe['alert_type']] = " checked = 'checked' ";
	}
	
	if($_REQUEST['subtask']=="update" && $uid)
	{
		if($subs)
		{
			foreach($subs as $key => $alert)
			{
				if(!$alert_type[$key])
				{
					if($key != 0)
					{
						$field_cond = " and id = '".$id."' and field='".$field."' ";
					}
					else
					{
						$field_cond = "";
					}
					
					mysql_query("delete from subscribe where uid='".$uid."' and cid='".$cid."' ".$field_cond." and alert_type='".$key."'");
					//Delete Subscription
					$flag=1;
					$subs[$key] = "";
				}
			}
		}
		
		if($alert_type)
		{
			foreach($alert_type as $key => $alert)
			{
				if(!$subs[$key])
				{
					//Insert Subscription
					if($key != 0)
					{
						$fid = $id;
						$ffield=$field;
					}
					else
					{
						$fid = "";
						$ffield = "";
					}
					
					mysql_query("insert into subscribe(uid, cid, id, alert_type, field ) values('".$uid."', '".$cid."', '".$fid."', '".$key."', '".$ffield."')");
					$flag=1;
					$subs[$key] = " checked = 'checked' ";
				}
			}
		}	
	}
	
	
	if($flag)
	{
		$out .="<b><span class='mainalert'>Your alerts subscriptions updated</span></b>";
	}
	$out .="<form method='post' action=''>";
	if($field == "mid")
	{
		foreach($movie_alert_types as $key => $alert)
		{
			$out .='<input type="checkbox" name="alert_type['.$key.']" value="1" '.$subs[$key].' /> '.$alert."<br/>";
		}
	}
	else if($field == "tid")
	{
		foreach($theater_alert_types as $key => $alert)
		{
			$out .='<input type="checkbox" name="alert_type['.$key.']" value="1" '.$subs[$key].' /> '.$alert."<br/>";
		}
	}
	if($uid)
	{
		$out .='<input type="hidden" name="subtask" value="update" /><input type="submit" class="submit" value="Update Alerts" /></form>';
	}
	else
	{
		$out .="</form><br/><h1>Please Login/Register to Subscribe Mobile Alerts</h1>";
		$out .=loginform($_POST);
	}

	return $out;
	
}

function my_alerts($details)
{
	global $db,$movie_alert_types,$theater_alert_types;
	$uid = $_SESSION['luid'];
	
	if($uid)
	{
		$allalerts = $movie_alert_types + $theater_alert_types;
		$query = "select * from subscribe where uid='".$uid."' order by cid";
		$subscribes = mysql_query($query);
		while($subscribe = mysql_fetch_array($subscribes))
		{
			if($subscribe['field']=="tid")
				{
					$table = "theaters";
					$rfield = "theater";
				}
				else
				{
					$table = "movies";
					$rfield = "movie";
				}
			
			$sub[$subscribe['cid']][$subscribe['alert_type']][] = '<a href="'.set_url("index.php?city=".get_any("cities",$subscribe['cid'],"city","cid").'&'.$rfield.'='.get_any($table, $subscribe['id'], $rfield, $subscribe['field'])).'"> '. get_any($table, $subscribe['id'], $rfield, $subscribe['field']).'</a>';
		}
		$out = '<div class="my_alert1">';
		if($sub)
		{
			foreach($sub as $cid => $avalue)
			{
				$row2 ='<div>'.get_any("cities",$cid,"city","cid")."</div><div class='sub'>";
				$header = "";
				foreach($allalerts as $key => $value)
				{
					$header .= "<div class=heading_style>".$value."</div>";
					$row2 .='<div class="my_alert2">'.@implode("<br/>",$sub[$cid][$key]).'</div>';
				}
				$row2 .="</div>";
				$rows .=$row2;
			}
		}
		else
		{
			$rows ="<div class='red'><br/>You haven't subscribed to any movie or theater. Please browse movies or theaters to subscribe and get alerts on your mobile.</div>";
		}
		$out .="<div>".$header."</div>";
		$out .= $rows;
		$out .="</div>";
	}
	else
	{
		$out .="<br/><h1>Please Login/Register to Subscribe Mobile Alerts</h1>";
		$out .=loginform($_POST);
	}
	
	return $out;
}


function sendlink($details)
{
	global $db;
	if($_SESSION['luid'])
	{
		$city = $_REQUEST['city'];
		$movie = $_REQUEST['movie'];
		$theater = $_REQUEST['theater'];
		if($details['to'] && $details['name'])
		{
			$url = "index.php?";
			if(trim($city))
			{
				$url .="city=".$city;
				if(trim($movie))
				{
					$url .="&movie=".$movie;
				}
				if(trim($theater))
				{
					$url .="&theater=".$theater;
				}
			}
			
			$sname = get_any("users",$_SESSION['luser'], "name", "uid");
			
			$message = "Hello ".$details['name']."<br/><br/>
			
			
			".$sname." has requested you to visit the following Website/Page/URL<br/><br/>
			
			
			".set_url($url)."<br/><br/>
			
			";
			$subject = $sname ." Requested you to check";
			sendemail($details['to'],$details['name'],$subject,$message);
			$msg = "E-Mail Sent to ".$details['name'];
			
		}
		 $out .='<div class="mainalert">'.$msg.'</div>';
		$out .='<h4>Invite your Friend</h4>';
		$out .='<div><form method="post">
				Friend Name:<br/><input type="text" name="name" /><br><br>
				E-Mail:<br /><input type="text" name="to" /><br>
				<input type="submit" value="send" /></form>';
		$out .='</div>';

		
	}
	else
	{
		$out .='<h4>Please login/Register to Invite</h4>'.loginform($_POST);
	}
	return $out;
}


function search($cid,$keyword, $stype, $start)
{
	
	if($stype == "theaters")
	{
		$out .=show_theaters($cid, $start, $keyword);
	}
	else
	{
		$out .=show_movies($cid, $start, $keyword);
	}
	return $out;
}

function set_city($cid)
{
	$_SESSION['user_sel_city'] = $cid;
}

function get_city_id($city)
{
	return get_any("cities",$city,"cid","city");
}

function get_movie_id($movie)
{
	return get_any("movies",$movie,"mid","movie");
}

function get_theater_id($cid, $theater)
{
	global $db;
	$theaters = mysql_query("select * from theaters where cid = '".$cid."' and theater='".$theater."'");
	if($tid = mysql_fetch_array($theaters))
	{
		return $tid['tid'];
	}
	else
	{
		return 0;
	}	
}


function pagenav($present, $link, $totalrecords, $climit = 10)
{
	if($_REQUEST['limit'])
	{
		$_SESSION['cur_limit'] = $_REQUEST['limit'];
		define('LIMIT',$_REQUEST['limit']);
	}
	
	if($_SESSION['limit'])
	{
		define('LIMIT',$_SESSION['limit']);
	}
	
	if($_SESSION['cur_limit'])
	{
		$limits = $_SESSION['cur_limit'];
	}
	else
	{
		$limits = $climit;
	}
	$pages = ceil($totalrecords / $limits);
	$output .="<div class='pagenav'>";
	$pstart = floor(ceil($present/$limits) / 10) * 10;
	$allpages = (($pstart+10) > $pages)? $pages : ($pstart + 10); 
	
	if($pstart == 1)
	{
		$output .="<a href=\"".set_url($link."&start=0")."\"> Start << </a>";
	}
	else if($pstart > 1)
	{
		$output .="<a href=\"".set_url($link."&start=".(($pstart-1) * $limits))."\"> Previous << </a>";
	}
	
	for($i=$pstart; $i<$allpages; $i++)
	{
		$start = $i * $limits;
		if($start == $present)
		{
			$output .="<span class='pageselected'>".($i+1)."</span>";
		}
		else
		{
			$output .="<span class='pages'><a href=\"".set_url($link."&start=".$start)."\">".($i+1)."</a></span>";
		}
	}
	$start = $i * $limits;
	if($allpages < $pages)
	{
		$output .="<a href=\"".set_url($link."&start=".$start)."\"> >> More</a>";
	}
	$output .="<span class='minifont'>[Showing ".(ceil($present/$limits)+1)." of ".$pages." pages.]</span></div>";
	return $output;
}

function top_movies($cid)
{
	global $db;
	$theaters = mysql_query("select * from theaters where cid='".$cid."'");
	while($theater = mysql_fetch_array($theaters))
	{
		$cond[]=" tid = '".$theater['tid']."'";
	}
	if($cond)
	{
		$conds = "(".implode(" or ",$cond).")";
		$playings = mysql_query("select mid from playing where status='1' and ".$conds." group by mid");
		
		while($playing = mysql_fetch_array($playings))
		{
			$mcond[] = " mid = '".$playing['mid']."'";
		}
		if($mcond)
		{
			$mconds =  implode(" or ",$mcond);
		
			$movies = mysql_query("select * from movies where ".$mconds." order by rating DESC LIMIT 0,3");
			$out ='<table width="100%" cellpadding="0" cellspacing="0">
						<tr><th>Top Movies</th></tr>
						<tr><td>';
			$out .= '<table width="100%" cellspacing="2" cellpadding="2" width="0">';
		
			$i=0;
			while($movie = mysql_fetch_array($movies))
			{
				if($i%2 == 0 && $i != 0)
				{
					$out .="</tr>";
				}
				if($i==0)
				{
					$out .= "<tr>";
				}
				$out .="<td align='center' width='50%'>".show_mini_movie($cid, $movie['mid'])."</td>";
				$i++;
			}
			while($i%2 !=0)
			{
				$out .="<td width='50%'>&nbsp;</td>";
				$i++;
			}
			$out .="</tr></table>";
			$out .='</td></tr></table>';
		}
	}
	return $out;
}

 function latest_movies($cid)
{
	global $db;
	$theaters = mysql_query("select * from theaters where cid='".$cid."'");
	while($theater = mysql_fetch_array($theaters))
	{
		$cond[]=" tid = '".$theater['tid']."'";
	}
	if($cond)
	{
		$conds = " and (".implode(" or ",$cond).")";
		$playings = mysql_query("select mid from playing where status='1' ".$conds." group by mid");
		while($playing = mysql_fetch_array($playings))
		{
			$mcond[] = " mid = '".$playing['mid']."'";
		}
		if($mcond)
		{
			$mconds =  " and (".implode(" or ",$mcond).")" ;
		
			$movies = mysql_query("select * from movies where rdate <= now() ".$mconds." order by rdate DESC LIMIT 0,6");
			$out ='<div class="latest">
						<h2>Latest Movies</h2>';
			$i=0;
			
			while($movie = mysql_fetch_array($movies))
			{
				$out .="<div>".show_mini_movie($cid, $movie['mid'])."</div>";
				$i++;
				if($i%2 == 0)
				{
					$out .="<div class='clear'></div>";
				}
			}
			
			$out .='</div>';
		}
	}
	return $out;
}
function upcoming($start=0)
{
	global $db;
	$cid = $_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1;	
	$city = get_any("cities", $cid, "city", "cid");
	$movies = mysql_query("select * from movies where rdate > now()");
	$total_records = mysql_num_rows($movies);
	
	$movies = mysql_query("select * from movies where rdate > now() order by rdate ASC LIMIT ".$start.", 10");
	set_page_title("Upcoming Movies in ".$city." Theaters");
	set_meta_data("Upcoming movies in '".$city." ' hitting to theaters in next few days. Get Film alerts and comments in email of ".$city." movies", "Upcoming, upcoming movies, movie, ".$city.", theaters, cinema, alerts, comments");
	$out .= '<h1>Upcoming Movies</h1>';
	$out .= '<div class="social">'.get_social_share().'</div>';
	
	$i=0;
	$out .= '<div class="movieslist1">';
	while($movie = @mysql_fetch_array($movies))
	{
		$out .="<div class='listed".($i%2)."'><div class='img_block'>";
		$out .=show_mini_movie($cid, $movie['mid']);
		$rdate = $movie['rdate'];
		$url = "index.php?city=".$city."&movie=".get_any("movies", $movie['mid'], "movie", "mid");

		$out .="</div>"; // End of image
		$out .="<div class='theaterslist'>";
		$out .='<strong>Crew/Cast:</strong> <br />';
		$out .='Hero: <span itemprop="actors">'.$movie['hero'].'</span> <br />';
		$out .='Heroine: <span itemprop="actors">'.$movie['heroine'].'</span> <br />';
		$out .='Others: <span itemprop="actors">'.$movie['other'].'</span> <br />';
		$out .='Banner: <span itemprop="productionCompany">'.$movie['banner'].'</span> <br />';
		$out .='Director: <span itemprop="director">'.$movie['director'].'</span> <br />';
		$out .='Music: <span itemprop="musicBy">'.$movie['music'].'</span> <br />';
		$out .='Producer: <span itemprop="producer">'.$movie['producer']."</span> <br />";
		$out .= "<span class='mainalert'>Release Date: ".$rdate."</span>";
		$out .="<div class='clear'></div>";//End of Theaters List
		$out .="<div><a href='".set_url($url."&tab=mobile_alerts")."'>Alerts</a> | <a href='".set_url($url."&tab=gallery")."'>Galleries</a> | <a href='".set_url($url)."'>Cast/Crew</a></div>"; //End of Links
		$out .="<div class='clear'></div>";
		$out .="</div>";//End of theaterlist Right Block;		
		$out .="<div class='clear'></div></div>";//End of list block
		$i++;	
	}
	$out .="<div class='clear'></div>";
	$out .= pagenav($start,"index.php?upcoming=list".$url_append,$total_records);
	$out .="</div>";

	return $out;
}
function coming_movies($cid, $style = "box", $start=0)
{
	global $db;

	if($style == "page")
	{
		$limit_res = 15;
	}
	else
	{
		$limit_res = 5;
		$start=0;
	}
			
	$movies = mysql_query("select * from movies where rdate > now() order by rdate ASC LIMIT ".$start.",".$limit_res);
	$out .= '<div class="bar">';
	$out .= '<h2>Coming Movies</h2>';
	$i=0;
	while($movie = mysql_fetch_array($movies))
	{
		$out .="<div class='movieimage'>".show_mini_movie($cid, $movie['mid'])."</div>";
		$i++;
	}
	$out .="</div>";
	$out .='<span class="readmore"><a href="'.set_url("index.php?upcoming=list").'">+More....</a></span>';
	return $out;
}

function featured_movie($cid)
{
	global $db;
	$query = "select * from theaters where cid = '".$cid."'";
	$theaters = mysql_query($query);
	while($theater = mysql_fetch_array($theaters))
	{
		$cond[] = " tid = '".$theater['tid']."' ";
	}
	$query = "select * from featured order by priority ASC";
	$featureds = mysql_query($query);
	$count = 0;
	while($featured = mysql_fetch_array($featureds))
	{
		$query = "select * from playing where mid = '".$featured['mid']."' and (".@implode("or", $cond).") and status='1'";
		$playings = mysql_query($query);
		if(mysql_num_rows($playings))
		{
			$fmovies = mysql_query("select * from movies where mid = '".$featured['mid']."'");
			if($fmovie = mysql_fetch_array($fmovies))
			{
				$output .= '<a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid").'&movie='.$fmovie['movie']).'"><h4>'.$fmovie['movie'].'</h4><br /><img src="'.set_url("posters/".$featured['mid'].".jpg").'" border="0" width="170" class="thumb" alt = "'.$fmovie['movie'].'" /><br />'.view_rating($featured['mid'],"mid",0).'</a><br/>';
				$count++;
			}
			if($count == 1)
				break;
		}
	}
	return $output;
}


function latest_comments($cid)
{
	global $db;
	$city = get_any("cities",$cid,"city","cid");
	$query = "select * from theaters where cid = '".$cid."'";
	$theaters = mysql_query($query);
	while($theater = mysql_fetch_array($theaters))
	{
		$cond[] = " tid = '".$theater['tid']."' ";
	}
	
	$tcond = implode("or", $cond);
	if($tcond)
	{
		$tcond = " where ".$tcond;
	}
	$query = "select * from playing ".$tcond;
	$movies = mysql_query($query);
	while($movie = mysql_fetch_array($movies))
	{
		$cond[] = " mid = '".$movie['mid']."' ";
	}
	
	$conds = implode("or", $cond);
	
	if(trim($conds))
	{
		$conds = " and ( ".$conds . " ) ";
	}
	
	$query = "select * from comments where aprove='1' ".$conds." order by cdate DESC LIMIT 0, 5";
	$comments = mysql_query($query);
	while($comment = mysql_fetch_array($comments))
	{
		
		if($comment['mid'])
		{
			$movie = get_any("movies", $comment['mid'], "movie", "mid");
			$link = "<a href=\"".set_url("index.php?city=".$city."&movie=".$movie."&tab=comments")."\">";
			$sub = "<br /><div class='dotted' > On <b>".$link.$movie. "</a></b> movie</div>"; 
			
		}
		else if($comment['tid'])
		{
			$theater = get_any("theaters", $comment['tid'], "theater", "tid");
			$link ="<a href=\"".set_url("index.php?city=".$city."&theater=".$theater."&tab=comments")."\">";			
			$sub =  "<br /><div class='dotted'> On <b>".$link.get_any("theaters", $comment['tid'], "theater", "tid")."</a></b></div>"; 
			
		}
		
		$out .=$link.ucwords(substr(stripslashes($comment['comment']),0, 65))."...</a>";
		$out .=$sub;
	}
	return "<div class='latest_comments'><h2>Latest Comments</h2>".$out."</div>";
}


function cities($cid, $country = "India")
{
	global $db;
	$cities = mysql_query("select * from cities where publish='1' and country = '".$country."' order by lorder ASC");
	while($city = mysql_fetch_array($cities))
	{
		if($cid == $city['cid'])
			$cities_links[] = '<a href="'.set_url("index.php?city=".$city['city']).'" class="citieslistselected"><strong>'.$city['city'].'</strong></a>';
		else
			$cities_links[] = '<a href="'.set_url("index.php?city=".$city['city']).'">'.$city['city'].'</a>';

	}
	$out ='<div class="citieslist">
			<h2>Show Timings in</h2>';
	$out .='City: '.implode(", ",$cities_links);
	$out .='</div>';

	return $out;
}

function menucities($cid, $listtype = "movie", $country = "India")
{
	global $db;
	$cities = mysql_query("select * from cities where publish='1' and country = '".$country."' order by lorder ASC");
	while($city = mysql_fetch_array($cities))
	{
		if($cid == $city['cid'])
			$cities_links .= '<li><a href="'.set_url("index.php?city=".$city['city']."&".$listtype."=").'" class="citieslistselected"><strong>'.$city['city'].'</strong></a></li>';
		else
						$cities_links .= '<li><a href="'.set_url("index.php?city=".$city['city']."&".$listtype."=").'">'.$city['city'].'</a></li>';

	}
	$out ='<ul>'.$cities_links.'</ul>';
	

	return $out;
}

function menugalleries($gtype = "")
{
	global $db, $gallerytypes;
	$gtype=ucwords($gtype);
	$cities = mysql_query("select * from cities where publish='1' and country = '".$country."' order by lorder ASC");
	foreach($gallerytypes as $val => $gallerytype)
	{
		if($gtype == $gallerytype)
			$gallery_links .= '<li><a href="'.set_url("index.php?gallery=list&gtype=".$gallerytype).'" class="citieslistselected"><strong>'.$gallerytype.'</strong></a></li>';
		else
			$gallery_links .= '<li><a href="'.set_url("index.php?gallery=list&gtype=".$gallerytype).'" >'.$gallerytype.'</a></li>';

	}
	$out ='<ul>'.$gallery_links.'</ul>';
	

	return $out;
}

function show_movies($cid, $start = 0, $keyword="")
{
	global $db;
	$theaters = mysql_query("select * from theaters where cid = '".$cid."'");
	while($theater = mysql_fetch_array($theaters))
	{
		$conds[] = "playing.tid='".$theater['tid']."'";
	}
	
	if($_REQUEST['language']!="")
	{
		$lmovies = mysql_query("select * from movies where language = '".$_REQUEST['language']."' order by rdate DESC");
		while($lmovie = mysql_fetch_array($lmovies))
		{
			$mconds[] = "playing.mid='".$lmovie['mid']."'";
		}
		$title_append = $_REQUEST['language']." ";
		$cond = "(".@implode(" or ", $mconds).") and ";
		$url_append = "&language=".$_REQUEST['language'];
	}
	
	if($keyword)
	{
		$lmovies = mysql_query("select * from movies where movie like '%".$keyword."%' order by rdate DESC");
		while($lmovie = mysql_fetch_array($lmovies))
		{
			$mconds[] = "playing.mid='".$lmovie['mid']."'";
		}
		$title_append = $keyword." ";
		$cond = "(".@implode(" or ", $mconds).") and ";
		$url_append = "&keyword=".$keyword."&stype=movies&task=search";
		
	}
	
	$cond .= "(".@implode(" or ", $conds).")";
	$cond .= " and  playing.status = 1";
	$total_records = @mysql_num_rows(mysql_query("select mid from playing where ".$cond." group by mid") );
	$movies = mysql_query("select playing.mid from playing, movies where playing.mid=movies.mid and ".$cond." group by playing.mid order by movies.rdate DESC LIMIT ".$start.", 10");
	$i=0;
	$city = get_any("cities",$cid,"city","cid");
	set_page_title(ucfirst($title_append)."Movies Playing in ".$city." Theaters with User review Comments and Ratings");
	$out = "<div class='social'>".get_social_share()."</div>";
	$out .="<h1>".ucfirst($title_append) ."Movies in ".$city."</h1>";
	
	set_meta_data("Movies playing in ".$city." theaters / List of Movies in ".$city." / CINEMA / FILM / MOVIE Changes in Theaters of ".$city, "movies, movies in ".$city.",".$city.", theaters, cinema, english, hindi, telugu, mobile, alerts, comments, reviews");
	
	if(!$total_records)
	{
		$out .="<br><br><span class='mainalert'>No. Movies found.</span>";
	}
	$out .= '<div class="movieslist">';
	while($movie = @mysql_fetch_array($movies))
	{
		$out .="<div class='listed".($i%2)."'><div class='img_block'>";
		$out .=show_mini_movie($cid, $movie['mid']);
		$rdate = get_any("movies", $movie['mid'], "rdate", "mid");
		if($rdate > date("Y-m-d"))
		{
			$out .= "<br /><span class='mainalert'>Coming Soon</span>";
		}
		$url = "index.php?city=".$city."&movie=".get_any("movies", $movie['mid'], "movie", "mid");

		$out .="</div>"; // End of image
		$out .="<div>"; //Right Block
		$out .="<div class='theaterslist'>".show_mini_playing_theaters($cid,$movie['mid'] )."</div>";
		$out .="<div class='clear'></div>";//End of Theaters List
		$out .="<div><a href='".set_url($url)."'><b>Theaters</b></a> | <a href='".set_url($url."&tab=comments")."'>Comments</a> | <a href='".set_url($url."&tab=mobile_alerts")."'>Alerts</a> | <a href='".set_url($url."&tab=gallery")."'>Galleries</a> | <a href='".set_url($url)."'>Cast/Crew</a></div>"; //End of Links
		$out .="<div class='clear'></div>";
		$out .="</div>";//End of Right Block;		
		$out .="<div class='clear'></div></div>";//End of list block
		$i++;	
	}
	$out .="<div class='clear'></div>";
	$out .= pagenav($start,"index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".$url_append,$total_records);
	$out .="</div>";//End of Movies List
	//Start of Right Block
	$out .='<div class="list_sky"><h4>Browse by Language</h4>'.languages($cid);
	/*if($_SERVER['HTTP_HOST'] !="localhost")
	{
		$out .='<br/><br/>ADVERTISEMENT<br/>';
              $out .='<!-- Ads160X600 -->
  <!-- MadAdsMedia.com Asynchronous Ad Tag For CineAlerts.com -->
    <!-- Size: 160x600 -->
    <script data-cfasync="false" src="http://ads-by.madadsmedia.com/tags/9723/5583/async/160x600.js" type="text/javascript"></script>
    <!-- MadAdsMedia.com Asynchronous Ad Tag For CineAlerts.com -->';
	}
	else
	{
		$out .="<div class='google_add'>Google Ad</div>";
	}
*/
	$out .="</div>"; //End of list sky (Right block)
	
		
	return $out;
}

function show_mini_movie($cid, $mid)
{
	global $db;
	$movies = mysql_query("select * from movies where mid = '".$mid."'");
	if($movie = mysql_fetch_array($movies))
	{
		return '<a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".$movie['movie']).'">'.$movie['movie'].'<br />'.show_movie_thumb($mid).'<br />'.view_rating($mid,"mid",0).'</a>';
	}
}

function show_movie_thumb($mid, $img_type = "thumbs/")
{

	return "<img src='".set_url("posters/".$img_type.$mid.".jpg")."' border='0' class='thumb' alt = '".get_any("movies",$mid,"movie","mid")."' itemprop=\"image\" />";
}

function show_movie($mid,$cid)
{
	global $db;
	
	$movie_name = get_any("movies",$mid,"movie","mid");
	$movie_city = get_any("cities",$cid,"city","cid");
	if($_REQUEST['tab'] == '')
	{
	$out = '<div itemscope itemtype="http://schema.org/Movie" class="movied_info">';
	}
	else
	{
	$out = '<div class="movied_info">';
	}
	$out .= '<div class="moviesummary">';		
	//$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=").'">'.$movie_city.' Movies </a> <span>></span> ';
	$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=").'">'.$movie_city.' Movies</a> > ';
	
	

	switch($_REQUEST['tab'])
	{
		case 'comments': set_page_title("Review Comments on ".$movie_name." Movie Playing in ".$movie_city);
				 break;
		case 'mobile_alerts': set_page_title("Get SMS & email alerts on ".$movie_name." Movie Playing ".$movie_city);
			      break;
		case 'gallery': set_page_title("Photo Gallery stills events actors actress of ".$movie_name." Movie Playing ".$movie_city);
				 break;
							
		case 'email':set_page_title("Invite friend to watch ".$movie_name." Movie Playing ".$movie_city);
				break;
		case 'related-news':set_page_title("Related News about ".$movie_name." Movie in ".$movie_city." | Latest Cinema News");
				break;
		default: 
			     set_page_title($movie_name." Movie / Picture / Film / Cinema ".$movie_city." Theaters & Showtimes");
				 break;
	}

	$out .= '<a itemprop="url" href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'"><strong>'.$movie_name.'</strong></a><div class="social">'.get_social_share().'</div><br/><br/><br/> ';
	if($_REQUEST['tab'] == '')
	{
	$out .= "<h1><span itemprop=\"name\">".$movie_name." Movie in ".$movie_city."</span></h1>".view_rating($mid,"mid");
	}
	else
	{
	$out .="<h1>".$movie_name." Movie in ".$movie_city."</h1>".view_rating($mid,"mid");
	}
	$out.="</div>";
	$movies = mysql_query("select * from movies where mid = '".$mid."'");
	
	if($movie = mysql_fetch_array($movies))
	{
		$out .='<div class="movieimage">';
		$out .= show_movie_thumb($mid,"thumbs/");
		$imgurl=set_url("posters/".$mid.".jpg");
		$title=$movie_name." Movie in ".$movie_city;
		fb_image($imgurl,$title);
		$out .='</div>';
		$out .='<div class="moviecrew">';
		if($movie['rating'])
		{
			$out .='Editorial Rating: <span class="mainalert">'.$movie['rating'].'/5.0</span><br />';
		}
		$out .='<strong>Crew/Cast:</strong> <br />';
		$out .='Hero: <span itemprop="actors">'.$movie['hero'].'</span> <br />';
		$out .='Heroine: <span itemprop="actors">'.$movie['heroine'].'</span> <br />';
		$out .='Others: <span itemprop="actors">'.$movie['other'].'</span> <br />';
		$out .='Banner: <span itemprop="productionCompany">'.$movie['banner'].'</span> <br />';
		$out .='Director: <span itemprop="director">'.$movie['director'].'</span> <br />';
		$out .='Music: <span itemprop="musicBy">'.$movie['music'].'</span> <br />';
		$out .='Producer: <span itemprop="producer">'.$movie['producer']."</span> <br />";
		$mdesc .="Film '".$movie_name ."' Cast and Crew details: ";
		$mdesc .=trim($movie['hero']).' acted as hero, ';
		$mdesc .=trim($movie['heroine']).' as heroine and ';
		$mdesc .='Others are '.$movie['other'].'.';
		$mdesc .=' Film banner is '.trim($movie['banner']).' and ';
		$mdesc .='Directed by '.$movie['director'].', ';
		//$mdesc .='Music given by '.$movie['music'].' ';
		//$mdesc .='and Produced by '.$movie['producer'].'.';
		
		$out .="<span class='mainalert' itemprop='datePublished' content='".date("Y-m-d",strtotime($movie['rdate']))."'>Release Date: ".date("M jS, Y",strtotime($movie['rdate']))."</span> ";
		if($movie['rdate'] > date("Y-m-d"))
		{
		}
		$out .='</div>';
		$out .='<div class="icons">';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name).'"><img src="'.set_url('images/icons/theaters.png').'" alt="Playing in"> Theaters</a>&nbsp;&nbsp;&nbsp;';		
		$out .=' <a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=comments").'"><img src="'.set_url('images/icons/comments.png').'" alt="Playing in">  Comments</a>&nbsp;&nbsp;&nbsp;';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=mobile_alerts").'"> <img src="'.set_url('images/icons/alerts.png').'" alt="Alerts"> Email Alerts</a>&nbsp;&nbsp;&nbsp;';
		$out .='<a href="'.set_url("index.php?city=".$movie_city."&movie=".$movie_name."&tab=gallery").'"> <img src="'.set_url('images/icons/photo_gallery.png').'" alt="Alerts"> Photo Galleries</a>&nbsp;&nbsp;&nbsp;';
		$out .='</div>';
	}
	$out .='</div>';
	switch($_REQUEST['tab'])
	{
		case 'comments': set_meta_data("Comments on ".$movie_name." Movie in ".$movie_city ." ". $mdesc , $movie_name.", ".$movie_city.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer'] );
				 $tabblock = comments($mid);
				 $ctab = "a";
				 break;
		case 'mobile_alerts': set_meta_data("Get SMS and email alerts on ".$movie_name." Movie in ".$movie_city ." ". $mdesc , $movie_name.", ".$movie_city.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer'] );
					$tabblock = subscribe($cid, $mid, $_REQUEST['alert_type'], $_SESSION['luid'] ,"mid");
					$stab = "a";
					break;
		case 'gallery': set_meta_data("Photo Galleries stills wall papers of ".$movie_name." Movie ". $mdesc , $movie_name.", ".$movie_city.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer'] );
					$tabblock = search_galleries($movie_name.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer']);
					$gtab = "a";
					break;
							
		case 'email': set_meta_data("Invite a friend to watch ".$movie_name." Movie in ".$movie_city ." ". $mdesc , $movie_name.", ".$movie_city.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer'] );
				$tabblock = sendlink($_REQUEST);
				$etab = "a";
				break;
		case 'related-news': set_meta_data("Related news about ".$movie_name." Movie in ".$movie_city ." ". $mdesc , $movie_name.", ".$movie_city.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer'] );
				$tabblock = related_news($mid);
				$rtab = "a";
				break;
		default: 
				 set_meta_data($movie_name." Movie / Film / Picture / Cinema playing in ".$movie_city ." Theaters and Showtimes. ".trim($mdesc) , $movie_name.", movie , ".$movie_city.", cinema, picture, film, playing, Theaters, showtimes" );
				 $tabblock = show_playing_theaters($cid,$mid);
				 $ttab = "a";
				 break;
	}
	
	
	$out .='<div class="movie_details">';
	$out .='<div style="width:79%;">';
	$out .='<div class="'.$ttab.'tab"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".get_any("movies",$mid,"movie","mid")).'">Theaters</a></div>';
	$out .='<div class="'.$ctab.'tab"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".get_any("movies",$mid,"movie","mid")."&tab=comments").'">Comments</a></div>';
	$out .='<div class="'.$stab.'tab" ><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".get_any("movies",$mid,"movie","mid")."&tab=mobile_alerts").'">Alerts</a></div>';
	$out .='<div class="'.$gtab.'tab"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".get_any("movies",$mid,"movie","mid")."&tab=gallery").'">Galleries</a></div>';
	$out .='<div class="'.$rtab.'tab" ><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".get_any("movies",$mid,"movie","mid")."&tab=related-news").'">Related News</a></div>';
	//$out .='<div class="'.$etab.'tab" ><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=".get_any("movies",$mid,"movie","mid")."&tab=email").'">Email</a></div>';
	$out .='<div class="movie_content">';
	$out .=$tabblock;	
	$out .="</div>";
	$out .="</div>";

	$out .='<div class="img_block">'.related_galleries($movie_name.", ".$movie['hero'].", ".$movie['heroine'].", ".$movie['other'].", ".$movie['director'].", ".$movie['music'].", ". $movie['producer'], 3);
      /*  $out .='<br/>Ads<br/><!-- Ads120X600 -->
<script type="text/javascript"><!--
google_ad_client = "ca-pub-7363809219244122";
google_ad_width = 120;
google_ad_height = 600;
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
';*/
	$out .='</div>';//End of Related Galleries
	$out .='</div>'; //End of Movie Content
	return $out;
}

function languages($cid)
{
	global $languagetypes;
	$links[] = '<a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&movie=").'">All</a> ';
	foreach($languagetypes as $languagetype)
	{
		if($_REQUEST['language'] == $languagetype)
		{
			$label = "<b>".$languagetype."</b>";
		}
		else
		{
			$label = $languagetype;
		}
		$links[] = '<a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&language=".$languagetype."&movie=").'">'.$label.'</a> ';
		
	}
	
	return @implode("<br />",$links);
}

function show_mini_playing_theaters($cid,$mid, $tlimit = 4)
{
	global $db;
    $city = get_any("cities",$cid,"city","cid");
	$theaters=mysql_query("select * from theaters where cid='".$cid."'");
	
	while($theater = mysql_fetch_array($theaters))
	{
		$city_theaters[]=" tid = ".$theater['tid'];
	}
	
	if($city_theaters)
	{
		$cond = " and (".@implode(" or ", $city_theaters).")";	
	}
	$playings = mysql_query("select * from playing where mid = '".$mid."' ".$cond. " and status = 1");
	while($playing = mysql_fetch_array($playings))
	{
		$play_shows[$playing['tid']][$playing['showid']] = $playing['timings'];
	}
	
	if($play_shows)
	{
		$i=0;
		foreach($play_shows as $tid => $shows)
		{
			@ksort($shows);
			$tname = get_any("theaters",$tid,"theater","tid");
			$theatersl[]="<a href=\"".set_url("index.php?city=".$city."&theater=".$tname)."\"><span class='minifont'>".$tname."</span></a> <span class='minifont'>".implode(",",$play_shows[$tid])."</span>";	
			$i++;
			if($i==$tlimit)
			{
				break;
			}
		}	
	}
	$theaterslist = @implode("<br/>",$theatersl);
	return "<br /><strong>Playing In:</strong><br/>".$theaterslist;
}

function show_playing_theaters($cid,$mid)
{
	global $db;
    $city = get_any("cities",$cid,"city","cid");
	$movie_name = get_any("movies",$mid,"movie","mid");

	$theaters=mysql_query("select * from theaters where cid='".$cid."'");
	while($theater = mysql_fetch_array($theaters))
	{
		$city_theaters[]=" tid = ".$theater['tid'];
	}
	
	if($city_theaters)
	{
		$cond = " and (".@implode(" or ", $city_theaters).")";	
	}
	$playings = mysql_query("select * from playing where mid = '".$mid."' ".$cond. " and status = 1 ");
	
	while($playing = mysql_fetch_array($playings))
	{
		$play_shows[$playing['tid']][] = $playing['timings'];
	}
	if($play_shows)
	{
		$i=0;
		foreach($play_shows as $tid => $shows)
		{
			@ksort($shows);
	
			$out .='<div class="theater">';	
			$tname = get_any("theaters",$tid,"theater","tid");
			$out .="<div class='theater_name'><a href=\"".set_url("index.php?city=".$city."&theater=".$tname)."\"><b>".$tname."</b></a><br /><span class='minifont'>".get_any("theaters",$tid,"area","tid")."</span></div>";
			$out .="<div class='theater_times'><span class='minifont'>".implode(", ",$play_shows[$tid])."</span> </div>";
			$out .="<div class='theater_rate'>".view_rating($tid,"tid",0)."<br/><a href=\"".set_url("index.php?city=".$city."&theater=".$tname)."\">View Details</a></div>";	
	
			$out .="</div>";
			$for_page_title_theaters[]= $tname;			
		}
		
	}
	else
	{
		$out .='<h4>'.$movie_name.' movie not playing or not yet released in '.$city.'</h4>';	
	}
	if(@$for_page_title_theaters)
	$out .='"'.$movie_name.'" movie in '.$city.' playing in '.@implode(", ",$for_page_title_theaters);
	//set_page_title(" ".substr(@implode(", ",$for_page_title_theaters), 0, 30)." Theaters");
	return $out;
}

function show_theaters($cid, $start = 0, $keyword="", $area="" )
{
	global $db;
	
	$area = str_replace("_", " ", $area);
	$city = get_any("cities",$cid,"city","cid");
	
	if(trim($keyword))
	{
		$cond = " and theater like '%".$keyword."%'";
		$url_append = "&keyword=".$keyword."&stype=theaters&task=search";
	}
	
	if(trim($area))
	{
		$cond .= " and area like '".$area."'";
		$url_append = "&area=".$area;
	}
	$total_records = mysql_num_rows(mysql_query("select * from theaters where cid='".$cid."'" . $cond));
	$theaters=mysql_query("select * from theaters where cid='".$cid."' ".$cond." LIMIT ".$start.", 30");
	$i=0;
	if($_REQUEST['area']!="")
	{
		$title_prepend = str_replace("_"," ",$_REQUEST['area']).", ";
	}
	
	set_page_title("Theaters in ".$title_prepend.$city." | Get alerts when ever movie changes in Cinema Hall");
	$out = "<div class='social'>".get_social_share()."</div><h1>Theaters in ".$title_prepend.$city."</h1>";
	$out .="<div class='theaters'>";

	while($theater = mysql_fetch_array($theaters))
	{

			$out .="<div class='theater'>";
			$out .="<div class='theater_name'><a href=\"".set_url("index.php?city=".$city."&theater=".$theater['theater'])."\"><b>".$theater['theater']."</b></a> <br /><span class='minifont'>".$theater['area']."</span> </div>";
			$out .="<div class='theater_times'><a href=\"".set_url("index.php?city=".$city."&theater=".$theater['theater'])."\">Movies Playing</a>  <br/><a href=\"".set_url("index.php?city=".$city."&theater=".$theater['theater']."&tab=mobile_alerts")."\"><img src='".set_url("images/alert_me.jpg")."' border='0' alt='Alert me on movie change' /></a> </div>";
			$out .="<div class='theater_rate'>".view_rating($theater['tid'],"tid",0)."</div>";				
			$out .= "</div>";				
	$out .="<div class='clear'></div>";
	}
	$out .="<div class='clear'></div>";
	set_meta_data("Theaters in ".$title_prepend.$city." list of movie show times in ".$city." theaters by ","theaters, ".$city.",Area-wise, movies, playing, comments, show times");
	$out .= pagenav($start,"index.php?city=".$city."&theater=".$url_append,$total_records,30);
	$out .="</div>";//End of Theaters
	$out .="<div class='list_sky'><h4>Area-Wise Theaters</h4><br />";
	$out .="<a href=\"".set_url("index.php?city=".$city."&theater=")."\">All areas</a>";
	$out .="<div class='scroller'>".areafilter($cid)."</div>";
	if($_SERVER['HTTP_HOST']!="localhost")
	{
	$out .='ADVERTISEMENT<br/>';
	}
	else
	{
		$out .="<div class='google_add2'>Google Ad</div>";
	}
	$out .="</div>";
	
	return $out;
}

function areafilter($cid)
{
	global $db;
	$areas = mysql_query("select area from theaters where cid='".$cid."'  group by area order by area ASC");
	set_meta_data("Area-Wise: ","");
	while($area = mysql_fetch_array($areas))
	{
		$list[]="<a href=\"".set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&theater=&area=".$area['area'])."\">".$area['area']."</a> ";
		set_meta_data($area['area'].", ","");
	}
	$list[]="<a href=\"".set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&theater=")."\">All areas</a>";
	
	if($list)
	{
		$out = implode("<br />",$list);
	}	
	return $out;
	
}

function show_mini_theater($tid)
{
	global $db;
	$theaters = mysql_query("select * from theaters where tid = '".$tid."'");
	if($theater = mysql_fetch_array($theaters))
	{
		$out = "<a href=\"".set_url("index.php?city=".get_any("cities",$theater['cid'],"city","cid")."&theater=".$theater['theater'])."\">".show_theater_thumb($theater['tid'])."<br/>".$theater['theater']."<br/>".view_rating($tid,"tid",0)."</a>";
	}
	return $out;
}

function show_theater_thumb($tid, $img_type = "thumbs/")
{
	return "<img src='theaters/".$img_type.$tid.".jpg' border='0' alt='".get_any("theaters",$tid, "theater","tid")."' />";
}

function show_theater($tid)
{
	global $db;
	$theaters = mysql_query("select * from theaters where tid='".$tid."'");
	
	if($theater = mysql_fetch_array($theaters))
	{
		$cid = $theater['cid'];
		$theater_name = get_any("theaters",$tid,"theater","tid");
		$city = get_any("cities",$cid,"city","cid");
		

		$out .='<a href="'.set_url("index.php?city=".$city."&theater=").'">'.$city.' Theaters</a> > ';
		$out .= '<a href="'.set_url("index.php?city=".$city."&theater=".$theater['theater']).'"><strong>'.$theater['theater'].'</strong></a><br/><br/> ';
		//$out .= "<h1>".get_any("theaters",$tid,"theater","tid")." in ".get_any("cities",$theater['cid'],"city","cid")."</h1>".view_rating($tid,"tid");
		
		$out .= "<div itemscope itemtype='http://schema.org/LocalBusiness' class='show_theater'><div class='show_theater1'><h1 itemprop='name'>".get_any("theaters",$tid,"theater","tid")." in ".get_any("cities",$theater['cid'],"city","cid")."</h1>".view_rating($tid,"tid")."</div><div class='social_share1'>".get_social_share()."</div></div>";
		
		$out .='<div class="social_share">';
		$out .='<div class="show_theater2">';
		$out .='Area Location:<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><span itemprop="addressLocality">'.$theater['area']."</span></span><br/>";
		$out .=trim($theater['contact'])?'Contact No:<span itemprop="telephone">'.$theater['contact']:'</span>';
		//Get current Playing movies in theater
		$movies = mysql_query("select * from playing where tid = '".$tid."' and status='1' order by showid ASC");
		while($movie = mysql_fetch_array($movies))
		{
			$movie_shows[$movie['mid']][$movie['showid']] = $movie['timings'];
		}
		$i=0;
		
		if($movie_shows)
		{
			$out .= "<h4>Movies Playing</h4>";
			
			
			foreach($movie_shows as $mid => $shows)
			{
				if($i%3 == 0 && $i != 0)
				{
					$out .="</div>";
				}
				if($i==0 || $i%3 == 0)
				{
					$out .= "<div class='clear'>";
				}
				@ksort($shows);
				$out .="<div class='show_theater_icons'>";
				$rdate = get_any("movies",$mid, "rdate", "mid");
				if($rdate > date("Y-m-d"))
				{
					$out .="<span class='alert'>Releasing Soon</span><br />";
				}
				$out .=show_mini_movie($cid, $mid)."<br/><span class='minifont'>".implode(", ",$shows)."</span></div>";
				$for_page_title[]= get_any("movies",$mid,"movie","mid");
				$i++;
			}
			while($i%3 !=0)
			{
				$out .="<div class='show_theater4' >&nbsp;</div>";
				$i++;
			}
			$out .="<div class='clear'></div></div>";
			
		}
		else
		{
			$out .= "<br/><br/><h4>Sorry! we have no Information about this theater.</h4>";
		}
	
		$out .='<div class="clear"></div></div></div>';
		
		
		switch($_REQUEST['tab'])
		{
			
			case 'mobile_alerts': $tabblock = subscribe($cid, $tid,$_REQUEST['alert_type'], $_SESSION['luid'], "tid");
								$title_suffix = "Subscribe for movie change alerts of ";
								$stab = "b";
								 break;
			case 'email': $tabblock = sendlink($_REQUEST);
						$etab = "b";
						$title_suffix = "Invite a friend to ";
						break;
			case 'movies': $tabblock = show_movies_played($cid,$tid);
					 $mtab = "b";
					 $title_suffix = "Recent Movies Played in ";
							 break;
			default: $tabblock = comments($tid,"tid");
							 $ctab = "b";
							 break;							 
		}
		set_page_title($title_suffix.str_replace(Theater, "" ,$theater_name)." Theater in ".$city);
		set_page_title(" | List of movies playing with Show times");
		
		set_meta_data($title_suffix.str_replace(Theater, "" ,$theater_name)." Theater in ".$city." and now playing ".@implode(", ",$for_page_title)." Movies in scheduled show times. ".$theater_name ." is located at ".$theater['area']." In ".$city.", and showing ".@implode(", ",$for_page_title)." Movie",$theater_name.", ".$city.", theater, movie, ".@implode(", ",$for_page_title)  );

		$out .='<div class="show_theater6">';
		$out .='<div class="movies_header">';
		$out .='<div class="'.$mtab.'tab1" valign="middle"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&theater=".get_any("theaters",$tid,"theater","tid")."&tab=movies").'">Movies Played in Theater</a></div>';
		$out .='<div class="'.$ctab.'tab1" valign="middle"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&theater=".get_any("theaters",$tid,"theater","tid")."&tab=comments").'">Comments</a></div>';
		$out .='<div class="'.$stab.'tab1" valign="middle"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&theater=".get_any("theaters",$tid,"theater","tid")."&tab=mobile_alerts").'">Alerts</a></div>';
		$out .='<div class="'.$etab.'tab1" valign="middle"><a href="'.set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&theater=".get_any("theaters",$tid,"theater","tid")."&tab=email").'">Email</a></div>';
		$out .='</div>';
		
		
		$out .='<div>'.$tabblock.'</div>';
		
		$out .="</div>";
		}
	return $out;
}


function show_movies_played($cid, $tid)
{
	global $db;
	//Get Previously Played movies in theater
		$movies = mysql_query("select * from playing where tid = '".$tid."' and status='0' order by showid, sdate DESC LIMIT 0, 100");
		$out .="<div class='show_movies_played'>";
		$movie_shows = "";
		unset($movie_shows);
		while($movie = mysql_fetch_array($movies))
		{
			$movie_shows[$movie['mid']][$movie['showid']] = $movie['timings'];
		}

		if($movie_shows)
		{
		$out .="Recent 20 movies Played in ".get_any("theaters",$tid,"theater","tid")."</div>";
			$i=0;
			foreach($movie_shows as $mid => $shows)
			{
				if($i%4 == 0 && $i != 0)
				{
					$out .="</div>";
				}
				if($i==0 || $i%4 == 0)
				{
					$out .= "<div class='social_share'>";
				}
				@ksort($shows);
				$out .="<div class='latest_gallery3'>".show_mini_movie($cid, $mid)."</div>";
				
				$i++;
				if($i==20)
				{
					break;
				}
			}
			while($i%4 !=0)
			{
				$out .="&nbsp";
				$i++;
			}
			
		}
		$out .="</div>";
		return $out;
}
function homepage($cid)
{
	
	$advantages = get_any_record("articles","4","aid");
      

	$out = "<div class='home_block'>";

	$out .= "<center><div class='dark'>".featured_movie($cid)."<br/>".poll_display(2,$cid);
	$out .="<br/><h6>".$advantages['atitle']."</h6><br/>".$advantages['introtext'];
	$out .="</div></center><div class='light' >".latest_news(1)."<div class='clear'></div></div>";
	$out .="<div class='clear'></div>";
	$out .=latest_comments($cid);
	$out .="<div class='clear'></div>";
	$out .=latest_galleries();
	$out .="<div class='clear'></div>";
	$out .=coming_movies($cid);
	$out .="<div class='clear'></div>";
	$out .="</div>"; //End of Dark and Light Blocks

	return $out;
}

function display_intro_content($article, $more = 1)
{
	$out = "<h1>".$article['atitle']."</h1>";
	$out .= $article['introtext'];
	if($more)
	{
		$out .= '<br />More..';
	}
	return $out;
}

function display_content($article)
{
	$out = "<h1>".$article['atitle']."</h1>";
	set_page_title($article['pagetitle']);
	$out .= '<br /><p>'.$article['maintext'].'</p>';
	return $out;
}

function poll_display($qst_id = 1,$cid=1)
{
	global $db;
	$s_id=session_id();
	if(mysql_fetch_object(mysql_query("select s_id from plus_poll_ans where s_id='".$s_id."' and qst_id='".$qst_id."'")))
	{
		$out_msg = "You have already Polled.";
	}
	
	
	$query=mysql_fetch_object(mysql_query("select * from plus_poll where qst_id=$qst_id and status='active'"));
	if($query)
	{
	if(!page_title)
	{
		set_page_title($query->qst." Opinion Poll",1);
	}
	$out .="
	<form method='post' action=''>
	<input type='hidden' name='task' value='poll_save' />
	<input type='hidden' name='qst_id' value='".$qst_id."' />
	<div class='poll'>
		<div class='poll_que'>$query->qst</div><br/>
  		<input type='radio' class='radios' value='".$query->opt1."'  name='opt' /> ".$query->opt1."<br/>
		<input type='radio' class='radios' value='".$query->opt2."'  name='opt' /> ".$query->opt2."<br/>
		<input type='radio' class='radios' value='".$query->opt3."'  name='opt' /> ".$query->opt3."<br/>
		<input type='radio' class='radios' value='".$query->opt4."'  name='opt' /> ".$query->opt4."<br/><br/><div>";
	 if(!$out_msg)
	 {
	 $out .="<input type='submit' value='Poll' class=''/>";
	 }
	 else
	 {
	 	$out .=$out_msg;
	 }
	
	$out .="<br /><a href=\"".set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&task=poll_result&pollid=".$qst_id)."\">View the poll result</a><br /><br /></div></div></form>";
	}
	 return $out;
}

function poll_save($details, $cid = 1)
{
	global $db;
	$s_id=session_id();
	if(isset($details['opt']))
	{
		$qt=mysql_query("insert into plus_poll_ans(s_id,qst_id,opt) values('".$s_id."','".$details['qst_id']."','".$details['opt']."')");

		$out .="Thanks for your views, Please check the result.".poll_result($details['qst_id'], $cid);
	}
	else
	{
		$out .= "<font face='Verdana' size='2' color=red>Please select one option and then submit.</font>".poll_display($details['qst_id'], $cid);
	}
	return $out;
}

function poll_result($qst_id, $cid=1)
{
	global $db;
	if($qst_id=="")
	{
		$cond = " status='active' order by cdate DESC";
	}
	else
	{
		$cond = "qst_id='".$qst_id."'";
	}
	
	
	$fg=mysql_query("select * from plus_poll where ".$cond);
	$row=mysql_fetch_object($fg);
	$qst_id = $row->qst_id;
	$out .=poll_display($qst_id, $cid);
	$out .= "<h1>$row->qst</h1>"; 
	set_page_title($row->qst." Opinion poll Results",1);
	$rt=mysql_num_rows(mysql_query("select ans_id from plus_poll_ans where qst_id='".$qst_id."'"));

	/* Find out the answers and display the graph */
	$query="select count(*) as no,qst,plus_poll_ans.opt from plus_poll,plus_poll_ans where plus_poll.qst_id=plus_poll_ans.qst_id and plus_poll.qst_id='$qst_id' group by opt ";
	$rs=mysql_query($query);
	$out .= "<div class='poll_table'>";
 
	while($noticia = mysql_fetch_array($rs))
	{
 		$out .= "<div class='row1'>&nbsp;<font size='1' color='#000000'>$noticia[opt]</font></div>";
		$width2=ceil($noticia['no'] * 100/$noticia[no]) ; /// change here the multiplicaiton factor //////////
		$ct=($noticia[no]/$rt)*100;
		$ct=sprintf ("%01.2f", $ct); // number formating 

		$out .= "    <div class='row2'>&nbsp;<font size='1' color='#000000'>($ct %)</font></div><div class='row3'>&nbsp;<img src='".set_url("images/graph.jpg")."' height=10 width='".$ct."%' alt='$ct %'></div>";
 		$out .= "<div class='space'>&nbsp;</div>";
  
	}
	$out .= "</div>";
	
	
	$fg=mysql_query("select * from plus_poll");
	
	$out .="<br/><br/><h4>View Other Poll Results</h4>";
	while($row = mysql_fetch_array($fg))
	{
		$out .="<a href=\"".set_url("index.php?city=".get_any("cities",$cid,"city","cid")."&task=poll_result&pollid=".$row['qst_id'])."\"><h5>".$row['qst']."</h5></a>";
	}
	

return $out;
}

function latest_news($show_all_tumbs=1,$num_rec = 8)
{
	global $db;
	$cid = $_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1;
	$news_items = mysql_query('select * from news where publish="1" order by cdate DESC LIMIT 0, '.$num_rec);
	$totalrecords = mysql_num_rows($news_items);
	if($totalrecords > 1)
	{
		$out = "";
	}
	$i=0;
	while($news = mysql_fetch_array($news_items))
	{
		$out .='<div class="news_item">';
                $n_title = stripslashes(strip_tags($news["title"]));
                $n_title = str_replace("?","",$n_title);
                $n_title = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $n_title);
                $n_title = trim($n_title);
		$link_url =set_url("index.php?news=".$n_title."&nid=".$news["nid"]);
                $out .='<a href="'.$link_url.'">';
		if($show_all_tumbs)
		{
			if($news["nid"])
			{
		  		$out .='<div class="news_thumb"><img src="'.set_url("images/news/thumbs/".$news["nid"].".jpg").'" alt="'.$news["title"].'" border="0" width="60" class="thumb" /></div>';
			}
		}
		$intro_text = stripslashes(strip_tags($news["introtext"]));
		
		$out .='<div class="news_text"><h6>'.$news["title"].'</h6>'.substr($intro_text,0,90)."...</div>";
		$out .='</a>';
		$out .='<div class="clear"></div></div>';
	}
	
	if($out)
	{
	$more_link = set_url('index.php?news=latest');
	$return_out ="<h2>Latest Cinema News</h2>".$out."<span class='readmore'><a href='".$more_link."'>+More News....</a></span><div class='clear'></div>";
	return $return_out;
	}
}

//related news start

function related_news($mid)
{
	global $db;
	$cid = $_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1;
	$result = mysql_query('select movie,hero,heroine,director from movies where mid="'.$mid.'"');
	$res=mysql_fetch_array($result);
	
	$news_items = mysql_query('select * from news where publish="1" and (title like "%'.$res['movie'].'%" or introtext like "%'.$res['movie'].'%" or maintext like "%'.$res['movie'].'%" or title like "%'.$res['hero'].'%" or introtext like "%'.$res['hero'].'%" or maintext like "%'.$res['hero'].'%" or title like "%'.$res['heroine'].'%" or introtext like "%'.$res['heroine'].'%" or maintext like "%'.$res['heroine'].'%" or title like "%'.$res['director'].'%" or introtext like "%'.$res['director'].'%" or maintext like "%'.$res['director'].'%" ) order by cdate DESC LIMIT 0,20');
	//set_page_title("Related News of ".$res['movie'] . " Movie | Latest Cinema News");
	//set_meta_data("Live news of ".$res['movie']." movie with all the information in the media. ".$res['movie']." related news / gossips / events / Audio release and promotion information. ", "movie, ".$city.", ".$res['movie'].",latest, news, gossips, related news");
	
	$i=0;
	while($news = mysql_fetch_array($news_items))
	{
		$out .='<div class="news_item">';
		$link_url =set_url("index.php?news=".$news['title']."&nid=".$news["nid"]);
		if($show_all_tumbs)
		{
			if($news["nid"])
			{
		  		$out .='<div class="news_thumb"><a href="'.$link_url.'"><img src="'.set_url("images/news/".$news["nid"].".jpg").'" alt="'.$news["title"].'" border="0" width="60" class="thumb" /></a></div>';
			}
		}
		$intro_text = stripslashes(strip_tags($news["introtext"]));
		$intro_text = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($intro_text, ENT_COMPAT, 'UTF-8'));
		$out .='<div class="news_text"><a href="'.$link_url.'"><h6>'.$news["title"].'</h6>'.substr($intro_text,0,140)."....	</a></div>";
		
		$out .='<div class="clear"></div></div>';
	}
	
	if($out)
	{
	$more_link = set_url('index.php?news=latest');
	$return_out ="".$out."<div class='clear'></div>";
	return $return_out;
	}
}

//related news end

 function display_news_item($nid)
{
	global $db;
	$cid = $_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1;
	$news_items = mysql_query('select * from news where nid="'.$nid.'"');
	while($news = mysql_fetch_array($news_items))
	{
		set_page_title($news['title'] . " | Latest Cinema News");
	set_meta_data(stripslashes($news["introtext"]), "movie, ".$city.", latest, news, gossips, cinema, mobile, alerts, comments");
		$out .='<div class="social">'.get_social_share().'</div><br/><br/><br/><div><div>';
		$link_url = set_url("index.php?news=".$news['title']."&nid=".$news["nid"]);
	
		if($news["nid"])
		{
			$out .='<a href="'.$link_url.'"><img src="'.set_url("images/news/".$news["nid"].".jpg").'" alt="'.$news["title"].'" border="0" width="240" style="float:left" class="news_img"  /></a>';
		}
		
		$imgurl = set_url("images/news/".$news["nid"].".jpg");
		$title = $news['title'];
		fb_image($imgurl, $title);
		
		$out .='<h1><a href="'.$link_url.'">'.$news["title"].'</a></h1>';
		$out .='<br/>'.stripslashes($news["maintext"]).'</div>';
		$out .='</div>';
	}
	if($out)
	{
	return "<div class='display_new_item'>".$out."</div>";
	}
}
function listall_news($start=0, $show_all_tumbs=1)
{
	global $db;
	$cid = $_SESSION['user_sel_city']?$_SESSION['user_sel_city']:1;
	$news_itemss = mysql_query('select * from news where publish="1"');
	$totalrecords = mysql_num_rows($news_itemss);
	set_page_title("Cinema News latest movie releases photo gossips of movie personalities");
	set_meta_data("Latest movie news on new releases, comments of cinema personalities, happening in cinema industry, what cinema people are looking for and hidden facts", "movie, ".$city.", latest, news, gossips, cinema, mobile, alerts, comments");
	$news_items = mysql_query('select * from news where publish="1" order by cdate DESC LIMIT '.$start.', '.LIMIT);
	$i=0;
	while($news = mysql_fetch_array($news_items))
	{
		$out .='<div class="news_item">';
                $n_title = stripslashes(strip_tags($news["title"]));
                $n_title = str_replace("?","",$n_title);
                $n_title = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $n_title);
                $n_title = trim($n_title);
		$link_url =set_url("index.php?news=".$n_title."&nid=".$news["nid"]);
		if($show_all_tumbs)
		{
			if($news["nid"])
			{
		  		$out .='<div class="news_thumb"><a href="'.$link_url.'"><img src="'.set_url("images/news/thumbs/".$news["nid"].".jpg").'" alt="'.$news["title"].'" border="0" width="100" class="thumb" /></a></div>';
			}
		}
		
		$out .='<div class="news_text"><h4><a href="'.$link_url.'">'.$news["title"].'</a></h4>';
		$out .='<br />'.stripslashes($news["introtext"]) . '</div><div class="clear"></div>';
		$out .='</div>';
	}
	if($_SERVER['HTTP_HOST'] !="localhost")
	{
		$gad ='ADVERTISEMENT<br/>';
	}
	else
	{
		$gad ="<div class='google_add'>Google Ad</div>";
	}


	if($out)
	{
	$out ="<div class='social_share'><div class='social'>".get_social_share()."</div></div><h2>Cinema News</h2><div class='content'><div class='listall1'>".$out."</div><div class='listall2'>".$gad."</div></div>";
	}
	$out .= pagenav($start,"index.php?news=latest",$totalrecords);
	return $out;
}

function listall_videos($start)
{
	global $db;
	$list_output .= "<h1>Videos</h1><div class='social'>".get_social_share()."</div>";
	$query = "select * from videos where status='1'";
	$videos = mysql_query($query);
	$total_records = mysql_num_rows($videos);
	$query = "select * from videos where status='1' order by cdate DESC LIMIT ".$start.",".LIMIT;
	$videos = mysql_query($query);
	
	$list_output .= '<table width="100%" cellspacing="0" cellpadding="6" class="movieslist">';	
	while($video = mysql_fetch_array($videos))
	{

		$list_output .="<tr><td colspan='2' class='listed".($i%2)."'>&nbsp;<a href='".set_url("index.php?video=".$video['title'])."'><strong>".$video['title']."</strong></a></td></tr>";		
		$list_output .="<tr><td width='130' rowspan='2' class='listed".($i%2)."'><a href='".set_url("index.php?video=".$video['title'])."'><img src='http://img.youtube.com/vi/".$video['url']."/default.jpg' class='thumb' /></a></td><td align='left' class='listed".($i%2)."' height='60'>".$video['description']."</td></tr><tr><td class='listed".($i%2)."'><a href='".set_url("index.php?video=".$video['title'])."'><strong>Play</strong></a></td></tr>";
		$i++;		

	}
	$list_output .="</table>";		

	return $list_output;	
}


function view_video($video)
{
	global $db;
	$list_output = '<a href="'.set_url("index.php?video=list").'">Videos</a> > <strong><a href="'.set_url("index.php?video=".$video).'">'.$video.'</a></strong>' ;
	$list_output .= "<div  class='social'>".get_social_share()."</div>";
	$query = "select * from videos where title='".trim($video)."' and status='1'";
	$videos = mysql_query($query);
	
	$list_output .= '<table width="100%" cellspacing="0" cellpadding="6" >';	
	if($video_details = mysql_fetch_array($videos))
	{

		$list_output .="<tr><td><h1>".$video_details['title']."</h1></td></tr>";		
		$list_output .="<tr><td align='center'><iframe src='http://youtube.com/embed/".$video_details['url']."' class='thumb' width='600' height='335' frameborder='0' allowfullscreen ></iframe></td></tr>";
		$list_output .="<td align='left'>".$video_details['description']."</td></tr>";		

	}
	$list_output .="</table>";		

	return $list_output;	
}

function listall_galleries($start, $gtype = "")
{
	global $db, $gallerytypes;
	$gtype = ucwords($gtype);
	if($gtype)
	{
		foreach($gallerytypes as $val => $gallerytype)
		{
			if($gallerytype == $gtype)
			{
				$gallerieslist[$val] = $gallerytypes[$val];
				$append_title = $gallerytypes[$val]." ";
				$listlimit = 20;
			}
		}	
	}
	else
	{
		$gallerieslist = $gallerytypes;
		$listlimit = 4;
	}

	$list_output .= "<div class='social_share'><div class='social_share1'><h1>Latest Photo Galleries</h1></div><div class='social'>".get_social_share()."</div></div>";
	$list_output .= "<div>";
	foreach($gallerieslist as $val => $gallerytype)
	{
		$query = "select * from galleries where gtype='".ucwords($val)."'  and status='1'";
		$galleries = mysql_query($query);
		$total_records = mysql_num_rows($galleries);
		$query = "select * from galleries where gtype='".$val."'  and status='1' order by cdate DESC LIMIT ".$start.",".$listlimit;
		$galleries = mysql_query($query);
		$list_output .= "<div class='gallery_type'>".$gallerytype."</div>";
		$list_output .="<div class='latest_gallery1'>";
		while($gallery = mysql_fetch_array($galleries))
		{
			$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
			if($iid)
			{
				$list_output .="<div  class='latest_gallery2'>";
				$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytype)."'><img src='".set_url('galleries/thumbs/'.$iid.'.jpg')."' width='120' class='thumb' alt='".$gallery['gname']."' border='0' /><br />".$gallery['gname']."</a>";
				$list_output .="</div>";		
				$i++;
			}
			if($i%4 == 0)
			{
				$list_output .="<div class='clear'></div>";
			}	
		}
	
		if($listlimit == 4)
		{
			$list_output .="<div class='latest_gallery4'><a href='".set_url('index.php?gallery=list&gtype='.$gallerytype)."'>More Galleries</a></div>";
		}
		else
		{
			$list_output .= "<div>".pagenav($start,'index.php?gallery=list&gtype='.$gallerytype.$url_append,$total_records,20)."</a></div>";
		}
		$list_output .= "</div>";
	}
	set_page_title($append_title."Latest Photo gallery wall paper stills | Photo Galleries of telugu Actress Actors Movies Latest Events");

	set_meta_data($append_title."Photo Galleries of tollywood actors actresses movies and events will be available in latest tollywood galleries.", "photos, gallery, movies, wall papers, stills, tollywood, actors, actress");
	$list_output .= "</div>";
	
	return $list_output;
}

function view_gallery($gid, $gtype, $start=0, $sel = 0)
{
	global $db, $gallerytypes;
	if(!$start || $start < 0)
	{
		$start=0;
	}
	if(!$sel || $sel < 0)
	{
		$sel=0;
	}
	$dir = "../galleries/".$gid;
	$gallerydetails = get_any_record("galleries", $gid, "gid");
	
	$list_output ="<a href='".set_url("index.php?gallery=list")."'>Photo Galleries  ></a> <a href='".set_url("index.php?gallery=list&gtype=".$gtype)."'>".$gtype."</a><div class='social'>".get_social_share()."</div><br/>";
	
	$list_output .= "<h1>".$gallerydetails['gname']."</h1><br/>";
	
	$query = "select * from galleryimages where gid='".$gid."' order by cdate DESC";
	$gallery = @mysql_query($query);
	$imagescount = mysql_num_rows($gallery);
	set_page_title($gallerydetails['gname']. " Latest Photo Gallery Images | Showing Image ".$sel." of ".$imagescount );
	while($image = mysql_fetch_array($gallery))
	{
		$imagesarray[] = $image['iid']; 
	}
	$list_output .= '<div class="thumb_gallery">';
	$list_output .="<div class='view_gallery'><a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".($start-2)."&sel=".$sel)."'> << </a></div>";
	for($i=$start; $i<=($start+2);$i++ )
	{
		$list_output .="<center>";	
		
		if($imagesarray[$i])
		{
			$list_output .="<a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".$start."&sel=".$i)."'><div class='view_gallery1' style='background:url(https://www.cinealerts.com/galleries/thumbs/".$imagesarray[$i].".jpg )no-repeat center center;' class='thumb' alt='".$gallerydetails['gname']."'></div></a>";
		}
		$list_output .="</center>";
	}	
	if($start+2 < $imagescount)
	{
		$list_output .="<div class='view_gallery2'><a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".($start+2)."&sel=".$sel)."'> >> </a></div>";
	}	
	$next = (($sel+1)<$imagescount)?($sel+1):0;
	$list_output .= "</div>";
	$list_output .= "<div class='latest_gallery5'><div class='latest_gallery4'>Image ".($sel+1)." of ".$imagescount."</div>";

	$list_output .="<div class='social_share'><div class='width1'><a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".$start."&sel=".($sel-1))."'>< Previous </a></div><div class='latest_gallery4'><a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".$start."&sel=".$next)."'> Next ></a></div></div>";
	$list_output .= "<div class='view_gal_img'><img src='".set_url("galleries/".$imagesarray[$sel].".jpg")."' maxwidth='450' alt='".$gallerydetails['gname']."'  /></div>";
	$imgurl=set_url("galleries/".$imagesarray[$sel].".jpg");
	$title = $gallerydetails['gname'];
	fb_image($imgurl, $title);
	$list_output .="<div class='social_share'><div class='width1'><a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".$start."&sel=".($sel-1))."'>< Previous </a></div><div class='latest_gallery4'><a href='".set_url('index.php?gallery='.$gallerydetails['gname'].'&gtype='.$gallerytypes[$gallerydetails['gtype']]."&start=".$start."&sel=".$next)."'> Next ></a></div></div>";

	
	$list_output .="</div>";	
	
	if(trim($gallerydetails['description']))
	{
	$list_output .='<div class="latest_gallery5">';
		$list_output .= trim($gallerydetails['description'])."<br/><br/>";
$list_output .='</div>';
		set_meta_data(trim($gallerydetails['description']), trim($gallerydetails['keywoards']));
	}
	else
	{
		header("Location:https://www.cinealerts.com/404.shtml");
		exit;
		$list_output = listall_galleries($start);
	}
	
	$list_output .=gallery_comments($gid);
	return $list_output;
}

function latest_galleries()
{
	global $db, $gallerytypes;
	$list_output .= '<div class="gal" >';	
	$list_output .= '<h2>Latest Photo Galleries</h2><div class="bar1">';

	$query = "select * from galleries where status='1' order by cdate DESC LIMIT 0, 6";
	$galleries = mysql_query($query);
	while($gallery = mysql_fetch_array($galleries))
	{
		$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
		if($iid)
		{
			$list_output .="<div class='latest_gallery' >";
			$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytypes[$gallery['gtype']])."'><img src='".set_url('galleries/'.$iid.'.jpg')."' class='gthumb' alt='".$gallery['gname']."' border='0' width='180' /><br /><div class='gname' >".$gallery['gname']."</div></a>";
			$list_output .="</div>";		
			$i++;
		}
		if($i==3)
		{
			$list_output .="</div><div class='bar1' >";
		}
	}

	$list_output .="</div></div>";		
	return $list_output;
}

function featured_galleries()
{
	global $db, $gallerytypes;
	$list_output .= '<div class="latest">';	
	$list_output .= '<h2>Hot Galleries</h2>';

	$query = "select * from galleries where featured='1' and status='1' order by cdate DESC LIMIT 0, 4";
	$galleries = mysql_query($query);
	while($gallery = @mysql_fetch_array($galleries))
	{
		$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
		if($iid)
		{
			$list_output .="<div>";
			$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytypes['gtype'])."'><img src='".set_url('galleries/thumbs/'.$iid.'.jpg')."' width='120' class='thumb' alt='".$gallery['gname']."' border='0' /><br />".$gallery['gname']."</a>";
			$list_output .="</div>";
			$i++;		
		}
		if($i%2 == 0)
		$list_output .="<div class='clear'></div>";

	}
	
	$list_output .="</div>";		
	return $list_output;
}

function latest_events()
{
	global $db, $gallerytypes;
	$list_output .= '<table width="100%" cellspacing="0" cellpadding="2" class="bar">';	
	$list_output .= '<tr><th colspan="5"><h4>Latest Events</h4></th></tr><tr>';
	
	$query = "select * from galleries where gtype='2'  and status='1' order by cdate DESC LIMIT 0,2";
	$galleries = mysql_query($query);
	while($gallery = mysql_fetch_array($galleries))
	{
		$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
		if($iid)
		{
			$list_output .="<td width='".(100/2)."%' align='center'>";
			$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytypes[$gallery['gtype']])."'><img src='".set_url('galleries/thumbs/'.$iid.'.jpg')."' alt='".$gallery['gname']."' class='thumb' border='0' /><br />".$gallery['gname']."</a>";
			$list_output .="</td>";		
			$i++;
		}
		if($i==2)
		{
			$list_output .="</tr><tr>";
		}	
	}
	$list_output .="</tr></table>";		
	return $list_output;
}

function watermark($SourceFile, $DestinationFile, $font_size = 14 )
{
	   $WaterMarkText = "CineAlerts.com";
	   list($width, $height) = getimagesize($SourceFile);
	   $image_p = imagecreatetruecolor($width, $height);
	   $image = imagecreatefromjpeg($SourceFile);
	   imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
	   $black = imagecolorallocate($image_p, 255, 255, 255);
	   $font = '../includes/monofont.ttf';
	   imagettftext($image_p, $font_size, 0, 5, ($height/2), $black, $font, $WaterMarkText);
	   imagettftext($image_p, $font_size, 0, ($width-120), ($height-20), $black, $font, $WaterMarkText);
	   if ($DestinationFile<>'') {
	      imagejpeg ($image_p, $DestinationFile, 100);
	   } 
}



function search_galleries($keywords)
{
	global $db;
	$keys = str_replace(',','%" or keywords like "%', $keywords);		
	$query = 'select * from galleries where status="1" and (keywords like "%'.$keys.'%") order by cdate DESC LIMIT 0,20';
	$galleries = mysql_query($query);
	while($gallery = @mysql_fetch_array($galleries))
	{
		$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
		if($iid)
		{
			$list_output .="<div class='img_block'>";
			$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytype)."'><img src='".set_url('galleries/thumbs/'.$iid.'.jpg')."' class='thumb' alt='".$gallery['gname']."' border='0' /><br />".$gallery['gname']."</a>";
			$list_output .="</div>";		
			$i++;
			if($i%4 ==0)
			{
				$list_output .="<div class='clear'></div>";
			}

		}	
	}
	
	return $list_output;	
}

function related_galleries($keywords, $rlimit = 2)
{
	global $db;
	$keys = str_replace(',','%" or keywords like "%', $keywords);		
	$query = 'select * from galleries where status="1" and (keywords like "%'.$keys.'%") order by cdate DESC LIMIT 0, '.$rlimit;
	$galleries = mysql_query($query);
	if(mysql_num_rows($galleries) == 0)
	{
		$query = "select * from galleries where status='1' order by cdate DESC LIMIT 0, ".$rlimit;
		$galleries = mysql_query($query);
	}
	while($gallery = @mysql_fetch_array($galleries))
	{
		$iid = get_any("galleryimages", $gallery['gid'], "iid","gid");
		if($iid)
		{
			$list_output .="<a href='".set_url('index.php?gallery='.$gallery['gname'].'&gtype='.$gallerytype)."'><img src='".set_url('galleries/thumbs/'.$iid.'.jpg')."' class='thumb' alt='".$gallery['gname']."' border='0' /><br />".$gallery['gname']."</a>";	
			$i++;
		}	
		$list_output .="<br/>";
	}
	return $list_output;	
}
?>