
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Text a call number</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<style>
body{padding: 15px;}

</style>

  </head>
  <body>


<?php

//var_dump($_GET);

if (isset($_POST["sendtext"])){

	processText();


}
else{

form();
}



?>


 
	<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
  </body>
</html>









<?php



function form(){

?>


<div id="sms" style="visibility: visible; display: block;">
<div id="smstop">Send the title, location, call number and permalink of this item to your mobile device</div>
<div id="smsmain">
<div id="smsinput">
<form name="sms_form" method="post" action="">
<p>
<b>Title</b>:<?= $_GET["title"] ?></p>
<input type="hidden" name="title" value="<?= $_GET["title"] ?>">
<input type="hidden" name="record_id" value="CP71115076560001451">
<input type="hidden" name="sendtext" value="true">
<p style="padding:5px 0;">
<b>Enter your cell phone #</b>: <input name="phone" type="text"></p>
<p style="padding:5px 0;"><b>Select your provider:</b> 
<select name="provider">
	<option value="cingular">AT&amp;T</option>
	<option value="cricket">Cricket</option>
	<option value="sprint">Sprint</option>
	<option value="tmobile">T-Mobile</option>
	<option value="verizon">Verizon</option>
	<option value="virgin">Virgin</option>
	</select>
</p>
	<p></p>
	<ol>
	<li style="margin-left:30px; margin-bottom:10px;">
	<input checked="1" type="radio" name="loc" value="<?= $_GET["availability"] ?>"> <?= $_GET["availability"] ?></li>
	</ol>
	<p style="padding:5px 0;"><strong>NOTE:</strong> Carrier charges may apply if your cell phone service plan does not include free text messaging.</p>
	<p style="margin: 10px 0; text-align:center;">
	
	<input type="submit" class="btn btn-info" value="Submit Button">
	
	<!--<a href="#here" id="sendmessage" class="smsbutton" onclick="sendSMS();return false;">Send Text</a>&nbsp;&nbsp;<a href="#here" class="smsbutton" id="clearmessage" onclick="clearsms();return false;">Cancel</a>-->
	</p>
	</form>
	</div>

	</div>
	</div> 


<?php

}


function processText(){

	//echo "send text!!";
	$title=$_POST["title"];
	$phone=$_POST["phone"];
	$provider=$_POST["provider"];
	$loc=$_POST["loc"];

//phpinfo();

//var_dump($_POST);
	sendtext();




}

function sendtext(){

	require_once("config.php");

require_once("Mail.php");
require_once("Mail/mime.php");

	$title			= trim($_POST['title']);
	$phoneNumber 	= trim($_POST['phone']);
	$provider 		= trim($_POST['provider']);
	$item 			= $_POST['loc']; //parse the item

	// sanitize location string
	$location		= str_replace("&nbsp;"," ",$item);
	$location		= trim(str_replace(LOCATION_STRING_PREFIX," ",$location));
	//$location 		= preg_replace('/[^A-Za-z0-9\-_()\.\s]/', "", $location);

	// extract call number
	$callNumber_pos = strrpos($location,"(");
	$callNumber 	= trim(substr($location,$callNumber_pos+1,-1));

	// set full location
	$location 		= "\nLoc: ".trim(substr($location,0,$callNumber_pos));

	//defined variables. Set the from address and subject as desired
	$subject 		= SMS_MESSAGE_SUBJECT;

	$providers = array(	'cingular' 	=> '@txt.att.net',
						'tmobile' 	=> '@tmomail.net',
						'virgin' 	=> '@vmobl.com',
						'sprint' 	=> '@messaging.sprintpcs.com',
						'nextel' 	=> '@messaging.nextel.com',
						'verizon'	=> '@vtext.com',
						'cricket'	=> '@mms.mycricket.com',
						'qwest'		=> '@qwestmp.com');

	//remove any non-numeric characters from the phone number
	$number = preg_replace('/[^\d]/', '', $phoneNumber);

	if(strlen($phoneNumber) == 10) { //does the phone have 10 digits

		if($providers[$provider])
		{
			//Format the email.
			$recipient = $number.$providers[$provider];
			$from = SMS_MESSAGE_FROM;
			$reply_to = $from;

			//send the email
			$headers = array('To'=>$recipient,'From'=> $from,'Return-Path'=> $reply_to,'Subject'=> $subject);


			$html_body = "\nTitle: $title $location \nCall Num: $callNumber";
			$body = strip_tags(nl2br($html_body));
		
			$mime = new Mail_mime(PHP_EOL);
			$mime->setTXTBody($body);
			$mime->setHTMLBody($html_body);
			$body = $mime->get();
			$headers = $mime->headers($headers);

			// smtp parameters
			$params['host'] = SMTP_HOST;

			// Create the mail object using the Mail::factory method
			$mail =& Mail::factory('smtp', $params);
			if(EMAIL_ON)
				$mail->send($recipient, $headers,  $body);
		
			if(LOG_USAGE)
			{
				if(is_writable(LOG_FILE))
				{
					$handle = fopen(LOG_FILE, 'a');
					$entry = date('m/d/Y g:i:sa',strtotime('now')) . " | " . $_SERVER["REMOTE_ADDR"] . " | " . $provider . " | " . $providers[$provider] . " | " . $callNumber . " | " . $location . " | " . $title . "\n";
					fwrite($handle, $entry);
					fclose($handle);
				}
			}

			echo "alert('Message sent!');";
			//echo "clearsms();";
			exit;
		}
}






}







/*
require_once("config.php");
require_once("Mail.php");
require_once("Mail/mime.php");

//form variables
$title			= trim($_GET['title']);
$phoneNumber 	= trim($_GET['number']);
$provider 		= trim($_GET['provider']);
$item 			= $_GET['item']; //parse the item

// sanitize location string
$location		= str_replace("&nbsp;"," ",$item);
$location		= trim(str_replace(LOCATION_STRING_PREFIX," ",$location));
//$location 		= preg_replace('/[^A-Za-z0-9\-_()\.\s]/', "", $location);

// extract call number
$callNumber_pos = strrpos($location,"(");
$callNumber 	= trim(substr($location,$callNumber_pos+1,-1));

// set full location
$location 		= "\nLoc: ".trim(substr($location,0,$callNumber_pos));

//defined variables. Set the from address and subject as desired
$subject 		= SMS_MESSAGE_SUBJECT;

$providers = array(	'cingular' 	=> '@txt.att.net',
             		'tmobile' 	=> '@tmomail.net',
             		'virgin' 	=> '@vmobl.com',
             		'sprint' 	=> '@messaging.sprintpcs.com',
             		'nextel' 	=> '@messaging.nextel.com',
             		'verizon'	=> '@vtext.com',
					'cricket'	=> '@mms.mycricket.com',
					'qwest'		=> '@qwestmp.com');

//remove any non-numeric characters from the phone number
$number = preg_replace('/[^\d]/', '', $phoneNumber);

if(strlen($phoneNumber) == 10) { //does the phone have 10 digits

	if($providers[$provider])
	{
		//Format the email.
		$recipient = $number.$providers[$provider];
		$from = SMS_MESSAGE_FROM;
		$reply_to = $from;

		//send the email
		$headers = array('To'=>$recipient,'From'=> $from,'Return-Path'=> $reply_to,'Subject'=> $subject);


		$html_body = "\nTitle: $title $location \nCall Num: $callNumber";
		$body = strip_tags(nl2br($html_body));
		
		$mime = new Mail_mime(PHP_EOL);
		$mime->setTXTBody($body);
		$mime->setHTMLBody($html_body);
		$body = $mime->get();
		$headers = $mime->headers($headers);

		// smtp parameters
		$params['host'] = SMTP_HOST;

		// Create the mail object using the Mail::factory method
		$mail =& Mail::factory('smtp', $params);
		if(EMAIL_ON)
			$mail->send($recipient, $headers,  $body);
		
		if(LOG_USAGE)
		{
			if(is_writable(LOG_FILE))
			{
				$handle = fopen(LOG_FILE, 'a');
				$entry = date('m/d/Y g:i:sa',strtotime('now')) . " | " . $_SERVER["REMOTE_ADDR"] . " | " . $provider . " | " . $providers[$provider] . " | " . $callNumber . " | " . $location . " | " . $title . "\n";
				fwrite($handle, $entry);
				fclose($handle);
			}
		}

		echo "alert('Message sent!');";
		echo "clearsms();";
		exit;
	}
}

echo "alert('ERROR: Message not sent!!');";
*/
?>