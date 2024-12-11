<?php
/**
 * Contact Form Handler
 * 
 * This class manages the contact form functionality including:
 * - Displaying the contact form
 * - Sending emails using PHPMailer
 * - Password reminder functionality
 */

class Contact {
    /**
     * Displays the contact form
     * Renders HTML form with fields for name, email, and message
     */
    public function PokazKontakt() {
        echo '
        <div class="container">
            <h1>Contact</h1>
            <form action="contact.php" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="4" required></textarea>
                
                <input type="submit" name="submit_contact" value="Send">
            </form>
        </div>';
    }

    /**
     * Sends contact email
     * Validates form data and sends email using mail() function
     * 
     * @param string $odbiorca Recipient email address
     */
    function WyslijMailKontakt($odbiorca) {
        // Check if all required fields are filled
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo "All fields are required!";
            PokazKontakt(); 
        } else {
            // Sanitize and prepare email data
            $mailData['subject'] = htmlspecialchars($_POST['temat']);
            $mailData['body'] = htmlspecialchars($_POST['tresc']);
            $mailData['sender'] = htmlspecialchars($_POST['email']);
            $mailData['recipient'] = $odbiorca;
    
            // Prepare email headers
            $headers = "From: Contact Form <" . $mailData['sender'] . ">\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\n";
            $headers .= "Content-Transfer-Encoding: 8bit\n";
            $headers .= "X-Sender: " . $mailData['sender'] . "\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "Return-Path: " . $mailData['sender'] . "\n";
    
            // Send email and display result
            if (mail($mailData['recipient'], $mailData['subject'], $mailData['body'], $headers)) {
                echo "Message sent successfully!";
            } else {
                echo "Error sending message.";
            }
        }
    }

    /**
     * Password reminder functionality
     * Sends an email with password reset information
     * 
     * @param string $email User's email address
     */
    public function PrzypomnijHaslo($email) {
        $to = $email;
        $subject = "Password Reminder";
        $headers = "From: no-reply@yourdomain.com";
        $body = "Your admin panel password is: 'your_password'. It is recommended to change it after logging in.";

        if (mail($to, $subject, $body, $headers)) {
            echo "Password reminder email has been sent.";
        } else {
            echo "Error sending password reminder email.";
        }
    }
}



// Sprawdzenie, czy formularz został przesłany
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {

    // Pobranie danych z formularza
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $temat = htmlspecialchars($_POST['temat']);
    $message = htmlspecialchars($_POST['message']);

    // Sprawdzenie, czy wszystkie pola są wypełnione
    if (empty($name) || empty($email) || empty($temat) || empty($message)) {
        echo "Wszystkie pola są wymagane!";
    } else {
        $to = "artemolegov228@gmail.com"; 
        $subject = "Nowa wiadomość: " . $temat;
        $body = "Imię: $name\nEmail: $email\nTemat: $temat\n\nWiadomość:\n$message";
        $headers = "From: $email";

        // Wysłanie maila
        if (mail($to, $subject, $body, $headers)) {
            echo "Wiadomość została wysłana!";
        } else {
            echo "Wystąpił błąd podczas wysyłania wiadomości.";
        }
    }
} else {
    echo "Formularz nie został przesłany poprawnie.";
}

?>
