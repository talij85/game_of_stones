<?php if (!$head) { ?>
<html>
<head>
<title>Admin Recreate Society Table</title>
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
  $query  = 'DROP TABLE IF EXISTS IP_logs';
  $result = mysql_query($query);
  echo "<b>Results</b><br><br>Drop Old Table: $result";

  // Create New Table
  $query = 'CREATE TABLE IF NOT EXISTS `IP_logs` (
  `addy` varchar(30) NOT NULL,
  `users` text,
  `num` int(11) DEFAULT NULL,
  `maxnum` int(11) DEFAULT NULL,
  `test` int(11) DEFAULT NULL,
  PRIMARY KEY (`addy`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1';
  $result = mysql_query($query);
  echo "<br>Create New Table: $result";

}
?>

<br><br>
<?php if (!$head) { ?>
</body>
</html>
<?php } ?>