<?php

var_dump($_GET);
exit();

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