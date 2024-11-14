<?php

function FormularzLogowania($error_message = '')
{
    $wynik = '
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <div class="logowanie">
            <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
                <table class="logowanie">
                    <tr><td class="log4_t">Email</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
                    <tr><td class="log4_t">Hasło</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
                    <tr><td>&nbsp; </td><td><input type="submit" name="x1_submit" class="logowanie" value="Zaloguj" /></td></tr>
                </table>
            </form>
        </div>
        ';

    if ($error_message) {
        $wynik .= '<div class="error">'.$error_message.'</div>';
    }

    $wynik .= '</div>';
    
    return $wynik;
}

session_start();
include('cfg.php');

function czyZalogowany()
{
    return isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
}

// Obsługa logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x1_submit'])) {
    $email = $_POST['login_email'] ?? '';
    $password = $_POST['login_pass'] ?? '';

    if ($email === $login && $password === $pass) {
        $_SESSION['zalogowany'] = true;
        header("Location: admin.php");
        exit;
    } else {
        echo FormularzLogowania("Niepoprawny login lub hasło!");
        exit;
    }
}

// Sprawdzanie dostępu do dalszych metod administracyjnych
if (!czyZalogowany()) {
    echo FormularzLogowania();
    exit;
}

function ListaPodstron()
{
    // Zastąp danymi do połączenia z twoją bazą danych
    $conn = new mysqli("localhost", "user", "password", "database");

    // Sprawdzenie połączenia z bazą danych
    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    // Zapytanie SQL do pobrania listy podstron
    $query = "SELECT * FROM page_list ORDER BY data DESC LIMIT 100";
    $result = $conn->query($query);

    // Sprawdzenie, czy są wyniki
    if ($result->num_rows > 0) {
        // Rozpoczęcie wyświetlania listy w formie tabeli
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Tytuł</th></tr>';

        // Pętla while do iteracji po wynikach
        while ($row = $result->fetch_assoc()) {
            // Wyświetlanie id oraz tytułu podstrony
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['tytul'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo "Brak podstron do wyświetlenia.";
    }

    // Zamknięcie połączenia z bazą danych
    $conn->close();
}

function EdytujPodstrone($id)
{
    // Połączenie z bazą danych
    $conn = new mysqli("localhost", "user", "password", "database");

    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    // Pobranie danych podstrony z bazy
    $query = "SELECT tytul, tresc, aktywna FROM page_list WHERE id = $id LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Formularz edycji podstrony
        echo '<form method="post" action="">
            <label>Tytuł:</label><br>
            <input type="text" name="tytul" value="' . $row['tytul'] . '"><br><br>

            <label>Treść:</label><br>
            <textarea name="tresc" rows="5" cols="40">' . $row['tresc'] . '</textarea><br><br>

            <label>Aktywna:</label>
            <input type="checkbox" name="aktywna" ' . ($row['aktywna'] ? 'checked' : '') . '><br><br>

            <input type="submit" name="submit" value="Zapisz zmiany">
        </form>';

        // Obsługa zapisu zmian do bazy danych
        if (isset($_POST['submit'])) {
            $tytul = $conn->real_escape_string($_POST['tytul']);
            $tresc = $conn->real_escape_string($_POST['tresc']);
            $aktywna = isset($_POST['aktywna']) ? 1 : 0;

            // Aktualizacja rekordu w bazie
            $updateQuery = "UPDATE page_list SET tytul='$tytul', tresc='$tresc', aktywna='$aktywna' WHERE id=$id LIMIT 1";
            if ($conn->query($updateQuery) === TRUE) {
                echo "Podstrona została zaktualizowana.";
            } else {
                echo "Błąd: " . $conn->error;
            }
        }
    } else {
        echo "Nie znaleziono podstrony o podanym ID.";
    }

    $conn->close();
}

function DodajNowaPodstrone()
{
    // Formularz dodawania nowej podstrony
    echo '<form method="post" action="">
        <label>Tytuł:</label><br>
        <input type="text" name="tytul" required><br><br>

        <label>Treść:</label><br>
        <textarea name="tresc" rows="5" cols="40" required></textarea><br><br>

        <label>Aktywna:</label>
        <input type="checkbox" name="aktywna"><br><br>

        <input type="submit" name="submit" value="Dodaj podstronę">
    </form>';

    // Obsługa dodawania nowej podstrony do bazy danych
    if (isset($_POST['submit'])) {
        $conn = new mysqli("localhost", "user", "password", "database");

        if ($conn->connect_error) {
            die("Błąd połączenia: " . $conn->connect_error);
        }

        $tytul = $conn->real_escape_string($_POST['tytul']);
        $tresc = $conn->real_escape_string($_POST['tresc']);
        $aktywna = isset($_POST['aktywna']) ? 1 : 0;

        // Wstawianie nowej podstrony do bazy
        $insertQuery = "INSERT INTO page_list (tytul, tresc, aktywna) VALUES ('$tytul', '$tresc', '$aktywna')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "Podstrona została dodana.";
        } else {
            echo "Błąd: " . $conn->error;
        }

        $conn->close();
    }
}

function UsunPodstrone($id)
{
    $conn = new mysqli("localhost", "user", "password", "database");

    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }

    // Zapytanie SQL do usunięcia podstrony
    $deleteQuery = "DELETE FROM page_list WHERE id = $id LIMIT 1";

    if ($conn->query($deleteQuery) === TRUE) {
        echo "Podstrona została usunięta.";
    } else {
        echo "Błąd: " . $conn->error;
    }

    $conn->close();
}

?>
