<?php
include_once('config.php');

$db = @mysql_connect($database_server, $database_username, $database_password);
if (!$db) {echo "<br><b>Could not connect to the MySQL database. Please try again in a few minutes.</b>"; exit;}
mysql_select_db($database_name,$db);
$time=time();
$div_img = "<table border=0 cellpadding=0 cellspacing=0 width=550 height=1 background=\"images/divider.gif\"><tr><td></td></tr></table>";

// CONSTANTS
$battlelimit = 100;
$max_gold = 10000000000;
$enable_producers = 0;
$base_inv_max = 15;
$is_firefox=substr_count($_SERVER["HTTP_USER_AGENT"],"Firefox");
$maxalts = 99;
$maxquests = 2;
$maxsocalts = 99;

  date_default_timezone_set('America/Los_Angeles');
?>