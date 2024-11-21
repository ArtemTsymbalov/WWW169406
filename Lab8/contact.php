<?php

class Contact {

    // Metoda wyświetlająca formularz kontaktowy
    public function PokazKontakt() {
        echo '
        <div class="container">
            <h1>Kontakt</h1>
            <form action="contact.php" method="post">
                <label for="name">Imię:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="message">Wiadomość:</label>
                <textarea id="message" name="message" rows="4" required></textarea>
                
                <input type="submit" name="submit_contact" value="Wyślij">
            </form>
        </div>';
    }

    // Metoda wysyłająca maila kontaktowego
    function WyslijMailKontakt($odbiorca) {
        // Sprawdzenie, czy wszystkie wymagane pola formularza są wypełnione
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo "Nie wypełniłeś wszystkich pól!";
            PokazKontakt(); // Ponownie wyświetl formularz kontaktowy
        } else {
            // Pobranie danych z formularza
            $mailData['subject'] = htmlspecialchars($_POST['temat']);
            $mailData['body'] = htmlspecialchars($_POST['tresc']);
            $mailData['sender'] = htmlspecialchars($_POST['email']);
            $mailData['recipient'] = $odbiorca; // Odbiorca wiadomości (adres email)
    
            // Złożenie nagłówków wiadomości
            $headers = "From: Formularz kontaktowy <" . $mailData['sender'] . ">\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/plain; charset=utf-8\n";
            $headers .= "Content-Transfer-Encoding: 8bit\n";
            $headers .= "X-Sender: " . $mailData['sender'] . "\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\n";
            $headers .= "X-Priority: 3\n";
            $headers .= "Return-Path: " . $mailData['sender'] . "\n";
    
            // Wysłanie maila
            if (mail($mailData['recipient'], $mailData['subject'], $mailData['body'], $headers)) {
                echo "Wiadomość została wysłana!";
            } else {
                echo "Błąd podczas wysyłania wiadomości.";
            }
        }
    }
    

    // Metoda przypominająca hasło
    public function PrzypomnijHaslo($email) {
        $to = $email;
        $subject = "Przypomnienie hasła";
        $headers = "From: no-reply@twojadomena.com";
        $body = "Twoje hasło do panelu admina to: 'twoje_haslo'. Zaleca się zmianę hasła po zalogowaniu.";

        if (mail($to, $subject, $body, $headers)) {
            echo "Email z hasłem został wysłany.";
        } else {
            echo "Błąd podczas wysyłania maila z przypomnieniem hasła.";
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
