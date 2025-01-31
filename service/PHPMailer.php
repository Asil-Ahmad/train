<?php
// Include PHPMailer files
// require '../phpmailer/src/PHPMailer.php';
// require '../phpmailer/src/SMTP.php';
// require '../phpmailer/src/Exception.php';

// // Function to send email
// function sendEmail($toEmail, $subject, $body)
// {
//     $mail = new PHPMailer(true);

//     try {
//         // Server settings
//         $mail->isSMTP();
//         $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
//         $mail->SMTPAuth = true;
//         $mail->Username = 'your-email@example.com'; // Replace with your email
//         $mail->Password = 'your-email-password'; // Replace with your email password
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port = 587;

//         // Recipients
//         $mail->setFrom('your-email@example.com', 'Your Name'); // Replace with your email and name
//         $mail->addAddress($toEmail);

//         // Content
//         $mail->isHTML(true);
//         $mail->Subject = $subject;
//         $mail->Body    = $body;

//         $mail->send();
//         return true;
//     } catch (Exception $e) {
//         error_log("Mailer Error: {$mail->ErrorInfo}");
//         return false;
//     }
// }
