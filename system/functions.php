<?php
namespace App\System;
require 'mailler/PHPMailer.php';
require 'mailler/Exception.php';
require 'mailler/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Functions
{	public function post()
	{
		if ($_POST) {
			return true;
		}
		else {
			return false;
		}	
	}	
	function get()
	{
		if ($_GET) {
			return true;
		}
		else {
			return false;
		}	
	}	
	function p($deger)
	{
		if (isset($_POST[$deger])) {
			return trim($_POST[$deger]);
		}
		return false;	
	}	
	function g($deger)
	{
		if (isset($_GET[$deger])) {
			return trim($_GET[$deger]);
		}
		return false;	
	}	
	function redirect($url, $time = 0)
	{
		if ($time == 0) {
			header("location:$url");
		}
		else {
			$time = (int)$time;
			header("refresh:$time;$url");
		}	
	}	
	function seoUrlOlustur($text)
	{
		$tr = ["Ç", "Ş", "Ğ", "Ü", "İ", "Ö", "ç", "ş", "ğ", "ü", "ı", "ö", "+", "#"];
		$ing = ["C", "S", "G", "U", "I", "O", "c", "s", "g", "u", "i", "o", "", ""];
		$text = strtolower(str_replace($tr, $ing, $text));
		$text = preg_replace("@[\.+]@", "", $text);
		$text = preg_replace("@[^A-Za-z0-9\-_\+]@", " ", $text);
		$text = trim(preg_replace('/\s+/', " ", $text));
		$text = str_replace(" ", "-", $text);
		return $text;	}	
	function pr($dizi)
	{
		echo "<pre>";
		print_r($dizi);
		echo "</pre>";	
	}
	public function base_url($param = "")
	{
		return BASEURL . "" . $param;	}


	public function HTTPStatus($num)
	{
		$http = array(
			100 => 'HTTP/1.1 100 Continue',
			101 => 'HTTP/1.1 101 Switching Protocols',
			200 => 'HTTP/1.1 200 OK',
			201 => 'HTTP/1.1 201 Created',
			202 => 'HTTP/1.1 202 Accepted',
			203 => 'HTTP/1.1 203 Non-Authoritative Information',
			204 => 'HTTP/1.1 204 No Content',
			205 => 'HTTP/1.1 205 Reset Content',
			206 => 'HTTP/1.1 206 Partial Content',
			300 => 'HTTP/1.1 300 Multiple Choices',
			301 => 'HTTP/1.1 301 Moved Permanently',
			302 => 'HTTP/1.1 302 Found',
			303 => 'HTTP/1.1 303 See Other',
			304 => 'HTTP/1.1 304 Not Modified',
			305 => 'HTTP/1.1 305 Use Proxy',
			307 => 'HTTP/1.1 307 Temporary Redirect',
			400 => 'HTTP/1.1 400 Bad Request',
			401 => 'HTTP/1.1 401 Unauthorized',
			402 => 'HTTP/1.1 402 Payment Required',
			403 => 'HTTP/1.1 403 Forbidden',
			404 => 'HTTP/1.1 404 Not Found',
			405 => 'HTTP/1.1 405 Method Not Allowed',
			406 => 'HTTP/1.1 406 Not Acceptable',
			407 => 'HTTP/1.1 407 Proxy Authentication Required',
			408 => 'HTTP/1.1 408 Request Time-out',
			409 => 'HTTP/1.1 409 Conflict',
			410 => 'HTTP/1.1 410 Gone',
			411 => 'HTTP/1.1 411 Length Required',
			412 => 'HTTP/1.1 412 Precondition Failed',
			413 => 'HTTP/1.1 413 Request Entity Too Large',
			414 => 'HTTP/1.1 414 Request-URI Too Large',
			415 => 'HTTP/1.1 415 Unsupported Media Type',
			416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',
			417 => 'HTTP/1.1 417 Expectation Failed',
			500 => 'HTTP/1.1 500 Internal Server Error',
			501 => 'HTTP/1.1 501 Not Implemented',
			502 => 'HTTP/1.1 502 Bad Gateway',
			503 => 'HTTP/1.1 503 Service Unavailable',
			504 => 'HTTP/1.1 504 Gateway Time-out',
			505 => 'HTTP/1.1 505 HTTP Version Not Supported',
		);	
		return $http[$num];
	}

	public function sendMail($mailAdres,$subject,$body,$altbody)
	{
		$mail = new PHPMailer(true);
		$smtp = new SMTP();

		try {
			$mail->SMTPOptions = array(
					'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
					)
				);
			//Server settings
			//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
			$mail->isSMTP();                                            //Send using SMTP
			$mail->Host       = 'srvc81.turhost.com';//'smtp.eschelping.com';                     //Set the SMTP server to send through
			$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
			$mail->Username   = 'admin@kucukaslanmatbaa.com';//'info@eschelping.com';                     //SMTP username
			$mail->Password   = '2aU+?MT8';//'Gm4wzQ4S';                               //SMTP password
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
			$mail->Port       = '465';//587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

			//Recipients
			$mail->setFrom('admin@kucukaslanmatbaa.com', 'Küçükaslan Matbaa');
			$mail->addAddress($mailAdres, '');     //Add a recipient
			//Content
			$mail->isHTML(true);                                  //Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $body;
			$mail->AltBody = $altbody;
			$mail->CharSet = 'UTF-8';

			$mail->send();
			// echo 'Message has been sent';
			return true;
		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
			return false;
			// echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}

	}
	public function sendMailold($mail,$message){
		$mail_to = "info@agsteknik.com";
        
        # Sender Data
        $subject = trim($mail);
                
        $email = filter_var(trim($mail), FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) OR empty($subject) OR empty($message)) {
            # Set a 400 (bad request) response code and exit.
            http_response_code(400);
            echo "Please complete the form and try again.";
            exit;
        }
        
        # Mail Content
		$content = "<HTML><head><title> AGSTEKNİK </title></head><body>";
        $content .= "<br>$message<br>";
		$content .= "</body></html>";

        # email headers.
        // $headers = "From: <$email>";
        $headers = "From: info@agsteknik.com";

        # Send the email.
		// ini_set("SMTP","localhost");
		// ini_set("smtp_port","25");
		// ini_set("sendmail_from","00000@gmail.com");
		// ini_set("sendmail_path", "C:\wamp\bin\sendmail.exe -t");
	 



		ini_set("SMTP","smtp.agsteknik.com");
		ini_set("smtp_port","587");
		ini_set("username","info@agsteknik.com");
		ini_set("password","Nv7aDtmL");
		// ini_set("sendmail_from","info@agsteknik.com");
        $success = mail($mail_to, $subject, $content, $headers);
        if ($success) {
            # Set a 200 (okay) response code.
            http_response_code(200);
            return true;
        } else {
            # Set a 500 (internal server error) response code.
            http_response_code(500);
           return false;
        }
	}

}