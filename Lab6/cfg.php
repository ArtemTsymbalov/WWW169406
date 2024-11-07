<?php
// Ustawienia połączenia z bazą danych
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = ''; // Domyślnie w XAMPP hasło jest puste
$baza = 'strona';

// Próba połączenia za pomocą mysqli
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Sprawdzenie połączenia
if (!$link) {
    echo '<b>Przerwane połączenie: ' . mysqli_connect_error() . '</b>';
    exit;
}
?>
