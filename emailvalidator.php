<html>
<body>
<form action="emailvalidator.php" method="POST">
<b>E-mail</b> <input type="text" name="email">
<input type="submit">
</form>
<?php
/* Validate an email address. */
function jValidateEmailUsingSMTP($sToEmail, $sFromDomain = "cinealerts.com", $sFromEmail = "webmaster@cinealerts.com", $bIsDebug = false) {

    $bIsValid = true; // assume the address is valid by default..
    $aEmailParts = explode("@", $sToEmail); // extract the user/domain..
    getmxrr($aEmailParts[1], $aMatches); // get the mx records..

    if (sizeof($aMatches) == 0) {
        return false; // no mx records..
    }

    foreach ($aMatches as $oValue) {

        if ($bIsValid && !isset($sResponseCode)) {

            // open the connection..
            $oConnection = @fsockopen($oValue, 25, $errno, $errstr, 30);
            $oResponse = @fgets($oConnection);

            if (!$oConnection) {

                $aConnectionLog['Connection'] = "ERROR";
                $aConnectionLog['ConnectionResponse'] = $errstr;
                $bIsValid = false; // unable to connect..

            } else {

                $aConnectionLog['Connection'] = "SUCCESS";
                $aConnectionLog['ConnectionResponse'] = $errstr;
                $bIsValid = true; // so far so good..

            }

            if (!$bIsValid) {

                if ($bIsDebug) print_r($aConnectionLog);
                return false;

            }

            // say hello to the server..
            fputs($oConnection, "HELO $sFromDomain\r\n");
            $oResponse = fgets($oConnection);
            $aConnectionLog['HELO'] = $oResponse;

            // send the email from..
            fputs($oConnection, "MAIL FROM: <$sFromEmail>\r\n");
            $oResponse = fgets($oConnection);
            $aConnectionLog['MailFromResponse'] = $oResponse;

            // send the email to..
            fputs($oConnection, "RCPT TO: <$sToEmail>\r\n");
            $oResponse = fgets($oConnection);
            $aConnectionLog['MailToResponse'] = $oResponse;

            // get the response code..
            $sResponseCode = substr($aConnectionLog['MailToResponse'], 0, 3);
            $sBaseResponseCode = substr($sResponseCode, 0, 1);

            // say goodbye..
            fputs($oConnection,"QUIT\r\n");
            $oResponse = fgets($oConnection);

            // get the quit code and response..
            $aConnectionLog['QuitResponse'] = $oResponse;
            $aConnectionLog['QuitCode'] = substr($oResponse, 0, 3);

            if ($sBaseResponseCode == "5") {
                $bIsValid = false; // the address is not valid..
            }

            // close the connection..
            @fclose($oConnection);

        }

    }

    if ($bIsDebug) {
        print_r($aConnectionLog); // output debug info..
    }

    return $bIsValid;

}

function domain_exists($email, $record = 'MX'){
	list($user, $domain) = explode('@', $email);
	return checkdnsrr($domain, $record);
}
if($_POST['email'])
{
$email = $_POST['email'];

$bIsEmailValid = jValidateEmailUsingSMTP("$email", "cinealerts.com", "webmaster@cinealerts.com");
echo $bIsEmailValid ? "<b>Valid!</b>" : "Invalid! :(";

if(domain_exists($_POST['email'])) {
	echo('This MX records exists; I will accept this email as valid.');
}
else {
	echo('No MX record exists;  Invalid email.');
}
}


if($_REQUEST['check']="all")
{
include("includes/configuration.php");
$query = "select email from users LIMIT ".$_REQUEST['start'].", ".$_REQUEST['count'];
$emails_res = mysql_query($query);
while($emails = mysql_fetch_array($emails_res))
{
$email = $emails['email'];

$bIsEmailValid = jValidateEmailUsingSMTP("$email", "gmail.com", "email@gmail.com");


if(!domain_exists($email) || !$bIsEmailValid ) {
mysql_query("update users set emailcheck = 0 where email = '".$email."'");
$out .= $email." Invalid" ;

}
}
echo $out."<br/>";

}
?>
</body>
</html>
