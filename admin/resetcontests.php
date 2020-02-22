<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Quest Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php } ?>
<u>Resets the table "Contests"</u><br><br>
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
  $query  = 'DROP TABLE IF EXISTS Contests';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = 'CREATE TABLE IF NOT EXISTS `Contests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `starts` int(11) DEFAULT NULL,
  `ends` int(11) DEFAULT NULL,
  `done` int(11) DEFAULT NULL,
  `distro` int(11) DEFAULT NULL,
  `contestants` text,
  `rules` text,
  `participation` text,
  `results` text,
  `reward` text,
  `winner` text,
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