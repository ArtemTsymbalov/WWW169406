<?php
/**
 * Database Configuration Module
 * 
 * Contains database connection settings and establishes the connection.
 * This file is included by other modules that need database access.
 */

// Database connection settings
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';
$login = "admin";
$pass = "pass";

// Attempt to establish mysqli connection
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

// Check connection and handle errors
if (!$link) {
    echo '<b>Connection failed: ' . mysqli_connect_error() . '</b>';
    exit;
}
?>
