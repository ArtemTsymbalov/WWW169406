<?php
// Ustawienia połączenia z bazą danych
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';
$login = "admin";
$pass = "pass";

// Próba połączenia za pomocą mysqli
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Sprawdzenie połączenia
if (!$link) {
    echo '<b>Przerwane połączenie: ' . mysqli_connect_error() . '</b>';
    exit;
}
?>
