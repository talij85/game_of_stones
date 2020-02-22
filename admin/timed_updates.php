<?php
// load basic data
echo "Connecting to DB...";
include_once("connect.php");

echo "Loading Display...";
include_once('displayFuncs.php');
echo "Loading Biz...";
include_once('busiFuncs.php');
echo "Loading chars...";
include_once('charFuncs.php');
echo "Loading map...";
include_once('mapData.php');

echo "Starting Updates...";

$curtime=time();
$check=intval($curtime/3600);
$qcheck = intval($curtime/900);

// if we are on an hour change
if ($check*4 == $qcheck)
{
  include_once('hourly_updates.php');
}

// Do quarter hour updates
include_once('quarterly_updates.php');
?>