<html>
<head>
<title>Admin Recreate Trade Table</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<u>Resets the table "Trade"</u><br><br>
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
$query1  = 'DROP TABLE Trade';

// Create New Table
$query = 'CREATE TABLE Trade( '.
'id int NOT NULL AUTO_INCREMENT, '.
'location char(50) UNIQUE, '.
'f1 bigint NOT NULL, '.
'f2 bigint NOT NULL, '.
'f3 bigint NOT NULL, '.
'f4 bigint NOT NULL, '.
'm1 bigint NOT NULL, '.
'm2 bigint NOT NULL, '.
'm3 bigint NOT NULL, '.
'm4 bigint NOT NULL, '.
'l1 bigint NOT NULL, '.
'l2 bigint NOT NULL, '.
'l3 bigint NOT NULL, '.
'l4 bigint NOT NULL, '.
'own char(50), '.
'war char(50), '.
'PRIMARY KEY (id))';
if ($_GET['r']==1)
{
  $result1 = mysql_query($query1);
  echo "<b>Results</b><br><br>Drop Old Table: $result1";
  $result = mysql_query($query);
  echo "<br>Create New Table: $result";
}
else echo "THIS <u>ONLY</u> CREATES NEW ENTRIES FOR <u>NEW</u> LOCATIONS<br><i>to reset the whole table go to:</i> <a href='resettrade.php?r=1'>resettrade.php?r=1</a>";

echo "<br><br><u>Location data inserts</u>";
// ADD FOR ALL LOCATIONS
include($server_name.'/map/mapdata/coordinates.inc');
echo "<table border='0'>";
foreach ($location_array as $loc => $coor)
{
  $result = mysql_query("INSERT INTO Trade SET location='$loc', own=''");
  if ($result) $text="<b><i>updated</i></b>";
  else $text="<i>unaltered</i>";
  echo "<tr><td><b>$loc</b>:</td><td>&nbsp;</td><td>".$text."</tr>";
}
echo "</table>";
}
?>

<br><br>
~Table reset and recreator for the Trading Post~
</body>
</html>