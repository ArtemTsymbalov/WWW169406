<?php
/**
 * Global Constants Configuration
 * 
 * Defines application-wide constants including:
 * - Root directory paths
 * - View paths
 * - Upload directory paths
 */

// Get main folder path dynamically
$main_folder = dirname(__DIR__);
$link_split = explode("/", $main_folder);

// Define application paths
define('ROOT', "/".$link_split[count($link_split) - 1]);
define('VIEWS', ROOT . '/views');
define('UPLOADS', ROOT . '/uploads');
?>