<?php
  $path = '/home/cinealer/public_html/cache';
  if ($handle = opendir($path)) {
	$i=0;
    while (false !== ($file = readdir($handle))) {
        if ((time()-filectime($path.'/'.$file)) > 1800) {  // 86400 = 60*60*24
          if (strripos($file, '.html') !== false) {
            unlink($path.'/'.$file) or die("Can not delete.");
          }
        }
    }
  }
?>
