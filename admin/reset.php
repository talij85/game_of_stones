<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Users Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>

<u>Resets the table "Users and Users_data"</u><br><br>
<?php
// Connect
include_once("connect.php");

/////////////////////////////////////////////////////////////////////////////////////
// USERS ////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////
if (strtolower($name) != "the" && strtolower($lastname) != "creator" && $head != 1)
{
  echo "Only the Creator has such powers!";
}
else
{
echo "::USERS TABLE::<br><br>";
// Drop Old Table
$query  = 'DROP TABLE IF EXISTS Users';
$result = mysql_query($query);
echo "Drop Old Table: $result";

// Create New Table
$query = "CREATE TABLE IF NOT EXISTS `Users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(25) NOT NULL,
  `lastname` char(25) NOT NULL,
  `password` char(40) DEFAULT NULL,
  `email` char(40) DEFAULT NULL,
  `sex` tinyint(1) DEFAULT NULL,
  `type` text,
  `nation` int(11) DEFAULT NULL,
  `jobs` text,
  `focus` int(11) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `align` float NOT NULL DEFAULT '0',
  `exp` int(11) DEFAULT NULL,
  `exp_up` int(11) DEFAULT NULL,
  `exp_up_s` int(11) DEFAULT NULL,
  `vitality` int(11) DEFAULT NULL,
  `gold` bigint(20) unsigned NOT NULL,
  `bankgold` int(11) DEFAULT NULL,
  `equip_pts` int(11) DEFAULT NULL,
  `used_pts` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `propoints` int(11) DEFAULT NULL,
  `stamina` int(11) DEFAULT NULL,
  `stamaxa` int(11) DEFAULT NULL,
  `location` char(50) NOT NULL,
  `avatar` char(200) DEFAULT NULL,
  `society` char(30) DEFAULT NULL,
  `soc_rank` int(11) DEFAULT NULL,
  `goodevil` int(11) DEFAULT NULL,
  `nextbattle` int(11) DEFAULT NULL,
  `battlestoday` int(11) DEFAULT NULL,
  `travelmode` int(11) DEFAULT NULL,
  `travelmode2` int(11) DEFAULT NULL,
  `pouch_type` int(11) DEFAULT NULL,
  `travelmode_name` char(20) DEFAULT NULL,
  `feedneed` int(11) DEFAULT NULL,
  `travelto` char(50) DEFAULT NULL,
  `arrival` int(11) DEFAULT NULL,
  `depart` int(11) DEFAULT NULL,
  `traveltype` int(11) DEFAULT NULL,
  `route` int(11) DEFAULT NULL,
  `routepoint` int(11) DEFAULT NULL,
  `born` int(11) DEFAULT NULL,
  `lastonline` int(11) DEFAULT NULL,
  `lastcheck` int(11) DEFAULT NULL,
  `lastscript` int(11) DEFAULT NULL,
  `lastbuy` int(11) DEFAULT NULL,
  `lastcontest` int(11) DEFAULT NULL,
  `lastbank` int(11) DEFAULT NULL,
  `lastpost` int(11) DEFAULT NULL,
  `newmsg` int(11) DEFAULT NULL,
  `newlog` int(11) DEFAULT NULL,
  `newachieve` int(11) DEFAULT NULL,
  `newskills` int(11) DEFAULT NULL,
  `newprof` int(11) DEFAULT NULL,
  `sort_inv` int(11) NOT NULL DEFAULT '0',
  `sort_consume` int(11) NOT NULL DEFAULT '0',
  `sort_vault` int(11) NOT NULL DEFAULT '0',
  `sort_estate` int(11) NOT NULL DEFAULT '0',
  `donor` int(11) DEFAULT NULL,
  `awesomeness` int(11) DEFAULT NULL,
  `ip` text,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `location` (`location`),
  KEY `exp` (`exp`),
  KEY `name` (`name`(3)),
  KEY `lastname` (`lastname`(3))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1";
$result = mysql_query($query);
echo "<br>Create New Table: $result";

/////////////////////////////////////////////////////////////////////////////////////
// USERS_DATA ///////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

echo "<br><br>::USERS_DATA TABLE::<br><br>";
// Drop Old Table
$query  = 'DROP TABLE IF EXISTS Users_data';
$result = mysql_query($query);
echo "Drop Old Table: $result";

// Create New Table
$query = "CREATE TABLE IF NOT EXISTS `Users_data` (
  `id` int(11) NOT NULL,
  `about` text,
  `skills` text,
  `active` text,
  `find_battle` text,
  `friends` text,
  `quests` text,
  `achieve` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";
$result = mysql_query($query);
echo "<br>Create New Table: $result";

// STATS
  // Drop Old Table
  $query  = 'DROP TABLE IF EXISTS Users_stats';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

$query = "CREATE TABLE IF NOT EXISTS `Users_stats` (
  `id` int(11) NOT NULL,
  `xp` int(11) DEFAULT '0',
  `ji` float DEFAULT '0',
  `align_high` int(11) NOT NULL DEFAULT '0',
  `align_low` int(11) NOT NULL DEFAULT '0',
  `wins` int(11) DEFAULT '0',
  `battles` int(11) DEFAULT '0',
  `duel_wins` int(11) DEFAULT '0',
  `tot_duels` int(11) DEFAULT '0',
  `enemy_wins` int(11) DEFAULT '0',
  `enemy_duels` int(11) DEFAULT '0',
  `off_wins` int(11) DEFAULT '0',
  `off_bats` int(11) DEFAULT '0',
  `def_wins` int(11) DEFAULT '0',
  `def_bats` int(11) DEFAULT '0',
  `npc_wins` int(11) DEFAULT '0',
  `tot_npcs` int(11) DEFAULT '0',
  `horde_wins` int(11) NOT NULL DEFAULT '0',
  `army_wins` int(11) NOT NULL DEFAULT '0',
  `horde_finish` int(11) NOT NULL DEFAULT '0',
  `army_finish` int(11) NOT NULL DEFAULT '0',
  `coin` int(11) DEFAULT '0',
  `bankcoin` int(11) DEFAULT '0',
  `coin_donated` int(11) DEFAULT '0',
  `duel_earn` int(11) DEFAULT '0',
  `item_earn` int(11) DEFAULT '0',
  `dice_earn` int(11) DEFAULT '0',
  `prof_earn` int(11) DEFAULT '0',
  `quest_earn` int(11) DEFAULT '0',
  `quests_done` int(11) DEFAULT '0',
  `play_quests_done` int(11) DEFAULT '0',
  `find_quests_done` int(11) DEFAULT '0',
  `npc_quests_done` int(11) DEFAULT '0',
  `horde_quests_done` int(11) DEFAULT '0',
  `escort_quests_done` int(11) DEFAULT '0',
  `item_quests_done` int(11) DEFAULT '0',
  `support_quest_ji` float DEFAULT '0',
  `my_quests_done` int(11) DEFAULT '0',
  `shadow_wins` int(11) DEFAULT '0',
  `shadow_npcs` int(11) DEFAULT '0',
  `military_wins` int(11) DEFAULT '0',
  `military_npcs` int(11) DEFAULT '0',
  `ruffian_wins` int(11) DEFAULT '0',
  `ruffian_npcs` int(11) DEFAULT '0',
  `channeler_wins` int(11) DEFAULT '0',
  `channeler_npcs` int(11) DEFAULT '0',
  `animal_wins` int(11) DEFAULT '0',
  `animal_npcs` int(11) DEFAULT '0',
  `exotic_wins` int(11) DEFAULT '0',
  `exotic_npcs` int(11) DEFAULT '0',
  `skill_pts_used` int(11) DEFAULT '0',
  `highest_skill` int(11) DEFAULT '0',
  `num_active_skills` int(11) DEFAULT '0',
  `num_classes` int(11) DEFAULT '0',
  `prof_pts_used` int(11) DEFAULT '0',
  `highest_prof` int(11) DEFAULT '0',
  `num_profs` int(11) DEFAULT '0',
  `num_biz` int(11) DEFAULT '0',
  `num_biz_types` int(11) DEFAULT '0',
  `most_biz` int(11) DEFAULT '0',
  `highest_biz` int(11) DEFAULT '0',
  `items_marketed` int(11) DEFAULT '0',
  `items_found` int(11) DEFAULT '0',
  `items_dropped` int(11) DEFAULT '0',
  `items_from_shop` int(11) DEFAULT '0',
  `items_from_market` int(11) DEFAULT '0',
  `items_donated` int(11) DEFAULT '0',
  `items_repaired` int(11) DEFAULT '0',
  `quests_created` int(11) NOT NULL DEFAULT '0',
  `maxed_biz` int(11) NOT NULL DEFAULT '0',
  `most_npc_wins` int(11) NOT NULL DEFAULT '0',
  `items_combined` int(11) NOT NULL DEFAULT '0',
  `items_consumed` int(11) NOT NULL DEFAULT '0',
  `spent_shop` int(11) NOT NULL DEFAULT '0',
  `spent_repair` int(11) NOT NULL DEFAULT '0',
  `withdrawls` int(11) NOT NULL DEFAULT '0',
  `deposits` int(11) NOT NULL DEFAULT '0',
  `dice_wins` int(11) NOT NULL DEFAULT '0',
  `use_inn` int(11) NOT NULL DEFAULT '0',
  `num_tourney` int(11) NOT NULL DEFAULT '0',
  `win_tourney` int(11) NOT NULL DEFAULT '0',
  `net_worth` int(11) NOT NULL DEFAULT '0',
  `top_estate` int(11) NOT NULL DEFAULT '0',
  `top_business` int(11) NOT NULL DEFAULT '0',
  `tot_estate` int(11) NOT NULL DEFAULT '0',
  `tot_business` int(11) NOT NULL DEFAULT '0',
  `highest_estate` int(11) NOT NULL DEFAULT '0',
  `num_estates` int(11) NOT NULL DEFAULT '0',
  `clans_joined` int(11) NOT NULL DEFAULT '0',
  `ally_wins` int(11) NOT NULL DEFAULT '0',
  `clan_posts` int(11) NOT NULL DEFAULT '0',
  `tar_posts` int(11) NOT NULL DEFAULT '0',
  `items_sold` int(11) NOT NULL DEFAULT '0',
  `inn_use` int(11) NOT NULL DEFAULT '0',
  `ways_use` int(11) NOT NULL DEFAULT '0',
  `outfit_use` int(11) NOT NULL DEFAULT '0',
  `loc_ji1` float NOT NULL,
  `loc_ji2` float NOT NULL,
  `loc_ji3` float NOT NULL,
  `loc_ji4` float NOT NULL,
  `loc_ji5` float NOT NULL,
  `loc_ji6` float NOT NULL,
  `loc_ji7` float NOT NULL,
  `loc_ji8` float NOT NULL,
  `loc_ji9` float NOT NULL,
  `loc_ji10` float NOT NULL,
  `loc_ji11` float NOT NULL,
  `loc_ji12` float NOT NULL,
  `loc_ji13` float NOT NULL,
  `loc_ji14` float NOT NULL,
  `loc_ji15` float NOT NULL,
  `loc_ji16` float NOT NULL,
  `loc_ji17` float NOT NULL,
  `loc_ji18` float NOT NULL,
  `loc_ji19` float NOT NULL,
  `loc_ji20` float NOT NULL,
  `loc_ji21` float NOT NULL,
  `loc_ji22` float NOT NULL,
  `loc_ji23` float NOT NULL,
  `loc_ji24` float NOT NULL,
  `achieved` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

  $result = mysql_query($query);
  echo "<br>Create New Table: $result";
  
  if (mysql_num_rows(mysql_query("SELECT id FROM Users_stats WHERE 1")) ==0)
  {
    for ($i=10001; $i<=10020; $i++)
    {
      mysql_query("INSERT INTO Users_stats (id) VALUES ('$i')");
    }
  }
}


/////////////////////////////////////////////////////////////////////////////////////
// ITEMS ///////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

echo "<br><br>::ITEMS TABLE::<br><br>";
// Drop Old Table
$query  = 'DROP TABLE IF EXISTS Items';
$result = mysql_query($query);
echo "Drop Old Table: $result";

// Create New Table
$query = "CREATE TABLE IF NOT EXISTS `Items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `cond` int(11) NOT NULL,
  `istatus` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `society` int(11) DEFAULT NULL,
  `last_moved` int(11) NOT NULL DEFAULT '0',
  `base` char(30) DEFAULT NULL,
  `prefix` char(30) DEFAULT NULL,
  `suffix` char(30) DEFAULT NULL,
  `stats` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1";

$result = mysql_query($query);
echo "<br>Create New Table: $result";

if (mysql_num_rows(mysql_query("SELECT id FROM Users WHERE 1")) ==0)
{
  include_once("initSpecialChars.php");
}

?>

<br><br>
<?php if (!$head) { ?>
</body>
</html>
<?php } ?>