<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/* po tym komentarzu będzie kod do dynamicznego ładowania stron */

require_once "config/constants.php";
include("cfg.php");
include ("showpage.php");

$web = show_page($_GET['idp'], $link);
?>
<!DOCTYPE HTML>
<html lang="pl">
  <head>
    <meta charset="UTF-8">
    <meta name="Content-Language" content="pl">
    <meta name="Author" content="Artem Tsymbalov">
    <title>Największe mosty świata</title>
    <link rel="stylesheet" href="css/style.css">
  </head>
  <body>
    <!-- Menu strony -->
    <table>
      <tr>
        <td><a href="index.php?idp=glowna">Strona Główna</a></td>
        <td><a href="index.php?idp=danyangKunshan">Most Danyang-Kunshan</a></td>
        <td><a href="index.php?idp=tianjinGrandBridge">Most Tianjin Grand Bridge</a></td>
        <td><a href="index.php?idp=weinanWeiheGrandBridge">Most Weinan Weihe Grand Bridge</a></td>
        <td><a href="index.php?idp=bangNaExpressway">Most Bang Na Expressway</a></td>
        <td><a href="index.php?idp=manchac">Most Manchac</a></td>
        <td><a href="index.php?idp=kontakt">Kontakt</a></td>
        <td><a href="index.php?idp=lab2js">Lab 2 JS</a></td>
        <td><a href="index.php?idp=lab3">Lab 3</a></td>
        <td><a href="index.php?idp=filmy">Filmy</a></td>

      </tr>
    </table>

    <div class="container">
      <?php
      error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
      $strona = 'html/glowna.html';
      

      // Sprawdzenie istnienia pliku i załączenie
      if (file_exists($strona)) {
          include($strona);
      } else {
          echo 'Strona nie znaleziona.';
      }
      ?>
    </div>
  </body>
</html>

    <?php
 $nr_indeksu = '169406';
 $nrGrupy = 'ISI4';
 echo 'Autor: Artem Tsymbalov ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
?>
  </body>
</html>
