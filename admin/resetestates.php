<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Quest Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "Estates"</u><br><br>
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
  $query  = 'DROP TABLE IF EXISTS Estates';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = 'CREATE TABLE IF NOT EXISTS `Estates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner` char(50) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `location` char(50) NOT NULL,
  `row` int(11) DEFAULT NULL,
  `col` int(11) DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `num_ups` int(11) DEFAULT NULL,
  `good` int(11) DEFAULT NULL,
  `trade` int(11) NOT NULL,
  `name` char(20) NOT NULL,
  `supporting` text,
  `upgrades` text,
  `inv` text,
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