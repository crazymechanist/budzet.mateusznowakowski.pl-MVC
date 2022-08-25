<?php
	
namespace App;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

	/**
	* Mail
	*
	* PHP version 7.0
	*/
class Mail{
	
	/**
	* Send a message 
	*
	* @param string $to Recipient
	* @param string $subject Subject
	* @param string $text Text-only content of the message
	* @param string @html HTML content of the message
	*
	* @return mixed
	*/
	public static function send($to , $subject , $text , $html){
		//Create an instance; passing `true` enables exceptions
		$from_name = 'Serwis';
		$mail = new PHPMailer(true);
		
		try {
			//Server settings
			$mail->SMTPDebug	=	0;										// Enable verbose debug output
			$mail->isSMTP();												// Send using SMTP
			$mail->Host			=	Config::SMTP_SERVER_ADRESS; 			// SMTP host
			$mail->Port			=	Config::SMTP_SERVER_PORT;				// Gmail SMTP port, TCP port to connect to; use 587 if you have set 
																			//SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
			$mail->SMTPAuth		=	true;									// Enable SMTP authentication
			$mail->Username		=	Config::MAIL_ADDRESS;					// SMTP username
			$mail->Password		=	Config::MAIL_PASSWORD;					// SMTP password
			$mail->SMTPSecure	=	'tls';									//Enable implicit TLS encryption
			
			//Recipients
			$mail->setFrom(Config::MAIL_ADDRESS, $from_name);
			$mail->addAddress($to);											//Name is optional
			
			
			//Content
			$mail->isHTML(true);											//Set email format to HTML
			$mail->Subject		=	$subject;
			$mail->Body			=	$html;
			$mail->AltBody		=	$text;
			
			$mail->send();
			echo 'Message has been sent';
			} catch (Exception $e) {
			echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
	}
}
