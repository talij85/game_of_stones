<?php
// load basic data
echo "Connecting to DB...";
include_once("connect.php");
include_once('displayFuncs.php');
include_once('busiFuncs.php');
include_once('charFuncs.php');
include_once('mapData.php');
echo "Starting Hourly Updates...";

$curtime=time();
$check=intval(time()/3600);
$lastBattleDone = mysql_num_rows(mysql_query("SELECT id FROM Contests WHERE type='99' AND done='1'"));
$msgs = mysql_fetch_array(mysql_query("SELECT * FROM messages WHERE id='0'"));
    
// UPDATE CLAN DATA
echo "Updating Clans...";
include_once('clandata.php');

// Determine what a new hordes health would be
// Needs to be done before City data, as it's used by city defense calculations
$row = mysql_fetch_assoc(mysql_query('SELECT SUM(vitality) AS value_sum FROM Users WHERE nation != 0')); 
$totvit = $row['value_sum'];
$resulth = mysql_query("SELECT id, level FROM Users WHERE nation != 0 ORDER BY level DESC, exp DESC LIMIT 1");
$topchar = mysql_fetch_array($resulth);
$hhealth = $totvit*$topchar[level]/20;
if ($hhealth < 1000) $hhealth = 1000;

// UPDATE CLAN BATTLE PARTICIPATION PTS
echo "Updating Clan Battle Participation Points...";
include_once('cb_hourly_update.php');

// UPDATE CITY DATA
echo "Updating Cities...";
include_once('citydata.php');

// UPDATE HORDES
echo "Updating Hordes...";
include_once('hordedata.php');

// DO LAST BATTLE STUFF
echo "Updating Last Battle...";
mysql_query("LOCK TABLES Hordes WRITE, Contests WRITE, Locations WRITE, Estates WRITE, messages WRITE, Items WRITE, Profs WRITE, Users WRITE, Users_Stats WRITE;");
// Get the number of Broken Seals
$numBroken = mysql_num_rows(mysql_query("SELECT id FROM Items WHERE type=0 AND owner='99999'"));
if ($numBroken >= 7) // if all seals are broken
{
  include_once('lastbattle.php');
} // All seals broken
mysql_query("UNLOCK TABLES;");

?>