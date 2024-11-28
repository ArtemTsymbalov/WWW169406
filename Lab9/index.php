<?php
/**
 * Main Application Entry Point
 * 
 * This file serves as the main controller for the website:
 * - Handles page routing
 * - Includes necessary configuration and modules
 * - Renders the main layout template
 */

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

require_once "config/constants.php";
include("cfg.php");
include("showpage.php");

// Load dynamic page content based on URL parameter
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
    <!-- Navigation Menu -->
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

    <!-- Main Content Container -->
    <div class="container">
      <?php
      error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
      echo $web;
      ?>
    </div>

    <!-- Author Information -->
    <?php
 $nr_indeksu = '169406';
 $nrGrupy = 'ISI4';
 echo 'Autor: Artem Tsymbalov ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
?>
  </body>
</html>
