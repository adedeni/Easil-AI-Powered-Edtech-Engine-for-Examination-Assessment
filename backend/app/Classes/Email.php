<?php

// Manually include the PHPMailer library files
// Make sure to adjust the path if you placed them elsewhere
require_once APPROOT . '/app/PHPMailer/Exception.php';
require_once APPROOT . '/app/PHPMailer/PHPMailer.php';
require_once APPROOT . '/app/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Email {
    private $_config;

    public function __construct() {
        // Load the mail configuration
        $this->_config = include(APPROOT . '/app/Config/mail.php');
    }

    public function sendWelcomeEmail($toEmail, $toName, $username, $password) {
        $subject = "Welcome to EASIL!";
        $body = "Hi $toName,<br><br>"
              . "Your account has been created on the EASIL platform.<br>"
              . "Your username is: <strong>$username</strong><br>"
              . "Your temporary password is: <strong>$password</strong><br><br>"
              . "Please log in and change your password immediately.<br>"
              . "Thank you,<br>The EASIL Team";

        return $this->sendEmail($toEmail, $toName, $subject, $body);
    }
    
    public function sendPasswordResetEmail($toEmail, $toName, $newPassword) {
        $subject = "Your EASIL Password Has Been Reset";
        $body = "Hi $toName,<br><br>"
              . "Your password has been reset by an administrator.<br>"
              . "Your new temporary password is: <strong>$newPassword</strong><br><br>"
              . "Please log in and change your password as soon as possible.<br>"
              . "Thank you,<br>The EASIL Team";

        return $this->sendEmail($toEmail, $toName, $subject, $body);
    }
    
    // Generic function to send an email
    private function sendEmail($toEmail, $toName, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0; // Set to 2 for debug output
            $mail->isSMTP();
            $mail->Host       = $this->_config['smtp']['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->_config['smtp']['username'];
            $mail->Password   = $this->_config['smtp']['password'];
            $mail->SMTPSecure = $this->_config['smtp']['encryption'];
            $mail->Port       = $this->_config['smtp']['port'];

            //Recipients
            $mail->setFrom($this->_config['smtp']['from_email'], $this->_config['smtp']['from_name']);
            $mail->addAddress($toEmail, $toName);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Email could not be sent to $toEmail. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}