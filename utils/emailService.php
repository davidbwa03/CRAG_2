<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Make sure Composer autoload is included
require __DIR__ . '/../vendor/autoload.php'; // Adjust path if needed

function sendEmail($to, $subject, $body) {
    // Basic mail function using PHP's mail()
    // NOTE: For real production, use PHPMailer or similar for reliability
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@taskflow.com" . "\r\n";

    // Suppress errors if mail cannot be sent
    @mail($to, $subject, $body, $headers);
}

function sendOTP($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // SMTP server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';       // Replace with your SMTP host
        $mail->SMTPAuth   = true;
        $mail->Username   = 'davidbwashi@gmail.com'; // SMTP username
        $mail->Password   = 'hssm iwvq nimx otty';    // SMTP password
        $mail->SMTPSecure = 'ssl';                    // or 'ssl'
        $mail->Port       = 465;                      // or 465 for SSL

        // Recipients
        $mail->setFrom('noreply@taskflow.com', 'TaskFlow');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
