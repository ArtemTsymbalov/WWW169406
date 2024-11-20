<?php
session_start();
include('../cfg.php');

function czyZalogowany()
{
    return isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
}

function sprawdzDostep()
{
    if (!czyZalogowany()) {
        header('Location: login.php'); // Przekierowanie do formularza logowania
        exit;
    }
}

// Funkcja generująca formularz logowania
function FormularzLogowania($error_message = '')
{
    $wynik = '
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel CMS - Logowanie</title>
    </head>
    <body>
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <div class="logowanie">
            <form method="post" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
                <table class="logowanie">
                    <tr><td>Email:</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
                    <tr><td>Hasło:</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
                    <tr><td></td><td><input type="submit" name="x1_submit" class="logowanie" value="Zaloguj się" /></td></tr>
                </table>
            </form>
        </div>
    ';
    if ($error_message) {
        $wynik .= '<div class="error">'.$error_message.'</div>';
    }
    $wynik .= '</div></body></html>';
    return $wynik;
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

// Sprawdzenie dostępu
if (!czyZalogowany()) {
    echo FormularzLogowania();
    exit;
}

// === Logika panelu administracyjnego ===

function ListaPodstron()
{
    sprawdzDostep();
    global $login, $pass;

    $conn = new mysqli("localhost", $login, $pass, "moja_strona");

    if ($conn->connect_error) {
        die("Помилка підключення до бази даних: " . $conn->connect_error);
    }

    $query = "SELECT * FROM page_list ORDER BY id DESC LIMIT 100";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Заголовок</th><th>Опції</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr><td>' . $row['id'] . '</td><td>' . $row['page_title'] . '</td>';
            echo '<td><a href="?action=edit&id=' . $row['id'] . '">Редагувати</a> | ';
            echo '<a href="?action=delete&id=' . $row['id'] . '" onclick="return confirm(\'Ви впевнені, що хочете видалити цю підсторінку?\')">Видалити</a></td></tr>';
        }
        echo '</table>';
    } else {
        echo "Немає підсторінок для відображення.";
    }
    $conn->close();
}


function DodajNowaPodstrone()
{
    sprawdzDostep(); // Sprawdzenie dostępu

    echo '<form method="post" action="">
        <label>Tytuł:</label><br>
        <input type="text" name="tytul" required><br><br>

        <label>Treść:</label><br>
        <textarea name="tresc" rows="5" cols="40" required></textarea><br><br>

        <label>Aktywna:</label>
        <input type="checkbox" name="aktywna"><br><br>

        <input type="submit" name="submit" value="Dodaj podstronę">
    </form>';

    if (isset($_POST['submit'])) {
        global $login, $pass;

        $conn = new mysqli("localhost", $login, $pass, "moja_strona");
        if ($conn->connect_error) {
            die("Błąd połączenia z bazą danych: " . $conn->connect_error);
        }

        $page_title = $conn->real_escape_string($_POST['tytul']);
        $page_content = $conn->real_escape_string($_POST['tresc']);
        $status = isset($_POST['aktywna']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$page_title', '$page_content', '$status')";
        if ($conn->query($query) === TRUE) {
            echo "Podstrona została dodana!";
        } else {
            echo "Błąd: " . $conn->error;
        }
        $conn->close();
    }
}

function Wylogowanie()
{
    if (isset($_SESSION['zalogowany'])) {
        unset($_SESSION['zalogowany']);
    }
    header("Location: login.php");
    exit;
}

// Dispatcher funkcji
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'list': ListaPodstron(); break;
        case 'add': DodajNowaPodstrone(); break;
        case 'edit': EdytujPodstrone($_GET['id']); break; // Handle editing a page
        case 'delete': UsunPodstrone($_GET['id']); break; // Handle deleting a page
        case 'logout': Wylogowanie(); break;
        default: echo "Nieznana akcja."; break;
    }
} else {
    // Wyświetlenie przycisków akcji dla administratora
    echo '
    <h2>Witaj w panelu administracyjnym!</h2>
    <p>Wybierz jedną z opcji:</p>
    <form action="" method="get">
        <button type="submit" name="action" value="list">Lista podstron</button>
        <button type="submit" name="action" value="add">Dodaj nową podstronę</button>
        <button type="submit" name="action" value="logout">Wyloguj się</button>
    </form>
    ';
}

function EdytujPodstrone($id)
{
    // Sprawdzamy dostęp do funkcji
    sprawdzDostep();

    // Połączenie z bazą danych
    global $login, $pass;
    $conn = new mysqli("localhost", $login, $pass, "moja_strona");

    if ($conn->connect_error) {
        die("Błąd połączenia z bazą danych: " . $conn->connect_error);
    }

    // Pobieramy dane podstrony na podstawie ID
    $query = "SELECT * FROM page_list WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $page = $result->fetch_assoc();
    
    // Jeśli podstrona nie istnieje
    if (!$page) {
        echo "Podstrona nie istnieje.";
        return;
    }

    // Formularz edycji
    echo '
    <form method="post" action="">
        <label>Tytuł:</label><br>
        <input type="text" name="tytul" value="' . htmlspecialchars($page['page_title']) . '" required><br><br>

        <label>Treść:</label><br>
        <textarea name="tresc" rows="5" cols="40" required>' . htmlspecialchars($page['page_content']) . '</textarea><br><br>

        <label>Aktywna:</label>
        <input type="checkbox" name="aktywna" ' . ($page['status'] ? 'checked' : '') . '><br><br>

        <input type="submit" name="submit_edit" value="Zapisz zmiany">
    </form>';

    // Obsługa formularza edycji
    if (isset($_POST['submit_edit'])) {
        $tytul = $conn->real_escape_string($_POST['tytul']);
        $tresc = $conn->real_escape_string($_POST['tresc']);
        $aktywna = isset($_POST['aktywna']) ? 1 : 0;

        $update_query = "UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ? LIMIT 1";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssii", $tytul, $tresc, $aktywna, $id);

        if ($update_stmt->execute()) {
            echo "Podstrona została zaktualizowana!";
        } else {
            echo "Błąd: " . $conn->error;
        }
    }

    $conn->close();
}
function UsunPodstrone($id)
{
    // Sprawdzamy dostęp do funkcji
    sprawdzDostep();

    // Połączenie z bazą danych
    global $login, $pass;
    $conn = new mysqli("localhost", $login, $pass, "moja_strona");

    if ($conn->connect_error) {
        die("Błąd połączenia z bazą danych: " . $conn->connect_error);
    }

    // Zapytanie usuwające podstronę
    $query = "DELETE FROM page_list WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Podstrona została usunięta!";
    } else {
        echo "Błąd: " . $conn->error;
    }

    $conn->close();
}


?>
