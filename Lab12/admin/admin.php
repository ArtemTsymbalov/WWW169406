<?php
/**
 * Admin Panel Module
 * 
 * This file contains the core functionality for the CMS admin panel including:
 * - User authentication
 * - Page management (CRUD operations)
 * - Security checks
 */

session_start();
include('../config/cfg.php');
require_once('CategoryManager.php');
require_once('ProductManager.php');

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

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['zalogowany'])) {
    echo FormularzLogowania();
    exit;
}

// HTML structure start
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
<?php

/**
 * Checks if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function czyZalogowany()
{
    return isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
}

/**
 * Validates admin access
 * Redirects to login page if user is not authenticated
 */
function sprawdzDostep()
{
    if (!czyZalogowany()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Generates login form HTML
 * 
 * @param string $error_message Optional error message to display
 * @return string HTML content of the login form
 */
function FormularzLogowania($error_message = '')
{
    $wynik = '
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panel CMS - Logowanie</title>
        <link rel="stylesheet" href="css/admin-style.css">
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

// Establish database connection
$conn = new mysqli($dbhost, $dbuser, $dbpass, $baza);

if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// === Logika panelu administracyjnego ===

/**
 * Lists all pages in the CMS
 * Requires admin authentication
 */
function ListaPodstron()
{
    sprawdzDostep();
    global $dbhost, $dbuser, $dbpass, $baza;
    
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $baza);
    
    if ($conn->connect_error) {
        die("Database connection error: " . $conn->connect_error);
    }

    $query = "SELECT * FROM page_list ORDER BY id DESC LIMIT 100";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo '<table border="1">';
        echo '<tr>
                <th>ID</th>
                <th>Title</th>
                <th>Options</th>
              </tr>';
              
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['page_title'] . '</td>';
            echo '<td>
                    <a href="?action=edit&id=' . $row['id'] . '">edit</a> | 
                    <a href="?action=delete&id=' . $row['id'] . '" 
                       onclick="return confirm(\'Are you sure you want to delete this page?\')">
                        Delete
                    </a>
                  </td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "There are no subpages to display.";
    }
    
    $conn->close();
}


function DodajNowaPodstrone()
{
    sprawdzDostep();
    global $dbhost, $dbuser, $dbpass, $baza;
    
    echo '
    <form method="post">
        <h2>Dodaj nową podstronę</h2>
        <label>Tytuł:</label><br>
        <input type="text" name="tytul" required><br><br>
        
        <label>Treść:</label><br>
        <textarea name="tresc" rows="10" cols="50" required></textarea><br><br>
        
        <label>Aktywna:</label>
        <input type="checkbox" name="aktywna" value="1" checked><br><br>
        
        <input type="submit" name="submit" value="Dodaj podstronę">
    </form>';

    if (isset($_POST['submit'])) {
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $baza);
        
        if ($conn->connect_error) {
            die("Błąd połączenia z bazą danych: " . $conn->connect_error);
        }

        $page_title = $conn->real_escape_string($_POST['tytul']);
        $page_content = $conn->real_escape_string($_POST['tresc']);
        $status = isset($_POST['aktywna']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $page_title, $page_content, $status);

        if ($stmt->execute()) {
            echo "<p>Podstrona została dodana!</p>";
        } else {
            echo "<p>Błąd: " . $conn->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}

function Wylogowanie()
{
    if (isset($_SESSION['zalogowany'])) {
        unset($_SESSION['zalogowany']);
        session_destroy();
    }
    header("Location: admin.php");
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
        case 'categories': 
            $categoryManager = new CategoryManager($conn);
            $categoryManager->pokazKategorie(); 
            break;
        case 'add_category': 
            $categoryManager = new CategoryManager($conn);
            $categoryManager->dodajKategorie(); 
            break;
        case 'edit_category': 
            $categoryManager = new CategoryManager($conn);
            $categoryManager->edytujKategorie($_GET['id']); 
            break;
        case 'delete_category': 
            $categoryManager = new CategoryManager($conn);
            $categoryManager->usunKategorie($_GET['id']); 
            header('Location: admin.php?action=categories');
            break;
        case 'products': 
            $productManager = new ProductManager($conn);
            $productManager->pokazProdukty(); 
            break;
        case 'add_product': 
            $productManager = new ProductManager($conn);
            $productManager->dodajProdukt(); 
            break;
        case 'edit_product': 
            $productManager = new ProductManager($conn);
            $productManager->edytujProdukt($_GET['id']); 
            break;
        case 'delete_product': 
            $productManager = new ProductManager($conn);
            $productManager->usunProdukt($_GET['id']); 
            header('Location: admin.php?action=products');
            break;
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
        <button type="submit" name="action" value="categories">Manage Categories</button>
        <button type="submit" name="action" value="products">Manage Products</button>
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

/**
 * Deletes a page from the CMS
 * 
 * @param int $id Page ID to delete
 */
function UsunPodstrone($id)
{
    sprawdzDostep();
    global $login, $pass;
    
    $conn = new mysqli("localhost", $login, $pass, "moja_strona");
    if ($conn->connect_error) {
        die("Database connection error: " . $conn->connect_error);
    }

    $query = "DELETE FROM page_list WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Page deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}


?>
