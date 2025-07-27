<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/vendor/autoload.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendPlainTextEmail( $subject, $body , $recipientEmail = 'trent.com2025@gmail.com', $recipientName = 'Trent')
{
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'SMTP2.Bmail.linkdatacenter.net';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@Trent.com.eg';
        $mail->Password   = 'Trent@Info2025';
        $mail->CharSet = 'UTF-8'; // Force UTF-8 encoding
        $mail->Encoding = 'base64'; // Optional but helps with complex characters
     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL/TLS
        $mail->Port       = 465;

        // Sender
        $mail->setFrom('info@Trent.com.eg', 'Trent Company');
        $mail->addAddress($recipientEmail, $recipientName);

        // Email Content (Plain Text)
        $mail->isHTML(false); // Disable HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        if (!$mail->send()) {
            return false;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}
