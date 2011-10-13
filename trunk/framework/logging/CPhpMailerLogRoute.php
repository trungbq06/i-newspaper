<?php

/**
* Use PHPMailer to send mail
*/
require_once 'class.phpmailer.php';

class CPhpMailerLogRoute extends CEmailLogRoute
{
	private $_password=null;
	
	private $_userName=null;
	
	private $_host=null;
	
	protected function setUserName($value) {
		$this->_userName = $value;
	}
	
	protected function setPassword($value) {
		$this->_password = $value;
	}
	
	protected function setHost($value) {
		$this->_host = $value;
	}
	
	protected function getPassword() {
		return $this->_password;
	}
	
	protected function getUserName() {
		return $this->_userName;
	}
	
	protected function getHost() {
		return $this->_host;
	}

    protected function sendEmail($email, $subject, $message)
    {
        $mail = new phpmailer();
        $mail->IsSMTP();
        $mail->Host = $this->getHost();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Username = $this->getUserName();
        $mail->Password = $this->getPassword(); //best to keep this in your config file
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->addAddress($email);
        $mail->send();
    }
}