<?php
// Include PHPMailer files
require './src/PHPMailer.php';
require './src/SMTP.php';
require './src/Exception.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$env = parse_ini_file(__DIR__ . '/.env');

// Function to send email
function sendEmail($toEmail, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'asil.infoseek@gmail.com';
        $mail->Password = 'tkqmrgkufgvxrgfe';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $toEmail = filter_var($toEmail, FILTER_SANITIZE_EMAIL);
        $mail->setFrom('asil.infoseek@gmail.com', 'Asil');
        $mail->addAddress('asil.infoseek@gmail.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
