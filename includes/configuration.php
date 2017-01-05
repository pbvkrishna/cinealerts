<?php
@session_start();
//Datebase Configuration
$host = "localhost";
$dbname = "movies";
$dbuser = "root";
$dbpassword = "";

$host = "localhost";
$dbname = "cinealer_alerts";
$dbuser = "cinealer_calerts";
$dbpassword = "Traceout007";


//Database Connection
$db = @mysql_connect($host,$dbuser,$dbpassword);
@mysql_select_db($dbname, $db);

//Default Page title
define("PAGE_TITLE","Cinema Alerts on movies & theaters of Hyderabad, Bangalore, Visakhapatnam, Vijayawada, Rajahmundry, Kakinada, Nellore, Guntur, Eluru, Warangal, Tirupati, Kurnool, Nizamabad, Kadapa, Anantapur and Karimnagar");
//define("SITE_URL","http://balu/movies/");
//define("SEO_URLS",0);
define("SITE_URL","https://www.cinealerts.com/");
define("SEO_URLS",1);
define("IMG_SITE_URL","https://www.cinealerts.com/");
$admin_email = "Cinema Alerts <webmaster@cinealerts.com>";
include("english.php");
?>