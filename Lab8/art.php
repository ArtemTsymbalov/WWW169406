<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_OFF;  // Disable debug output
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'artemolegov228@gmail.com';
    $mail->Password   = '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $subject = htmlspecialchars($_POST['subject']);
        $message = htmlspecialchars($_POST['message']);

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            echo "All fields are required!";
        } else {
            try {
                $mail->setFrom('artemolegov228@gmail.com', 'Your Website');
                $mail->addAddress('artemtsimbalov@gmail.com');
                $mail->addReplyTo($email, $name);

                $mail->Subject = $subject . ' | From: ' . $email;
                $mail->Body    = $message;

                $mail->send();
                echo "<p>Message has been sent successfully.</p>";  // Success message
            } catch (Exception $e) {
                echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
            }
        }
    }

} catch (Exception $e) {
    echo "<p>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
}
?>