<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Quest Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "Hordes"</u><br><br>
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
  $query  = 'DROP TABLE IF EXISTS Hordes';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = "CREATE TABLE IF NOT EXISTS `Hordes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `target` varchar(30) DEFAULT NULL,
  `starts` int(11) DEFAULT NULL,
  `ends` int(11) DEFAULT NULL,
  `next` int(11) DEFAULT NULL,
  `army_done` int(11) NOT NULL DEFAULT '0',
  `defeated` int(11) NOT NULL,
  `done` int(11) DEFAULT NULL,
  `finisher` int(11) NOT NULL,
  `afinisher` int(11) NOT NULL,
  `npcs` text,
  `users` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1";

  $result = mysql_query($query);
  echo "<br>Create New Table: $result";

}
?>

<br><br>
<?php if (!$head) { ?>
</body>
</html>
<?php } ?>