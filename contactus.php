<?php
@session_start();
if( isset($_POST['submit'])) {
   if( $_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'] ) ) {
   
		 
		 if($_REQUEST['task'] == 'mail' ) { 
		
		
			 									
					 // mailing  start
					
					$to = 'pbvkrishna@yahoo.com';
 
					
					$subject  = 'Auto Generated mail, Don’t Reply.';
					$message = '<span style="font-family:Arial, Helvetica, sans-serif;  font-size:12px;">
					<p>
					<b>
					Dear  !<br><br>
		        	You Got A Message 
					Here are the details...</b>
					</p><p>
					 
					<b>Name</b>               : '.$_POST['name'].' <br>
					<b>Company Name</b>       : '.$_POST['company'].' <br>
					<b>Phone</b>      		  : '.$_POST['phone'].' <br>
					<b>Email:        </b>     : '.$_POST['email'].'  <br>
					<b>Comments    </b>       : '.$_POST['comments'].'  <br>
					
					 <br><br><br>
					 <b>
					 Warm Regards,<br>
					 Cinealerts Administrator.
					 </b>
					 </p>
					 
					 
					</span>';
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: contact@cinealerts.com' . "\r\n" ;
							   'Reply-To: '.$_POST['email']. "\r\n" .
							   'X-Mailer: PHP/' . phpversion();
					mail($to, $subject, $message, $headers);
					// mailing ends 
			 		$msg = "<p style='text-align:center; color:#000000;'> <b>Your message has been Sucessfully Posted. <br /> We will contact you very soon.</b></p>	";	
						
					}			
			

		unset($_SESSION['security_code']);
   } else {
		// Insert your code for showing an error message here
		$msg =  'Sorry, you have provided an invalid security code';
		$stask = 1;
   }
} 	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Advertise in cinealerts</title>
<link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
<meta name="description" content="contact for advertising in cinema alerts (cinealerts.com)" />
<meta name="keywords" content="advertise, cinealerts, movies, sms, alerts, email" />
<link href="includes/style.css" rel="stylesheet" />
<script type="text/javascript" >
						function valid()
						{
						if(document.contactus.name.value == '' )
						{
						alert(" PLease enter your Name " );
						return false ; 
						}
						if(document.contactus.company.value == '' )
						{
						alert(" PLease enter your Company Name " );
						return false ; 
						}if(document.contactus.phone.value == '' )
						{
						alert(" PLease enter your Phone number " );
						return false ; 
						}
						if(document.contactus.email.value == '' )
						{
						alert(" PLease enter your Email " );
						return false ; 
						}
						if(document.contactus.comments.value == '' )
						{
						alert(" PLease enter your Comments " );
						return false ; 
						}
						if(document.contactus.security_code.value == '' )
						{
						alert(" PLease enter the Security Code " );
						return false ; 
						}
						else
						return true;
						
						}							
						</script>	
</head>
<body>
<table width="400" cellpadding="0" cellspacing="0" border="0" align="center" class="container">
<tr>
	<td class="top_bar"></td>
</tr>
<tr>
	<td align="right" id="logo_banner">

	</td>
</tr>
<tr>
<td align="center" height="300">
					<?=$msg?>


					<? 	  if($_REQUEST['task'] != 'mail' || $stask==1) { ?>			 
				<form name="contactus" method="post" action="contactus.php?task=mail" onsubmit="return valid();">
				<table border="0" cellpadding="5" cellspacing="0" align="center" width="400" class="container"  >
				<tr>
					<td><b>Name</b></td>
					<td><input type="text" name="name" size="26" value="<?=$_POST['name']?>" /></td>
				</tr>
				<tr>
					<td><b>Company</b></td>
					<td><input type="text" name="company" size="26" value="<?=$_POST['company']?>" /></td>
				</tr>
				<tr>
					<td><b>Phone</b></td>
					<td><input type="text" name="phone" size="26" value="<?=$_POST['phone']?>" /></td>
				</tr>
				<tr>
					<td><b>Email</b></td>
					<td><input type="text" name="email" size="26"  value="<?=$_POST['email']?>" /></td>
				</tr>
				<tr>
					<td><b>Comments</b></td>
					<td><textarea name="comments" rows="5"><?=trim($_POST['company'])?></textarea></td>
				</tr>
				<tr>
					<td><b>Type the code shown</b></td>
					<td><input id="security_code" name="security_code" type="text"  size="26"/></td>
				</tr>
				<tr>
					<td align="right"></td>
					<td align="left"><img src="includes/CaptchaSecurityImages.php?width=150&height=50&characters=6" style="border:1px solid #999999;"/></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="submit" value="Submit" name="submit" /></td>
				</tr>
				</table>
				</form>
				
				<? } ?>	
				
				
</td>
</tr>
<tr>
	<td class="footer"></td>
</tr>
<tr>
	<td class="footer_content">©2010, All Rights Reserved to <a href="http://www.cinealerts.com" title="Cinema Alerts: Cinealerts.com">Cinema Alerts</a><br /><br />
</td>
</tr>
</table>						
						
</body>
</html>
