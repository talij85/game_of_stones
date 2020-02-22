<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Quest Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "Quests"</u><br><br>
<?php
// Connect
include_once("connect.php");

if (strtolower($name) != "the" && strtolower($lastname) != "creator" && $head != 1)
{
  echo "Only the Creator has such powers!";
}
else
{
  // Drop Old Table
  $query  = 'DROP TABLE IF EXISTS Quests';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = 'CREATE TABLE IF NOT EXISTS `Quests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `type` int(11) DEFAULT NULL,
  `cat` int(11) NOT NULL DEFAULT 0,
  `offerer` char(51) NOT NULL,
  `num_avail` int(11) DEFAULT NULL,
  `num_done` int(11) DEFAULT NULL,
  `started` int(11) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `align` int(11) NOT NULL,
  `users` text,
  `location` text,
  `reqs` text,
  `goals` text,
  `special` text,
  `reward` text,
  `done` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1';
  $result = mysql_query($query);
  echo "<br>Create New Table: $result";

}
?>

<br><br>
<?php if (!$head) { ?>
</body>
</html>
<?php } ?>