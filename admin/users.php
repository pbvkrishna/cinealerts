<?php
@session_start();
include_once("../includes/configuration.php");
sessioncheck(4);
global $msg;
$top_form = '<table width="96%" align="center"><tr><td align="right"><br><a href="cpanel.php?option=users">Add User</a><br><br><form action="cpanel.php?option=users" method="post">Username/Name: <input type="text" name="keyword" value="'.$_REQUEST['keyword'].'" /></span><input type="submit" value="Search" /></form></td></tr><tr><td align="center"></td></table><br>';
switch($_REQUEST['task'])
{
	case 'aprove': aprove($_REQUEST['uid'],$_REQUEST['aprove']);
					break;
	case 'confirm': confirm($_REQUEST['uid'],$_REQUEST['confirm']);
					break;
	case 'mconfirm': mobileconfirm($_REQUEST['uid'],$_REQUEST['mconfirm']);
					break;
	case 'delete': delete($_REQUEST['uid']);
					break;
	case 'update': if(update_user($_POST))
				 {
				 	header("Location:cpanel.php?option=users&msg=Sucessfully Updated");
				 }
				 else
				 {
					if(!mobilecheck($_POST['mobile'], $_POST['uid']))
					{
						$msg = "User already registered with this Mobile no.";
					}
					$output = listall(0);
				 }
				 break;
	case 'save' :if(save_user($_POST))
				 {
				 	header("Location:cpanel.php?option=users&msg=Sucessfully registered");
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
					$output = listall($_REQUEST['start']?$_REQUEST['start']:0);
				 }
				 break;
	default:$output = listall($_REQUEST['start']?$_REQUEST['start']:0);
}

$output = $top_form . $output;


//User Functions

function listall($start = 0)
{
	global $db, $membertypes;
	if($_REQUEST['keyword'])
	{
		$keywords = trim($_REQUEST['keyword']);
		$keys = explode(" ", $keywords);
		foreach($keys as $key)
		{
			$cond[] = ' name like "%'.$key.'%" or username like "%'.$key.'%"';
		}
		
	}
	if($cond)
	{
		$conds = " where ".implode(" or ", $cond) . " ";
	}
	$users = mysql_query('select * from users '.$conds);
	$totalrecords = mysql_num_rows($users);
	
	$orderby = $_REQUEST['orderby']?$_REQUEST['orderby']:"cdate";
	$sort_order = $_REQUEST['sorder']?$_REQUEST['sorder']:"DESC";
	
	$next_order = ($sort_order == "DESC")?"ASC":"DESC";
	
	$users = mysql_query('select * from users '.$conds.' order by '.$orderby.' '.$sort_order.' LIMIT '.$start.', '.LIMIT);

	$list_output .= "<table class='whitetable' width='98%' align='center'><tr><td width='85%' valign='top'>";

	$list_output .= "<table class='contenttable' width='98%' align='center'>";
	
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=username&sorder=".$next_order."'>Username</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=name&sorder=".$next_order."'>Name</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=E-mail&sorder=".$next_order."'>E-Mail</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=user_type&sorder=".$next_order."'>User Type</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=mobile&sorder=".$next_order."'>Mobile</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=city&sorder=".$next_order."'>City</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=confirm&sorder=".$next_order."'>Mail Confirm</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=confirm&sorder=".$next_order."'>Mobile Confirm</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=aprove&sorder=".$next_order."'>Aprove</a></b></th>";
	$list_output .= "<th><b><a href='cpanel.php?option=users&start=".$start."&orderby=cdate&sorder=".$next_order."'>R. On</a></b></th>";
	$list_output .= "<th><b>Actions</b></th></tr>";
	
	while($user = mysql_fetch_array($users))
	{
		$confirm = "<a href='cpanel.php?option=users&task=confirm&uid=".$user['uid']."&start=".$start."&confirm=".($user['confirm']?0:1)."'><img src='images/".$user['confirm'].".png' border='0'></a>";

		if($user['confirm']==0)
		{
			$confirm .= "<a href='http://www.cinealerts.com/index.php?task=confirm&uid=".$user['uid']."&remail=1' target='_blank'>Resend</a>";
		}

		$mconfirm = "<a href='cpanel.php?option=users&task=mconfirm&uid=".$user['uid']."&start=".$start."&mconfirm=".($user['mconfirm']?0:1)."'><img src='images/".$user['mconfirm'].".png' border='0'></a>";
		if($user['mconfirm']==0)
		{
			$mconfirm .= "<a href='http://www.cinealerts.com/index.php?task=confirm&uid=".$user['uid']."&rmobile=1' target='_blank'>Resend</a>";
		}
		$aprove = "<a href='cpanel.php?option=users&task=aprove&uid=".$user['uid']."&start=".$start."&aprove=".($user['aprove']?0:1)."'><img src='images/".$user['aprove'].".png' border='0'></a>";
		
		$list_output .= "<tr><td><a href='cpanel.php?option=users&task=edit&uid=".$user['uid']."'>".$user['username']."</a></td>";
		$list_output .= "<td>".$user['name']."</td>";
		
		$list_output .= "<td>".$user['email']."</td>";
		$list_output .= "<td>".$membertypes[$user['user_type']]."</td>";
		$list_output .= "<td>".$user['mobile']."</td>";
		$list_output .= "<td>".$user['city']."</td>";
		$list_output .= "<td>".$confirm."</td>";
		$list_output .= "<td>".$mconfirm."</td>";
		$list_output .= "<td>".$aprove."</td>";
		$list_output .= "<td>".date("d-m-Y",strtotime($user['cdate']))."</td>";
		$list_output .= "<td><a href='cpanel.php?option=users&task=edit&uid=".$user['uid']."'>Edit</a> - <a href='cpanel.php?option=users&task=delete&start=".$_REQUEST['start']."&uid=".$user['uid']."' onclick='return confirm(\"Do you Want to Delete \")'>Delete</a></td></tr>";
	}
	$list_output .="</table>";
	$list_output .= pagenav($start,"admin/cpanel.php?option=users",$totalrecords);
	$list_output .="</td>";
	$list_output .="<td>";
	if($_REQUEST['task'] == 'edit')
	{
		$edit_user = mysql_fetch_array(mysql_query("select * from users where uid='".$_REQUEST['uid']."'"));
		$list_output .=view_form($edit_user);
	}
	else
	{
		$list_output .=view_form($_POST);
	}
	$list_output .="</td>";
	$list_output .="</tr></table>";
	
	return $list_output;
} 

function view_form($user)
{
	global $membertypes;
	
	$list_output .="<form action='cpanel.php?option=users' method='post'>";
	if($user['uid'])
	{
		$task="update";
		$list_output .="<input type='hidden' name='uid' value='".$user['uid']."'>";
		$list_output .="<input type='hidden' name='username' value='".$user['username']."'>";
	}
	else
	{
		$task = "save";
	}
	$list_output .="<input type='hidden' name='task' value='".$task."'>";
	$list_output .="<table class='whitetable' width='100%' align='center'>";
	if($user['uid'])
	{
		$list_output .="<tr><td>Username:<br/><b>".$user['username']."</b></td></tr>";
		
	}
	else
	{
		$list_output .="<tr><td>Username:<br/><input type='text' name='username' value='".$user['username']."' /></td></tr>";
	}
	$list_output .="<tr><td>Password:<br/><input type='password' name='password' value='' /></td></tr>";
	$list_output .="<tr><td>Re Type Password:<br/><input type='password' name='rpassword' value='' /></td></tr>";
	$list_output .="<tr><td>Name:<br/><input type='text' name='name' value='".$user['name']."' /></td></tr>";
	$list_output .="<tr><td>E-Mail:<br/><input type='text' name='email' value='".$user['email']."' /></td></tr>";
	$list_output .="<tr><td>Mobile No.:<br/><input type='text' name='mobile' value='".$user['mobile']."' /></td></tr>";
	$list_output .="<tr><td>City:<br/><select name='city'> ".get_allcities($user['city'], "../includes/")." </select></td></tr>";
	if($user['confirm'])
	{
		$confirm_checked = " checked='checked' ";
	}
	if($user['aprove'])
	{
		$aprove_checked = " checked='checked' ";
	}
	
	$list_output .="<tr><td><input type='checkbox' name='confirm' value='1' ".$confirm_checked." /> Confirm <input type='checkbox' name='aprove' value='1' ".$aprove_checked." /> Aprove</td></tr>";
	$list_output .="<tr><td>User Type:<br/><select name='user_type'>";
	foreach($membertypes as $key => $value)
	{
		if($user['user_type'] == $key)
		{
			$selected = " selected='selected' ";
		}
		else
		{
			$selected = "";
		}
		$list_output .="<option value='".$key."' ".$selected.">".$value."</option>";
	}
	$list_output .="</select></td></tr>";
	$list_output .="<tr><td align='center'><input type='Submit' value='Save' /></td></tr>";
	$list_output .="</table></form>";
	return $list_output;
}

function save_user($user)
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
	
	$admin_aprove = ADMIN_APROVE?0:1;
	$email_verification = EMAIL_VERIFICATION?0:1;
	
	
	$code = md5($_POST['username']).rand(5000,1000);
	$query = "insert into users(username, password, name, email, mobile, city, confirm, aprove, user_type,activationcode) values('".$_POST['username']."', '".md5($_POST['password'])."', '".$_POST['name']."','".$_POST['email']."','".$_POST['mobile']."','".$_POST['city']."', '".$email_verification."','".$admin_aprove."','".$_POST['user_type']."', '".$code."')";
	
	$result=mysql_query($query);
	if($result)
	{
			$header="";
			
			if(EMAIL_VERIFICATION)
			{
				//Activation Link
				$message="";
			}
			else
			{
				$message="";
			}
			//mail($_REQUEST['email'],'Secessfully Registered..', $message,$header);
			
			
			/////////////////////////////////
			
			$header="";
			$message="";
			//mail('Administrator','Registration Pending Approval.', $message,$header);
		
		
		return true;
	}
	else
	{
		return false;
	}

	
}

function update_user($user)
{
	global $db;
	
	if(!mobilecheck($_POST['mobile'], $_POST['uid']))
	{
		return false;
	}
	
	if(trim($_POST['password']))
	{
		$pass = " , password= '".md5($_POST['password'])."'";
	}
	
	$query = "update users set name='".$_POST['name']."' ".$pass.", email= '".$_POST['email']."', mobile = '".$_POST['mobile']."', city='".$_POST['city']."', confirm = '".$_POST['confirm']."', aprove = '".$_POST['aprove']."', user_type = '".$_POST['user_type']."' where uid='".$_POST['uid']."'";
		
	$result=mysql_query($query);
	if($result)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}

function confirm($uid, $confirm)
{
	global $db;
	$query = "update users set confirm='".$confirm."' where uid='".$uid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=users&msg=Confirm Satus Updated!");
}

function mobileconfirm($uid, $confirm)
{
	global $db;
	$query = "update users set mconfirm='".$confirm."' where uid='".$uid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=users&msg=Mobile Confirm Satus Updated!");
}


function aprove($uid, $aprove)
{
	global $db;
	$query = "update users set aprove='".$aprove."' where uid='".$uid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=users&msg=Aprove Satus Updated!");
}

function delete($uid)
{
	global $db;
	$query = "delete from users where uid='".$uid."'";
	mysql_query($query);
	header("Location:cpanel.php?option=users&msg=User Deleted!");
}
?>
