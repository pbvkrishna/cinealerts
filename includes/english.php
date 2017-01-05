<?php
define('LIMIT',10);
define('ADMIN_APROVE',0);
define('EMAIL_VERIFICATION',1);
define('MOBILE_VERIFICATION',0);
define('THUMB_WIDTH',120);
define('THUMB_HEIGHT',120);
define('GALLERY_THUMB_WIDTH',180);
define('GALLERY_THUMB_HEIGHT',200);

$membertypes = array("0" => "Customer", "1" => "Content Writer", "2" => "Marketing", "3" => "City Administrator", "4" => "Administrator");
$languagetypes = array("0" => "Telugu", "1" => "Hindi", "2" => "English", "3" => "Tamil", "4" => "Kannada", "5" => "Malayalam", "6" => "Punjabi", "7" => "Bengali");
$gallerytypes = array("0" => "Actress", "1" => "Actors", "2" => "Events", "3" => "Movies",  "4" => "Other");
//$movie_alert_types = array("1"=> "SMS me comments posted on this movie", "2" => "E-mail me comments posted on this movie");
//$theater_alert_types = array("3"=>"SMS me movie changes in this theater", "4"=>"SMS me the comments posted on this theater", "5"=>"E-Mail me movie changes in this theater", "6"=>"E-Mail me the comments posted on this theater");
$movie_alert_types = array("2" => "E-mail me comments posted on this movie");
$theater_alert_types = array("5"=>"E-Mail me movie changes in this theater", "6"=>"E-Mail me the comments posted on this theater");
?>
