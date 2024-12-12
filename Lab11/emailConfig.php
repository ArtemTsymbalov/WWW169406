<?php
/**
 * Email Handler Module
 * 
 * Handles email sending functionality using PHPMailer:
 * - Configures SMTP settings
 * - Processes contact form submissions
 * - Implements email security measures
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Initialize PHPMailer with exceptions enabled
$mail = new PHPMailer(true);

try {
    // Configure SMTP settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'artemolegov228@gmail.com';
    $mail->Password   = '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
        // Sanitize input data
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $subject = htmlspecialchars($_POST['subject']);
        $message = htmlspecialchars($_POST['message']);

        // Validate required fields
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            echo "All fields are required!";
        } else {
            try {
                // Configure email parameters
                $mail->setFrom('artemolegov228@gmail.com', 'Your Website');
                $mail->addAddress('artemtsimbalov@gmail.com');
                $mail->addReplyTo($email, $name);

                $mail->Subject = $subject . ' | From: ' . $email;
                $mail->Body    = $message;

                // Send email and handle result
                $mail->send();
                echo "<p>Message has been sent successfully.</p>";
            } catch (Exception $e) {
                echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
            }
        }
    }
} catch (Exception $e) {
    echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
}
?>