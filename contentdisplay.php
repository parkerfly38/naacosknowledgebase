<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once($_SERVER["DOCUMENT_ROOT"].'/includes/memberClicks.inc.php');

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/_system.config.php'); 
define('DB_HOST', NAACOS_DB_HOST);
define('DB_NAME', NAACOS_DB_NAME);
define('DB_USER', NAACOS_DB_USER);
define('DB_PASS', NAACOS_DB_PASS); 
define('DB_PORT', NAACOS_DB_PORT);
date_default_timezone_set('UTC'); // default time zone
require_once($_SERVER["DOCUMENT_ROOT"].'/includes/mysql.inc.php');

if (isset($_GET["id"])) {
    $sqlgetthetext = "SELECT theText FROM forumTopic where idforumTopic = ".$_GET["id"].";";
    $thetext = $db->query($sqlgetthetext);
    $row = $thetext->fetchAll();
    echo $row[0]["theText"];
} ?>