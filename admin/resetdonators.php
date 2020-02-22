<html>
<head>
<title>Recreate donators table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<u>Resets the table "donate"</u><br><br>
<?php
// Connect
include("connect.php");

if (strtolower($name) != "the" && strtolower($lastname) != "creator")
{
  echo "Only the Creator has such powers!";
}
else
{
// Drop Old Table
$query  = 'DROP TABLE donate';
$result = mysql_query($query);
echo "<b>Results</b><br><br>Drop Old Table: $result";

// Create New Table
$query = 'CREATE TABLE IF NOT EXISTS donate ('.
'  id int(11) NOT NULL AUTO_INCREMENT,'.
'  email char(60) DEFAULT NULL,'.
'  amount int(11) DEFAULT NULL,'.
'  PRIMARY KEY (id)'.
') ENGINE=MyISAM  DEFAULT CHARSET=latin1';

'CREATE TABLE donate( '.
'id int NOT NULL AUTO_INCREMENT, '.
'email char(40), '.
'amount int, '.
'PRIMARY KEY (id)'.
')';
$result = mysql_query($query);
echo "<br>Create New Table: $result";
}
// Close Database
mysql_close($db);
?>

</body>
</html>