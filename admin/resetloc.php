<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Society Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "Locations"</u><br><br>
<?php
// Connect
include_once("connect.php");
//include_once("userdata.php");
include_once("displayFuncs.php");
include_once("locFuncs.php");

if (strtolower($name) != "the" && strtolower($lastname) != "creator" && $head != 1)
{
  echo "Only the Creator has such powers!";
}
else
{
  // Drop Old Table
  $query  = 'DROP TABLE IF EXISTS Locations';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = "CREATE TABLE IF NOT EXISTS `Locations` (
  `name` varchar(30) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shoplvls` text,
  `ruler` varchar(30) DEFAULT NULL,
  `bank` int(11) NOT NULL DEFAULT '0',
  `pop` int(11) NOT NULL,
  `myOrder` int(11) NOT NULL DEFAULT '0',
  `chaos` int(11) NOT NULL DEFAULT '0',
  `army` int(11) NOT NULL DEFAULT '1000',
  `last_war` int(11) NOT NULL,
  `last_tourney` int(11) NOT NULL,
  `last_update` int(11) DEFAULT NULL,
  `curr_dice` text,
  `prev_dice` text,
  `wager` int(11) DEFAULT NULL,
  `old_wager` int(11) DEFAULT NULL,
  `gtype` char(5) DEFAULT NULL,
  `lastdice` int(11) DEFAULT NULL,
  `minw` int(11) NOT NULL,
  `maxw` int(11) NOT NULL,
  `num_ups` int(11) NOT NULL,
  `grain` int(11) NOT NULL DEFAULT '1000',
  `livestock` int(11) NOT NULL DEFAULT '1000',
  `lumber` int(11) NOT NULL DEFAULT '1000',
  `stone` int(11) NOT NULL DEFAULT '1000',
  `fish` int(11) NOT NULL DEFAULT '1000',
  `luxury` int(11) NOT NULL DEFAULT '1000',
  `isDestroyed` tinyint(1) NOT NULL DEFAULT '0',
  `info` text,
  `shipg` text,
  `shopg` text,
  `upgrades` text NOT NULL,
  `clan_scores` text NOT NULL,
  `clan_support` text NOT NULL,
  `estate_support` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`(3))
) ENGINE=MyISAM  DEFAULT CHARSET=latin1"; 

  $result = mysql_query($query);
  echo "<br>Create New Table: $result";

  if (mysql_num_rows(mysql_query("SELECT id FROM Locations WHERE 1")) ==0)
  {
    // Create New Table
    for ($loc_id=0; $loc_id < 24; ++$loc_id)
    {
      $tname = $townnames[$loc_id];
      $shipg = 0;
      if ($loc_ship_goods[$tname]) $shipg = $loc_ship_goods[$tname];
      $shipgs = serialize($shipg);
      $shopg= serialize(build_base_consume_list($town_consumables[$tname]));
      $ruler= "No One";
      $insert_id = $loc_id + 1;
      $shoplvls = serialize ($shop_base[$tname]);
      $wager = 10;
      $last_update= floor(time()/3600)-1;
      if ($loc_id%2==1) { $gtype = 'd';} else $gtype='';
      $upgrades=serialize(array(0,0,0,0,0,0,0,0));
      mysql_query("INSERT INTO Locations (name,    id,          shoplvls,   ruler,   bank,pop, last_war,last_tourney,last_update,   wager,   gtype,   lastdice,minw,maxw,shopg,   shipg,    upgrades) 
                                  VALUES ('$tname','$insert_id','$shoplvls','$ruler','0', '10','0',     '0',         '$last_update','$wager','$gtype','0',     '1', '4', '$shopg','$shipgs','$upgrades')");
    }
  }
}
?>

<br><br>
<?php if (!$head) { ?>
</body>
</html>
<?php } ?>