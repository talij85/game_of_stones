<html>
<head>
<title>Admin Recreate Market Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<u>Resets the table "Market"</u><br><br>
<?php
// Connect
include("connect.php");

/*if (strtolower($name) != "the" && strtolower($lastname) != "creator")
{
  echo "Only the Creator has such powers!";
}
else*/
{
// Drop Old Table
$query  = 'DROP TABLE Market';
$result = mysql_query($query);
echo "<b>Results</b><br><br>Drop Old Table: $result";

// Create New Table
$query = 'CREATE TABLE Market( '.
'id int NOT NULL AUTO_INCREMENT, '.
'name varchar(90), '.
'prefix varchar(30), '.
'suffix varchar(30), '.
'base varchar(30), '.
'type int, '.
'bonus varchar(100), '.
'cost int, '.
'sname varchar(12), '.
'slastname varchar(12), '.
'location varchar(50), '.
'PRIMARY KEY (id),'.
'UNIQUE id (id))';
$result = mysql_query($query);
echo "<br>Create New Table: $result";
}
// Close Database
mysql_close($db);
?>

<br><br>
~Table reset and recreator for the Market "Show Sold"~
</body>
</html>