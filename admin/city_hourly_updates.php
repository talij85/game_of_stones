<?php
// load basic data
echo "Connecting to DB...";
include_once("connect.php");
include_once('displayFuncs.php');
include_once('busiFuncs.php');
include_once('charFuncs.php');

echo "Starting Hourly Updates...";

// UPDATE CITY DATA
echo "Updating Cities...";
include_once('citydata.php');

?>