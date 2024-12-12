<?php
/**
 * Laboratory Exercise Module
 * 
 * Demonstrates various PHP programming concepts:
 * - File inclusion methods
 * - Control structures
 * - Loops
 * - PHP superglobals
 */

 $nr_indeksu = '169406';
 $nrGrupy = 'ISI4';
 echo 'Artem Tsymbalov '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';

// Demonstration of include() and require_once()
echo 'Using include() and require_once() methods <br />';
echo 'We have plik_do_zalacenia.php which contains $color = green;$fruit = apple;';
include('plik_do_zalaczenia.php');
require_once('plik_do_zalaczenia.php');

// Demonstration of control structures
echo '<br />Using if, else, elseif and switch conditions <br />';
echo '<br />Color=green; Grade=4;<br />';
$ocena = 4;

if ($color == 'green') {
    echo 'The color is green<br />';
} elseif ($color == 'orange') {
    echo 'The color is orange<br />';
} else {
    echo 'No color specified<br />';
}

// Switch example
switch ($ocena) {
    case 5:
        echo 'Excellent grade<br />';
        break;
    case 4:
        echo 'Good grade<br />';
        break;
    case 3:
        echo 'Satisfactory grade<br />';
        break;
    default:
        echo 'Failing grade<br />';
        break;
}

// Loop demonstrations
echo '<br />Using while() and for() loops, counting from 0 to 4 <br />';

$i = 0;
while ($i < 5) {
    echo 'While loop - iteration: ' . $i . '<br />';
    $i++;
}

for ($j = 0; $j < 5; $j++) {
    echo 'For loop - iteration: ' . $j . '<br />';
}

// PHP superglobals demonstration
echo '<br />Using $_GET, $_POST, $_SESSION variables <br />';

// $_GET example
if (isset($_GET['name'])) {
    echo 'Received via $_GET: ' . htmlspecialchars($_GET['name']) . '<br />';
}

// $_POST example
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    echo 'Received via $_POST: ' . htmlspecialchars($_POST['name']) . '<br />';
}

// $_SESSION example
session_start();
if (!isset($_SESSION['zalogowany'])) {
    $_SESSION['zalogowany'] = true;
    echo 'Session started.<br />';
} else {
    echo 'Session already exists.<br />';
}
?>

<form method="POST" action="">
    <label for="name">Enter your name:</label>
    <input type="text" id="name" name="name">
    <input type="submit" value="Submit">
</form>
