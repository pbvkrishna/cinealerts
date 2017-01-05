<?php
include("../includes/configuration.php");
include("../includes/functions.php");
if($_REQUEST['username'])
{
	if(usernameavailability($_REQUEST['username']))
	{
		echo "Avilable";
	}
	else
	{
		echo "Unavilable";
	}
}
if($_REQUEST['mobile'])
{
	if(get_any("users",$_REQUEST['mobile'], "mobile", "mobile"))
	{
		echo "Mobile already registered";
	}
}

if($_REQUEST['email'])
{
	if(get_any("users",$_REQUEST['email'], "email", "email"))
	{
		echo "E-Mail ID already registered";
	}
}
?>