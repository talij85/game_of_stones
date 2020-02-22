<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Quest Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "IP_logs"</u><br><br>
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
  $query  = 'DROP TABLE IF EXISTS Profs';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = 'CREATE TABLE IF NOT EXISTS `Profs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `type` int(11) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `location` char(50) NOT NULL,
  `value` int(11) DEFAULT NULL,
  `started` int(11) DEFAULT NULL,
  `upgrades` text,
  `status` text,
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