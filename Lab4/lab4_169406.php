<?php
 $nr_indeksu = '169406';
 $nrGrupy = 'ISI4';
 echo 'Artem Tsymbalov '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';

// a) Metoda include() i require_once()
echo 'Zastosowanie metody include() i require_once() <br />';
echo 'Mamy plik_do_zalacenia.php który zawiera $color = green;$fruit = apple;';
include('plik_do_zalaczenia.php'); // załącza plik, jeśli nie istnieje to wyświetli ostrzeżenie
require_once('plik_do_zalaczenia.php'); // załącza plik tylko raz, jeśli nie istnieje, to wyświetli błąd

// b) Warunki if, else, elseif, switch
echo '<br />Zastosowanie warunków if, else, elseif i switch <br />';
echo '<br />Color=green; Ocena=4;<br />';
$ocena = 4;

if ($color == 'green') {
    echo 'Kolorem jest zielony<br />';
} elseif ($color == 'orange') {
    echo 'Kolorem jest pomarancowy<br />';
} else {
    echo 'Nie ma koloru<br />';
}

// Przykład switch
switch ($ocena) {
    case 5:
        echo 'Ocena bardzo dobra<br />';
        break;
    case 4:
        echo 'Ocena dobra<br />';
        break;
    case 3:
        echo 'Ocena dostateczna<br />';
        break;
    default:
        echo 'Ocena negatywna<br />';
        break;
}

// c) Pętla while() i for()
echo '<br />Zastosowanie pętli while() i for(), gdzie licymy od 0 do 4 <br />';

$i = 0;
while ($i < 5) {
    echo 'Pętla while - iteracja: ' . $i . '<br />';
    $i++;
}

for ($j = 0; $j < 5; $j++) {
    echo 'Pętla for - iteracja: ' . $j . '<br />';
}

// d) Typy zmiennych $_GET, $_POST, $_SESSION
echo '<br />Zastosowanie zmiennych $_GET, $_POST, $_SESSION <br />';

// Przykład użycia $_GET
if (isset($_GET['name'])) {
    echo 'Otrzymano przez $_GET: ' . htmlspecialchars($_GET['name']) . '<br />';
}
// Przykład użycia $_POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    echo 'Otrzymano przez $_POST: ' . htmlspecialchars($_POST['name']) . '<br />';
}

// Przykład użycia $_SESSION
session_start();
if (!isset($_SESSION['zalogowany'])) {
    $_SESSION['zalogowany'] = true;
    echo 'Sesja została rozpoczęta.<br />';
} else {
    echo 'Sesja już istnieje.<br />';
}
?>

<form method="POST" action="">
    <label for="name">Wpisz swoje imię:</label>
    <input type="text" id="name" name="name">
    <input type="submit" value="Wyślij">
</form>
